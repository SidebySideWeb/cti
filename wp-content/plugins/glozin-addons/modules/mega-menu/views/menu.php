<div class="megamenu-modal__menu">
	<# if ( data.depth == 0 ) { #>
		<a href="#" class="media-menu-item {{ data.current === 'mega' ? 'active' : '' }}" data-panel="mega" data-title="<?php esc_attr_e( 'Mega Menu', 'glozin-addons' ) ?>"><?php esc_html_e( 'Mega Menu', 'glozin-addons' ) ?></a>
		<a href="#" class="media-menu-item {{ data.current === 'design' ? 'active' : '' }}" data-panel="design" data-title="<?php esc_attr_e( 'Mega Menu Design', 'glozin-addons' ) ?>"><?php esc_html_e( 'Design', 'glozin-addons' ) ?></a>
		<a href="#" class="media-menu-item {{ data.current === 'badges' ? 'active' : '' }}" data-panel="badges" data-title="<?php esc_attr_e( 'Badges', 'glozin-addons' ) ?>"><?php esc_html_e( 'Badges', 'glozin-addons' ) ?></a>
	<# } else if ( data.depth == 1 ) { #>
		<a href="#" class="media-menu-item {{ data.current === 'settings' ? 'active' : '' }}" data-panel="settings" data-title="<?php esc_attr_e( 'Menu Setting', 'glozin-addons' ) ?>"><?php esc_html_e( 'Settings', 'glozin-addons' ) ?></a>
		<a href="#" class="media-menu-item {{ data.current === 'content' ? 'active' : '' }}" data-panel="content" data-title="<?php esc_attr_e( 'Menu Content', 'glozin-addons' ) ?>"><?php esc_html_e( 'Content', 'glozin-addons' ) ?></a>
		<a href="#" class="media-menu-item {{ data.current === 'design' ? 'active' : '' }}" data-panel="design" data-title="<?php esc_attr_e( 'Mega Column Design', 'glozin-addons' ) ?>"><?php esc_html_e( 'Design', 'glozin-addons' ) ?></a>
	<# } else { #>
		<a href="#" class="media-menu-item {{ data.current === 'content' ? 'active' : '' }}" data-panel="content" data-title="<?php esc_attr_e( 'Menu Content', 'glozin-addons' ) ?>"><?php esc_html_e( 'Content', 'glozin-addons' ) ?></a>
	<# } #>
	<a href="#" class="media-menu-item {{ data.current === 'icon' ? 'active' : '' }}" data-panel="icon" data-title="<?php esc_attr_e( 'Menu Icon', 'glozin-addons' ) ?>"><?php esc_html_e( 'Icon', 'glozin-addons' ) ?></a>
</div>