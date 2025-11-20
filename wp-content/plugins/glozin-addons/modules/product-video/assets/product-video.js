(function ($) {
	'use strict';

	function product_video_init() {
		var $gallery = $('.woocommerce-product-gallery'),
			$pagination = $gallery.find('.glozin-product-gallery-thumbnails'),
			$video = $gallery.find('.woocommerce-product-gallery__image.glozin-product-video');

		if ($video.length > 0) {
			var videoNumber = $video.index();
			$gallery.addClass('has-video');
			$pagination.find('.woocommerce-product-gallery__image').eq(videoNumber).append('<div class="glozin-i-video"></div>');
		}

		$(document.body).on( 'glozing_product_thumbnails_init glozin_product_quick_view_loaded', function() {
			var $gallery = $('.woocommerce-product-gallery'),
				$pagination = $gallery.find('.glozin-product-gallery-thumbnails'),
				$video = $gallery.find('.woocommerce-product-gallery__image.glozin-product-video');

			if ($video.length > 0 && ! $gallery.addClass('has-video')) {
				var videoNumber = $video.index();
				$gallery.addClass('has-video');
				$pagination.find('.woocommerce-product-gallery__image').eq(videoNumber).append('<div class="glozin-i-video"></div>');
			}
		});

		$(document.body).on('click', '.glozin-i-video', function(e) {
			e.preventDefault();

			var $wrapper = $(this).closest('.glozin-product-video'),
				$videoWrapper = $wrapper.find('.glozin-video-wrapper');

				if ( $videoWrapper.hasClass('video-youtube') ) {
					const iframe = $videoWrapper.find('iframe').get(0);
					iframe.contentWindow.postMessage('{"event":"command","func":"playVideo","args":""}', '*');
					if( ! $(this).closest( '.product-video-loop-thumbnail' ).length ) {
						iframe.contentWindow.postMessage('{"event":"command","func":"unMute","args":""}', '*');
					}
				} else if ( $videoWrapper.hasClass('video-vimeo') ) {
					const iframe = $videoWrapper.find('iframe').get(0);
					iframe.contentWindow.postMessage('{"method":"play","value":""}', "*");
					if( ! $(this).closest( '.product-video-loop-thumbnail' ).length ) {
						iframe.contentWindow.postMessage('{"method":"setVolume","value":1}', "*");
					}
				} else {
					const video = $videoWrapper.find('video').get(0);
					if( ! $(this).closest( '.product-video-loop-thumbnail' ).length ) {
						if (typeof video.muted !== 'undefined') {
							video.muted = false; 
						}
						if (typeof video.volume !== 'undefined') {
							video.volume = 1; 
						}
					}
					video.play();
				}

			$wrapper.addClass('glozin-product-video-play');
		});

		$(document.body).on( 'glozin_product_gallery_init glozin_product_gallery_quickview_init', function() {
			var $gallery = $('.woocommerce-product-gallery__wrapper'),
				$swiperSlider = $gallery.find( '.swiper-slide' );

			$swiperSlider.each( function() {
				var $wrapper = $(this).closest( '.glozin-product-video' ),
					$video = $(this).find('.glozin-video-wrapper');

				if( $video.hasClass( 'video-autoplay' ) ) {
					$wrapper.addClass('glozin-product-video-play');
				}

				if( $(this).hasClass( 'swiper-slide-active' ) ) {
					if( $video.length > 0 ) {
						if( $video.hasClass('video-youtube') ) {
							if( $video.hasClass( 'video-autoplay' ) ) {
								$video.find( 'iframe' ).attr( 'src', $video.find( 'iframe' ).attr( 'src' ) + '&enablejsapi=1&playsinline=1&mute=1&playerapiid=ytplayer&showinfo=0&fs=0&modestbranding=0&rel=0&loop=1&html5=1&autoplay=1' );
							} else {
								$video.find( 'iframe' ).attr( 'src', $video.find( 'iframe' ).attr( 'src' ) + '&enablejsapi=1&playsinline=1&mute=1&playerapiid=ytplayer&showinfo=0&fs=0&modestbranding=0&rel=0&loop=1&html5=1' );
							}
						} else if ( $video.hasClass('video-vimeo') ) {
							$video.find( 'iframe' ).attr( 'src', $video.find( 'iframe' ).attr( 'src' ) + '&api=1&autoplay=1&muted=1&loop=1' );
						}
					}
				} else {
					if( $video.length > 0 ) {
						if( $video.hasClass('video-youtube') ) {
							$video.find( 'iframe' ).attr( 'src', $video.find( 'iframe' ).attr( 'src' ) + '&enablejsapi=1&playsinline=1&mute=1&playerapiid=ytplayer&showinfo=0&fs=0&modestbranding=0&rel=0&loop=1&html5=1' );
						} else if ( $video.hasClass('video-vimeo') ) {
							$video.find( 'iframe' ).attr( 'src', $video.find( 'iframe' ).attr( 'src' ) + '&api=1&muted=1&loop=1' );
						}

						if ( $wrapper.hasClass('glozin-product-video-play') ) {
							$wrapper.removeClass('glozin-product-video-play');
						}
					}
				}
			});

			autoPlay();
		} );

		$('.product-video-loop-thumbnail').on( 'glozin_lazy_load_image_loaded', function() {
			if( ! $(this).length ) {
				return;
			}

			var $videoWrapper = $(this).find('.glozin-video-wrapper');

			if( ! $videoWrapper.length ) {
				return;
			}

			if( $videoWrapper.hasClass('video-youtube') ) {
				if( $videoWrapper.hasClass('video-autoplay') ) {
					$videoWrapper.find( 'iframe' ).attr( 'src', $videoWrapper.find( 'iframe' ).attr( 'src' ) + '&enablejsapi=1&playsinline=1&mute=1&playerapiid=ytplayer&showinfo=0&fs=0&modestbranding=0&rel=0&loop=1&html5=1&autoplay=1&controls=0' );
				} else {
					$videoWrapper.find( 'iframe' ).attr( 'src', $videoWrapper.find( 'iframe' ).attr( 'src' ) + '&enablejsapi=1&playsinline=1&mute=1&playerapiid=ytplayer&showinfo=0&fs=0&modestbranding=0&rel=0&loop=1&html5=1&controls=0' );
				}
			} else if ( $videoWrapper.hasClass('video-vimeo') ) {
				$videoWrapper.find( 'iframe' ).attr( 'src', $videoWrapper.find( 'iframe' ).attr( 'src' ) + '&api=1&autoplay=1&muted=1&loop=1&background=1' );
			}
		} );
	}

	function autoPlay() {
		var $gallery = $('.woocommerce-product-gallery__wrapper');

		if( $gallery.length && $gallery.get(0).swiper ) {
			$(document.body).on( 'glozin_product_gallery_slideChangeTransitionEnd glozin_product_gallery_quickview_slideChangeTransitionEnd', function() {
				var $swiperSlider = $gallery.find( '.swiper-slide' );

				$swiperSlider.each( function() {
					var $video = $(this).find('.glozin-video-wrapper');

					if( $(this).hasClass( 'swiper-slide-active' ) ) {
						if( ! $video.hasClass( 'video-autoplay' ) ) {
							return;
						}

						$(this).addClass('glozin-product-video-play');
						
						if( $video.length > 0 ) {
							if( $video.hasClass('video-youtube') ) {
								$video.find('iframe').get(0).contentWindow.postMessage('{"event":"command","func":"playVideo","args":""}', '*');
							} else if ( $video.hasClass('video-vimeo') ) {
								$video.find('iframe').get(0).contentWindow.postMessage('{"method":"play","value":""}', "*");
							} else {
								$video.find('video').get(0).play();
							}
						}
					} else {
						if( $video.length > 0 ) {
							if( $video.hasClass('video-youtube') ) {
								
								$video.find('iframe').get(0).contentWindow.postMessage('{"event":"command","func":"pauseVideo","args":""}', '*');
							} else if ( $video.hasClass('video-vimeo') ) {
								$video.find('iframe').get(0).contentWindow.postMessage('{"method":"pause","value":""}', "*");
							} else {
								$video.find('video').get(0).pause();
							}
						}

						if ( $(this).hasClass('glozin-product-video-play') ) {
							$(this).removeClass('glozin-product-video-play');
						}
					}
				});
			} );
		} else {
			var $items = $gallery.find( '.woocommerce-product-gallery__image' );

			$items.each( function() {
				var $video = $(this).find('.glozin-video-wrapper');

				if( $video.length > 0 ) {
					if( $video.hasClass('video-youtube') ) {
						if( $video.hasClass( 'video-autoplay' ) ) {
							$video.find( 'iframe' ).attr( 'src', $video.find( 'iframe' ).attr( 'src' ) + '&enablejsapi=1&playsinline=1&mute=1&playerapiid=ytplayer&showinfo=0&fs=0&modestbranding=0&rel=0&loop=1&html5=1&autoplay=1' );
						} else {
							$video.find( 'iframe' ).attr( 'src', $video.find( 'iframe' ).attr( 'src' ) + '&enablejsapi=1&playsinline=1&mute=1&playerapiid=ytplayer&showinfo=0&fs=0&modestbranding=0&rel=0&loop=1&html5=1' );
						}
					} else if ( $video.hasClass('video-vimeo') ) {
						$video.find( 'iframe' ).attr( 'src', $video.find( 'iframe' ).attr( 'src' ) + '&api=1&autoplay=1&muted=1&loop=1' );
					}
				}

				if( ! $video.hasClass( 'video-autoplay' ) ) {
					return;
				}

				$(this).addClass('glozin-product-video-play');
				
				if( $video.length > 0 ) {
					if( $video.hasClass('video-youtube') ) {
						$video.find('iframe').get(0).contentWindow.postMessage('{"event":"command","func":"playVideo","args":""}', '*');
					} else if ( $video.hasClass('video-vimeo') ) {
						$video.find('iframe').get(0).contentWindow.postMessage('{"method":"play","value":""}', "*");
					} else {
						$video.find('video').get(0).play();
					}
				}
			});
		}
	}

	/**
	 * Document ready
	 */
	$(function () {
		product_video_init();
		autoPlay();
		$(document.body).on( 'glozin_product_gallery_quickview_init', function() {
			autoPlay();
		});
	});

})(jQuery);