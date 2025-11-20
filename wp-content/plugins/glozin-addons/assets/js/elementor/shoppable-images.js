class GlozinShoppableImagesCarouselWidgetHandler extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
			selectors: {
				container: '.glozin-shoppable-images-carousel',
				modal: '.shoppable-images-modal',
			},
		};
    }

    getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$container: this.$element.find( selectors.container ),
			$modal: this.$element.find( selectors.modal ),
		};
	}

    ajaxShoppableImages() {
        const self = this;

        jQuery( document.body ).on( 'click', '.glozin-shoppable-images-carousel__button-shoppable', function( e ) {
            e.preventDefault();

            var $button = jQuery(this),
                $buttonIcon = $button.closest( '.glozin-shoppable-images-carousel__item' ).find( '.glozin-shoppable-images-carousel__icon' );

            const $modal = self.elements.$modal,
                  shoppable_images_id = $button.data( 'shoppable_images_id' );

            $buttonIcon.addClass( 'loading' );

            if( ! shoppable_images_id ) {
                $button.removeClass( 'loading' );
                return;
            }

            if( parseInt( $modal.attr( 'data-shoppable_images_id' ) ) === parseInt( shoppable_images_id ) ) {
                $buttonIcon.removeClass( 'loading' );
                self.toggleModal( $button, $modal );
                return;
            }

            $modal.find( '.modal__shoppable' ).empty();
            jQuery( document.body ).trigger( 'glozin_progress_bar_start' );
            jQuery.ajax({
                url: wc_add_to_cart_params.wc_ajax_url.toString().replace(  '%%endpoint%%', 'load_shoppable_images_elementor' ),
                type: 'POST',
                data: {
                    shoppable_images_id: shoppable_images_id,
                },
                success: function( response ) {
                    if( response.success ) {
                        $modal.find( '.modal__shoppable' ).html( response.data );
                        $modal.find( '.product-thumbnail' ).append( '<span class="glozin-shoppable-images__active glozin-svg-icon gz-button gz-button-light gz-button-icon"><svg width="11" height="9" viewBox="0 0 11 9" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.75195 0.751953C9.87044 0.642578 10.0072 0.587891 10.1621 0.587891C10.3262 0.587891 10.4629 0.642578 10.5723 0.751953C10.6908 0.870443 10.75 1.01172 10.75 1.17578C10.75 1.33073 10.6908 1.46289 10.5723 1.57227L4.16016 7.99805C4.05078 8.10742 3.91406 8.16211 3.75 8.16211C3.58594 8.16211 3.44922 8.10742 3.33984 7.99805L0.427734 5.07227C0.309245 4.96289 0.25 4.83073 0.25 4.67578C0.25 4.51172 0.309245 4.37044 0.427734 4.25195C0.537109 4.14258 0.669271 4.08789 0.824219 4.08789C0.988281 4.08789 1.12956 4.14258 1.24805 4.25195L3.75 6.75391L9.75195 0.751953Z" fill="currentColor"/></svg></span>' );
                    }
                },
                complete: function() {
                    $modal.attr( 'data-shoppable_images_id', shoppable_images_id );
                    self.toggleModal( $button, $modal );
                    $buttonIcon.removeClass( 'loading' );
                    jQuery( document.body ).trigger( 'glozin_get_products_ajax_loaded' );
                    jQuery( document.body ).trigger( 'glozin_get_shoppable_images_loaded' );
                    jQuery( document.body ).trigger( 'glozin_progress_bar_complete' );
                }
            });
        } );
    }

    toggleModal( $button, $modal ) {
        const target = $button.data( 'target' );

        if( $modal.hasClass( 'modal--open' ) ) {
            $modal.removeClass( 'modal--open' ).addClass( 'modal--closing' ).fadeOut(800, function() {
                jQuery( this ).removeClass( 'modal--closing' );
            });

            jQuery( document.body ).removeClass( target + '-opened' );
            jQuery( document.body ).removeAttr('style');
            jQuery( document.body ).removeClass( 'modal-opened' ).trigger( 'glozin_modal_closed', [target] );
        } else {
            var widthScrollBar = window.innerWidth - jQuery('#page').width();
            if( jQuery('#page').width() < 767 ) {
                widthScrollBar = 0;
            }

            jQuery( document.body ).css( { 'padding-inline-end': widthScrollBar, 'overflow': 'hidden' } );

            $modal.fadeIn();
            $modal.addClass( 'modal--open' );
            jQuery( document.body ).addClass( 'modal-opened ' + target + '-opened' ).trigger( 'glozin_modal_opened', [$modal] );
        }
    }

    onInit( ...args ) {
		super.onInit( ...args );

		this.ajaxShoppableImages();
	}
}

class GlozinShoppableImagesWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
        return {
			selectors: {
				container: '.glozin-shoppable-images',
			},
		};
    }

    getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$container: this.$element.find( selectors.container ),
		};
	}

    findHotspots() {
       jQuery( '.glozin-shoppable-images__hotspot' ).on('mouseenter', function( e ) {
            e.preventDefault();

            var product_id = jQuery( this ).data( 'product_id' ),
                $product = jQuery( this ).closest( '.glozin-shoppable-images' ).find( 'li.product.post-' + product_id  ),
                $scroll_container = jQuery( this ).closest( '.glozin-shoppable-images' ).find( 'ul.products' );

            if( product_id === undefined || product_id === 0 || product_id === null ) {
                return;
            }

            if ( ! $scroll_container.length ) {
                return;
            }

            if( $product.hasClass( 'active' ) ) {
                return;
            }

            $product.siblings().removeClass( 'active' );
            $product.addClass( 'active' );

            const leftPos = $product.position().left + $scroll_container.scrollLeft() - ( parseFloat( $scroll_container.css( 'gap' ) ) * ( parseFloat( $scroll_container.children().length ) - 1 ) );
            $scroll_container.animate({ scrollLeft: leftPos }, 500);
       });
    }

    scrollbar() {
        var $box = this.elements.$container.find( 'ul.products' );
        if( $box.length === 0 ) {
            return;
        }

        if ($box.get(0).scrollWidth > $box.innerWidth() ) {
            $box.addClass('has-scrollbar');
        } else {
            $box.removeClass('has-scrollbar');
        }
    }

    onInit( ...args ) {
		super.onInit( ...args );

		this.findHotspots();
		this.scrollbar();
	}
}

jQuery( window ).on( 'elementor/frontend/init', () => {
    elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-shoppable-images-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinShoppableImagesCarouselWidgetHandler, { $element } );
	} );

    elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-shoppable-images.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinShoppableImagesWidgetHandler, { $element } );
	} );

    jQuery(document.body).on('glozin_get_shoppable_images_loaded', function() {
		jQuery('.elementor-widget-glozin-shoppable-images').each(function() {
			elementorFrontend.elementsHandler.addHandler(GlozinShoppableImagesWidgetHandler, { $element: jQuery(this) });
		});
	});
} );