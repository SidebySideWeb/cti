class GlozinToggleMobileWidgetHandler extends elementorModules.frontend.handlers.Base {
	bindEvents() {
		const settings = this.getElementSettings();

		var $title = this.$element.find('.glozin-toggle-mobile__title');

		jQuery( window ).on('resize', function () {
			if ( jQuery( window ).width() < 767 && settings.toggle_menu == "yes") {
				$title.addClass( 'glozin-toggle-mobile__title--toggle' );
				$title.next( '.glozin-toggle-mobile__content' ).addClass( 'clicked' );
				$title.closest( '.glozin-toggle-mobile__wrapper' ).addClass( 'dropdown' );

				if ( settings.toggle_status == "yes" ) {
					$title.addClass( 'active' );
					$title.siblings( '.glozin-toggle-mobile__content' ).css( 'display', 'block' );
				} else {
					$title.removeClass( 'active' );
				}

			} else {
				$title.removeClass( 'glozin-toggle-mobile__title--toggle' );
				$title.removeClass( 'active' );
				$title.siblings( '.glozin-toggle-mobile__content' ).removeAttr('style');
				$title.next('.glozin-toggle-mobile__content').removeClass('clicked');
				$title.next('.glozin-toggle-mobile__content').removeAttr('style');
				$title.closest('.glozin-toggle-mobile__wrapper').removeClass('dropdown');
			}
		}).trigger('resize');

		this.$element.on( 'click', '.glozin-toggle-mobile__title--toggle', function ( e ) {
			e.preventDefault();

			if ( !$title.closest( '.glozin-toggle-mobile__wrapper' ).hasClass( 'dropdown' ) ) {
				return;
			}

			jQuery(this).next('.clicked').stop().slideToggle();
			jQuery(this).toggleClass('active');
			return false;
		} );
	}
}

class GlozinCarouselWidgetHandler extends elementorModules.frontend.handlers.CarouselBase {
	getDefaultSettings() {
		const settings = super.getDefaultSettings(),
			  $el = this.$element.find( '.glozin-carousel--elementor' );

		if( $el.hasClass( 'swiper' ) ) {
			settings.selectors.carousel = '.glozin-carousel--elementor';
		} else {
			settings.selectors.carousel = '.glozin-carousel--elementor .swiper';
		}

		return settings;
	}

	getSwiperSettings() {
		const self = this,
			  settings = super.getSwiperSettings(),
			  elementSettings = this.getElementSettings(),
			  slidesToShow = +elementSettings.slides_to_show || 3,
			  isSingleSlide = 1 === slidesToShow,
			  elementorBreakpoints = elementorFrontend.config.responsive.activeBreakpoints,
			  defaultSlidesToShowMap = {
					mobile: 1,
					tablet: isSingleSlide ? 1 : 2,
				};

		let argsBreakpoints = {};
		let lastBreakpointSlidesToShowValue = slidesToShow;

		var spaceBetween = elementSettings.image_spacing_custom ? parseInt(elementSettings.image_spacing_custom) : 0;

		settings.spaceBetween = elementSettings.custom_space_between == 'yes' ? 0 : spaceBetween;

		if( elementSettings.slides_rows && parseInt(elementSettings.slides_rows) > 1 ) {
			settings.grid = {
				fill: 'row',
				rows: elementSettings.slides_rows
			};
			settings.loop = false;
		}

		var changed = false;
		Object.keys(elementorBreakpoints).forEach(breakpoint => {
			if( elementorBreakpoints[breakpoint].value !== elementorBreakpoints[breakpoint].default_value ) {
				return;
			}

			elementorBreakpoints[breakpoint].value = parseInt( elementorBreakpoints[breakpoint].value ) + 1;
			changed = true;
		});


		if ( changed ) {
			Object.keys( elementorBreakpoints ).reverse().forEach( ( breakpointName ) => {
				// Tablet has a specific default `slides_to_show`.
				const defaultSlidesToShow = defaultSlidesToShowMap[ breakpointName ] ? defaultSlidesToShowMap[ breakpointName ] : lastBreakpointSlidesToShowValue;

				argsBreakpoints[ elementorBreakpoints[ breakpointName ].value ] = {
					slidesPerView: + parseInt(elementSettings[ 'slides_to_show_' + breakpointName ]) || defaultSlidesToShow,
					slidesPerGroup: +parseInt(elementSettings[ 'slides_to_scroll_' + breakpointName ]) || 1,
				};

				if ( elementSettings.image_spacing_custom ) {
					argsBreakpoints[ elementorBreakpoints[ breakpointName ].value ].spaceBetween = this.getSpaceBetween( breakpointName );
				}

				lastBreakpointSlidesToShowValue = +parseInt(elementSettings[ 'slides_to_show_' + breakpointName ]) || defaultSlidesToShow;
			} );

			settings.breakpoints = argsBreakpoints;
		}

		if( elementSettings.slides_rows && parseInt(elementSettings.slides_rows) > 1 ) {
			settings.grid = {
				fill: 'row',
				rows: elementSettings.slides_rows
			};
		}

		Object.keys(elementorBreakpoints).forEach(breakpoint => {
			if( elementSettings.custom_space_between == 'yes') {
				settings.breakpoints[ elementorBreakpoints[breakpoint].value ].spaceBetween = 0;
			} else {
				settings.breakpoints[ elementorBreakpoints[breakpoint].value ].spaceBetween = elementSettings[ 'image_spacing_custom_' + breakpoint ] ? elementSettings[ 'image_spacing_custom_' + breakpoint ] : spaceBetween;
			}

			if ( breakpoint == 'mobile' && elementSettings[ 'slidesperview_auto_mobile' ] == 'yes' ) {
				settings.breakpoints[ elementorBreakpoints[breakpoint].value ].slidesPerView = 1.5;
			}

			if( elementSettings.slides_rows && elementSettings.slides_rows > 1 ) {
				settings.breakpoints[ elementorBreakpoints[breakpoint].value ].grid = {
					fill: 'row',
					rows: elementSettings[ 'slides_rows_' + breakpoint ] ? elementSettings[ 'slides_rows_' + breakpoint ] : elementSettings.slides_rows
				};
			}
		});

		settings.on.resize = function () {
			var self           = this,
				$productThumbnail = this.$el.closest( '.glozin-carousel--elementor' ).find('.product-thumbnail'),
				$postThumbnail    = this.$el.closest( '.glozin-carousel--elementor' ).find('.post-thumbnail'),
				$iframeWidth      = jQuery(self.slides).find('.gz-ratio--iframe').length ? jQuery(self.slides).find('.gz-ratio--iframe').width() : 0;

			if( $productThumbnail.length > 0 ) {
				jQuery(this.$el).imagesLoaded(function () {
					var	heightThumbnails = $productThumbnail.outerHeight(),
						top = ( ( heightThumbnails / 2 ) + 15 ) + 'px';

					jQuery(self.navigation.$nextEl).css({ '--gz-arrow-top': top });
					jQuery(self.navigation.$prevEl).css({ '--gz-arrow-top': top });
				});
			}

			if( $postThumbnail.length > 0 ) {
				jQuery(this.$el).imagesLoaded(function () {
					var	heightThumbnails = $postThumbnail.outerHeight(),
						top = ( heightThumbnails / 2 ) + 'px';

					jQuery(self.navigation.$nextEl).css({ '--gz-arrow-top': top });
					jQuery(self.navigation.$prevEl).css({ '--gz-arrow-top': top });
				});
			}

			if( $iframeWidth > 0 ) {
				jQuery(self.slides).find('.gz-ratio--iframe').css({ '--gz-item-iframe-width': $iframeWidth });

				if( jQuery(self.slides).find('.gz-ratio--iframe iframe').length > 0 ) {
					var ratioPercentage = jQuery(self.slides).find('.gz-ratio--iframe iframe').height() / jQuery(self.slides).find('.gz-ratio--iframe iframe').width() * 100;
					jQuery(self.slides).find('.gz-ratio--iframe').css({ '--gz-ratio-percent': ratioPercentage + '%' });
				}
			}
		}

		if( this.$element.find('.glozin-carousel--elementor').hasClass('glozin-carousel--background') ) {
			settings.on.slideChangeTransitionStart = function () {
				const activeSlide = this.slides[this.activeIndex];
				const bgColor = activeSlide.dataset.backgroundColor;

				jQuery(this.$el).find( '.glozin-split-hero-slider__item' ).css({ 'background-color': bgColor });

				if( jQuery(this.$el).parent().find('.glozin-split-hero-slider__images').length > 0 ) {
					jQuery(this.$el).parent().find('.glozin-split-hero-slider__image').removeClass('active');
					jQuery(this.$el).parent().find('.glozin-split-hero-slider__image').eq(this.activeIndex).addClass('active');
				}
			}
		}

		return settings;
	}

	onEditSettingsChange( propertyName ) {
		if( this.swiper === undefined ) {
			return;
		}

		if ( 'activeItemIndex' === propertyName ) {
			this.swiper.slideToLoop( this.getEditSettings( 'activeItemIndex' ) - 1 );
		}
	}

	a11ySetSlideAriaHidden( status = '' ) {
		const currentIndex = 'initialisation' === status ? 0 : this.swiper?.activeIndex;

		if ( 'number' !== typeof currentIndex ) {
			return;
		}

		const $slides = this.elements.$swiperContainer.find( this.getSettings( 'selectors' ).slideContent );

		$slides.each( ( index, slide ) => {
			slide.removeAttribute( 'inert' );
		} );
	}

	onInit() {
		super.onInit();
		this.elements.$swiperContainer.find('.swiper-wrapper').attr('role', 'list');
		this.elements.$swiperContainer.find('.swiper-slide').attr('role', 'listitem');

		this.glozinUpdateSwiper();
	}

	glozinUpdateSwiper() {
		const swiper = this.$element.find('.swiper').get(0)?.swiper;

		if ( ! swiper ) {
			return;
		}

		const elementSettings = this.getElementSettings();

		swiper.params.breakpoints[ elementorFrontend.config.responsive.activeBreakpoints['tablet'].value ].spaceBetween = elementSettings.custom_space_between == 'yes' ? 0 : this.getSpaceBetween( 'desktop' );

		if( elementSettings.slides_rows && parseInt(elementSettings.slides_rows) > 1 ) {
			swiper.params.breakpoints[ elementorFrontend.config.responsive.activeBreakpoints['tablet'].value ].grid = {
				fill: 'row',
				rows: elementSettings.slides_rows
			};
		}

		swiper.update();
	}
}

class GlozinCodeDiscountWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				button: '.glozin-code-discount',
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$button: this.findElement( selectors.button ),
		};
	}

	bindEvents() {
		super.bindEvents();

		this.elements.$button.on( {
			click: ( event ) => {
				event.preventDefault();

				const $target = jQuery(event.currentTarget);
				const codeText = $target.find('.glozin-button-text-code').text().trim();

				navigator.clipboard.writeText(codeText);
				$target.addClass('added');
				$target.find('.glozin-button-text-copied').removeClass('invisible');
				$target.find('.glozin-button-text-code').addClass('invisible');

				setTimeout(() => {
					$target.removeClass('added');
					$target.find('.glozin-button-text-copied').addClass('invisible');
					$target.find('.glozin-button-text-code').removeClass('invisible');
				}, 2000);
			}
		});
	}

	onInit() {
		super.onInit();

		if( this.$element.closest( '.modal__content' ).length > 0 ) {
			this.$element.closest( '.modal__content' ).addClass( 'modal--has-code-discount' );
		}
	}
}

class GlozinShortContentWidgetHandler extends elementorModules.frontend.handlers.Base {
	bindEvents() {
		var elementSettings = this.getElementSettings();

		var originalMaxHeight = elementSettings.max_height.size + elementSettings.max_height.unit;
		this.$element.find( '.glozin-short-content__content' ).css('max-height', originalMaxHeight);

		this.$element.on( 'click', '.glozin-short-content__button', function ( e ) {
			e.preventDefault();
			var $button = jQuery(this),
				$text_show = $button.data( 'show' ),
				$text_hide = $button.data( 'hide' );

			if( $button.siblings( '.glozin-short-content__content' ).hasClass( 'show' ) ) {
				$button.siblings( '.glozin-short-content__content' ).css('max-height', originalMaxHeight);
				$button.siblings( '.glozin-short-content__content' ).removeClass( 'show' );
				$button.text( $text_show );
			} else {
				var actualHeight = $button.siblings( '.glozin-short-content__content' ).get(0).scrollHeight;
				$button.siblings( '.glozin-short-content__content' ).addClass( 'show' );
				$button.siblings( '.glozin-short-content__content' ).css('max-height', actualHeight);
				$button.text( $text_hide );
			}
		} );
	}
}

class GlozinHotspotWidgetHandler extends elementorModules.frontend.handlers.Base {
	bindEvents() {
		var product = this.$element.find( '.glozin-lookbook-carousel__product' ),
			modal_content = this.$element.find( '.lookbook-carousel-modal-content' );

		this.$element.off('click', '.glozin-lookbook-carousel__button').on( 'click', '.glozin-lookbook-carousel__button', function ( e ) {
			e.preventDefault();

			var items = jQuery(this).closest( '.glozin-lookbook-carousel__item' );

			items.siblings().find( '.glozin-lookbook-carousel__product' ).removeClass( 'active');
			jQuery(this).closest( '.glozin-lookbook-carousel__product' ).toggleClass( 'active' ).siblings().removeClass( 'active');

			if( jQuery(this).closest( '.glozin-lookbook-carousel__product' ).hasClass('active' ) ) {
				jQuery(this).closest( '.glozin-carousel--elementor' ).addClass( 'hotspot-active' );
				items.find( '.glozin-lookbook-carousel__product-inner' ).removeClass( 'hidden' );
			} else {
				jQuery(this).closest( '.glozin-carousel--elementor' ).removeClass( 'hotspot-active' );
				items.find( '.glozin-lookbook-carousel__product-inner' ).addClass( 'hidden' );
			}

			var clone = jQuery(this).closest( '.glozin-lookbook-carousel__product' ).find( '.glozin-lookbook-carousel__product-inner' ).clone().html();

			modal_content.html( clone );
		} );

		jQuery( document.body ).on('click', function (evt) {
			if (jQuery( evt.target ).closest( product ).length > 0) {
                return;
            }

			product.closest( '.glozin-carousel--elementor' ).removeClass( 'hotspot-active' );
            product.removeClass('active');
			product.find( '.glozin-lookbook-carousel__product-inner' ).addClass( 'hidden' );
        });


	}

	onInit() {
		super.onInit();

		var $modal = this.$element.find( '.lookbook-carousel-modal' ),
			$button = this.$element.find( '.glozin-lookbook-carousel__button' );

		jQuery( window ).on( 'resize', function () {
			if( jQuery( window ).width() < 1025 ) {
				$button.attr( 'data-toggle', 'modal' );
				$modal.find( '.glozin-lookbook-carousel__close' ).trigger('click');
			} else {
				$button.removeAttr('data-toggle');
			}
		}).trigger('resize');
	}
}

class GlozinImageBeforeAfterWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				container: '.glozin-image-before-after'
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$container: this.$element.find( selectors.container )
		};
	}

	changeImagesHandle() {
		const container = this.elements.$container;

        container.imagesLoaded( function () {
            container.find( '.box-thumbnail' ).imageslide();
        } );
	}

	onInit() {
		super.onInit();

		if ( ! this.elements.$container.length ) {
			return;
		}

		this.changeImagesHandle();
	}
}

class GlozinContentPreviewTabsWidgetHandler extends elementorModules.frontend.handlers.Base {
	actionTabs() {
		jQuery('.glozin-content-preview-tabs__title').on( 'mouseenter', function() {
			var $this = jQuery(this),
				key = $this.data('key');

			if( $this.attr('data-active') === 'true' ) {
				return;
			}

			$this.siblings('.glozin-content-preview-tabs__title').attr('data-active', 'false');
			$this.attr('data-active', 'true');

			var $panel = $this.closest( '.glozin-content-preview-tabs' ).find( '.glozin-content-preview-tabs__image[data-key="'+key+'"]');

			$panel.siblings('.glozin-content-preview-tabs__image').attr('data-active', 'false');
			$panel.attr('data-active', 'true');
		});
	}

	onInit() {
		super.onInit();

		this.actionTabs();
	}
}

class GlozinProductHightlightSliderWidgetHandler extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                container: '.glozin-product-highlight-slider'
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');

        return {
            $container: this.$element.find(selectors.container)
        };

    }

    getProductSwiperInit() {
        const settings = this.getElementSettings();

		const shouldAutoplay = settings.autoplay === 'yes';
		const autoplaySettings = shouldAutoplay
		? {
			delay: settings.autoplay_speed || 3000,
			disableOnInteraction: false,
			}
		: false;

		const $container = this.elements.$container;

		const galleryBoxEl = $container.find('.glozin-product-highlight-slider__image').get(0);
		const galleryImageEl = $container.find('.glozin-product-highlight-slider__product').get(0);

        var galleryBox = new Swiper(this.elements.$container.find('.glozin-product-highlight-slider__image').get(0), {
            watchOverflow: true,
            watchSlidesVisibility: true,
            watchSlidesProgress: true,
            allowTouchMove: false,
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },
			loop: settings.infinite == 'yes' ? true : false,
			autoplay: autoplaySettings,
			speed: parseInt(settings.speed) || 800

        });
        var galleryImage = new Swiper(this.elements.$container.find('.glozin-product-highlight-slider__product').get(0), {
            watchOverflow: true,
            thumbs: {
                swiper: galleryBox
            },
            pagination: {
                el: this.elements.$container.find('.swiper-pagination').get(0),
                clickable: true
            },
			spaceBetween: 30,
			loop: settings.infinite == 'yes' ? true : false,
			autoplay: autoplaySettings,
			speed: parseInt(settings.speed) || 800
        });

		if (shouldAutoplay && settings.pause_on_hover === 'yes') {
			const stopAutoplay = () => {
				galleryBox.autoplay?.stop();
				galleryImage.autoplay?.stop();
			};

			const startAutoplay = () => {
				galleryBox.autoplay?.start();
				galleryImage.autoplay?.start();
			};

			galleryBoxEl.addEventListener('mouseenter', stopAutoplay);
			galleryBoxEl.addEventListener('mouseleave', startAutoplay);

			galleryImageEl.addEventListener('mouseenter', stopAutoplay);
			galleryImageEl.addEventListener('mouseleave', startAutoplay);
		}
    }

    onInit() {
        var self = this;
        super.onInit();

        self.getProductSwiperInit();
    }
}

class GlozinTiktokVideoCarouselWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				wrapper: '.glozin-tiktok-video-carousel',
				item: '.glozin-tiktok-lazy-wrapper',
			}
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings('selectors');
		return {
			$wrapper: this.$element.find(selectors.wrapper),
			$items: this.$element.find(selectors.item),
		};
	}

	bindEvents() {
		this.lazyLoadVideos();
	}

	lazyLoadVideos() {
		const observer = new IntersectionObserver((entries, observer) => {
			entries.forEach(entry => {
				if (entry.isIntersecting) {
					const el = entry.target;
					const src = el.getAttribute('data-src');

					const iframe = document.createElement('iframe');
					iframe.setAttribute('src', src);
					iframe.setAttribute('width', '100%');
					iframe.setAttribute('height', '739');
					iframe.setAttribute('style', 'display:block;visibility:unset;max-height:739px;');
					iframe.setAttribute('sandbox', 'allow-popups allow-popups-to-escape-sandbox allow-scripts allow-top-navigation allow-same-origin');

					el.innerHTML = '';
					el.appendChild(iframe);

					observer.unobserve(el);
				}
			});
		}, {
			rootMargin: '100px',
			threshold: 0.1,
		});

		this.elements.$items.each(function () {
			observer.observe(this);
		});
	}
}
class GlozinSlidesWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				wrapper: '.glozin-slides-elementor',
			}
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings('selectors');
		return {
			$wrapper: this.$element.find(selectors.wrapper),
		};
	}

	onInit() {
		super.onInit();
		this.updateFullScreenVariable();
	}

	updateFullScreenVariable() {
		const isFullScreen = this.getElementSettings('slides_full_screen_desktop') === 'yes';
		if (!isFullScreen) return;

		const getHeight = selector => {
			const el = document.querySelector(selector);
			return el ? el.offsetHeight : 0;
		};

		const headerHeight =
			getHeight('#wpadminbar') +
			getHeight('.topbar') +
			getHeight('.campaignbar') +
			getHeight('.site-header');

		this.elements.$wrapper.get(0).style.setProperty('--gz-slides-full-screen-height', headerHeight + 'px');
	}
}

class GlozinProductSpotlightGridWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				contentImage: '.glozin-product-spotlight__content-image',
				imageItem: '.glozin-product-spotlight__image-item',
				item: '.glozin-product-spotlight__item',
			}
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings('selectors');
		return {
			$contentImages: this.$element.find(selectors.contentImage),
			$imageItems: this.$element.find(selectors.imageItem),
		};
	}

	bindEvents() {
		const selectors = this.getSettings('selectors');
		const { $contentImages, $imageItems } = this.elements;

		$contentImages.on('mouseenter', function () {
			const $this = jQuery(this);
			const dataId = $this.data('id');

			$imageItems.removeClass('active');
			jQuery(selectors.item).removeClass('active');

			const $targetImage = $imageItems.filter(`[data-id="${dataId}"]`);
			$targetImage.addClass('active');

			const $parentItem = $this.closest(selectors.item);
			$parentItem.addClass('active');
		});
	}
}

jQuery( window ).on( 'elementor/frontend/init', () => {
	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-navigation-menu.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinToggleMobileWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-subscribe-group.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinToggleMobileWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-slides.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinCarouselWidgetHandler, { $element } );
		elementorFrontend.elementsHandler.addHandler( GlozinSlidesWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-categories-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinCarouselWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-testimonial-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinCarouselWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-shoppable-images-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinCarouselWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-icon-box-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinCarouselWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-banner-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinCarouselWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-tiktok-video-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinCarouselWidgetHandler, { $element } );
		elementorFrontend.elementsHandler.addHandler( GlozinTiktokVideoCarouselWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-posts-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinCarouselWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-image-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinCarouselWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-image-box-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinCarouselWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-code-discount.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinCodeDiscountWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-short-content.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinShortContentWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-lookbook-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinCarouselWidgetHandler, { $element } );
		elementorFrontend.elementsHandler.addHandler( GlozinHotspotWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-image-before-after.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinImageBeforeAfterWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-shoppable-video-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinCarouselWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-content-preview-tabs.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinContentPreviewTabsWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-product-highlight-slider.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinProductHightlightSliderWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-product-spotlight-grid.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinProductSpotlightGridWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-split-hero-slider.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinCarouselWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-testimonial-carousel-2.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinCarouselWidgetHandler, { $element } );
	} );
} );