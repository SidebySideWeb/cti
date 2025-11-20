jQuery( document ).ready(function($) {
	$('.single-product div.product .entry-summary .variations_form:not(.product-select__variation)').on( 'found_variation', function( event, variation ) {
		var $stockProgressBar    = $(this).closest('.entry-summary').find('.glozin-stock-progress-bar'),
			$stockProgressBarNoHtml = $(this).closest('.entry-summary').find('.glozin-stock-progress-bar__no-html'),
			$productATCGroup        = $(this).closest('.entry-summary').find('.glozin-product-atc');

		if( variation.stock_progress_bar_html ) {
			if( $stockProgressBar.length > 0 ) {
				$stockProgressBar.replaceWith( variation.stock_progress_bar_html );
			} else {
				if( $stockProgressBarNoHtml.length > 0 ) {
					$stockProgressBarNoHtml.replaceWith( variation.stock_progress_bar_html );
				} else {
					$productATCGroup.insertBefore( variation.stock_progress_bar_html );
				}
			}
		} else {
			if( $stockProgressBar.length > 0 ) {
				$stockProgressBar.replaceWith( '<div class="glozin-stock-progress-bar__no-html"></div>' );
			}
		}
	});

	$('.single-product div.product .entry-summary .variations_form:not(.product-select__variation)').on( 'reset_data', function( event, variation ) {
		var $stockProgressBar    = $(this).closest('.entry-summary').find('.glozin-stock-progress-bar');
		
		if( $stockProgressBar.length > 0 ) {
			$stockProgressBar.replaceWith( '<div class="glozin-stock-progress-bar__no-html"></div>' );
		}
	});
});