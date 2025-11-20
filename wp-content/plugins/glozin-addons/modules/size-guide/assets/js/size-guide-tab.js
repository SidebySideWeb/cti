jQuery( document ).ready( function( $ ) {
	$( '.glozin-size-guide-tabs' ).on( 'click', '.glozin-size-guide-tabs__nav li', function() {
        var $tab = $( this ),
            index = $tab.data( 'target' ),
            $panels = $tab.closest( '.glozin-size-guide-tabs' ).find( '.glozin-size-guide-tabs__panels' ),
            $panel = $panels.find( '.glozin-size-guide-tabs__panel[data-panel="' + index + '"]' );

        if ( $tab.hasClass( 'active' ) ) {
            return;
        }

        $tab.addClass( 'active' ).siblings( 'li.active' ).removeClass( 'active' );

        if ( $panel.length ) {
            $panel.addClass( 'active' ).siblings( '.glozin-size-guide-tabs__panel.active' ).removeClass( 'active' );
        }
    } );
} );