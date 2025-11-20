jQuery(document).ready(function ($) {
	"use strict";

	$( '#variable_product_options' ).on( 'reload', function() {
		block();
		var postID = $('#post_ID').val();
		$.ajax({
			url     : ajaxurl,
			dataType: 'json',
			method  : 'post',
			data    : {
				action : 'glozin_wc_product_variation_compare',
				post_id: postID
			},
			success : function (response) {
				$('#glozin-variation-compare').find('.glozin_product_variation_attribute_field').replaceWith(response.data);
				unblock();
			}
		});
	});

	/**
	 * Block edit screen
	 */
	function block() {
		if ( ! $.fn.block ) {
			return;
		}

		$( '#woocommerce-product-data' ).block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
	}

	/**
	 * Unblock edit screen
	 */
	function unblock() {
		if ( ! $.fn.unblock ) {
			return;
		}

		$( '#woocommerce-product-data' ).unblock();
	}

});