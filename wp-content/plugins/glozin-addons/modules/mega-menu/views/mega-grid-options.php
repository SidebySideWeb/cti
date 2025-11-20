<div class="megamenu-modal-grid__options-popup" tabindex="-1" role="dialog">
	<div class="megamenu-modal-grid__options-popup-content">
		<h2><?php esc_html_e( 'Options', 'glozin-addons' ) ?></h2>
		<button type="button" class="button button-close" data-action="close-options">
			<span class="dashicons dashicons-no-alt"></span>
			<span class="screen-reader-text"><?php esc_html_e( 'Close', 'glozin-addons' ) ?></span>
		</button>

		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><?php esc_html_e( 'Padding', 'glozin-addons' ) ?></th>
					<td>
						<fieldset class="megamenu-modal__option-spacing">
							<label>
								<input type="text" value="{{ data.padding.top }}" data-name="padding.top" size="4"><br>
								<span class="description"><?php esc_html_e( 'Top', 'glozin-addons' ) ?></span>
							</label>
							&nbsp;
							<label>
								<input type="text" value="{{ data.padding.bottom }}" data-name="padding.bottom" size="4"><br>
								<span class="description"><?php esc_html_e( 'Bottom', 'glozin-addons' ) ?></span>
							</label>
							&nbsp;
							<label>
								<input type="text" value="{{ data.padding.left }}" data-name="padding.left" size="4"><br>
								<span class="description"><?php esc_html_e( 'Left', 'glozin-addons' ) ?></span>
							</label>
							&nbsp;
							<label>
								<input type="text" value="{{ data.padding.right }}" data-name="padding.right" size="4"><br>
								<span class="description"><?php esc_html_e( 'Right', 'glozin-addons' ) ?></span>
							</label>
						</fieldset>
					</td>
				</tr>
				<# if ( ! data.row ) { #>
				<!-- Margin settings for rows only, not for columns -->
				<tr>
					<th scope="row"><?php esc_html_e( 'Margin', 'glozin-addons' ) ?></th>
					<td>
						<fieldset class="megamenu-modal__option-spacing">
							<label>
								<input type="text" value="{{ data.margin.top }}" data-name="margin.top" size="4"><br>
								<span class="description"><?php esc_html_e( 'Top', 'glozin-addons' ) ?></span>
							</label>
							&nbsp;
							<label>
								<input type="text" value="{{ data.margin.bottom }}" data-name="margin.bottom" size="4"><br>
								<span class="description"><?php esc_html_e( 'Bottom', 'glozin-addons' ) ?></span>
							</label>
						</fieldset>
					</td>
				</tr>
				<# } #>
				<tr>
					<th scope="row"><?php esc_html_e( 'Background', 'glozin-addons' ) ?></th>
					<td>
						<fieldset class="megamenu-modal__option-background">
							<div class="megamenu-modal__option-group">
								<div class="megamenu-modal__option-background-image megamenu-modal__option-background-field megamenu-media {{ data.background.image.url ? '' : 'megamenu-media--empty' }}">
									<span class="megamenu-media__preview">
										<# if ( data.background.image.url ) { #>
											<img src="{{ data.background.image.url }}">
										<# } #>
									</span>

									<button type="button" class="megamenu-media__remove">
										<span class="dashicons dashicons-trash"></span>
										<span class="screen-reader-text"><?php esc_html_e( 'Remove', 'glozin-addons' ) ?></span>
									</button>
									<input type="hidden" data-name="background.image.id" value="{{ data.background.image.id }}" data-image_input="id">
									<input type="hidden" data-name="background.image.url" value="{{ data.background.image.url }}" data-image_input="url">
								</div>

								<div class="megamenu-modal__option-background-position megamenu-modal__option-background-field">
									<label><?php esc_html_e( 'Image Position', 'glozin-addons' ) ?></label>
									<p>
										<select data-name="background.position.x" data-toggle_condition="rowbg_posx" data-toggle_scope="p">
											<option value="left" {{ 'left' == data.background.position.x ? 'selected="selected"' : '' }}><?php esc_html_e( 'Left', 'glozin-addons' ) ?></option>
											<option value="center" {{ 'center' == data.background.position.x ? 'selected="selected"' : '' }}><?php esc_html_e( 'Center', 'glozin-addons' ) ?></option>
											<option value="right" {{ 'right' == data.background.position.x ? 'selected="selected"' : '' }}><?php esc_html_e( 'Right', 'glozin-addons' ) ?></option>
											<option value="custom" {{ 'custom' == data.background.position.x ? 'selected="selected"' : '' }}><?php esc_html_e( 'Custom', 'glozin-addons' ) ?></option>
										</select>
										<br>
										<input
											type="text"
											size="6"
											data-name="background.position.custom_x"
											value="{{ data.background.position.custom_x }}"
											class="{{ 'custom' != data.background.position.x ? 'hidden' : '' }}"
											data-toggle_rowbg_posx="custom">
									</p>

									<p>
										<select data-name="background.position.y" data-toggle_condition="rowbg_posy" data-toggle_scope="p">
											<option value="top" {{ 'top' == data.background.position.y ? 'selected="selected"' : '' }}><?php esc_html_e( 'Top', 'glozin-addons' ) ?></option>
											<option value="center" {{ 'center' == data.background.position.y ? 'selected="selected"' : '' }}><?php esc_html_e( 'Middle', 'glozin-addons' ) ?></option>
											<option value="bottom" {{ 'bottom' == data.background.position.y ? 'selected="selected"' : '' }}><?php esc_html_e( 'Bottom', 'glozin-addons' ) ?></option>
											<option value="custom" {{ 'custom' == data.background.position.y ? 'selected="selected"' : '' }}><?php esc_html_e( 'Custom', 'glozin-addons' ) ?></option>
										</select>
										<br>
										<input
											type="text"
											size="6"
											data-name="background.position.custom_y"
											value="{{ data.background.position.custom_y }}"
											class="{{ 'custom' != data.background.position.y ? 'hidden' : '' }}"
											data-toggle_rowbg_posy="custom">
									</p>
								</div>
							</div>

							<div class="megamenu-modal__option-group">
								<p class="megamenu-modal__option-background-color megamenu-modal__option-background-field">
									<label><?php esc_html_e( 'Color', 'glozin-addons' ) ?></label>
									<input type="text" data-type="colorpicker" data-name="background.color" value="{{ data.background.color }}">
								</p>

								<p class="megamenu-modal__option-background-repeat megamenu-modal__option-background-field">
									<label><?php esc_html_e( 'Repeat', 'glozin-addons' ) ?></label>
									<select data-name="background.repeat">
										<option value="no-repeat" {{ 'no-repeat' == data.background.repeat ? 'selected="selected"' : '' }}><?php esc_html_e( 'No Repeat', 'glozin-addons' ) ?></option>
										<option value="repeat" {{ 'repeat' == data.background.repeat ? 'selected="selected"' : '' }}><?php esc_html_e( 'Tile', 'glozin-addons' ) ?></option>
										<option value="repeat-x" {{ 'repeat-x' == data.background.repeat ? 'selected="selected"' : '' }}><?php esc_html_e( 'Tile Horizontally', 'glozin-addons' ) ?></option>
										<option value="repeat-y" {{ 'repeat-y' == data.background.repeat ? 'selected="selected"' : '' }}><?php esc_html_e( 'Tile Vertically', 'glozin-addons' ) ?></option>
									</select>
								</p>

								<p class="megamenu-modal__option-background-attachment megamenu-modal__option-background-field">
									<label><?php esc_html_e( 'Attachment', 'glozin-addons' ) ?></label>
									<select data-name="background.attachment">
										<option value="scroll" {{ 'scroll' == data.background.attachment ? 'selected="selected"' : '' }}><?php esc_html_e( 'Scroll', 'glozin-addons' ) ?></option>
										<option value="fixed" {{ 'fixed' == data.background.attachment ? 'selected="selected"' : '' }}><?php esc_html_e( 'Fixed', 'glozin-addons' ) ?></option>
									</select>
								</p>

								<p class="megamenu-modal__option-background-size megamenu-modal__option-background-field">
									<label><?php esc_html_e( 'Size', 'glozin-addons' ) ?></label>
									<select data-name="background.size">
										<option value=""><?php esc_html_e( 'Default', 'glozin-addons' ) ?></option>
										<option value="cover" {{ 'cover' == data.background.size ? 'selected="selected"' : '' }}><?php esc_html_e( 'Cover', 'glozin-addons' ) ?></option>
										<option value="contain" {{ 'contain' == data.background.size ? 'selected="selected"' : '' }}><?php esc_html_e( 'Contain', 'glozin-addons' ) ?></option>
									</select>
								</p>
							</div>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Border', 'glozin-addons' ) ?></th>
					<td>
						<fieldset class="megamenu-modal__option-border">
							<div class="megamenu-modal__option-group">
								<div class="megamenu-modal__option-border-type megamenu-modal__option-background-field">
									<label><?php esc_html_e( 'Type', 'glozin-addons' ) ?></label>
									<select data-name="border.type">
										<option value=""><?php esc_html_e( 'None', 'glozin-addons' ) ?></option>
										<option value="solid" {{ data.border && data.border.type == 'solid' ? 'selected="selected"' : '' }}><?php esc_html_e( 'Solid', 'glozin-addons' ) ?></option>
										<option value="double" {{ data.border && data.border.type == 'double' ? 'selected="selected"' : '' }}><?php esc_html_e( 'Double', 'glozin-addons' ) ?></option>
										<option value="dotted" {{ data.border && data.border.type == 'dotted' ? 'selected="selected"' : '' }}><?php esc_html_e( 'Dotted', 'glozin-addons' ) ?></option>
										<option value="dashed" {{ data.border && data.border.type == 'dashed' ? 'selected="selected"' : '' }}><?php esc_html_e( 'Dashed', 'glozin-addons' ) ?></option>
										<option value="groove" {{ data.border && data.border.type == 'groove' ? 'selected="selected"' : '' }}><?php esc_html_e( 'Groove', 'glozin-addons' ) ?></option>
									</select>
								</div>
								<p class="megamenu-modal__option-border-color megamenu-modal__option-background-field">
									<label><?php esc_html_e( 'Border Color', 'glozin-addons' ) ?></label>
									<input
										type="text"
										data-type="colorpicker"
										data-name="border.color"
										value="{{ data.border && data.border.color ? data.border.color : '' }}">
								</p>
								<p class="megamenu-modal__option-border-width megamenu-modal__option-background-field">
									<label><?php esc_html_e( 'Width', 'glozin-addons' ) ?></label>
								</p>
								<p class="megamenu-modal__option-border-width">
									<label>
										<input type="text" value="{{ data.border && data.border.width && data.border.width.top ? data.border.width.top : '' }}" data-name="border.width.top" size="4"><br>
										<span class="description"><?php esc_html_e( 'Top', 'glozin-addons' ) ?></span>
									</label>
									&nbsp;
									<label>
										<input type="text" value="{{ data.border && data.border.width && data.border.width.bottom ? data.border.width.bottom : '' }}" data-name="border.width.bottom" size="4"><br>
										<span class="description"><?php esc_html_e( 'Bottom', 'glozin-addons' ) ?></span>
									</label>
									&nbsp;
									<label>
										<input type="text" value="{{ data.border && data.border.width && data.border.width.left ? data.border.width.left : '' }}" data-name="border.width.left" size="4"><br>
										<span class="description"><?php esc_html_e( 'Left', 'glozin-addons' ) ?></span>
									</label>
									&nbsp;
									<label>
										<input type="text" value="{{ data.border && data.border.width && data.border.width.right ? data.border.width.right : '' }}" data-name="border.width.right" size="4"><br>
										<span class="description"><?php esc_html_e( 'Right', 'glozin-addons' ) ?></span>
									</label>
								</p>
							</div>
						</fieldset>
					</td>
				</tr>
			</tbody>
		</table>

		<div class="megamenu-modal-grid__options-popup-toolbar">
			<button type="button" class="button button-primary button-large" data-action="save-options">
				<?php esc_html_e( 'Save Changes', 'glozin-addons' ) ?>
			</button>
		</div>
	</div>
	<div class="media-modal-backdrop"></div>
</div>