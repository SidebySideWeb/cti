class GlozinCounterWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				counterNumber: '.glozin-counter__number'
			}
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings('selectors');
		return {
		  $counterNumber: this.$element.find(selectors.counterNumber)
		};
	}

	onInit() {
		super.onInit();

		this.intersectionObserver = elementorModules.utils.Scroll.scrollObserver({
			callback: event => {
			  if (event.isInViewport) {
				this.intersectionObserver.unobserve(this.elements.$counterNumber[0]);
				const data = this.elements.$counterNumber.data(),
					  decimalDigits = data.toValue.toString().match(/\.(.*)/);

				if (decimalDigits) {
				  data.rounding = decimalDigits[1].length;
				}

				this.elements.$counterNumber.numerator(data);
			  }
			}
		  });
		  this.intersectionObserver.observe(this.elements.$counterNumber[0]);
	}
}

jQuery( window ).on( 'elementor/frontend/init', () => {
	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-counter.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinCounterWidgetHandler, { $element } );
	} );
} );
