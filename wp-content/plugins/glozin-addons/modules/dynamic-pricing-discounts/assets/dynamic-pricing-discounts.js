(function ($) {
    'use strict';

	function change_quantity () {
		$( document.body ).on( 'change', 'input.qty', function() {
			if( ! $(this).closest( 'div.product' ).find('.dynamic-pricing-discounts').length ) {
				return;
			}

			var qty               = $(this).val(),
			    $addtocart_button = $(this).closest( 'form.cart' ).find( '.single_add_to_cart_button' ),
			    $items            = $('.dynamic-pricing-discounts').find( '.dynamic-pricing-discounts-item' );

			$items.each( function( i, item ) {
				var data           = $(item).data( 'discount' ),
					dataNext       = $(item).next().data( 'discount' ),
					from           = data.from,
					fromNext       = dataNext ? dataNext.from : null,
					to             = data.to,
					price          = $(item).hasClass( 'active') ? data.price : $addtocart_button.find( '.price' ).data( 'price' ),
					discount_price = data.discount_price,
					in_range = false;

				if( to == '0' && fromNext ) {
					if( qty >= from && qty < fromNext ) {
						if( ! $(item).hasClass( 'active' ) ) {
							$(item).addClass( 'active' ).siblings( '.active' ).removeClass( 'active' );
							$(item).find( 'input[type="radio"]' ).prop( 'checked', true );
						}

						change_price_addtocart_button( $addtocart_button, qty, discount_price );

						in_range = true;
					}
				} else {
					if( ( to == '0' && qty >= from ) || ( qty >= from && qty <= to ) ) {
						if( ! $(item).hasClass( 'active' ) ) {
							$(item).addClass( 'active' ).siblings( '.active' ).removeClass( 'active' );
							$(item).find( 'input[type="radio"]' ).prop( 'checked', true );
						}

						change_price_addtocart_button( $addtocart_button, qty, discount_price );

						in_range = true;
					}
				}

				if( in_range ) {
					return false;
				}
				$(item).removeClass( 'active' );
				$(item).find( 'input[type="radio"]' ).prop( 'checked', false );
				change_price_addtocart_button( $addtocart_button, qty, price );
			});
		});
	}

	function select_discount () {
		$( document.body ).on( 'click', '.dynamic-pricing-discounts-item', function() {
			var data              = $(this).data( 'discount' ),
				$radio_button     = $(this).find( 'input[type="radio"]' ),
				$form_cart        = $(this).closest( 'div.product' ).find( '.entry-summary form.cart' ),
				$quantity         = $form_cart.find( '.quantity input.qty' ),
				$addtocart_button = $form_cart.find( '.single_add_to_cart_button' );

			$radio_button.prop( 'checked', false );

			if( ! $(this).hasClass( 'active' ) ) {
				$(this).siblings().removeClass( 'active' );
				$(this).addClass( 'active' );
				$radio_button.prop( 'checked', true );

				if( $(this).closest( '.dynamic-pricing-discounts--grid' ).length > 0 ) {
					$quantity.val( data.from );
				} else {
					if( data.to > data.from ) {
						$quantity.val( data.to );
					} else {
						$quantity.val( data.from );
					}
				}

				$addtocart_button.attr( 'data-discounts', JSON.stringify( data ) );
				if( $addtocart_button.find( '.price' ).length > 0 ) {
					var qty = data.to > data.from ? data.to : data.from;

					if( $(this).closest( '.dynamic-pricing-discounts--grid' ).length > 0 ) {
						qty = data.from;
					}

					change_price_addtocart_button( $addtocart_button, qty, data.discount_price );
				}
			} else {
                $(this).removeClass( 'active' );
				$radio_button.prop( 'checked', false );
                $quantity.val(1);
                $addtocart_button.removeAttr( 'data-discounts' );
				if( $addtocart_button.find( '.price' ).length > 0 ) {
					change_price_addtocart_button( $addtocart_button, 1, data.price );
				}
            }
		});
	}

	function select_discount_variation() {
        $('.single-product div.product .entry-summary .variations_form').on( 'show_variation', function ( event, variation ) {
			var $pricing_discounts_item = $(this).closest( 'div.product' ).find( '.dynamic-pricing-discounts-item' ),
			    $addtocart_button       = $(this).closest( 'form.cart' ).find( '.single_add_to_cart_button' ),
			    qty                     = $(this).closest( 'form.cart' ).find( '.quantity input.qty' ).val(),
			    display_price           = variation.display_price,
			    variation_id            = $(this).find( '.variation_id' ).val();

			$pricing_discounts_item.each( function( index, element ) {
				var $price_variable = $(element).find( '.price' ),
				    discount        = $(element).data( 'discount' ).discount,
				    discount_price  = parseFloat( display_price ) * ( 100 - parseFloat( discount ) ) / 100;

				if( $price_variable && variation_id !== '0' ) {
					$(element).find( '.dynamic-pricing-discounts-item__price' ).removeClass( 'hidden' );
					$price_variable.find( 'ins bdi').html( formatNumber( discount_price ) );
					$price_variable.find( 'del bdi').html( formatNumber( display_price ) );

					var $data_discount = $(element).data( 'discount' );
					$data_discount.price = display_price;
					$data_discount.discount_price = discount_price;
					$(element).data( 'discount', $data_discount);
				}

				if( $(element).hasClass( 'active') ) {
					change_price_addtocart_button( $addtocart_button, qty, discount_price );
				}
			});
        });

        $('.single-product div.product .entry-summary .variations_form').on( 'hide_variation', function () {
			var $pricing_discounts_item = $(this).closest( 'div.product' ).find( '.dynamic-pricing-discounts-item' );

			$pricing_discounts_item.each( function( index, element ) {
				var price           = parseFloat( $(element).data( 'discount' ).price ),
				    discount_price  = parseFloat( $(element).data( 'discount' ).discount_price );

				$(element).find( '.dynamic-pricing-discounts-item__price' ).addClass( 'hidden' );
				$(element).find( '.price ins bdi').html( formatNumber( discount_price ) );
				$(element).find( '.price del bdi').html( formatNumber( price ) );
			});
        });
	}

	function change_price_addtocart_button( $addtocart_button, qty, price ) {
		$addtocart_button.find( '.price' ).html( formatNumber( qty * price ));
	}

	function formatNumber( $number ) {
		var currency       = glozinDpd.currency_symbol,
			thousand       = glozinDpd.thousand_sep,
			decimal        = glozinDpd.decimal_sep,
			price_decimals = glozinDpd.price_decimals,
			currency_pos   = glozinDpd.currency_pos,
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

    /**
     * Document ready
     */
    $(function () {
		if ( typeof glozinDpd === 'undefined' ) {
			return false;
		}

		if ( ! $( 'body' ).hasClass( 'single-product' ) ) {
			return;
		}

		if ( $( '#glozin-dynamic-pricing-discounts' ).length < 1 ) {
			return;
		}

		change_quantity();
		select_discount();
		select_discount_variation();
    });

})(jQuery);