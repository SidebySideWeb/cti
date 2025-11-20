(function ($) {
	'use strict';

	var glozin = glozin || {};

	glozin.init = function () {
		glozin.$body   = $(document.body),
		glozin.$window = $(window),
		glozin.$header = $('#site-header');

		this.toggleOffCanvas();
		this.toggleModals();
		this.togglePopover();
		this.formFieldFocus();
		this.instantSearch();

		this.progressBar();

		this.lazyLoadVideo();

		// Login Popup
		this.loginPopup();
		this.loginModalAuthenticate();
		this.registerPopup();

		//Header
		this.currencyLanguage();
		this.headerCampaignBar();
		this.stickyHeader();
		this.headerProductCategories();

		// Swiper
		this.glozinSwiper();

		// Blog
		this.loadMorePosts();
		this.postFound();

		// Product Card
		this.lazyLoadImage();
		this.productAttribute();
		this.productQuantityNumber();
		this.productQuickView();
		this.productSaleMarquee();
		glozin.$body.on('glozin_products_filter_request_success glozin_get_products_ajax_loaded', function () {
			glozin.productSaleMarquee();
        });

		// Cart
		this.updateCartQtyAuto();

		// Single product
		this.productVariation();
		glozin.$body.on( 'glozin_single_product_variation_form_init', function(){
			glozin.productVariation();
		} );
		this.recentlyViewedProducts();
		this.productAffiliate();

		// Mini Cart
		this.openMiniCartPanel();
		this.updateMiniCartQtyAuto();
		this.updateMiniCartContent();
		this.productQuickEdit();
		this.removeItemFromMiniCart();
		this.applyCoupon();
		this.updateShippingAddress();

		// Product Notification
		this.addedToWishlistNotice();
		this.addedToCompareNotice();

		this.copyLink();

		// Back to top
		this.backToTop();

		this.toggleProductCategoriesWidget();
		this.dropdownProductCategoriesSidebar();

		// Tooltip
		this.tooltip();

		// Cart and Checkout: Order Comments
		this.orderComments();

		// Mobile Menu
		this.mobileMenu();
		this.hamburgerToggleMenuItem();
		this.hamburgerToggleMegaMenuItem();
		this.navigationBar();
	};

	/**
	 * Toggle off-screen panels
	 */
	glozin.toggleOffCanvas = function() {
		$( document.body ).on( 'click', '[data-toggle="off-canvas"]', function( event ) {
			var target = '#' + $( this ).data( 'target' );

			if ( $( target ).hasClass( 'offscreen-panel--open' ) ) {
				glozin.closeOffCanvas( target );
			} else if ( glozin.openOffCanvas( target ) ) {
				event.preventDefault();
			}

			if( target == '#filter-sidebar-panel' && $( 'body' ).hasClass( 'woocommerce-shop-elementor' ) ) {
				if( $( 'body' ).find( '.catalog-toolbar__filter-button' ).length > 0 ) {
					event.preventDefault();
					$($( 'body' ).find( '.catalog-toolbar__filter-button' ).get(0)).trigger( 'click' );
				}
			}

		} ).on( 'click', '.offscreen-panel .panel__button-close, .offscreen-panel .panel__backdrop, .offscreen-panel .sidebar__button-close, .offscreen-panel .sidebar__backdrop', function( event ) {
			event.preventDefault();

			glozin.closeOffCanvas( this );
		} ).on( 'keyup', function ( e ) {
			if ( e.keyCode === 27 ) {
				glozin.closeOffCanvas();
			}
		} );
	};

	/**
	 * Open off canvas panel.
	 * @param string target Target selector.
	 */
	glozin.openOffCanvas = function( target ) {
		var $target = $( target );

		if ( ! $target.length ) {
			if( ! $('.offscreen-panel[data-id="' + target.replace( '#', '') + '"]' ).length ) {
				return false;
			} else {
				$target = $('.offscreen-panel[data-id="' + target.replace( '#', '') + '"]' );
			}
		}

		var widthScrollBar = window.innerWidth - $('#page').width();
		if( $('#page').width() < 767 ) {
			widthScrollBar = 0;
		}
		$(document.body).css({'padding-right': widthScrollBar, 'overflow': 'hidden'});

		$target.find('.panel__backdrop, .sidebar__backdrop').fadeIn();
		$target.addClass( 'offscreen-panel--open' );

		var $dataId = $target.attr( 'id' ) ? $target.attr( 'id' ) : $target.data( 'id' );

		$( document.body ).addClass( 'offcanvas-opened ' + $dataId + '-opened' ).trigger( 'glozin_off_canvas_opened', [$target] );

		return true;
	}

	/**
	 * Close off canvas panel.
	 * @param DOM target
	 */
	glozin.closeOffCanvas = function( target ) {
		if ( !target ) {
			$( '.offscreen-panel' ).each( function() {
				var $panel = $( this ),
					$dataId = $panel.attr( 'id' ) ? $panel.attr( 'id' ) : $panel.data( 'id' );

				if ( ! $panel.hasClass( 'offscreen-panel--open' ) ) {
					return;
				}

				if( $panel.find('.glozin-mini-products-recommended').length ) {
					$panel.find('.glozin-mini-products-recommended').removeClass('mini--open');
					setTimeout(function() {
						$panel.removeClass( 'offscreen-panel--open' );
						$panel.find('.panel__backdrop, .sidebar__backdrop').fadeOut();
					}, 600);
				} else {
					$panel.removeClass( 'offscreen-panel--open' );
					$panel.find('.panel__backdrop, .sidebar__backdrop').fadeOut();
				}

				$( document.body ).removeClass( $dataId + '-opened' );

				if( $panel.hasClass( 'modal-above-panel' ) ) {
					$panel.removeClass( 'modal-above-panel' );
				}

				if( $panel.hasClass( 'modal-above-panel__quickadd' ) ) {
					$panel.removeClass( 'modal-above-panel__quickadd' );
				}
			} );
		} else {
			target = $( target ).closest( '.offscreen-panel' );
			var $dataId = target.attr( 'id' ) ? target.attr( 'id' ) : target.data( 'id' );
			if( target.find('.glozin-mini-products-recommended').length && target.find('.glozin-mini-products-recommended').hasClass('mini--open') ) {
				target.find('.glozin-mini-products-recommended').removeClass('mini--open');
				setTimeout(function() {
					target.removeClass( 'offscreen-panel--open' );
					target.find('.panel__backdrop, .sidebar__backdrop').fadeOut();
				}, 600);
			} else {
				target.removeClass( 'offscreen-panel--open' );
				target.find('.panel__backdrop, .sidebar__backdrop').fadeOut();
			}

			$( document.body ).removeClass( $dataId + '-opened' );

			if( target.hasClass( 'modal-above-panel' ) ) {
				target.removeClass( 'modal-above-panel' );
			}

			if( target.hasClass( 'modal-above-panel__quickadd' ) ) {
				target.removeClass( 'modal-above-panel__quickadd' );
			}
		}

		const body = document.body;
		const navHeight = body.style.getPropertyValue('--gz-navigation-bar-height');

		$(document.body).removeAttr('style');

		if (navHeight) {
			body.style.setProperty('--gz-navigation-bar-height', navHeight);
		}

		$( document.body ).removeClass( 'offcanvas-opened' ).trigger( 'glozin_off_canvas_closed', [target] );
	}

	/**
	 * Toggle modals.
	 */
	glozin.toggleModals = function() {
		$( document.body ).on( 'click', '[data-toggle="modal"]', function( event ) {
			if( $( this ).data( 'modal' ) == 'no' ) {
				return;
			}

			var target = '#' + $( this ).data( 'target' );

			if ( $( target ).hasClass( 'modal--open' ) ) {
				if( $(this).is( 'form.gz-instant-search__form' ) ) {
					return;
				}

				if( $(this).closest('body').hasClass('offcanvas-opened') ) {
					glozin.closeModal( target, false );
				} else {
					glozin.closeModal( target );
				}
			} else if ( glozin.openModal( target ) ) {
				event.preventDefault();
			}
		} ).on( 'click', '.modal .modal__button-close, .modal .modal__backdrop', function( event ) {
			event.preventDefault();

			if( $(this).closest('body').hasClass('offcanvas-opened') ) {
				glozin.closeModal( this, false );
			} else {
				glozin.closeModal( this );
			}
		} ).on( 'keyup', function ( e ) {
			if ( e.keyCode === 27 ) {
				glozin.closeModal();
			}
		} );
	};

	/**
	 * Open a modal.
	 *
	 * @param string target
	 */
	glozin.openModal = function( target ) {
		var $target = $( target );
		$target = $target.length ? $target : $('.modal[data-id="' + target + '"]' );
		if ( !$target.length ) {
			var target = target.replace( '#', '');
			$target = $('.modal[data-id="' + target + '"]' );
		}

		if ( !$target.length ) {
			return false;
		}

		if( $target.hasClass('modal-to-form' ) ) {
			$(document.body).addClass('search-modal-form');
		}

		var $pageWidth = $('body').hasClass('glozin-header-sidebar-categories-enable') ? $('#page').width() + parseFloat($('body').css('padding-inline-start')) : $('#page').width();
		var widthScrollBar = window.innerWidth - parseFloat($pageWidth);

		if ( ( $("html").attr("dir") === "rtl" || $("body").css("direction") === "rtl" ) && $('body').hasClass('glozin-header-sidebar-categories-enable')) {
			widthScrollBar = window.innerWidth - parseFloat($pageWidth) + parseFloat($('body').css('padding-inline-start'));
		}

		if( $pageWidth < 767 ) {
			widthScrollBar = 0;
		}
		$(document.body).css({'padding-right': widthScrollBar, 'overflow': 'hidden'});

		$target.addClass('modal--opening');
		$target.fadeIn(1000, function() {
			$( this ).removeClass( 'modal--opening' );
		});
		$target.addClass( 'modal--open' );

		$( document.body ).addClass( 'modal-opened ' + $target.attr( 'id' ) + '-opened' ).trigger( 'glozin_modal_opened', [$target] );

		return true;
	}

	/**
	 * Close a modal.
	 *
	 * @param string target
	 */
	glozin.closeModal = function( target, removePadding = true, speed = 400 ) {
		if ( !target ) {
			$( '.modal' ).removeClass( 'modal--open' ).addClass( 'modal--closing' ).fadeOut(speed, function() {
				$( this ).removeClass( 'modal--closing' );
			});

			$( '.modal' ).each( function() {
				var $modal = $( this );

				if ( ! $modal.hasClass( 'modal--open' ) ) {
					return;
				}

				$modal.removeClass( 'modal--open' ).addClass( 'modal--closing' ).fadeOut(speed, function() {
					$( this ).removeClass( 'modal--closing' );
				});
				$( document.body ).removeClass( $modal.attr( 'id' ) + '-opened' );
			} );
		} else {
			target = $( target ).closest( '.modal' );
			target.removeClass( 'modal--open' ).addClass( 'modal--closing' ).fadeOut(speed, function() {
				$( this ).removeClass( 'modal--closing' );
			});

			$( document.body ).removeClass( target.attr( 'id' ) + '-opened' );
		}

		$( document.body ).removeClass('search-modal-form');


		if( ! $(target).parents('.modal').length ) {
			if( removePadding ) {
				const body = document.body;

				const navHeight = body.style.getPropertyValue('--gz-navigation-bar-height');

				$(document.body).removeAttr('style');

				if (navHeight) {
					body.style.setProperty('--gz-navigation-bar-height', navHeight);
				}
			}
			$( document.body ).removeClass( 'modal-opened' ).trigger( 'glozin_modal_closed', [target] );
		}

	}

	/**
	 * Toggle modals.
	 */
	glozin.togglePopover = function() {
		$( document.body ).on( 'click', '[data-toggle="popover"]', function( event ) {
			var target = '#' + $( this ).data( 'target' );

			if( $( this ).data( 'device' ) == 'mobile' ) {
				if( glozin.$window.width() > 767 ) {
					return;
				}
			}

			if ( $( target ).hasClass( 'popover--open' ) ) {
				glozin.closePopover( target );
			} else if ( glozin.openPopover( target ) ) {
				event.preventDefault();
			}
		} ).on( 'click', '.popover .popover__button-close, .popover .popover__backdrop, [data-popover="close"]', function( event ) {
			event.preventDefault();

			glozin.closePopover( this );
		} ).on( 'keyup', function ( e ) {
			if ( e.keyCode === 27 ) {
				glozin.closePopover();
			}
		} );
	};

	/**
	 * Open a popover.
	 *
	 * @param string target
	 */
	glozin.openPopover = function( target ) {
		var $target = $( target );

		$target = $target.length ? $target : $('.popover[data-id="' + target + '"]' );
		if ( !$target.length ) {
			var target = target.replace( '#', '');
			$target = $('.popover[data-id="' + target + '"]' );
		}

		if ( !$target.length ) {
			return false;
		}

		var widthScrollBar = window.innerWidth - $('#page').width();
		if( $('#page').width() < 767 ) {
			widthScrollBar = 0;
		}

		if( $target.data('padding') !== false ) {
			$(document.body).css({'padding-right': widthScrollBar, 'overflow': 'hidden'});
		}

		$target.fadeIn();
		$target.addClass( 'popover--open' );

		$( document.body ).addClass( 'popover-opened ' + $target.attr( 'id' ) + '-opened' ).trigger( 'glozin_popover_opened', [$target] );

		return true;
	}

	/**
	 * Close a popover.
	 *
	 * @param string target
	 */
	glozin.closePopover = function( target, removePadding = true ) {
		if ( !target ) {
			$( '.popover' ).removeClass( 'popover--open' ).fadeOut();

			$( '.popover' ).each( function() {
				var $popover = $( this );

				if ( ! $popover.hasClass( 'popover--open' ) ) {
					return;
				}

				$popover.removeClass( 'popover--open' );
				$( document.body ).removeClass( $popover.attr( 'id' ) + '-opened' );
			} );
		} else {
			target = $( target ).closest( '.popover' );
			target.removeClass( 'popover--open' );

			$( document.body ).removeClass( target.attr( 'id' ) + '-opened' );

			if (target.attr('data-padding') === 'false') {
				removePadding = false;
			}
		}

		if( removePadding ) {
			$(document.body).removeAttr('style');
		}

		$( document.body ).removeClass( 'popover-opened' ).trigger( 'glozin_popover_closed', [target] );
	}

	/**
     * Progressbar
     */
    glozin.progressBar = function () {
		let progressContainer = $("#gz-progress-container");
        let progressBar = $("#gz-progress-bar");

        function updateProgress(percent) {
            progressBar.css("width", percent + "%");
        }

        function startProgress() {
            progressContainer.show();
            updateProgress(10);

			setTimeout(() =>  updateProgress(70), 300);
        }

        function completeProgress() {
            updateProgress(100);
            setTimeout(() => progressContainer.hide(), 500);
        }

		$(document.body).on('glozin_progress_bar_start', function() {
			startProgress();
		});

		$(document.body).on('glozin_progress_bar_update', function(event, percent) {
			updateProgress(percent);
		});

		$(document.body).on('glozin_progress_bar_complete', function() {
			completeProgress();
		});

    }

	/**
	 * Lazy Load Video
	 * Converts all video src to data-src and loads them when scrolled into view
	 */
	glozin.lazyLoadVideo = function() {
		// Run once on page load to convert all video src to data-src
		convertVideoSrcToDataSrc();

		// Run the lazy loading function
		lazyLoadVideo();

		// Re-run when new content is loaded
		glozin.$body.on('glozin_get_products_ajax_loaded glozin_products_filter_request_success', function() {
			convertVideoSrcToDataSrc();
			lazyLoadVideo();
		});

		// Function to convert all video src attributes to data-src
		function convertVideoSrcToDataSrc() {
			// Find all videos with src attribute but without data-src and not already processed
			var regularVideos = document.querySelectorAll('video[src]:not([data-src]):not(.gz-lazy-video-processed)');

			regularVideos.forEach(function(video) {
				// Store the original src in data-src
				if (video.src && video.src !== '') {
					video.dataset.src = video.src;
					video.removeAttribute('src');
				}

				// Handle poster attribute if present
				if (video.poster && video.poster !== '') {
					video.dataset.poster = video.poster;
					video.removeAttribute('poster');
				}

				// Handle source elements if present
				var sources = video.querySelectorAll('source[src]');
				sources.forEach(function(source) {
					source.dataset.src = source.src;
					source.removeAttribute('src');
				});

				// Add class for lazy loading
				video.classList.add('gz-lazy-video');

				// Mark as processed to avoid double-processing
				video.classList.add('gz-lazy-video-processed');
			});
		}

		function lazyLoadVideo() {
			// Look for videos with data-src attribute or with the gz-lazy-video class
			var videos = document.querySelectorAll('video[data-src], video.gz-lazy-video');

			if (!videos.length) return;

			// Set up Intersection Observer with a margin to preload before fully visible
			var options = {
				root: null,
				rootMargin: '200px', // Preload videos when they're 200px from entering the viewport
				threshold: 0.1
			};

			var observer = new IntersectionObserver(function(entries, observer) {
				entries.forEach(function(entry) {
					if (entry.isIntersecting) {
						var video = entry.target;

						// Add a loading class
						video.classList.add('gz-video-loading');

						// Set the src attribute from data-src
						if (video.dataset.src) {
							video.src = video.dataset.src;
							video.removeAttribute('data-src');
						}

						// Handle poster if present
						if (video.dataset.poster) {
							video.poster = video.dataset.poster;
							video.removeAttribute('data-poster');
						}

						// Handle source elements if present
						var sources = video.querySelectorAll('source[data-src]');
						sources.forEach(function(source) {
							source.src = source.dataset.src;
							source.removeAttribute('data-src');
						});

						// Set preload attribute to auto to ensure video is ready
						video.preload = 'auto';

						// Load the video
						video.load();

						// Listen for the loadeddata event to know when video is ready
						video.addEventListener('loadeddata', function onVideoLoaded() {
							// Remove loading class and add loaded class
							video.classList.remove('gz-video-loading');
							video.classList.add('gz-video-loaded');

							// Play if autoplay is set
							if (video.hasAttribute('autoplay')) {
								video.play().catch(function(error) {
									console.log('Autoplay prevented:', error);
								});
							}

							// Trigger a custom event
							glozin.$body.trigger('glozin_lazy_load_video_loaded', [video]);

							// Remove this event listener since we only need it once
							video.removeEventListener('loadeddata', onVideoLoaded);
						});

						// Stop observing this video
						observer.unobserve(video);
					}
				});
			}, options);

			// Start observing each video
			videos.forEach(function(video) {
				observer.observe(video);
			});
		}
	}

	glozin.glozinSwiper = function() {
		var $els = $('.glozin-swiper');
		if (!$els.length) return;

		$els.each(function() {
			if( $(this).get(0).swiper ) {
				return;
			}

			var $element = $(this);
			var data = $element.data('swiper');

			var $breakpointDesktop = data && data.breakpoints && data.breakpoints.desktop > 1025 ? data.breakpoints.desktop : 1025;

			if( $element.hasClass( 'glozin-product-carousel') ) {
				$element.find('ul.products').addClass('swiper-wrapper');
				$element.find('li.product').addClass('swiper-slide');
			}

			// Default Swiper options
			var swiper_options = {
				loop: data && data.loop || false,
				autoplay: data && data.autoplay || false,
				speed: data && data.speed || 800,
				watchOverflow: true,
				navigation: {
					nextEl: $element.find('.swiper-button-next').get(0),
					prevEl: $element.find('.swiper-button-prev').get(0),
				},
				pagination: {
					el: $element.find('.swiper-pagination').get(0),
					type: 'bullets',
					clickable: true
				},
				spaceBetween: data && data.spaceBetween && parseInt(data.spaceBetween.desktop) || 30,
				breakpoints: {
					0: {
						slidesPerView: data && data.slidesPerView && parseInt(data.slidesPerView.mobile) || 1,
						slidesPerGroup: data && data.slidesPerGroup && parseInt(data.slidesPerGroup.mobile) || 1,
						spaceBetween: data && data.spaceBetween && parseInt(data.spaceBetween.mobile) || 15,
					},
					768: {
						slidesPerView: data && data.slidesPerView && parseInt(data.slidesPerView.tablet) || 2,
						slidesPerGroup: data && data.slidesPerGroup && parseInt(data.slidesPerGroup.tablet) || 1,
						spaceBetween: data && data.spaceBetween && parseInt(data.spaceBetween.tablet) || 30,
					},
					[ $breakpointDesktop ]: {
						slidesPerView: data && data.slidesPerView && parseInt(data.slidesPerView.desktop) || 3,
						slidesPerGroup: data && data.slidesPerGroup && parseInt(data.slidesPerGroup.desktop) || 1,
						spaceBetween: data && data.spaceBetween && parseInt(data.spaceBetween.desktop) || 30,
					}
				},
				on: {
					init: initSwiper,
					slideChangeTransitionEnd: updateArrowPosition,
				}
			};

			if( data && data.slidesPerViewAuto ) {
				if( data.slidesPerViewAuto.mobile ) {
					swiper_options.breakpoints[0].slidesPerView = 1.5;
				}
			}

			// Initialize Swiper
			new Swiper($element.get(0), swiper_options);
		});

		function initSwiper() {
			$els.find('ul.products').attr('role', 'list');
			$els.find('li.product').attr('role', 'listitem');
			updateArrowPosition();
		}

		function updateArrowPosition() {
			if ( ! $els.hasClass('gz-arrows-middle') ) {
				return;
			}

			$els.find(".swiper-button-next, .swiper-button-prev").each(function () {
				let slide = $(this).closest(".gz-arrows-middle").find(".gz-arrows-middle__image, .product-thumbnail .woocommerce-loop-product__link img");
				if (slide.length) {
					let imageHeight = slide.height();
					let topPosition = imageHeight / 2;

					$(this).get(0).style.setProperty('--gz-arrow-top', `${topPosition}px`);
				}
			});
		}
	};

	/**
	 * Ajax load more posts.
	 */
	glozin.loadMorePosts = function() {
		// Infinite scroll.
		if ( $( '.glozin-pagination--blog' ).hasClass( 'glozin-pagination--infinite' ) ) {
			var waiting = false,
				endScrollHandle;

			$(window).on( 'scroll', function() {
				if ( waiting ) {
					return;
				}

				waiting = true;

				clearTimeout( endScrollHandle );

				infiniteScoll();

				setTimeout( function() {
					waiting = false;
				}, 100 );

				endScrollHandle = setTimeout( function() {
					waiting = false;
					infiniteScoll();
				}, 200 );
			});

		}

		function infiniteScoll() {
			var $navigation = $( '.glozin-pagination--blog.glozin-pagination--ajax' ),
				$button = $( 'a', $navigation );

			if ( glozinIsVisible( $button ) && $button.length && ! $button.hasClass( 'loading' ) ) {
                $button.addClass( 'loading' );

				loadPosts( $button );
			}
		}

		// Load More
		$( document.body ).on( 'click', '.glozin-pagination--blog.glozin-pagination--loadmore a', function( event ) {
			event.preventDefault();

			if ( $(this).hasClass( 'loading' ) ) {
				return;
			}

			$(this).addClass( 'loading' );

			loadPosts( $(this) );
		});

		function loadPosts( $button ) {
			var $posts = $button.closest('#primary').find('#main'),
				currentPosts = $posts.children('.hentry').length,
				$navigation = $button.closest( '.glozin-pagination' ),
				url = $button.attr( 'href' ),
				$found = $('.gz-posts-found');

				$(document.body).trigger('glozin_progress_bar_start');
			$.get( url, function( response ) {
				var $content = $( '#main', response ),
					$posts = $( '.hentry', $content ),
					numberPosts = $posts.length + currentPosts,
					$nav = $( '.next-posts-pagination', $content.parent() );

				$posts.addClass( 'gz-fadeinup gz-animated' );

				let delay = 0.5;
				$posts.each( function( index, post ) {
					$( post ).css( '--gz-fadeinup-delay', delay + 's' );
					delay = delay + 0.1;
				} );

				// Check if posts are wrapped or not.
				if ( $navigation.siblings( '#main' ).length ) {
					$posts.appendTo( $navigation.siblings( '#main' ) );
				} else {
					$posts.insertBefore( $found );
				}



				if ( $nav.length ) {
					$button.replaceWith( $( 'a', $nav ) );
				} else {
					$button.fadeOut();
				}

				if( $posts.hasClass( 'gz-animated' ) ) {
					setTimeout( function() {
						$posts.removeClass( 'gz-animated' );
					}, 10 );
				}

				$button.removeClass( 'loading' );

				$found.find('.current-post').html(' ' + numberPosts);

				glozin.postFound();
				$(document.body).trigger('glozin_progress_bar_complete');

				if ( glozinData.blog_nav_ajax_url_change ) {
					window.history.pushState( null, '', url );
				}
			});
		}
	};

	glozin.postFound = function (el) {
		var $found = $('.gz-posts-found__inner'),
			$foundEls = $found.find('.count-bar'),
			$current = $found.find('.current-post').html(),
			$total = $found.find('.found-post').html(),
			pecent = ($current / $total) * 100;

		$foundEls.css('width', pecent + '%');
	}

	// Lazy Load Image
	glozin.lazyLoadImage = function () {
		lazyLoadImage();
		glozin.$body.on( 'glozin_get_products_ajax_loaded glozin_products_filter_request_success added_to_cart wc_fragments_refreshed wc_fragments_loaded', function() {
			lazyLoadImage();
		});

		function lazyLoadImage() {
			var $lazyLoadImage = glozin.$body.find('.gz-lazy-load-image'),
			$parent = $lazyLoadImage.parent();

			$parent.each(function() {
				$(this).imagesLoaded( function () {
					$parent.closest( '.gz-lazy-load' ).removeClass( 'gz-lazy-load' );
					$parent.trigger( 'glozin_lazy_load_image_loaded' );
				});
			});
		}
	}

	// Product Attribute
	glozin.productAttribute = function () {
		glozin.$body.on( 'click', '.product-variation-item', function (e) {
            e.preventDefault();
			if( $(this).hasClass('selected') ) {
				return;
			}

			if( ! $(this).closest('.product-variation-items').hasClass('gz-variation-hover') ) {
				return;
			}

			var $thumbnails = $(this).closest('.product-inner').find('.product-thumbnail');

			$(this).siblings('.product-variation-item').removeClass('selected');
            $(this).addClass('selected');
            var variations= $(this).data('product_variations'),
                $mainImages = $thumbnails.find('.woocommerce-LoopProduct-link').first(),
                $image = $mainImages.find('img').first();

            $mainImages.addClass('image-loading');

			if ( variations && variations.img_src !== 'undefined' && variations.img_src ) {
            	$image.attr('src', variations.img_src);
				$mainImages.closest('.product-thumbnail').find('.product-video-loop-thumbnail').addClass('hidden');
			}
            if ( variations && variations.img_srcset !== 'undefined' && variations.img_srcset ) {
                $image.attr('srcset', variations.img_srcset);
            }
            if ( variations && variations.img_original !== 'undefined' && variations.img_original ) {
                $image.closest('a' ).attr('data-zoom', variations.img_original);
                $image.attr('data-zoom', variations.img_original);
            }

            $image.load(function () {
                $mainImages.removeClass('image-loading');
            });

        });

		glozin.$body.on('mouseover', '.product-variation-items', function (e) {
            e.preventDefault();
            $(this).closest('.product-inner').find('.product-thumbnail').addClass('hover-swatch');
        }).on('mouseout', '.product-variation-items', function (e) {
            e.preventDefault();
			$(this).closest('.product-inner').find('.product-thumbnail').removeClass('hover-swatch');
        });
    };

	glozin.instantSearch = function() {
		var $selector = $('#search-modal');

		if( $selector.hasClass( 'search-type-sidebar' ) ) {
			setTimeout(function() {
				$selector.find( '.swiper' ).each( function() {
					if( $(this).length > 0 ) {
						if( $(this).get(0).swiper ) {
							$(this).get(0).swiper.destroy();
						}

						$(this).addClass( 'destroy-swiper' );
					}
				});
			}, 1);

			glozin.$body.on( 'glozin_ajax_search_request_success', function() {
				glozin.lazyLoadImage();
				$selector.find( '.swiper' ).addClass( 'destroy-swiper' );
			});
		}

		if( $selector.hasClass('search-type-popup') ) {
			if( $selector.hasClass('modal-to-form') ) {
				glozin.$body.on('click', function(event) {
					if( glozin.$window.width() < 1200 ) {
						return;
					}

					if ( $( event.target ).hasClass('search-modal') || $( event.target ).closest('.search-modal' ).length || $( event.target ).data('target' ) == 'search-modal' || $( event.target ).closest('[data-target="search-modal"]' ).length ) {
						return;
					}

					if( $selector.hasClass('modal--open') ) {
						glozin.closeModal( $selector );
					}
				});
			}

			setTimeout(function() {
				if ( glozin.$window.width() < 1200 ) {
					$selector.find( '.swiper' ).each( function() {
						if( $(this).length > 0 && $(this).get(0).swiper ) {
							$(this).get(0).swiper.destroy();
							$(this).addClass( 'destroy-swiper' );
						}
					});
				}
			}, 1);

			glozin.$body.on( 'glozin_ajax_search_request_success', function() {
				glozin.lazyLoadImage();

				if ( glozin.$window.width() > 1199 ) {
					glozin.glozinSwiper();
				} else {
					$selector.find( '.swiper' ).addClass( 'destroy-swiper' );
				}
			});

			glozin.$window.on('resize', function () {
				if ( glozin.$window.width() < 1200 ) {
					$selector.find( '.swiper' ).each( function() {
						if( $(this).length > 0 ) {
							if( $(this).get(0).swiper ) {
								$(this).get(0).swiper.destroy();
							}

							$(this).addClass( 'destroy-swiper' );
						}
					});
				} else {
					var hasSwiper = false;
					$selector.find( '.swiper' ).each( function() {
						if( $(this).hasClass( 'destroy-swiper' ) ) {
							$(this).removeClass( 'destroy-swiper' );
							hasSwiper = true;
						}
					});

					if ( hasSwiper ) {
						glozin.glozinSwiper();
					}
				}
			}).trigger('resize');
		}

		$( document.body ).on( 'click', '[data-target="search-modal"]', function() {
			if ( glozin.$window.width() > 1199 ) {
				if( $selector.hasClass('search-type-popup') && $selector.hasClass('modal-to-form') ) {
					var top = 0,
						headerDesktop = document.querySelector('.site-header__desktop'),
						headerMain = document.querySelector('.header-main'),
						headerBottom = document.querySelector('.header-bottom');

					top = headerDesktop.getBoundingClientRect().top + headerDesktop.offsetHeight;

					if( $('.site-header__desktop').hasClass('minimized') ) {
						if( $('.site-header__desktop').find('.header-main').hasClass('header-sticky') ) {
							top = headerMain.getBoundingClientRect().top + headerMain.offsetHeight;
						}

						if( $('.site-header__desktop').find('.header-bottom').hasClass('header-sticky') ) {
							top = headerBottom.getBoundingClientRect().top + headerBottom.offsetHeight;
						}
					}

					$selector.css('--gz-modal-top-spacing', top + 'px');
				}
			}
		});

		$(document.body).on( 'glozin_modal_opened', function() {
			$selector.find(".swiper-button-next, .swiper-button-prev").each(function () {
				let slide = $(this).closest(".gz-arrows-middle").find(".gz-arrows-middle__image, .product-thumbnail .woocommerce-loop-product__link img");
				if (slide.length) {
					let imageHeight = slide.height();
					let topPosition = imageHeight / 2;

					$(this).get(0).style.setProperty('--gz-arrow-top', `${topPosition}px`);
				}
			});
		});
	};

	/**
	 * Add class to .form-row when inputs are focused.
	 */
	glozin.formFieldFocus = function() {
		if (!$(".woocommerce-account, .wpcf7-form, .wapf-field-container, .cart-panel").length) {
			return;
		}

		$('.woocommerce-account, .wpcf7-form, .wapf-field-container, .cart-panel')
		.on('keyup focus change', '.woocommerce-form-row .input-text, .wpcf7-form-control, .wapf-input', function() {
			$(this).closest('.woocommerce-form-row, .wpcf7-form-row, .wapf-field-container').addClass('focused');
		})
		.on('blur', '.woocommerce-form-row .input-text, .wpcf7-form-control, .wapf-input', function() {
			if ($(this).val() === '') {
				$(this).closest('.woocommerce-form-row, .wpcf7-form-row, .wapf-field-container').removeClass('focused');
			}
		})
		.find('.woocommerce-form-row, .wpcf7-form-row, .wapf-field-container').each(function() {
			var $input = $(this).find('.input-text, .wpcf7-form-control, .wapf-input');
			if ($input.val() !== '') {
				$(this).addClass('focused');
			}

			$input.on('animationstart', function(e) {
				if (e.originalEvent.animationName === 'autofill-animation') {
					$input.closest('.woocommerce-form-row, .wpcf7-form-row, .wapf-field-container').addClass('focused');
				}
			});
		})
		.on('click', '.showlogin', function() {
			$(this).closest('.woocommerce').find('.gz-button-login-mode').trigger('click');
		});

		glozin.$window.on("load", function() {
			$('.woocommerce-account .woocommerce-form-row .input-text, .wpcf7-form .wpcf7-form-control, .wapf-field-container .wapf-input, .cart-panel .input-text').each(function() {
				if ($(this).val().length !== 0) {
					$(this).closest('.woocommerce-form-row, .wpcf7-form-row, .wapf-field-container, .cart-panel').addClass('focused');
				}
			});
		});
	};

	/**
	 * Login Popup
	 */
	glozin.loginPopup = function() {
		var $modal = $( '#login-modal' );

		$modal
		.on( 'click', '.gz-button-register-mode', function(e) {
			e.preventDefault();
			$modal.find('.woocommerce-customer-login').removeClass('active');
			$modal.find('.woocommerce-customer-register').addClass('active');
			$modal.find('.login-modal-notices').remove();
		} )
		.on( 'click', '.gz-button-login-mode', function(e) {
			e.preventDefault();
			$modal.find('.woocommerce-customer-login').addClass('active');
			$modal.find('.woocommerce-customer-register').removeClass('active');
		} );

	}

	/**
	 * Ajax login before refresh page
	 */
	glozin.loginModalAuthenticate = function () {
		var $modal = $( '#login-modal' ),
			xhr = null;
		$modal.on( 'submit', 'form.login', function authenticate( event ) {
			var remember = $( 'input[name=rememberme]', this ).is( ':checked' ),
				nonce = $( 'input[name=woocommerce-login-nonce]', this ).val();

			var formData = {
				action: 'login_modal_authenticate',
				security: nonce,
				remember: remember
			};

			getLoginAJAX( this, formData, event );
		});

		$modal.on( 'submit', 'form.register', function authenticate( event ) {
			var nonce = $( 'input[name=woocommerce-register-nonce]', this ).val();

			var formData = {
				action: 'register_modal_authenticate',
				security: nonce,
			};

			getLoginAJAX( this, formData, event );
		});

		function getLoginAJAX( form, formData, event ) {
			var username = $( 'input[name=username]', form ).val(),
				password = $( 'input[name=password]', form ).val(),
				email = $( 'input[name=email]', form ).val(),
				$button = $( '[type=submit]', form ),
				$form = $( form );

			if ($form.find('input[name=username]').length) {
				if( !username ) {
					$form.find('input[name=username]').focus();
					return false;
				}

			}

			if ($form.find('input[name=email]').length) {
				if( !email ) {
					$form.find('input[name=email]').focus();
					return false;
				}
			}

			if ($form.find('input[name=password]').length) {
				if( ! password ) {
					$form.find('input[name=password]').focus();
					return false;
				}

			}
			if ( $form.data( 'validated' ) ) {
				return true;
			}

			if (xhr) {
				xhr.abort();
			}
			var newformData = $form.serializeArray();
			newformData.forEach(function (item) {
				formData[item.name] = item.value;
			});
			$modal.find('.login-modal-notices').remove();
			$button.addClass('loading');
			$(document.body).trigger('glozin_progress_bar_start');
			xhr = $.post(
				glozinData.admin_ajax_url,
				formData,
				function (response) {
					if ( ! response.success ) {
						var $notice = '<div class="login-modal-notices woocommerce-error">' + response.data + '</div>';
						$modal.find('.modal__content').append( $notice );
						$button.removeClass('loading');
					} else {
						var $notice = '<div class="login-modal-notices woocommerce-info">' + response.data + '</div>';
						$modal.find('.modal__content').append( $notice );
						$button.removeClass('loading');
						setTimeout( function() {
							$form.data( 'validated', true ).trigger('submit');
						}, 1500 );
					}
					$(document.body).trigger('glozin_progress_bar_complete');
				}

			);

			event.preventDefault();
		};
	};

	/**
	 * Change product quantity
	 */
	glozin.registerPopup = function () {
		$('#header-account-register').on('click', function(e) {
			e.preventDefault();
			var $modal = $('#login-modal');

			$modal.find('.woocommerce-customer-login').removeClass('active');
			$modal.find('.woocommerce-customer-register').addClass('active');
		});

		$(document).on('click', function(e) {
			var $modal = $('#login-modal');

			if (!$modal.is(e.target) && $modal.has(e.target).length === 0 && !$('#header-account-register').is(e.target)) {
				$modal.find('.woocommerce-customer-register').removeClass('active');
				$modal.find('.woocommerce-customer-login').addClass('active');
			}
		});
	}

	/**
	 * Change product quantity
	 */
	glozin.productQuantityNumber = function () {
		var debounceTimeout = null;
		glozin.$body.on('click', '.glozin-qty-button', function (e) {
			e.preventDefault();

			var $this = $(this),
				$qty = $this.siblings('.qty'),
				current = 0,
				min = parseFloat($qty.attr('min')),
				max = parseFloat($qty.attr('max')),
				step = parseFloat($qty.attr('step'));

			if ($qty.val() !== '') {
				current = parseFloat($qty.val());
			} else if ($qty.attr('placeholder') !== '') {
				current = parseFloat($qty.attr('placeholder'))
			}

			min = min ? min : 0;
			max = max ? max : current + 1;

			if ($this.hasClass('decrease') && current > min) {
				$qty.val(current - step);
			}
			if ($this.hasClass('increase') && current < max) {
				$qty.val(current + step);
			}

			if ( debounceTimeout ) {
				clearTimeout( debounceTimeout );
			}

			debounceTimeout = setTimeout( function() {
				$qty.trigger('change');
			}, 500 );
		});
	};

	/**
	 * Quick view modal.
	 */
	glozin.productQuickView = function() {
		$( document.body ).on( 'click', '.glozin-quickview-button', function( event ) {
			event.preventDefault();

			var $el = $( this ),
				product_id = $el.data( 'product_id' ),
				$target = $( '#' + $el.data( 'target' ) ),
				$container = $target.find( '.woocommerce' ),
				ajax_url = glozinData.ajax_url.toString().replace('%%endpoint%%', 'product_quick_view');

			$target.addClass( 'loading' );

			$el.addClass( 'loading' );
			$container.find( '.product-quickview' ).html( '' );
			$(document.body).trigger('glozin_progress_bar_start');
			$.post(
				ajax_url,
				{
					action    : 'glozin_get_product_quickview',
					product_id: product_id,
					security  : glozinData.product_quickview_nonce
				},
				function( response ) {
					$container.find( '.product-quickview' ).replaceWith( response.data );

					glozinMore( $( '.product-quickview' ).find( '.short-description__more' ) );

					if ( response.success ) {
						update_quickview();
					}

					$el.removeClass( 'loading' );

					$target.removeClass( 'loading' );
					if( ! $target.hasClass( 'modal--open' ) ) {
						glozin.openModal( $target );
					}

					if( $target.find( '.size-guide-button' ).length == 1 ) {
						$target.find( '.size-guide-button' ).attr( 'data-modal', 'no' );
					}

					glozin.$body.trigger( 'glozin_product_quick_view_loaded' );

					if ( $container.find('.glozin-countdown').length > 0) {
						$(document.body).trigger('glozin_countdown', [$('.glozin-countdown')]);
					}

					$(document.body).trigger('glozin_progress_bar_complete');
				}
			).fail( function() {
				window.location.herf = $el.attr( 'href' );
			} );

			/**
			 * Update quick view common elements.
			 */
			function update_quickview() {
				var $product = $container.find( '.product-quickview' ),
					$gallery = $product.find( '.woocommerce-product-gallery' ),
					$variations = $product.find( '.variations_form' );

				update_product_gallery();
				$gallery.on( 'glozin_update_product_gallery_on_quickview', function(){
					update_product_gallery();
				});

				// Variations form.
				if (typeof wc_add_to_cart_variation_params !== 'undefined') {

					$variations.each(function () {
						glozin.productVariation();
						$(this).wc_variation_form();
					});
				}

				$( document.body ).trigger( 'init_variation_swatches');
			}

			/**
			 * Update quick view common elements.
			 */
			function update_product_gallery() {
				var $product = $container.find( '.product-quickview' ),
					$gallery = $product.find( '.woocommerce-product-gallery' ),
					$slider = $gallery.find( '.woocommerce-product-gallery__wrapper' );

				// Prevent clicking on gallery image link.
				$gallery.on( 'click', '.woocommerce-product-gallery__image a', function( event ) {
					event.preventDefault();
				} );

				// Init swiper slider.
				if ( $slider.find( '.woocommerce-product-gallery__image' ).length > 1 ) {
					$slider.addClass('woocommerce-product-gallery__slider swiper slides-per-view-auto--mobile no-space-end');
					if( glozinData.mobile_single_product_gallery_arrows ) {
						$slider.closest('.woocommerce-product-gallery').addClass('woocommerce-product-gallery--has-arrows-mobile');
					}
					$slider.css({'--gz-swiper-auto-width-mobile': '64%', '--gz-swiper-auto-fluid-end-mobile': '10px'});
					$slider.attr('data-mobile', '1');
					$slider.wrapInner('<div class="swiper-wrapper"></div>');
					$slider.find('.swiper-wrapper').after('<span class="glozin-svg-icon glozin-svg-icon__inline glozin-svg-icon--icon-back swiper-button-prev swiper-button"><svg width="24" height="24" aria-hidden="true" role="img" focusable="false"> <use href="#icon-back" xlink:href="#icon-back"></use> </svg></span>');
					$slider.find('.swiper-wrapper').after('<span class="glozin-svg-icon glozin-svg-icon__inline glozin-svg-icon--icon-next swiper-button-next swiper-button"><svg width="24" height="24" aria-hidden="true" role="img" focusable="false"> <use href="#icon-next" xlink:href="#icon-next"></use> </svg></span>');
					$slider.find('.swiper-wrapper').after('<div class="swiper-fraction d-inline-flex d-none-md position-absolute bottom-15 end-15 pe-none z-2 py-8 px-17 border text-dark bg-light rounded-30 lh-1"></div>');
					$slider.find('.woocommerce-product-gallery__image').addClass('swiper-slide');

					var options = {
						loop: false,
						autoplay: false,
						speed: 800,
						watchOverflow: true,
						autoHeight: true,
						navigation: {
							nextEl: $slider.find('.swiper-button-next').get(0),
							prevEl: $slider.find('.swiper-button-prev').get(0),
						},
						pagination: {
							el: $slider.find('.swiper-fraction').get(0),
							type: "fraction",
							modifierClass: 'swiper-pagination--',
						},
						on: {
							init: function () {
								$gallery.css('opacity', 1);
								glozin.$body.trigger( 'glozin_product_gallery_quickview_init' );
							},
							slideChangeTransitionEnd: function () {
								glozin.$body.trigger( 'glozin_product_gallery_quickview_slideChangeTransitionEnd' );
							}
						},
						breakpoints: {
							0: {
								slidesPerView: 1.5,
								spaceBetween: 15,
							},
							768: {
								slidesPerView: 1,
								spaceBetween: 30,
							},
						},
					};

					new Swiper($slider.get(0), options);
				} else {
					$gallery.css( 'opacity', 1 );
				}
			}

			function glozinMore ( $selector ) {
				if ( $selector.length === 0 ) {
					return;
				}

				var $settings = $selector.data( 'settings' ),
					$more     = $settings.more,
					$less     = $settings.less;

				if( $selector.hasClass( 'less' ) ) {
					$selector.removeClass( 'less' );
					$selector.text( $more );
					$selector.siblings( '.short-description__content' ).css( '-webkit-line-clamp', '' );
				} else {
					$selector.addClass( 'less' );
					$selector.text( $less );
					$selector.siblings( '.short-description__content' ).css( '-webkit-line-clamp', 'inherit' );
				}
			}
		});
	}

	/**
	 * Product Sale Marquee
	 */
	glozin.productSaleMarquee = function () {
		var $selector = $( '.glozin-sale-flash-marquee' );

		if( ! $selector.length ) {
			return;
		}

		$selector.each(function () {
			var $this = $(this);

			if ($this.hasClass('gz-marquee-initialized')) {
				return;
			}

			$this.addClass('gz-marquee-initialized');
			$this.closest('li.product').addClass('glozin-sale-flash-marquee--enabled');

			var $inner = $this.find('.glozin-marquee__inner'),
				$items = $this.find('.glozin-marquee__items');

			$inner.imagesLoaded(function () {
				var item,
					amount = (parseInt(Math.ceil(jQuery(window).width() / $items.outerWidth(true))) || 0) + 1,
					dataSpeed = $this.data('speed'),
					speed = 1 / parseFloat(dataSpeed) * ($items.outerWidth(true) / 350);

				$inner.css('--gz-marquee-speed', speed + 's');

				for (let i = 1; i <= amount; i++) {
					item = $items.clone();
					item.addClass('glozin-marquee--duplicate');
					item.css('--gz-marquee-index', i.toString());
					item.attr('data-index', i);

					item.appendTo($inner);
				}

				const originalId = $inner.find('.glozin-marquee--original').data('id');

				$inner.find('.glozin-marquee--duplicate').each(function () {
					const $dup = $(this);
					if ($dup.data('id') !== originalId) {
						$dup.remove();
					}
				});
			});
		});
	}

	/**
	 * Open Mini Cart
	 */
	glozin.openMiniCartPanel = function () {
		if (typeof glozinData.added_to_cart_notice === 'undefined') {
			return;
		}

		if (glozinData.added_to_cart_notice.added_to_cart_notice_layout !== 'mini') {
			return;
		}

		var status = false;
		$(document.body).on('adding_to_cart', function () {
            status = true;
			$('#cart-panel').find('.glozin-mini-products-recommended').removeClass('mini--open');
		});

		$(document.body).on( 'added_to_cart wc_fragments_refreshed', function () {
			if( status ) {
				var $close_modal = false;
				if( $('#quick-view-modal').length && $('#quick-view-modal').hasClass('modal--open') ) {
					glozin.closeModal('#quick-view-modal', true, 800)
					$close_modal = true;
				}

				if( $close_modal ) {
					setTimeout(function() {
						glozin.openOffCanvas( '#cart-panel' );
					}, 800);
				} else {
					glozin.openOffCanvas( '#cart-panel' );
				}

				if( $('#cart-panel').find('.glozin-mini-products-recommended').length ) {
					setTimeout(function() {
						if($('#cart-panel').hasClass('offscreen-panel--open')) {
							$('#cart-panel').find('.glozin-mini-products-recommended').addClass('mini--open');
						}
					}, 1200);
				}

				glozin.glozinSwiper();
			}
		});

		$(document.body).on('click', '.products-recommended__button-close', function() {
			$(this).closest('.glozin-mini-products-recommended').removeClass('mini--open');
		});

		$( document.body ).on( 'click', '.remove_from_cart_button', function() {
			if( $(this).closest('.widget_shopping_cart_content').find('.glozin-mini-products-recommended').length ) {
				$(this).closest('.widget_shopping_cart_content').find('.glozin-mini-products-recommended').removeClass('mini--open');
			}

			glozin.glozinSwiper();
		});

		var $remove_from_cart = false;
		$( document.body ).on( 'removed_from_cart', function() {
			$remove_from_cart = true;
		});

		$( document.body ).on( 'wc_fragments_loaded wc_fragments_refreshed', function() {
			if( $('#cart-panel').find('.glozin-mini-products-recommended').length && $remove_from_cart ) {
				setTimeout(function() {
					$('#cart-panel').find('.glozin-mini-products-recommended').addClass('mini--open');
					$remove_from_cart = false;
				}, 500);
			}

			glozin.glozinSwiper();

			var $miniCartShippingCalculatorItems = $('#cart-panel').find('#mini-cart-shipping-calculator-items'),
				$miniCartShippingCalculatorPopover = $('#cart-panel').find('#mini-cart-shipping-calculator-popover');

			if( $miniCartShippingCalculatorItems.length && $miniCartShippingCalculatorPopover.length ) {
				$miniCartShippingCalculatorPopover.html($miniCartShippingCalculatorItems.html());
				$miniCartShippingCalculatorItems.remove();
			}
		});
	};

	glozin.updateCartQtyAuto = function() {
		$( document.body ).on( 'change', 'table.cart .qty', function() {
			if (typeof glozinData.update_cart_page_auto !== undefined && glozinData.update_cart_page_auto == '1') {
				glozin.$body.find('button[name="update_cart"]').attr( 'clicked', 'true' ).prop( 'disabled', false ).attr( 'aria-disabled', false );
				glozin.$body.find('button[name="update_cart"]').trigger('click');
			}
		} );

	}

	/**
	 * Product Variation
	 */
	glozin.productVariation = function () {
		var $productPrice 	 = $( 'div.product .entry-summary .glozin-product-price' ),
			$variation_form  = $( '.single-product div.product .entry-summary .variations_form' ),
			$description   	 = $( '.single-product div.product .entry-summary .short-description' ),
			$product_meta    = $( '.single-product div.product .entry-summary .product_meta' ),
			$badges_original = $( '.single-product div.product .entry-summary .woocommerce-badges--single' ),
			$meta_stock      = $product_meta.find( '.stock' ),
			$buttonATC       = $( '.single-product div.product .entry-summary .single_add_to_cart_button' ),
			variation_args   = [],
			$stickySelector = $( '#glozin-sticky-add-to-cart' );

		// Disable outofstock variation when product only has one attribute
		if( ! glozinData.product_clickable_outofstock_variations ) {
			$('.single-product div.product .entry-summary .variations_form:not(.product-select__variation)').on( 'wc_variation_form woocommerce_update_variation_values', function () {
				if( $variation_form.length > 0 && $variation_form.find( 'table.variations tbody .label' ).length == 1 ) {
					var dataProductVariations = $variation_form.data( 'product_variations' );
					if( dataProductVariations.length > 0 ) {
						for( var i = 0; i < dataProductVariations.length; i++ ) {
							if( ! dataProductVariations[i].is_in_stock ) {
								var value = Object.values(dataProductVariations[i].attributes) ? Object.values(dataProductVariations[i].attributes)[0] : null;
								if( value ) {
									if( ! $variation_form.find( 'li[data-value="' + value + '"]' ).hasClass( 'disabled' ) ) {
										variation_args.push( value );
									}
								}
							}
						}
					}
				}

				setTimeout( function() {
					variation_args.forEach( function( value ) {
						$variation_form.find( 'li[data-value="' + value + '"]' ).addClass( 'disabled' );
					} );
				}, 20 );
			});
		}

		$('.single-product div.product .entry-summary .variations_form:not(.product-select__variation)').on( 'found_variation', function (event, variation) {
			var $badges = $(this).closest( 'div.product' ).find( '.entry-summary .woocommerce-badges--single' );
			if( $badges.length > 0 ) {
				if( variation.badges_html ) {
					var $badges_html = $( decodeHtml( variation.badges_html ) );

					if( $badges_html.find('.onsale').length ) {
						if( $badges.find( '.onsale' ).length ) {
							if( $badges.find( '.onsale' ).hasClass( 'hidden' ) ) {
								$badges.find( '.onsale' ).removeClass( 'hidden' );
							}

							$badges.find( '.onsale' ).addClass( 'variation-badge' ).wc_set_content( $badges_html.find('.onsale').text() );
						} else {
							$badges.append( $badges_html.find('.onsale').clone() );
						}
					} else {
						if( $badges.find( '.onsale' ).length ) {
							$badges.find( '.onsale' ).addClass( 'hidden' );
						}
					}

					if( $badges_html.find('.stock-badge').length ) {
						if( $badges.find( '.stock-badge' ).length ) {
							if( $badges.find( '.variation-stock' ).length ) {
								$badges.find( '.variation-stock' ).remove();
							}

							if( ! $badges.find( '.stock-badge' ).hasClass( 'hidden' ) ) {
								$badges.find( '.stock-badge' ).addClass( 'hidden' );
							}

							$badges.find( '.stock-badge' ).after( $badges_html.find('.stock-badge').addClass( 'variation-stock' ).clone() );
						} else {
							$badges.append( $badges_html.find('.stock-badge').addClass( 'variation-stock' ).clone() );
						}
					}
				} else {
					$badges.empty();
				}
			} else {
				if( variation.badges_html ) {
					var $badges_html = $( decodeHtml( variation.badges_html ) );
					if( $badges_html.length ) {
						if( $(this).closest('div.product').find('.entry-summary .woocommerce-product-gallery').length ) {
							$(this).closest('div.product').find('.entry-summary .woocommerce-product-gallery').after( $badges_html.clone() );
						} else {
							$(this).closest( 'div.product' ).find('.entry-summary').first().prepend( $badges_html.clone() );
						}
					}
				}
			}

			if( $productPrice.length > 0 ) {
				if( $productPrice.find( '.variation-price' ).length > 0 ) {
					$productPrice.find( '.variation-price' ).remove();
				}

				if( variation.price_html ) {
					$productPrice.find( '.price' ).css( 'display', 'none' );
					$productPrice.append( '<div class="variation-price">' + variation.price_html + '</div>' );
				} else {
					$productPrice.find( '.price' ).css( 'display', '' );
				}
			} else {
				$productPrice = $(this).closest('.entry-summary').find('.glozin-product-price');
				if( $productPrice.length > 0 ) {
					if( $productPrice.find( '.variation-price' ).length > 0 ) {
						$productPrice.find( '.variation-price' ).remove();
					}

					if( variation.price_html ) {
						$productPrice.find( '.price' ).css( 'display', 'none' );
						$productPrice.append( '<div class="variation-price">' + variation.price_html + '</div>' );
					} else {
						$productPrice.find( '.price' ).css( 'display', '' );
					}
				}
			}
			var  $form = $(this).closest( '.variations_form' );
			glozin.variations_image_update(variation, $form);

			if( $description.length > 0 ) {
				if( $description.find( '.short-description__content-new' ).length > 0 ) {
					$description.find( '.short-description__content-new' ).remove();
				}

				if( variation.description ) {
					$description.find( '.short-description__content' ).css( 'display', 'none' );
					$description.find( '.short-description__content' ).after( '<div class="short-description__content short-description__content-new">' + variation.description + '</div>' );
					updateDescriptionButtonMore( $description, 'short-description__content-new' );
				} else {
					$description.find( '.short-description__content' ).css( 'display', '' );
				}
			}


			var $countdown = $(this).closest('div.product').find('.entry-summary .gz-countdown-single-product');
			if( $countdown.length ) {
				var $_countdown = $countdown.find('.glozin-countdown');
				if( variation.countdown_expire && $_countdown.attr('data-expire') !== variation.countdown_expire ) {
					$(window).get(0).clearCountdownInterval( $_countdown.get(0) );

					if( $countdown.hasClass( 'hidden' ) ) {
						$countdown.removeClass( 'hidden' );
					}

					$_countdown.attr('data-expire', variation.countdown_expire);
					$_countdown.empty();

					$_countdown.glozin_countdown();
				} else {
					$(window).get(0).clearCountdownInterval( $_countdown.get(0) );

					$_countdown.find('.glozin-countdown').empty();

					if( ! $countdown.hasClass( 'hidden' ) ) {
						$countdown.addClass( 'hidden' );
					}
				}
			}

			if( $meta_stock.length > 0 ) {
				if ( variation.availability_status ) {
					$meta_stock.wc_set_content( variation.availability_status );
				} else {
					$meta_stock.wc_reset_content();
				}
			}

			if( $buttonATC.length > 0 ) {
				if( variation.availability_status && ( ! variation.is_purchasable || ! variation.is_in_stock || ! variation.variation_is_visible || ( variation.is_pre_order !== undefined && variation.is_pre_order == 'yes' ) ) ) {
					$buttonATC.wc_set_content( variation.availability_status );
				} else {
					$buttonATC.wc_reset_content();
				}
			}
		});

		$('.single-product div.product .entry-summary .variations_form:not(.product-select__variation)').on( 'reset_data', function () {
			var $badges = $(this).closest( 'div.product' ).find( '.entry-summary .woocommerce-badges--single' );
			if( $badges.length > 0 ) {
				$badges.replaceWith( $badges_original.clone() );
			} else {
				if( $(this).closest('div.product').find('.entry-summary .woocommerce-product-gallery').length ) {
					$(this).closest('div.product').find('.entry-summary .woocommerce-product-gallery').after( $badges_original.clone() );
				} else {
					$(this).closest( 'div.product' ).find('.entry-summary').first().prepend( $badges_original.clone() );
				}
			}

			if( $productPrice.length > 0 && $productPrice.find( '.variation-price' ).length > 0 ) {
				$productPrice.find( '.variation-price' ).remove();
				$productPrice.find( '.price' ).css( 'display', '' );
			} else {
				$productPrice = $(this).closest('div.product').find('.entry-summary .glozin-product-price');
				if( $productPrice.length > 0 && $productPrice.find( '.variation-price' ).length > 0  ) {
					$productPrice.find( '.variation-price' ).remove();
					$productPrice.find( '.price' ).css( 'display', '' );
				}
			}

			if( $description.length > 0 && $description.find( '.short-description__content-new' ).length > 0 ) {
				$description.find( '.short-description__content-new' ).remove();
				$description.find( '.short-description__content' ).css( 'display', '' );
				updateDescriptionButtonMore( $description, 'short-description__content' );
			}

			var $_countdown = $(this).closest('div.product').find('.entry-summary .gz-countdown-single-product');
			if( $_countdown.length ) {
				var firstCountdown = $_countdown.find('.glozin-countdown').get(0);

				$(window).get(0).clearCountdownInterval(firstCountdown);

				$_countdown.find('.glozin-countdown').empty();

				if( ! $_countdown.hasClass( 'hidden' ) ) {
					$_countdown.addClass( 'hidden' );
				}
			}

			if( $meta_stock.length > 0 ) {
				$meta_stock.wc_reset_content();
			}

			if( $buttonATC.length > 0 ) {
				$buttonATC.wc_reset_content();
			}

			var $form = $(this).closest( '.variations_form' );
			glozin.variations_image_reset($form);
		});

		function updateDescriptionButtonMore( $selector, $class ) {
			$selector.find( '.' + $class ).each( function () {
				if( jQuery(this)[0].scrollHeight > jQuery(this)[0].clientHeight ) {
					jQuery(this).siblings( '.short-description__more' ).removeClass( 'hidden' );
				} else {
					if( ! jQuery(this).siblings( '.short-description__more' ).hasClass( 'hidden' ) ) {
						jQuery(this).siblings( '.short-description__more' ).addClass( 'hidden' );
					}
				}
			});
		}

		function decodeHtml( encodedStr ) {
			var textArea = document.createElement('textarea');
			textArea.innerHTML = encodedStr;
			return textArea.value;
		}
	}

	/**
	 * Sets product images for the chosen variation
	 */
	glozin.variations_image_update = function( variation, $form ) {
		var $product          = $form.closest( '.product' ),
			$product_gallery  = $product.find( '.woocommerce-product-gallery:not(.woocommerce-product-gallery__video)' ),
			$gallery_wrapper = $product_gallery.find( '.woocommerce-product-gallery__wrapper' ),
			$gallery_nav      = $product.find( '.glozin-product-gallery-thumbnails' );

		if ( variation && variation.image && variation.image.src && variation.image.src.length > 1 ) {
			// See if the gallery has an image with the same original src as the image we want to switch to.
			var galleryHasImage = $product_gallery.find( '.woocommerce-product-gallery__image[data-thumb="' + variation.image.gallery_thumbnail_src + '"]' ).length > 0;

			// If the gallery has the image, reset the images. We'll scroll to the correct one.
			if ( galleryHasImage ) {
				glozin.variations_image_reset($form);
			} else {
				glozin.set_variation_image( $form, variation );
			}

			if( glozin.$window.width() > 768 && $product_gallery.hasClass( 'woocommerce-product-gallery--grid' ) ) {
				if( ! glozin.setVariationImageToGalleryGrid($gallery_wrapper, $gallery_nav, $form, variation) ) {
					return false;
				}
			} else if( $gallery_wrapper.hasClass('swiper') ) {
				if( ! glozin.setVariationImageToGallerySwiper($gallery_wrapper, $gallery_nav, $form, variation) ) {
					return false;
				}
			}

		} else {
			if( $gallery_wrapper.hasClass('swiper') ) {
				$gallery_wrapper.get(0).swiper.slideTo(0);
			} else {
				glozin.variations_image_reset($form);
			}
		}

	};

	glozin.setVariationImageToGalleryGrid = function($gallery_wrapper, $gallery_nav, $form, variation) {
		var slideToImage = $gallery_wrapper.find( '.woocommerce-product-gallery__image[data-thumb="' + variation.image.gallery_thumbnail_src + '"]' );
		if ( slideToImage.length > 0 && ! $( slideToImage ).is(':first-child') ) {
			$('html, body').animate({
				scrollTop: $( slideToImage ).offset().top
			}, 0);

			$form.attr( 'current-image', variation.image_id );
			return false;
		}

		if( glozin.$window.scrollTop() > $gallery_wrapper.offset().top ) {
			$('html, body').animate({
				scrollTop: $gallery_wrapper.offset().top
			}, 0);
		}
		glozin.set_variation_image( $form, variation );
		return true;

	}

	glozin.setVariationImageToGallerySwiper = function($gallery_wrapper, $gallery_nav, $form, variation) {
		$gallery_nav = $gallery_nav.length > 0 ? $gallery_nav : $gallery_wrapper;
		var slideToImage = $gallery_nav.find( '.woocommerce-product-gallery__image[data-thumb="' + variation.image.gallery_thumbnail_src + '"]' );

		if ( slideToImage.length > 0 ) {
			var index = $gallery_nav.find( '.woocommerce-product-gallery__image').index( slideToImage );

			$gallery_wrapper.get(0).swiper.slideTo(index);
			$form.attr( 'current-image', variation.image_id );
			return false;
		}

		$gallery_wrapper.get(0).swiper.slideTo(0);
		glozin.set_variation_image( $form, variation );

		return true;
	}


	/**
	 * Reset main image to defaults.
	 */
	glozin.variations_image_reset = function($form) {
		var $product         = $form.closest( '.product' ),
		    $product_gallery = $product.find( '.woocommerce-product-gallery:not(.woocommerce-product-gallery__video)' ),
		    $gallery_wrapper = $product_gallery.find( '.woocommerce-product-gallery__wrapper' ),
		    $gallery_nav     = $product.find( '.glozin-product-gallery-thumbnails' ),
		    $gallery_img     = $gallery_nav.find( '.woocommerce-product-gallery__image:eq(0) img' ),
			$product_img_wrap = $product_gallery
				.find( '.woocommerce-product-gallery__image, .woocommerce-product-gallery__image--placeholder' )
				.eq( 0 ),
			$product_img      = $product_img_wrap.find( '.wp-post-image' ),
			$product_link     = $product_img_wrap.find( 'a' ).eq( 0 );

		if( $gallery_wrapper.hasClass('swiper') ) {
			$gallery_wrapper.get(0).swiper.slideTo(0);
		}

		$product_img.wc_reset_variation_attr( 'src' );
		$product_img.wc_reset_variation_attr( 'width' );
		$product_img.wc_reset_variation_attr( 'height' );
		$product_img.wc_reset_variation_attr( 'srcset' );
		$product_img.wc_reset_variation_attr( 'sizes' );
		$product_img.wc_reset_variation_attr( 'title' );
		$product_img.wc_reset_variation_attr( 'data-caption' );
		$product_img.wc_reset_variation_attr( 'alt' );
		$product_img.wc_reset_variation_attr( 'data-src' );
		$product_img.wc_reset_variation_attr( 'data-large_image' );
		$product_img.wc_reset_variation_attr( 'data-large_image_width' );
		$product_img.wc_reset_variation_attr( 'data-large_image_height' );
		$product_img_wrap.wc_reset_variation_attr( 'data-thumb' );

		$gallery_img.wc_reset_variation_attr( 'src' );
		$gallery_img.wc_reset_variation_attr( 'srcset' );
		$gallery_img.wc_reset_variation_attr( 'sizes' );
		$gallery_img.wc_reset_variation_attr( 'data-large_image_width' );
		$gallery_img.wc_reset_variation_attr( 'data-large_image_height');
		$gallery_img.wc_reset_variation_attr( 'data-large_image' );
		$gallery_img.wc_reset_variation_attr( 'title' );
		$gallery_img.wc_reset_variation_attr( 'data-caption' );
		$gallery_img.wc_reset_variation_attr( 'alt' );
		$gallery_img.wc_reset_variation_attr( 'data-src' );

		$product_link.wc_reset_variation_attr( 'href' );
	};

	/**
	 * Update varation main image
	 */
	glozin.set_variation_image = function($form, variation) {
		var $product         = $form.closest( '.product' ),
		    $product_gallery = $product.find( '.woocommerce-product-gallery:not(.woocommerce-product-gallery__video)' ),
			$gallery_nav     = $product.find( '.glozin-product-gallery-thumbnails' ),
		    $gallery_img     = $gallery_nav.find( '.woocommerce-product-gallery__image:eq(0) img' ),
			$product_img_wrap = $product_gallery
				.find( '.woocommerce-product-gallery__image, .woocommerce-product-gallery__image--placeholder' )
				.eq( 0 ),
			$product_img      = $product_img_wrap.find( '.wp-post-image' ),
			$product_link     = $product_img_wrap.find( 'a' ).eq( 0 );

		$product_img.wc_set_variation_attr( 'src', variation.image.src );
		$product_img.wc_set_variation_attr( 'height', variation.image.src_h );
		$product_img.wc_set_variation_attr( 'width', variation.image.src_w );
		$product_img.wc_set_variation_attr( 'srcset', variation.image.srcset );
		$product_img.wc_set_variation_attr( 'sizes', variation.image.sizes );
		$product_img.wc_set_variation_attr( 'title', variation.image.title );
		$product_img.wc_set_variation_attr( 'data-caption', variation.image.caption );
		$product_img.wc_set_variation_attr( 'alt', variation.image.alt );
		$product_img.wc_set_variation_attr( 'data-src', variation.image.full_src );
		$product_img.wc_set_variation_attr( 'data-large_image', variation.image.full_src );
		$product_img.wc_set_variation_attr( 'data-large_image_width', variation.image.full_src_w );
		$product_img.wc_set_variation_attr( 'data-large_image_height', variation.image.full_src_h );
		$product_img_wrap.wc_set_variation_attr( 'data-thumb', variation.image.gallery_thumbnail_src );

		$gallery_img.wc_set_variation_attr( 'src', variation.image.gallery_thumbnail_src );
		$gallery_img.wc_set_variation_attr( 'srcset', variation.image.gallery_thumbnail_src );
		$gallery_img.wc_set_variation_attr( 'sizes', variation.image.sizes );
		$gallery_img.wc_set_variation_attr( 'data-large_image_width', variation.image.full_src_w );
		$gallery_img.wc_set_variation_attr( 'data-large_image_height', variation.image.full_src_h );
		$gallery_img.wc_set_variation_attr( 'data-large_image', variation.image.full_src );
		$gallery_img.wc_set_variation_attr( 'title', variation.image.title );
		$gallery_img.wc_set_variation_attr( 'data-caption', variation.image.caption );
		$gallery_img.wc_set_variation_attr( 'alt', variation.image.alt );
		$gallery_img.wc_set_variation_attr( 'data-src', variation.image.full_src );

		$product_link.wc_set_variation_attr( 'href', variation.image.full_src );
	}

	glozin.updateMiniCartQtyAuto = function() {
		var debounceTimeout = null;
		$( document.body ).on( 'change', '.woocommerce-mini-cart .qty', function() {
			var $this = $(this);
			if ( debounceTimeout ) {
				clearTimeout( debounceTimeout );
			}

			debounceTimeout = setTimeout( function() {
				glozin.updateCartAJAX( $this );
			}, 500 );

		} );
	};

	glozin.updateMiniCartContent = function() {
		updateMiniCartContentHeight();
		$(window).on('resize', updateMiniCartContentHeight);

		function updateMiniCartContentHeight() {
			var $freeShippingBar = $('#cart-panel .glozin-free-shipping-bar'),
				$panelHeader = $('#cart-panel .panel__header'),
				$freeShippingBarHeight = 0,
				$content = $('#cart-panel .widget_shopping_cart_content');

			if ($freeShippingBar.length) {
				$freeShippingBarHeight = $freeShippingBar.outerHeight();
			}


			$content.css('--gz-shopping-cart-content-offset', $freeShippingBarHeight + $panelHeader.outerHeight() + 'px');
		}
	};

	glozin.removeItemFromMiniCart = function() {
		$( document.body ).on( 'click', '.woocommerce-mini-cart .remove', function() {
			$(document.body).trigger('glozin_progress_bar_start');
			$(this).addClass('loading');
		} );

		$( document.body ).on( 'removed_from_cart', function() {
			$(document.body).trigger('glozin_progress_bar_complete');
		} );
	};

	glozin.applyCoupon = function() {
		$( document.body ).on( 'click', '.discount-popover [name="apply_coupon"]', function(e) {
			e.preventDefault();

			var $form = $(this).closest('form'),
				data = {
					action: 'glozin_apply_coupon',
					coupon_code: $form.find('[name="coupon_code"]').val()
				},
				$button = $(this),
				$notices = $(this).closest('.popover__content').find('.woocommerce-notices-wrapper');

			if ( $button.data('requestRunning') ) {
				return;
			}

			$button.data('requestRunning', true);
			$button.addClass('loading');
			$.ajax({
				type: 'POST',
				url: wc_add_to_cart_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'glozin_apply_coupon' ),
				data: data,
				success: function(response) {
					if( response.notices ) {
						$notices.html(response.notices);
					}

					if( response.coupon_html ) {
						if( $button.closest('.popover__content').find('.glozin-mini-cart-coupons').length ) {
							$button.closest('.popover__content').find('.glozin-mini-cart-coupons').html(response.coupon_html);
						} else {
							$button.closest('.popover__content').find('form').before('<div class="glozin-mini-cart-coupons mb-15 d-flex flex-column gap-5">' + response.coupon_html + '</div>');
						}
					}

					$button.removeClass('loading');
					$button.data('requestRunning', false);
				}
			});
		} );

		$(document.body).on( 'click', '.discount-popover .woocommerce-remove-coupon', function(e) {
			e.preventDefault();

			var $button = $(this),
				data = {
					action: 'glozin_remove_coupon',
					coupon_code: $button.data('coupon')
				};

			if ( $button.data('requestRunning') ) {
				return;
			}

			$button.data('requestRunning', true);
			$.ajax({
				type: 'POST',
				url: wc_add_to_cart_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'glozin_remove_coupon' ),
				data: data,
				success: function(response) {
					if( $button.closest( '.glozin-mini-cart-coupons' ).length > 0 ) {
						$button.closest('.cart-discount').remove();
					} else {
						$button.closest( '.glozin-mini-cart-coupons' ).remove();
					}

					$button.data('requestRunning', false);
				}
			});
		});

		$(document.body).on( 'glozin_off_canvas_closed', function(e) {
			if( $(e.target).hasClass('discount-popover-opened') || $(e.target).hasClass('note-popover-opened') || $(e.target).hasClass('estimate-popover-opened') ) {
				glozin.closePopover();
			}
		});
	}

	/**
	 * Update shipping address.
	 */
	glozin.updateShippingAddress = function() {
		$( document.body ).on( 'click', '.estimate-popover [name="calc_shipping"]', function(e) {
			e.preventDefault();

			var $button = $(this),
				$form = $button.closest('form'),
				data = $form.serializeArray(),
				$notices = $form.find('.woocommerce-notices-wrapper');

			data.push({
				name: 'action',
				value: 'glozin_update_shipping_address'
			});

			if ( $button.data('requestRunning') ) {
				return;
			}

			$button.data('requestRunning', true);
			$button.addClass('loading');

			$.ajax({
				type: 'POST',
				url: wc_add_to_cart_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'glozin_update_shipping_address' ),
				data: data,
				success: function(response) {
					$notices.html(response.notices);
					$button.removeClass('loading');
					$button.data('requestRunning', false);
				}
			});
		} );
	}

	/**
	 * Quick Edit modal.
	 */
	glozin.productQuickEdit = function() {
		$( document.body ).on( 'click', '.glozin-quickedit-button', function( event ) {
			event.preventDefault();

			var $el = $( this ),
				cart_item_key = $el.data( 'cart_item_key' ),
				position = $el.data( 'position' ),
				$target = $( '#quick-edit-modal' ),
				$container = $target.find( '.woocommerce' ),
				ajax_url = glozinData.ajax_url.toString().replace('%%endpoint%%', 'product_quick_edit');

			$el.addClass( 'loading' );
			$container.find( '.product-quickedit' ).html( '' );
			$(document.body).trigger('glozin_progress_bar_start');
			$.post(
				ajax_url,
				{
					cart_item_key: cart_item_key,
					position: position,
				},
				function( response ) {
					$container.find( '.product-quickedit' ).html( response.data );

					if ( response.success ) {
						update_quickedit();
						glozin.openModal( $target );
					}

					$el.removeClass( 'loading' );
					$(document.body).trigger('glozin_progress_bar_complete');
				}
			).fail( function() {
				window.location.herf = $el.attr( 'href' );
			} );

			/**
			 * Update quick edit common elements.
			 */
			function update_quickedit() {
				var $product = $container.find( '.product-quickedit' ),
					$variations = $product.find( '.variations_form' );

				// Variations form.
				if (typeof wc_add_to_cart_variation_params !== 'undefined') {

					$variations.each(function () {
						variation_form();
						$(this).wc_variation_form();
					});
				}

				$( document.body ).trigger( 'init_variation_swatches.wcboost-variation-swatches');
			}
		});

		$(document.body).on( 'click', '.quick_edit_add_to_cart_button', function( event ) {
			event.preventDefault();
			var $button = $(this);

			if( ! $button.closest('form').hasClass('edit-cart-form') ) {
				return;
			}

			if ( $button.is('.disabled') ) {
				return;
			}

			if ( $button.data('requestRunning') ) {
				return;
			}

			$button.data('requestRunning', true);
			$button.removeClass( 'added' );
			$button.addClass( 'loading' );

			var formData = $button.closest('form').serializeArray(),
				formAction = $button.closest('form').attr('action'),
				ajax_url = glozinData.ajax_url.toString().replace('%%endpoint%%', 'quick_edit_update_cart_item');

			// Trigger event.
			$( document.body ).trigger( 'adding_to_cart', [ $button, formData ] );
			$(document.body).trigger('glozin_progress_bar_start');
			$.ajax({
				url: ajax_url,
				method: 'post',
				data: formData,
				error: function (response) {
					window.location = formAction;
				},
				success: function ( response ) {
					if( response && ! response.error ) {
						$( document.body ).trigger( 'added_to_cart', [ response.fragments, response.cart_hash, $button ] );
					}

					$button.removeClass( 'loading' );
					$button.data('requestRunning', false);
					glozin.closeModal( '#quick-edit-modal', false );
					$(document.body).trigger('glozin_progress_bar_complete');
				}
			});
		} );

		function variation_form() {
			var $form = $( '.product-quickedit .variations_form.edit-cart-form:not(.product-select__variation)' ),
				$productThumbnail =  $form.find('.product-thumbnail'),
				$image = $productThumbnail.find('img'),
				image_original = $productThumbnail.data('image_original'),
				$productPrice = $form.find('.product-price'),
				$buttonATC = $form.find('.quick_edit_add_to_cart_button' );

			$productThumbnail.attr('data-image_srcset_original', $image.attr('srcset'));

			var image_srcset_original = $productThumbnail.attr('data-image_srcset_original');

			$form.on( 'found_variation', function (event, variation) {
				$productThumbnail.addClass('image-variation');
				if ( variation && variation.image.thumb_src !== 'undefined' && variation.image.thumb_src ) {
					$image.attr('src', variation.image.thumb_src);
				} else {
					$image.attr('src', image_original);
				}

				if ( variation && variation.image.srcset !== 'undefined' && variation.image.srcset ) {
					$image.attr('srcset', variation.image.srcset);
				} else {
					$image.attr('srcset', image_srcset_original);
				}

				if( $productPrice.length > 0 ) {
					if( $productPrice.find( '.variation-price' ).length > 0 ) {
						$productPrice.find( '.variation-price' ).remove();
					}

					if( variation.price_html ) {
						$productPrice.find( '.price' ).css( 'display', 'none' );
						$productPrice.append( '<div class="variation-price">' + variation.price_html + '</div>' );
					} else {
						$productPrice.find( '.price' ).css( 'display', '' );
					}
				} else {
					$productPrice = $(this).closest('.entry-summary').find('.glozin-product-price');
					if( $productPrice.length > 0 ) {
						if( $productPrice.find( '.variation-price' ).length > 0 ) {
							$productPrice.find( '.variation-price' ).remove();
						}

						if( variation.price_html ) {
							$productPrice.find( '.price' ).css( 'display', 'none' );
							$productPrice.append( '<div class="variation-price">' + variation.price_html + '</div>' );
						} else {
							$productPrice.find( '.price' ).css( 'display', '' );
						}
					}
				}

				if( $buttonATC.length > 0 ) {
					if( variation.availability_status && ( ! variation.is_purchasable || ! variation.is_in_stock || ! variation.variation_is_visible || ( variation.is_pre_order !== undefined && variation.is_pre_order == 'yes' ) ) ) {
						$buttonATC.wc_set_content( variation.availability_status );
					} else {
						$buttonATC.wc_reset_content();
					}

					if( ! variation.is_purchasable || ! variation.is_in_stock || ! variation.variation_is_visible ) {
						$buttonATC.addClass( 'disabled wc-variation-selection-needed' );
					} else {
						$buttonATC.removeClass( 'disabled wc-variation-selection-needed' );
					}
				}
			});

			$form.on( 'reset_data', function () {
				if( $productThumbnail.hasClass('image-variation') ) {
					$productThumbnail.removeClass('image-variation');
					$image.attr('src', image_original);
					$image.attr('srcset', image_srcset_original);
				}

				if( $productPrice.length > 0 && $productPrice.find( '.variation-price' ).length > 0 ) {
					$productPrice.find( '.variation-price' ).remove();
					$productPrice.find( '.price' ).css( 'display', '' );
				} else {
					$productPrice = $(this).closest('.entry-summary').find('.glozin-product-price');
					if( $productPrice.length > 0 && $productPrice.find( '.variation-price' ).length > 0  ) {
						$productPrice.find( '.variation-price' ).remove();
						$productPrice.find( '.price' ).css( 'display', '' );
					}
				}

				if( $buttonATC.length > 0 ) {
					$buttonATC.wc_reset_content();
					$buttonATC.addClass( 'disabled wc-variation-selection-needed' );
				}
			});

			$form.find('.value select').each( function() {
				var $label = $( this ).closest( '.value' ).siblings( '.label' ).find( 'label' ),
					$holder = $label.find( '.wcboost-variation-swatches__selected-label' );

				if ( ! $holder.length ) {
					$holder = $( '<span class="wcboost-variation-swatches__selected-label" />' );

					$label.append( $holder )
				}

				if ( this.value ) {
					$holder.text( this.options[ this.selectedIndex ].text ).show();
				} else {
					$holder.text( '' ).hide();
				}
			});
		}
	}

	glozin.updateCartAJAX = function ($qty) {
		var $row = $qty.closest('.woocommerce-mini-cart-item'),
			$cart_item = $qty.closest('.widget_shopping_cart_content').find('.woocommerce-mini-cart-item'),
			key = $row.find('a.remove').data('cart_item_key'),
			nonce = $row.find('.woocommerce-mini-cart-item__qty').data('nonce'),
			ajax_url = glozinData.ajax_url.toString().replace('%%endpoint%%', 'update_cart_item');

		if ($.fn.block) {
			$row.block({
				message: null,
				overlayCSS: {
					opacity: 0.6,
					background: '#fff'
				}
			});
		}
		$(document.body).trigger('glozin_progress_bar_start');
		$.post(
			ajax_url, {
				cart_item_key: key,
				qty: $qty.val(),
				cart_item_length: $cart_item.length,
				security: nonce
			}, function (response) {
				if (!response || !response.fragments) {
					return;
				}

				if ($.fn.unblock) {
					$row.unblock();
				}

				$( document.body ).trigger( 'added_to_cart', [response.fragments, response.cart_hash, $row] );
				$(document.body).trigger('glozin_progress_bar_complete');

			}).fail(function () {
			if ($.fn.unblock) {
				$row.unblock();
			}
			$(document.body).trigger('glozin_progress_bar_complete');

			return;
		});
	};

	/**
	 * Recently viewed
	 */
	glozin.recentlyViewedProducts = function () {
		var $recently = glozin.$body.find( '.recently-viewed-products' ),
			$recently_heading = glozin.$body.find( '.recently-viewed-products__title' ),
			$recently_description = glozin.$body.find( '.recently-viewed-products__description' ),
			$recently_columns = $recently.data('columns'),
			ajax_url  = glozinData.ajax_url.toString().replace( '%%endpoint%%', 'glozin_get_recently_viewed' ),
			xhr       = null;

		if ( $recently.length < 1 ) {
			return;
		}

		if ( $recently.hasClass( 'recently-viewed-products--elementor' ) ) {
			return;
		}

		if ( $recently.hasClass( 'products-loaded' ) ) {
			return;
		}

		if ( ! $recently.hasClass( 'has-ajax' ) ) {
			return;
		}

		glozin.$window.on( 'scroll', function () {
			if ( glozinIsVisible( $recently ) && ! xhr ) {
				loadAjaxRecently();
			}
		}).trigger( 'scroll' );

        function loadAjaxRecently() {
			if ( $recently.data( 'requestRunning' ) ) {
				return;
			}

			$recently.data( 'requestRunning', true );
			$recently.addClass( 'ajax-loading' );

			xhr = $.post(
				ajax_url,
				{
					action: 'glozin_get_recently_viewed',
				},
				function (response) {
					if( response.success ) {
						$recently.append( response.data );


						if ( $recently.find( '.no-products' ).length < 1 ) {
							if( $recently.find( '.swiper').length ) {
								glozin.glozinSwiper();
							}

							$recently_heading.removeClass( 'hidden' );
							$recently_description.removeClass( 'hidden' );
							glozin.$body.trigger( 'glozin_get_products_ajax_loaded', [$recently, false] );
						}

						$recently.addClass( 'products-loaded' );
						$recently.data( 'requestRunning', false );
						$recently.removeClass( 'ajax-loading' );
					}
				});
        }

	};

	/**
	 * Product Affiliate
	 */
	glozin.productAffiliate = function () {
		var $product = $( '.single-product div.product' );

		if( ! $product.hasClass( 'product-type-external' ) ) {
			return;
		}

		var $button = $product.find( '.single_add_to_cart_button' );

		$button.on( 'click', function( e ) {
			e.preventDefault();

			window.open( $button.closest( 'form' ).attr( 'action' ), '_blank' );
		} );
	};


	glozin.addedToWishlistNotice = function () {
		if ( typeof glozinData.added_to_wishlist_notice === 'undefined' || ! $.fn.notify ) {
			return;
		}

		glozin.$body.on('added_to_wishlist', function (e, $el_wrap) {
			var content = $el_wrap.data('product_title');
			getaddedToWishlistNotice(content);
			return false;
		});

		function getaddedToWishlistNotice($content) {
			$content += ' ' + glozinData.added_to_wishlist_notice.added_to_wishlist_text;
			$content += '<a href="' + glozinData.added_to_wishlist_notice.wishlist_view_link + '" class="btn-button">' + glozinData.added_to_wishlist_notice.wishlist_view_text + '</a>';

			var $checkIcon = '<span class="glozin-svg-icon message-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></span>',
				$closeIcon = '<span class="glozin-svg-icon svg-active"><svg class="svg-icon" aria-hidden="true" role="img" focusable="false" width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 1L1 14M1 1L14 14" stroke="#A0A0A0" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></path></svg></span>';

			$.notify.addStyle('glozin', {
				html: '<div>' + $checkIcon + $content + $closeIcon + '</div>'
			});

			$.notify('&nbsp', {
				autoHideDelay: glozinData.added_to_wishlist_notice.wishlist_notice_auto_hide,
				className: 'success',
				style: 'glozin',
				showAnimation: 'fadeIn',
				hideAnimation: 'fadeOut'
			});
		}
	}

	glozin.addedToCompareNotice = function () {
		if ( typeof glozinData.added_to_compare_notice === 'undefined' || ! $.fn.notify ) {
			return;
		}

		glozin.$body.on( 'added_to_compare', function (e, $el_wrap) {
			var content = $el_wrap.data('product_title');
			getaddedToCompareNotice(content);
			return false;
		});

		function getaddedToCompareNotice($content) {
			$content += ' ' + glozinData.added_to_compare_notice.added_to_compare_text;
			$content += '<a href="' + glozinData.added_to_compare_notice.compare_view_link + '" class="btn-button">' + glozinData.added_to_compare_notice.compare_view_text + '</a>';

			var $checkIcon = '<span class="glozin-svg-icon message-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></span>',
				$closeIcon = '<span class="glozin-svg-icon svg-active"><svg class="svg-icon" aria-hidden="true" role="img" focusable="false" width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 1L1 14M1 1L14 14" stroke="#A0A0A0" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></path></svg></span>';

			$.notify.addStyle('glozin', {
				html: '<div>' + $checkIcon + $content + $closeIcon + '</div>'
			});

			$.notify('&nbsp', {
				autoHideDelay: glozinData.added_to_compare_notice.compare_notice_auto_hide,
				className: 'success',
				style: 'glozin',
				showAnimation: 'fadeIn',
				hideAnimation: 'fadeOut'
			});
		}
	}

	/**
     * Copy link
     */
    glozin.copyLink = function () {
        $( '.glozin-copylink__button' ).on( 'click', function(e) {
            e.preventDefault();
            var $button = $(this).closest('form').find('.glozin-copylink__link');
            $button.select();
            document.execCommand('copy');

			var icon = $(this).data('icon'),
				icon_copied = $(this).data('icon_copied');

			if( icon_copied ) {
				$(this).html( icon_copied );
			}
        });
    }

	/**
     * Currencies
     */
	glozin.currencyLanguage = function () {
		if( ! $('.glozin-currency-language').length ) {
			return;
		}

		$( document.body ).on( 'click', '.glozin-currency-language .current', function( event ) {
			if (! $(this).next().hasClass('currency-dropdown')) {
				return;
			}

			if ( $(this).hasClass('active') ) {
				$(this).removeClass('active');
				$(this).next('.currency-dropdown').removeClass('active');
			} else {
				$('.glozin-currency-language .current').removeClass('active');
				$('.glozin-currency-language .currency-dropdown').removeClass('active');
				$('.glozin-currency-language-mobile-menu').removeClass('opened');

				$(this).next('.currency-dropdown').stop().toggleClass('active');
				$(this).toggleClass('active');

				if ( $(this).closest('.glozin-currency-language').hasClass('glozin-currency-language-mobile-menu') ) {
					$(this).closest('.glozin-currency-language-mobile-menu').toggleClass('opened');
				}
			}
		} ).on( 'keyup', function ( e ) {
			if ( e.keyCode === 27 ) {
				$(this).removeClass('active');
				$('.glozin-currency-language .current').removeClass('active');
				$('.glozin-currency-language .currency-dropdown').removeClass('active');
				$('.glozin-currency-language-mobile-menu').removeClass('opened');
			}
		} ).on( 'click', function( event ) {
			var $target = $( event.target );

			if ( $target.is( '.glozin-currency-language .current' ) ) {
				return;
			}

			if ( $target.is( '.glozin-currency-language .preferences-menu__item-child' ) ) {
				return;
			}

			$('.glozin-currency-language .current').removeClass('active');
			$('.glozin-currency-language .currency-dropdown').removeClass('active');
			$('.glozin-currency-language-mobile-menu').removeClass('opened');
		} );

		$(document.body).on( 'glozin_popover_opened', function () {
			$('.glozin-currency-language .current').removeClass('active');
			$('.glozin-currency-language .currency-dropdown').removeClass('active');
			$('.glozin-currency-language-mobile-menu').removeClass('opened');
		});

		$(document.body).on( 'click', '.glozin-currency-language-mobile-menu .current', function() {
			var $currencyDropdown = $(this).next('.currency-dropdown');
			$(this).closest('.glozin-currency-language-mobile-menu').css('--gz-currency-language-mobile-menu-height', $currencyDropdown.outerHeight() + 'px');
		});
    }

	glozin.mobileMenu = function () {
		var $menu = $('#mobile-menu-panel');

		if( ! $menu.length ) {
			return;
		}

		$menu.on( 'click', '.panel__menu-items a', function( e ) {
			e.preventDefault();

			var $item = $( this ).closest('li'),
				index = $item.index();

			$( this ).toggleClass( 'active' );

			$item.siblings().find('a').removeClass( 'active' );

			$menu.find('.panel__content-items').eq( index ).addClass( 'active' ).siblings().removeClass( 'active' );
		});
	}

	/**
     * Click dropdown menu in hamburger menu
     */
	glozin.hamburgerToggleMenuItem = function () {
		var $menu  = $('#mobile-menu-panel'),
		    $click = 'ul.menu li.menu-item-has-children > a';

		if( ! $menu.length ) {
			return;
		}

		if( glozinData.header_mobile_menu_open_primary_submenus_on == 'icon' ) {
			$menu.find( 'ul.menu li.menu-item-has-children > a').append( '<span class="toggle-menu-children"></span>' );
			$click = 'ul.menu li.menu-item-has-children > a .toggle-menu-children';
		}

		var $submenuHeading = $menu.find('.panel__footer .submenu-items--heading');

		$menu.find('li.menu-item-has-children').each(function() {
			$(this).find('ul.sub-menu').prepend('<li class="submenu-items--heading">' + $submenuHeading.html() + '</li>');
		});

		$menu.on( 'click', $click, function( e ) {
			e.preventDefault();

			var title = $(this).data('title');

			if( ! title ) {
				title = $(this).closest('a').data('title');
			}

			$( this ).closest('li.menu-item-has-children').find('.submenu-items--title-text').html(title);

			$( this ).closest('li.menu-item-has-children').addClass( 'submenu--open' );


		});

		$menu.on( 'click', '.submenu-items--title', function( e ) {
			e.preventDefault();

			if( $(this).closest('.mega-items--heading').length ) {
				$( this ).closest('.mega-menu__column').removeClass( 'submenu--open' );
				$( this ).closest('li.menu-item-has-children').removeClass('megamenu--open');
			} else {
				$( this ).closest('li.menu-item-has-children').removeClass('submenu--open');
			}

		});
	}


	/**
     * Click dropdown mega menu item in hamburger menu
     */
	glozin.hamburgerToggleMegaMenuItem = function () {
		var $menu  = $('#mobile-menu-panel'),
		    $click = '.mega-menu .menu-item--widget-heading';

			if( ! $menu.length ) {
				return;
			}

		if( glozinData.header_mobile_menu_open_primary_submenus_on == 'icon' ) {
			$menu.find( '.mega-menu .menu-item--widget-heading > *' ).append( '<span class="toggle-menu-children"></span>' );
			$click = '.mega-menu .menu-item--widget-heading > * .toggle-menu-children';
		}

		var $submenuHeading = $menu.find('.panel__footer .submenu-items--heading');

		$menu.find(".mega-menu__column").each(function () {
			var $menuHeading = $(this).find( '.menu-item--widget-heading' ),
				$menuItems = $(this).find( '.menu-item' ).not( '.menu-item--widget-heading' );

				if( ! $menuHeading.length ) {
					return;
				}

				if ($menuItems.length) {
					let $wrapper = $("<li class='sub-menu'><ul></ul></li>"),
					 	$subMenu = $wrapper.find("ul"),
						$headingItem = $("<li class='submenu-items--heading mega-items--heading'>"+ $submenuHeading.html() + "</li>");
					$headingItem.appendTo($subMenu);
					$menuItems.each(function () {
						$(this).appendTo($subMenu);
					});

					$menuHeading.after($wrapper);
				}
		});

		$menu.on( 'click', $click, function( e ) {
			e.preventDefault();
			var title = '';
			if( $(this).closest('.menu-item--widget-heading-title').length ) {
				title = $(this).closest('.menu-item--widget-heading-title').html();
			} else {
				title = $(this).find('.menu-item--widget-heading-title').html();
			}


			$( this ).closest('.mega-menu__column').find('.submenu-items--heading').find('.submenu-items--title-text').html(title);

			$( this ).closest('.mega-menu__column').addClass( 'submenu--open' );

			$( this ).closest('li.menu-item-has-children').addClass('megamenu--open');

		});

	}

	/**
     * Header campaign bar
     */
	glozin.headerCampaignBar = function () {
		var $selector = $('#campaign-bar');

		if( ! $selector.length ) {
			return;
		}

		$selector.on( 'click', '.campaign-bar__close', function( e ) {
			e.preventDefault();

			$(this).closest('.campaign-bar').slideUp();
		});
	}

	/**
	 * Sticky header
	 */
	glozin.stickyHeader = function () {
		if( glozin.$body.hasClass('elementor-editor-active') ) {
			return;
		}
		if( glozinData.header_sticky ) {
			sticky( glozin.$header.find('.site-header__desktop'), glozin.$header.find('.site-header__section.glozin-header-sticky') );
		}

		if( glozinData.header_mobile_sticky ) {
			sticky( glozin.$header.find('.site-header__mobile'), glozin.$header.find('.site-header__section.glozin-header-mobile-sticky') );
		}

		function sticky( $headerSticky, $headerSection ) {
			var header    = glozin.$header.outerHeight(true) / 2,
				hBody     = glozin.$body.outerHeight(true),
				campaign  = $('#campaign-bar').is(":visible") ? $('#campaign-bar').height() : 0,
				topbar    = $('#topbar').is(":visible") ? $('#topbar').height() : 0,
				scrollTop = header + campaign + topbar,
				heightHeader = glozin.$header.find('.site-header__desktop').length ? glozin.$header.find('.site-header__desktop').outerHeight() : 0,
				heightHeaderMobile = glozin.$header.find('.site-header__mobile').length ? glozin.$header.find('.site-header__mobile').outerHeight() : 0;

			if( hBody < scrollTop*5 ) {
				return;
			}

			if ( 'up' === glozinData.header_sticky_on ) {
				if( $headerSticky.length && typeof Headroom !== 'undefined' ) {
					var stickyHeader = new Headroom( $headerSticky.get(0), {
						offset: scrollTop
					});

					stickyHeader.init();
				}
			}

			glozin.$window.on('scroll', function () {
				var scroll = glozin.$window.scrollTop();

				if (hBody <= scrollTop + glozin.$window.height()) {
					return;
				}


				if (scroll > scrollTop) {
					glozin.$header.css({
						'--gz-header-height': heightHeader + 'px',
						'--gz-header-height-mobile': heightHeaderMobile + 'px'
					});

					if ( 'up' !== glozinData.header_sticky_on ) {
						$headerSection.addClass('minimized');
					}

					glozin.$header.addClass('gz-header-sticky-visible');

				} else {
					glozin.$header.removeAttr('style');
					glozin.$header.removeClass('gz-header-sticky-visible');

					if ( 'up' !== glozinData.header_sticky_on ) {
						$headerSection.removeClass('minimized');
					}
				}
			});
		}
	};

	glozin.headerProductCategories = function () {
		var $menu = $('#header-sidebar-categories'),
			$backdrop = $menu.find('.header-sidebar-categories__backdrop');

		$menu.on('mouseenter', function () {
			$menu.addClass('opened');
		});

		$backdrop.on('mouseenter', function (e) {
			$menu.removeClass('opened');
			e.stopPropagation();
		});
	}

	/**
	 * Back to top icon
	 */
	glozin.backToTop = function () {
		var $scrollTop = $('#gotop');

		glozin.$window.on('scroll', function () {
			if ( glozin.$window.scrollTop() > 100 ) {
				$scrollTop.addClass('show-scroll');
			} else {
				$scrollTop.removeClass('show-scroll');
			}

			let scrollTop2    = glozin.$window.scrollTop(),
				docHeight     = $(document).height(),
				winHeight     = $(window).height(),
				scrollPercent = (scrollTop2 / (docHeight - winHeight)) * 100;

			$scrollTop.css( '--gz-gotop-height-scroll', scrollPercent + '%' );
		});

		glozin.$body.on('click', '#gotop', function (e) {
			e.preventDefault();

			$('html, body').animate({ scrollTop: 0 }, 600);
		});
	};


	/**
	 * Dropdown product categories sidebar
	 *
	 * @return boolean
	 */
	glozin.toggleProductCategoriesWidget = function() {
		var $widget = $( '.widget_product_categories' );

		$widget.find('li.cat-parent').each( function() {
			if ( $(this).find('ul.children').length > 0 ) {
				$(this).append('<span class="gz-product-cat-item-toggle"></span>');
			}
		});

		$widget.on( 'click', 'li.cat-parent > .gz-product-cat-item-toggle', function (e) {
			e.preventDefault();

			var $item = $( this ).closest('li.cat-parent');

			$item.toggleClass( 'active' ).siblings().removeClass( 'active' );

			// If this is sub-menu item
			$item.children( 'ul.children' ).slideToggle();

        });

		$('.catalog-filters-sidebar .wp-block-group').each(function(){
			if( $(this).find('.wp-block-heading').length ) {
				$(this).find('.wp-block-heading').addClass('gz-widget-heading clicked');
				$(this).find('.wp-block-heading').append('<span class="gz-collapse-icon"></span>');
				$(this).find('.wp-block-heading').nextAll().wrapAll('<div class="gz-widget-group dropdown"/>');
			}
		});

		$('.catalog-filters-sidebar .widget').each(function(){
			if( $(this).find('.widget-title').length ) {
				$(this).find('.widget-title').addClass('gz-widget-heading clicked');
				$(this).find('.widget-title').append('<span class="gz-collapse-icon"></span>');
				$(this).find('.widget-title').nextAll().wrapAll('<div class="gz-widget-group dropdown"/>');
			}
		});

		$('.catalog-filters-sidebar .gz-widget-heading').on('click', function (e) {
			e.preventDefault();
			if (! $(this).next().hasClass('dropdown')) {
				return;
			}
			if ($(this).closest('.catalog-filters-horizontal').length && $(window).width() > 1024) {
				return;
			}

			$(this).next('.dropdown').stop().slideToggle();
			$(this).toggleClass('active');
			return false;
		});
	};

	/**
	 * Dropdown product categories sidebar
	 *
	 * @return boolean
	 */
	glozin.dropdownProductCategoriesSidebar = function( el ) {
		var $widget = $( '.wp-block-woocommerce-product-categories' ),
			$categoriesList = $widget.find('.wc-block-product-categories-list');

		if ( ! $widget.hasClass('is-list') ) {
			return;
		}

		$widget.addClass('gz-product-categories-widget');

		$categoriesList.addClass('gz-product-categories-dropdown');
		$categoriesList.closest('.wc-block-product-categories-list-item').addClass('gz-product-categories-has-children');
		$categoriesList.closest('.wc-block-product-categories-list-item').append('<span class="gz-product-categories-toggler" aria-hidden="true"></span>');

		$widget.on( 'click', '.gz-product-categories-has-children > .gz-product-categories-toggler', function (e) {
			e.preventDefault();

			var $item = $( this ).closest('.gz-product-categories-has-children');

			$item.toggleClass( 'active' ).siblings().removeClass( 'active' );

			// If this is sub-menu item
			$item.children( '.gz-product-categories-dropdown' ).slideToggle();
			$item.siblings().find( '.gz-product-categories-dropdown' ).slideUp();

        });
	};

	glozin.formatPrice = function( $number ) {
		var currency       = glozinData.currency_symbol,
			thousand       = glozinData.thousand_sep,
			decimal        = glozinData.decimal_sep,
			price_decimals = glozinData.price_decimals,
			currency_pos   = glozinData.currency_pos,
			n              = $number;

		if ( parseInt(price_decimals) > -1 ) {
			$number = $number.toFixed(price_decimals) + '';
			var x = $number.split('.');
			var x1 = x[0],
				x2 = x.length > 1 ? decimal + x[1] : '';

			if( thousand ) {
				var rgx = /(\d+)(\d{3})/;
				while (rgx.test(x1)) {
					x1 = x1.replace(rgx, '$1' + thousand + '$2');
				}
			}

			n = x1 + x2
		}

		switch (currency_pos) {
			case 'left' :
				return currency + n;
				break;
			case 'right' :
				return n + currency;
				break;
			case 'left_space' :
				return currency + ' ' + n;
				break;
			case 'right_space' :
				return n + ' ' + currency;
				break;
		}
	}

	/**
	 * Tooltip
	 */
	glozin.tooltip = function () {
		glozin.$body.on( 'mouseenter', '.gz-tooltip', function (e) {
			tooltip_data($(this));
		}).on( 'mouseleave', '.gz-tooltip', function (e) {
			var tooltip = glozin.$body.find('.gz-tooltip--data');

			if( tooltip.hasClass('left') ) {
				tooltip.addClass('move-right');
			} else if( tooltip.hasClass('right') ) {
				tooltip.addClass('move-left');
			} else {
				tooltip.addClass('move-down');
			}

			setTimeout(function() {
				tooltip.remove();
			}, 300);
		});

		function tooltip_data($el) {
			if( $el.hasClass( 'loading') ) {
				$('.gz-tooltip--data').remove();
				return;
			}

			var $tooltip = $( '<div class="gz-tooltip--data position-absolute text-light"></div>' ),
				self = $el,
				time = 50;

			$tooltip.appendTo( 'body' );

			$tooltip.attr( 'data-tooltip', $el.data( 'tooltip' ) );

			if( $el.data( 'tooltip_added' ) ) {
				$tooltip.attr( 'data-tooltip_added', $el.data( 'tooltip_added' ) );
			}

			if( $el.hasClass( 'added' ) ) {
				$tooltip.addClass( 'added' );

				if( ! $el.data( 'tooltip_added' ) ) {
					$tooltip.attr( 'data-tooltip_added', $el.data( 'tooltip' ) );
				}
			}

			$tooltip.fadeIn( time, function() {
				var position = self.offset(),
					height = self.outerHeight(),
					tooltipWidth = $('.gz-tooltip--data').outerWidth(),
					tooltipHeight = $('.gz-tooltip--data').outerHeight(),
					top = ( position.top - tooltipHeight ) + height + 'px',
					left = position.left + self.outerWidth() / 2 - tooltipWidth / 2 + 'px',
					spacing = 10;

				if ( $el.hasClass('gz-tooltip-short-spacing') ) {
					spacing = 6;
				}

				$tooltip.addClass( 'top' );
				top = position.top - tooltipHeight - spacing + 'px';

				if( ! $el.closest('.tooltip-top').length ) {
					if( ( ! check_rtl() && self.data( 'tooltip_position' ) === 'left' ) || ( check_rtl() && self.data( 'tooltip_position' ) === 'right' ) ) {
						$tooltip.addClass( 'left' );
						left = check_rtl() ? position.left - tooltipWidth + spacing + 'px' : position.left - tooltipWidth - spacing + 'px';
						top = position.top + self.outerHeight() / 2 - tooltipHeight / 2 + 'px';
					}

					if( ( ! check_rtl() && self.data( 'tooltip_position' ) === 'right' ) || ( check_rtl() && self.data( 'tooltip_position' ) === 'left' ) ) {
						$tooltip.addClass( 'right' );
						left = check_rtl() ? ( position.left + self.outerWidth() + spacing ) + 'px' : ( position.left + self.outerWidth() - spacing ) + 'px';
						top = position.top + self.outerHeight() / 2 - tooltipHeight / 2 + 'px';
					}

					if( self.data( 'tooltip_position' ) === 'bottom' ) {
						$tooltip.addClass( 'bottom' );
						top = position.top + height + spacing + 'px';
					}
				}

				$tooltip.css( {
					top: top,
					left: left,
				});
			});
		}

		$(document.body).on( 'added_to_cart wc_fragments_refreshed', function () {
			$( '.gz-tooltip--data' ).remove();
		});

		function check_rtl() {
			if ($("html").attr("dir") === "rtl" || $("body").css("direction") === "rtl") {
				return true;
			}

			return false;
		}
	}

	glozin.orderComments = function() {
		var $orderCommentsField = $('#order_comments_field #order_comments');
		if( ! $orderCommentsField.length ) {
			return;
		}

		// Load saved comments if available
		if (localStorage.getItem('order_comments')) {
			$orderCommentsField.val(localStorage.getItem('order_comments') );
		}

		// Check if autosave is enabled (default is true if attribute is not present)
		var autoSave = $orderCommentsField.attr('data-autosave') !== 'false';

		if (autoSave) {
			// Auto-save on input when enabled
			$orderCommentsField.on('input', function() {
				localStorage.setItem('order_comments', $(this).val());
			});
		} else {
			// Find the existing save button
			var $saveButton = $('.order-comments-save');

			if ($saveButton.length) {
				// Save button click handler - use event delegation to ensure it works even if button is added later
				$(document).on('click', '.order-comments-save', function(e) {
					e.preventDefault();
					localStorage.setItem('order_comments', $orderCommentsField.val());
				});
			}
		}

		// Clear storage on form submit
		$('form.checkout').on('submit', function() {
			localStorage.removeItem('order_comments');
		} );
	}

	glozin.navigationBar = function() {
		var $navigationBar = $('#glozin-mobile-navigation-bar');

		if ( ! $navigationBar.length ) {
			return;
		}

		glozin.$body.css('--gz-navigation-bar-height', $navigationBar.outerHeight() + 'px');

		glozin.$body.addClass('mobile-has-navigation-bar');
	}

	/**
	 * Document ready
	 */
	$(function () {
		glozin.init();
	});

})(jQuery);
