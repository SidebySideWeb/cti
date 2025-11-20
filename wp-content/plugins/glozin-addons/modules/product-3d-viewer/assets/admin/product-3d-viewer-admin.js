(function ($) {
	'use strict';

	var product_gallery_frame;
	function product_3d_viewer_init() {
		$( '#product_product_3d_viewer_data' ).on( 'click', '#set-product_3d_viewer-thumbnail', function( event ) {
			var $el = $( this ),
				$thumbnail_id = $el.closest('.form-field').find('#product_3d_viewer_thumbnail_id'),
				$remove_product_3d_viewer = $el.closest('.form-field').find('#remove-product_3d_viewer-thumbnail');

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

						$remove_product_3d_viewer.removeClass('hidden');

						$thumbnail_id.val( attachment.id );

					}
				});

			});

			// Finally, open the modal.
			product_gallery_frame.open();
		});


		// Remove images.
		$( '#product_product_3d_viewer_data' ).on( 'click', '#remove-product_3d_viewer-thumbnail', function() {
			var $el = $( this ),
				$thumbnail_id = $el.closest('.form-field').find('#product_3d_viewer_thumbnail_id'),
				$set_product_3d_viewer = $el.closest('.form-field').find('#set-product_3d_viewer-thumbnail');

			$el.addClass('hidden');

			$thumbnail_id.val(0);
			$set_product_3d_viewer.html( $el.data('set-text') );

			return false;
		});
	}

	/**
	 * Document ready
	 */
	$(function () {
		product_3d_viewer_init();
	});

})(jQuery);