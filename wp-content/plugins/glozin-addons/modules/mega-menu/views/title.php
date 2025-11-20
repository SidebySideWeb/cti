<h3>{{{ data.title }}}</h3>

<h5><?php esc_html_e( 'Menu item', 'glozin-addons' ) ?>: {{{ data.item_title }}}</h5>
<# if ( data.depth ) { #>
	<span class="separator">|</span>
	<button class="megamenu-modal__back-settings button-link">
		<?php esc_html_e( 'Back to Top', 'glozin-addons' ) ?>
	</button>
<# } #>
