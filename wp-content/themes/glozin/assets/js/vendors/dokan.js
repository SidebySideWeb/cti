(function ($) {
	"use strict";

    $(function () {
		dropdownCategoriesWidget();
		sidebarPanel();
		formFieldFocus();
    });

	function dropdownCategoriesWidget() {
		if ( ! $( '.dokan-store-sidebar' ).length ) {
			return;
		}

		var $widget = $( '.dokan-category-menu, .dokan-store-menu' );

		$widget.find('li.has-children').each( function() {
			if ( $(this).find('ul.children').length > 0 ) {
				$(this).find('ul.children').hide();
				$(this).find('a').removeAttr('style');
				$(this).append('<span class="gz-product-cat-item-toggle"></span>');
			}
		});

		setTimeout(function () {
			$widget.find('li.has-children').each( function() {
				$(this).find('ul.children').addClass('list-children');
			});
		}, 1000);

		$widget.on( 'click', 'li.has-children > .gz-product-cat-item-toggle', function (e) {
			e.preventDefault();

			var $item = $( this ).closest('li.has-children');

			$item.toggleClass( 'active' ).siblings().removeClass( 'active' );

			// If this is sub-menu item
			$item.children( 'ul.children' ).slideToggle();

        });
	};

	function sidebarPanel() {
		var $sidebar = $( '.dokan-store-sidebar' );

		if ( ! $sidebar.length ) {
			return;
		}

		var $sidebarContent = $('.dokan-store-sidebar').find('.dokan-widget-area'),
			$sidebarPanel = $('#dokan-sidebar-panel');

		$sidebar.prepend($sidebarPanel);

		$sidebarPanel.find('.sidebar__content').html( $sidebarContent );

		$(window).on('resize', function () {
            if ( $(window).width() < 1025 ) {
				$sidebarPanel.addClass('offscreen-panel');
				$sidebarPanel.find('.sidebar__backdrop').addClass('panel__backdrop');
				$sidebarPanel.find('.sidebar__container').addClass('panel__container');
				$sidebarPanel.find('.sidebar__header').addClass('panel__header');
				$sidebarPanel.find('.sidebar__content').addClass('panel__content');
            } else {
				$sidebarPanel.removeClass('offscreen-panel');
				$sidebarPanel.find('.sidebar__backdrop').removeClass('panel__backdrop');
				$sidebarPanel.find('.sidebar__container').removeClass('panel__container');
				$sidebarPanel.find('.sidebar__header').removeClass('panel__header');
				$sidebarPanel.find('.sidebar__content').removeClass('panel__content');
				$sidebarPanel.removeClass( 'offscreen-panel--open' );
				$( document.body ).removeClass( 'dokan-sidebar-panel-opened offcanvas-opened' );
				$(document.body).removeAttr('style');
            }

        }).trigger('resize');
	}

	function formFieldFocus() {
		$( '.woocommerce-account' )
		.on( 'keyup focus change', '.form-row .input-text, .form-row input[type=text]', function() {
			$( this ).closest( '.form-row' ).addClass( 'focused' );
		} )
		.on( 'blur','.form-row .input-text, .form-row input[type=text]', function() {
			if ( $( this ).val() === '' ) {
				$( this ).closest( '.form-row' ).removeClass( 'focused' );
			}
		} )
		.find('.form-row').each( function () {
			var $input = $(this).find( '.input-text, input[type=text]' );
			if ( $input.val() !== '') {
				$( this ).addClass( 'focused' );
			}

			$input.on('animationstart', function(e) {
				if (e.originalEvent.animationName === 'autofill-animation') {
					$input.closest('.form-row').addClass('focused');
				}
		 	} );

		})

		$(window).on("load", function () {
			$( '.woocommerce-account .form-row .input-text' ).map(function() {
				if ( $(this).val().length !== 0) {
					$(this).closest('.form-row').addClass( 'focused' );
				}
			}).get();
		});
	};


})(jQuery);