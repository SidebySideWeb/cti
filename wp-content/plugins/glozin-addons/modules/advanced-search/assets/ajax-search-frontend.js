(function ($) {
	'use strict';

	var glozin = glozin || {};

	glozin.init = function () {
		if (glozinAjaxSearch.header_ajax_search != 'yes') {
			return;
		}

        this.instanceSearch();
		this.focusSearch();
    }

	glozin.focusSearch = function() {
		$(document.body).on('glozin_modal_opened', function(event, $target) {
			if( $target.length && $target.hasClass('search-modal') ) {
				$target.find('.search-modal__field').trigger( 'focus' );
			}
		});

		$( '.header-search .header-search__field' ).on( 'hover focus', function() {
			var $field = $( this );

			if ( ! $field.closest('.header-search').length ) {
				return;
			}

			if($field.closest('.header-search__form').hasClass('searched')) {
				$field.closest('.header-search__form').addClass('actived');
			}
		} );

		$( document.body ).on( 'click', 'div', function( event ) {
			var $target = $( event.target );

			if ( $target.is( '.header-search' ) || $target.closest( '.header-search' ).length || $target.closest( '.header-search__form' ).length ) {
				return;
			}

			$('.header-search__form').removeClass('actived');
		} );
	}

	glozin.instanceSearch = function () {
		var xhr = null,
			searchCache = {};

		$('.gz-instant-search__form').on('keyup', '.gz-instant-search__field', function (e) {
			var valid = false,
			$search = $(this);

			if (typeof e.which == 'undefined') {
				valid = true;
			} else if (typeof e.which == 'number' && e.which > 0) {
				valid = !e.ctrlKey && !e.metaKey && !e.altKey;
			}

			if (!valid) {
				return;
			}

			if (xhr) {
				xhr.abort();
			}

			var $currentForm = $search.closest('.search-modal__form'),
				$currentContent = $search.closest('.modal__container'),
				$results = $search.closest('.search-modal').find('.modal__content-results'),
				$suggestion = $search.closest('.search-modal').find('.modal__content-suggestion');

			if ( $search.closest('form').hasClass('header-search__form') ) {
				var $modal_search = $search.closest('body').find('.search-modal.modal--open');
				
				$currentForm = $search.closest('.header-search__form');
				$currentContent = $modal_search.find('.modal__container');
				$suggestion = $modal_search.find('.modal__content-suggestion');
				$results = $modal_search.find('.modal__content-results');

				if( $modal_search.find('.gz-instant-search__field').length ) {
					$modal_search.find('.gz-instant-search__field').val($search.val());
				}
			} else {
				if( $search.closest('body').find('.header-search__form .gz-instant-search__field').length ) {
					$search.closest('body').find('.header-search__form .gz-instant-search__field').val($search.val());
				}
			}

			$results.html('');

			if ($search.val().length < 2) {
				$currentForm.removeClass('searching searched actived found-products found-no-product invalid-length ');
				$currentContent.removeClass('searching searched actived found-products found-no-product invalid-length ');
				$results.hide();
				$suggestion.show();
				$('#search-modal .modal__footer').addClass('hidden');
			}

			search($currentForm);
		}).on('focusout', '.gz-instant-search__field', function () {
			var $search = $(this),
				$currentContent = $search.closest('.modal__container'),
				$currentForm = $search.closest('body').find('.header-search__form');

			if ( $search.closest('form').hasClass('header-search__form') ) {
				var $modal_search = $search.closest('body').find('.search-modal.modal--open'),
					$currentForm = $search.closest('.header-search__form');
					
				$currentContent = $modal_search.find('.modal__container');
			}

			if ($search.val().length < 2) {
				$currentForm.removeClass('searching searched actived found-products found-no-product invalid-length ');
				$currentContent.removeClass('searching searched actived found-products found-no-product invalid-length ');
			}
		}).on('click', '.close-search-results', function (e) {
			e.preventDefault();
			var $close = $(this);
			var $currentForm = $close.closest('.modal__container'),
				$suggestion = $currentForm.find('.modal__content-suggestion'),
				$results = $currentForm.find('.modal__content-results'),
				$searchField = $currentForm.find('.search-modal__field'),
				$_searchField = $close.closest('body').find('.header-search__form .gz-instant-search__field');
				$currentContent = $close.closest('body').find('.header-search__form');

			if ( $close.closest('form').hasClass('header-search__form') ) {
				var $modal_search = $close.closest('body').find('.search-modal.modal--open'),
					$currentContent = $modal_search.find('.modal__container');

				$currentForm = $close.closest('.header-search__form');
				$searchField = $currentForm.find('.header-search__field');
				$_searchField = $modal_search.find('.gz-instant-search__field');
				$suggestion = $modal_search.find('.modal__content-suggestion');
				$results = $modal_search.find('.modal__content-results');
			}

			$searchField.val('');
			$_searchField.val('');
			$currentForm.removeClass('searching searched actived found-products found-no-product invalid-length ');
			$currentContent.removeClass('searching searched actived found-products found-no-product invalid-length ');

			$results.html('');
			$results.hide();
			$suggestion.show();
		});

		/**
		 * Private function for search
		 */
		function search($currentForm) {
			var $search = $currentForm.find('.gz-instant-search__field'),
				keyword = $search.val(),
				$results = $currentForm.closest('.modal__container').find('.modal__content-results'),
				$suggestion = $currentForm.closest('.modal__container').find('.modal__content-suggestion'),
				$currentForm = $currentForm.closest('.modal__container'),
				$currentContent = $currentForm.closest('body').find('.header-search__form');

			if ( $search.closest('form').hasClass('header-search__form') ) {
				var $modal_search = $search.closest('body').find('.search-modal.modal--open');
				
				$currentForm = $search.closest('.header-search__form');
				$currentContent = $modal_search.find('.modal__container');
				$suggestion = $modal_search.find('.modal__content-suggestion');
				$results = $modal_search.find('.modal__content-results');
			}

			if (keyword.trim().length < 2) {
				$currentForm.removeClass('searching found-products found-no-product').addClass('invalid-length');
				$currentContent.removeClass('searching found-products found-no-product').addClass('invalid-length');
				return;
			}

			$currentForm.removeClass('found-products found-no-product').addClass('searching');
			$currentContent.removeClass('found-products found-no-product').addClass('searching');

			var keycat = keyword;

			if (keycat in searchCache) {
				var result = searchCache[keycat];

				$currentForm.removeClass('searching');
				$currentForm.addClass('found-products');
				$currentContent.removeClass('searching');
				$currentContent.addClass('found-products');
				$suggestion.hide();
				$results.html(result.products).show();


				$(document.body).trigger('glozin_ajax_search_request_success', [$results]);

				if( $results.find('.list-item-empty').length ) {
					$currentForm.addClass('found-no-product');
					$currentContent.addClass('found-no-product');
					$suggestion.show();
				}

				$currentForm.removeClass('invalid-length');
				$currentForm.addClass('searched actived');
				$currentContent.removeClass('invalid-length');
				$currentContent.addClass('searched actived');
			} else {
				var data = {
						'term': keyword,
						'ajax_search_number': glozinAjaxSearch.header_search_number,
					},
					ajax_url = glozinAjaxSearch.ajax_url.toString().replace('%%endpoint%%', 'glozin_instance_search_form');

				xhr = $.ajax({
					url: ajax_url,
					method: 'post',
					data: data,
					success: function (response) {
						var $products = response.data;
						$suggestion.hide();
						$currentForm.removeClass('searching');
						$currentForm.addClass('found-products');
						$currentContent.removeClass('searching');
						$currentContent.addClass('found-products');
						$results.html($products).show();
						$currentForm.removeClass('invalid-length');
						$currentContent.removeClass('invalid-length');

						$(document.body).trigger('glozin_ajax_search_request_success', [$results]);

						// Cache
						searchCache[keycat] = {
							found: true,
							products: $products
						};

						if( $results.find('.list-item-empty').length ) {
							$currentForm.addClass('found-no-product');
							$currentContent.addClass('found-no-product');
							$suggestion.show();
						}

						$currentForm.addClass('searched actived');
						$currentContent.addClass('searched actived');
					}
				});
			}
		}
	}

	/**
	 * Document ready
	 */
	$(function () {
		glozin.init();
	});

})(jQuery);