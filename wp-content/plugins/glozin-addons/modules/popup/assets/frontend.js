(function($) {

	function closePopup($popup, days) {
		var date = new Date(),
			value = date.getTime(),
			options = $popup.data('options'),
			post_ID = options.post_ID;

		date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
		document.cookie = 'glozin_popup_'+ post_ID +'=' + value + ';expires=' + date.toGMTString() + ';path=/';

		if ( $popup.hasClass('glozin-popup-type--slide') ) {
			$popup.removeClass('offscreen-panel--open');
			$popup.find('.panel__backdrop').fadeOut();
			$( document.body ).removeClass( 'offcanvas-opened' );
		} else {
			$popup.removeClass('modal--open').addClass( 'modal--closing' ).fadeOut(400, function() {
				$( this ).removeClass( 'modal--closing' );
			});
			$( document.body ).removeClass( 'modal-opened' );
		}

		$(document.body).removeAttr('style');
	}

	function openNextPopup($post_IDs) {
		var $next_popup_ID = $post_IDs[0],
			$next_popup = $('#glozin_popup_' + $next_popup_ID);
			if( ! $next_popup.length ) {
				return;
			}

		var options = $next_popup.data('options'),
			visible = options.visiblle,
			seconds = options.seconds,
			seconds = Math.max( seconds, 0 );
			seconds = 'delayed' === visible ? seconds : 0;
			setTimeout( function() {
				if ( $next_popup.hasClass('glozin-popup-type--slide') ) {
					$next_popup.addClass('offscreen-panel--open');
					$next_popup.find('.panel__backdrop').fadeIn();
					$( document.body ).addClass( 'offcanvas-opened' );
				} else {
					$next_popup.addClass('modal--opening').fadeIn(400, function() {
						$( this ).removeClass( 'modal--opening' );
					}).addClass('modal--open');
					$( document.body ).addClass( 'modal-opened' );
				}

				var widthScrollBar = window.innerWidth - $('#page').width();
				if( $('#page').width() < 767 ) {
					widthScrollBar = 0;
				}

				$(document.body).css({'padding-inline-end': widthScrollBar, 'overflow': 'hidden'});
			}, seconds * 1000 );
	}

	$(function() {
		if ( ! $('.glozin-popup').length ) {
			return;
		}
		var $post_IDs = [],
			$post_exit_IDs = [];
		$('.glozin-popup').each(function() {
			var $this = $(this),
				options = $this.data('options'),
				post_ID = options.post_ID,
				visible = options.visiblle,
				frequency = options.frequency,
				seconds = options.seconds;
				seconds = Math.max( seconds, 0 );
				seconds = 'delayed' === visible ? seconds : 0;

				$cookie_name = 'glozin_popup_' + post_ID;
				if (frequency > 0 && document.cookie.match(new RegExp('(^|;\\s*)' + $cookie_name + '=([^;]*)'))) {
					return;
				}

				if( 'exit' === visible  ) {
					$post_exit_IDs.push( post_ID );
					return;
				}

				if( ! $post_IDs.length ) {
					$( window ).on( 'load', function() {
						setTimeout( function() {
							if ( $this.hasClass('glozin-popup-type--slide') ) {
								$this.addClass('offscreen-panel--open');
								$this.find('.panel__backdrop').fadeIn();
								$( document.body ).addClass( 'offcanvas-opened' );
							} else {
								$this.fadeIn().addClass('modal--open');
								$( document.body ).addClass( 'modal-opened' );
							}

							var widthScrollBar = window.innerWidth - $('#page').width();
							if( $('#page').width() < 767 ) {
								widthScrollBar = 0;
							}

							$(document.body).css({'padding-inline-end': widthScrollBar, 'overflow': 'hidden'});
						}, seconds * 1000 );
					} );
				}


				$post_IDs.push(post_ID);

		});

		$('.glozin-popup:not(.glozin-popup-visible--exit)').on('click', '.glozin-popup__close, .glozin-popup__backdrop', function (e) {
			e.preventDefault();
			var $this = $(this),
				$popup = $this.closest('.glozin-popup'),
				options = $popup.data('options'),
				days = options.frequency;

			closePopup($popup, days);
			$post_IDs.shift();
			if( $post_IDs.length ) {
				openNextPopup($post_IDs);
			}

		});

		$( document.body ).on('click', '.glozin-dismiss-popup-button', function (e) {
			e.preventDefault();
			var $popup = $(this).closest('.glozin-popup'),
				options = $(this).closest('.elementor-widget-glozin-dismiss-popup-button').data('settings'),
				days = options.dismiss_button_frequency;

			closePopup($popup, days);
			$post_IDs.shift();
			if( $post_IDs.length ) {
				openNextPopup($post_IDs);
			}

		});

		document.addEventListener('mouseleave', function(event) {
			setTimeout(function() {
				if ($post_exit_IDs.length ) {
					openNextPopup($post_exit_IDs);
				}
			}, 500);

			$('.glozin-popup-visible--exit').on('click', '.glozin-popup__close, .glozin-popup__backdrop', function (e) {
				e.preventDefault();

				var $this = $(this),
					$popup = $this.closest('.glozin-popup'),
					options = $popup.data('options'),
					days = options.frequency;

				closePopup($popup, days);
				$post_exit_IDs.shift();
				if( $post_exit_IDs.length ) {
					openNextPopup($post_exit_IDs);
				}

			});
		});

	});
})(jQuery);