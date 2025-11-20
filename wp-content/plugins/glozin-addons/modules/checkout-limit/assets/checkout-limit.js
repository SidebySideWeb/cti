(function ($) {
	"use strict";

    var timeOutId = null,
        timeOutId2 = null;

    /**
     * Checkout limit
     */
    function checkoutLimit() {
        const action = glozinCL.action,
              message = glozinCL.message,
              time = glozinCL.time * 1000,
              emptyCartTime = glozinCL.emptyCartTime * 1000;

        var $checkoutLimitWrapper = $('.glozin-checkout-limit__wrapper');

        if( ! $checkoutLimitWrapper.length ) {
            return;
        }

        if (isNaN(time) || time <= 0) {
            return;
        }

        timeOutId = setTimeout(function() {
            $checkoutLimitWrapper.html('<div class="glozin-checkout-limit__end-message">' + message + '</div>');
            if( action === 'empty_cart' ) {
                timeOutId2 = setTimeout(function() {
                    emptyWooCommerceCart();
                }, emptyCartTime);
            }
        }, time);
    }

    /**
     * Empty WooCommerce cart
     */
    function emptyWooCommerceCart() {
        $.ajax({
            url: wc_cart_fragments_params.wc_ajax_url.toString().replace(  '%%endpoint%%', 'glozin_checkout_limit_empty_cart' ),
            type: 'POST',
            data: {
                action: 'glozin_checkout_limit_empty_cart'
            },
            success: function(response) {
                if( response.success ) {
                    $(document.body).trigger('updated_wc_div');
                    $(document.body).trigger('wc_update_cart');
                }
            }
        });
    }

	/**
	 * Document ready
	 */
	$(function () {
		if ( typeof glozinCL === 'undefined' ) {
			return;
		}
       
        $(document.body).on('wc_fragments_loaded wc_fragments_refreshed', function() {
            if( ! $('.glozin-checkout-limit').length ) {
                return;
            }

            if( timeOutId ) {
                clearTimeout(timeOutId);
            }

            if( timeOutId2 ) {
                clearTimeout(timeOutId2);
            }

            checkoutLimit();

            if( $('.woocommerce-mini-cart__empty-message').length ) {
                $('.glozin-checkout-limit').addClass('hidden');
            } else {
                $('.glozin-checkout-limit').removeClass('hidden');
                $(document.body).trigger('glozin_countdown', $('#cart-panel').find('.glozin-checkout-limit__time'));
            }
        });
    });

})(jQuery);