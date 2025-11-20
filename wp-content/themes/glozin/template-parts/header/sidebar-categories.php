<?php
/**
 * Template part for displaying the header sidebar categories
 *
 * @package Glozin
 */

if ( ! has_nav_menu( 'category-menu' ) ) {
	return;
}

?>
<div id="header-sidebar-categories" class="header-sidebar-categories position-fixed start-0 z-100 d-none d-block-xl h-100">
	<div class="header-sidebar-categories__backdrop position-fixed top-0 start-0 end-0 d-block"></div>
	<div class="header-sidebar-categories__container bg-light border-right h-100 position-relative border-end z-3">
		<div class="header-sidebar-categories__header bg-primary text-light rounded-30 d-flex align-items-center gap-10">
			<?php echo \Glozin\Icon::get_svg( 'hamburger', 'ui', 'class=header-sidebar-categories__header-icon' ); ?>
			<span class="header-sidebar-categories__header-text fw-semibold"><?php echo esc_html__( 'Product Categories', 'glozin' ); ?></span>
		</div>
		<div class="header-sidebar-categories__content">
			<?php
				if ( class_exists( '\Glozin\Addons\Modules\Mega_Menu\Walker' ) ) {
					wp_nav_menu( apply_filters( 'glozin_navigation_category_menu_content', array(
						'theme_location' 	=> 'category-menu',
						'container'      	=> 'nav',
						'container_class'   => 'main-navigation category-navigation',
						'menu_class'     	=> 'nav-menu menu',
						'walker'			=> new \Glozin\Addons\Modules\Mega_Menu\Walker()
					) ) );
				} else {
					wp_nav_menu( apply_filters( 'glozin_navigation_category_menu_content', array(
						'theme_location' 	=> 'category-menu',
						'container'      	=> 'nav',
						'container_class'   => 'main-navigation category-navigation',
						'menu_class'     	=> 'nav-menu menu',
					) ) );
				}
			?>
		</div>
	</div>
</div>
