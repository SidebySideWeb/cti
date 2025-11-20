class GlozinCountDownWidgetHandler extends elementorModules.frontend.handlers.Base {

	getDefaultSettings() {
		return {
			selectors: {
				container: '.glozin-countdown'
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$container: this.$element.find( selectors.container )
		};
	}

	getCountDownInit() {
		jQuery(document.body).trigger('glozin_countdown', this.elements.$container);
	}

	onInit() {
		super.onInit();
		this.getCountDownInit();
	}
}

jQuery( window ).on( 'elementor/frontend/init', () => {
	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-countdown.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinCountDownWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-banner.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinCountDownWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-product-deals.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinCountDownWidgetHandler, { $element } );
	} );
} );
