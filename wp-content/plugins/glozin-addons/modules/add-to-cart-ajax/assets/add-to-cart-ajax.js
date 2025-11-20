(function ($) {
    'use strict';

    function add_to_cart_ajax() {
		$(document).on( 'click', '.single_add_to_cart_button', function (e) {
			var $thisbutton = $(this),
				$cartForm = $thisbutton.closest('form.cart'),
				$buttonclicked = $thisbutton;

			if( $thisbutton.hasClass( 'single_add_to_cart_button') ) {
				if ( $thisbutton.closest('.product').hasClass('product-type-external') ) {
					return;
				}

				if ( $cartForm.hasClass('buy-now-clicked') ) {
					$buttonclicked = $cartForm.find('.gz-buy-now-button');
				}
			}

			if ( $thisbutton.is('.disabled') ) {
				return;
			}

			if ( $cartForm.length > 0 ) {
				e.preventDefault();
			} else {
				return;
			}

			if ( $thisbutton.data('requestRunning') ) {
				return;
			}

			$thisbutton.data( 'requestRunning', true );

			var found = false;

			$thisbutton.removeClass( 'added' );
			$buttonclicked.addClass( 'loading' );

			if ( found ) {
				return;
			}

			found = true;

			// Allow 3rd parties to validate and quit early.
			if ( false === $( document.body ).triggerHandler( 'should_send_ajax_request.adding_to_cart', [ $thisbutton ] ) ) {
				$( document.body ).trigger( 'ajax_request_not_sent.adding_to_cart', [ false, false, $thisbutton ] );
				return true;
			}

			var formData = $cartForm.serializeArray(),
				formAction = $cartForm.attr('action');

			var data = {};

			if( $thisbutton.hasClass( 'single_add_to_cart_button') ) {
				// Fetch changes that are directly added by calling $thisbutton.data( key, value )
				$.each( formData, function( key, value ) {
					if( value['name'] == 'add-to-cart' ) {
						if( value['value'] ) {
							data['glozin-add-to-cart-ajax'] = value['value'];
						}
					} else {
						data[value['name']] = value['value'];
					}
				});

				if( data['glozin-add-to-cart-ajax'] == undefined ) {
					data['glozin-add-to-cart-ajax'] = $thisbutton.val();
					formData.push({'name': 'add-to-cart', 'value': $thisbutton.val()});
				}
			}

			// Trigger event.
			$( document.body ).trigger( 'adding_to_cart', [ $thisbutton, formData ] );

			var glozin_ajax_url = '';
			if (typeof glozinData !== 'undefined') {
				glozin_ajax_url = glozinData.ajax_url;
			} else if (typeof wc_add_to_cart_params !== 'undefined') {
				glozin_ajax_url = wc_add_to_cart_params.wc_ajax_url;
			}

			if( !glozin_ajax_url ) {
				return;
			}
			$(document.body).trigger('glozin_progress_bar_start');
			$.ajax({
				url: glozin_ajax_url.toString().replace( '%%endpoint%%', 'glozin_add_to_cart_single_ajax' ),
				method: 'post',
				data: data,
				error: function (response) {
					window.location = formAction;
				},
				success: function ( response ) {
					if ( ! response ) {
						window.location = formAction;
					}

					if( response.url ) {
						$cartForm.removeClass('buy-now-clicked');
						$thisbutton.data('requestRunning', false);
						$buttonclicked.removeClass( 'loading' );
						$(document.body).trigger('glozin_progress_bar_complete');
						window.location = response.url;
						return;
					}

					// Trigger event so themes can refresh other areas.
					if( ! response.error ) {
						$( document.body ).trigger( 'added_to_cart', [ response.fragments, response.cart_hash, $thisbutton ] );
					} else {
						$buttonclicked.removeClass( 'loading' );
					}


					if ( $.fn.notify && response.error ) {
						var $checkIcon = '<span class="glozin-svg-icon message-icon"><svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 50 50" width="50px" height="50px"><path d="M 25 2 C 12.309295 2 2 12.309295 2 25 C 2 37.690705 12.309295 48 25 48 C 37.690705 48 48 37.690705 48 25 C 48 12.309295 37.690705 2 25 2 z M 25 4 C 36.609824 4 46 13.390176 46 25 C 46 36.609824 36.609824 46 25 46 C 13.390176 46 4 36.609824 4 25 C 4 13.390176 13.390176 4 25 4 z M 25 11 A 3 3 0 0 0 22 14 A 3 3 0 0 0 25 17 A 3 3 0 0 0 28 14 A 3 3 0 0 0 25 11 z M 21 21 L 21 23 L 22 23 L 23 23 L 23 36 L 22 36 L 21 36 L 21 38 L 22 38 L 23 38 L 27 38 L 28 38 L 29 38 L 29 36 L 28 36 L 27 36 L 27 21 L 26 21 L 22 21 L 21 21 z"/></svg></span>',
							$closeIcon = '<span class="glozin-svg-icon svg-active"><svg class="svg-icon" aria-hidden="true" role="img" focusable="false" width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 1L1 14M1 1L14 14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></path></svg></span>',
							className  = 'error',
							$message   = response.error,
							button     = '<a href="' + glozinATCA.view_cart_link + '" class="btn-button">' + glozinATCA.view_cart_text + '</a>';

						$.notify.addStyle('glozin', {
							html: '<div>' + $checkIcon + '<div class="message-box">' + $message + '</div>' + $closeIcon + '</div>'
						});

						$.notify('&nbsp', {
							autoHideDelay: 5000,
							className: className,
							style: 'glozin',
							showAnimation: 'fadeIn',
							hideAnimation: 'fadeOut'
						});


					}
					$(document.body).trigger('glozin_progress_bar_complete');

					$thisbutton.data('requestRunning', false);
					found = false;
				},
			});
		});
    }

    /**
     * Document ready
     */
    $(function () {
        if( ! glozinATCA ) {
            return;
        }

        if( glozinATCA.add_to_cart_ajax !== 'yes' ) {
            return;
        }

        add_to_cart_ajax();
    });

})(jQuery);