jQuery(document).ready(function ($) {
	var $nav = $( '.glozin_live_sales_notification_navigation' ),
		$table = $nav.closest( 'table' ),
		$class = $( '.' + $nav.val() + '-show' );

	$table.find( '.live-sales--condition' ).closest( 'tr' ).addClass( 'hidden' );
	$table.find( $class ).closest( 'tr' ).removeClass( 'hidden' );

	$nav.on( 'change', function() {
		var $tableNew = $(this).closest( 'table' ),
			$classNew = $( '.' + $(this).val() + '-show' );

		$tableNew.find( '.live-sales--condition' ).closest( 'tr' ).addClass( 'hidden' );
		$tableNew.find( $classNew ).closest( 'tr' ).removeClass( 'hidden' );
	});
});