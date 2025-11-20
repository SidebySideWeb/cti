class GlozinBannerProductsWidgetHandler extends elementorModules.frontend.handlers.Base {
	bindEvents() {
		var $button = this.$element.find( '.glozin-banner__button' ),
			$buttonClose = this.$element.find( '.glozin-banner__products-close' );

		jQuery( $button ).on( 'click', function ( e ) {
			e.preventDefault();
			
			if( jQuery( this ).closest( '.glozin-banner' ).find( '.glozin-banner__products' ).hasClass( 'opened' ) ) {
				jQuery( this ).closest( '.glozin-banner' ).find( '.glozin-banner__products' ).removeClass( 'opened' );
			} else {
				jQuery( this ).closest( '.glozin-banner' ).find( '.glozin-banner__products' ).addClass( 'opened' );
			}
		} );

		jQuery( $buttonClose ).on( 'click', function ( e ) {
			e.preventDefault();

			jQuery( this ).closest( '.glozin-banner' ).find( '.glozin-banner__products' ).removeClass( 'opened' );
		} );

		jQuery(document.body).on( 'click', function ( e ) {
			if( ! jQuery( e.target ).closest( '.glozin-banner__products' ).length && ! jQuery( e.target ).closest( '.glozin-banner__button' ).length && ! jQuery( e.target ).hasClass( '.glozin-banner__button' ) ) {
				if( jQuery( '.glozin-banner__products' ).hasClass( 'opened' ) ) {
					jQuery( '.glozin-banner__products' ).removeClass( 'opened' );
				}
			}
		} );
	}
}

jQuery( window ).on( 'elementor/frontend/init', () => {
	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-banner-products.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinBannerProductsWidgetHandler, { $element } );
	} );
} );