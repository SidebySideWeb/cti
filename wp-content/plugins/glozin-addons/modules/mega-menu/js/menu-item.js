jQuery( function( $ ) {
	'use strict';

	var $modal = $('#megamenu-modal');

	$modal.on( 'change', '.menu-item-settings .glozin-menu-item-taxonomy select', function( event ) {
		if ( 'product_cat' === event.target.value ) {
			$( this ).closest( '.menu-item-settings' ).find( '.glozin-menu-item-taxonomy-category' ).removeClass( 'hidden' );
			$( this ).closest( '.menu-item-settings' ).find( '.glozin-menu-item-taxonomy-brand' ).addClass( 'glozin-hidden' );
		} else {
			$( this ).closest( '.menu-item-settings' ).find( '.glozin-menu-item-taxonomy-category' ).addClass( 'hidden' );
			$( this ).closest( '.menu-item-settings' ).find( '.glozin-menu-item-taxonomy-brand' ).removeClass( 'glozin-hidden' );
		}
	} );

	$modal.on( 'click', '.menu-item-depth-0 .item-edit', function( event ) {
		var $value = $modal.find( '.menu-item-settings .glozin-menu-item-taxonomy select' ).val();

		if( $value == 'product_brand' ) {
			$( this ).closest( '.megamenu-modal-grid__items' ).find( '.glozin-menu-item-taxonomy-category' ).addClass( 'hidden' );
			$( this ).closest( '.megamenu-modal-grid__items' ).find( '.glozin-menu-item-taxonomy-brand' ).removeClass( 'glozin-hidden' );
		}
	});
} );
