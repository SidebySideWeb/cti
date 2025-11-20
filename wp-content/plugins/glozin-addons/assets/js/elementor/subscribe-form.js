class GlozinSubscribeWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				formRow: '.mc4wp-form-row',
				formControl: '.mc4wp-form-control'
			}
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings('selectors');
		return {
			$formRow: this.$element.find(selectors.formRow),
			$formControl: this.$element.find(selectors.formControl)
		};
	}

	onInit() {
		super.onInit();

		const formRow = this.elements.$formRow;
		const formControl = this.elements.$formControl;
		const selectors = this.getSettings('selectors');

		if (!formRow.length) return;

		formRow
			.on('keyup focus change', selectors.formControl, function() {
				jQuery(this).closest(selectors.formRow).addClass('focused');
			})
			.on('blur', selectors.formControl, function() {
				if (jQuery(this).val() === '') {
					jQuery(this).closest(selectors.formRow).removeClass('focused');
				}
			});

		formRow.each(function() {
			const $input = jQuery(this).find(selectors.formControl);
			if ($input.val() !== '') {
				jQuery(this).addClass('focused');
			}

			$input.on('animationstart', function(e) {
				if (e.originalEvent.animationName === 'autofill-animation') {
					$input.closest(selectors.formRow).addClass('focused');
				}
			});
		});

		jQuery(window).on("load", function() {
			formControl.each(function() {
				if (jQuery(this).val().length !== 0) {
					jQuery(this).closest(selectors.formRow).addClass('focused');
				}
			});
		});
	}
}

jQuery( window ).on( 'elementor/frontend/init', () => {
	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-subscribe-box.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinSubscribeWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-subscribe-group.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinSubscribeWidgetHandler, { $element } );
	} );
} );
