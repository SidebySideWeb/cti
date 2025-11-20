(function ($) {
	'use strict';

	var glozin = glozin || {};

	glozin.init = function () {
        this.selectVariation();
    }

    glozin.selectVariation = function() {
        var $selector = $( '#product-variation-compare-modal' );

        if ( !$selector.length ) {
            return;
        }

        var $selects = $selector.find( '.gz-product-compare-attributes__selects' );

        if ( !$selects.length ) {
            return;
        }

        $selects.on( 'click', '.gz-product-compare-attributes__item', function() {
            $(this).toggleClass( 'active' );

            var key = $(this).data( 'key' ),
                $product = $selects.siblings( '.gz-product-compare-attributes__products' ).find( '.gz-product-compare-attributes__product[data-key="' + key + '"]' );

            $product.toggleClass( 'active' );
        });
    }
	/**
	 * Document ready
	 */
	$(function () {
		glozin.init();
	});

})(jQuery);