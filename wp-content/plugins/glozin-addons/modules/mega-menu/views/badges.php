<# if ( 0 == data.depth ) { #>
<div class="megamenu-modal__panel" data-panel="badges">
	<div class="megamenu-modal__panel-wrapper">
		<div class="megamenu-modal__panel-option">
			<label for="{{ megaMenuFieldName( 'badges_text', data.data['menu-item-db-id'] ) }}"><?php esc_html_e( 'Text', 'glozin-addons' ) ?></label>
			<input type="text" name="{{ megaMenuFieldName( 'badges_text', data.data['menu-item-db-id'] ) }}" value="{{ data.megaData.badges_text }}">
		</div>

		<div class="megamenu-modal__panel-option">
			<label for="{{ megaMenuFieldName( 'badges_background', data.data['menu-item-db-id'] ) }}"><?php esc_html_e( 'Background Color', 'glozin-addons' ) ?></label>
			<input type="text" data-type="colorpicker" name="{{ megaMenuFieldName( 'badges_background', data.data['menu-item-db-id'] ) }}" value="{{ data.megaData.badges_background }}">
		</div>

		<div class="megamenu-modal__panel-option">
			<label for="{{ megaMenuFieldName( 'badges_color', data.data['menu-item-db-id'] ) }}"><?php esc_html_e( 'Color', 'glozin-addons' ) ?></label>
			<input type="text" data-type="colorpicker" name="{{ megaMenuFieldName( 'badges_color', data.data['menu-item-db-id'] ) }}" value="{{ data.megaData.badges_color }}">
		</div>
	</div>
</div>
<# } #>