<?php
/**
 * Template part for displaying the search modal
 *
 * @package Glozin
 */

$post_type = 'product';
if( \Glozin\Helper::is_blog() || is_singular('post') ) {
	$post_type = 'post';
}

$search_layout = $args['search_layout'];
$search_type = $args['search_type'];
$view_all_link = function_exists('wc_get_page_id') ? get_permalink( wc_get_page_id( 'shop' ) ) : '';
$classes = 'search-type-' . esc_attr( $search_type );

if( $search_layout === 'form' ) {
	$classes .= ' modal-to-form';
}

if( $search_type === 'popup' ) {
	$classes .= ' animation-slide--top';
}

if( $search_type === 'sidebar' ) {
	$classes .= is_rtl() ? ' animation-slide--left' : ' animation-slide--right';
}
$modal_title_class = $search_type === 'popup' ? 'd-none d-block-md text-center mt-0 mb-15 fs-24' : 'py-18 px-30 border-bottom my-0 h5';
$modal_sidebar_class = $search_type === 'sidebar' ? 'mt-30 px-30' : '';
?>
<div id="search-modal" class="search-modal modal animation-slide <?php echo esc_attr( $classes ); ?>">
	<div class="modal__backdrop"></div>
	<div class="modal__container">
		<div class="modal__wrapper">
			<div class="modal__header">
				<div class="container-xxl">
					<h4 class="search-modal__title <?php echo esc_attr( $modal_title_class ); ?>"><?php esc_html_e('Search Our Site', 'glozin'); ?></h4>
					<div class="d-flex align-items-center gap-0 <?php echo esc_attr( $modal_sidebar_class ); ?>">
						<form class="search-modal__form gz-instant-search__form flex-grow-1 position-relative" method="get" action="<?php echo esc_url( home_url( '/' ) ) ?>">
							<input type="text" name="s" class="search-modal__field gz-instant-search__field w-100" placeholder="<?php esc_attr_e("I'm looking for…", 'glozin') ?>" autocomplete="off">
							<input type="hidden" name="post_type" class="search-modal__post-type" value="<?php echo esc_attr( $post_type ); ?>">
							<a href="#" class="close-search-results position-absolute end-45 top-0 gz-button gz-button-icon invisible"><?php echo \Glozin\Icon::get_svg( 'close', 'ui'); ?></a>
							<button type="submit" class="search-modal__button gz-instant-search__button gz-button gz-button-icon position-absolute end-5 top-0">
								<?php echo \Glozin\Icon::inline_svg( [ 'icon' => 'icon-search', 'class' => 'has-vertical-align' ] ); ?>
							</button>
						</form>
						<a href="#" class="modal__button-close d-inline-flex align-items-center justify-content-end">
							<?php echo \Glozin\Icon::get_svg( 'close', 'ui' ); ?>
						</a>
					</div>
				</div>
			</div>
			<div class="modal__content woocommerce">
				<div class="container-xxl">
					<?php do_action('glozin_search_modal_before_form'); ?>
					<div class="modal__content-suggestion d-flex flex-column gap-25 mt-25">
						<?php
						\Glozin\Header\Search::get_trending();
						\Glozin\Header\Search::get_products();
						?>
					</div>
					<?php do_action('glozin_search_modal_after_form'); ?>
				</div>
			</div>
		</div>
	</div>
</div>