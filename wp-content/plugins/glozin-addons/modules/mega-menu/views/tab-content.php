<div class="megamenu-modal__panel" data-panel="tab-content">
	<div class="megamenu-modal__tab-content">
		<div class="megamenu-modal-grid__inside"></div>

		<div class="megamenu-modal-grid__actions">
			<button type="button" class="button" data-action="add-row" data-options="<?php echo esc_attr( json_encode( \Glozin\Addons\Modules\Mega_Menu\Module::default_row_options() ) ) ?>">
				<span class="dashicons dashicons-insert"></span>
				<span><?php esc_html_e( 'Add a row', 'glozin-addons' ) ?></span>
			</button>
		</div>
	</div>
</div>