(function ($) {
    'use strict';

	function productVariationChange() {
		var $el_original = $('.single-product div.product .entry-summary .glozin-pre-order-availability');
		
		$('.single-product div.product .entry-summary .variations_form:not(.product-select__variation)').on( 'found_variation', function (event, variation) {
			var $el = $(this).closest('.entry-summary').find('.glozin-pre-order-availability');

			if( variation.pre_order_html ) {
				if( $el.length ) {
					var $text = $( decodeHtml( variation.pre_order_html ) ).text();
					if( $el.length ) {
						$el.wc_set_content( $text );
					}
				} else {
					$(this).closest('.entry-summary').find('.woocommerce-variation-add-to-cart').before( $( decodeHtml( variation.pre_order_html ) ).clone() );
				}
			} else {
				if( $el.length ) {
					$el.remove();
				}
			}
		});
		
		$('.single-product div.product .entry-summary .variations_form:not(.product-select__variation)').on( 'reset_data', function () {
			var $el = $(this).closest('.entry-summary').find('.glozin-pre-order-availability');
			if( $el_original.length ) {
				$el.wc_reset_content();
			} else {
				if( $el.length ) {
					$el.remove();
				}
			}
		});
	}

	function decodeHtml( encodedStr ) {
		var textArea = document.createElement('textarea');
		textArea.innerHTML = encodedStr;
		return textArea.value;
	}

    /**
     * Document ready
     */
    $(function () {
		if ( ! $('body').hasClass('single-product') ) {
			return;
		}

		productVariationChange();
    });

})(jQuery);