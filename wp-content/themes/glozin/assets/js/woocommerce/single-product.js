(function ($) {
	'use strict';

	var glozin = glozin || {};
	glozin.init = function () {
		glozin.$body = $(document.body),
			glozin.$window = $(window),
			glozin.$header = $('#site-header');

		this.singleProductGallery();
		this.productImageZoom();
		glozin.$body.on( 'glozin_product_gallery_zoom', function(){
			glozin.productImageZoom();
		} );

		this.productLightBox();
		this.glozinMore();
		this.productReview();
		this.productTabsDropdown();
		this.addToCartGroupedProductVariable();
		this.productHighlights();
	};

	/**
	 * Product Gallery
	 */
	glozin.productGallery = function ( vertical, $selector = $('.woocommerce-product-gallery') ) {
		if (typeof Swiper === 'undefined') {
			return;
		}

		var $window = $( window );
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

						glozin.$body.trigger( 'glozin_product_gallery_init' );

						if( this.slides && this.slides[this.realIndex] && this.slides[this.realIndex].getAttribute( 'data-zoom_status' ) == 'false' ) {
							this.$el.parent().addClass( 'swiper-item-current-extra' );
						}
					},
					slideChange: function () {
						if( this.slides && this.slides[this.realIndex] && this.slides[this.realIndex].getAttribute( 'data-zoom_status' ) == 'false' ) {
							this.$el.parent().addClass( 'swiper-item-current-extra' );
						} else {
							if( this.$el.parent().hasClass( 'swiper-item-current-extra' ) ) {
								this.$el.parent().removeClass( 'swiper-item-current-extra' );
							}
						}
					},
					slideChangeTransitionEnd: function () {
						glozin.$body.trigger( 'glozin_product_gallery_slideChangeTransitionEnd' );
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
			var $gallery = $selector.find('.woocommerce-product-gallery__wrapper');

			$gallery.addClass('woocommerce-product-gallery__slider swiper');
			$gallery.wrapInner('<div class="swiper-wrapper"></div>');
			$gallery.find('.swiper-wrapper').after('<span class="glozin-svg-icon glozin-svg-icon__inline glozin-svg-icon--icon-back swiper-button-prev swiper-button"><svg width="24" height="24" aria-hidden="true" role="img" focusable="false"> <use href="#icon-back" xlink:href="#icon-back"></use> </svg></span>');
			$gallery.find('.swiper-wrapper').after('<span class="glozin-svg-icon glozin-svg-icon__inline glozin-svg-icon--icon-next swiper-button-next swiper-button"><svg width="24" height="24" aria-hidden="true" role="img" focusable="false"> <use href="#icon-next" xlink:href="#icon-next"></use> </svg></span>');
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

						glozin.$body.trigger( 'glozin_product_thumbnails_init' );
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
	};

	/**
	 * Single Product Gallery
	 */
	glozin.singleProductGallery = function () {
		var $gallery = $('div.product .woocommerce-product-gallery');

		if ( ! $gallery.length ) {
			return;
		}

		if( $gallery.hasClass( 'woocommerce-product-gallery--vertical' ) ) {
			$('.woocommerce-product-gallery').on('product_thumbnails_slider_vertical wc-product-gallery-after-init', function(){
				glozin.productGallery(true);
			});
		}

		if( $gallery.hasClass( 'woocommerce-product-gallery--horizontal' ) ) {
			glozin.productGallery(false);
			$('.woocommerce-product-gallery').on('product_thumbnails_slider_horizontal', function(){
				glozin.productGallery(false);
			});
		}
	};

	/**
	 * Product Image Zoom
	 */
	glozin.productImageZoom = function () {
		if (typeof Drift === 'undefined') {
			return;
		}

		var $selector = $('.product-gallery-summary');

		if( ! $selector ) {
			return;
		}

		if( glozinData.product_image_zoom == 'none' ) {
			return;
		}

		var $summary   = $selector.find('.entry-summary'),
		    $gallery   = $selector.find('.woocommerce-product-gallery__wrapper');

		if( glozinData.product_image_zoom == 'bounding' ) {
			if( ! $summary.find('.glozin-product-zoom-wrapper').length ) {
				var $zoom = $( '<div class="glozin-product-zoom-wrapper" />' );
				$summary.prepend( $zoom );
			} else {
				var $zoom = $summary.find('.glozin-product-zoom-wrapper');
			}
		}

		var options = {
			containInline: true,
		};

		if( glozinData.product_image_zoom == 'bounding' ) {
			options.paneContainer = $zoom.get(0);
			options.hoverBoundingBox = true;
			options.zoomFactor = 2;
		}

		if( glozinData.product_image_zoom == 'inner' ) {
			options.zoomFactor = 3;
		}

		if( glozinData.product_image_zoom == 'magnifier' ) {
			options.zoomFactor = 2;
			options.inlinePane = true;
		}

		$gallery.find( '.woocommerce-product-gallery__image' ).each( function() {
			var $this = $(this),
				$image = $this.find( 'img' ),
				imageUrl = $this.find( 'a' ).attr('href');

			if( $this.hasClass('glozin-product-video') || $this.data( 'zoom_status' ) == false ) {
				return;
			}

			if( glozinData.product_image_zoom == 'inner' ) {
				options.paneContainer = $this.get(0);
			}

			$image.attr( 'data-zoom', imageUrl );

			new Drift( $image.get(0), options );
		});

		$('.single-product div.product .product-gallery-summary .variations_form').on( 'show_variation hide_variation', function () {
			var $selector = $(this).closest( '.product-gallery-summary' ),
				$gallery = $selector.find( '.woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image' ).eq(0),
				imageUrl = $gallery.find( 'a' ).attr( 'href' ),
				$image = $gallery.find( 'img' );

			$image.attr( 'data-zoom', imageUrl );
		});
	};

	/**
 	 * Glozin More
 	 */
	glozin.glozinMore = function () {
		var $selector =  $(document).find( '.short-description__content' );

			$selector.each( function () {
				if( jQuery(this)[0].scrollHeight > jQuery(this)[0].clientHeight ) {
					jQuery(this).siblings( '.short-description__more' ).removeClass( 'hidden' );
				}
			});

		$( document.body ).on( 'click', '.short-description__more', function(e) {
			e.preventDefault();

			var $settings = $(this).data( 'settings' ),
				$more     = $settings.more,
				$less     = $settings.less;

			if( $(this).hasClass( 'less' ) ) {
				$(this).removeClass( 'less' );
				$(this).text( $more );
				$(this).siblings( '.short-description__content' ).css( '-webkit-line-clamp', '' );
			} else {
				$(this).addClass( 'less' );
				$(this).text( $less );
				$(this).siblings( '.short-description__content' ).css( '-webkit-line-clamp', 'inherit' );
			}
		});
	}

	/**
	 * Product Light Box
	 */
	glozin.productLightBox = function () {
		var $selector = $('.woocommerce-product-gallery');

		$('.woocommerce-product-gallery__image').on( 'click', 'a', function (e) {
			return false;
		});

		if( ! $selector ) {
			return;
		}

		if( ! glozinData.product_image_lightbox ) {
			return
		}
		lightBoxButton();
		glozin.$body.on( 'glozin_product_gallery_lightbox', function(){
			lightBoxButton();
		} );

		$(document).on( 'click', '.glozin-button--product-lightbox', function (e) {
			e.preventDefault();

			var pswpElement = $( '.pswp' )[0],
				items       = getGalleryItems( $(this).siblings( '.woocommerce-product-gallery__wrapper' ).find( '.woocommerce-product-gallery__image' ) ),
				clicked = $(this).siblings( '.woocommerce-product-gallery__wrapper' ).find( '.swiper-slide-active' ),
				index = 0;

			$.each(items, function(key, value) {
				if( value.src === clicked.find('a').attr('href') ) {
					index = key;
				}
			});

			var options = $.extend( {
				index: index,
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
			$('.woocommerce-product-gallery__image').on( 'click', 'a', function (e) {
				return false;
			});

			$selector.append('<a href="#" aria-label="Open product lightbox" class="glozin-button--product-lightbox top-15 end-15 position-absolute d-none d-flex-md align-items-center justify-content-center rounded-100 z-2"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="currentColor"> <path d="M0.000357628 13.3636L0.000596046 10.1813C0.000596046 9.82984 0.28544 9.54512 0.636787 9.54512C0.988253 9.54512 1.27286 9.82984 1.27286 10.1814V11.8261L4.96974 8.12603C5.09417 8.00148 5.25721 7.93963 5.42013 7.93963C5.58281 7.93963 5.7455 8.0016 5.8698 8.12591C6.1183 8.37416 6.11853 8.77699 5.87016 9.02549L2.17208 12.7271H3.81643C4.16789 12.7271 4.45274 13.0121 4.45274 13.3637C4.45274 13.715 4.16777 14 3.81643 14H0.636787C0.467907 14 0.306178 13.9329 0.186758 13.8134C0.067338 13.6941 0.000357628 13.532 0.000357628 13.3636ZM0.636668 4.45524C0.988253 4.45524 1.27286 4.16992 1.27286 3.81869V2.17399L4.90777 5.77791C5.1565 6.02641 5.57638 6.02665 5.82487 5.77815C6.07348 5.53002 6.08206 5.12694 5.83381 4.87857L2.23561 1.27286H3.88174H3.88305C4.23452 1.27286 4.51972 0.988133 4.51984 0.636548C4.51995 0.285439 4.23559 0.000356674 3.884 0.000356674L0.70484 0C0.53584 0 0.339906 0.0670996 0.220843 0.186399C0.101542 0.3057 0.000238419 0.467548 0.000238419 0.636189V3.81881C0.000357628 4.17004 0.285321 4.45524 0.636668 4.45524ZM9.09271 5.80592L12.7273 2.17375V3.81881C12.7273 4.17028 13.0065 4.45452 13.3579 4.45452H13.3552C13.7067 4.45452 13.9902 4.16992 13.9902 3.81881L13.99 0.636667C13.99 0.467787 13.9227 0.305939 13.8034 0.186638C13.6838 0.0672178 13.5217 0.000237465 13.353 0.000237465H10.1732C9.82174 0.000237465 9.5369 0.285201 9.5369 0.636548C9.5369 0.988253 9.82186 1.2731 10.1732 1.2731H11.8171L8.18705 4.90646C7.93832 5.15483 7.94153 5.55826 8.19003 5.8064C8.43852 6.05453 8.84409 6.0543 9.09271 5.80592ZM11.8283 12.6698H10.1842C9.8327 12.6698 9.54798 12.9544 9.54798 13.3058C9.54798 13.6574 9.83282 13.9423 10.1842 13.9423L13.3636 13.9426H13.3637C13.5326 13.9426 13.6942 13.8758 13.8137 13.7565C13.9329 13.6372 14 13.4755 14 13.3064L13.9996 10.124C13.9996 9.77299 13.7148 9.48767 13.3635 9.48767C13.012 9.48767 12.7273 9.77299 12.7273 10.124V11.7689L9.05934 8.09802C8.93503 7.97359 8.77199 7.91138 8.60907 7.91138C8.4465 7.91138 8.28358 7.97335 8.1594 8.09766C7.91079 8.34592 7.91043 8.74911 8.15904 8.99784L11.8283 12.6698Z" fill="currentColor"></path></svg></a>');
		}

		function getGalleryItems( $slides ) {
			var items = [];

			if ( $slides.length > 0 ) {
				$slides.each( function( i, el ) {
					var img = $( el ).find( 'img' );

					if ( $( el ).hasClass('glozin-product-video') ) {
						var video = $( el ).find('.glozin-video-wrapper').html();

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

	/**
	 * Product Review
	 */
	glozin.productReview = function () {
		var $button = $( '.glozin-form-review' ),
			text = $button.data( 'text' ),
			textCancel = $button.data( 'text-cancel' ),
			$form = $( '.glozin-review-form');

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

	/**
	 * Product tabs dropdown
	 */
	glozin.productTabsDropdown = function () {
		var $productTabs = $( '.woocommerce-tabs--dropdown' );

		if( ! $productTabs ) {
			return;
		}

		if( glozinData.product_tabs_layout !== 'accordion' ) {
			return;
		}

		$productTabs.on( 'click', '.woocommerce-tabs-title', function() {
			if( $(this).hasClass('active') ) {
				if( $(this).closest('.woocommerce-tabs--dropdown').hasClass('wc-tabs-first--opened') ) {
					$(this).closest('.woocommerce-tabs--dropdown').removeClass('wc-tabs-first--opened');
				}

				$(this).removeClass('active');
				$(this).siblings('.woocommerce-tabs-content').slideUp(200);
			} else {
				$(this).addClass('active');
				$(this).siblings('.woocommerce-tabs-content').slideDown(200);
			}
		});

		$( 'a.woocommerce-review-link' ).on('click', function() {
			$('#tab-reviews .woocommerce-tabs-title:not(.active)').trigger('click');
		});

		if ( window.location.href.indexOf( '#reviews' ) > -1 ) {
			$('#tab-reviews .woocommerce-tabs-title:not(.active)').trigger('click');
		}
	};

	/**
	 * Add to cart grouped product variable
	 */
	glozin.addToCartGroupedProductVariable = function () {
		if( ! glozin.$body.hasClass( 'single-product' ) ) {
			return;
		}
		
		if( ! glozin.$body.find( '.woocommerce-grouped-product-list' ).length ) {
			return;
		}

		var $variation = $( 'select[name="variation_id"]' );
	
		$variation.on( 'change', function() {
			var optionSelected = $("option:selected", this),
				variationId    = optionSelected.val(),
				attributes     = optionSelected.data('attributes'),
				priceHtml      = optionSelected.data( 'price_html' ),
				image          = optionSelected.data( 'image' ),
				$price         = $(this).closest('.woocommerce-grouped-product-list-item').find( '.woocommerce-grouped-product-list-item__price' ),
				stockStatus    = optionSelected.data( 'stock' );

			if( attributes !== undefined && stockStatus && stockStatus.stock !== 'out_of_stock' ) {
				var attrs = {};
				if( $(this).siblings('input[name^="attribute"]').length ) {
					$(this).siblings('input[name^="attribute"]').remove();
				}
				if( $(this).siblings('input[name^="variation_ids"]').length ) {
					$(this).siblings('input[name^="variation_ids"]').remove();
				}
				for( var key in attributes) {
					attrs[key] = attributes[key];
				}

				$(this).after( '<input type="hidden" name="attributes[' + variationId + ']" value="">' );
				$(this).after( '<input type="hidden" name="variation_ids[' + variationId + ']" value="' + variationId + '">' );
				$(this).siblings('input[name^="attributes"]').val(JSON.stringify(attrs));
			} else {
				$(this).siblings('input[name^="attribute"]').remove();
				$(this).siblings('input[name^="variation_ids"]').remove();
			}

			if( image ) {
				if( $(this).closest('.woocommerce-grouped-product-list-item').find('.variation-image').length ) {
					$(this).closest('.woocommerce-grouped-product-list-item').find('.variation-image').remove();
				}
				$(this).closest('.woocommerce-grouped-product-list-item').find('.woocommerce-grouped-product-list__thumbnail img').addClass('hidden');
				$(this).closest('.woocommerce-grouped-product-list-item').find('.woocommerce-grouped-product-list__thumbnail img').after( '<img class="variation-image" src="' + image + '">' );
			} else {
				$(this).closest('.woocommerce-grouped-product-list-item').find('.woocommerce-grouped-product-list__thumbnail img').removeClass('hidden');
				$(this).closest('.woocommerce-grouped-product-list-item').find('.variation-image').remove();
			}

			if( isNumber( variationId ) && variationId ) {
				$(this).closest('.woocommerce-grouped-product-list__quantity').find( 'input.qty' ).attr( 'name', 'quantity[' + variationId + ']' );
				$price.find( '.price' ).css( 'display', 'none' );
				if( $price.find( '.price-variation-new' ).length ) {
					$price.find( '.price-variation-new' ).remove();
				}
				$price.append( '<span class="price-variation-new">' + priceHtml + '</span>' );
			} else {
				variationId = $(this).closest('.woocommerce-grouped-product-list__quantity').data( 'grouped_product_id' );
				$(this).closest('.woocommerce-grouped-product-list__quantity').find( 'input.qty' ).attr( 'name', 'quantity[' + variationId + ']' );
				$price.find( '.price-variation-new' ).remove();
				$price.find( '.price' ).removeAttr( 'style' );
			}

			if( ! stockStatus || stockStatus.stock == 'out_of_stock' ) {
				$(this).closest('.woocommerce-grouped-product-list__quantity').find( '.quantity' ).css( { 'pointer-events': 'none', 'opacity': '0.5' } );
			} else {
				$(this).closest('.woocommerce-grouped-product-list__quantity').find( '.quantity' ).removeAttr( 'style' );
			}

			if( $(this).closest('.woocommerce-grouped-product-list-item').find('.woocommerce-grouped-product-list__title').find('.woocommerce-badge').length ) {
				$(this).closest('.woocommerce-grouped-product-list-item').find('.woocommerce-grouped-product-list__title').find('.woocommerce-badge').remove();
			}

			if( stockStatus && ( stockStatus.stock == 'out_of_stock' || stockStatus.is_pre_order ) ) {
				var classes = stockStatus.is_pre_order ? 'pre-order' : 'sold-out';
				$(this).closest('.woocommerce-grouped-product-list-item').find('.woocommerce-grouped-product-list__title a').after('<span class="woocommerce-badge badge-small '+ classes +'">' + stockStatus.button_text + '</span>' );
			}
		});

		var $variation = $( 'select[name="variation_id"]' );

		if( $variation.length ) {
			$variation.trigger( 'change' );
		}

		function isNumber(value) {
			return !isNaN(value) && value.trim() !== '';
		}
	};

	/**
	 * Product Highlights
	 */
	glozin.productHighlights = function () {
		if( ! glozinData.product_highlights ) {
			return;
		}

		var $selector = $( '.product-highlights' );

		if( ! $selector.length ) {
			return;
		}

		var $inner = $selector.find('.glozin-marquee__inner'),
			$items = $selector.find('.glozin-marquee__items');

		$inner.imagesLoaded(function () {
			var item,
				amount = ( parseInt( Math.ceil( jQuery( window ).width() / $items.outerWidth( true ) ) ) || 0 ) + 1,
				dataSpeed = $selector.data('speed'),
				speed = 1 / parseFloat( dataSpeed ) * ( $items.outerWidth( true ) / 350 );

			$inner.css( '--gz-marquee-speed', speed + 's' );

			for ( let i = 1; i <= amount; i++ ) {
				item = $items.clone();
				item.addClass( 'glozin-marquee--duplicate' );
				item.css( '--gz-marquee-index', i.toString() );

				item.appendTo( $inner );
			}
		});
	}
	/**
	 * Document ready
	 */
	glozin.init();

})(jQuery);