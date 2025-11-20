<?php
/**
 * Template part for displaying the search form
 *
 * @package Glozin
 */

?>

<div class="header-search">
	<form class="header-search__form gz-instant-search__form position-relative" method="get" action="<?php echo esc_url( home_url( '/' ) ) ?>" data-toggle="modal" data-target="search-modal">
		<button type="submit" aria-label="<?php esc_attr_e( 'Search', 'glozin' ); ?>" class="header-search__button gz-instant-search__button gz-button gz-button-icon position-absolute start-5 top-0">
			<?php echo \Glozin\Icon::inline_svg( [ 'icon' => 'icon-search', 'class' => 'has-vertical-align' ] ); ?>
		</button>
		<input type="text" name="s" class="header-search__field gz-instant-search__field" placeholder="<?php esc_attr_e("I'm looking for…", 'glozin') ?>" autocomplete="off">
		<input type="hidden" name="post_type" class="header-search__post-type" value="product">
		<a href="#" class="close-search-results position-absolute end-5 top-0 gz-button gz-button-icon invisible"><?php echo \Glozin\Icon::get_svg( 'close', 'ui'); ?></a>
	</form>
</div>
