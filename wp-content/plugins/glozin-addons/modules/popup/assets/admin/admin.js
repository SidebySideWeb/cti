(function($) {
	$(function() {
		$(".glozin-popup__toggle-button").on("change", '.glozin-popup__enabled', function(e) {
			e.preventDefault();
			var $button = $(this),
				newState = 0,
				post_ID = $button.data("popup-id"),
				nonce = $button.data("nonce");

			if (true === e.target.checked) {
				newState = 1;
			}

			$.ajax({
				type: "POST",
				dataType: "json",
				// eslint-disable-next-line no-undef
				url: ajaxurl,
				data: {
					action: "glozin_save_popup_enable",
					nonce: nonce,
					post_ID: post_ID,
					enabled: newState
				}
			});
		});

		if ( $('.glozin-popup__visible').val() == 'delay' ) {
			$('.glozin-popup__visible').next().removeClass('hidden');
		}

		$(".glozin-popup-triggers").on("change", '.glozin-popup__visible', function(e) {
			var value = $(this).find(':selected').val();

			if ( value == 'delay' ) {
				$(this).next().removeClass('hidden');
			} else {
				$(this).next().addClass('hidden');
			}

		});

		// Sort product tabs
		$( 'table.widefat tbody th, table.widefat tbody td' ).css( 'cursor', 'move' );

		$( 'table.widefat tbody' ).sortable({
			items: 'tr:not(.inline-edit-row)',
			cursor: 'move',
			axis: 'y',
			containment: 'table.widefat',
			scrollSensitivity: 40,
			helper: function( event, ui ) {
				ui.each( function() {
					$( this ).width( $( this ).width() );
				});
				return ui;
			},
			start: function( event, ui ) {
				ui.item.css( 'background-color', '#ffffff' );
				ui.item.children( 'td, th' ).css( 'border-bottom-width', '0' );
				ui.item.css( 'outline', '1px solid #dfdfdf' );
			},
			stop: function( event, ui ) {
				ui.item.removeAttr( 'style' );
				ui.item.children( 'td,th' ).css( 'border-bottom-width', '1px' );
			},
			update: function( event, ui ) {
				$( 'table.widefat tbody th, table.widefat tbody td' ).css( 'cursor', 'default' );
				$( 'table.widefat tbody' ).sortable( 'disable' );

				var postid     = ui.item.attr( 'id' ).replace( 'post-', '' );
				var prevpostid = ui.item.prev().attr( 'id' ) ? ui.item.prev().attr( 'id' ).replace( 'post-', '' ) : 0;
				var nextpostid = ui.item.next().attr( 'id' ) ? ui.item.next().attr( 'id' ).replace( 'post-', '' ) : 0;

				// Show Spinner
				ui.item.find( '.check-column' ).append( '<img alt="processing" src="images/wpspin_light.gif" class="waiting" style="margin-inline-start: 6px;" />' );

				// Go do the sorting stuff via ajax
				$.post(
					ajaxurl,
					{ action: 'glozin_popup_ordering', id: postid, previd: prevpostid, nextid: nextpostid },
					function( response ) {
						ui.item.find( '.check-column img' ).remove();
						$( 'table.widefat tbody th, table.widefat tbody td' ).css( 'cursor', 'move' );
						$( 'table.widefat tbody' ).sortable( 'enable' );
					}
				);

				// fix cell colors
				$( 'table.widefat tbody tr' ).each( function() {
					var i = $( 'table.widefat tbody tr' ).index( this );
					if ( i%2 === 0 ) {
						$( this ).addClass( 'alternate' );
					} else {
						$( this ).removeClass( 'alternate' );
					}
				});
			}
		});

	});
})(jQuery);