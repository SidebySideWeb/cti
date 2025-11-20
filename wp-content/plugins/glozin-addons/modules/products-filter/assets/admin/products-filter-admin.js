jQuery( document ).ready( function( $ ) {
	'use strict';

	var wp = window.wp,
		data = window.glozin_products_filter_params,
		$body = $( document.body ),
		template = wp.template( 'glozin-products-filter' );

	// Change the active section.
	$body.on( 'click', '.glozin-products-filter-form__sub-nav button', function( event ) {
		event.preventDefault();

		var $button = $( this );

		if ( $button.hasClass( 'active' ) ) {
			return;
		}

		$button
			.addClass( 'active' )
			.siblings()
			.removeClass( 'active' )
			.parent()
			.siblings( '.glozin-products-filter-form__section[data-section="' + $button.data( 'section' ) + '"]' )
			.addClass( 'active' )
			.siblings( '.glozin-products-filter-form__section' )
			.removeClass( 'active' );

		$button.closest( '.widget' ).data( 'active_section', $button.data( 'section' ) );
	} );

	// Reopen active section after saved.
	$( document ).on( 'widget-updated', function( event, widgetContainer ) {
		var activeSection = widgetContainer.data( 'active_section' );

		if ( ! activeSection ) {
			return;
		}

		widgetContainer
			.find( '.glozin-products-filter-form__sub-nav button[data-section="' + activeSection + '"]' )
			.addClass( 'active' )
			.siblings()
			.removeClass( 'active' );

		widgetContainer
			.find( '.glozin-products-filter-form__section[data-section="' + activeSection + '"]' )
			.addClass( 'active' )
			.siblings( '.glozin-products-filter-form__section' )
			.removeClass( 'active' );
	} );

	// Toggle a filter's fields.
	$body.on( 'click', '.glozin-products-filter-form__filter-title, .glozin-products-filter-form__filter-toggle', function( event ) {
		event.preventDefault();

		$( this )
			.closest( '.glozin-products-filter-form__filter' )
			.toggleClass( 'open' )
			.children( '.glozin-products-filter-form__filter-options' )
			.toggle();
	} );

	// Add a new filter.
	$body.on( 'click', '.glozin-products-filter-form__add-new', function( event ) {
		event.preventDefault();

		var $button = $( this ),
			$filters = $button.closest( '.glozin-products-filter-form__section' ).children( '.glozin-products-filter-form__filter-fields' ),
			$title = $button.closest( '.widget-content' ).find( 'input' ).first();

		data.name = $button.data( 'name' );
		data.count = $button.data( 'count' );

		$button.data( 'count', data.count + 1 );
		$filters.append( template( data ) );
		$filters.trigger( 'appended' );
		$title.trigger( 'change' ); // Support customize preview.
	} );

	// Remove a filter.
	$body.on( 'click', '.glozin-products-filter-form__remove-filter', function( event ) {
		event.preventDefault();

		var $button = $( this ),
			$filters = $button.closest( '.glozin-products-filter-form__filter-fields' );

		$button
			.closest( '.glozin-products-filter-form__filter' )
			.hide()
			.remove();

		$filters
			.trigger( 'truncated' )
			.closest( '.widget-content' )
			.find( 'input' )
			.first()
			.trigger( 'change' );
	} );

	// Toggle the message.
	$body.on( 'appended truncated', '.glozin-products-filter-form__filter-fields', function( event ) {
		var $filters = $( this ).children();

		if ( $filters.length ) {
			$( this ).siblings( '.glozin-products-filter-form__message' ).addClass( 'hidden' );
		} else {
			$( this ).siblings( '.glozin-products-filter-form__message' ).removeClass( 'hidden' );
		}
	} );

	// Live update for the filter title.
	$body.on( 'input', '.glozin-products-filter-form__filter-option[data-option="filter:name"] input', function() {
		$( this ).closest( '.glozin-products-filter-form__filter' ).find( '.glozin-products-filter-form__filter-title' ).text( this.value );
	} ).on( 'change', '.glozin-products-filter-form__filter-option[data-option="filter:source"] select', function() {
		var $filter = $( this ).closest( '.glozin-products-filter-form__filter' );

		if ( ! $( '.glozin-products-filter-form__filter-option[data-option="filter:name"] input', $filter ).val() ) {
			$( '.glozin-products-filter-form__filter-title', $filter ).text( this.options[ this.selectedIndex ].innerHTML );
		}
	} );

	// Change display options.
	$body.on( 'change', '.glozin-products-filter-form__filter-options [data-option="filter:source"] select', function() {
		var $input = $( this ),
			source = $input.val();

		var options = source in data.display ? data.display[source] : data.display.default;

		$input.closest( '.glozin-products-filter-form__filter-options' ).find( '[data-option="filter:display"] select' ).html( function() {
			var html = '';

			for ( var option in options ) {
				html += '<option value="' + option + '">' + options[option] + '</option>';
			}

			return html;
		} ).prop( 'selectedIndex', 0 ).trigger( 'change' );
	} );

	/**
	 * Toggle fields
	 *
	 * Add a listener to the "change" event of inputs, then lookup for fields that denpends on it.
	 *
	 * @todo Improve the performance.
	 */
	$body.on( 'change', '.glozin-products-filter-form__section :input', function() {
		var $input = $( this ),
			$container = $input.closest( '.widget-content' ),
			optionName = $input.closest( '[data-option]' ).data( 'option' ),
			optionPrefix = '';

		if ( optionName.indexOf( 'filter:' ) === 0 ) {
			$container = $input.closest( '.glozin-products-filter-form__filter-options' );
			optionName = optionName.replace( 'filter:', '' );
			optionPrefix = 'filter:';
		}

		var $dependencies = $( '[data-condition]', $container ).filter( function() {
			var conditions = $( this ).data( 'condition' );

			return optionName in conditions || optionName + '!' in conditions;
		} );

		if ( ! $dependencies.length ) {
			return;
		}

		$dependencies.each( function() {
			var $field = $( this ),
				conditions = $field.data( 'condition' );

			var valid = true;

			_.each( conditions, function( value, key ) {
				var keyParts = key.match( /([a-z_\-0-9]+)(!?)$/ ),
					pureKey = keyParts[1],
					isNegative = !! keyParts[2];

				var $conditionInput = $( '[data-option="' + optionPrefix + pureKey + '"] :input', $container );
				var instanceValue = $conditionInput.is( ':checkbox' ) ? $conditionInput.is( ':checked' ) : $conditionInput.val();
				var isContain = value == instanceValue;

				if ( value instanceof Array && value.length ) {
					isContain = value.indexOf( instanceValue ) > -1;
				}

				if ( ( isNegative && isContain ) || ( ! isNegative && ! isContain ) ) {
					valid = false;
				}
			} );

			if ( ! valid ) {
				$field.addClass( 'hidden' );
			} else {
				$field.removeClass( 'hidden' );
			}
		} );
	} );

	// Initialize sortable (for drag-and-drop)
	$body.find('.glozin-products-filter-form__filter-fields').sortable({
		items: '.glozin-products-filter-form__filter',
		placeholder: 'sortable-placeholder',
	});

  	// Handle button up/down clicks
	$(document).on('click', '.glozin-products-filter-form__sort-up, .glozin-products-filter-form__sort-down', function (e) {
		e.preventDefault();

		const $item = $(this).closest('.glozin-products-filter-form__filter');
		if ( $(this).hasClass('glozin-products-filter-form__sort-up') ) {
			const $prev = $item.prev();
			if ($prev.length) {
				$item.insertBefore($prev);
			}
		} else {
			const $next = $item.next();
			if ($next.length) {
				$item.insertAfter($next);
			}
		}

		$(this).trigger('change');
	});
} );
