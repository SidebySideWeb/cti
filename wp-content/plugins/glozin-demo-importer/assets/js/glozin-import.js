jQuery( document ).ready( function( $ ) {
	"use strict";

	var glozin = {
		init: function() {
			this.$progress = $( '#glozin-demo-import-progress' );
			this.$log = $( '#glozin-demo-import-log' );
			this.$importer = $( '#glozin-demo-importer' );

			// Events.
			$( document.body )
				.on( 'click', '.glozin-tab-nav-wrapper > .nav-tab', glozin.switchTabs )
				.on( 'click', '.toggle-options', glozin.toggleOptions );


			// Start importing.
			this.startImporting();
		},

		switchTabs: function( event ) {
			event.preventDefault();
			var $tab = $( event.target );

			if ( $tab.hasClass( 'nav-tab-active' ) ) {
				return;
			}

			$tab.addClass( 'nav-tab-active' ).siblings().removeClass( 'nav-tab-active' );

			$( $tab.attr( 'href' ) ).addClass( 'tab-panel-active' ).siblings().removeClass( 'tab-panel-active' );
		},

		toggleOptions: function( event ) {
			event.preventDefault();

			$( event.target ).closest( 'form' ).find( '.demo-import-options' ).stop( true, true ).fadeToggle( 'fast' );
		},

		startImporting: function() {
			if ( ! glozin.$importer.length ) {
				return;
			}

			// Collect steps.
			var steps = glozin.$importer.find( 'input[name="demo_parts"]' ).val();

			if ( ! steps ) {
				return;
			}

			if ( 'all' === steps ) {
				glozin.steps = ['content', 'customizer', 'widgets', 'sliders'];
			} else {
				glozin.steps = steps.split( ',' );
			}

			// Check if content is selected.
			glozin.containsContent = glozin.steps.indexOf( 'content' ) >= 0;

			// Check if need to regenerate images.
			glozin.regenImages = !! parseInt( glozin.$importer.find( 'input[name="regenerate_images"]' ).val() );

			// Check if this is manually upload.
			glozin.isManual = !! parseInt( glozin.$importer.find( 'input[name="uploaded"]' ).val() );

			// Let's go.
			if ( glozin.isManual ) {
				glozin.import( glozin.steps.shift() );
			} else {
				glozin.download( glozin.steps.shift() );
			}
		},

		download: function( type ) {
			glozin.log( 'Downloading ' + type + ' file' );

			$.get(
				ajaxurl,
				{
					action: 'glozin_download_file',
					type: type,
					demo: glozin.$importer.find( 'input[name="demo"]' ).val(),
					uploaded: glozin.$importer.find( 'input[name="uploaded"]' ).val(),
					_wpnonce: glozin.$importer.find( 'input[name="_wpnonce"]' ).val()
				},
				function( response ) {
					if ( response.success ) {
						glozin.import( type );
					} else {
						glozin.log( response.data );

						if ( glozin.steps.length ) {
							glozin.download( glozin.steps.shift() );
						} else {
							glozin.configTheme();
						}
					}
				}
			).fail( function() {
				glozin.log( 'Failed' );
			} );
		},

		import: function( type ) {
			glozin.log( 'Importing ' + type );

			var data = {
					action: 'glozin_import',
					type: type,
					_wpnonce: glozin.$importer.find( 'input[name="_wpnonce"]' ).val()
				};
			var url = ajaxurl + '?' + $.param( data );
			var evtSource = new EventSource( url );

			evtSource.addEventListener( 'message', function ( message ) {
				var data = JSON.parse( message.data );

				switch ( data.action ) {
					case 'updateTotal':
						console.log( data.delta );
						break;

					case 'updateDelta':
						console.log(data.delta);
						break;

					case 'complete':
						evtSource.close();
						glozin.log( type + ' has been imported successfully!' );

						if ( glozin.steps.length ) {
							if ( glozin.isManual ) {
								glozin.import( glozin.steps.shift() );
							} else {
								glozin.download( glozin.steps.shift() );
							}
						} else {
							glozin.configTheme();
						}

						break;
				}
			} );

			evtSource.addEventListener( 'log', function ( message ) {
				var data = JSON.parse( message.data );
				glozin.log( data.message );
			});
		},

		configTheme: function() {
			// Stop if no content imported.
			if ( ! glozin.containsContent ) {
				glozin.generateImages();
				return;
			}

			$.get(
				ajaxurl,
				{
					action: 'glozin_config_theme',
					demo: glozin.$importer.find( 'input[name="demo"]' ).val(),
					_wpnonce: glozin.$importer.find( 'input[name="_wpnonce"]' ).val()
				},
				function( response ) {
					if ( response.success ) {
						glozin.generateImages();
					}

					glozin.log( response.data );
				}
			).fail( function() {
				glozin.log( 'Failed' );
			} );
		},

		generateImages: function() {
			// Stop if no content imported.
			if ( ! glozin.containsContent || ! glozin.regenImages ) {
				glozin.log( 'Import completed!' );
				glozin.$progress.find( '.spinner' ).hide();
				return;
			}

			$.get(
				ajaxurl,
				{
					action: 'glozin_get_images',
					_wpnonce: glozin.$importer.find( 'input[name="_wpnonce"]' ).val()
				},
				function( response ) {
					if ( ! response.success ) {
						glozin.log( response.data );
						glozin.log( 'Import completed!' );
						glozin.$progress.find( '.spinner' ).hide();
						return;
					} else {
						var ids = response.data;

						if ( ! ids.length ) {
							glozin.log( 'Import completed!' );
							glozin.$progress.find( '.spinner' ).hide();
						}

						glozin.log( 'Starting generate ' + ids.length + ' images' );

						glozin.generateSingleImage( ids );
					}
				}
			);
		},

		generateSingleImage: function( ids ) {
			if ( ! ids.length ) {
				glozin.log( 'Import completed!' );
				glozin.$progress.find( '.spinner' ).hide();
				return;
			}

			var id = ids.shift();

			$.get(
				ajaxurl,
				{
					action: 'glozin_generate_image',
					id: id,
					_wpnonce: glozin.$importer.find( 'input[name="_wpnonce"]' ).val()
				},
				function( response ) {
					glozin.log( response.data + ' (' + ids.length + ' images left)' );

					glozin.generateSingleImage( ids );
				}
			);
		},

		log: function( message ) {
			glozin.$progress.find( '.text' ).text( message );
			glozin.$log.prepend( '<p>' + message + '</p>' );
		}
	};


	glozin.init();
} );


const searchInput = document.getElementById("glozin-demo-importer-search");

searchInput.addEventListener("input", function () {
	const searchTerm = searchInput.value.toLowerCase();

	const items = document.querySelectorAll(".demo-selector");

	items.forEach(item => {
		const classList = Array.from(item.classList);
		const match = classList.some(cls => cls.toLowerCase().includes(searchTerm));
		console.log(match);
		item.classList.toggle("hidden", !match);
	});
});