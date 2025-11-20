(function ($) {
	"use strict";

	function init() {
		var interval = glozinPVF.interval,
		    from = glozinPVF.from,
		    to   = glozinPVF.to;

		setInterval( function () {
			var number = Math.floor( ( Math.random() * to ) + from );

			number = number < from ? from : number;
			number = number > to ? to : number;

			$( '.glozin-people-view__numbers' ).text( number );
		}, interval );
	}

	/**
	 * Document ready
	 */
	$(function () {
		if ( typeof glozinPVF === 'undefined' ) {
			return false;
		}

		init();
    });

})(jQuery);