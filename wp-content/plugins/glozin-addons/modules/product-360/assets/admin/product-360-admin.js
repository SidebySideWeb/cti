(function ($) {
	'use strict';

	var product_gallery_frame;
	function product_360_init() {
		$( '#product_product_360_data' ).on( 'click', '#set-product_360-thumbnail', function( e ) {
			var $el = $( this ),
				$thumbnail_ids = $el.closest('#product_product_360_data').find( '#product_360_thumbnail_ids' ),
				$list_thumbnails = $el.closest('#product_product_360_data').find( 'ul.product-360__list-thumbnails'),
				$delete_button = $el.closest('#product_product_360_data').find( '#delete-product-360-thumbnails');

			e.preventDefault();

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
							multiple: true,
						})
					],
				});
			}

			product_gallery_frame.off( 'select' );

			// When an thumbnail is selected, run a callback.
			product_gallery_frame.on( 'select', function() {
				var selection = product_gallery_frame.state().get( 'selection' );
				var attachment_ids = $thumbnail_ids.val();

				selection.map( function( attachment ) {
					attachment = attachment.toJSON();

					if ( attachment.id ) {
						attachment_ids   = attachment_ids ? attachment_ids + ',' + attachment.id : attachment.id;
						var attachment_thumbnail = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
						$list_thumbnails.append(
							'<li class="thumbnail" data-attachment_id="' + attachment.id + '"><img src="' + attachment_thumbnail +
							'" /><a href="#" class="delete" title="' + $el.data('delete') + '"></a></li>'
						);
					}
				});

				$thumbnail_ids.val(attachment_ids);

				if( $delete_button.hasClass('hidden') ) {
					$delete_button.removeClass( 'hidden' );
				}
			});

			// Finally, open the modal.
			product_gallery_frame.open();
		});

		// Image ordering.
		$( '#product_product_360_data' ).find('ul.product-360__list-thumbnails').sortable({
			items: 'li.thumbnail',
			cursor: 'move',
			scrollSensitivity: 40,
			forcePlaceholderSize: true,
			forceHelperSize: false,
			helper: 'clone',
			opacity: 0.65,
			placeholder: 'wc-metabox-sortable-placeholder',
			start: function( event, ui ) {
				ui.item.css( 'background-color', '#f6f6f6' );
			},
			stop: function( event, ui ) {
				ui.item.removeAttr( 'style' );
			},
			update: function() {
				var attachment_ids = '';

				$(this).closest('#product_product_360_data').find( 'ul.product-360__list-thumbnails li.thumbnail' ).css( 'cursor', 'default' ).each( function() {
					var attachment_id = $( this ).attr( 'data-attachment_id' );
					attachment_ids = attachment_ids + attachment_id + ',';
				});

				$(this).closest('#product_product_360_data').find( '#product_360_thumbnail_ids' ).val( attachment_ids );
			}
		});

		// Delete thumbnail.
		$( '#product_product_360_data' ).on( 'click', 'a.delete', function() {
			var $el = $( this ),
				$thumbnail_list = $el.closest('ul.product-360__list-thumbnails'),
				$thumbnail_ids = $el.closest('#product_product_360_data').find( '#product_360_thumbnail_ids' ),
				$delete_button = $el.closest('#product_product_360_data').find( '#delete-product-360-thumbnails');

			$el.closest( 'li.thumbnail' ).remove();

			var attachment_ids = '';

			$thumbnail_list.find( 'li.thumbnail' ).each( function() {
				var attachment_id = $(this).attr( 'data-attachment_id' );
				attachment_ids = attachment_ids + attachment_id + ',';
			});

			$thumbnail_ids.val(attachment_ids);

			if( attachment_ids == '' ) {
				$delete_button.addClass( 'hidden' );
			}

			return false;
		});

		// Delete all thumbnails
		$( '#product_product_360_data' ).on( 'click', '#delete-product-360-thumbnails', function(e) {
			e.preventDefault();

			var $thumbnail_list = $(this).closest( '#product_product_360_data' ).find( 'ul.product-360__list-thumbnails' ),
				$thumbnail_ids = $(this).closest( '#product_product_360_data' ).find( '#product_360_thumbnail_ids' );

			$(this).addClass( 'hidden' );
			$thumbnail_list.empty();
			$thumbnail_ids.val('');
		});
	}

	/**
	 * Document ready
	 */
	$(function () {
		product_360_init();
	});

})(jQuery);