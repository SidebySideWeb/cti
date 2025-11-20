jQuery(document).ready(function ($) {
	// Switch status
	$(document).on( 'change', '.pre-order__switch input', function () {
		var $switch = $(this).closest('.pre-order__switch'),
			$text	= $switch.siblings( '.pre-order__switch-label' ),
			status  = $switch.hasClass( 'enabled' ) ? 'yes' : 'no',
			$condition = $(this).closest('#product_pre_order_data').find('.pre-order__condition');

		if( $switch.hasClass( 'enable' ) ) {
			$switch.removeClass( 'enable' ).addClass( 'disable' );
            $text.text( $text.data( 'disable' ) );
			status = 'no';
			$condition.slideUp();
		} else {
			$switch.removeClass( 'disable' ).addClass( 'enable' );
            $text.text( $text.data( 'enable' ) );
			status = 'yes';
			$condition.slideDown();
		}

		if( $switch.hasClass( 'pre-order__switch--column' ) ) {
			var post_id = $switch.data( 'post_id' );

			if ( $switch.data( 'requestRunning' ) ) {
				return;
			}

			$switch.data( 'requestRunning', true );

			$.ajax({
				type: "POST",
				url: ajaxurl,
				data: {
					action: 'glozin_pre_order_status_update',
					post_id: post_id,
					status: status
				},
				success: function( response ) {
					$switch.data( 'requestRunning', false );
				}
			});
		}
	});

	// Datepicker
	var now = new Date();
	$( '#_pre_order_date' ).datepicker( {
		defaultDate: '',
		dateFormat: 'yy-mm-dd',
		numberOfMonths: 1,
		showButtonPanel: true,
		minDate: now,
	});

	$(document).on( 'woocommerce_variations_loaded', function() {
		$( '.woocommerce_variation' ).each(function() {
			$( this ).find( '.pre_order_date' ).datepicker( {
				defaultDate: '',
				dateFormat: 'yy-mm-dd',
				numberOfMonths: 1,
				showButtonPanel: true,
				minDate: now,
			});
		});
	});
});