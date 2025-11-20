(function ($) {
	"use strict";

    $(function () {
		sidebarPanel();
    });

	function sidebarPanel() {
		var $sidebar = $( '.wcfmmp-store-page-wrap .sidebar, .wcfmmp-stores-listing .sidebar' );

		if ( ! $sidebar.length ) {
			return;
		}

		var $sidebarContent = $sidebar.find('.widget'),
			$sidebarPanel = $('#wcfm-sidebar-panel');

		if ( $( '.wcfmmp-stores-listing .sidebar' ).length ) {
			$sidebarContent = $sidebar.find('.wcfmmp-store-search-form');
		}

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
				$( document.body ).removeClass( 'wcfm-sidebar-panel-opened offcanvas-opened' );
				$(document.body).removeAttr('style');
            }

        }).trigger('resize');
	}


})(jQuery);