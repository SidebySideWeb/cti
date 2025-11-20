<# var itemId = data.data['menu-item-db-id']; #>
<div class="megamenu-modal__panel" data-panel="design">
	<table class="form-table">
		<# if ( 1 == data.depth ) { #>
			<tr>
				<th scope="row"><?php esc_html_e( 'Padding', 'glozin-addons' ) ?></th>
				<td>
					<fieldset class="megamenu-modal__option-spacing">
						<label>
							<input type="text" value="{{ data.megaData.padding.top }}" name="{{ megaMenuFieldName( 'padding.top', itemId ) }}" size="4" placeholder="30px"><br>
							<span class="description"><?php esc_html_e( 'Top', 'glozin-addons' ) ?></span>
						</label>
						&nbsp;
						<label>
							<input type="text" value="{{ data.megaData.padding.bottom }}" name="{{ megaMenuFieldName( 'padding.bottom', itemId ) }}" size="4" placeholder="20px"><br>
							<span class="description"><?php esc_html_e( 'Bottom', 'glozin-addons' ) ?></span>
						</label>
						&nbsp;
						<label>
							<input type="text" value="{{ data.megaData.padding.left }}" name="{{ megaMenuFieldName( 'padding.left', itemId ) }}" size="4" placeholder="23px"><br>
							<span class="description"><?php esc_html_e( 'Left', 'glozin-addons' ) ?></span>
						</label>
						&nbsp;
						<label>
							<input type="text" value="{{ data.megaData.padding.right }}" name="{{ megaMenuFieldName( 'padding.right', itemId ) }}" size="4" placeholder="20px"><br>
							<span class="description"><?php esc_html_e( 'Right', 'glozin-addons' ) ?></span>
						</label>
					</fieldset>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php esc_html_e( 'Margin', 'glozin-addons' ) ?></th>
				<td>
					<fieldset class="megamenu-modal__option-spacing">
						<label>
							<input type="text" value="{{ data.megaData.margin.top }}" name="{{ megaMenuFieldName( 'margin.top', itemId ) }}" size="4"><br>
							<span class="description"><?php esc_html_e( 'Top', 'glozin-addons' ) ?></span>
						</label>
						&nbsp;
						<label>
							<input type="text" value="{{ data.megaData.margin.bottom }}" name="{{ megaMenuFieldName( 'margin.bottom', itemId ) }}" size="4"><br>
							<span class="description"><?php esc_html_e( 'Bottom', 'glozin-addons' ) ?></span>
						</label>
						&nbsp;
						<label>
							<input type="text" value="{{ data.megaData.margin.left }}" name="{{ megaMenuFieldName( 'margin.left', itemId ) }}" size="4"><br>
							<span class="description"><?php esc_html_e( 'Left', 'glozin-addons' ) ?></span>
						</label>
						&nbsp;
						<label>
							<input type="text" value="{{ data.megaData.margin.right }}" name="{{ megaMenuFieldName( 'margin.right', itemId ) }}" size="4"><br>
							<span class="description"><?php esc_html_e( 'Right', 'glozin-addons' ) ?></span>
						</label>
					</fieldset>
				</td>
			</tr>
		<# } #>

		<tr>
			<th scope="row"><?php esc_html_e( 'Background', 'glozin-addons' ) ?></th>
			<td>
				<fieldset class="megamenu-modal__option-background">

					<div class="megamenu-modal__option-background-image megamenu-modal__option-background-field megamenu-media {{ data.megaData.background.image ? '' : 'megamenu-media--empty' }}">
						<span class="megamenu-media__preview">
							<# if ( data.megaData.background.image ) { #>
								<img src="{{ data.megaData.background.image }}">
							<# } #>
						</span>

						<button type="button" class="megamenu-media__remove">
							<span class="dashicons dashicons-trash"></span>
							<span class="screen-reader-text"><?php esc_html_e( 'Remove', 'glozin-addons' ) ?></span>
						</button>

						<input type="hidden" name="{{ megaMenuFieldName( 'background.image', itemId ) }}" value="{{ data.megaData.background.image }}" data-image_input="url">
					</div>

					<div class="megamenu-modal__option-background-position megamenu-modal__option-background-field">
						<label><?php esc_html_e( 'Image Position', 'glozin-addons' ) ?></label>
						<p>
							<select name="{{ megaMenuFieldName( 'background.position.x', itemId ) }}" data-toggle_condition="bg_posx" data-toggle_scope="p">
								<option value="left" {{ 'left' == data.megaData.background.position.x ? 'selected="selected"' : '' }}><?php esc_html_e( 'Left', 'glozin-addons' ) ?></option>
								<option value="center" {{ 'center' == data.megaData.background.position.x ? 'selected="selected"' : '' }}><?php esc_html_e( 'Center', 'glozin-addons' ) ?></option>
								<option value="right" {{ 'right' == data.megaData.background.position.x ? 'selected="selected"' : '' }}><?php esc_html_e( 'Right', 'glozin-addons' ) ?></option>
								<option value="custom" {{ 'custom' == data.megaData.background.position.x ? 'selected="selected"' : '' }}><?php esc_html_e( 'Custom', 'glozin-addons' ) ?></option>
							</select>
							<br>
							<input
								type="text"
								size="6"
								name="{{ megaMenuFieldName( 'background.position.custom.x', itemId ) }}"
								value="{{ data.megaData.background.position.custom.x }}"
								class="{{ 'custom' != data.megaData.background.position.x ? 'hidden' : '' }}"
								data-toggle_bg_posx="custom">
						</p>

						<p>
							<select name="{{ megaMenuFieldName( 'background.position.y', itemId ) }}" data-toggle_condition="bg_posy" data-toggle_scope="p">
								<option value="top" {{ 'top' == data.megaData.background.position.y ? 'selected="selected"' : '' }}><?php esc_html_e( 'Top', 'glozin-addons' ) ?></option>
								<option value="center" {{ 'center' == data.megaData.background.position.y ? 'selected="selected"' : '' }}><?php esc_html_e( 'Middle', 'glozin-addons' ) ?></option>
								<option value="bottom" {{ 'bottom' == data.megaData.background.position.y ? 'selected="selected"' : '' }}><?php esc_html_e( 'Bottom', 'glozin-addons' ) ?></option>
								<option value="custom" {{ 'custom' == data.megaData.background.position.y ? 'selected="selected"' : '' }}><?php esc_html_e( 'Custom', 'glozin-addons' ) ?></option>
							</select>
							<br>
							<input
								type="text"
								size="6"
								name="{{ megaMenuFieldName( 'background.position.custom.y', itemId ) }}"
								value="{{ data.megaData.background.position.custom.y }}"
								class="{{ 'custom' != data.megaData.background.position.y ? 'hidden' : '' }}"
								data-toggle_bg_posy="custom">
						</p>
					</div>

					<p class="megamenu-modal__option-background-color megamenu-modal__option-background-field">
						<label><?php esc_html_e( 'Color', 'glozin-addons' ) ?></label>
						<input type="text" data-type="colorpicker" name="{{ megaMenuFieldName( 'background.color', itemId ) }}" value="{{ data.megaData.background.color }}">
					</p>

					<p class="megamenu-modal__option-background-repeat megamenu-modal__option-background-field">
						<label><?php esc_html_e( 'Repeat', 'glozin-addons' ) ?></label>
						<select name="{{ megaMenuFieldName( 'background.repeat', itemId ) }}">
							<option value="no-repeat" {{ 'no-repeat' == data.megaData.background.repeat ? 'selected="selected"' : '' }}><?php esc_html_e( 'No Repeat', 'glozin-addons' ) ?></option>
							<option value="repeat" {{ 'repeat' == data.megaData.background.repeat ? 'selected="selected"' : '' }}><?php esc_html_e( 'Tile', 'glozin-addons' ) ?></option>
							<option value="repeat-x" {{ 'repeat-x' == data.megaData.background.repeat ? 'selected="selected"' : '' }}><?php esc_html_e( 'Tile Horizontally', 'glozin-addons' ) ?></option>
							<option value="repeat-y" {{ 'repeat-y' == data.megaData.background.repeat ? 'selected="selected"' : '' }}><?php esc_html_e( 'Tile Vertically', 'glozin-addons' ) ?></option>
						</select>
					</p>

					<p class="megamenu-modal__option-background-attachment megamenu-modal__option-background-field">
						<label><?php esc_html_e( 'Attachment', 'glozin-addons' ) ?></label>
						<select name="{{ megaMenuFieldName( 'background.attachment', itemId ) }}">
							<option value="scroll" {{ 'scroll' == data.megaData.background.attachment ? 'selected="selected"' : '' }}><?php esc_html_e( 'Scroll', 'glozin-addons' ) ?></option>
							<option value="fixed" {{ 'fixed' == data.megaData.background.attachment ? 'selected="selected"' : '' }}><?php esc_html_e( 'Fixed', 'glozin-addons' ) ?></option>
						</select>
					</p>

					<p class="megamenu-modal__option-background-size megamenu-modal__option-background-field">
						<label><?php esc_html_e( 'Size', 'glozin-addons' ) ?></label>
						<select name="{{ megaMenuFieldName( 'background.size', itemId ) }}">
							<option value=""><?php esc_html_e( 'Default', 'glozin-addons' ) ?></option>
							<option value="cover" {{ 'cover' == data.megaData.background.size ? 'selected="selected"' : '' }}><?php esc_html_e( 'Cover', 'glozin-addons' ) ?></option>
							<option value="contain" {{ 'contain' == data.megaData.background.size ? 'selected="selected"' : '' }}><?php esc_html_e( 'Contain', 'glozin-addons' ) ?></option>
						</select>
					</p>

				</fieldset>
			</td>
		</tr>
	</table>
</div>
