class GlozinShoppableVideoCarouselWidgetHandler extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
			selectors: {
				container: '.glozin-shoppable-video-carousel',
				modal: '.shoppable-video-modal',
				modal_mobile: '.shoppable-video-mobile-modal',
			},
		};
    }

    getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$container: this.$element.find( selectors.container ),
			$modal: this.$element.find( selectors.modal ),
			$modal_mobile: this.$element.find( selectors.modal_mobile ),
		};
	}

    ajaxShoppableVideo() {
        const self = this,
              settings = self.getElementSettings(),
              modal_mute = settings.modal_mute;

        jQuery( document.body ).on( 'click', '[data-target="shoppable-video-modal"]', function( e ) {
            e.preventDefault();

            var $el = jQuery(this);

            const $modal = self.elements.$modal,
                  $html = $el.clone(),
                  product_id = $el.data( 'product_id' );

            $el.addClass( 'loading' );
            if( ! product_id ) {
                $el.removeClass( 'loading' );
                return;
            }

            if( modal_mute !== 'yes' ) {
                $html.find( 'video' ).removeAttr('muted');
                $html.find( 'video' ).removeAttr('autoplay');
            }

            $html.find('.glozin-shoppable-video-carousel__product').addClass('d-none-md');
            $modal.find( '.modal__shoppable-video' ).empty();
            jQuery( document.body ).trigger( 'glozin_progress_bar_start' );
            jQuery.ajax({
                url: wc_add_to_cart_params.wc_ajax_url.toString().replace(  '%%endpoint%%', 'glozin_load_shoppable_video_elementor' ),
                type: 'POST',
                data: {
                    'action': 'glozin_load_shoppable_video_elementor',
                    product_id: product_id,
                },
                success: function( response ) {
                    if( response.success ) {
                        $modal.find( '.modal__shoppable-video' ).html( response.data );
                        $modal.find('.woocommerce-product-gallery__video').removeAttr( 'style' );
                        $modal.find('.woocommerce-product-gallery__video').html( $html.html() );
                        $modal.find('.glozin-shoppable-video-carousel__product .glozin-button').removeClass('hidden').addClass('d-none-md');
                        $modal.find('.glozin-shoppable-video-carousel__product .glozin-button').attr('data-toggle', 'modal');
                        $modal.find('.glozin-shoppable-video-carousel__product .glozin-button').attr('data-target', 'shoppable-video-mobile-modal');
                    }
                },
                complete: function() {
                    self.openModal( $el, $modal );
                    $el.removeClass( 'loading' );
                    jQuery( document.body ).trigger( 'glozin_progress_bar_complete' );
                }
            });
        } );
    }

    ajaxShoppableVideoMobile() {
        const self = this;

        jQuery( document.body ).on( 'click', '[data-target="shoppable-video-mobile-modal"]', function( e ) {
            e.preventDefault();

            var $el = jQuery(this),
                $modal = $el.closest('.shoppable-video-modal'),
                $summary = $el.closest('.shoppable-video-modal').find('.entry-summary');

            $modal.addClass('shoppable-video-mobile-modal');
            $summary.addClass('open');
        } );
    }

    openModal( $el, $modal ) {
        const   self = this,
                target = $el.data( 'target' );

        if( ! $modal.hasClass( 'modal--open' ) ) {
            var widthScrollBar = window.innerWidth - jQuery('#page').width();
            if( jQuery('#page').width() < 767 ) {
                widthScrollBar = 0;
            }

            jQuery( document.body ).css( { 'padding-inline-end': widthScrollBar, 'overflow': 'hidden' } );

            $modal.fadeIn();
            $modal.addClass( 'modal--open' );
            self.actionVideo($modal);
            self.gallerySwiper($modal);
            self.variationForm( $modal.find('form') );
            jQuery( document.body ).addClass( 'modal-opened ' + target + '-opened' ).trigger( 'glozin_modal_opened', [$modal] );
        }
    }

    actionModal() {
        const   self = this;

        jQuery(document.body).on( 'click', '.modal__backdrop, .modal__button-close', function() {
            var $this = jQuery(this);

            if( ! $this.closest('.shoppable-video-mobile-modal').length ) {
                return;
            }

            var $modal = $this.closest('.shoppable-video-modal'),
                video = $modal.find('video');

            self.closeModal($modal);
        });

        jQuery(document.body).on('glozin_modal_closed', function(target) {
            jQuery('.shoppable-video-modal').find( '.modal__shoppable-video' ).empty();
        });
    }

	closeModal( target, removePadding = true ) {
		if ( !target ) {
			jQuery( '.modal' ).removeClass( 'modal--open' ).addClass( 'modal--closing' ).fadeOut(400, function() {
				$( this ).removeClass( 'modal--closing' );
			});

			jQuery( '.modal' ).each( function() {
				var $modal = jQuery( this );

				if ( ! $modal.hasClass( 'modal--open' ) ) {
					return;
				}

				$modal.removeClass( 'modal--open' ).addClass( 'modal--closing' ).fadeOut(400, function() {
					jQuery( this ).removeClass( 'modal--closing' );
				});
				jQuery( document.body ).removeClass( $modal.attr( 'id' ) + '-opened' );
			} );
		} else {
			target = jQuery( target ).closest( '.modal' );
			target.removeClass( 'modal--open' ).addClass( 'modal--closing' ).fadeOut(400, function() {
				jQuery( this ).removeClass( 'modal--closing' );
			});

            if (typeof self.actionVideo === 'function') {
                self.actionVideo(target, false);
            }

			jQuery( document.body ).removeClass( target.attr( 'id' ) + '-opened' );
		}

		jQuery( document.body ).removeClass('search-modal-form');

		if( removePadding ) {
			jQuery(document.body).removeAttr('style');
		}

		jQuery( document.body ).removeClass( 'modal-opened' ).trigger( 'glozin_modal_closed', [target] );
	}

    actionVideo($modal, $open = true) {
        const settings = this.getElementSettings(),
              modal_mute = settings.modal_mute;

        if( $open ) {
            if( $modal.find( 'video' ).length ) {
                $modal.find( 'video' ).get(0).play();
                $modal.find( 'video' ).get(0).controls = true;
                if( modal_mute !== 'yes' ) {
                    if (typeof $modal.find( 'video' ).get(0).muted !== 'undefined') {
                        $modal.find( 'video' ).get(0).muted = false;
                    }
                    if (typeof $modal.find( 'video' ).get(0).volume !== 'undefined') {
                        $modal.find( 'video' ).get(0).volume = 1;
                    }
                }
            }
        } else {
            if( $modal.find( 'video' ).length ) {
                $modal.find( 'video' ).get(0).pause();
            }
        }
    }

    gallerySwiper( $selector ) {
        var $gallery = $selector.find('.entry-summary .woocommerce-product-gallery__wrapper');

        $gallery.addClass('woocommerce-product-gallery__slider swiper');
        $gallery.wrapInner('<div class="swiper-wrapper"></div>');
        $gallery.find('.swiper-wrapper').after('<div class="swiper-progressbar"></div>');
        $gallery.find('.woocommerce-product-gallery__image').addClass('swiper-slide');

        var options = {
            loop: false,
            autoplay: false,
            speed: 800,
            slidesPerView: 2,
            spaceBetween: 15,
            watchOverflow: true,
            autoHeight: true,
            pagination: {
                el: $gallery.find('.swiper-progressbar').get(0),
                type: "progressbar",
                modifierClass: 'swiper-pagination--',
            },
            on: {
                init: function () {
                    $gallery.css('opacity', 1);
                    $gallery.parent().css('opacity', 1);
                },
            }
        };

        new Swiper( $gallery.get(0), options );
    }

    variationForm( $form ) {
        $form.wc_variation_form();
        jQuery( document.body ).trigger( 'init_variation_swatches');
        jQuery( document.body ).trigger( 'glozin_single_product_variation_form_init');
    }

    onInit( ...args ) {
        const self = this;
		super.onInit( ...args );

		self.ajaxShoppableVideo();
		self.ajaxShoppableVideoMobile();
        self.actionModal();
	}
}

jQuery( window ).on( 'elementor/frontend/init', () => {
    elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-shoppable-video-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinShoppableVideoCarouselWidgetHandler, { $element } );
	} );
} );