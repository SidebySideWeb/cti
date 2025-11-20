jQuery(document).ready(function ($) {
	"use strict";

	$('#the-list').on('click', '.editinline', function(){
		var post_id = $(this).closest('tr').attr('id');

		post_id = post_id.replace("post-", "");

		var unit_id_inline_data = $('#unit_measure_id_inline_' + post_id).find('#unit_measure_id').text();

		$( 'input[name="unit_measure"]', '.inline-edit-row' ).val( unit_id_inline_data );
	});
});