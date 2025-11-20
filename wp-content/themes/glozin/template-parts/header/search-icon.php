<?php
/**
 * Template part for displaying the search icon
 *
 * @package Glozin
 */

?>

<a href="#" class="gz-button gz-button-text gz-button-icon header-search__icon" data-toggle="modal" data-target="search-modal">
	<?php echo \Glozin\Icon::get_svg( 'search' ); ?>
	<span class="screen-reader-text"><?php esc_html_e( 'Search', 'glozin' ) ?></span>
</a>
