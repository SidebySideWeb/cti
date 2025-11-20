class GlozinProductRecentlyViewedWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				container: '.recently-viewed-products__elementor'
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$container: this.$element.find( selectors.container )
		};
	}

	bindEvents() {
		this.elements.$container.on( 'click', '.recently-viewed-products__clear-all', function(e) {
			e.preventDefault();

			let pastDate = new Date();
			pastDate.setTime(pastDate.getTime() - (7 * 24 * 60 * 60 * 1000));
			document.cookie = "woocommerce_recently_viewed=; path=/; expires=" + pastDate.toUTCString() + ";";

			jQuery(this).addClass('d-none');
			jQuery(this).closest('.recently-viewed-products__elementor').find('.recently-viewed-products__no-products').removeClass('d-none');
			jQuery(this).closest('.recently-viewed-products__elementor').find('.recently-viewed-products').remove();
			jQuery(this).closest('.recently-viewed-products__elementor').find('.recently-viewed-products__empty').removeClass('d-none');
		});
	}

	loadProductsAjax(page, container) {
		var products = container.find('ul.products'),
			paginationButton = container.find('.woocommerce-pagination-button');

		var settings = container.data('settings');
		var ajax_url = this.getAJAXURL();

		page = page || 1;

		jQuery.post(
            ajax_url.toString().replace(  '%%endpoint%%', 'load_recently_viewed_products_elementor' ),
            {
                limit: settings.limit,
                columns: settings.columns,
				pagination: settings.pagination,
				page: page
            },
            function (response) {
                if (!response) {
                    return;
                }

				var $response = jQuery(response),
					productsResponse = $response.find('li.product'),
					paginationResponse = $response.find('.woocommerce-pagination-button'),
					paginationPage = paginationResponse.data('page');

				if (page == 1) {
					container.removeClass('ajax-loading').prepend(response);
				} else {
					products.append(productsResponse);
					if (paginationResponse.length) {
						paginationButton.removeClass('loading');
						paginationButton.attr('data-page', paginationPage);
					} else {
						paginationButton.remove();
					}
				}
				
				jQuery(document.body).trigger('glozin_get_products_ajax_loaded');
            }
        );
	}

	loadRecentlyViewedProducts() {
		var self = this;
		var container = this.elements.$container.find('.recently-viewed-products');

		if ( ! container.hasClass('has-ajax') ) {
			return;
		}
		container.addClass( 'ajax-loading' );
		this.loadProductsAjax(1, container);
	}

	pageLoadMore() {
		var self = this,
			container = this.elements.$container.find('.recently-viewed-products');

		container.on('click', '.woocommerce-pagination-button', function(e) {
			e.preventDefault();

			var page = jQuery(this).attr('data-page');
			jQuery(this).addClass('loading');
			self.loadProductsAjax(page, container);
		});
	}

	getAJAXURL() {
		var ajax_url = '';
		if (typeof glozinData !== 'undefined') {
			ajax_url = glozinData.ajax_url;
		} else if (typeof wc_add_to_cart_params !== 'undefined') {
			ajax_url = wc_add_to_cart_params.wc_ajax_url;
		}

		return ajax_url;
	}

	async onInit() {
		super.onInit();
		this.loadRecentlyViewedProducts();
		this.pageLoadMore();
    }
}

jQuery( window ).on( 'elementor/frontend/init', () => {
	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-product-recently-viewed.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinProductRecentlyViewedWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/glozin-product-recently-viewed-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( GlozinProductRecentlyViewedWidgetHandler, { $element } );
	} );
} );
