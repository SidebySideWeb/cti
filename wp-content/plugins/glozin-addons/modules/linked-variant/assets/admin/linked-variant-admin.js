jQuery(document).ready(function ($) {
	// Switch status
	$('.linked-variant__switch').on( 'change', 'input', function () {
		var $switch = $(this).closest('.linked-variant__switch'),
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

		if( $switch.hasClass( 'linked-variant__switch--column' ) ) {
			var post_id = $switch.data( 'post_id' );

			if ( $switch.data( 'requestRunning' ) ) {
				return;
			}

			$switch.data( 'requestRunning', true );

			$.ajax({
				type: "POST",
				url: ajaxurl,
				data: {
					action: 'glozin_linked_variant_status_update',
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
	$( '.glozin-linked-variant__items' ).sortable({
		items: '.glozin-linked-variant__attribute',
		cursor: 'move',
		scrollSensitivity: 40,
		forcePlaceholderSize: true,
		forceHelperSize: false,
		helper: 'clone',
		opacity: 0.65,
	});
});