(function ($) {
    'use strict';

    var glozin = glozin || {};
    glozin.init = function () {
        glozin.$body = $(document.body),
            glozin.$window = $(window),
            glozin.$header = $('#site-header');

        // Catalog
		this.shopHeaderMore();

		this.catalogFiltersHorizontal();
        this.changeCatalogElementsFiltered();

        this.catalogOrderBy();
		this.catalogView();
       	this.loadMoreProducts();
		this.loadProductsbyViewAJAX();

		this.sidebarPanel();
    };

	/**
 	 * Shop Header More
 	 */
	glozin.shopHeaderMore = function () {
		$(".shop-header__description-inner").each(function () {
			var $content = $(this);
			var defaultHeight = $content.outerHeight();

			$content.data("defaultHeight", defaultHeight);

			$content.css({
				"max-height": defaultHeight + "px"
			});

			if( $content[0].scrollHeight > $content[0].clientHeight ) {
				$content.siblings( '.shop-header__more-wrapper' ).removeClass( 'hidden' );
			}
		});

		$( document.body ).on( 'click', '.shop-header__more', function(e) {
			e.preventDefault();

			var $settings = $(this).data( 'settings' ),
				$wrapper   = $(this).closest( '.shop-header__more-wrapper' ),
				$content  = $wrapper.siblings('.shop-header__description-inner'),
				$more     = $settings.more,
				$less     = $settings.less;

				if ($content.length === 0) {
					return;
				};

				if (!$content.data("defaultHeight")) {
					$content.data("defaultHeight", $content.outerHeight());
				}

				var defaultHeight = $content.data("defaultHeight");

			if( $wrapper.hasClass( 'less' ) ) {
				$wrapper.removeClass( 'less' );
				$(this).text( $more );
				$content.css({
					"max-height": defaultHeight + "px",
				});
			} else {
				$wrapper.addClass( 'less' );
				$(this).text( $less );
				$content.css( '-webkit-line-clamp', 'inherit' );
				$content.css({
					"max-height": $content[0].scrollHeight + "px",
				});
			}
		});
	}

	glozin.catalogFiltersHorizontal = function () {
		glozin.$body.on('click', '.catalog-filters-horizontal .products-filter__filter .filter-name', function (e) {
			e.preventDefault();

			var $currentFilter = $(this).closest('.products-filter__filter');

			$('.products-filter__filter').not($currentFilter).removeClass('glozin-active');
			$currentFilter.toggleClass('glozin-active');

			$('.gz-widget-heading').removeClass('glozin-active');
		}).on('click', '.catalog-filters-horizontal .gz-widget-heading', function (e) {
			e.preventDefault();

			var $currentFilter = $(this);

			$('.gz-widget-heading').not($currentFilter).removeClass('glozin-active');
			$currentFilter.toggleClass('glozin-active');

			$('.products-filter__filter').removeClass('glozin-active');
		}).on( 'keyup', function ( e ) {
			if ( e.keyCode === 27 ) {
				$('.products-filter__filter').removeClass('glozin-active');
				$('.gz-widget-heading').removeClass('glozin-active');
			}
		} ).on('click', function (e) {
			var $target = $( e.target );

			if ($target.closest('.products-filter__filter, .gz-widget-heading').length) {
				return;
			}

			$('.products-filter__filter').removeClass('glozin-active');
			$('.gz-widget-heading').removeClass('glozin-active');
		});
	}

    glozin.changeCatalogElementsFiltered = function () {
		glozin.$body.on('glozin_products_filter_before_send_request', function (e, response) {
			$('.products-filter__filter').removeClass('glozin-active');
			$('.gz-widget-heading').removeClass('glozin-active');
			setTimeout(function() {
				$('.catalog-filters-sidebar .sidebar__button-close').trigger('click');
			}, 300);

			$('#glozin-shop-content').addClass('loading');
        } );

		var  $page_header = glozin.$body.find('#page-header'),
			$catalog_toolbar = glozin.$body.find('.catalog-toolbar'),
			$activeFilters = glozin.$body.find( '.catalog-toolbar__active-filters' ),
			$primaryFilter = $( '.catalog-toolbar__filters-actived' ),
			$activeFilters = $( '.catalog-toolbar__active-filters' ),
			$panelFilter = $( '.catalog-filters-sidebar' ),
			$widgetFilter = $panelFilter.find( '.products-filter__activated-items' ),
			$removeAll = '<a href="#" class="remove-filtered remove-filtered-all">' + $primaryFilter.data( 'clear-text' ) + '</a>';

		filterActivated();
        glozin.$body.on('glozin_products_filter_request_success', function (e, response) {
            var $html = $(response);
			changeElements( $html );
			changeFilterActivated( $html );
			glozin.scrollTopFilterAJAX();

        });

		function changeElements( $html ) {
			if( $html.find('#page-header').length ) {
                $page_header.replaceWith( $html.find( '#page-header' ) );
            }

			if( $html.find('.catalog-toolbar__result-count').length ) {
				$catalog_toolbar.find('.catalog-toolbar__result-count').replaceWith( $html.find( '.catalog-toolbar__result-count' ) );
			}

			if( $html.find('.catalog-toolbar__orderby-form').length ) {
				$catalog_toolbar.find('.catalog-toolbar__orderby-form').replaceWith( $html.find( '.catalog-toolbar__orderby-form' ) );

				// Active Item
				var activeVal = $catalog_toolbar.find('.woocommerce-ordering option:selected').val();
				$catalog_toolbar.find('.catalog-toolbar__orderby-list a[data-id='+ activeVal +']').addClass('selected');
			}
		}

		function filterActivated() {
			if( $widgetFilter.html() && $widgetFilter.html().trim() ) {
				$primaryFilter.html('');
				$primaryFilter.removeClass( 'active' );
				$primaryFilter.prepend( $widgetFilter.html() + $removeAll );
				$primaryFilter.addClass( 'active' );
				$activeFilters.addClass( 'actived' );
				$activeFilters.removeClass( 'hidden' );
			}

			$primaryFilter.on( 'click', '.remove-filtered:not(.remove-filtered-all)', function (e) {
				var value = $(this).data( 'value' );

				if ( value !== 'undefined' ) {
					$panelFilter.find( ".remove-filtered[data-value='" + value + "']" ).trigger( 'click' );
				}

				$('#glozin-shop-content').addClass('loading');

				return false;
			});

			$primaryFilter.on( 'click', '.remove-filtered-all', function (e) {
				e.preventDefault();
				$('#glozin-shop-content').addClass('loading');
				$panelFilter.find( '.products-filter__activated-heading .reset-button' ).trigger( 'click' );
			});

			$(document.body).on( 'glozin_products_filter_reseted', function() {
				$('#site-content .woocommerce-notices-wrapper').fadeOut();
			});
		}

		function changeFilterActivated ($response) {
			var $widgetNewFilter = $response.filter( '.catalog-filters-sidebar').find( '.products-filter__activated-items' ),
				$widgetNewFilter_builder = $response.find( '.catalog-filters-sidebar .products-filter__activated-items' );

			if( $widgetNewFilter.children().length > 0 ) {
				if( $widgetNewFilter.html() && $widgetNewFilter.html().trim() ) {
					$primaryFilter.removeClass('hidden');
					$primaryFilter.html('');
					$primaryFilter.removeClass( 'active' );
					$primaryFilter.prepend( $widgetNewFilter.html() + $removeAll );
					$primaryFilter.addClass( 'active' );
					$activeFilters.addClass( 'actived' );
					$activeFilters.removeClass( 'hidden' );
				}
			} else if( $widgetNewFilter_builder.children().length > 0 ) {
				if( $widgetNewFilter_builder.html() && $widgetNewFilter_builder.html().trim() ) {
					$primaryFilter.removeClass('hidden');
					$primaryFilter.html('');
					$primaryFilter.removeClass( 'active' );
					$primaryFilter.prepend( $widgetNewFilter_builder.html() + $removeAll );
					$primaryFilter.addClass( 'active' );
					$activeFilters.addClass( 'actived' );
					$activeFilters.removeClass( 'hidden' );
				}
			} else {
				if( $primaryFilter.hasClass( 'active' ) ) {
					$primaryFilter.html('');
					$primaryFilter.removeClass( 'active' );
					$activeFilters.removeClass( 'actived' );
					$activeFilters.addClass( 'hidden' );
				}
			}

		};



    };

	glozin.scrollTopFilterAJAX = function() {
		var $offset = 0;

		if( ! $("#glozin-shop-content").length ) {
			return;
		}

		if ( glozin.$window.width() > 1200 ) {
			if ( $('#wpadminbar').length ) {
				$offset += $('#wpadminbar').outerHeight();
			}

			if ( $('.site-header__desktop').hasClass('glozin-header-sticky') ) {
				if ( $('.site-header__desktop').hasClass('header-sticky--both') ) {
					$offset += $('.site-header__desktop').outerHeight();
				} else {
					$offset += $('.site-header__desktop').find('.header-sticky').outerHeight();
				}
			}
		} else {
			if ( $('.site-header__mobile').hasClass('glozin-header-mobile-sticky') ) {
				if ( $('.site-header__mobile').hasClass('header-sticky--both') ) {
					$offset += $('.site-header__mobile').outerHeight();
				} else {
					$offset += $('.site-header__mobile').find('.header-mobile-sticky').outerHeight();
				}
			}
		}

		var hasSticky = $("#primary").hasClass('position-sticky-lg');
		if( hasSticky ) {
			$("#primary").removeClass('position-sticky-lg');
		}
		$('html,body').stop().animate({
			scrollTop: $("#primary").length ? $("#primary").offset().top - $offset - 30 : $("#glozin-shop-content").offset().top - $offset - 30
		}, 300, function () {
			if( hasSticky ) {
				$("#primary").addClass('position-sticky-lg');
			}
		});

		$('#glozin-shop-content').delay(500).queue(function(next) {
			$(this).removeClass('loading');
			next();
		});
	};

	glozin.catalogView = function() {
		var $toolbarView = $('#glozin-toolbar-view'),
			$products = $('#glozin-shop-content').find('ul.products'),
			productsLayout = $products.data('layout');

			glozin.$body.on('click', '.gz-shop-view-item', function(e){
				e.preventDefault();

				var $this = $(this),
					$url = $this.attr('href'),
					$products = $this.closest('.site-content').find('#glozin-shop-content').find('ul.products');

				$this.siblings().removeClass('current');
				$(this).addClass('current');

				var view = 'grid',
					column = $this.data('column'),
					class_list = '',
					class_grid = '';
				switch( column ) {
					case 1:
						view = 'list';
						class_list = 'columns-1 product-card-layout-list';
						class_grid = 'columns-3 columns-2 columns-4 product-card--' + productsLayout;
						$products.removeClass(class_grid).addClass(class_list);
						$toolbarView.removeClass('view-grid').addClass('view-list');
						break;
					case 2:
					case 3:
					case 4:
						view = 'grid';
						class_grid = 'columns-' + column + ' product-card-layout-' + productsLayout;
						class_list = 'product-card-layout-list columns-1 columns-2 columns-3 columns-4';
						$products.removeClass(class_list).addClass(class_grid);
						$toolbarView.removeClass('view-list').addClass('view-grid');
						break;
				}

				document.cookie = 'catalog_view=' + view + ';domain=' + window.location.host + ';path=/';

				window.history.pushState( null, '', $url );

			});
	};

	glozin.loadProductsbyViewAJAX = function () {
		var $toolbarView = $('#glozin-toolbar-view'),
			$shopContent = $('#glozin-shop-content');

		glozin.$body.on('submit', '.catalog-toolbar__orderby-form', function(e) {
			e.preventDefault();

			var $form = $(this),
				action = $form.attr('action') || window.location.href.split('?')[0],
				url = action,
				separator = action.indexOf('?') !== -1 ? '&' : '?',
				$inputs = $form.find('input, select'),
				params = {};

			params = $inputs.filter(function () {
				return this.value !== '' && this.name !== '';
			}).serializeObject();

			if (params.paged && params.paged === '1') {
				delete params.paged;
			}

			if ( _.size( params ) > 0 ) {
				url += separator + $.param(params, true);
			}

			loadProducts( url );
		});

		function loadProducts( $url ) {
			$shopContent.addClass('loading');
			$(document.body).trigger('glozin_progress_bar_start');
			$.get( $url, function( response ) {
				var $shopContentResponse = $(response).find('.glozin-shop-content'),
					$productsResponse = $shopContentResponse.find('ul.products'),
					$navigationResponse = $(response).find('.woocommerce-pagination').html(),
					$toolbarViewResponse = $(response).find('.glozin-toolbar-view').html();

				$(document.body).trigger('glozin_progress_bar_update', [85]);

				let delay = 0.5;
				$productsResponse.find('li.product').each( function( i, product ) {
					$(product).addClass('gz-fadeinup');
					$(product).css( '--gz-fadeinup-delay', delay + 's' );
					delay = delay + 0.1;
				});

				$shopContent.find('ul.products').attr( 'class', $productsResponse.attr( 'class' ) );
				$shopContent.find('ul.products').html( $productsResponse.html() );
				$toolbarView.html( $toolbarViewResponse );
				$shopContent.find('.woocommerce-pagination').html( $navigationResponse );
				$shopContent.removeClass('loading');
				glozin.scrollTopFilterAJAX();

				glozin.$body.trigger( 'glozin_get_products_ajax_loaded' );

				window.history.pushState( null, '', $url );

				$(document.body).trigger('glozin_progress_bar_complete');

			});
		}

	};

    glozin.catalogOrderBy = function () {
		var $selector = $('.catalog-toolbar__orderby-form'),
			$orderForm = $('.catalog-toolbar__item .woocommerce-ordering');

		glozin.$body.on('click', '.catalog-toolbar__orderby-default', function (e) {
			e.preventDefault();

			var $currentOrderForm = $(this).closest('.catalog-toolbar__orderby-form');

			$currentOrderForm.toggleClass('glozin-active');
		}).on('keyup', function (e) {
			if (e.keyCode === 27) {
				$selector.removeClass('glozin-active');
			}
		}).on('click', function (e) {
			var $target = $(e.target);

			if ($target.parent('.catalog-toolbar__orderby-form').length) {
				return;
			}

			$(this).closest('.catalog-toolbar__orderby-form').removeClass('glozin-active');
		});

		glozin.$body.on('click', '.catalog-toolbar__orderby-item', function (e) {
            e.preventDefault();

			var value = $(this).data('id'),
				text = $(this).text(),
				$selector = $(this).closest('.catalog-toolbar__orderby-form'),
				$orderForm = glozin.$body.find('.woocommerce-ordering'),
				$defaultName = $selector.find('.catalog-toolbar__orderby-default-name'),
				$self = $(this);

			// Select content form order
			$orderForm.find('option:selected').attr("selected", false);
			$orderForm.find('option[value='+ value +']').attr("selected", "selected");

			$defaultName.text( text );

			$('.catalog-toolbar__orderby-form').removeClass('glozin-active');

			$orderForm.trigger( 'submit' );

			if (glozin.$window.width() < 768) {
				$('.popover .popover__button-close').trigger('click');
			}

			setTimeout(function () {
				// Click selectd item popup order list
				$selector.find('.catalog-toolbar__orderby-list .selected').removeClass('selected');
				$self.addClass( 'selected' );
			}, 100);
        });

		// Active Item
		var activeVal = $orderForm.find('option:selected').val();
		$selector.find('.catalog-toolbar__orderby-list a[data-id='+ activeVal +']').addClass('selected');
    };

     /**
	 * Ajax load more products.
	 */
	glozin.loadMoreProducts = function() {
		// Infinite scroll.
		if ( $( '.woocommerce-pagination' ).hasClass( 'woocommerce-pagination--infinite' ) ) {
			var waiting = false,
				endScrollHandle;

			$( window ).on( 'scroll', function() {
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
			var $navigation = $( '.woocommerce-pagination.woocommerce-pagination--ajax' ),
				$button = $( '.woocommerce-pagination-button', $navigation );

			if ( glozinIsVisible( $navigation ) && $button.length && !$button.hasClass( 'loading' ) ) {
                $button.addClass( 'loading' );

				loadProducts( $button, function( respond ) {
					$button = $navigation.find( '.woocommerce-pagination-button' );
				});
			}
		}

		//Load More
		if ( $( '.woocommerce-pagination' ).hasClass( 'woocommerce-pagination--loadmore' ) ) {
			glozin.$body.on( 'click', '.woocommerce-pagination.woocommerce-pagination--loadmore .woocommerce-pagination-button', function (event) {
				event.preventDefault();
				loadMore();
			});
		}

		function loadMore() {
			var $navigation = $( '.woocommerce-pagination.woocommerce-pagination--ajax' ),
				$button = $( '.woocommerce-pagination-button', $navigation );

			if ( glozinIsVisible( $navigation ) && $button.length && !$button.hasClass( 'loading' ) ) {
                $button.addClass( 'loading' );

				loadProducts( $button, function( respond ) {
					$button = $navigation.find( '.woocommerce-pagination-button' );
				});
			}
		}

		/**
		 * Ajax load products.
		 *
		 * @param jQuery $el Button element.
		 * @param function callback The callback function.
		 */
		function loadProducts( $el, callback ) {
			var $nav = $el.closest( '.woocommerce-pagination' ),
				url = $el.attr( 'href' ),
				currentProducts = $el.closest('.glozin-shop-content').find('ul.products').children().length,
				bannerProducts = $el.closest('.glozin-shop-content').find('li.gz-product-grid-banner');
			$(document.body).trigger('glozin_progress_bar_start');
			$.get( url, function( response ) {
				var $content = $( '.glozin-shop-content', response ),
					$list = $( 'ul.products', $content ),
					$products = $list.children(),
					$newNav = $( '.woocommerce-pagination.woocommerce-pagination--ajax', $content ),
					numberPosts = $products.length + currentProducts,
					$found = $('.gz-posts-found');

				$products.appendTo( $nav.parent().find( 'ul.products' ) );

				if ( $newNav.length ) {
					$el.replaceWith( $( 'a', $newNav ) );
				} else {
					$nav.fadeOut( function() {
						$nav.remove();
					} );
				}

				if ( 'function' === typeof callback ) {
					callback( response );
				}

				$products.addClass( 'gz-fadeinup gz-animated' );

				let delay = 0.5;
				$products.each( function( i, item ) {
					jQuery(item).css( '--gz-fadeinup-delay', delay + 's' );
					delay = delay + 0.1;
				});

				glozin.$body.trigger( 'glozin_get_products_ajax_loaded', [$products, true] );

				if( $products.hasClass( 'gz-animated' ) ) {
					setTimeout( function() {
						$products.removeClass( 'gz-animated' );
					}, 10 );
				}
				$el.removeClass( 'loading' );
				numberPosts = bannerProducts.length ? numberPosts - 1 : numberPosts;
				$found.find('.current-post').html(' ' + numberPosts);
				postFound();

				if ( glozinData.shop_nav_ajax_url_change ) {
					window.history.pushState( null, '', url );
				}

				$(document.body).trigger('glozin_progress_bar_complete');
			});
		}

		function postFound() {
			var $found = $('.gz-posts-found__inner'),
				$foundEls = $found.find('.count-bar'),
				$current = $found.find('.current-post').html(),
				$total = $found.find('.found-post').html(),
				pecent = ($current / $total) * 100;

			$foundEls.css('width', pecent + '%');
		}
	};

	glozin.sidebarPanel = function () {
		if ( $('.glozin-catalog-page').hasClass('popup') ) {
			return;
		}

        var $selector = $('.glozin-catalog-page #filter-sidebar-panel');

        glozin.$window.on('resize', function () {
            if (glozin.$window.width() > 1024) {
                if ( $selector.length > 0 && $selector.hasClass('offscreen-panel') ) {
                    $selector.removeClass('offscreen-panel').removeAttr('style');
                }
            } else {
				if( $selector.length > 0 ) {
                	$selector.addClass('offscreen-panel');
				}
            }

        }).trigger('resize');
    };

    /**
     * Document ready
     */
    $(function () {
        glozin.init();
    });

})(jQuery);