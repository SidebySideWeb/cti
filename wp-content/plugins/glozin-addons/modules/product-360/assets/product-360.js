jQuery( document ).ready(function($) {
	var $gallery = $('.woocommerce-product-gallery'),
		$360 = $gallery.find('.woocommerce-product-gallery__image.glozin-product-360');

	if ($360.length > 0) {
		$gallery.addClass('has-360');
	}

	var $pagination = $gallery.find('.glozin-product-gallery-thumbnails'),
		$video = $gallery.find('.woocommerce-product-gallery__image.glozin-product-360');

	if ($video.length > 0) {
		var videoNumber = $video.index();
		$pagination.find('.woocommerce-product-gallery__image').eq(videoNumber).append('<span class="glozin-i-360" role="button"></span>');
	}

	$(document.body).on( 'click', '.glozin-product-360 .glozin-i-360', function() {
		window.CI360.init();

		$(this).addClass( 'hidden' );

		if( $(this).find( '.glozin-product-360__image' ).length ) {
			$(this).find( '.glozin-product-360__image' ).remove();
		}
	});
});