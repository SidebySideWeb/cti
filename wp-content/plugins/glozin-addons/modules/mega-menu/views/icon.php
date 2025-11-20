<div class="megamenu-modal__panel" data-panel="icon">
	<div class="megamenu-modal__panel-toolbar">
		<div class="megamenu-modal__panel-option">
			<label>
				<span class="megamenu-modal__panel-option-label"><?php esc_html_e( 'Type', 'glozin-addons' ) ?></span>
				<select name="{{ megaMenuFieldName( 'icon_type', data.data['menu-item-db-id'] ) }}" data-toggle_condition="icon_type">
					<option value=""><?php esc_html_e( 'No icon', 'glozin-addons' ) ?></option>
					<?php if ( class_exists( '\Glozin\Icon' ) ) : ?>
						<option value="theme" {{ 'theme' === data.megaData.icon_type ? 'selected="selected"' : '' }}><?php esc_html_e( 'Theme', 'glozin-addons' ) ?></option>
					<?php endif; ?>
					<option value="image" {{ 'image' === data.megaData.icon_type ? 'selected="selected"' : '' }}><?php esc_html_e( 'Image', 'glozin-addons' ) ?></option>
					<option value="svg" {{ 'svg' === data.megaData.icon_type ? 'selected="selected"' : '' }}><?php esc_html_e( 'SVG Code', 'glozin-addons' ) ?></option>
				</select>
			</label>
		</div>

		<div class="megamenu-modal__panel-option" style="{{ 'svg' == data.megaData.icon_type ? '' : 'display: none;' }}" data-toggle_icon_type="svg">
			<label>
				<span class="megamenu-modal__panel-option-label"><?php esc_html_e( 'Position', 'glozin-addons' ) ?></span>
				<select name="{{ megaMenuFieldName( 'icon_position', data.data['menu-item-db-id'] ) }}">
					<option value="left"><?php esc_html_e( 'Left', 'glozin-addons' ) ?></option>
					<option value="right" {{ 'right' === data.megaData.icon_position ? 'selected="selected"' : '' }}><?php esc_html_e( 'Right', 'glozin-addons' ) ?></option>
				</select>
			</label>
		</div>
	</div>

	<?php if ( class_exists( '\Glozin\Icon' ) ) : ?>
		<div class="megamenu-modal__icon-picker megamenu-modal__icons-theme {{ 'theme' !== data.megaData.icon_type ? 'hidden' : '' }}" data-toggle_icon_type="theme">
			<div class="megamenu-modal__icon-preview">
				<span class="megamenu-modal__icon-selected" title="<?php esc_attr_e( 'Click to remove', 'glozin-addons' ) ?>" data-selected="{{ data.megaData.icon }}"></span>
				<input class="megamenu-modal__icon-search" type="text" placeholder="<?php esc_attr_e( 'Search Icon', 'glozin-addons' ) ?>">
				<input type="hidden" name="{{ megaMenuFieldName( 'icon', data.data['menu-item-db-id'] ) }}" value="{{ data.megaData.icon }}">
			</div>

			<hr>

			<div class="megamenu-modal__icon-list">
				<?php foreach ( \Glozin\Icon::$social_icons as $icon => $svg ) : ?>
					<span class="megamenu-modal__icon-list-item <?php echo esc_attr( $icon ) ?>" data-icon="<?php echo esc_attr( $icon ) ?>">
						<?php echo $svg; ?>
					</span>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>

	<div class="megamenu-modal__icon-picker megamenu-modal__icons-image {{ 'image' !== data.megaData.icon_type ? 'hidden' : '' }}" data-toggle_icon_type="image">
		<label><?php esc_html_e( 'Upload Image', 'glozin-addons' ) ?></label><br><br>
		<div class="megamenu-media megamenu-media--icon {{ data.megaData.icon_image ? '' : 'megamenu-media--empty' }}">
			<div class="megamenu-media__preview">
				<# if ( data.megaData.icon_image ) { #>
					<img src="{{ data.megaData.icon_image }}">
				<# } #>
			</div>
			<button type="button" class="megamenu-media__remove">
				<span class="dashicons dashicons-trash"></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Remove', 'glozin-addons' ) ?></span>
			</button>

			<input type="hidden" name="{{ megaMenuFieldName( 'icon_image', data.data['menu-item-db-id'] ) }}" value="{{ data.megaData.icon_image }}" data-image_input="url">
			<input type="hidden" name="{{ megaMenuFieldName( 'icon_image_id', data.data['menu-item-db-id'] ) }}" value="{{ data.megaData.icon_image_id }}" data-image_input="id">
		</div>
		<br>
	</div>

	<div class="megamenu-modal__icon-picker megamenu-modal__icons-svg {{ 'svg' !== data.megaData.icon_type ? 'hidden' : '' }}" data-toggle_icon_type="svg">
		<label>
			<span><?php esc_html_e( 'SVG Code', 'glozin-addons' ) ?></span><br>
			<textarea name="{{ megaMenuFieldName( 'icon_svg', data.data['menu-item-db-id'] ) }}" rows="10" class="widefat">{{ data.megaData.icon_svg }}</textarea>
		</label>
	</div>
</div>