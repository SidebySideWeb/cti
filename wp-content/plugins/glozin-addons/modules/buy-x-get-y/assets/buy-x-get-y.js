(function ($) {
    'use strict';

    function productVariationChange() {
        $('.glozin-buy-x-get-y').find( '.variations_form' ).on('change', 'select', function (e) {
            var $form          = $(this).closest('form'),
                optionSelected = $("option:selected", this),
                productID      = $form.data( 'product_id' ),
                attributes     = optionSelected.data('attributes'),
                stock          = optionSelected.data('stock'),
                $titleProduct  = $(this).closest('.glozin-buy-x-get-y__product-summary').find( '.woocommerce-loop-product__title a' );

            if( stock ) {
                if( stock.stock == 'out_of_stock' ) {
                    if( $titleProduct.find( 'span.stock' ).length > 0 ) {
                        $titleProduct.find( 'span.stock' ).text( ' - ' + stock.button_text );
                    } else {
                        $titleProduct.append( '<span class="stock"> - ' + stock.button_text + '</span>' );
                        $titleProduct.closest( '.glozin-buy-x-get-y__product' ).addClass( 'out-of-stock' );
                    }
                } else if( stock.stock == 'on_backorder' ) {
                    if( $titleProduct.find( 'span.stock' ).length > 0 ) {
                        $titleProduct.find( 'span.stock' ).text( ' - ' + stock.button_text );
                    } else {
                        $titleProduct.append( '<span class="stock"> - ' + stock.button_text + '</span>' );
                        $titleProduct.closest( '.glozin-buy-x-get-y__product' ).removeClass( 'out-of-stock' );
                    }
                } else {
                    $titleProduct.find( 'span.stock' ).remove();
                    $titleProduct.closest( '.glozin-buy-x-get-y__product' ).removeClass( 'out-of-stock' );
                }
            } else {
                if( $titleProduct.find( 'span.stock' ).length > 0 ) {
                    $titleProduct.closest( '.glozin-buy-x-get-y__product' ).removeClass( 'out-of-stock' );
                }

                $titleProduct.find( 'span.stock' ).remove();
            }

            if( attributes !== undefined ) {
                for( var key in attributes) {
                    $form.find('input[name="'+ key +'"]').val( attributes[key] );
                }

				found_variation( optionSelected, productID );
            } else {
				$form.find('input').val( '' );

				reset_data( optionSelected, productID );
			}
        });

        $( '.glozin-buy-x-get-y .variations_form select' ).trigger('change');
    }

    function found_variation( $selector, productID ) {
        var $product     = $selector.closest('.glozin-buy-x-get-y__product'),
            $image_clone = $product.find('.product-thumbnail img:not(.product-thumbnail__new)').clone(),
            $form        = $selector.closest( 'form' ),
            $price       = $product.find(  '.price' ),

			variation_id   = $selector.attr( 'value' ),
			data_variation = $selector.data( 'variation' ),
            stock          = $selector.data( 'stock' ),
			image          = $selector.data( 'image' ),
			image_ori	   = $product.find('.product-thumbnail img:not(.product-thumbnail__new)').attr( 'src' );

		if ( $product.length ) {
			if ( image ) {
                $image_clone.addClass( 'product-thumbnail__new' );
                $image_clone.attr( 'src', image );

                if( $image_clone.attr( 'srcset') ) {
                    $image_clone.attr( 'srcset', image );
                }

				if( $product.find('.product-thumbnail').hasClass( 'change' ) ) {
                    image_ori = $product.find('.product-thumbnail img.product-thumbnail__new').attr( 'src' );
                    if( image_ori !== image ) {
                        $product.find('.product-thumbnail .product-thumbnail__new').remove();
                        $product.find('.product-thumbnail').append( $image_clone );
                    }
                } else {
				    $product.find('.product-thumbnail').addClass('change').append( $image_clone );
                }
			}

            $form.attr( 'data-variation', JSON.stringify( data_variation ) );

            if( data_variation[productID].price_html ) {
                if( $price.find( '.price-hidden' ).length ) {
                    $price.find( '.price-hidden' ).siblings().remove();
                } else {
                    $price.html( '<div class="price-hidden hidden">'+ $price.html() +'</div>' );
                }

                $price.attr( 'data-price', data_variation[productID].price );
                $price.append( $("<div/>").html(data_variation[productID].price_html).text() );
            }
		}

		variationProduct( $product, variation_id );
	};

	function reset_data( $selector, productID ) {
		var $product     = $selector.closest('.glozin-buy-x-get-y__product'),
            $form        = $selector.closest( 'form' ),
            $price       = $product.find( '.price' ),

            variation_id   = $selector.attr( 'value' ),
            data_variation = $selector.data( 'variation' ),
            image          = $selector.data( 'image' );

		if ( $product.length ) {
            if( ! data_variation ) {
                $form.attr( 'data-variation', '' );
                $price.attr( 'data-price', 0 );
            }

			if ( ! image ) {
				$product.find('.product-thumbnail').removeClass('change');
				$product.find('.product-thumbnail .product-thumbnail__new').remove();
			}

            if( $price.find( '.price-hidden' ).length ) {
                $price.find( '.price-hidden' ).siblings().remove();
                $price.html( $price.find( '.price-hidden' ).html() );
            }
		}

		variationProduct( $product, variation_id );
	};

	function variationProduct ( $product, variation_id ) {
		if(  ! $product.hasClass( 'variable' ) ) {
			return;
		}

        var $main_data     = $product.closest( '.glozin-buy-x-get-y' ).find( 'input[name = "glozin_buy_x_get_y_main_product"]' ),
            $sub_data      = $product.closest( '.glozin-buy-x-get-y' ).find( 'input[name = "glozin_buy_x_get_y_sub_product"]' ),
            $main_products = $( '.glozin-buy-x-get-y .main-products' ),
            $sub_products  = $( '.glozin-buy-x-get-y .sub-products' );

        var main_data  = [];
        $main_products.find( '.glozin-buy-x-get-y__product' ).each( function( i, product ) {
            if( $(product).find( 'form' ).attr( 'data-variation' ) ) {
                main_data.push( $(product).find( 'form' ).attr( 'data-variation' ) );
            }
        });
        $main_data.val( JSON.stringify(main_data) );

        var sub_data  = [];
        $sub_products.find( '.glozin-buy-x-get-y__product' ).each( function( i, product ) {
            if( $(product).find( 'form' ).attr( 'data-variation' ) ) {
                sub_data.push( $(product).find( 'form' ).attr( 'data-variation' ) );
            }
        });
        $sub_data.val( JSON.stringify(sub_data) );

        check_button();
	}

    function check_button() {
		var $el  = $( '.glozin-buy-x-get-y' ),
			$products      = $el.find( '.glozin-buy-x-get-y__product' ),
            $main_products = $el.find( '.glozin-buy-x-get-y__products.main-products .glozin-buy-x-get-y__product' ),
		    $button        = $el.find( '[name="glozin_buy_x_get_y_add_to_cart"]' );

        if( $button.hasClass( 'disabled' ) ) {
            $button.removeClass( 'disabled' );
        }

        $products.each( function( i, product ) {
            if( $(product).hasClass( 'variable' ) && ! $(product).find( 'form' ).attr( 'data-variation' ) && ! $button.hasClass( 'disabled' ) ) {
                $button.addClass( 'disabled' );
            }
        });

        $main_products.each( function( i, product ) {
            if( $(product).hasClass( 'out-of-stock' ) && ! $button.hasClass( 'disabled' ) ) {
                $button.addClass( 'disabled' );
            }
        });
	}

    function formatNumber( $number ) {
		var currency       = glozinBXGY.currency_symbol,
			thousand       = glozinBXGY.thousand_sep,
			decimal        = glozinBXGY.decimal_sep,
			price_decimals = glozinBXGY.price_decimals,
			currency_pos   = glozinBXGY.currency_pos,
			n              = $number;

		if ( parseInt(price_decimals) > -1 ) {
			$number = $number.toFixed(price_decimals) + '';
			var x = $number.split('.');
			var x1 = x[0],
				x2 = x.length > 1 ? decimal + x[1] : '';

			if( thousand ) {
				var rgx = /(\d+)(\d{3})/;
				while (rgx.test(x1)) {
					x1 = x1.replace(rgx, '$1' + thousand + '$2');
				}
			}

			n = x1 + x2
		}

		switch (currency_pos) {
			case 'left' :
				return currency + n;
				break;
			case 'right' :
				return n + currency;
				break;
			case 'left_space' :
				return currency + ' ' + n;
				break;
			case 'right_space' :
				return n + ' ' + currency;
				break;
		}
	}

    function roundLikePHP(num, dec = glozinBXGY.price_decimals){
		var num_sign = num >= 0 ? 1 : -1;
		return parseFloat((Math.round((num * Math.pow(10, dec)) + (num_sign * 0.0001)) / Math.pow(10, dec)).toFixed(dec));
	}

    function add_to_cart_ajax() {
		$(document).on( 'click', '.glozin-buy-x-get-y-add-to-cart', function (e) {
			var $thisbutton = $(this),
				$cartForm = $thisbutton.closest('form.cart');

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
			$thisbutton.addClass( 'loading' );
			$(document.body).trigger('glozin_progress_bar_start');

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

			if( $thisbutton.hasClass( 'glozin-buy-x-get-y-add-to-cart') ) {
				var $_data = JSON.parse( $thisbutton.val() ),
					product_id = $_data[0]['product_id'];

				data['glozin-bxgy-add-to-cart-ajax'] = product_id;
				data['quantity'] = $_data[0]['qty'];
				data[$thisbutton.attr('name')] = $thisbutton.val();

				$.each( formData, function( key, value ) {
					if( value['value'] ) {
						data[value['name']] = value['value'];
					}
				});

				if( data['glozin_buy_x_get_y_main_product'] ) {
					var data_variation = JSON.parse( data['glozin_buy_x_get_y_main_product'] );

					$.each( data_variation, function( key, value ) {
						var _data_variation = JSON.parse( value );
						if( _data_variation[product_id] && _data_variation[product_id]['variation_id'] ) {
							data['variation_id'] = _data_variation[product_id]['variation_id'];

							$.each( _data_variation[product_id]['attributes'], function( key, value ) {
								data[key] = value;
							});
						}
					});
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

			$.ajax({
				url: glozin_ajax_url.toString().replace( '%%endpoint%%', 'glozin_buy_x_get_y_add_to_cart_ajax' ),
				method: 'post',
				data: data,
				error: function (response) {
					window.location = formAction;
				},
				success: function ( response ) {
					if ( ! response ) {
						window.location = formAction;
					}

					if (typeof wc_add_to_cart_params !== 'undefined') {
						if (wc_add_to_cart_params.cart_redirect_after_add === 'yes') {
							window.location = wc_add_to_cart_params.cart_url;
							return;
						}
					}

					// Trigger event so themes can refresh other areas.
					if( ! response.error ) {
						$( document.body ).trigger( 'added_to_cart', [ response.fragments, response.cart_hash, $thisbutton ] );
					} else {
						$thisbutton.removeClass( 'loading' );
					}

					if ( $.fn.notify && response.error ) {
						var $checkIcon = '<span class="glozin-svg-icon message-icon"><svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 50 50" width="50px" height="50px"><path d="M 25 2 C 12.309295 2 2 12.309295 2 25 C 2 37.690705 12.309295 48 25 48 C 37.690705 48 48 37.690705 48 25 C 48 12.309295 37.690705 2 25 2 z M 25 4 C 36.609824 4 46 13.390176 46 25 C 46 36.609824 36.609824 46 25 46 C 13.390176 46 4 36.609824 4 25 C 4 13.390176 13.390176 4 25 4 z M 25 11 A 3 3 0 0 0 22 14 A 3 3 0 0 0 25 17 A 3 3 0 0 0 28 14 A 3 3 0 0 0 25 11 z M 21 21 L 21 23 L 22 23 L 23 23 L 23 36 L 22 36 L 21 36 L 21 38 L 22 38 L 23 38 L 27 38 L 28 38 L 29 38 L 29 36 L 28 36 L 27 36 L 27 21 L 26 21 L 22 21 L 21 21 z"/></svg></span>',
							$closeIcon = '<span class="glozin-svg-icon svg-active"><svg class="svg-icon" aria-hidden="true" role="img" focusable="false" width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 1L1 14M1 1L14 14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></path></svg></span>',
							className  = 'error',
							$message   = response.error,
							button     = '<a href="' + glozinBXGY.view_cart_link + '" class="btn-button">' + glozinBXGY.view_cart_text + '</a>';

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
				}
			});
		});
    }

    /**
     * Document ready
     */
    $(function () {
        if ( typeof glozinBXGY === 'undefined' ) {
			return false;
		}

        if ( ! $( 'body' ).hasClass( 'single-product' ) ) {
            return;
        }

        if ( $( '.glozin-buy-x-get-y' ).length < 1 ) {
            return;
        }

        $(window).on('load', function () {
			$( '.glozin-buy-x-get-y' ).find( 'select[name="variation_id"] option' ).prop( 'selected', function () {
				return this.defaultSelected;
			});
		});

        add_to_cart_ajax();

        productVariationChange();
        check_button();
    });

})(jQuery);