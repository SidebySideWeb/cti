(function ($) {
	'use strict';

	var product_gallery_frame;
	function model_sizing_init() {
		$( '#product_model_sizing_data' ).on( 'click', '#set-model_sizing-thumbnail', function( event ) {
			var $el = $( this ),
				$thumbnail_id = $el.closest('.form-field').find('#model_sizing_thumbnail_id'),
				$remove_model_sizing = $el.closest('.form-field').find('#remove-model_sizing-thumbnail');

			event.preventDefault();

			// Create the media frame.
			if ( ! product_gallery_frame ) {
				product_gallery_frame = wp.media({
					// Set the title of the modal.
					title: $el.data( 'choose' ),
					button: {
						text: $el.data( 'update' )
					},
					states: [
						new wp.media.controller.Library({
							title: $el.data( 'choose' ),
							filterable: 'all',
						})
					]
				});
			}

			product_gallery_frame.off( 'select' );

			// When an image is selected, run a callback.
			product_gallery_frame.on( 'select', function() {
				var selection = product_gallery_frame.state().get( 'selection' );

				selection.map( function( attachment ) {
					attachment = attachment.toJSON();

					if ( attachment.id ) {
						var attachment_image = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

						$el.html(
							'<img src="' + attachment_image +
							'" />'
						);

						$remove_model_sizing.removeClass('hidden');

						$thumbnail_id.val( attachment.id );

					}
				});

			});

			// Finally, open the modal.
			product_gallery_frame.open();
		});


		// Remove images.
		$( '#product_model_sizing_data' ).on( 'click', '#remove-model_sizing-thumbnail', function() {
			var $el = $( this ),
				$thumbnail_id = $el.closest('.form-field').find('#model_sizing_thumbnail_id'),
				$set_model_sizing = $el.closest('.form-field').find('#set-model_sizing-thumbnail');

			$el.addClass('hidden');

			$thumbnail_id.val(0);
			$set_model_sizing.html( $el.data('set-text') );

			return false;
		});
	}

	function add_custom_information() {
		$( '#product_model_sizing_data' ).on( 'click', '.add-custom-information', function() {
			var $el = $( this ),
				$custom_information_item = $el.siblings('.custom-information-item').first(),
				remove_text = $custom_information_item.data( 'remove' );

			var clone = $custom_information_item.clone();
			
			clone.find('input').val('');
			if( ! clone.find('.remove-custom-information').length ) {
				clone.append( '<button type="button" class="button remove-custom-information">' + remove_text + '</button>' );
			}
			clone.insertBefore( '.add-custom-information' );
		});

		$( '#product_model_sizing_data' ).on( 'click', '.remove-custom-information', function() {
			var $el = $( this ),
				$custom_information_item = $el.closest('.custom-information-item');

			$custom_information_item.remove();
		});
	}

	/**
	 * Document ready
	 */
	$(function () {
		model_sizing_init();
		add_custom_information();
	});

})(jQuery);