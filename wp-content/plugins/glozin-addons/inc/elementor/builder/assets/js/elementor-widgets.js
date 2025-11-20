class GlozinProductImagesWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				productGallery: '.woocommerce-product-gallery',
				parentGallery: '.product-gallery-summary'
			}
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings('selectors');
		return {
		  $productGallery: this.$element.find(selectors.productGallery),
		  $parentGallery: this.$element.find(selectors.parentGallery)
		};
	}

	productGallery( vertical, $selector = this.elements.$productGallery ) {
		if (typeof Swiper === 'undefined') {
			return;
		}

		const settings = this.getElementSettings();
		var $window = jQuery( window ),
			$body = jQuery( document.body );
		var slider = null;
		var thumbs = null;

		function initSwiper( $el, options ) {
			if( $el.length < 1 ) {
				return;
			}

			return new Swiper( $el.get(0), options );
		}

		function enableSwiper( el ) {
			el.enable();
		}

		function disableSwiper( el ) {
			el.disable();
		}

		function galleryOptions( $el ) {
			var options = {
				loop: false,
				autoplay: false,
				speed: 800,
				spaceBetween: 30,
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

			if( thumbs ) {
				options.thumbs = {
					swiper: thumbs,
				};
			}

			return options;
		}

		function initGallery() {
			var $gallery = $selector.find('.woocommerce-product-gallery__wrapper'),
				$selectorParent = $selector.closest( '.glozin-product-gallery' ),
				$icon_prev = '<svg width="24" height="24" aria-hidden="true" role="img" focusable="false"> <use href="#icon-back" xlink:href="#icon-back"></use> </svg>',
				$icon_next = '<svg width="24" height="24" aria-hidden="true" role="img" focusable="false"> <use href="#icon-next" xlink:href="#icon-next"></use> </svg>';

			if( $selectorParent.data( 'prev_icon' ) ) {
				$icon_prev = $selectorParent.data( 'prev_icon' );
			}

			if( $selectorParent.data( 'next_icon' ) ) {
				$icon_next = $selectorParent.data( 'next_icon' );
			}

			$gallery.addClass('woocommerce-product-gallery__slider swiper');
			$gallery.wrapInner('<div class="swiper-wrapper"></div>');
			$gallery.find('.swiper-wrapper').after('<span class="glozin-svg-icon glozin-svg-icon__inline glozin-svg-icon--icon-back swiper-button-prev swiper-button">' + $icon_prev + '</span>');
			$gallery.find('.swiper-wrapper').after('<span class="glozin-svg-icon glozin-svg-icon__inline glozin-svg-icon--icon-next swiper-button-next swiper-button">' + $icon_next + '</span>');
			$gallery.find('.swiper-wrapper').after('<div class="swiper-fraction d-inline-flex d-none-md position-absolute bottom-15 end-15 pe-none z-2 py-8 px-17 border text-dark bg-light rounded-30 lh-1"></div>');
			$gallery.find('.woocommerce-product-gallery__image').addClass('swiper-slide');

			return initSwiper( $gallery, galleryOptions( $gallery ) );
		}

		function thumbnailsOptions( $el ) {
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

		function initThumbnails() {
			var $thumbnails = $selector.find( '.glozin-product-gallery-thumbnails' );

			$thumbnails.addClass('swiper');
			$thumbnails.wrapInner('<div class="woocommerce-product-thumbnail__nav swiper-wrapper"></div>');
			$thumbnails.find('.woocommerce-product-gallery__image').addClass('swiper-slide');

			return initSwiper( $thumbnails, thumbnailsOptions( $thumbnails ) );
		}

		function responsiveGallery() {
			if ( $window.width() < 768 ) {
				enableSwiper( thumbs );
				enableSwiper( slider );
			} else {
				disableSwiper( thumbs );
				disableSwiper( slider );
			}
		}

		function init() {
			$selector.imagesLoaded(function () {
				var $thumbnails = $selector.find( '.glozin-product-gallery-thumbnails' );
					$thumbnails.appendTo( $selector );

				thumbs = initThumbnails();
				slider = initGallery();

				if ( typeof glozinData.product_gallery_slider !== 'undefined' && ! glozinData.product_gallery_slider ) {
					$selector.addClass( 'woocommerce-product-gallery--reponsive' );

					responsiveGallery();
					$window.on( 'resize', function () {
						responsiveGallery();
					});
				}
			});
		}

		init();
	}

	productImageZoom () {
		if (typeof Drift === 'undefined') {
			return;
		}

		const settings = this.getElementSettings();
		var $selector = jQuery('.glozin-product-gallery');

		if( ! $selector ) {
			return;
		}

		if( settings.product_image_zoom == 'none' ) {
			return
		}

		var $summary   = $selector.closest( '.e-con-full' ).siblings(),
		    $gallery   = $selector.find('.woocommerce-product-gallery__wrapper');

		if( settings.product_image_zoom == 'bounding' ) {
			if( ! $summary.find('.glozin-product-zoom-wrapper').length ) {
				var $zoom = jQuery( '<div class="glozin-product-zoom-wrapper" />' );
				$summary.prepend( $zoom );
			} else {
				var $zoom = $summary.find('.glozin-product-zoom-wrapper');
			}
		}

		var options = {
			containInline: true,
		};

		if( settings.product_image_zoom == 'bounding' ) {
			options.paneContainer = $zoom.get(0);
			options.hoverBoundingBox = true;
			options.zoomFactor = 2;
		}

		if( settings.product_image_zoom == 'inner' ) {
			options.zoomFactor = 3;
		}

		if( settings.product_image_zoom == 'magnifier' ) {
			options.zoomFactor = 2;
			options.inlinePane = true;
		}

		$gallery.find( '.woocommerce-product-gallery__image' ).each( function() {
			var $this = jQuery(this),
				$image = $this.find( 'img' ),
				imageUrl = $this.find( 'a' ).attr('href');

			if( $this.hasClass('glozin-product-video') || $this.data( 'zoom_status' ) == false ) {
				return;
			}

			if( settings.product_image_zoom == 'inner' ) {
				options.paneContainer = $this.get(0);
			}

			$image.attr( 'data-zoom', imageUrl );

			new Drift( $image.get(0), options );
		});

		jQuery('.single-product div.product .product-gallery-summary .variations_form').on( 'show_variation hide_variation', function () {
			var $selector = jQuery(this).closest( '.product-gallery-summary' ),
				$gallery = $selector.find( '.woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image' ).eq(0),
				imageUrl = $gallery.find( 'a' ).attr( 'href' ),
				$image = $gallery.find( 'img' );

			$image.attr( 'data-zoom', imageUrl );
		});
	}

	productLightBox() {
		const settings = this.getElementSettings();
		var $selector = jQuery('.woocommerce-product-gallery');

		jQuery('.woocommerce-product-gallery__image').on( 'click', 'a', function (e) {
			return false;
		});

		if( ! $selector ) {
			return;
		}

		if( ! settings.product_image_lightbox ) {
			return
		}
		lightBoxButton();
		jQuery(document.body).on( 'glozin_product_gallery_lightbox', function(){
			lightBoxButton();
		} );

		jQuery(document).on( 'click', '.glozin-button--product-lightbox', function (e) {
			e.preventDefault();

			var pswpElement = jQuery( '.pswp' )[0],
				items       = getGalleryItems( jQuery(this).siblings( '.woocommerce-product-gallery__wrapper' ).find( '.woocommerce-product-gallery__image' ) ),
				clicked = jQuery(this).siblings( '.woocommerce-product-gallery__wrapper' ).find( '.swiper-slide-active' );

			var options = jQuery.extend( {
				index: jQuery( clicked ).index(),
				addCaptionHTMLFn: function( item, captionEl ) {
					if ( ! item.title ) {
						captionEl.children[0].textContent = '';
						return false;
					}
					captionEl.children[0].textContent = item.title;
					return true;
				}
			}, wc_single_product_params.photoswipe_options );

			// Initializes and opens PhotoSwipe.
			var photoswipe = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options );
			photoswipe.init();
		});

		function lightBoxButton() {
			jQuery('.woocommerce-product-gallery__image').on( 'click', 'a', function (e) {
				return false;
			});

			$selector.append('<a href="#" class="glozin-button--product-lightbox top-15 end-15 position-absolute d-none d-flex-md align-items-center justify-content-center rounded-100 z-2"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="currentColor"> <path d="M0.000357628 13.3636L0.000596046 10.1813C0.000596046 9.82984 0.28544 9.54512 0.636787 9.54512C0.988253 9.54512 1.27286 9.82984 1.27286 10.1814V11.8261L4.96974 8.12603C5.09417 8.00148 5.25721 7.93963 5.42013 7.93963C5.58281 7.93963 5.7455 8.0016 5.8698 8.12591C6.1183 8.37416 6.11853 8.77699 5.87016 9.02549L2.17208 12.7271H3.81643C4.16789 12.7271 4.45274 13.0121 4.45274 13.3637C4.45274 13.715 4.16777 14 3.81643 14H0.636787C0.467907 14 0.306178 13.9329 0.186758 13.8134C0.067338 13.6941 0.000357628 13.532 0.000357628 13.3636ZM0.636668 4.45524C0.988253 4.45524 1.27286 4.16992 1.27286 3.81869V2.17399L4.90777 5.77791C5.1565 6.02641 5.57638 6.02665 5.82487 5.77815C6.07348 5.53002 6.08206 5.12694 5.83381 4.87857L2.23561 1.27286H3.88174H3.88305C4.23452 1.27286 4.51972 0.988133 4.51984 0.636548C4.51995 0.285439 4.23559 0.000356674 3.884 0.000356674L0.70484 0C0.53584 0 0.339906 0.0670996 0.220843 0.186399C0.101542 0.3057 0.000238419 0.467548 0.000238419 0.636189V3.81881C0.000357628 4.17004 0.285321 4.45524 0.636668 4.45524ZM9.09271 5.80592L12.7273 2.17375V3.81881C12.7273 4.17028 13.0065 4.45452 13.3579 4.45452H13.3552C13.7067 4.45452 13.9902 4.16992 13.9902 3.81881L13.99 0.636667C13.99 0.467787 13.9227 0.305939 13.8034 0.186638C13.6838 0.0672178 13.5217 0.000237465 13.353 0.000237465H10.1732C9.82174 0.000237465 9.5369 0.285201 9.5369 0.636548C9.5369 0.988253 9.82186 1.2731 10.1732 1.2731H11.8171L8.18705 4.90646C7.93832 5.15483 7.94153 5.55826 8.19003 5.8064C8.43852 6.05453 8.84409 6.0543 9.09271 5.80592ZM11.8283 12.6698H10.1842C9.8327 12.6698 9.54798 12.9544 9.54798 13.3058C9.54798 13.6574 9.83282 13.9423 10.1842 13.9423L13.3636 13.9426H13.3637C13.5326 13.9426 13.6942 13.8758 13.8137 13.7565C13.9329 13.6372 14 13.4755 14 13.3064L13.9996 10.124C13.9996 9.77299 13.7148 9.48767 13.3635 9.48767C13.012 9.48767 12.7273 9.77299 12.7273 10.124V11.7689L9.05934 8.09802C8.93503 7.97359 8.77199 7.91138 8.60907 7.91138C8.4465 7.91138 8.28358 7.97335 8.1594 8.09766C7.91079 8.34592 7.91043 8.74911 8.15904 8.99784L11.8283 12.6698Z" fill="currentColor"></path></svg></a>');
		}

		function getGalleryItems( $slides ) {
			var items = [];

			if ( $slides.length > 0 ) {
				$slides.each( function( i, el ) {
					var img = jQuery( el ).find( 'img' );

					if ( jQuery( el ).hasClass('glozin-product-video') ) {
						var video = jQuery( el ).find('.glozin-video-wrapper').html();

						if( video.length ) {
							var item = {
								html: '<div class="pswp__video">'+ video +'</div>'
							};
							items.push( item );
						}
					} else if ( img.length ) {
						var large_image_src = img.attr( 'data-large_image' ),
							large_image_w   = img.attr( 'data-large_image_width' ),
							large_image_h   = img.attr( 'data-large_image_height' ),
							alt             = img.attr( 'alt' ),
							item            = {
								alt  : alt,
								src  : large_image_src,
								w    : large_image_w,
								h    : large_image_h,
								title: img.attr( 'data-caption' ) ? img.attr( 'data-caption' ) : img.attr( 'title' )
							};
						items.push( item );
					}
				} );
			}

			return items;
		};
	};

	onInit() {
		super.onInit();
		var self = this;
		if( this.elements.$productGallery.hasClass( 'woocommerce-product-gallery--vertical' ) ) {
			self.productGallery(true);
			this.elements.$productGallery.on('product_thumbnails_slider_vertical wc-product-gallery-after-init', function(){
				self.productGallery(true);
			});
		}

		if( this.elements.$productGallery.hasClass( 'woocommerce-product-gallery--horizontal' ) ) {
			self.productGallery(false);
			this.elements.$productGallery.on('product_thumbnails_slider_horizontal', function(){
				self.productGallery(false);
			});
		}

		self.productImageZoom();
		jQuery(document.body).on( 'glozin_product_gallery_zoom', function(){
			self.productImageZoom();
		} );

		self.productLightBox();
	}
}

class GlozinProductShortDescriptionWidgetHandler extends elementorModules.frontend.handlers.Base {
	onInit() {
        super.onInit();

		var $selector =  jQuery(document).find( '.short-description__content' );

		$selector.each( function () {
			if( jQuery(this)[0].scrollHeight > jQuery(this)[0].clientHeight ) {
				jQuery(this).siblings( '.short-description__more' ).removeClass( 'hidden' );
			}
		});

		jQuery( document.body ).on( 'click', '.short-description__more', function(e) {
			e.preventDefault();

			var $settings = jQuery(this).data( 'settings' ),
				$more     = $settings.more,
				$less     = $settings.less;

			if( jQuery(this).hasClass( 'less' ) ) {
				jQuery(this).removeClass( 'less' );
				jQuery(this).text( $more );
				jQuery(this).siblings( '.short-description__content' ).removeAttr( 'style' );
			} else {
				jQuery(this).addClass( 'less' );
				jQuery(this).text( $less );
				jQuery(this).siblings( '.short-description__content' ).css( '-webkit-line-clamp', 'inherit' );
			}
		});
    }
}

class GlozinProductDataTabsWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				productTabs: '.woocommerce-tabs--dropdown',
			}
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings('selectors');
		return {
		  $productTabs: this.$element.find(selectors.productTabs),
		};
	}

	onInit() {
		super.onInit();
		const settings = this.getElementSettings();

		if( settings.product_tabs_layout !== 'accordion' ) {
			return;
		}

		this.elements.$productTabs.on( 'click', '.woocommerce-tabs-title', function() {
			var self = jQuery(this);
			if( self.hasClass('active') ) {
				if( self.closest('.woocommerce-tabs--dropdown').hasClass('wc-tabs-first--opened') ) {
					self.closest('.woocommerce-tabs--dropdown').removeClass('wc-tabs-first--opened');
				}

				self.removeClass('active');
				self.siblings('.woocommerce-tabs-content').slideUp(200);
			} else {
				self.addClass('active');
				self.siblings('.woocommerce-tabs-content').slideDown(200);
			}
		});

		jQuery( 'a.woocommerce-review-link' ).on( 'click', function() {
			jQuery('#tab-reviews .woocommerce-tabs-title:not(.active)').trigger('click');
		});

		jQuery(document).ready(function() {
			if ( window.location.href.indexOf( '#reviews' ) > -1 ) {
				jQuery('#tab-reviews .woocommerce-tabs-title:not(.active)').trigger('click');
			}
		});
	}
}

class GlozinProductReviewsWidgetHandler extends elementorModules.frontend.handlers.Base {
	bindEvents() {
		var $button = this.$element.find( '.glozin-form-review' ),
			text = $button.data( 'text' ),
			textCancel = $button.data( 'text-cancel' ),
			$form = this.$element.find( '.glozin-review-form' );

		if( ! $button.length ) {
			return;
		}

		if( ! $form.length ) {
			return;
		}

		$button.on( 'click', function() {
			if( ! $button.hasClass( 'active' ) ) {
				$button.addClass( 'active' );
				$button.text( textCancel );
				$form.slideDown();
			} else {
				$button.removeClass( 'active' );
				$button.text( text );
				$form.slideUp();
			}
		});
	}
}

class GlozinProductsFilterWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				filter: '.glozin-products-filter',
			}
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings('selectors');
		return {
			$filter: this.$element.find(selectors.filter),
		};
	}

	searchTerms() {
		var $this = jQuery( this ),
		term = $this.val().toLowerCase(),
		$list = $this.next( '.products-filter__options' ).find( '.products-filter__option' );

		if ( term ) {
			$list.hide().filter( function() {
				return jQuery( '.name', this ).text().toLowerCase().indexOf( term ) !== -1;
			} ).show();
		} else {
			$list.show();
		}
    }

	updatePrice(event) {
		event.preventDefault();
		var $item = jQuery( this ).closest( '.products-filter__option' ),
			$filter = $item.closest( '.filter' ),
			$value = $item.data('value'),
			$box = $filter.find('.product-filter-box');

		if ( ! $filter.hasClass( 'price' ) ) {
			return;
		}

		$box.find('input[name="min_price"]').val($value.min);
		$box.find('input[name="max_price"]').val($value.max);
	}

	toggleItem (event) {
		event.preventDefault();

		var $item = jQuery( this ).closest( '.products-filter__option' ),
			$filter = $item.closest( '.filter' ),
			$input = $item.closest( '.products-filter__options' ).next( 'input[type=hidden]' ),
			current = $input.val(),
			value = $item.data( 'value' ),
			form = $item.closest( 'form' ).get( 0 ),
			index = -1;

		if ( $filter.hasClass( 'multiple' ) ) {
			current = current ? current.split( ',' ) : [];
			index = current.indexOf( value );
			index = (-1 !== index) ? index : current.indexOf( value.toString() );

			if ( index !== -1 ) {
				current = _.without( current, value );
			} else {
				current.push( value );
			}

			$input.val( current.join( ',' ) );
			$item.toggleClass( 'selected' );

			$input.prop( 'disabled', current.length <= 0 );

			if ( $filter.hasClass( 'attribute' ) ) {
				var $queryTypeInput = $input.next( 'input[name^=query_type_]' );

				if ( $queryTypeInput.length ) {
					$queryTypeInput.prop( 'disabled', current.length <= 1 );
				}
			}
		} else {
			// @note: Ranges are always single selection.
			if ( $item.hasClass( 'selected' ) ) {
				$input.val( '' ).prop( 'disabled', true );
				$item.removeClass( 'selected' );
				if ( $filter.hasClass( 'ranges' ) ) {
					$input.next( 'input[type=hidden]' ).val( '' ).prop( 'disabled', true );

					var $box = $filter.find('.product-filter-box');

					$box.find('input[name="min_price"]').val( '' ).prop( 'disabled', true );
					$box.find('input[name="max_price"]').val( '' ).prop( 'disabled', true );
				}
			} else {
				$filter.find( '.products-filter__option.selected' );
				$item.addClass( 'selected' );
				$input.val( value ).prop( 'disabled', false );

				if ( $filter.hasClass( 'ranges' ) ) {
					$input.val( value.min ).prop( 'disabled', ! value.min );
					$input.next( 'input[type=hidden]' ).val( value.max ).prop( 'disabled', ! value.max );
				}
			}
		}

		if ( $filter.hasClass( 'products-filter--collapsible' ) && $item.children( 'ul' ).length ) {
			event.data.widget.checkToggleCollapse( $item );
		}

		jQuery( document.body ).trigger( 'glozin_products_filter_change', [form] );
	}

	checkToggleCollapse( $item ) {
		var $children = $item.children( 'ul' );

		if ( ! $children.length ) {
			return;
		}

		if ( $item.hasClass( 'selected' ) && ! $item.hasClass( 'active' ) ) {
			$children.stop( true, true ).slideDown( function() {
				$item.addClass( 'active' );
			} );
		}

		if ( ! $item.hasClass( 'selected' ) && $item.hasClass( 'active' ) ) {
			// Don't close if subitems are selected.
			if ( $item.find( '.products-filter__option.selected' ).length ) {
				return;
			}

			$children.stop( true, true ).slideUp( function() {
				$item.removeClass( 'active' );
			} );
		}
	}

	toggleCollapse( event ) {
		var $option = jQuery( this ).closest( '.products-filter__option' ),
			$children = $option.children( 'ul' );

		if ( ! $children.length ) {
			return;
		}

		event.preventDefault();

		$children.stop( true, true ).slideToggle( function() {
			$option.toggleClass( 'active' );
		} );
	}

	triggerItemChange() {
		var form = jQuery( this ).closest( 'form' ).get( 0 );
		jQuery( document.body ).trigger( 'glozin_products_filter_change', [form] );
	}

	resetFilters() {
		var $form = jQuery( this ).closest( 'form' );

		$form.get( 0 ).reset();
		$form.find( '.selected' ).removeClass( 'selected' );
		$form.find( 'select' ).val( '' ).prop('disabled', true);
		$form.find( ':input' ).not( '[type="button"], [type="submit"], [type="reset"]' )
			.val( '' )
			.filter('[type="hidden"],[name="min_price"], [name="max_price"]').prop('disabled', true);

		$form.trigger( 'submit' );
		jQuery( document.body ).trigger( 'glozin_products_filter_reseted', [$form] );
	}

	removeFiltered( event ) {
		event.preventDefault();

		var $el = jQuery( this ),
			$widget = $el.closest( ' .products-filter-widget--elementor' ),
			$form = $widget.find( 'form' ),
			name = $el.data( 'name' ),
			key = name.replace( /^filter_/g, '' ),
			value = $el.data( 'value' ),
			$filter = $widget.find( '.filter.' + key );

		$el.remove();

		if ( $filter.length ) {
			var $input = $filter.find( ':input[name=' + name + ']' ),
				current = $input.val();

			if( name == 'price' ) {
				$filter.find(':input[name=min_price]').val('');
				$filter.find(':input[name=max_price]').val('');
				$filter.find('.products-filter__option').removeClass('selected');
			} else {
				if ( $input.is( 'select' ) ) {
					$input.prop( 'selectedIndex', 0 );
					$input.trigger( 'change' );
				} else {
					current = current.replace( ',' + value, '' );
					current = current.replace( value, '' );
					$input.val( current );

					if ( '' == current ) {
						$input.prop( 'disabled', true );
					}

					$filter.find( '[data-value="' + value + '"]' ).removeClass( 'selected' );
				}
			}

			$form.trigger( 'submit' );
		}
	}

	ajaxSearch( event ) {
		event.data.widget.sendAjaxRequest( this );
		return false;
	}

	collapseFilterWidget( event ) {
		if ( ! jQuery(this).closest( 'form' ).hasClass('has-collapse')) {
            return;
        }

		if (jQuery(this).closest( '.glozin-products-filter--horizontal' ).length ) {
			return;
		}

		event.preventDefault();

		jQuery(this).next().slideToggle();
		jQuery(this).closest('.products-filter__filter').toggleClass('glozin-active');
	}

	instantSearch( event, form ) {
		var settings = jQuery( form ).data( 'settings' );

		if ( ! settings.instant ) {
			return;
		}

		event.data.widget.sendAjaxRequest( form );
	}

	updateURL( event, response, url, form ) {
		var settings = jQuery( form ).data( 'settings' );

		if ( ! settings.change_url ) {
			return;
		}

		if ( '?' === url.slice( -1 ) ) {
			url = url.slice( 0, -1 );
		}

		url = url.replace( /%2C/g, ',' );

		history.pushState( null, '', url );
	}

	updateForm( event, response, url, form ) {
		var $widget = jQuery( form ).closest( '.elementor-widget-glozin-products-filter' ),
			widgetId = $widget.data( 'id' ),
			$newWidget = jQuery( '.elementor-widget-glozin-products-filter[data-id="' + widgetId + '"', response );

		if ( ! $newWidget.length ) {
			return;
		}

		if( jQuery('#glozin-shop-content').length && jQuery('#glozin-shop-content').hasClass('loading') ) {
			jQuery('#glozin-shop-content').removeClass('loading');
		}

		jQuery( '.filters', form ).html( jQuery( '.filters', $newWidget ).html() );
		jQuery( '.products-filter__activated', $widget ).html( jQuery( '.products-filter__activated', $newWidget ).html() );

		jQuery( document.body ).trigger( 'glozin_products_filter_widget_updated', [form] );
	}

	sendAjaxRequest( form ) {
		var self = this,
			$form = jQuery( form ),
			$content = jQuery('#glozin-shop-content'),
			$container = jQuery('.glozin-archive-products ul.products'),
			$notice = jQuery('.site-content .woocommerce-notices-wrapper'),
			$noticeNF = jQuery('.glozin-archive-products .woocommerce-info'),
			$count = jQuery('.glozin-result-count'),
			$page_header = jQuery('.page-header-elementor'),
			$breadcrumb = jQuery('.glozin-woocommerce-breadcrumb'),
			$top_catetories = jQuery('.catalog-top-categories'),
			$filter_active = jQuery('.catalog-toolbar__filters-actived'),
			$removeAll = '<a href="#" class="remove-filtered remove-filtered-all">' + $filter_active.data( 'clear-text' ) + '</a>',
			$inputs = $form.find(':input:not(:checkbox):not(:button)'),
			params = {},
			action = $form.attr('action'),
			separator = action.indexOf('?') !== -1 ? '&' : '?',
			url = action;

		params = $inputs.filter( function() {
			return this.value != '' && this.name != '';
		} ).serializeObject();


		if (params.min_price && params.min_price == $inputs.filter('[name=min_price]').data('min')) {
			delete params.min_price;
		}

		if (params.max_price && params.max_price == $inputs.filter('[name=max_price]').data('max')) {
			delete params.max_price;
		}

		// the filer always contains "filter" param
		// so it is empty if the size less than 2
		if ( _.size( params ) > 1 ) {
			url += separator + jQuery.param(params, true);
		}

		if ($container.hasClass('layout-carousel')) {
			window.location.href = url;
			return false;
		}

		if (!$container.length) {
			$container = jQuery('.glozin-archive-products ul.products');
			jQuery('#site-content .woocommerce-info').replaceWith($container);
		}

		if ( self.ajax ) {
			self.ajax.abort();
		}

		$form.addClass('filtering');
		$container.fadeIn();

		if ($form.closest('.glozin-products-filter__form').find('.sidebar__backdrop').length) {
			$form.closest('.glozin-products-filter__form').find('.sidebar__backdrop').trigger('click');
		} else {
			$form.closest('.glozin-products-filter__form').find('.sidebar__button-close').trigger('click');
		}
		jQuery(document.body).trigger('glozin_products_filter_before_send_request', $container);
		jQuery(document.body).trigger('glozin_progress_bar_start');
		$content.addClass('loading');
		self.ajax = jQuery.get(url, function (response) {
			var $html = jQuery(response),
				$products = $html.find('.glozin-archive-products ul.products'),
				$pagination = $container.next('.woocommerce-pagination'),
				$nav = $html.find('.woocommerce-navigation, .woocomerce-pagination');

			if ( ! $products.children().length ) {
				var $info = $html.find('.glozin-archive-products .woocommerce-info');
				$pagination.fadeOut();
				$container.fadeOut();
				$count.fadeOut();
				$notice.html($info);
				$notice.fadeIn();

			} else {
				var $nav = $products.next('.woocommerce-pagination'),
					$order = jQuery('form.woocommerce-ordering');

				if ($nav.length) {
					if ($pagination.length) {
						$pagination.replaceWith($nav).fadeIn();
					} else {
						$container.after($nav);
					}
				} else {
					$pagination.fadeOut();
				}
				$count.fadeIn();
				$notice.fadeOut();
				$noticeNF.fadeOut();
				$container.fadeIn();
				$products.children().each(function (index, product) {
					jQuery(product).css('animation-delay', index * 100 + 'ms');
				});

				// Modify the ordering form.
				$inputs.each(function () {
					var $input = jQuery(this),
						name = $input.attr('name'),
						value = $input.val();

					if (name === 'orderby') {
						return;
					}

					if ('min_price' === name && value == $input.data('min')) {
						$order.find('input[name="min_price"]').remove();
						return;
					}

					if ('max_price' === name && value == $input.data('max')) {
						$order.find('input[name="max_price"]').remove();
						return;
					}

					$order.find('input[name="' + name + '"]').remove();

					if (value !== '' && value != 0) {
						jQuery('<input type="hidden" name="' + name + '">').val(value).appendTo($order);
					}
				});

				// Replace result count.
				$count.replaceWith($html.find('.glozin-result-count'));

				$page_header.replaceWith($html.find('.page-header-elementor'));

				$breadcrumb.replaceWith($html.find('.glozin-woocommerce-breadcrumb'));

				$top_catetories.replaceWith($html.find('.catalog-top-categories'));

				$container.html($products.html());
				$products.find('li.product').addClass('animated glozinFadeInUp');

				self.changeFilterActivated($html, $filter_active, $removeAll);
				self.changeElements($html);

				jQuery(document.body).trigger('glozin_products_loaded', [$products.children(), false]); // appended = false
			}

			$form.removeClass('filtering');
			$content.removeClass('loading');
			jQuery(document.body).trigger('glozin_products_filter_request_success', [response, url, form]);
			jQuery(document.body).trigger('glozin_progress_bar_complete');
		});
	}

	viewMoreCats(event) {
		var $filter = event ? event.data.widget.elements.$filter : this.elements.$filter,
			$widget = $filter.find('.product_cat'),
			$widgetChild = $filter.find('.products-filter--show-children-only'),
			$items = $widget.find('.products-filter--list > .filter-list-item, .products-filter--checkboxes > .filter-checkboxes-item'),
			catNumbers = parseInt($widget.find('input.widget-cat-numbers').val(), 10);

		if (!$widget.hasClass('products-filter--view-more')) {
			return;
		}

		if ( $widgetChild.find('.products-filter__option').hasClass('selected') ) {
			$items = $widgetChild.find('ul.products-filter--list li.selected .children > .filter-list-item, ul.products-filter--checkboxes li.selected .children > .filter-checkboxes-item');
		}

		var count = $widget.find( $items ).size();

		if (count > catNumbers) {
			$widget.find('.show-more').show();

			if ( $widgetChild.find('ul.products-filter__options > .products-filter__option').hasClass( 'selected' ) ) {
				$widgetChild.find( '.glozin-widget-product-cats-btn' ).addClass( 'btn-children' );
			}
		}

		$widget.find('ul.products-filter--list > .filter-list-item:lt(' + catNumbers + ')').show();
		$widget.find('ul.products-filter--checkboxes > .filter-checkboxes-item:lt(' + catNumbers + ')').show();

		$widgetChild.find('ul.products-filter--list li.selected .children > .filter-list-item:lt(' + catNumbers + ')').show();
		$widgetChild.find('ul.products-filter--checkboxes li.selected .children > .filter-checkboxes-item:lt(' + catNumbers + ')').show();

		$widget.on('click', '.show-more', function () {
			$widget.find( $items ).show();
			jQuery(this).hide();
			$widget.find('.show-less').show();
			$widget.find( '.glozin-widget-product-cats-btn' ).addClass( 'btn-show-item' );
		});

		$widget.on('click', '.show-less', function () {
			$widget.find( 'ul.products-filter--list > .filter-list-item' ).not(':lt(' + catNumbers + ')').hide();
			$widget.find( 'ul.products-filter--checkboxes > .filter-checkboxes-item' ).not(':lt(' + catNumbers + ')').hide();
			$widgetChild.find( 'ul.products-filter--list li.selected .children > .filter-list-item' ).not(':lt(' + catNumbers + ')').hide();
			$widgetChild.find( 'ul.products-filter--checkboxes li.selected .children > .filter-checkboxes-item' ).not(':lt(' + catNumbers + ')').hide();
			jQuery(this).hide();
			$widget.find('.show-more').show();
			$widget.find( '.glozin-widget-product-cats-btn' ).removeClass( 'btn-show-item' );
		});
	}

	initDropdowns( event, form ) {
		if ( ! jQuery.fn.select2 ) {
			return;
		}

		var $container = form ? jQuery( form ) : this.elements.$filter,
			direction = jQuery( document.body ).hasClass( 'rtl' ) ? 'rtl' : 'ltr';

		jQuery( 'select', $container ).each( function() {
			var $select = jQuery( this ),
				$searchBoxText = $select.prev( '.products-filter__search-box' ),
				searchText = $searchBoxText.length ? $searchBoxText.text() : false;

			$select.select2( {
				dir: direction,
				width: '100%',
				minimumResultsForSearch: searchText ? 3 : -1,
				dropdownCssClass: 'products-filter-dropdown',
				dropdownParent: $select.parent()
			} );
		} );
	}

	initSliders( event, form ) {
		jQuery( document.body ).trigger( 'init_price_filter' );

		event.data.widget.removeSliderInputs( form );
	}

	updateActivatedItems( event, form ) {
		var $container = form ? jQuery( form ) : this.elements.$filter;

		if ( jQuery.trim( $container.find( '.products-filter__activated-items' ).html() ) ) {
			$container.find('.products-filter__activated').removeClass( 'hidden' );
		} else {
			$container.find('.products-filter__activated').addClass( 'hidden' );
		}
	}

	removeSliderInputs( form ) {
		var $container = form ? jQuery( form ) : this.elements.$filter;

		jQuery( '.widget_price_filter', $container ).find( 'input[type=hidden]' ).not( '[name=min_price], [name=max_price]' ).remove();
	}


	collapseFilterWidgetMobile() {
		if ( ! this.elements.$filter.find( 'form' ).hasClass('has-collapse')) {
            return;
        }

		if ( ! this.elements.$filter.find( 'form' ).hasClass('products-filter__filter-section--collapse') ) {
			return;
		}

		if (this.elements.$filter.find( '.glozin-products-filter--horizontal' ).length ) {
			return;
		}

		var $this = this.elements.$filter.find('.products-filter__filter');

		jQuery(window).on('resize', function () {
			if (jQuery(window).width() < 768) {
				$this.addClass('glozin-active');
			} else {
				$this.removeClass('glozin-active');
				$this.find('.products-filter__filter-control').removeAttr('style');
			}

		}).trigger('resize');

	}

	scrollFilters() {
		if( ! jQuery(".glozin-archive-products").length ) {
			return;
		}

		var $height = 0;

		jQuery(window).on( 'resize', function () {
			if ( jQuery(window).width() < 1024 ) {
				if (jQuery( '.glozin-products-filter__form' ).hasClass( 'offscreen-panel--open' )) {
					if (jQuery( '.glozin-products-filter__form' ).find('.sidebar__backdrop').length) {
						jQuery( '.glozin-products-filter__form' ).find('.sidebar__backdrop').trigger('click');
					} else {
						jQuery( '.glozin-products-filter__form' ).find('.sidebar__button-close').trigger('click');
					}
				}
				$height += 100;
			} else {
				var $sticky 	= jQuery( document.body ).hasClass('glozin-header-sticky') ? jQuery( '#site-header .header-sticky' ).outerHeight() : 0,
					$wpadminbar = jQuery('#wpadminbar').is(":visible") ? jQuery('#wpadminbar').height() : 0;

					$height 	= $sticky + $wpadminbar + 150;
			}
		}).trigger( 'resize' );

		jQuery('html,body').stop().animate({
				scrollTop: jQuery(".glozin-archive-products").offset().top - $height
			},
		'slow');
	}

	currentFilterActivated() {
		var self = this,
			$filter_active = jQuery('.catalog-toolbar__filters-actived'),
			$removeAll = '<a href="#" class="remove-filtered remove-filtered-all">' + $filter_active.data( 'clear-text' ) + '</a>';
		
		if ( self.elements.$filter.find('.products-filter__activated-items').children().length > 0 ) {
			$filter_active.parent().removeClass('hidden');
			$filter_active.html(self.elements.$filter.find('.products-filter__activated-items').html() + $removeAll);
		}
	}

	changeFilterActivated($html, $filter_active, $removeAll) {
		if ( $html.find('.products-filter__activated-items').children().length > 0 ) {
			$filter_active.parent().removeClass('hidden');
			$filter_active.html($html.find('.products-filter__activated-items').html() + $removeAll);
		} else {
			$filter_active.html('');
			$filter_active.parent().addClass('hidden');
		}
	}

	removeFilteredActived() {
		var $filter = this.elements.$filter;
		jQuery(document).on('click', '.remove-filtered', function(e) {
			e.preventDefault();

			var $this = jQuery(this),
				value = $this.data( 'value' );

			if( $this.hasClass('remove-filtered-all') ) {
				$filter.find( '.products-filter__activated-heading .reset-button' ).trigger( 'click' );
			} else {
				if ( value !== 'undefined' ) {
					$filter.find( ".remove-filtered[data-value='" + value + "']" ).trigger( 'click' );
				}
			}

			jQuery('#glozin-shop-content').addClass('loading');
		});
	}

	changeElements( $html ) {
		if( $html.find('.catalog-toolbar__result-count').length && jQuery('.catalog-toolbar__result-count').length ) {
			jQuery('.catalog-toolbar__result-count').replaceWith( $html.find( '.catalog-toolbar__result-count' ) );
		}
	}

	catalogFiltersHorizontal() {
		this.elements.$filter.on('click', '.products-filter__filter', function (e) {
			e.preventDefault();

			var $this = jQuery(this);

			if( ! $this.closest('.glozin-products-filter--horizontal').length ) {
				return;
			}

			if( ! $this.hasClass('glozin-active') ) {
				$this.siblings('.products-filter__filter').removeClass('glozin-active');
			}

			$this.toggleClass('glozin-active');
		});
	}

	onInit() {
		super.onInit();

		this.initDropdowns();
		this.removeSliderInputs();
		this.viewMoreCats('');
		this.collapseFilterWidgetMobile();
		this.catalogFiltersHorizontal();

		this.elements.$filter
		.off('input', '.products-filter__search-box').on( 'input', '.products-filter__search-box', this.searchTerms )
		.off('click', '.products-filter__option-name').on( 'click', '.products-filter__option-name', this.updatePrice)
		.off('click', '.products-filter__option-name, .products-filter__options .swatch').on( 'click', '.products-filter__option-name, .products-filter__options .swatch', { widget: this }, this.toggleItem)
		.off('click', '.products-filter--collapsible .products-filter__option-toggler').on( 'click', '.products-filter--collapsible .products-filter__option-toggler', this.toggleCollapse )
		.off('change', 'input, select').on( 'change', 'input, select', this.triggerItemChange )
		.off('click', '.reset-button').on( 'click', '.reset-button', this.resetFilters )
		.off('click', '.remove-filtered').on( 'click', '.remove-filtered', this.removeFiltered )
		.off('submit', 'form.ajax-filter').on( 'submit', 'form.ajax-filter', { widget: this }, this.ajaxSearch )
		.off('click', '.products-filter__filter-name').on( 'click', '.products-filter__filter-name', this.collapseFilterWidget );

		jQuery( document.body )
		.off('glozin_products_filter_change').on( 'glozin_products_filter_change', { widget: this }, this.instantSearch )
		.on( 'glozin_products_filter_request_success', this.updateURL )
		.on( 'glozin_products_filter_request_success', this.updateForm )
		.on( 'glozin_products_filter_request_success', { widget: this }, this.viewMoreCats )
		.on( 'glozin_products_filter_widget_updated', this.initDropdowns )
		.on( 'glozin_products_filter_widget_updated', { widget: this }, this.initSliders )
		.on( 'glozin_products_filter_widget_updated', this.updateActivatedItems )
		.off('glozin_products_filter_before_send_request').on( 'glozin_products_filter_before_send_request', this.scrollFilters);

		if( jQuery('.catalog-toolbar__filters-actived').length ) {
			this.currentFilterActivated();
			this.removeFilteredActived();
		}
	}
}

class GlozinProductSidebarWidgetHandler extends elementorModules.frontend.handlers.Base {
	bindEvents() {
		var self = this;
		jQuery( window ).on( 'resize', function () {
			if( self.$element.find( '.single-product-sidebar-panel' ).hasClass( 'offscreen-panel--open' ) ) {
				if( jQuery( window ).width() > 1199 ) {
					if( self.$element.find( '.single-product-sidebar-panel' ).hasClass( 'desktop-sidebar' ) ) {
						self.$element.find( '.single-product-sidebar-panel .sidebar__button-close' )[0].click();
					}
				}

				if( jQuery( window ).width() < 1200 && jQuery( window ).width() > 767 ) {
					if( self.$element.find( '.single-product-sidebar-panel' ).hasClass( 'tablet-sidebar' ) ) {
						self.$element.find( '.single-product-sidebar-panel .sidebar__button-close' )[0].click();
					}
				}

				if( jQuery( window ).width() < 768 ) {
					if( self.$element.find( '.single-product-sidebar-panel' ).hasClass( 'mobile-sidebar' ) ) {
						self.$element.find( '.single-product-sidebar-panel .sidebar__button-close' )[0].click();
					}
				}
			}
		});
	}

	onInit() {
		super.onInit();
		const settings = this.getElementSettings();

		if( settings.toggle_heading !== 'yes' ) {
			return;
		}

		var $heading = this.$element.find( '.glozin-heading' );

		$heading.append( '<span class="em-collapse-icon"></span>' );

		$heading.on( 'click', function() {
			jQuery(this).toggleClass( 'active' );

			if( jQuery(this).hasClass( 'active' ) ) {
				jQuery( this ).closest( '.elementor-widget-glozin-heading' ).siblings().slideUp();
			} else {
				jQuery( this ).closest( '.elementor-widget-glozin-heading' ).siblings().slideDown();
			}
		});
	}
}

class GlozinArchiveTopCategoriesWidgetHandler extends elementorModules.frontend.handlers.CarouselBase {
	getDefaultSettings() {
		const settings = super.getDefaultSettings(),
			  $el = this.$element.find( '.glozin-carousel--elementor' );

		if( $el.hasClass( 'swiper' ) ) {
			settings.selectors.carousel = '.glozin-carousel--elementor';
		} else {
			settings.selectors.carousel = '.glozin-carousel--elementor .swiper';
		}

		return settings;
	}

	getSwiperSettings() {
		const self = this,
			  settings = super.getSwiperSettings(),
			  elementSettings = this.getElementSettings(),
			  slidesToShow = +elementSettings.slides_to_show || 3,
			  isSingleSlide = 1 === slidesToShow,
			  elementorBreakpoints = elementorFrontend.config.responsive.activeBreakpoints,
			  defaultSlidesToShowMap = {
					mobile: 1,
					tablet: isSingleSlide ? 1 : 2,
				};

		let argsBreakpoints = {};
		let lastBreakpointSlidesToShowValue = slidesToShow;

		var spaceBetween = elementSettings.image_spacing_custom ? elementSettings.image_spacing_custom : 0;

		settings.spaceBetween = elementSettings.custom_space_between == 'yes' ? 0 : spaceBetween;

		if( elementSettings.slides_rows && parseInt(elementSettings.slides_rows) > 1 ) {
			settings.grid = {
				fill: 'row',
				rows: elementSettings.slides_rows
			};
			settings.loop = false;
		}

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
					argsBreakpoints[ elementorBreakpoints[ breakpointName ].value ].spaceBetween = this.getSpaceBetween( breakpointName );
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
		});

		settings.on.resize = function () {
			var self           = this,
				$productThumbnail = this.$el.closest( '.glozin-carousel--elementor' ).find('.product-thumbnail'),
				$postThumbnail    = this.$el.closest( '.glozin-carousel--elementor' ).find('.post-thumbnail'),
				$iframeWidth      = jQuery(self.slides).find('.gz-ratio--iframe').length ? jQuery(self.slides).find('.gz-ratio--iframe').width() : 0;

			if( $productThumbnail.length > 0 ) {
				jQuery(this.$el).imagesLoaded(function () {
					var	heightThumbnails = $productThumbnail.outerHeight(),
						top = ( ( heightThumbnails / 2 ) + 15 ) + 'px';

					jQuery(self.navigation.$nextEl).css({ '--gz-arrow-top': top });
					jQuery(self.navigation.$prevEl).css({ '--gz-arrow-top': top });
				});
			}

			if( $postThumbnail.length > 0 ) {
				jQuery(this.$el).imagesLoaded(function () {
					var	heightThumbnails = $postThumbnail.outerHeight(),
						top = ( heightThumbnails / 2 ) + 'px';

					jQuery(self.navigation.$nextEl).css({ '--gz-arrow-top': top });
					jQuery(self.navigation.$prevEl).css({ '--gz-arrow-top': top });
				});
			}

			if( $iframeWidth > 0 ) {
				jQuery(self.slides).find('.gz-ratio--iframe').css({ '--gz-item-iframe-width': $iframeWidth });

				if( jQuery(self.slides).find('.gz-ratio--iframe iframe').length > 0 ) {
					var ratioPercentage = jQuery(self.slides).find('.gz-ratio--iframe iframe').height() / jQuery(self.slides).find('.gz-ratio--iframe iframe').width() * 100;
					jQuery(self.slides).find('.gz-ratio--iframe').css({ '--gz-ratio-percent': ratioPercentage + '%' });
				}
			}
		}

		return settings;
	}

	onEditSettingsChange( propertyName ) {
		if( this.swiper === undefined ) {
			return;
		}

		if ( 'activeItemIndex' === propertyName ) {
			this.swiper.slideToLoop( this.getEditSettings( 'activeItemIndex' ) - 1 );
		}
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

	onInit() {
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
}

class GlozinWCCartWidgetHandler extends elementorModules.frontend.handlers.Base {
	updateAuto() {
		const settings = this.getElementSettings();

		if( ! settings.cart_button_update_auto ) {
			return;
		}

		jQuery( '.elementor-widget-glozin-wc-cart' ).on( 'change', '.quantity .qty', function() {
			if( jQuery(this).closest( '.woocommerce-cart-form' ).find( '[name="update_cart"]' ).is( ':disabled' ) ) {
				jQuery(this).closest( '.woocommerce-cart-form' ).find( '[name="update_cart"]' ).prop( 'disabled', false );
			}

			if( jQuery(this).val() == 0 ) {
				jQuery(this).closest( '.woocommerce-cart-form__cart-item' ).find( 'a.remove' ).trigger( 'click' );
			} else {
				jQuery(this).closest( '.woocommerce-cart-form' ).find( '[name="update_cart"]' ).trigger( 'click' );
			}
		});
	}

	input_quantity() {
        jQuery( '.elementor-widget-glozin-wc-cart' ).on( 'keyup', '.quantity .qty', function( e ) {
            if( jQuery(this).val() ) {
                jQuery(this).attr( 'value', jQuery(this).val() );
            } else {
                jQuery(this).attr( 'value', 0 );
            }
        });
    }

	onInit() {
		this.updateAuto();
		this.input_quantity();
	}
}

class GlozinWCCartCrossSellWidgetHandler extends elementorModules.frontend.handlers.CarouselBase {
	getDefaultSettings() {
		const settings = super.getDefaultSettings();

		settings.selectors.carousel = '.glozin-products-carousel--elementor > .cross-sells';
		settings.selectors.swiperArrow = this.$element.find('.glozin-products-carousel--elementor > .swiper-button').get(0);
		settings.selectors.swiperWrapper = '.products';
		settings.selectors.slideContent = '.product';

		return settings;
	}

	getSwiperSettings() {
		const settings = super.getSwiperSettings(),
			  elementSettings = this.getElementSettings(),
			  elementorBreakpoints = elementorFrontend.config.responsive.activeBreakpoints;

		var changed = false;
		Object.keys(elementorBreakpoints).forEach(breakpoint => {
			if( elementorBreakpoints[breakpoint].value !== elementorBreakpoints[breakpoint].default_value ) {
				return;
			}

			elementorBreakpoints[breakpoint].value = parseInt( elementorBreakpoints[breakpoint].value ) + 1;

			changed = true;
		});

		if ( changed ) {
			const 	slidesToShow = +elementSettings.slides_to_show || 3,
					isSingleSlide = 1 === slidesToShow,
					elementorBreakpoints = elementorFrontend.config.responsive.activeBreakpoints,
					defaultSlidesToShowMap = {
						mobile: 1,
						tablet: isSingleSlide ? 1 : 2,
					};

			let argsBreakpoints = {};
			let lastBreakpointSlidesToShowValue = slidesToShow;

			Object.keys( elementorBreakpoints ).reverse().forEach( ( breakpointName ) => {
				// Tablet has a specific default `slides_to_show`.
				const defaultSlidesToShow = defaultSlidesToShowMap[ breakpointName ] ? defaultSlidesToShowMap[ breakpointName ] : lastBreakpointSlidesToShowValue;

				argsBreakpoints[ elementorBreakpoints[ breakpointName ].value ] = {
					slidesPerView: +elementSettings[ 'slides_to_show_' + breakpointName ] || defaultSlidesToShow,
					slidesPerGroup: +elementSettings[ 'slides_to_scroll_' + breakpointName ] || 1,
				};

				if ( elementSettings.image_spacing_custom ) {
					argsBreakpoints[ elementorBreakpoints[ breakpointName ].value ].spaceBetween = this.getSpaceBetween( breakpointName );
				}

				lastBreakpointSlidesToShowValue = +elementSettings[ 'slides_to_show_' + breakpointName ] || defaultSlidesToShow;
			} );


			settings.breakpoints = argsBreakpoints;
		}

		settings.navigation.nextEl = this.$element.find('.glozin-products-carousel--elementor > .elementor-swiper-button-next').get(0);
		settings.navigation.prevEl = this.$element.find('.glozin-products-carousel--elementor > .elementor-swiper-button-prev').get(0);

		settings.on.beforeInit = function () {
			var self = this,
				$productThumbnail =	this.$el.closest( '.glozin-products-carousel--elementor' ).find('.product-thumbnail');

			if( $productThumbnail.length > 0 ) {
				jQuery(this.$el).imagesLoaded(function () {
					var	heightThumbnails = $productThumbnail.outerHeight(),
						top = ( ( heightThumbnails / 2 ) + parseFloat( self.$el.closest( '.swiper' ).css( 'padding-top') ) ) + 'px';

					jQuery(self.navigation.$nextEl).css({ '--em-arrow-top': top });
					jQuery(self.navigation.$prevEl).css({ '--em-arrow-top': top });
				});
			}
		}

		settings.on.resize = function () {
			var self = this,
				$productThumbnail =	this.$el.closest( '.glozin-products-carousel--elementor' ).find('.product-thumbnail');

			if( $productThumbnail.length > 0 ) {
				jQuery(this.$el).imagesLoaded(function () {
					var	heightThumbnails = $productThumbnail.outerHeight(),
						top = ( ( heightThumbnails / 2 ) + parseFloat( self.$el.closest( '.swiper' ).css( 'padding-top') ) ) + 'px';

					jQuery(self.navigation.$nextEl).css({ '--em-arrow-top': top });
					jQuery(self.navigation.$prevEl).css({ '--em-arrow-top': top });
				});
			}
		}

		return settings;
	}

	async onInit() {
        super.onInit();

		this.elements.$swiperContainer.addClass( 'swiper' );
		this.elements.$swiperContainer.find('.products').addClass('swiper-wrapper');
		this.elements.$swiperContainer.find('.product').addClass('swiper-slide');

		if ( ! this.elements.$swiperContainer.length ) {
			return;
		}

		await super.initSwiper();

		const elementSettings = this.getElementSettings();
		if ( 'yes' === elementSettings.pause_on_hover ) {
			this.togglePauseOnHover( true );
		}
    }
}

class GlozinProductCarouselBuilderWidgetHandler extends elementorModules.frontend.handlers.CarouselBase {
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

		var spaceBetween = elementSettings.image_spacing_custom ? elementSettings.image_spacing_custom : 0;

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

class GlozinArchivePageHeaderWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				pageHeader: '.page-header--shop',
			}
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings('selectors');
		return {
			$pageHeader: this.$element.find(selectors.pageHeader),
		};
	}

	bindEvents() {
		this.elements.$pageHeader.on( 'click', '.shop-header__more', function(e) {
			e.preventDefault();

			var $settings = jQuery(this).data( 'settings' ),
				$wrapper   = jQuery(this).closest( '.shop-header__more-wrapper' ),
				$content  = $wrapper.siblings('.shop-header__description-inner'),
				$more     = $settings.more,
				$less     = $settings.less;

				if ($content.length === 0) {
					return;
				};

				if (!$content.data("defaultHeight")) {
					$content.data("defaultHeight", $content.outerHeight());
				}

				var defaultHeight = $content.data("defaultHeight");

			if( $wrapper.hasClass( 'less' ) ) {
				$wrapper.removeClass( 'less' );
				jQuery(this).text( $more );
				$content.css({
					"max-height": defaultHeight + "px",
				});
			} else {
				$wrapper.addClass( 'less' );
				jQuery(this).text( $less );
				$content.css( '-webkit-line-clamp', 'inherit' );
				$content.css({
					"max-height": $content[0].scrollHeight + "px",
				});
			}
		});
	}

	onInit() {
		super.onInit();

		jQuery(".shop-header__description-inner").each(function () {
			var $content = jQuery(this);
			var defaultHeight = $content.outerHeight();

			$content.data("defaultHeight", defaultHeight);

			$content.css({
				"max-height": defaultHeight + "px"
			});

			if( $content[0].scrollHeight > $content[0].clientHeight ) {
				$content.siblings( '.shop-header__more-wrapper' ).removeClass( 'hidden' );
			}
		});
	}
}

class GlozinArchiveProductViewWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				toolbarView: '#glozin-toolbar-view',
				productList: '#glozin-shop-content ul.products',
				viewSwitcher: '.gz-shop-view-item',
			}
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings('selectors');
		return {
			$toolbarView: this.$element.find(selectors.toolbarView),
			$products: this.$element.closest('#site-content').find(selectors.productList),
			$viewSwitcher: this.$element.find(selectors.viewSwitcher),
		};
	}

	bindEvents() {
		this.elements.$viewSwitcher.on('click', (e) => this.onViewSwitch(e));
	}

	onViewSwitch(e) {
		e.preventDefault();
		const $clicked = jQuery(e.currentTarget);
		const column = $clicked.data('column');
		const url = $clicked.attr('href');
		let layout = this.elements.$products.data('layout');
		layout = layout == 'list' ? '1' : layout;
		let view = 'grid';

		const $products = this.$element.closest('#site-content').find('#glozin-shop-content ul.products');

		$clicked.siblings().removeClass('current');
		$clicked.addClass('current');
		const classList = 'product-card-layout-list columns-1 columns-2 columns-3 columns-4';
		const classGrid = `columns-${column} product-card-layout-${layout}`;
		const classGridAll = 'columns-2 columns-3 columns-4 product-card--' + layout;
		if (column === 1) {
			view = 'list';
			$products.removeClass(classGridAll).addClass('columns-1 product-card-layout-list');
			this.elements.$toolbarView.removeClass('view-grid').addClass('view-list');
		} else if ([2, 3, 4].includes(column)) {
			view = 'grid';
			$products.removeClass(classList).addClass(classGrid);
			this.elements.$toolbarView.removeClass('view-list').addClass('view-grid');
		}

		document.cookie = 'catalog_view=' + view + ';domain=' + window.location.host + ';path=/';
		window.history.pushState(null, '', url);
	}

	onInit() {
		super.onInit();
		this.bindEvents();
	}
}

class GlozinArchiveProductOrderingWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				orderingForm: '.catalog-toolbar__item .catalog-toolbar__orderby-form',
			}
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings('selectors');
		return {
			$orderingForm: this.$element.find(selectors.orderingForm),
		};
	}

	loadProductsbyViewAJAX() {
		var self = this;

		jQuery(document.body).on('submit', '.catalog-toolbar__orderby-form', function(e) {
			e.preventDefault();

			var $form = jQuery(this),
				action = $form.attr('action') || window.location.href.split('?')[0],
				url = action,
				separator = action.indexOf('?') !== -1 ? '&' : '?',
				$inputs = $form.find('input, select'),
				params = {};

			params = $inputs.filter(function () {
				return this.value !== '' && this.name !== '';
			}).serializeObject();

			if (params.paged && params.paged === '1') {
				delete params.paged;
			}

			if ( _.size( params ) > 0 ) {
				url += separator + jQuery.param(params, true);
			}

			self.loadProducts( url, self );
		});
	}

	loadProducts( $url, self ) {
		var $toolbarView = jQuery('#glozin-toolbar-view'),
			$shopContent = jQuery('#glozin-shop-content');

		$shopContent.addClass('loading');
		jQuery(document.body).trigger('glozin_progress_bar_start');
		jQuery.get( $url, function( response ) {
			var $shopContentResponse = jQuery(response).find('.glozin-shop-content'),
				$productsResponse = $shopContentResponse.find('ul.products'),
				$navigationResponse = jQuery(response).find('.woocommerce-pagination').html(),
				$toolbarViewResponse = jQuery(response).find('.glozin-toolbar-view').html();

			jQuery(document.body).trigger('glozin_progress_bar_update', [85]);

			let delay = 0.5;
			$productsResponse.find('li.product').each( function( i, product ) {
				jQuery(product).addClass('gz-fadeinup');
				jQuery(product).css( '--gz-fadeinup-delay', delay + 's' );
				delay = delay + 0.1;
			});

			$shopContent.find('ul.products').attr( 'class', $productsResponse.attr( 'class' ) );
			$shopContent.find('ul.products').html( $productsResponse.html() );
			$toolbarView.html( $toolbarViewResponse );
			$shopContent.find('.woocommerce-pagination').html( $navigationResponse );
			$shopContent.removeClass('loading');

			self.scrollTopFilterAJAX();

			jQuery(document.body).trigger( 'glozin_get_products_ajax_loaded' );

			window.history.pushState( null, '', $url );

			jQuery(document.body).trigger('glozin_progress_bar_complete');

		});
	}

	scrollTopFilterAJAX = function() {
		var $offset = 0;

		if( ! jQuery("#glozin-shop-content").length ) {
			return;
		}

		if ( jQuery(window).width() > 1200 ) {
			if ( jQuery('#wpadminbar').length ) {
				$offset += jQuery('#wpadminbar').outerHeight();
			}

			if ( jQuery('.site-header__desktop').hasClass('glozin-header-sticky') ) {
				if ( jQuery('.site-header__desktop').hasClass('header-sticky--both') ) {
					$offset += jQuery('.site-header__desktop').outerHeight();
				} else {
					$offset += jQuery('.site-header__desktop').find('.header-sticky').outerHeight();
				}
			}
		} else {
			if ( jQuery('.site-header__mobile').hasClass('glozin-header-mobile-sticky') ) {
				if ( jQuery('.site-header__mobile').hasClass('header-sticky--both') ) {
					$offset += jQuery('.site-header__mobile').outerHeight();
				} else {
					$offset += jQuery('.site-header__mobile').find('.header-mobile-sticky').outerHeight();
				}
			}
		}

		var hasSticky = jQuery("#primary").hasClass('position-sticky-lg');
		if( hasSticky ) {
			jQuery("#primary").removeClass('position-sticky-lg');
		}
		jQuery('html,body').stop().animate({
			scrollTop: jQuery("#primary").length ? jQuery("#primary").offset().top - $offset - 30 : jQuery("#glozin-shop-content").offset().top - $offset - 30
		}, 300, function () {
			if( hasSticky ) {
				jQuery("#primary").addClass('position-sticky-lg');
			}
		});

		jQuery('#glozin-shop-content').delay(500).queue(function(next) {
			jQuery(this).removeClass('loading');
			next();
		});
	}

	catalogOrderBy() {
		var $selector = this.$element.find('.catalog-toolbar__orderby-form'),
			$orderForm = this.$element.find('.woocommerce-ordering');

		this.$element.on('click', '.catalog-toolbar__orderby-default', function (e) {
			e.preventDefault();

			var $currentOrderForm = jQuery(this).closest('.catalog-toolbar__orderby-form');

			$currentOrderForm.toggleClass('glozin-active');
		}).on('keyup', function (e) {
			if (e.keyCode === 27) {
				$selector.removeClass('glozin-active');
			}
		}).on('click', function (e) {
			var $target = jQuery(e.target);

			if ($target.parent('.catalog-toolbar__orderby-form').length) {
				return;
			}

			jQuery(this).closest('.catalog-toolbar__orderby-form').removeClass('glozin-active');
		});

		jQuery(document.body).on('click', '.catalog-toolbar__orderby-item', function (e) {
            e.preventDefault();

			var value = jQuery(this).data('id'),
				text = jQuery(this).text(),
				$selector = jQuery(this).closest('.catalog-toolbar__orderby-form'),
				$orderForm = jQuery(document.body).find('.woocommerce-ordering'),
				$defaultName = $selector.find('.catalog-toolbar__orderby-default-name'),
				$self = jQuery(this);

			// Select content form order
			$orderForm.find('option:selected').prop("selected", false);
			$orderForm.find('option[value='+ value +']').prop("selected", true);

			$defaultName.text( text );

			jQuery('.catalog-toolbar__orderby-form').removeClass('glozin-active');

			$orderForm.trigger( 'submit' );

			if (jQuery(window).width() < 768) {
				jQuery('.popover .popover__button-close').trigger('click');
			}

			setTimeout(function () {
				// Click selectd item popup order list
				$selector.find('.catalog-toolbar__orderby-list .selected').removeClass('selected');
				$self.addClass( 'selected' );
			}, 100);
        });

		// Active Item
		var activeVal = $orderForm.find('option:selected').val();
		$selector.find('.catalog-toolbar__orderby-list a[data-id='+ activeVal +']').addClass('selected');
    }

	onInit() {
		super.onInit();
		this.loadProductsbyViewAJAX();
		this.catalogOrderBy();
	}
}

class GlozinArchiveProductsWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				shopContent: '.glozin-shop-content',
			}
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings('selectors');
		return {
			$shopContent: this.$element.find(selectors.shopContent),
		};
	}

	loadMoreProducts() {
		var self = this;

		if ( jQuery( '.woocommerce-pagination' ).hasClass( 'woocommerce-pagination--infinite' ) ) {
			var waiting = false,
				endScrollHandle;

			jQuery( window ).on( 'scroll', function() {
				if ( waiting ) {
					return;
				}

				waiting = true;

				clearTimeout( endScrollHandle );

				var $navigation = jQuery( '.woocommerce-pagination.woocommerce-pagination--ajax' ),
					$button = jQuery( '.woocommerce-pagination-button', $navigation );

				if ( glozinIsVisible( $navigation ) && $button.length && !$button.hasClass( 'loading' ) ) {
					$button.addClass( 'loading' );

					self.loadProducts( $button, function( respond ) {
						$button = $navigation.find( '.woocommerce-pagination-button' );
					});
				}

				setTimeout( function() {
					waiting = false;
				}, 100 );

				endScrollHandle = setTimeout( function() {
					waiting = false;
					
					var $navigation = jQuery( '.woocommerce-pagination.woocommerce-pagination--ajax' ),
						$button = jQuery( '.woocommerce-pagination-button', $navigation );

					if ( glozinIsVisible( $navigation ) && $button.length && !$button.hasClass( 'loading' ) ) {
						$button.addClass( 'loading' );

						self.loadProducts( $button, function( respond ) {
							$button = $navigation.find( '.woocommerce-pagination-button' );
						});
					}
				}, 200 );
			});

		}

		if ( jQuery( '.woocommerce-pagination' ).hasClass( 'woocommerce-pagination--loadmore' ) ) {
			jQuery( document.body ).on( 'click', '.woocommerce-pagination.woocommerce-pagination--loadmore .woocommerce-pagination-button', function (event) {
				event.preventDefault();
				
				var $navigation = jQuery( '.woocommerce-pagination.woocommerce-pagination--ajax' ),
					$button = jQuery( '.woocommerce-pagination-button', $navigation );

				if ( glozinIsVisible( $navigation ) && $button.length && !$button.hasClass( 'loading' ) ) {
					$button.addClass( 'loading' );

					self.loadProducts( $button, function( respond ) {
						$button = $navigation.find( '.woocommerce-pagination-button' );
					});
				}
			});
		}
	}

	loadProducts( $el, callback ) {
		var $nav = $el.closest( '.woocommerce-pagination' ),
			url = $el.attr( 'href' ),
			currentProducts = $el.closest('.glozin-shop-content').find('ul.products').children().length,
			bannerProducts = $el.closest('.glozin-shop-content').find('li.gz-product-grid-banner');

		jQuery(document.body).trigger('glozin_progress_bar_start');
		jQuery.get( url, function( response ) {
			var $content = jQuery( '.glozin-shop-content', response ),
				$list = jQuery( 'ul.products', $content ),
				$products = $list.children(),
				$newNav = jQuery( '.woocommerce-pagination.woocommerce-pagination--ajax', $content ),
				numberPosts = $products.length + currentProducts,
				$found = jQuery('.gz-posts-found');

			$products.appendTo( $nav.parent().find( 'ul.products' ) );

			if ( $newNav.length ) {
				$el.replaceWith( jQuery('a', $newNav ) );
			} else {
				$nav.fadeOut( function() {
					$nav.remove();
				} );
			}

			if ( 'function' === typeof callback ) {
				callback( response );
			}

			$products.addClass( 'gz-fadeinup gz-animated' );

			let delay = 0.5;
			$products.each( function( i, item ) {
				jQuery(item).css( '--gz-fadeinup-delay', delay + 's' );
				delay = delay + 0.1;
			});

			jQuery(document.body).trigger( 'glozin_get_products_ajax_loaded', [$products, true] );

			if( $products.hasClass( 'gz-animated' ) ) {
				setTimeout( function() {
					$products.removeClass( 'gz-animated' );
				}, 10 );
			}
			$el.removeClass( 'loading' );
			numberPosts = bannerProducts.length ? numberPosts - 1 : numberPosts;
			$found.find('.current-post').html(' ' + numberPosts);
			
			var $found = jQuery('.gz-posts-found__inner'),
				$foundEls = $found.find('.count-bar'),
				$current = $found.find('.current-post').html(),
				$total = $found.find('.found-post').html(),
				pecent = ($current / $total) * 100;

			$foundEls.css('width', pecent + '%');

			window.history.pushState( null, '', url );

			jQuery(document.body).trigger('glozin_progress_bar_complete');
		});
	}

	onInit() {
		super.onInit();
		this.loadMoreProducts();
	}
}

class GlozinProductAddToCartFormWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				form: '.glozin-product-add-to-cart-form',
			}
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings('selectors');
		return {
			$form: this.$element.find(selectors.form),
		};
	}
	
	onInit() {
		super.onInit();
		this.initForm();
	}

	initForm() {
		const $form = this.$form;
	}
}

jQuery( window ).on( 'elementor/frontend/init', () => {
	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-product-images.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinProductImagesWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-product-short-description.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinProductShortDescriptionWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-product-data-tabs.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinProductDataTabsWidgetHandler, { $element } );
		elementorFrontend.elementsHandler.addHandler( GlozinProductReviewsWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-product-reviews.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinProductReviewsWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-product-related.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinProductCarouselBuilderWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-product-upsells.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinProductCarouselBuilderWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-advanced-linked-products.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinProductCarouselBuilderWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-products-filter.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinProductsFilterWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-product-sidebar.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinProductSidebarWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-archive-top-categories.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinArchiveTopCategoriesWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-wc-cart.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinWCCartWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-wc-cart-cross-sell.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinWCCartCrossSellWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-archive-page-header.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinArchivePageHeaderWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-archive-product-view.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinArchiveProductViewWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-archive-product-ordering.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinArchiveProductOrderingWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-archive-products.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinArchiveProductsWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-product-add-to-cart-form.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinProductAddToCartFormWidgetHandler, { $element } );
	} );
} );

jQuery(document).ready(function($) {
	$( document.body ).on( 'price_slider_create price_slider_change price_slider_slide', function() {
		var $slider = $(this).find( '.products-filter-widget--elementor .price_slider.ui-slider' );
		$slider.each( function() {
			var $el = $( this ),
				form = $el.closest( 'form' ).get( 0 ),
				onChange = $el.slider( 'option', 'change' );

			$el.slider( 'option', 'change', function( event, ui ) {
				onChange( event, ui );

				$( document.body ).trigger( 'glozin_products_filter_change', [form] );
			} );
		} );
	});
});