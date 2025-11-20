<?php
/**
 * Template part for displaying the hamburger panel
 *
 * @package Glozin
 */

$menu_items = (array) \Glozin\Helper::get_option('header_mobile_menu_els');
$style = '';
if ( in_array( 'currency', $menu_items ) || in_array( 'language', $menu_items ) ) {
	$style = 'style=--gz-mobile-footer-height:60px;';
}

$rtl_class = is_rtl() ? 'offscreen-panel--side-right' : 'offscreen-panel--side-left';
?>

<div id="mobile-menu-panel" class="offscreen-panel hamburger-panel <?php echo esc_attr( $rtl_class ); ?>">
	<div class="panel__backdrop"></div>
	<div class="panel__container">
		<div class="panel__header bg-primary py-15 px-30 text-on-primary">
			<ul class="panel__menu-items fs-15 d-flex gap-30 list-unstyled fw-semibold">
				<li>
					<a class="active" href="#"><?php echo esc_html__( 'Menu', 'glozin' ); ?></a>
				</li>
				<?php if ( in_array( 'category-menu', $menu_items ) && ! empty( \Glozin\Helper::get_option('header_mobile_menu_category_menu') ) ) { ?>
				<li>
					<a href="#"><?php echo esc_html__( 'Categories', 'glozin' ); ?></a>
				</li>
			<?php } ?>
			</ul>
			<?php echo \Glozin\Icon::get_svg( 'close', 'ui', 'class=panel__button-close' ); ?>
		</div>
		<div class="panel__content" <?php echo esc_attr( $style ); ?>>
			<div class="panel__content-items active">
				<?php echo \Glozin\Header\Mobile::mobile_menu_items(); ?>
			</div>
			<div class="panel__content-items mobile-category-menu">
				<?php echo \Glozin\Header\Mobile::mobile_category_menu_items(); ?>
			</div>
		</div>
		<?php
		ob_start();
		\Glozin\Header\Mobile::mobile_menu_items_footer();
		$menu_items = ob_get_clean();
		$footer_class = empty( $menu_items ) ? 'd-none' : 'd-flex';
		?>
		<div class="panel__footer <?php echo esc_attr( $footer_class ); ?>">
			<?php echo ! empty( $menu_items ) ? $menu_items : '';?>
			<div class="submenu-items--heading d-none">
				<div class="submenu-items--title d-flex align-items-center fs-16 gap-15">
					<?php echo \Glozin\Icon::get_svg( 'arrow-left', 'ui', 'class=submenu__button-back' ); ?>
					<div class="submenu-items--title-text fw-semibold"></div>
				</div>
				<?php echo \Glozin\Icon::get_svg( 'close', 'ui', 'class=panel__button-close' ); ?>
			</div>
		</div>
	</div>
</div>