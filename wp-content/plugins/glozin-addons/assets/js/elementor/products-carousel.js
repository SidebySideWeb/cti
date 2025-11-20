class GlozinProductCarouselWidgetHandler extends elementorModules.frontend.handlers.CarouselBase {
	getDefaultSettings() {
		const settings = super.getDefaultSettings();

		settings.selectors.carousel = this.$element.find('.product-swiper--elementor');

		settings.selectors.swiperArrow = this.$element.find('.product-swiper--elementor > .swiper-button').get(0);

		settings.selectors.swiperWrapper = '.products';
		settings.selectors.slideContent = '.product';

		return settings;
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
		};

		if( elementSettings.slides_rows && parseInt(elementSettings.slides_rows) > 1 ) {
			settings.grid = {
				fill: 'row',
				rows: elementSettings.slides_rows
			};
		}

		Object.keys(elementorBreakpoints).reverse().forEach(breakpoint => {
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

		settings.navigation.nextEl = this.$element.find('.product-swiper--elementor  > .elementor-swiper-button-next').get(0);
		settings.navigation.prevEl = this.$element.find('.product-swiper--elementor  > .elementor-swiper-button-prev').get(0);

		settings.on.resize = function () {
			var self = this,
				$productThumbnail =	jQuery(this.$el).closest( '.product-swiper--elementor' ).find('.product-thumbnail').first();

			if( $productThumbnail.length > 0 ) {
				jQuery(this.$el).imagesLoaded(function () {
					var	heightThumbnails = $productThumbnail.outerHeight(),
						top = ( ( heightThumbnails / 2 ) + parseFloat(self.$el.closest( '.product-swiper--elementor').css( 'padding-top')) ) + 'px';

					jQuery(self.navigation.$nextEl).css({ '--gz-arrow-top': top });
					jQuery(self.navigation.$prevEl).css({ '--gz-arrow-top': top });
				});
			}
		}

		return settings;
	}

	initSwiper() {
		this.elements.$swiperContainer.find('.products').addClass('swiper-wrapper');
		this.elements.$swiperContainer.find('.product').addClass('swiper-slide');

		super.initSwiper();
		this.elements.$swiperContainer.find('.products').attr('role', 'list');
		this.elements.$swiperContainer.find('.product').each(function() {
			jQuery(this).attr('role', 'listitem');
		} );

	}

	onInit() {
        const self = this;
        super.onInit();

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

class GlozinProductsBundleWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				bundle: '.glozin-products-bundle',
				products: '.glozin-products-bundle__products',
				sidebar: '.glozin-products-bundle__sidebar',
				progressbar: '.glozin-products-bundle__progressbar',
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$bundle: this.findElement( selectors.bundle ),
			$products: this.findElement( selectors.products ),
			$sidebar: this.findElement( selectors.sidebar ),
			$progressbar: this.findElement( selectors.progressbar ),
		};
	}

	addBundle() {
		var self = this,
			settings = self.getElementSettings(),
			limit = settings.limit,
			bundle_max = settings.bundle_max,
			bundle_min = settings.bundle_min,
			bundle_discount = settings.bundle_discount;

		self.elements.$bundle.on( 'click', '.glozin-add-to-bundle', function(e) {
			e.preventDefault();

			var $button = jQuery(this),
				product_id = $button.data('product_id'),
				product_type = $button.data('product_type');

			if ( $button.data('requestRunning') ) {
				return;
			}

			if( product_type == 'variable' ) {
				if( ! $button.siblings('input[name="variation_id"]').length ) {
					return;
				}

				if( ! $button.siblings('input[name^="attribute_"]').length ) {
					return;
				}
			}

			$button.closest('.glozin-products-bundle__products').addClass('adding');
			$button.data('requestRunning', true);

			var data = {
				action: 'glozin_add_product_bundle',
				product_id: product_type == 'variable' ? $button.siblings('input[name="variation_id"]').val() : product_id,
				product_type: product_type,
				limit: limit,
				bundle_max: bundle_max,
				bundle_min: bundle_min,
				bundle_discount: bundle_discount
			};

			if( product_type == 'variable' ) {
				if( $button.siblings('input[name^="attribute_"]').length ) {
					$button.siblings('input[name^="attribute_"]').each(function () {
						data[jQuery(this).attr('name')] = jQuery(this).val();
					});
				}
			}

			if( ! $button.hasClass('disabled') ) {
				$button.addClass( 'added disabled' );
				$button.text( $button.data('text_added') );
			}

			jQuery.ajax({
				type: 'POST',
				url: wc_add_to_cart_params.wc_ajax_url.toString().replace(  '%%endpoint%%', 'glozin_add_product_bundle' ),
				data: data,
				error: (response) => {
					if( $button.hasClass('disabled') ) {
						$button.removeClass( 'added disabled' );
						$button.text( $button.data('text') );
					}
				},
				success: (response) => {
					if( ! response.success ) {
						if( $button.hasClass('disabled') ) {
							$button.removeClass( 'added disabled' );
							$button.text( $button.data('text') );
						}
					}

					if( response.data && ! response.data.limit && response.data.html ) {
						$button.closest('.glozin-products-bundle').find('.glozin-products-bundle__sidebar-products').empty().html(response.data.html);

						if( $button.closest('.glozin-products-bundle').find('.glozin-add-to-cart-bundle').hasClass('disabled') ) {
							$button.closest('.glozin-products-bundle').find('.glozin-add-to-cart-bundle').removeClass('disabled');
						}

						if( response.data.total_html ) {
							$button.closest( '.glozin-products-bundle').find( '.glozin-products-bundle__sidebar-subtotal-price' ).html(response.data.total_html);
						}
					}

					if(response.data && response.data.current_limit ) {
						$button.closest('ul.products').find('.glozin-add-to-bundle').each(function () {
							if( ! jQuery(this).hasClass('disabled limited') ) {
								jQuery(this).addClass('disabled limited');
							}
						});
					}
				},
				complete: () => {
					self.progressBar();
					$button.closest('.glozin-products-bundle__products').removeClass('adding');
					$button.data('requestRunning', false);
				}
			});
		});
	}

	removeBundle() {
		var self      = this,
			settings     = self.getElementSettings(),
			limit        = settings.limit,
			bundle_max  = settings.bundle_max,
			bundle_min = settings.bundle_min,
			bundle_discount = settings.bundle_discount;

		self.elements.$bundle.on( 'click', '.product-bundle__item-remove', function(e) {
			e.preventDefault();

			var $button = jQuery(this),
				product_id = $button.data('product_id'),
				product_type = $button.data('product_type'),
				product_parent = $button.data('product_parent');

			if ( $button.data('requestRunning') ) {
				return;
			}
			$button.addClass('loading');

			$button.data('requestRunning', true);

			jQuery.ajax({
				type: 'POST',
				url: wc_add_to_cart_params.wc_ajax_url.toString().replace(  '%%endpoint%%', 'glozin_remove_product_bundle' ),
				data: {
					action: 'glozin_remove_product_bundle',
					product_id: product_id,
					product_type: product_type,
					limit: limit,
					bundle_max: bundle_max,
					bundle_min: bundle_min,
					bundle_discount: bundle_discount
				},
				success: (response) => {
					product_id = product_parent ? product_parent : product_id;
					var $_button = $button.closest('.glozin-products-bundle').find('.glozin-add-to-bundle').filter(function () {
						var $btn = jQuery(this);
						var btn_product_id = $btn.data('product_id');
						var btn_parent_id = $btn.data('product_parent');
						return btn_product_id == product_id || btn_parent_id == product_id;
					});

					$_button.removeClass('added disabled limited');
					$_button.text($_button.data('text'));

					$button.closest('.glozin-products-bundle').find('.glozin-add-to-bundle').each(function () {
						if( jQuery(this).hasClass('disabled limited') && ! jQuery(this).hasClass('added') ) {
							if( jQuery(this).hasClass('wc-variation-selection-needed')) {
								jQuery(this).removeClass('limited');
							} else {
								jQuery(this).removeClass('disabled limited');
							}
						}
					});

					if( ! $button.closest('.glozin-products-bundle').find('.glozin-add-to-cart-bundle').hasClass('disabled') && ! response.data.hasproduct ) {
						$button.closest('.glozin-products-bundle').find('.glozin-add-to-cart-bundle').addClass( 'disabled' );
					}

					$button.data('requestRunning', false);
					$button.removeClass('loading');
					if( response.data.html ) {
						$button.closest('.glozin-products-bundle').find('.glozin-products-bundle__sidebar-products').empty().html(response.data.html);
					}

					if( response.data.total_html ) {
						$button.closest('.glozin-products-bundle').find( '.glozin-products-bundle__sidebar-subtotal-price').html(response.data.total_html);
					}
				},
				complete: () => {
					self.progressBar();
				}
			});
		});
	}

	variationsBundle() {
		var self = this,
			$button = self.elements.$bundle.find('.variations_form:not(.product-select__variation) .glozin-add-to-bundle');

		$button.addClass('disabled wc-variation-selection-needed');

		self.elements.$bundle.find('.product-thumbnail img').each( function() {
			jQuery(this).attr('data-original', jQuery(this).attr('src') );
			jQuery(this).attr('data-srcset-original', jQuery(this).attr('srcset') );
		});

		self.elements.$bundle.find('.variations_form:not(.product-select__variation)').on( 'found_variation', function (event, variation) {
			var $form = jQuery(this),
				$price = $form.siblings('.price'),
				$variation_price = $form.siblings('.variation-price'),
				$product_thumbnail = $form.closest('.product-inner').find('.product-thumbnail img');

			$button = $form.find('.glozin-add-to-bundle');

			if( variation.variation_id ) {
				if( ! $button.hasClass('limited') ) {
					$button.removeClass('disabled wc-variation-selection-needed');
				} else {
					$button.removeClass('wc-variation-selection-needed');
				}

				if( $form.find( 'input[name="variation_id"]').length ) {
					$form.find( 'input[name="variation_id"]').val( variation.variation_id );
				} else {
					$form.append( '<input type="hidden" name="variation_id" value="' + variation.variation_id + '" />' );
				}

				jQuery.each(variation.attributes, function (key, value) {
					var $attrInput = $form.find('input[name="' + key + '"]');

					value = value == '' ? $form.find('select[name="' + key + '"]').val() : value;

					if ($attrInput.length) {
						$attrInput.val(value);
					} else {
						$form.append('<input type="hidden" name="' + key + '" value="' + value + '" />');
					}
				});
			}

			if( variation.price_html ) {
				if( $variation_price.length ) {
					$variation_price.empty().html( variation.price_html );
				} else {
					$price.addClass( 'hidden' );
					$price.after( '<span class="variation-price">' + variation.price_html + '</span>' );
				}
			}

			if( variation.image && variation.image.thumb_src ) {
				$product_thumbnail.attr( 'src', variation.image.thumb_src );
				$product_thumbnail.attr( 'srcset', variation.image.srcset );
			} else {
				$product_thumbnail.attr( 'src', $product_thumbnail.attr( 'data-original' ) );
				$product_thumbnail.attr( 'srcset', $product_thumbnail.attr( 'data-srcset-original' ) );
			}
		});

		self.elements.$bundle.find('.variations_form:not(.product-select__variation)').on( 'reset_data', function () {
			var $form = jQuery(this),
				$price = $form.siblings('.price'),
				$variation_price = $form.siblings('.variation-price'),
				$product_thumbnail = $form.closest('.product-inner').find('.product-thumbnail img');

			$button = $form.find('.glozin-add-to-bundle');

			if( ! $button.hasClass('limited') ) {
				$button.addClass('disabled wc-variation-selection-needed');
			} else {
				$button.addClass('wc-variation-selection-needed');
			}

			if( $form.find( 'input[name="variation_id"]').length ) {
				$form.find( 'input[name="variation_id"]').remove();
			}

			if( $form.find('input[name^="attribute_"]').length ) {
				$form.find('input[name^="attribute_"]').remove();
			}

			if( $variation_price.length ) {
				$variation_price.remove();
				$price.removeClass('hidden');
			}

			$product_thumbnail.attr( 'src', $product_thumbnail.attr( 'data-original' ) );
			$product_thumbnail.attr( 'srcset', $product_thumbnail.attr( 'data-srcset-original' ) );
		});
	}

	add_to_cart() {
		var self = this,
			settings   = self.getElementSettings(),
			limit      = settings.limit,
			bundle_max = settings.bundle_max,
			bundle_min = settings.bundle_min,
			bundle_discount = settings.bundle_discount;

		self.elements.$bundle.on( 'click', '.glozin-add-to-cart-bundle', function(e) {
			e.preventDefault();

			var $button = jQuery(this);

			if ( $button.data('requestRunning') ) {
				return;
			}

			$button.addClass( 'loading' );
			$button.closest( '.glozin-products-bundle').find('.glozin-products-bundle__products').addClass('adding');
			$button.data('requestRunning', true);

			var data = {
				'action': 'glozin_add_to_cart_bundle',
				'limit': limit,
				'bundle_min': bundle_min,
				'bundle_max': bundle_max,
				'bundle_discount': bundle_discount,
			};

			$button.closest('form').find('.qty').each(function () {
				data[jQuery(this).attr('name')] = jQuery(this).val();
			});

			jQuery( document.body ).trigger( 'adding_to_cart' );
			jQuery.ajax({
				type: 'POST',
				url: wc_add_to_cart_params.wc_ajax_url.toString().replace(  '%%endpoint%%', 'glozin_add_to_cart_bundle' ),
				data: data,
				success: (response) => {
					if( response.data.html ) {
						$button.closest('.glozin-products-bundle__sidebar').find('.glozin-products-bundle__sidebar-products').empty().html(response.data.html);
						$button.closest('.glozin-products-bundle').find('.glozin-products-bundle__products .glozin-add-to-bundle').removeClass('disabled');
						$button.addClass('disabled');
						jQuery( document.body ).trigger( 'added_to_cart', [ response.data.fragments, response.data.cart_hash ] );
					}

					if( response.data.total_html ) {
						$button.closest('.glozin-products-bundle').find( '.glozin-products-bundle__sidebar-subtotal-price').html(response.data.total_html);
					}

					$button.closest( '.glozin-products-bundle').find('.glozin-products-bundle__products').removeClass('adding');
					$button.data('requestRunning', false);
				},
				complete: () => {
					$button.removeClass( 'loading' );
					self.progressBar();
				}
			});
		});
	}

	toggleBundle() {
		var self = this;
		self.elements.$bundle.on( 'click', '.glozin-bundle__toggle', function(e) {
			e.preventDefault();
			var $button = jQuery(this);

			if( $button.hasClass('active') ) {
				$button.removeClass('active');
				$button.closest( '.glozin-products-bundle__sidebar' ).find( '.glozin-products-bundle__sidebar-products' ).slideUp();
			} else {
				$button.addClass( 'active' );
				$button.closest( '.glozin-products-bundle__sidebar' ).find( '.glozin-products-bundle__sidebar-products' ).slideDown();
			}
		});

		jQuery( window ).on('resize', function () {
			if( jQuery( window ).width() > 1024 ) {
				jQuery('.glozin-bundle__toggle').removeClass('active');
				jQuery('.glozin-bundle__toggle').closest( '.glozin-products-bundle__sidebar' ).find( '.glozin-products-bundle__sidebar-products' ).removeAttr('style');
			}
		});
	}

	progressBar() {
		var self      = this,
			settings     = self.getElementSettings(),
			bundle_min = settings.bundle_min,
			percent = 0;

		if( jQuery('.glozin-products-bundle__sidebar').find( '.product-bundle__item' ).length > 0 ) {
			var number = parseInt( jQuery('.glozin-products-bundle__sidebar').find( '.product-bundle__item' ).length );

			percent = 100;
			if( number <= bundle_min ) {
				percent = Math.round( ( number / bundle_min ) * 100 );
			}
		}

		self.elements.$progressbar.css( '--gz-bundle-progressbar-width', percent + '%' );
	}

	onInit( ...args ) {
        super.onInit( ...args );

        var self = this;
        self.addBundle();
        self.removeBundle();
        self.variationsBundle();
        self.add_to_cart();
        self.toggleBundle();
    }
}

class GlozinProductShowcaseWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				container: '.glozin-product-showcase',
				gallery: '.woocommerce-product-gallery',
				galleryWrapper: '.woocommerce-product-gallery__wrapper',
				thumbnails: '.glozin-product-gallery-thumbnails',
				summary: '.entry-summary',
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$container: this.$element.find( selectors.container ),
			$gallery: this.$element.find( selectors.gallery ),
			$galleryWrapper: this.$element.find( selectors.galleryWrapper ),
			$thumbnails: this.$element.find( selectors.thumbnails ),
			$summary: this.$element.find( selectors.summary ),
		};
	}

	initSwiper( $el, options ) {
		if( $el.length < 1 ) {
			return;
		}

		return new Swiper( $el.get(0), options );
	}

	enableSwiper( el ) {
		el.enable();
	}

	disableSwiper( el ) {
		el.disable();
	}

	galleryOptions( $el ) {
		var options = {
			loop: false,
			autoplay: false,
			speed: 800,
			spaceBetween: 15,
			watchOverflow: true,
			autoHeight: true,
			navigation: {
				nextEl: $el.find('.swiper-button-next').get(0),
				prevEl: $el.find('.swiper-button-prev').get(0),
			},
			pagination: {
				el: $el.find('.swiper-fraction').get(0),
				type: "fraction",
				modifierClass: 'swiper-pagination--',
			},
			on: {
				init: function () {
					$el.css('opacity', 1);

					jQuery(document.body).trigger( 'glozin_product_gallery_init' );

					if( this.slides[this.realIndex].getAttribute( 'data-zoom_status' ) == 'false' ) {
						this.$el.parent().addClass( 'swiper-item-current-extra' );
					}
				},
				slideChange: function () {
					if( this.slides[this.realIndex].getAttribute( 'data-zoom_status' ) == 'false' ) {
						this.$el.parent().addClass( 'swiper-item-current-extra' );
					} else {
						if( this.$el.parent().hasClass( 'swiper-item-current-extra' ) ) {
							this.$el.parent().removeClass( 'swiper-item-current-extra' );
						}
					}
				},
				slideChangeTransitionEnd: function () {
					jQuery(document.body).trigger( 'glozin_product_gallery_slideChangeTransitionEnd' );
				}
			}
		};

		if( this.elements.$thumbnails.length > 0 ) {
			options.thumbs = {
				swiper: this.elements.$thumbnails.get(0).swiper,
			};
		}

		return options;
	}

	initGallery() {
		var $gallery = this.elements.$galleryWrapper;

		$gallery.addClass('woocommerce-product-gallery__slider swiper');
		$gallery.wrapInner('<div class="swiper-wrapper"></div>');
		$gallery.find('.swiper-wrapper').after('<span class="glozin-svg-icon glozin-svg-icon__inline glozin-svg-icon--icon-back swiper-button-prev swiper-button"><svg width="24" height="24" aria-hidden="true" role="img" focusable="false"> <use href="#icon-back" xlink:href="#icon-back"></use> </svg></span>');
		$gallery.find('.swiper-wrapper').after('<span class="glozin-svg-icon glozin-svg-icon__inline glozin-svg-icon--icon-next swiper-button-next swiper-button"><svg width="24" height="24" aria-hidden="true" role="img" focusable="false"> <use href="#icon-next" xlink:href="#icon-next"></use> </svg></span>');
		$gallery.find('.swiper-wrapper').after('<div class="swiper-fraction d-inline-flex d-none-md position-absolute bottom-15 end-15 pe-none z-2 py-8 px-17 border text-dark bg-light rounded-30 lh-1"></div>');
		$gallery.find('.woocommerce-product-gallery__image').addClass('swiper-slide');

		return this.initSwiper( $gallery, this.galleryOptions( $gallery ) );
	}

	thumbnailsOptions( $el, vertical ) {
		var options = {
			spaceBetween: 10,
			watchOverflow: true,
			watchSlidesProgress: true,
			autoHeight: true,
			on: {
				init: function () {
					$el.css('opacity', 1);

					jQuery(document.body).trigger( 'glozin_product_thumbnails_init' );
				},
			},
		};

		if (vertical) {
			options.breakpoints = {
								0: {
									direction: 'horizontal',
									slidesPerView: 5,
								},
								768: {
									direction: 'vertical',
									slidesPerView: "auto",
								}
							};
		} else {
			options.direction = 'horizontal';
			options.slidesPerView = 6;
		}

		return options;
	}

	initThumbnails( vertical ) {
		var $thumbnails = this.elements.$thumbnails;

		$thumbnails.addClass('swiper');
		$thumbnails.wrapInner('<div class="woocommerce-product-thumbnail__nav swiper-wrapper"></div>');
		$thumbnails.find('.woocommerce-product-gallery__image').addClass('swiper-slide');

		return this.initSwiper( $thumbnails, this.thumbnailsOptions( $thumbnails, vertical ) );
	}

	descriptionMore() {
		var $summary = this.elements.$summary,
			$selector =  $summary.find( '.short-description__content' ),
			$line = glozinData.product_description_lines,
			$height = parseInt( $selector.css( 'line-height' ) ) * $line;

		$selector.each( function () {
			var $currentHeight = jQuery(this).outerHeight();

			if( $currentHeight > $height ) {
				jQuery(this).siblings( '.short-description__more' ).removeClass( 'hidden' );
			}
		});

		jQuery(document.body).on( 'click', '.short-description__more', function(e) {
			e.preventDefault();

			var $this = jQuery(this),
				$settings = $this.data( 'settings' ),
				$more     = $settings.more,
				$less     = $settings.less;

			if( $this.hasClass( 'less' ) ) {
				$this.removeClass( 'less' );
				$this.text( $more );
				$this.siblings( '.short-description__content' ).removeAttr( 'style' );
			} else {
				$this.addClass( 'less' );
				$this.text( $less );
				$this.siblings( '.short-description__content' ).css( '-webkit-line-clamp', 'inherit' );
			}
		});
	}

	onInit() {
		var self = this;
		super.onInit();

		this.elements.$gallery.imagesLoaded(function () {
			self.elements.$gallery.css( 'opacity', '1' );
			self.elements.$thumbnails.appendTo( self.elements.$gallery );

			if( self.elements.$gallery.hasClass( 'woocommerce-product-gallery--vertical' ) ) {
				self.initThumbnails(true);
				self.initGallery();
				self.elements.$gallery.on('product_thumbnails_slider_vertical wc-product-gallery-after-init', function(){
					self.initThumbnails(true);
				});
			}

			if( self.elements.$gallery.hasClass( 'woocommerce-product-gallery--horizontal' ) ) {
				self.initThumbnails(false);
				self.initGallery();
				self.elements.$gallery.on('product_thumbnails_slider_horizontal', function(){
					self.initThumbnails(false);
				});
			}

		});

		self.descriptionMore();
	}
}

class GlozinLookbookProductsWidgetHandler extends elementorModules.frontend.handlers.Base {
	bindEvents() {
		jQuery(document.body).on( 'click', '.glozin-lookbook-products__hotspot', function() {
			var $this = jQuery(this),
				index = $this.data( 'index' ),
				swiper = $this.closest('.glozin-lookbook-products').find( '.swiper' ).get(0).swiper;

			swiper.slideTo(index);
		});
	}
}

jQuery( window ).on( 'elementor/frontend/init', () => {
	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-product-recently-viewed-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinProductCarouselWidgetHandler, { $element } );
	} );

	jQuery(document.body).on('glozin_get_products_ajax_loaded', function() {
		jQuery('.elementor-widget-glozin-product-recently-viewed-carousel').each(function() {
			elementorFrontend.elementsHandler.addHandler(GlozinProductCarouselWidgetHandler, { $element: jQuery(this) });
		});
	});

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-products-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinProductCarouselWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-products-bundle.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinProductsBundleWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-product-showcase.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinProductShowcaseWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-lookbook-products.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinProductCarouselWidgetHandler, { $element } );
		elementorFrontend.elementsHandler.addHandler( GlozinLookbookProductsWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-product-deals.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinProductCarouselWidgetHandler, { $element } );
	} );
} );