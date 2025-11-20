jQuery(document).ready(function ($) {
	// Switch status
	$('.buy-x-get-y__switch').on( 'change', 'input', function () {
		var $switch = $(this).closest('.buy-x-get-y__switch'),
			$text	= $(this).siblings( '.text' ),
			status  = $switch.hasClass( 'enabled' ) ? 'yes' : 'no';

		if( $switch.hasClass( 'enable' ) ) {
			$switch.removeClass( 'enable' ).addClass( 'disable' );
            $text.text( $text.data( 'disable' ) );
			status = 'no';
		} else {
			$switch.removeClass( 'disable' ).addClass( 'enable' );
            $text.text( $text.data( 'enable' ) );
			status = 'yes';
		}

		if( $switch.hasClass( 'buy-x-get-y__switch--column' ) ) {
			var post_id = $switch.data( 'post_id' );

			if ( $switch.data( 'requestRunning' ) ) {
				return;
			}

			$switch.data( 'requestRunning', true );

			$.ajax({
				type: "POST",
				url: ajaxurl,
				data: {
					action: 'glozin_buy_x_get_y_status_update',
					post_id: post_id,
					status: status
				},
				success: function( response ) {
					$switch.data( 'requestRunning', false );
				}
			});
		}
	});

	// Sortable
	$( '.glozin-buy-x-get-y__items' ).sortable({
		items: '.group-items',
		cursor: 'move',
		scrollSensitivity: 40,
		forcePlaceholderSize: true,
		forceHelperSize: false,
		helper: 'clone',
		opacity: 0.65,
	});

	$(document).on( 'change', '.glozin-buy-x-get-y__items .product-discount-type select', function () {
		var $select = $(this),
			$discount_input = $select.closest( '.group-items' ).find( 'input[type="number"].discount' );

		if( $select.val() == 'percent' || $select.val() == 'fixed_price' ) {
			$discount_input.prop( 'disabled', false );
		} else {
			$discount_input.prop( 'disabled', true );
		}
	});

	// Change field product tab type
	var $settings = $('#glozin-buy-x-get-y-source'),
		$display = $settings.find('select[name="_buy_x_get_y_display"]'),
		$product = $settings.find('.glozin-buy-x-get-y--product'),
		$categories = $settings.find('.glozin-buy-x-get-y--categories');

	$display.on( 'change', function() {
		var display = $(this).val();

		if( display == 'products' ) {
			$product.removeClass( 'hidden' );
			$categories.addClass( 'hidden' );

		} else if( display == 'categories' ) {
			$product.addClass( 'hidden' );
			$categories.removeClass( 'hidden' );
		}
	});

	// Add new and remove group
	$(document.body).on( 'click', '.glozin-buy-x-get-y__addnew', function ( e ) {
		e.preventDefault();

		var $clone = $(this).closest( '.glozin-buy-x-get-y' ).find( '.glozin-buy-x-get-y__group' ).first().clone(),
			index = $(this).closest( '.glozin-buy-x-get-y__items' ).find( '.group-items' ).length;

		$clone.find( 'input' ).val( '' );
		$clone.find( 'input[type="number"]' ).val( 0 );
		$clone.find( 'input[type="number"].discount' ).prop('disabled', true);
		
		var $selectWoo = $clone.find( '.wc-product-search' );
		var select2_args = {
			allowClear:  $selectWoo.data( 'allow_clear' ) ? true : false,
			placeholder: $selectWoo.data( 'placeholder' ),
			minimumInputLength: $selectWoo.data( 'minimum_input_length' ) ? $selectWoo.data( 'minimum_input_length' ) : '3',
			escapeMarkup: function( m ) {
				return m;
			},
			ajax: {
				url:         wc_enhanced_select_params.ajax_url,
				dataType:    'json',
				delay:       250,
				data:        function( params ) {
					return {
						term         : params.term,
						action       : $selectWoo.data( 'action' ) || 'woocommerce_json_search_products_and_variations',
						security     : wc_enhanced_select_params.search_products_nonce,
						exclude      : $selectWoo.data( 'exclude' ),
						exclude_type : $selectWoo.data( 'exclude_type' ),
						include      : $selectWoo.data( 'include' ),
						limit        : $selectWoo.data( 'limit' ),
						display_stock: $selectWoo.data( 'display_stock' )
					};
				},
				processResults: function( data ) {
					var terms = [];
					if ( data ) {
						$.each( data, function( id, text ) {
							terms.push( { id: id, text: text } );
						});
					}
					return {
						results: terms
					};
				},
				cache: true
			}
		};

		select2_args = $.extend( select2_args, getEnhancedSelectFormatString() );

		$selectWoo.siblings().remove();
		$selectWoo.children().remove();
		$clone.find( '.wc-product-search' ).selectWoo(select2_args);
		$clone.find( 'select:not(.wc-product-search)' ).prop( 'selectedIndex', 0 );

		rename($clone.find( 'input' ), index);
		rename($clone.find( '.wc-product-search' ), index);
		rename($clone.find( 'select:not(.wc-product-search)' ), index);

		$clone.addClass( 'glozin-buy-x-get-y__group--clone' );
		$clone.find( '.glozin-buy-x-get-y__remove' ).removeClass( 'hidden' );
		$(this).closest( '.glozin-buy-x-get-y' ).find( '.glozin-buy-x-get-y__button' ).before( $clone );
	});

	$(document.body).on( 'click', '.glozin-buy-x-get-y__remove', function ( e ) {
        e.preventDefault();
        $(this).closest( '.glozin-buy-x-get-y__group--clone' ).remove();
    });

	// Products quantity of display is products
	$('.product-buy-x-get-y-id').on( 'change', function(){
		var $product_qtys = $(this).closest( '.glozin-buy-x-get-y-source').find( '.glozin-product-quantitys' ),
			$form_field   = $product_qtys.closest( '.form-field' ),
			post_id       = $(this).closest( '.glozin-buy-x-get-y-source').data( 'post_id'),
			ajax_url      = glozinBXGYadmin.ajax_admin_url.toString().replace('%%endpoint%%', 'glozin_get_quantity_products');

		if( ! $product_qtys.length ) {
			return;
		}

		$product_qtys.addClass( 'loading' );

		$.ajax({
			type: "POST",
			url: ajax_url,
			data: {
				action: 'glozin_get_quantity_products',
				post_id: post_id,
				product_ids: $(this).val(),
			},
			success: function( response ) {
				if( ! response ) {
					window.location = window.location.href;
				}

				$product_qtys.html( response.data ) ;

				if( response.data && $form_field.hasClass( 'hidden' ) ) {
					$form_field.removeClass( 'hidden' );
				}

				if( ! response.data && ! $form_field.hasClass( 'hidden' ) ) {
					$form_field.addClass( 'hidden' );
				}
			},
			complete: function() {
				$product_qtys.removeClass( 'loading' );
			}
		});
	});
	
	// Functions
	function rename( $el, index ) {
		$el.each( function() {
			var currentIndex = $(this).attr('name').match(/\[(\d+)\]/)[1];

			$(this).attr('name', $(this).attr('name').replace( '['+ currentIndex +']', '['+ index +']' ) );
		});
	}

	function getEnhancedSelectFormatString() {
		return {
			'language': {
				errorLoading: function() {
					// Workaround for https://github.com/select2/select2/issues/4355 instead of i18n_ajax_error.
					return wc_enhanced_select_params.i18n_searching;
				},
				inputTooLong: function( args ) {
					var overChars = args.input.length - args.maximum;

					if ( 1 === overChars ) {
						return wc_enhanced_select_params.i18n_input_too_long_1;
					}

					return wc_enhanced_select_params.i18n_input_too_long_n.replace( '%qty%', overChars );
				},
				inputTooShort: function( args ) {
					var remainingChars = args.minimum - args.input.length;

					if ( 1 === remainingChars ) {
						return wc_enhanced_select_params.i18n_input_too_short_1;
					}

					return wc_enhanced_select_params.i18n_input_too_short_n.replace( '%qty%', remainingChars );
				},
				loadingMore: function() {
					return wc_enhanced_select_params.i18n_load_more;
				},
				maximumSelected: function( args ) {
					if ( args.maximum === 1 ) {
						return wc_enhanced_select_params.i18n_selection_too_long_1;
					}

					return wc_enhanced_select_params.i18n_selection_too_long_n.replace( '%qty%', args.maximum );
				},
				noResults: function() {
					return wc_enhanced_select_params.i18n_no_matches;
				},
				searching: function() {
					return wc_enhanced_select_params.i18n_searching;
				}
			}
		};
	}
});