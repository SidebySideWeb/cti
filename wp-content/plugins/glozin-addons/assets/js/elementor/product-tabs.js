class GlozinProductTabsWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				tab: '.glozin-product-tabs__heading span',
				panel: '.glozin-product-tabs__item',
				products: 'ul.products',
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$tabs: this.findElement( selectors.tab ),
			$panels: this.findElement( selectors.panel ),
			$products: this.findElement( selectors.products ),
		};
	}

	activateDefaultTab() {
		const defaultActiveTab = this.getEditSettings( 'activeItemIndex' ) || 1;

		if ( this.isEdit ) {
			jQuery( document.body ).trigger( 'glozin_get_products_ajax_loaded', [this.elements.$products.find( 'li.product' ), false] );
		}

		this.changeActiveTab( defaultActiveTab );
	}

	changeActiveTab( tabIndex ) {
		if ( this.isActiveTab( tabIndex ) ) {
			return;
		}

		const $tab = this.getTab( tabIndex ),
			  $panel = this.getPanel( tabIndex );

		$tab.addClass( 'active' ).siblings( '.active' ).removeClass( 'active' );

		if ( $panel.length ) {
			$panel.siblings( '.active' ).removeClass( 'active' ).addClass( 'waiting' );

			setTimeout( function() {
				$panel.removeClass( 'no-active' ).addClass( 'active' );
				$panel.parent().find( '.waiting' ).removeClass( 'waiting' ).addClass( 'no-active' );
			}, 300);
		} else {
			this.loadNewPanel( tabIndex );
		}
	}

	isActiveTab( tabIndex ) {
		return this.getTab( tabIndex ).hasClass( 'active' );
	}

	hasTabPanel( tabIndex ) {
		return this.getPanel( tabIndex ).length;
	}

	getTab( tabIndex ) {
		return this.elements.$tabs.filter( '[data-target="' + tabIndex + '"]' );
	}

	getPanel( tabIndex ) {
		return this.elements.$panels.filter( '[data-panel="' + tabIndex + '"]' );
	}

	loadNewPanel( tabIndex ) {
		if ( this.hasTabPanel( tabIndex ) ) {
			return;
		}

		const isEdit           = this.isEdit,
		      $tab             = this.elements.$tabs.filter( '[data-target="' + tabIndex + '"]' ),
		      $panelsContainer = this.elements.$panels.first().parent(),
		      atts             = $tab.data( 'atts' );

		if ( ! atts ) {
			return;
		}

		var self = this;

		var ajax_url = '';
		if (typeof glozinData !== 'undefined') {
			ajax_url = glozinData.ajax_url;
		} else if (typeof wc_add_to_cart_params !== 'undefined') {
			ajax_url = wc_add_to_cart_params.wc_ajax_url;
		}

		if( ! ajax_url ) {
			return;
		}

		ajax_url = ajax_url.toString().replace(  '%%endpoint%%', 'glozin_get_products_tab' );

		$panelsContainer.addClass( 'loading' );

		jQuery.post( ajax_url, {
			action: 'glozin_get_products_tab',
			atts  : atts,
		}, ( response ) => {
			if ( ! response.success ) {
				$panelsContainer.removeClass( 'loading' );
				return;
			}

			const $newPanel = this.elements.$panels.first().clone();

			$newPanel.html( response.data );
			$newPanel.attr( 'data-panel', tabIndex );
			$newPanel.removeClass( 'no-active' ).addClass( 'active' );
			$newPanel.appendTo( $panelsContainer );
			$newPanel.siblings( '.active' ).removeClass( 'active' ).addClass( 'no-active' );

			this.elements.$panels = this.elements.$panels.add( $newPanel );

			self.loadProductsInfinite();

			if ( ! isEdit ) {
				jQuery( document.body ).trigger( 'glozin_get_products_ajax_loaded', [$newPanel.find( 'li.product' ), false] );
			}

			if( response.success ) {
				$panelsContainer.removeClass( 'loading' );
			}
		} );
	}

	loadMoreProducts() {
		var self = this;

		// Load Products
		this.$element.on( 'click', '.woocommerce-pagination a', function (e) {
			e.preventDefault();

			var $el = jQuery(this),
				$els = jQuery(this).closest( '.woocommerce-pagination-button' );

			if ( $els.hasClass('loading')) {
				return;
			}

			$els.addClass( 'loading' );

			self.loadProducts($el);
		});
	};

	loadProductsInfinite() {
		var self = this;
		if ( ! this.elements.$panels.find( '.woocommerce-pagination' ).hasClass( 'woocommerce-pagination--infinite' ) ) {
			return;
		}

		var $el = this.elements.$panels.find('.woocommerce-pagination-button'),
			waiting = false;

			jQuery(window).on('scroll', function () {
				if (waiting || !$el.length || !$el.is(':visible')) {
					return;
				}

				var buttonOffset = $el.offset().top,
					windowHeight = jQuery(window).height(),
					scrollPosition = jQuery(window).scrollTop();

				if (scrollPosition + windowHeight >= buttonOffset) {
					waiting = true;

					if (!$el.hasClass('loading')) {
						$el.addClass('loading');
						self.loadProducts($el);
					}

					setTimeout(function () {
						waiting = false;

						if (!$el.is(':visible')) {
							jQuery(window).off('scroll');
						}
					}, 300);
				}
			});

    };

	loadProducts($el) {
		var ajax_url = glozinData.ajax_url.toString().replace('%%endpoint%%', 'glozin_elementor_load_products' ),
			$panel = $el.closest( '.glozin-product-tabs__item' ).attr( 'data-panel' ),
			$settings = $el.closest( '.glozin-product-tabs' ).find( "span[data-target='" + $panel + "']" ).data( 'atts' );;

		if( ! ajax_url ) {
			return;
		}

		jQuery.post(
			ajax_url,
			{
				page: $el.attr( 'data-page' ),
				settings: $settings
			},
			function ( response ) {
				if ( ! response ) {
					return;
				}

				$el.closest( '.woocommerce-pagination-button' ).removeClass( 'loading' );

				var $data = jQuery( response.data ),
					$products = $data.find( 'li.product' ),
					$container = $el.closest( '.glozin-product-tabs__item' ),
					$grid = $container.find( 'ul.products' ),
					$page_number = $data.find( '.page-number' ).data( 'page' );

				if ( $products.length ) {
					$products.addClass( 'gz-fadeinup gz-animated' );

					let delay = 0.5;
					$products.each( function( i, product ) {
						jQuery(product).css( '--gz-fadeinup-delay', delay + 's' );
						delay = delay + 0.1;
					});

					$grid.append($products);

					if ($page_number == '0') {
						$el.closest( '.woocommerce-pagination' ).remove();
					} else {
						$el.attr( 'data-page', $page_number );
					}
				}

				if( response.success ) {
					if( $products.hasClass( 'gz-animated' ) ) {
						setTimeout( function() {
							$products.removeClass( 'gz-animated' );
						}, 10 );
					}
				}

				jQuery(document.body).trigger( 'glozin_get_products_ajax_loaded', [ $products, true ] );
			}
		);
	};

	bindEvents() {
		this.elements.$tabs.on( {
			click: ( event ) => {
				event.preventDefault();

				this.changeActiveTab( event.currentTarget.getAttribute( 'data-target' ) );
			}
		} );
	}

	onInit( ...args ) {
		super.onInit( ...args );

		this.activateDefaultTab();

		this.loadMoreProducts();
		this.loadProductsInfinite();
	}
}

class GlozinProductTabsCarouselWidgetHandler extends elementorModules.frontend.handlers.CarouselBase {
	getDefaultSettings() {
		const settings = super.getDefaultSettings();

		settings.selectors.carousel = '.glozin-product-tabs-carousel__item .swiper';
		settings.selectors.swiperWrapper = '.products';
		settings.selectors.slideContent = '.product';
		settings.selectors.swiperArrow = this.$element.find('.glozin-product-tabs-carousel__item .swiper-arrows .swiper-button').get(0);
		settings.selectors.tab = '.glozin-product-tabs-carousel__heading span';
		settings.selectors.panel = '.glozin-product-tabs-carousel__item';
		settings.selectors.products = 'ul.products';

		return settings;
	}

	getDefaultElements() {
		const 	selectors = this.getSettings( 'selectors' ),
				elements = super.getDefaultElements();

		elements.$tabs = this.$element.find( selectors.tab );
		elements.$panels = this.$element.find( selectors.panel );
		elements.$products = this.$element.find( selectors.products );

		return elements;
	}

	getSwiperSettings() {
		const 	self = this,
				settings = super.getSwiperSettings(),
				elementSettings = this.getElementSettings(),
				slidesToShow = +elementSettings.slides_to_show || 4,
				isSingleSlide = 1 === slidesToShow,
				elementorBreakpoints = elementorFrontend.config.responsive.activeBreakpoints,
				defaultSlidesToShowMap = {
					mobile: 2,
					tablet: isSingleSlide ? 1 : 3,
				};

		let argsBreakpoints = {};
		let lastBreakpointSlidesToShowValue = slidesToShow;

		var spaceBetween = elementSettings.image_spacing_custom ? parseInt(elementSettings.image_spacing_custom) : 0;

		settings.spaceBetween = elementSettings.custom_space_between == 'yes' ? 0 : spaceBetween;

		var changed = false;
		Object.keys(elementorBreakpoints).forEach(breakpoint => {
			if( elementorBreakpoints[breakpoint].value !== elementorBreakpoints[breakpoint].default_value ) {
				return;
			}

			elementorBreakpoints[breakpoint].value = parseInt( elementorBreakpoints[breakpoint].value ) + 1;

			changed = true;
		});

		if ( changed ) {
			Object.keys( elementorBreakpoints ).reverse().forEach( ( breakpointName ) => {
				// Tablet has a specific default `slides_to_show`.
				const defaultSlidesToShow = defaultSlidesToShowMap[ breakpointName ] ? defaultSlidesToShowMap[ breakpointName ] : lastBreakpointSlidesToShowValue;

				argsBreakpoints[ elementorBreakpoints[ breakpointName ].value ] = {
					slidesPerView: +elementSettings[ 'slides_to_show_' + breakpointName ] || defaultSlidesToShow,
					slidesPerGroup: +elementSettings[ 'slides_to_scroll_' + breakpointName ] || 1,
				};

				if ( elementSettings.image_spacing_custom ) {
					argsBreakpoints[ elementorBreakpoints[ breakpointName ].value ].spaceBetween = elementSettings.custom_space_between == 'yes' ? 0 : this.getSpaceBetween( breakpointName );
				}

				lastBreakpointSlidesToShowValue = +elementSettings[ 'slides_to_show_' + breakpointName ] || defaultSlidesToShow;
			} );

			settings.breakpoints = argsBreakpoints;
		}

		if( elementSettings.slides_rows && parseInt(elementSettings.slides_rows) > 1 ) {
			settings.grid = {
				fill: 'row',
				rows: elementSettings.slides_rows
			};
		}

		Object.keys(elementorBreakpoints).forEach(breakpoint => {
			if( elementSettings.custom_space_between == 'yes') {
				settings.breakpoints[ elementorBreakpoints[breakpoint].value ].spaceBetween = 0;
			} else {
				settings.breakpoints[ elementorBreakpoints[breakpoint].value ].spaceBetween = elementSettings[ 'image_spacing_custom_' + breakpoint ] ? elementSettings[ 'image_spacing_custom_' + breakpoint ] : spaceBetween;
			}
			
			if ( breakpoint == 'mobile' && elementSettings[ 'slidesperview_auto_mobile' ] == 'yes' ) {
				settings.breakpoints[ elementorBreakpoints[breakpoint].value ].slidesPerView = 1.5;
			}

			if( elementSettings.slides_rows && elementSettings.slides_rows > 1 ) {
				settings.breakpoints[ elementorBreakpoints[breakpoint].value ].grid = {
					fill: 'row',
					rows: elementSettings[ 'slides_rows_' + breakpoint ] ? elementSettings[ 'slides_rows_' + breakpoint ] : elementSettings.slides_rows
				};
			}

			if( elementSettings.custom_space_between == 'yes' ) {
				settings.breakpoints[ elementorBreakpoints[breakpoint].value ].spaceBetween = 0;
			}
		});

		settings.navigation.nextEl = this.elements.$panels.find('.elementor-swiper-button-next').get(0);
		settings.navigation.prevEl = this.elements.$panels.find('.elementor-swiper-button-prev').get(0);
		settings.pagination.el = this.elements.$panels.find('.swiper-pagination').get(0);

		settings.on.resize = function () {
			var self = this,
				$productThumbnail =	this.$el.closest( '.glozin-product-tabs-carousel' ).find('.product-thumbnail');

			if( $productThumbnail.length > 0 ) {
				jQuery(this.$el).imagesLoaded(function () {
					var	heightThumbnails = $productThumbnail.outerHeight(),
						top = ( ( heightThumbnails / 2 ) + 15 ) + 'px';

					jQuery(self.navigation.$nextEl).css({ '--gz-arrow-top': top });
					jQuery(self.navigation.$prevEl).css({ '--gz-arrow-top': top });
				});
			}
		}

		return settings;
	}

	activateDefaultTab() {
		const defaultActiveTab = this.getEditSettings( 'activeItemIndex' ) || 1;

		if ( this.isEdit ) {
			jQuery( document.body ).trigger( 'glozin_get_products_ajax_loaded', [this.elements.$products.find( 'li.product' ), false] );
		}

		this.changeActiveTab( defaultActiveTab );
	}

	changeActiveTab( tabIndex ) {
		if ( this.isActiveTab( tabIndex ) ) {
			return;
		}

		const $tab = this.getTab( tabIndex ),
			  $panel = this.getPanel( tabIndex );

		$tab.addClass( 'active' ).siblings( '.active' ).removeClass( 'active' );

		if ( $panel.length ) {
			$panel.siblings( '.active' ).removeClass( 'active' ).addClass( 'waiting' );

			setTimeout( function() {
				$panel.removeClass( 'no-active' ).addClass( 'active' );
				$panel.parent().find( '.waiting' ).removeClass( 'waiting' ).addClass( 'no-active' );
			}, 300);

			$panel.find( '.swiper-slide' ).removeAttr( 'inert' );
		} else {
			this.loadNewPanel( tabIndex );
		}
	}

	isActiveTab( tabIndex ) {
		return this.getTab( tabIndex ).hasClass( 'active' );
	}

	hasTabPanel( tabIndex ) {
		return this.getPanel( tabIndex ).length;
	}

	getTab( tabIndex ) {
		return this.elements.$tabs.filter( '[data-target="' + tabIndex + '"]' );
	}

	getPanel( tabIndex ) {
		return this.elements.$panels.filter( '[data-panel="' + tabIndex + '"]' );
	}

	loadNewPanel( tabIndex ) {
		const self = this;
		if ( self.hasTabPanel( tabIndex ) ) {
			return;
		}

		const isEdit           = self.isEdit,
		      $tab             = self.elements.$tabs.filter( '[data-target="' + tabIndex + '"]' ),
		      $panelsContainer = self.elements.$panels.first().parent(),
		      atts             = $tab.data( 'atts' );

		if ( ! atts ) {
			return;
		}

		var ajax_url = '';
		if (typeof glozinData !== 'undefined') {
			ajax_url = glozinData.ajax_url;
		} else if (typeof wc_add_to_cart_params !== 'undefined') {
			ajax_url = wc_add_to_cart_params.wc_ajax_url;
		}

		if( ! ajax_url ) {
			return;
		}

		ajax_url = ajax_url.toString().replace(  '%%endpoint%%', 'glozin_get_products_tab' );

		$panelsContainer.addClass( 'loading' );

		jQuery.post( ajax_url, {
			action: 'glozin_get_products_tab',
			atts  : atts,
		}, ( response ) => {
			if ( ! response.success ) {
				$panelsContainer.removeClass( 'loading' );
				return;
			}

			const $newPanel = self.elements.$panels.first().clone();

			$newPanel.html( response.data );
			$newPanel.attr( 'data-panel', tabIndex );
			$newPanel.removeClass( 'no-active' ).addClass( 'active' );
			$newPanel.appendTo( $panelsContainer );
			$newPanel.siblings( '.active' ).removeClass( 'active' ).addClass( 'no-active' );

			if( response.data.error ) {
				$newPanel.html( response.data.error );
				self.elements.$panels = self.elements.$panels.add( $newPanel );
				$panelsContainer.removeClass( 'loading' );
				return;
			} else {
				self.elements.$panels = self.elements.$panels.add( $newPanel );
			}

			if ( ! isEdit ) {
				jQuery( document.body ).trigger( 'glozin_get_products_ajax_loaded', [$newPanel.find( 'li.product' ), false] );
			}
			
			if ( ! response.data ) {
				$panelsContainer.removeClass( 'loading' );
				return;
			}

			var $data_swiper = self.$element.find('.swiper-original');
			$newPanel.children().wrapAll( '<div class="swiper"></div>' );
			jQuery.each($data_swiper[0].attributes, function () {
				if( this.name !== 'class' && this.name !== 'id' ) {
					$newPanel.find('.swiper').attr(this.name, this.value);
				}

				if( this.name == 'class' ) {
					$newPanel.find('.swiper').addClass(this.value);
				}
			});
			$newPanel.find('.swiper').removeClass( 'swiper-original hidden' );
			$newPanel.find('.swiper').append( self.$element.find( '.navigation-original .swiper-arrows' ).clone() );
			$newPanel.find('.swiper').append( self.$element.find( '.navigation-original .swiper-pagination' ).clone() );
			$newPanel.find( '.products' ).addClass( 'swiper-wrapper' );
			$newPanel.find( '.product' ).addClass( 'swiper-slide' );

			const 	Swiper = elementorFrontend.utils.swiper,
					settings = jQuery.merge(self.getSwiperSettings(), super.getSwiperSettings());

			settings.navigation.nextEl = $newPanel.find('.elementor-swiper-button-next').get(0);
			settings.navigation.prevEl = $newPanel.find('.elementor-swiper-button-prev').get(0);
			settings.pagination.el = $newPanel.find('.swiper-pagination').get(0);

			settings.on.resize = function () {
				var $productThumbnail =	$newPanel.find('.product-thumbnail');

				if( $productThumbnail.length > 0 ) {
					$newPanel.imagesLoaded(function () {
						var	heightThumbnails = $productThumbnail.outerHeight(),
							top = ( ( heightThumbnails / 2 ) + 15 ) + 'px';

						$newPanel.find('.elementor-swiper-button-next').css({ '--gz-arrow-top': top });
						$newPanel.find('.elementor-swiper-button-prev').css({ '--gz-arrow-top': top });
					});
				}
			}

			new Swiper( $newPanel.find( '.swiper' ).get(0), settings );

			if( response.success ) {
				$panelsContainer.removeClass( 'loading' );
			}
		} );
	}

	bindEvents() {
		this.elements.$tabs.on( {
			click: ( event ) => {
				event.preventDefault();

				this.changeActiveTab( event.currentTarget.getAttribute( 'data-target' ) );
			}
		} );
	}

	initSwiper() {
		this.elements.$panels.find('.swiper').append( this.$element.find( '.navigation-original .swiper-arrows' ).clone() );
		this.elements.$panels.find('.swiper').append( this.$element.find( '.navigation-original .swiper-pagination' ).clone() );
		this.elements.$panels.find( '.products' ).addClass( 'swiper-wrapper' );
		this.elements.$panels.find( '.product' ).addClass( 'swiper-slide' );

		super.initSwiper();

		this.elements.$swiperContainer.find('.products').attr('role', 'list');
		this.elements.$swiperContainer.find('.product').each(function() {
			jQuery(this).attr('role', 'listitem');
		} );
	}

	onInit() {
		super.onInit();
		this.activateDefaultTab();
		
		this.glozinUpdateSwiper();
	}

	glozinUpdateSwiper() {
		const swiper = this.$element.find('.swiper').get(0)?.swiper;

		if ( ! swiper ) {
			return;
		}

		const elementSettings = this.getElementSettings();

		swiper.params.breakpoints[ elementorFrontend.config.responsive.activeBreakpoints['tablet'].value ].spaceBetween = elementSettings.custom_space_between == 'yes' ? 0 : this.getSpaceBetween( 'desktop' );

		if( elementSettings.slides_rows && parseInt(elementSettings.slides_rows) > 1 ) {
			swiper.params.breakpoints[ elementorFrontend.config.responsive.activeBreakpoints['tablet'].value ].grid = {
				fill: 'row',
				rows: elementSettings.slides_rows
			};
		}

		swiper.update();
	}

	a11ySetSlideAriaHidden( status = '' ) {
		const currentIndex = 'initialisation' === status ? 0 : this.swiper?.activeIndex;

		if ( 'number' !== typeof currentIndex ) {
			return;
		}

		const $slides = this.elements.$swiperContainer.find( this.getSettings( 'selectors' ).slideContent );

		$slides.each( ( index, slide ) => {
			slide.removeAttribute( 'inert' );
		} );
	}
}

jQuery( window ).on( 'elementor/frontend/init', () => {
	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-product-tabs.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinProductTabsWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-product-tabs-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinProductTabsCarouselWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-product-sale-tabs.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinProductTabsCarouselWidgetHandler, { $element } );
	} );
} );