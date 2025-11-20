class GlozinStoreLocationsWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				content: '.glozin-store-locations__content',
				embed: '.glozin-store-locations__embed',
				mapbox: '.glozin-store-locations__mapbox',
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$content: this.findElement( selectors.content ),
			$embed: this.findElement( selectors.embed ),
			$mapbox: this.findElement( selectors.mapbox ),
		};
	}

	changeActiveTab( data ) {
		const $content     = this.elements.$content.filter( '[data-tab="' + data + '"]' ),
		      $embed       = this.elements.$embed.filter( '[data-tab="' + data + '"]' ),
		      isActive    = $content.hasClass( 'active' );

		if ( isActive ) {
			return;
		}

		$content.addClass( 'active' ).siblings( '.active' ).removeClass( 'active' );
		$embed.addClass( 'active' ).siblings( '.active' ).removeClass( 'active' );
	}

	bindEvents() {
		this.elements.$content.on( {
			click: ( event ) => {
				event.preventDefault();

				this.changeActiveTab( event.currentTarget.getAttribute( 'data-tab' ) );
			}
		} );
	}

	get_coordinates(){
		var data = [],
			index = 0,
			self = this,
			el = self.elements.$mapbox,
			elsMap = el.data('map'),
			local = elsMap.local,
			mapboxClient = mapboxSdk({ accessToken: elsMap.token }),
			wrapper = el.find('.glozin-map__content').attr('id');

		mapboxgl.accessToken = elsMap.token;

		var locations = Array.isArray(local) ? local : [local];

		var promises = locations.map((val, i) => {
			return mapboxClient.geocoding.forwardGeocode({
				query: val,
				autocomplete: false,
				limit: 1
			})
			.send()
			.then(response => {
				if (response && response.body && response.body.features && response.body.features.length) {
					var feature = response.body.features[0],
						tab = el.find('.glozin-store-locations__info');

					tab.eq(i).attr("data-latitude", feature.center[0]);
					tab.eq(i).attr("data-longitude", feature.center[1]);

					var item = {
						"type": "Feature",
						'properties': {
							'description': tab.eq(i).html(),
							'icon': 'theatre'
						},
						"geometry": {
							"type": "Point",
							"coordinates": [feature.center[0], feature.center[1]]
						}
					};

					if (index === 0) {
						var center = [feature.center[0], feature.center[1]];
						self.get_map(wrapper, elsMap, tab, elsMap.token, data, center);
					}

					data.push(item);
					index++;
				}
			});
		});

		// Chờ tất cả request hoàn thành trước khi trả về data
		return Promise.all(promises).then(() => data);
	}

	get_map(wrapper,elsMap, tab, accessToken, data, center){
			var map = new mapboxgl.Map( {
				container: wrapper,
				style    : 'mapbox://styles/mapbox/'+ elsMap.mode ,
				center   : center,
				zoom     : elsMap.zom
			} );

			var geocoder = new MapboxGeocoder( {
				accessToken: mapboxgl.accessToken
			} );

			map.addControl( geocoder );

			map.on( 'load', function () {
				map.loadImage( elsMap.marker, function ( error, image ) {
					if ( error ) throw error;
					map.addImage( 'marker', image );
					map.addLayer( {
						"id"    : "points",
						"type"  : "symbol",
						"source": {
							"type": "geojson",
							"data": {
								"type"    : "FeatureCollection",
								"features": data
							}
						},
						"layout": {
							"icon-image": "marker",
							"icon-size" : 1
						}
					} );
				} );

				map.addSource( 'single-point', {
					"type": "geojson",
					"data": {
						"type"    : "FeatureCollection",
						"features": []
					}
				} );

				map.addLayer( {
					"id"    : "point",
					"source": "single-point",
					"type"  : "circle",
					"paint" : {
						"circle-radius": 10,
						"circle-color" : "#007cbf"
					}
				} );

				map.setPaintProperty( 'water', 'fill-color', elsMap.color_1 );
				map.setPaintProperty( 'building', 'fill-color', elsMap.color_2 );

				geocoder.on( 'result', function ( ev ) {
					map.getSource( 'single-point' ).setData( ev.result.geometry );
				} );

				map.on('click', 'points', function (e) {
					var coordinates = e.features[0].geometry.coordinates.slice();
					var description = e.features[0].properties.description;

					while (Math.abs(e.lngLat.lng - coordinates[0]) > 180) {
						coordinates[0] += e.lngLat.lng > coordinates[0] ? 360 : -360;
					}

					new mapboxgl.Popup()
						.setLngLat(coordinates)
						.setHTML(description)
						.addTo(map);
				});

				map.on('mouseenter', 'points', function () {
					map.getCanvas().style.cursor = 'pointer';
				});

				map.on('mouseleave', 'points', function () {
					 map.getCanvas().style.cursor = '';
				});
			} );

			tab.on('click', function () {
				var  lat = jQuery(this).data('latitude'),
					 long = jQuery(this).data('longitude');

				map.flyTo({
					center: [lat,long ],
					zoom: elsMap.zom,
					essential: true, // this animation is considered essential with respect to prefers-reduced-motion,
					speed: 3,
					curve: 1,
				});
			});
	}

	onInit( ...args ) {
        super.onInit( ...args );
		this.get_coordinates();
    }
}

jQuery( window ).on( 'elementor/frontend/init', () => {
	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-store-locations.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinStoreLocationsWidgetHandler, { $element } );
	} );
} );
