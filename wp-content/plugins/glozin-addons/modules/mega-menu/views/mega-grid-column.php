<div class="megamenu-modal-grid__column" data-id="{{ data.id }}" data-width="{{ data.width }}" style="{{ 'auto' !== data.width ? 'width:' + data.width + '%' : '' }}">
	<div class="megamenu-modal-grid__column-inside">
		<div class="megamenu-modal-grid__column-actions">
			<div class="megamenu-modal-grid__column-resize">
				<button type="button" class="button-link" data-action="resize" data-dir="decrease">
					<span class="dashicons dashicons-arrow-left-alt2"></span>
					<span class="screen-reader-text"><?php esc_html_e( 'Decrease width', 'glozin-addons' ) ?></span>
				</button>
				<span class="megamenu-modal-grid__column-width-label">{{{ 'auto' === data.width ? '<?php esc_html_e( 'Auto', 'glozin-addons' ) ?>' : data.width + '%' }}}</span>
				<button type="button" class="button-link" data-action="resize" data-dir="increase">
					<span class="dashicons dashicons-arrow-right-alt2"></span>
					<span class="screen-reader-text"><?php esc_html_e( 'Increase width', 'glozin-addons' ) ?></span>
				</button>
				<input type="hidden" data-name="width" value="{{ data.width }}">
			</div>

			<button type="button" class="button-link" data-action="toggle-options">
				<span class="dashicons dashicons-ellipsis"></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Options', 'glozin-addons' ) ?></span>
			</button>
			<ul class="megamenu-modal-grid__column-options">
				<li><button type="button" class="button" data-action="options"><?php esc_html_e( 'Options', 'glozin-addons' ) ?></button></li>
				<li><button type="button" class="button" data-action="reset-width"><?php esc_html_e( 'Reset width', 'glozin-addons' ) ?></button></li>
				<li><button type="button" class="button" data-action="delete"><?php esc_html_e( 'Delete', 'glozin-addons' ) ?></button></li>
			</ul>
		</div>

		<ul class="megamenu-modal-grid__items">
			<# _.each( data.items, function( item, index ) { #>
				<li class="menu-item menu-item-depth-{{ item.subDepth }}">
					<span>{{{ item.title }}}</span>
				</li>
			<# } ); #>
		</ul>
		<div class="megamenu-modal-grid__items-actions">
			<button type="button" class="button-link" data-action="add-item" title="<?php esc_attr_e( 'Add items to this column', 'glozin-addons' ) ?>">
				<span class="dashicons dashicons-plus"></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Add item', 'glozin-addons' ) ?></span>
			</button>
		</div>
	</div>
</div>
