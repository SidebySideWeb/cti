class GlozinProductGridHandler extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                container: '.glozin-product-grid'
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');

        return {
            $container: this.$element.find(selectors.container)
        };

    }

    loadProductsGrid() {
		var self = this;

        // Load Products
        this.elements.$container.on('click', 'a.ajax-load-products', function (e) {
            e.preventDefault();

            var $el = jQuery(this);

            if ($el.hasClass('loading')) {
                return;
            }

            $el.addClass('loading');

            self.loadProducts($el);
        });
    };

    loadProductsInfinite() {
		var self = this;
		if ( ! this.elements.$container.find( '.woocommerce-pagination' ).hasClass( 'woocommerce-pagination--infinite' ) ) {
			return;
		}

		var $el = this.elements.$container.find('.woocommerce-pagination-button'),
			waiting = false;

			jQuery(window).on('scroll', function () {
				if (waiting || !$el.length || !$el.is(':visible')) {
					return;
				}

				var buttonOffset = $el.offset().top,
					windowHeight = jQuery(window).height(),
					scrollPosition = jQuery(window).scrollTop();

				if (scrollPosition + windowHeight >= buttonOffset) {
					waiting = true;

					if (!$el.hasClass('loading')) {
						$el.addClass('loading');
						self.loadProducts($el);
					}

					setTimeout(function () {
						waiting = false;

						if (!$el.is(':visible')) {
							jQuery(window).off('scroll');
						}
					}, 300);
				}
			});

    };

	loadProducts($el) {
		var ajax_url = '';
		if (typeof glozinData !== 'undefined') {
			ajax_url = glozinData.ajax_url;
		} else if (typeof wc_add_to_cart_params !== 'undefined') {
			ajax_url = wc_add_to_cart_params.wc_ajax_url;
		}

        const settings = this.getElementSettings();

		if( ! ajax_url ) {
			return;
		}

		jQuery.post(
			ajax_url.toString().replace(  '%%endpoint%%', 'glozin_elementor_load_products' ),
			{
				page: $el.attr('data-page'),
				settings: settings
			},
			function (response) {
				if ( ! response ) {
					return;
				}

				$el.removeClass('loading');

				var $data = jQuery(response.data),
					$products = $data.find('li.product'),
					$container = $el.closest('.glozin-product-grid'),
					$grid = $container.find('ul.products'),
					$page_number = $data.find('.page-number').data('page');

				// If has products
				if ($products.length) {
					$products.addClass( 'gz-fadeinup gz-animated' );

					let delay = 0.5;
					$products.each( function( i, product ) {
						jQuery(product).css( '--gz-fadeinup-delay', delay + 's' );
						delay = delay + 0.1;
					});

					$grid.append($products);

					if ($page_number == '0') {
						$el.remove();
					} else {
						$el.attr('data-page', $page_number);
					}
				}

				if( response.success ) {
					if( $products.hasClass( 'gz-animated' ) ) {
						setTimeout( function() {
							$products.removeClass( 'gz-animated' );
						}, 10 );
					}
				}

				jQuery(document.body).trigger('glozin_products_loaded', [$products, true]);
			}
		);
	}

	lazyLoadImage() {
		const $lazyLoadImage = this.$element.find('.gz-lazy-load-image');

		$lazyLoadImage.each(function () {
			const $img = jQuery(this);
			const $parent = $img.parent();

			$parent.imagesLoaded(function () {
				$parent.closest('.gz-lazy-load').removeClass('gz-lazy-load');
				$parent.trigger('glozin_lazy_load_image_loaded');
			});
		});
	}

    onInit() {
        var self = this;
        super.onInit();

        self.loadProductsGrid();
        self.loadProductsInfinite();

		jQuery(document.body).on('glozin_products_loaded', (event, $products, isSuccess) => {
			self.lazyLoadImage();
		});
    }
}

jQuery( window ).on( 'elementor/frontend/init', () => {
	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-product-grid.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinProductGridHandler, { $element } );
	} );
} );
