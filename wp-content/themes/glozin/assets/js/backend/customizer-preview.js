/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $, api ) {
	'use strict';

	var $popup = $( '#popup-modal' );
	var popupOpened = $popup.hasClass( 'open' );

	// Some handlers need to attach after previewer ready.
	api.bind( 'preview-ready', function() {
		// Toggle the poup when entering/leaving the section.
		api.preview.bind( 'open_popup_previewer', function( data ) {
			if ( ! api( 'popup_enable' ).get() ) {
				return;
			}

			if ( data.expanded ) {
				openPopup();
			} else if ( ! popupOpened ) {
				closePopup();
			}
		} );

	} );


} )( jQuery, wp.customize );
