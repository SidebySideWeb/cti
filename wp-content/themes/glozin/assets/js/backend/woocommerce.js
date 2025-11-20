jQuery(document).ready(function ($) {
	"use strict";

	$( '#custom_badges_bg' ).not( '[id*="__i__"]' ).wpColorPicker({
		change: function(e, ui) {
			$(e.target).val(ui.color.toString());
			$(e.target).trigger('change');
		},
		clear: function(e, ui) {
			$(e.target).trigger('change');
		}
	});

	$( '#custom_badges_color' ).not( '[id*="__i__"]' ).wpColorPicker({
		change: function(e, ui) {
			$(e.target).val(ui.color.toString());
			$(e.target).trigger('change');
		},
		clear: function(e, ui) {
			$(e.target).trigger('change');
		}
	});

	$( '#variable_product_options' ).on( 'reload', function() {
		block();
		var postID = $('#post_ID').val();
		$.ajax({
			url     : ajaxurl,
			dataType: 'json',
			method  : 'post',
			data    : {
				action : 'glozin_wc_product_attributes',
				post_id: postID
			},
			success : function (response) {
				$('#glozin-product-attributes').html(response.data);
				unblock();
			}
		});
	});

	$('#the-list').on('click', '.editinline', function(){
		var post_id = $(this).closest('tr').attr('id');

		post_id = post_id.replace("post-", "");

		var unit_id_inline_data = $('#unit_measure_id_inline_' + post_id).find('#unit_measure_id').text();

		$( 'input[name="unit_measure"]', '.inline-edit-row' ).val( unit_id_inline_data );
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