/* global wp, wpNavMenu, glozinAddonsMegaMenuConfig */
var kmm = kmm || {};

( function( $, _, wp ) {
	hasModified = false;
	var config = window.glozinAddonsMegaMenuConfig || {};

	if ( ! config ) {
		return false;
	}

	config.namespace = 'glozin_addons_megamenu';

	var mediaFrame = null;

	/**
	 * Megamenu class
	 */
	var MegaMenu = function() {
		var self = this;

		self.$modal     = $( '#megamenu-modal' );
		self.templates  = {};
		self.state      = { changed: false };
		self.itemData   = {};

		// Methods.
		self.getTemplates  = self.getTemplates.bind( self );
		self.bindEvents    = self.bindEvents.bind( self );
		self.setState      = self.setState.bind( self );
		self.getState      = self.getState.bind( self );
		self.getMegaData   = self.getMegaData.bind( self );
		self.clearMegaData = self.clearMegaData.bind( self );
		self.openModal     = self.openModal.bind( self );
		self.closeModal    = self.closeModal.bind( self );
		self.confirmClose  = self.confirmClose.bind( self );
		self.render        = self.render.bind( self );
		self.renderPart    = self.renderPart.bind( self );
		self.initSubmenu   = self.initSubmenu.bind( self );
		self.setItemData   = self.setItemData.bind( self );
		self.getFormData   = self.getFormData.bind( self );
		self.getFieldName  = self.getFieldName.bind( self );

		// Call init methods.
		self.getTemplates();
		self.bindEvents();
	}

	MegaMenu.prototype.getTemplates = function() {
		var self = this;

		if ( self.templates.length ) {
			return self.templates;
		}

		_.each( config.templates, function( name ) {
			self.templates[ name ] = wp.template( 'megamenu__' + name );
		} );

		return self.templates;
	}

	MegaMenu.prototype.bindEvents = function() {
		var self = this;

		// Initial state.
		$( document ).off( '.' + config.namespace );
		$( document.body ).off( '.' + config.namespace );
		self.$modal.off( '.' + config.namespace );

		// Events of displaying the modal.
		$( document.body )
			.on( 'click.' + config.namespace, '.menu-item-settings .item-config-mega', { megaMenu: self }, self.onConfigMega )
			.on( 'click.' + config.namespace, '.menu-item-settings .item-config-icon', { megaMenu: self }, self.onConfigIcon )
			.on( 'click.' + config.namespace, '.megamenu-modal__close, .megamenu-modal__backdrop, .megamenu-modal__cancel', { megaMenu: self }, self.onClose );

		// Events of rendering the modal content.
		self.$modal
			.on( 'closed.' + config.namespace, { megaMenu: self }, self.onClosed )
			.on( 'opened.' + config.namespace, { megaMenu: self }, self.onOpened )
			.on( 'render.' + config.namespace, { megaMenu: self }, self.onRender )
			.on( 'rendered.' + config.namespace, { megaMenu: self }, self.onRendered )
			.on( 'click.' + config.namespace, '.megamenu-modal__menu > a', { megaMenu: self }, self.onChangePanel )
			.on( 'click.' + config.namespace, 'button.megamenu-modal__submenu-settings', { megaMenu: self }, self.onOpenSubMenuSettings )
			.on( 'click.' + config.namespace, 'button.megamenu-modal__back-settings', { megaMenu: self }, self.onGoBackSettings );

		// Events of handling setting fields.
		self.$modal
			// Dertimine if options changed
			.on( 'change.' + config.namespace + ' input.' + config.namespace, ':input', { megaMenu: self }, self.onInputsChange )
			// Toggle icon fields (and other toggleable fields)
			.on( 'change.' + config.namespace, '[data-toggle_condition]', { megaMenu: self }, self.onChangeToggleCondition )
			// Select icon
			.on( 'click.' + config.namespace, '.megamenu-modal__icon-list-item', { megaMenu: self }, self.onSelectIcon )
			.on( 'click.' + config.namespace, '.megamenu-modal__icon-selected', { megaMenu: self }, self.onClearSelectedIcon )
			.on( 'input.' + config.namespace, '.megamenu-modal__icon-search', self.onSearchIcon )
			// Change column width
			.on( 'click.' + config.namespace, '.megamenu-modal__column-width-handle', { megaMenu: self }, self.onResizeMegaColumn )
			// Mega panel "Tabs" events.
			.on( 'click.' + config.namespace, '.megamenu-modal__submenu-tab', { megaMenu: self }, self.onChangeSubmenuTab )
			.on( 'click.' + config.namespace, '.megamenu-modal__submenu-tab button[data-action="edit-tab"]', { megaMenu: self }, self.onEditSubmenuTab )
			// Save changes
			.on( 'click.' + config.namespace, 'button.megamenu-modal__save', { megaMenu: self }, self.onSaveChanges );

		// General events.
		$( document.body )
			.on( 'megamenu_option_changed.' + config.namespace, { megaMenu: self }, self.onOptionChanged )
			.on( 'click.' + config.namespace, '.megamenu-media__preview', { megaMenu: self }, self.onMediaUploaderOpen )
			.on( 'click.' + config.namespace, '.megamenu-media__remove', { megaMenu: self }, self.onMediaUploaderRemove );

		// Prevent adding custom mega menu items to the normal menu.
		$( document ).on( 'menu-item-added.' + config.namespace, self.onAddedCustomMenuItems );
	}

	MegaMenu.prototype.openModal = function() {
		this.$modal.show().addClass( 'opened' );
		this.$modal.trigger( 'opened' );

		this.$modal.appendTo( wpNavMenu.menuList.closest( '#post-body' ) );

		// document.body.classList.add( 'modal-open' );
		document.body.classList.add( 'megamenu-modal-open' );
	}

	MegaMenu.prototype.closeModal = function() {
		this.$modal.hide().removeClass( 'opened' );
		this.$modal.trigger( 'closed' );

		this.$modal.appendTo( document.body );

		// document.body.classList.remove( 'modal-open' );
		document.body.classList.remove( 'megamenu-modal-open' );
	}

	MegaMenu.prototype.setState = function( prop, value ) {
		this.state[ prop ] = value;

		if ( 'changed' === prop ) {
			this.$modal.find( '.megamenu-modal__save' ).prop( 'disabled', ! value );
		}
	}

	MegaMenu.prototype.getState = function( prop ) {
		if ( _.has( this.state, prop ) ) {
			return this.state[ prop ];
		}

		return undefined;
	}

	MegaMenu.prototype.getMegaData = function( menuItem ) {
		var self = this,
			$menuItem = $( menuItem ).closest( 'li.menu-item' ),
			$menuData = $menuItem.find( '.mega-data' ),
			megaData = $menuData.data( 'mega' );

		megaData.content = $menuData.html();

		self.itemData = {
			depth   : $menuItem.menuItemDepth(),
			megaData: megaData,
			data    : $menuItem.getItemData(),
			element : $menuItem.get( 0 )
		};

		if ( 0 === self.itemData.depth ) {
			self.itemGridData = $menuItem.find( '.mega-data-gridbuilder' ).data( 'griddata' );

			var children = $menuItem.childMenuItems();

			self.itemData.children = [];

			_.each( children, function( item ) {
				var $subItem = $( item ),
					$subItemData = $subItem.find( '.mega-data' ),
					depth = $subItem.menuItemDepth(),
					subMegaData = $subItemData.data( 'mega' );

				subMegaData.content = $subItemData.html();

				self.itemData.children.push( {
					depth   : depth,
					subDepth: depth - self.itemData.depth - 1,
					megaData: megaData,
					data    : $subItem.getItemData(),
					element : item
				} );
			} );
		} else { // Check if this is a sub-menu of a mega menu.
			var $topItem = $( self.itemData.element ).prevUntil( '.menu-item-depth-0' );

			$topItem = $topItem.length ? $topItem.last().prev( '.menu-item-depth-0' ) : $( self.itemData.element.previousSibling );

			var topMegaData = $topItem.find( '.mega-data' ).data( 'mega' );

			self.itemData.in_mega = !! topMegaData.mega;
			self.itemData.in_mega_mode = topMegaData.mega_mode;
		}
	}

	MegaMenu.prototype.clearMegaData = function() {
		this.itemData = {};
		this.itemGridData = {};
		this.tabContentData = {};
		this.subMenuGridBuilder = null;
	}

	MegaMenu.prototype.confirmClose = function() {
		if ( this.getState( 'changed' ) && ! confirm( config.l10n.close_confirm ) ) {
			return false;
		}

		return true;
	}

	MegaMenu.prototype.onClose = function( event ) {
		event.preventDefault();

		if ( event.data.megaMenu.confirmClose() ) {
			event.data.megaMenu.closeModal();
		}
	}

	MegaMenu.prototype.onConfigMega = function( event ) {
		event.preventDefault();

		var self = event.data.megaMenu,
			part = self.itemData.depth ? 'settings' : 'mega';

		self.getMegaData( event.target );

		self.setState( 'rendering', part );
		self.openModal();
	}

	MegaMenu.prototype.onConfigIcon = function( event ) {
		event.preventDefault();

		var self = event.data.megaMenu;

		self.getMegaData( event.target );
		self.setState( 'rendering', 'icon' );
		self.openModal();
	}

	MegaMenu.prototype.onInputsChange = function( event ) {
		if ( event.target.dataset.ignore_state_tracking ) {
			return;
		}

		$( document.body ).trigger( 'megamenu_option_changed' );
	}

	MegaMenu.prototype.onOptionChanged = function( event ) {
		event.data.megaMenu.setState( 'changed', true );
	}

	MegaMenu.prototype.onClosed = function( event ) {
		event.data.megaMenu.clearMegaData();
		event.data.megaMenu.setState( 'changed', false );
		event.data.megaMenu.$modal.find( '.megamenu-modal__content' ).html( '' );

		// Reset wpNavMenu targetList.
		wpNavMenu.targetList = wpNavMenu.menuList;
		$( '.submit-add-to-menu', '#side-sortables' ).prop( 'disabled', false );
		$( '.submit-add-to-menu', '#glozin_addons_mega_items' ).prop( 'disabled', true );

		// Reset menusChanged state.
		wpNavMenu.menusChanged = self.menusChanged;
	}

	MegaMenu.prototype.onOpened = function( event ) {
		var self = event.data.megaMenu;

		if ( self.getState( 'rendering' ) ) {
			self.$modal.trigger( 'render' );
		}

		// Disable "Add to Menu" buttons.
		$( '.submit-add-to-menu', '#side-sortables' ).prop( 'disabled', true );

		// Store wpNavMenu.menusChanged status and then restore it when modal closed, because the grid builder may change this status.
		self.menusChanged = wpNavMenu.menusChanged;
	}

	MegaMenu.prototype.onRender = function( event ) {
		var panel = event.data.megaMenu.getState( 'rendering' );

		if ( ! panel ) {
			return;
		}

		event.data.megaMenu.render( panel );
	}

	MegaMenu.prototype.render = function( panel ) {
		// Render menu.
		this.renderPart( 'menu', {
			depth: this.itemData.depth,
			current: panel,
			in_mega: this.itemData.in_mega ? this.itemData.in_mega : false,
			in_mega_mode: this.itemData.in_mega ? this.itemData.in_mega_mode : '',
		} );

		// Render title.
		this.renderPart( 'title', {
			title: this.$modal.find( '.megamenu-modal__menu a[data-panel="' + panel + '"]' ).data( 'title' ),
			depth: this.itemData.depth,
			item_title: this.itemData.data['menu-item-title']
		} );

		// Open or render the panel content.
		var $content = this.$modal.find( '.megamenu-modal__content > [data-panel="' + panel + '"]' );

		if ( ! $content.length ) {
			this.renderPart( panel );
			$content = this.$modal.find( '.megamenu-modal__content > [data-panel="' + panel + '"]' );
		}

		$content.addClass( 'active' ).siblings().removeClass( 'active' );

		this.setState( 'rendering', '' );
		this.$modal.trigger( 'rendered', [ panel ] );
	}

	MegaMenu.prototype.renderPart = function( part, data ) {
		var self = this;

		data = data || self.itemData;

		switch ( part ) {
			case 'menu':
				self.$modal.find( '.megamenu-modal__frame-menu' ).html( this.templates.menu( data ) );
				break;

			case 'title':
				self.$modal.find( '.megamenu-modal__title' ).html( self.templates.title( data ) );
				break;

			case 'icon':
					self.$modal.find( '.megamenu-modal__content' ).append( self.templates.icon( data ) );
					break;

			default:
				if ( _.has( self.templates, part ) ) {
					if ( 0 !== self.itemData.depth && ! self.itemData.in_mega ) {
						self.$modal.find( '.megamenu-modal__content' ).append(
							$( '<div class="megamenu-modal__panel" />' )
								.attr( 'data-panel', part )
								.html( '<p>' + config.l10n.enable_mega_message + '</p>' )
						);
					} else {
						self.$modal.find( '.megamenu-modal__content' ).append( self.templates[ part ]( data ) );
					}
				}
				break;
		}
	}

	MegaMenu.prototype.onRendered = function( event, panel ) {
		var self = event.data.megaMenu,
			$content = self.$modal.find( '.megamenu-modal__content > [data-panel="' + panel + '"]' );

		if ( $content.data( 'initialized' ) ) {
			return;
		}

		// Init color pickers.
		$content.find( 'input[data-type="colorpicker"]' ).wpColorPicker( {
			change: function() {
				$( document.body ).trigger( 'megamenu_option_changed' );
			},
			clear: function() {
				$( document.body ).trigger( 'megamenu_option_changed' );
			}
		} );

		// Mega panel init columns.
		if ( 'mega' === panel ) {
			self.initSubmenu();
		}

		if ( 'tab-content' === panel ) {
			var $tabGrid = $content.find( '.megamenu-modal__tab-content' );

			if ( $tabGrid.length ) {
				self.tabContentBuilder = new MegaMenuGridBuilder( $tabGrid, self.tabContentData );
			}
		}

		// Init icon panel.
		if ( 'icon' === panel ) {
			$content.find( '.megamenu-modal__icon-selected' ).html( function() {
				if ( ! this.dataset.selected ) {
					return '';
				}

				return $content.find( '.megamenu-modal__icon-list [data-icon="' + this.dataset.selected + '"]' ).addClass( 'active' ).html();
			} );
		}

		$content.data( 'initialized', 'yes' );
	}

	MegaMenu.prototype.onChangePanel = function( event ) {
		event.preventDefault();

		event.data.megaMenu.setState( 'rendering', event.target.dataset.panel );
		event.data.megaMenu.$modal.trigger( 'render' );
	}

	MegaMenu.prototype.onOpenSubMenuSettings = function( event ) {
		event.preventDefault();

		var self = event.data.megaMenu;

		if ( ! self.confirmClose() ) {
			return false;
		}

		var itemId = $( event.target ).closest( 'li' ).data( 'item_id' );

		self.closeModal();
		$( '#menu-item-' + itemId ).find( '.menu-item-settings .item-config-mega' ).trigger( 'click' );
	}

	MegaMenu.prototype.onGoBackSettings = function( event ) {
		event.preventDefault();

		var self = event.data.megaMenu;

		if ( ! self.confirmClose() ) {
			return false;
		}

		var $topItem = $( self.itemData.element ).prevUntil( '.menu-item-depth-0' );
		$topItem = $topItem.length ? $topItem.last().prev( '.menu-item-depth-0' ) : $( self.itemData.element.previousSibling );

		self.closeModal();

		$topItem.find( '.menu-item-settings .item-config-mega' ).trigger( 'click' );
	}

	MegaMenu.prototype.onMediaUploaderOpen = function( event ) {
		event.preventDefault();

		if ( ! mediaFrame ) {
			mediaFrame = wp.media( { library: { type: 'image' } } );
		}

		var $uploader = $( event.target ).closest( '.megamenu-media' );

		// Remove all attached 'select' event.
		mediaFrame.off( 'select' );

		// Update input and preview image.
		mediaFrame.on( 'select', function () {
			var selection = mediaFrame.state().get( 'selection' ).first().toJSON();

			$uploader.find( '.megamenu-media__preview' ).html( '<img src="' + selection.url + '">' );
			$uploader.find( 'input[data-image_input="id"]' ).val( selection.id ).trigger( 'change' );
			$uploader.find( 'input[data-image_input="url"]' ).val( selection.url ).trigger( 'change' );
			$uploader.removeClass( 'megamenu-media--empty' );
		} );

		mediaFrame.open();
	}

	MegaMenu.prototype.onMediaUploaderRemove = function( event ) {
		event.preventDefault();

		var $uploader = $( event.target ).closest( '.megamenu-media' );

		$uploader.find( '.megamenu-media__preview img' ).remove();
		$uploader.find( 'input[data-image_input="id"]' ).val( '' ).trigger( 'change' );
		$uploader.find( 'input[data-image_input="url"]' ).val( '' ).trigger( 'change' );
		$uploader.addClass( 'megamenu-media--empty' );
	}

	MegaMenu.prototype.getFieldName = function( name, id ) {
		id   = id || this.itemData.data['menu-item-db-id'];
		name = name.split( '.' );
		name = '[' + name.join( '][' ) + ']';

		return 'menu-item-mega[' + id + ']' + name;
	}

	MegaMenu.prototype.onChangeToggleCondition = function( event ) {
		var dataName = 'data-toggle_' + event.currentTarget.dataset.toggle_condition,
			value = event.currentTarget.value,
			$scope = event.data.megaMenu.$modal;

		if ( event.currentTarget.dataset.toggle_scope ) {
			$scope = $( event.currentTarget ).closest( event.currentTarget.dataset.toggle_scope );
		}

		if ( 'radio' === event.currentTarget.type ) {
			value = $scope.find( 'input[type="radio"][name="' + event.currentTarget.name + '"]:checked' ).val();
		} else if ( 'checkbox' === event.currentTarget.type ) {
			value = event.currentTarget.checked ? value : '';
		}

		$scope
			.find( '[' + dataName + ']' )
			.hide().addClass( 'hidden' )
			.filter( '[' + dataName + '="' + value + '"]' )
			.show().removeClass( 'hidden' );
	}

	MegaMenu.prototype.onSelectIcon = function( event ) {
		event.preventDefault();
		event.stopPropagation();

		var $el = $( this );

		// Toggle the .active class
		$el.addClass( 'active' ).siblings( '.active' ).removeClass( 'active' );

		// Preview selected icon.
		// Can't merge into one chain to avoid issue when no .siblings( '.active' ) is selected.
		$el.closest( '.megamenu-modal__icon-picker' )
			.find( '.megamenu-modal__icon-selected' )
			.html( $el.html() )
			.siblings( 'input[type="hidden"]' ).val( this.dataset.icon ).trigger( 'change' );
	}

	MegaMenu.prototype.onClearSelectedIcon = function( event ) {
		event.preventDefault();
		event.stopPropagation();

		$( event.target )
			.closest( '.megamenu-modal__icon-selected' ).html( '' )
			.siblings( 'input[type="hidden"]' ).val( '' ).trigger( 'change' )
			.closest( '.megamenu-modal__icon-picker' ).find( '.megamenu-modal__icon-list-item.active' ).removeClass( 'active' );
	}

	MegaMenu.prototype.onSearchIcon = function( event ) {
		var term = event.target.value.toLowerCase(),
			$icons = $( event.target ).closest( '.megamenu-modal__icon-picker' ).find( '.megamenu-modal__icon-list-item' );

		if ( ! term ) {
			$icons.show();
		} else {
			$icons.hide().filter( function() {
				return this.dataset.icon.toLowerCase().indexOf( term ) > -1;
			} ).show();
		}
	}

	MegaMenu.prototype.onResizeMegaColumn = function( event ) {
		event.preventDefault();

		var $el = $( event.target ).closest( '.megamenu-modal__column-width-handle' ),
			$column = $el.closest( '.megamenu-modal__submenu-column' ),
			currentWidth = $column.data( 'width' ),
			widthData = event.data.megaMenu.getWidthData( currentWidth ),
			nextWidth;

		if ( ! widthData ) {
			return;
		}

		if ( 'increase' === $el.data( 'action' ) ) {
			nextWidth = widthData.increase ? widthData.increase : widthData;
		} else {
			nextWidth = widthData.decrease ? widthData.decrease : widthData;
		}

		$column.css( 'width', nextWidth.width );
		$column.data( 'width', nextWidth.width );
		$column.find( '.megamenu-modal__column-width-label' ).text( nextWidth.label );
		$column.find( '.menu-item-depth-0 input.menu-item-width' ).val( nextWidth.width ).trigger( 'change' );
	}

	MegaMenu.prototype.getWidthData = function( width ) {
		var steps = [
			{width: '12.50%', label: '1/8'},
			{width: '20.00%', label: '1/5'},
			{width: '25.00%', label: '1/4'},
			{width: '33.33%', label: '1/3'},
			{width: '37.50%', label: '3/8'},
			{width: '40.00%', label: '2/5'},
			{width: '50.00%', label: '1/2'},
			{width: '60.00%', label: '3/5'},
			{width: '62.50%', label: '5/8'},
			{width: '66.66%', label: '2/3'},
			{width: '75.00%', label: '3/4'},
			{width: '80.00%', label: '4/5'},
			{width: '87.50%', label: '7/8'},
			{width: '100.00%', label: '1/1'}
		];

		var index = _.findIndex( steps, function( data ) { return data.width === width; } );

		if ( index === 'undefined' ) {
			return false;
		}

		var data = {
			index: index,
			width: steps[index].width,
			label: steps[index].label
		};

		if ( index > 0 ) {
			data.decrease = {
				index: index - 1,
				width: steps[index - 1].width,
				label: steps[index - 1].label
			};
		}

		if ( index < steps.length - 1 ) {
			data.increase = {
				index: index + 1,
				width: steps[index + 1].width,
				label: steps[index + 1].label
			};
		}

		return data;
	}

	MegaMenu.prototype.initSubmenu = function() {
		var self = this,
			$columns = self.$modal.find( '.megamenu-modal__submenu-column' );

		// Init columns
		if ( $columns.length ) {
			var defaultWidth = '25.00%';

			// Support maximum 5 columns
			if ( $columns.length <= 5 ) {
				defaultWidth = String( ( 100 / $columns.length ).toFixed( 2 ) ) + '%';
			}

			_.each( $columns, function ( column ) {
				var width = column.dataset.width;

				if ( ! parseInt( width ) ) {
					width = defaultWidth;
				}

				var widthData = self.getWidthData( width );

				column.style.width = widthData.width;
				column.dataset.width = widthData.width;
				$( column ).find( '.menu-item-depth-0 input.menu-item-width' ).val( width );
				$( column ).find( '.megamenu-modal__column-width-label' ).text( widthData.label );
			} );
		}

		// Init grid builder.
		var $gridBuilder = self.$modal.find( '.megamenu-modal__submenu--grid' );

		if ( $gridBuilder.length ) {
			self.subMenuGridBuilder = new MegaMenuGridBuilder( $gridBuilder, self.itemGridData );
		}
	}

	MegaMenu.prototype.onChangeSubmenuTab = function( event ) {
		event.preventDefault();

		var $tab = $( this );

		if ( $tab.hasClass( 'active' ) ) {
			return;
		}

		$tab.addClass( 'active' ).siblings().removeClass( 'active' );
	}

	MegaMenu.prototype.onEditSubmenuTab = function( event ) {
		event.preventDefault();

		var self = event.data.megaMenu;

		if ( ! self.confirmClose() ) {
			return false;
		}

		var itemId = $( this ).data( 'item_id' );

		self.closeModal();

		$( '#menu-item-' + itemId ).find( '.menu-item-settings .item-config-mega' ).trigger( 'click' );
	}

	MegaMenu.prototype.getFormData = function () {
		var $inputs = this.$modal.find( '.megamenu-modal__content :input' );

		// Handle unchecked checkboxes.
		$inputs.filter( ':checkbox:not(:checked)' ).each( function( index, input ) {
			var $checkbox = $( input );

			$checkbox.data( 'value', $checkbox.val() )
			$checkbox.attr( 'value', '' ).prop( 'checked', true );
		} );

		var data = $inputs.serialize();

		// Reset unchecked checkboxes.
		$inputs.filter( ':checkbox[value=""]' ).each( function( index, input ) {
			var $checkbox = $( input );

			$checkbox.prop( 'checked', false ).attr( 'value', $checkbox.data( 'value' ) ).removeData( 'value' );
		} );

		return data;
	}

	MegaMenu.prototype.setItemData = function( item, data ) {
		var $dataHolder = $( item ).find( '.mega-data' );

		if ( _.has( data, 'content' ) ) {
			$dataHolder.html( data.content );
			delete data.content;
		}

		$dataHolder.data( 'mega', data );
	}

	MegaMenu.prototype.onSaveChanges = function( event ) {
		event.preventDefault();

		var self = event.data.megaMenu,
			$spinner = self.$modal.find( '.spinner' ),
			data = self.getFormData();

		$spinner.addClass( 'is-active' );

		$.post( ajaxurl, {
			action: config.namespace + '_save',
			data  : data
		}, function ( res ) {
			$spinner.removeClass( 'is-active' );

			if ( ! res.success ) {
				return;
			}

			var data = res.data['menu-item-mega'];

			// Update parent menu item
			if ( _.has( data, self.itemData.data['menu-item-db-id'] ) ) {
				self.setItemData( self.itemData.element, data[ self.itemData.data['menu-item-db-id'] ] );
			}

			_.each( self.itemData.children, function ( menuItem ) {
				if ( ! _.has( data, menuItem.data['menu-item-db-id'] ) ) {
					return;
				}

				self.setItemData( menuItem.element, data[ menuItem.data['menu-item-db-id'] ] );
			} );

			self.setState( 'changed', false );
			wpNavMenu.menusChanged = self.menusChanged;
		} );

		if ( self.subMenuGridBuilder ) {
			self.subMenuGridBuilder.saveChanges( {
				action: config.namespace + '_update_grid',
				menuItemId: self.itemData.data['menu-item-db-id']
			} );

			// Update grid data.
			$( self.itemData.element ).find( '.mega-data-gridbuilder' ).data( 'griddata', self.subMenuGridBuilder.data );
		}

		if ( self.tabContentBuilder ) {
			self.tabContentBuilder.saveChanges( {
				action: config.namespace + '_update_tab',
				menuItemId: self.itemData.data['menu-item-db-id']
			} );

			// Update tab data.
			$( self.itemData.element ).find( '.tab-data-gridbuilder' ).data( 'tabdata', self.tabContentBuilder.data );
		}
	}

	MegaMenu.prototype.onAddedCustomMenuItems = function( event, $menuItems ) {
		// Just check with the default menu.
		if ( ! wpNavMenu.targetList.is( wpNavMenu.menuList ) ) {
			return;
		}

		$menuItems.filter( '.menu-item' ).each( function() {
			var $item = $( this );

			if ( $item.hasClass( 'menu-item-megamenu' ) ) {
				wpNavMenu.removeMenuItem( $item );
			}
		} );
	}

	/**
	 * Grid builder class
	 */
	var MegaMenuGridBuilder = function( $grid, data ) {
		var self = this;

		self.$ui = $grid;
		self.$content = self.$ui.find( '.megamenu-modal-grid__inside' );
		self.data = {
			rows: [],
			columns: [],
			items: []
		};
		self.templates = {
			row: wp.template( 'megamenu__mega-grid-row' ),
			column: wp.template( 'megamenu__mega-grid-column' ),
			options: wp.template( 'megamenu__mega-grid-options' )
		};
		self.selectors = {
			row: '.megamenu-modal-grid__row',
			column: '.megamenu-modal-grid__column',
			itemList: '.megamenu-modal-grid__items',
			optionsPopup: '.megamenu-modal-grid__options-popup'
		}

		// Initial state.
		self.$ui.off();
		$( document ).off( '.' + config.namespace );

		// Methods.
		self.render = self.render.bind( self );
		self.addRow = self.addRow.bind( self );
		self.removeRow = self.removeRow.bind( self );
		self.updateRow = self.updateRow.bind( self );
		self.addColumn = self.addColumn.bind( self );
		self.removeColumn = self.removeColumn.bind( self );
		self.updateColumn = self.updateColumn.bind( self );
		self.getRowFreeWidth = self.getRowFreeWidth.bind( self );
		self.addItem = self.addItem.bind( self );
		self.removeItem = self.removeItem.bind( self );
		self.updateItem = self.updateItem.bind( self );
		self.sortItems = self.sortItems.bind( self );
		self.getMenuItemData = self.getMenuItemData.bind( self );
		self.dataToParams = self.dataToParams.bind( self );
		self.saveChanges = self.saveChanges.bind( self );
		self.generateId = self.generateId.bind( self );
		self.openOptionsModal = self.openOptionsModal.bind( self );

		// Bind events.
		self.$ui.on( 'init', { builder: self }, self.onInit );

		self.$ui.on( 'click', '.megamenu-modal-grid__actions [data-action="add-row"]', { builder: self }, self.onAddRow );
		self.$ui.on( 'click', '.megamenu-modal-grid__row-actions [data-action="add-column"]', { builder: self }, self.onAddColumn );
		self.$ui.on( 'click', '.megamenu-modal-grid__row-actions [data-action="toggle-options"]', { builder: self }, self.onToggleRowOptions );
		self.$ui.on( 'click', '.megamenu-modal-grid__row-actions [data-action="delete"]', { builder: self }, self.onDeleteRow );
		self.$ui.on( 'click', '.megamenu-modal-grid__row-actions [data-action="options"]', { builder: self }, self.onToggleRowOptionsPopup );

		self.$ui.on( 'click', '.megamenu-modal-grid__column-actions [data-action="toggle-options"]', { builder: self }, self.onToggleColumnOptions );
		self.$ui.on( 'click', '.megamenu-modal-grid__column-actions [data-action="options"]', { builder: self }, self.onToggleColumnOptionsPopup );
		self.$ui.on( 'click', '.megamenu-modal-grid__column-actions [data-action="delete"]', { builder: self }, self.onDeleteColumn );
		self.$ui.on( 'click', '.megamenu-modal-grid__column-actions [data-action="reset-width"]', { builder: self }, self.onResetColumnWidth );
		self.$ui.on( 'click', '.megamenu-modal-grid__column-actions [data-action="resize"]', { builder: self }, self.onResizeColumn );
		self.$ui.on( 'column_added', { builder: self }, self.onColumnAdded );
		self.$ui.on( 'column_updated', { builder: self }, self.onColumnUpdated );

		$( document )
			.on( 'click.' + config.namespace, self.selectors.optionsPopup + ' [data-action="save-options"]', { builder: self }, self.onSaveOptionsPopup )
			.on( 'click.' + config.namespace, self.selectors.optionsPopup + ' [data-action="close-options"]', { builder: self }, self.onCloseOptionsPopup );

		self.$ui.on( 'click', '.megamenu-modal-grid__items-actions [data-action="add-item"]', { builder: self }, self.onSelectColumnToAddItem );
		self.$ui.on( 'click', self.selectors.column, { builder: self }, self.onSelectColumnToAddItem );
		$( document )
			.on( 'menu-item-added.' + config.namespace, { builder: self }, self.onMenuItemAdded )
			.on( 'menu-removing-item.' + config.namespace, { builder: self }, self.onMenuItemRemoving );

		// Bind events of widget items.
		self.$ui.on( 'change input', self.selectors.itemList + ' :input', { builder: self }, self.onChangeItemInputs );
		self.$ui.on( 'change input', '.edit-menu-item-title, .edit-menu-item-button-text', { builder: self }, self.onChangeItemTitle );

		// Init.
		self.render( data );
		self.$ui.trigger( 'init', [ self ] );
	}

	MegaMenuGridBuilder.prototype.render = function( data ) {
		var self = this,
			rows = data.rows || [],
			columns = data.columns || [],
			items = data.items || [];

		// Reset content.
		self.$content.html( '' );

		// Return if there is no rows.
		if ( _.isEmpty( rows ) ) {
			return;
		}

		// Render rows.
		_.each( rows, function( rowData ) {
			self.addRow( rowData );
		} );

		// Render columns.
		_.each( columns, function( columnData ) {
			self.addColumn( columnData );
		} );

		// Render items.
		if ( _.size( items ) ) {
			_.each( items, function( itemData ) {
				self.addItem( itemData );
			} );

			var $loader = $( '<div class="megamenu-modal__submenu-loader"><span class="spinner is-active"></span></div>' );
			$loader.appendTo( self.$ui );

			// Send ajax to get item HTML.
			$.post( ajaxurl, {
				action: config.namespace + '_get_grid_items',
				items: items,
				"menu-settings-column-nonce": $( '#menu-settings-column-nonce' ).val()
			}, function( html ) {
				$loader.remove();

				$( html ).filter( '.menu-item' ).each( function() {
					var $item = $( this ),
						id = $item.find( '.menu-item-checkbox' ).data( 'menu-item-id' ),
						itemData = _.find( self.data.items, { id: id } );

					if ( ! itemData ) {
						return;
					}

					self.modifyMenuItem( $item );
					$item.hideAdvancedMenuItemFields();

					var $column = self.$ui.find( self.selectors.column + '[data-id="' + itemData.col.toString() + '"]' );
					$( self.selectors.itemList, $column ).append( $item );
				} );
			} );
		}
	}

	MegaMenuGridBuilder.prototype.onInit = function( event ) {
		var self = event.data.builder;

		self.$content.sortable( {
			placeholder: 'sortable-placeholder',
			handle: 'span[data-action="sort"]',
			update: function( event ) {
				self.$content.children( self.selectors.row ).each( function( index, row ) {
					self.updateRow( {
						id: $( row ).data( 'id' ),
						order: index
					} );
				} );

				self.data.rows = _.sortBy( self.data.rows, 'order' );
			}
		} );

		// Looking for data change events.
		// Do not add this event listener on constructor.
		self.$ui.on( 'row_added row_updated row_removed column_added column_updated column_removed item_added item_updated item_removed', { builder: self }, self.onDataChanged );
	}

	MegaMenuGridBuilder.prototype.onDataChanged = function( event ) {
		event.preventDefault();

		$( document.body ).trigger( 'megamenu_option_changed' );
		event.data.builder.hasChanges = true;

		// Regiter changes to wpNavMenu.
		wpNavMenu.registerChange();
	}

	MegaMenuGridBuilder.prototype.addRow = function( rowData, position ) {
		rowData.id    = parseInt( rowData.id );
		rowData.order = parseInt( rowData.order );

		var row = this.templates.row( rowData ),
			$row = $( row );

		if ( 'top' === position ) {
			this.$content.prepend( $row );
		} else {
			this.$content.append( $row );
		}

		this.data.rows.push( rowData );
		this.$ui.trigger( 'row_added', [ rowData ] );
	}

	MegaMenuGridBuilder.prototype.removeRow = function( rowId ) {
		var self = this;

		self.$content.find( self.selectors.row + '[data-id="' +  rowId + '"]' ).remove();

		self.data.rows = _.without( self.data.rows, _.findWhere( self.data.rows, { id: rowId } ) );

		var columns = _.filter( self.data.columns, function( colData ) { return colData.row == rowId } );

		_.each( columns, function( colData ) {
			self.removeColumn( colData.id );
		} );

		self.$ui.trigger( 'row_removed', [ rowId ] );
	}

	MegaMenuGridBuilder.prototype.updateRow = function( data ) {
		if ( ! _.isObject( data ) || ! data.id ) {
			return;
		}

		var index = _.findIndex( this.data.rows, { id: data.id } );

		if ( index < 0 ) {
			return;
		}

		this.data.rows[ index ] = $.extend( true, {}, this.data.rows[ index ], data );

		this.$ui.trigger( 'row_updated', [ data ] );
	}

	MegaMenuGridBuilder.prototype.addColumn = function( colData ) {
		if ( ! colData.row ) {
			return;
		}

		colData.id  = parseInt( colData.id );
		colData.row = parseInt( colData.row );

		if ( _.has( colData, 'order' ) ) {
			colData.order = parseInt( colData.order );
		}

		var $row = this.$content.find( this.selectors.row + '[data-id="' + colData.row + '"]' ),
			column = this.templates.column( colData ),
			$column = $( column );

		$row.find( '.megamenu-modal-grid__row-inside' ).append( $column );

		this.data.columns.push( colData );
		this.$ui.trigger( 'column_added', [ colData.id ] );
	}

	MegaMenuGridBuilder.prototype.removeColumn = function( colId ) {
		var self = this;

		self.$content.find( self.selectors.column + '[data-id="' +  colId + '"]' ).remove();

		self.data.columns = _.without( self.data.columns, _.findWhere( self.data.columns, { id: colId } ) );

		var items = _.filter( self.data.items, { col: colId } );

		_.each( items, function( itemData ) {
			self.removeItem( itemData.id );
		} );

		self.$ui.trigger( 'column_removed', [ colId ] );
	}

	MegaMenuGridBuilder.prototype.updateColumn = function( data ) {
		if ( ! _.isObject( data ) || ! data.id ) {
			return;
		}

		var index = _.findIndex( this.data.columns, { id: data.id } );

		if ( index < 0 ) {
			return;
		}

		this.data.columns[ index ] = $.extend( true, {}, this.data.columns[ index ], data );

		this.$ui.trigger( 'column_updated', [ data ] );
	}

	MegaMenuGridBuilder.prototype.addItem = function( data ) {
		if ( ! data.id && ! data.col ) {
			return;
		}

		data.id    = parseInt( data.id );
		data.col   = parseInt( data.col );
		data.order = parseInt( data.order );

		if ( _.has( data, 'is_widget' ) ) {
			data.is_widget = ( 'true' === data.is_widget || true === data.is_widget );
		}

		this.data.items.push( data );
		this.sortItems();
		this.$ui.trigger( 'item_added', [ data ] );
	}

	MegaMenuGridBuilder.prototype.removeItem = function( itemId ) {
		this.data.items = _.without( this.data.items, _.findWhere( this.data.items, { id: itemId } ) );
		this.sortItems();
		this.$ui.trigger( 'item_removed', [ itemId ] );

		// Do not remove HTML (WP control this already).
	}

	MegaMenuGridBuilder.prototype.updateItem = function( data ) {
		if ( ! _.isObject( data ) || ! data.id ) {
			return;
		}

		var index = _.findIndex( this.data.items, { id: data.id } );

		this.data.items[ index ] = $.extend( true, {}, this.data.items[ index ], data );

		this.$ui.trigger( 'item_updated', [ data ] );
	}

	MegaMenuGridBuilder.prototype.sortItems = function() {
		this.data.items = _.chain( this.data.items ).sortBy( 'order' ).sortBy( 'col' ).value();
	}

	MegaMenuGridBuilder.prototype.onAddRow = function( event ) {
		event.preventDefault();

		var self = event.data.builder,
			rowId = self.generateId(),
			nextOrder = _.isEmpty( self.data.rows ) ? 0 : _.max( self.data.rows, 'order' ).order + 1,
			options = _.extend( { id: rowId, order: nextOrder }, $( event.currentTarget ).data( 'options' ) );

		self.addRow( options );

		// Add 2 columns by default.
		self.$content.find( self.selectors.row + '[data-id="' + rowId +'"] button[data-action="add-column"]' ).trigger( 'click' ).trigger( 'click' );
	}

	MegaMenuGridBuilder.prototype.onToggleRowOptions = function( event ) {
		event.preventDefault();

		var self = event.data.builder;

		$( event.currentTarget )
			.siblings( '.megamenu-modal-grid__row-options' ).toggle()
			.closest( self.selectors.row ).toggleClass( 'options-active' );
	}

	MegaMenuGridBuilder.prototype.onDeleteRow = function( event ) {
		event.preventDefault();

		var self = event.data.builder,
			rowId = $( event.currentTarget ).closest( self.selectors.row ).data( 'id' );

		self.removeRow( rowId );
	}

	MegaMenuGridBuilder.prototype.onToggleRowOptionsPopup = function( event ) {
		event.preventDefault();

		var self = event.data.builder,
			rowId = $( event.currentTarget ).closest( self.selectors.row ).data( 'id' );

		self.openOptionsModal( rowId, 'row' );
	}

	MegaMenuGridBuilder.prototype.onAddColumn = function( event ) {
		event.preventDefault();

		var self = event.data.builder,
			$el = $( event.currentTarget ),
			rowId = $el.closest( self.selectors.row ).data( 'id' ),
			columns = _.filter( self.data.columns, { row: rowId } ),
			options = _.extend( { id: self.generateId(), row: rowId, width: 'auto' }, $el.data( 'options' ) );

		if ( columns.length < 12 ) {
			// Get free width before adding.
			var freeWidth = self.getRowFreeWidth( rowId );

			self.addColumn( options );

			// Resize the last column if not enough free space.
			if ( freeWidth < 8.33 ) {
				var resizableColumn = _.last( _.filter( columns, function( colData ) {
					return colData.width !== 'auto' && parseFloat( colData.width ) > 8.33;
				} ) );

				self.updateColumn( {
					id: resizableColumn.id,
					width: Math.max( parseFloat( resizableColumn.width ) - 8.33, 8.33 ).toFixed( 2 )
				} );
			}
		}
	}

	MegaMenuGridBuilder.prototype.getRowFreeWidth = function( rowId ) {
		var columns = _.filter( this.data.columns, { row: rowId } ),
			max = 100;

		_.each( columns, function( colData ) {
			if ( 'auto' !== colData.width ) {
				max -= parseFloat( colData.width );
			} else {
				max -= 8.33; // 1 column width.
			}
		} );

		return max;
	}

	MegaMenuGridBuilder.prototype.onToggleColumnOptions = function( event ) {
		event.preventDefault();

		var self = event.data.builder,
			$column = $( event.currentTarget ).closest( self.selectors.column );

		if ( $column.hasClass( 'options-active' ) ) {
			$column.removeClass( 'options-active' );
		} else {
			$( self.selectors.column ).removeClass( 'options-active' );
			$column.addClass( 'options-active' );
		}
	}

	MegaMenuGridBuilder.prototype.onToggleColumnOptionsPopup = function( event ) {
		event.preventDefault();

		var self = event.data.builder,
			colId = $( event.currentTarget ).closest( self.selectors.column ).data( 'id' );

		self.openOptionsModal( colId, 'column' );

		$( self.selectors.column ).removeClass( 'options-active' );
	}

	MegaMenuGridBuilder.prototype.onDeleteColumn = function( event ) {
		event.preventDefault();

		var self = event.data.builder,
			colId = $( event.currentTarget ).closest( self.selectors.column ).data( 'id' );

		self.removeColumn( colId );
	}

	MegaMenuGridBuilder.prototype.onResizeColumn = function( event ) {
		event.preventDefault();

		var self = event.data.builder,
			$column = $( event.currentTarget ).closest( self.selectors.column ),
			current = $column.data( 'width' ),
			step = 8.33, // 1 column of 12 columns.
			next = 0,
			max = self.getRowFreeWidth( $column.closest( self.selectors.row ).data( 'id' ) );

		if ( 'auto' === current ) {
			current = parseFloat( $column.width() / $column.parent().width() * 100 );
			max += step;
		} else {
			current = parseFloat( current );
			max += current;
		}

		next = current;

		if ( 'increase' === event.currentTarget.dataset.dir ) {
			var $nextColumn = $column.next(),
				nextColWidth = $nextColumn.data( 'width' );

			if ( nextColWidth !== 'auto' && nextColWidth > step && current >= max ) {
				max += nextColWidth - step;

				self.updateColumn( {
					id: $nextColumn.data( 'id' ),
					width: Math.max( nextColWidth - step, step ).toFixed( 2 )
				} );
			}

			next = current + step;
			next = Math.min( next, max )
		} else if ( 'decrease' === event.currentTarget.dataset.dir && current > step ) {
			next = current - step;
			next = Math.max( next, step ); // min = step.
		}

		if ( next !== current ) {
			self.updateColumn( {
				id: $column.data( 'id' ),
				width: parseFloat( next ).toFixed( 2 )
			} );
		}
	}

	MegaMenuGridBuilder.prototype.onResetColumnWidth = function( event ) {
		event.preventDefault();

		var self = event.data.builder,
			id = $( event.currentTarget ).closest( self.selectors.column ).removeClass( 'options-active' ).data( 'id' );

		self.updateColumn( {
			id: id,
			width: 'auto'
		} );
	}

	/**
	 * Make columns resizeable on added.
	 * Init the sortable for item list.
	 */
	MegaMenuGridBuilder.prototype.onColumnAdded = function( event, colId ) {
		var self = event.data.builder,
			$column = self.$content.find( self.selectors.column + '[data-id="' + colId + '"]' ),
			minWidth = $column.parent().width() / 12;

		$column.resizable( {
			containment: 'parent',
			handles: 'e',
			minWidth: minWidth,
			start: function( event, ui ) {
				var	$columns = ui.element.siblings( self.selectors.column ),
					$next = ui.element.next(),
					containerWidth = ui.element.parent().width(),
					maxWidth = containerWidth;

				$columns.each( function( index, column ) {
					if ( 'auto' !== column.dataset.width ) {
						maxWidth -= $( column ).outerWidth();
					} else {
						maxWidth -= minWidth;
					}
				} );

				if ( $next.length && $next.data( 'width' ) !== 'auto' && $next.outerWidth() > minWidth ) {
					maxWidth += $next.outerWidth() - minWidth;
				}

				ui.originalElement.resizable( 'option', 'maxWidth', maxWidth );
				ui.originalElement.data( 'width', 'manual' ).attr( 'data-width', 'manual' );
			},
			stop: function( event, ui ) {
				var width = ( ui.size.width / ui.element.parent().width() * 100 ).toFixed( 2 );

				self.updateColumn( {
					id: ui.element.data( 'id' ),
					width: width
				} );
			},
			resize: function( event, ui ) {
				var containerWidth = ui.element.parent().width(),
					width = ( ui.size.width / containerWidth * 100 ).toFixed( 2 );

				self.updateColumn( {
					id: ui.element.data( 'id' ),
					width: width
				} );

				// Resize next column if needed.
				var $next = ui.element.next(),
					nextColWidth = $next.outerWidth();

				if ( $next.length && $next.data( 'width' ) !== 'auto' && nextColWidth > minWidth ) {
					var resizeWidth = ui.size.width - ui.originalElement.resizable( 'option', 'maxWidth' ) + nextColWidth - minWidth;

					if ( resizeWidth > 0 ) {
						self.updateColumn( {
							id: $next.data( 'id' ),
							width: ( ( nextColWidth - resizeWidth ) / containerWidth * 100 ).toFixed( 2 )
						} );
					}
				}
			}
		} );

		// Make items sortable.
		$column.find( self.selectors.itemList ).sortable( {
			placeholder: 'sortable-placeholder',
			handle: '.menu-item-handle',
			connectWith: self.selectors.itemList,
			update: function( event, ui ) {
				ui.item.closest( self.selectors.itemList ).children( 'li' ).each( function( index, item ) {
					self.updateItem( {
						id: $( item ).find( 'input.menu-item-checkbox' ).data( 'menu-item-id' ),
						col: colId,
						order: index
					} );
				} );

				self.sortItems();
			},
			remove: function( event ) {
				$( event.target ).closest( self.selectors.itemList ).children( 'li' ).each( function( index, item ) {
					self.updateItem( {
						id: $( item ).find( 'input.menu-item-checkbox' ).data( 'menu-item-id' ),
						order: index
					} );
				} );

				self.sortItems();
			}
		} );
	}

	MegaMenuGridBuilder.prototype.onColumnUpdated = function( event, data ) {
		var self = event.data.builder;

		if ( _.has( data, 'width' ) ) {
			var $column = self.$content.find( self.selectors.column + '[data-id="' + data.id + '"]' );

			$column.data( 'width', data.width )
				.attr( 'data-width', data.width )
				.css( 'width', 'auto' === data.width ? '' : data.width + '%' )
				.find( '.megamenu-modal-grid__column-width-label' ).text( 'auto' === data.width ? config.l10n.width_auto : parseFloat( data.width ).toFixed( 1 ) + '%' );

			if ( $column.width() < 280 ) {
				$column.addClass( 'column-small-width' );
			} else {
				$column.removeClass( 'column-small-width' );
			}
		}
	}

	MegaMenuGridBuilder.prototype.onSelectColumnToAddItem = function( event ) {
		var self = event.data.builder;
		var $column = $( event.currentTarget ).closest( self.selectors.column );

		// Highlight the left column.
		if ( event.currentTarget.tagName.toLowerCase() === 'button' ) {
			$( '#side-sortables' ).addClass( 'highlighted' );

			setTimeout( function() {
				$( '#side-sortables' ).removeClass( 'highlighted' );
			}, 1500 );
		}

		// No need to run this on every click. Let other things work.
		if ( $column.hasClass( 'selected-to-add') ) {
			return;
		}

		self.$ui.find( self.selectors.column + '.selected-to-add' ).removeClass( 'selected-to-add' );
		$column.addClass( 'selected-to-add' );

		wpNavMenu.targetList = $column.find( self.selectors.itemList );
		$( '.submit-add-to-menu', '#side-sortables' ).prop( 'disabled', false );
	}

	MegaMenuGridBuilder.prototype.onMenuItemAdded = function( event, $menuItems ) {
		var self = event.data.builder;

		if ( ! wpNavMenu.targetList.is( self.selectors.itemList ) ) {
			return;
		}

		// Check if new items were added to this builder.
		if ( ! self.$ui.has( $menuItems ) ) {
			return;
		}

		var colId = $menuItems.closest( self.selectors.column ).data( 'id' ),
			items = _.filter( self.data.items, { col: colId } ),
			nextOrder = _.isEmpty( items ) ? 0 : _.max( items, 'order' ).order + 1;

		$menuItems.filter( '.menu-item' ).each( function( index, item ) {
			var $item = $( item );

			// Remove input name attribute and unnecessary elements.
			self.modifyMenuItem( $item );

			// Add item.
			var itemData = _.extend( { col: colId, order: nextOrder + index }, self.getMenuItemData( item ) );

			self.addItem( itemData );
		} );
	}

	MegaMenuGridBuilder.prototype.modifyMenuItem = function( $item ) {
		var id = $item.find( '.menu-item-checkbox' ).data( 'menu-item-id' );

		// Remove unnecessary elements.
		$item.find( '.field-mega-options, .field-move' ).remove();

		if ( ! $item.hasClass( 'menu-item-megamenu' ) ) {
			// wp-admin/js/nav-menu.js: jQueryExtensions.getItemData
			var fields = [
				'menu-item-db-id',
				'menu-item-object-id',
				'menu-item-object',
				'menu-item-parent-id',
				'menu-item-position',
				'menu-item-type',
				'menu-item-title',
				'menu-item-url',
				'menu-item-description',
				'menu-item-attr-title',
				'menu-item-target',
				'menu-item-classes',
				'menu-item-xfn'
			];

			$item.find( ':input' ).each( function() {
				var field, key,
					i = fields.length;

				while ( i-- ) {
					field = fields[i] + '[' + id + ']';
					key   = fields[i].replace( 'menu-item-', '' ).replace( '-', '_' );
					key   = 'db_id' === key ? 'id' : key;

					if ( this.name && field == this.name ) {
						this.removeAttribute( 'name' );
						this.setAttribute( 'data-name', key );
					}
				}
			} );
		}

		$item.attr( 'data-id', id );
	}

	MegaMenuGridBuilder.prototype.onMenuItemRemoving = function( event, $menuItem ) {
		var self = event.data.builder;

		if ( ! wpNavMenu.targetList.is( self.selectors.itemList ) ) {
			return;
		}

		// Check if these items belong to this builder.
		if ( ! self.$ui.has( $menuItem ) ) {
			return;
		}

		self.removeItem( $menuItem.data( 'id' ) );
	}

	MegaMenuGridBuilder.prototype.getMenuItemData = function( item ) {
		var $item = $( item ),
			data = { is_widget: false },
			self = this;

		$item.find( ':input' ).each( function() {
			if ( ! this.dataset.name ) {
				return;
			}

			data = $.extend( true, {}, data, self.generateInputData( this ) );
		} );

		if ( $item.hasClass( 'menu-item-megamenu' ) ) {
			data.is_widget = true;
			data.object    = data.type;
			data.type      = 'custom';
		}

		return data;
	}

	MegaMenuGridBuilder.prototype.onChangeItemInputs = function( event ) {
		var self = event.data.builder,
			$item = $( event.currentTarget ).closest( '.menu-item' ),
			data = $.extend( true, { id: $item.data( 'id' ) }, self.generateInputData( event.currentTarget ) );

		self.updateItem( data );
	}

	MegaMenuGridBuilder.prototype.onChangeItemTitle = function( event ) {
		var $input = $( event.currentTarget ),
			$title = $input.closest( '.menu-item' ).find( '.menu-item-title' ),
			text   = $input.val();

			if ( !text ) {
				text = $input.closest( '.menu-item' ).find( '.edit-menu-item-button-text' ).val();
			}

			// Don't update to empty title.
			if ( text ) {
				$title.text( text ).removeClass( 'no-title' );
			} else {
				$title.text( wp.i18n._x( '(no label)', 'missing menu item navigation label' ) ).addClass( 'no-title' );
			}
	}

	MegaMenuGridBuilder.prototype.dataToParams = function() {
		return {
			rows: _.extend( {}, this.data.rows ),
			columns: _.extend( {}, this.data.columns ),
			items: _.extend( {}, this.data.items ),
		}
	};

	MegaMenuGridBuilder.prototype.saveChanges = function( options ) {
		var self = this;

		if ( ! self.hasChanges ) {
			return;
		}

		$.post( ajaxurl, {
			action: options.action, //config.namespace + '_update_grid',
			data: self.dataToParams(),
			id: options.menuItemId
		}, function( response ) {
			if ( ! response.success ) {
				alert( response.message );
				return;
			}

			self.hasChanges = false;
		} );
	}

	MegaMenuGridBuilder.prototype.generateId = function() {
		if ( _.isEmpty( this.data.rows ) ) {
			return _.uniqueId();
		}

		var id = parseInt( _.max( this.data.rows, 'id' ).id ),
			max = _.isEmpty( this.data.columns ) ? 0 : parseInt( _.max( this.data.columns, 'id' ).id );

		id = Math.max( id, max ) + 1;

		return id;
	}

	MegaMenuGridBuilder.prototype.generateInputData = function( element ) {
		if ( ! element.dataset.name ) {
			return {};
		}

		var nameArray = element.dataset.name.split( '.' ),
			dataString = '',
			value = element.value;

		if ( ( 'checkbox' === element.type || 'radio' === element.type ) && ! element.checked ) {
			value = '';
		}

		nameArray.forEach( function( prop ) {
			dataString += '{"' + prop + '":';
		} );

		dataString += JSON.stringify( value );
		dataString += '}'.repeat( nameArray.length );

		return JSON.parse( dataString );
	}

	MegaMenuGridBuilder.prototype.openOptionsModal = function( id, type ) {
		var data = null;

		if ( 'row' === type ) {
			data = _.find( this.data.rows, { id: id } );
		} else if ( 'column' === type ) {
			data = _.find( this.data.columns, { id: id } );
		}

		if ( ! data ) {
			return;
		}

		var $modal = $( this.templates.options( data ) );

		// Init color pickers.
		$modal.find( 'input[data-type="colorpicker"]' ).wpColorPicker();

		$modal.data( 'id', id ).data( 'type', type ).addClass( 'open' ).appendTo( document.body );
	}

	MegaMenuGridBuilder.prototype.onCloseOptionsPopup = function( event ) {
		event.preventDefault();

		$( event.currentTarget ).closest( event.data.builder.selectors.optionsPopup ).remove();
	}

	MegaMenuGridBuilder.prototype.onSaveOptionsPopup = function( event ) {
		var self = event.data.builder,
			$popup = $( event.currentTarget ).closest( self.selectors.optionsPopup ),
			type = $popup.data( 'type' ),
			id = $popup.data( 'id' ),
			data = { id: id };

		if ( ! type || ! id ) {
			return;
		}

		$popup.find( ':input[data-name]' ).each( function() {
			data = $.extend( true, {}, data, self.generateInputData( this ) );
		} );

		if ( 'row' === type ) {
			self.updateRow( data );
		} else if ( 'column' === type ) {
			self.updateColumn( data );
		}

		$popup.remove();
	}

	/** Init */
	$( function() {
		kmm = new MegaMenu();
	} );
} )( jQuery, _, wp );

var megaMenuFieldName = function( name, id ) {
	name = name.split( '.' );
	name = '[' + name.join( '][' ) + ']';

	return 'menu-item-mega[' + id + ']' + name;
}
