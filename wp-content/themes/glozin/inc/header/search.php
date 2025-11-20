<?php
/**
 * Search AJAX template hooks.
 *
 * @package Glozin
 */

namespace Glozin\Header;
use Glozin\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Header Search Form template.
 */
class Search {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {

	}

	public static function get_trending() {
		if( ! Helper::get_option('header_search_trending') ) {
			return;
		}

		$trending_searches = (array) apply_filters( 'glozin_search_trending', Helper::get_option( 'header_search_links' ) );

		if ( empty( $trending_searches ) ) {
			return;
		}

		?>
		<div class="header-search__trending">
			<h5 class="header-search__suggestion-label mt-0 mb-15"><?php esc_html_e( 'Trending Search', 'glozin' ); ?></h5>
			<ul class="header-search__trending-links list-unstyled d-flex flex-wrap gap-10 align-items-center">
				<?php
				foreach ( $trending_searches as $trending_search ) {
					$url = $trending_search['url'];
					printf(
						'<li><a class="gz-button gz-button-outline" href="%s">%s</a></li>',
						esc_url( $trending_search['url'] ),
						esc_html( $trending_search['text'] )
					);
				}
				?>
			</ul>
		</div>
		<?php

	}

	public static function get_products() {
		self::products();
	}

	public static function get_products_ids() {
		if( ! Helper::get_option('header_search_products') ) {
			return;
		}

		if ( ! class_exists( 'WC_Shortcode_Products' ) ) {
            return;
        }

        $limit = Helper::get_option( 'header_search_product_limit' );
        $type  = Helper::get_option( 'header_search_products_type' );

        if('none' == $type){
            return;
        }

        $atts = array(
            'per_page'     => intval( $limit ),
        );

        switch ( $type ) {
            case 'sale_products':
            case 'top_rated_products':
				$atts['orderby'] =  'title';
				$atts['order'] =  'ASC';
                break;

			case 'featured_products':
            case 'recent_products':
				$atts['orderby'] =  'date';
				$atts['order'] =  'DESC';
                break;
        }

        $args  = new \WC_Shortcode_Products( $atts, $type );
        $args  = $args->get_query_args();
        $query = new \WP_Query( $args );

		return $query->posts;
	}

	public static function products() {
		$products_ids = self::get_products_ids();

		if( empty($products_ids) || ! count( $products_ids ) ) {
            return;
        }

		$swiper_options = array(
			'slidesPerView' => array(
				'desktop' => 5,
				'tablet' => 3,
				'mobile' => 1,
			),
			'spaceBetween' => array(
				'desktop' => 20,
				'tablet' => 20,
				'mobile' => 15,
			),
		);
		?>
       	<div class="header-search__products">
	   		<h5 class="header-search__suggestion-label mt-0 mb-15"><?php esc_html_e( 'Popular Products', 'glozin' ); ?></h5>
			<div class="swiper glozin-swiper glozin-product-carousel gz-arrows-middle navigation-class--tabletdots navigation-class--mobiledots" data-swiper=<?php echo esc_attr( json_encode( $swiper_options ) ); ?> data-desktop="<?php echo esc_attr( $swiper_options['slidesPerView']['desktop'] ); ?>" data-tablet="<?php echo esc_attr( $swiper_options['slidesPerView']['tablet'] ); ?>" data-mobile="<?php echo esc_attr( $swiper_options['slidesPerView']['mobile'] ); ?>">
				<?php
					wc_setup_loop(
						array(
							'columns' => $swiper_options['slidesPerView']['desktop'],
						)
					);
					self::get_template_loop( $products_ids );
				?>
				<?php \Glozin\Helper::get_swiper_navigation(); ?>
				<?php \Glozin\Helper::get_swiper_pagination(); ?>
			</div>
        </div>
		<?php
	}

	/**
	 * Loop over products
	 *
	 * @since 1.0.0
	 *
	 * @param string
	 */
	public static function get_template_loop( $products_ids, $template = 'product' ) {
		if( empty( $products_ids ) ) {
			return;
		}
		update_meta_cache( 'post', $products_ids );
		update_object_term_cache( $products_ids, 'product' );

		$original_post = $GLOBALS['post'];

		woocommerce_product_loop_start();

		foreach ( $products_ids as $product_id ) {
			$GLOBALS['post'] = get_post( $product_id ); // WPCS: override ok.
			setup_postdata( $GLOBALS['post'] );
			wc_get_template_part( 'content', $template );
		}

		$GLOBALS['post'] = $original_post; // WPCS: override ok.

		woocommerce_product_loop_end();

		wp_reset_postdata();
		wc_reset_loop();
	}

	public static function products_suggest() {
		$products_ids = (array) self::get_products_ids();

		if( ! count( $products_ids ) ) {
            return;
        }

		?>
       <ul class="search-products-suggest-list list-unstyled">
			<?php
				foreach( $products_ids as $id ) {
					$_product = wc_get_product( $id );
					$price    = $_product->get_price_html();
					$image_id = get_post_thumbnail_id( $id );
					?>
						<li class="d-flex">
							<div class="suggest-list__image">
								<a class="suggest-list__link gz-ratio" href="<?php echo esc_url( get_permalink( $id )); ?>">
									<img src="<?php echo esc_url( wp_get_attachment_url( $image_id ) ); ?>" alt="<?php echo esc_attr( $_product->get_title() ); ?>"/>
								</a>
							</div>
							<div class="suggest-list__content">
								<div class="suggest-list__title">
									<a href="<?php echo esc_url( get_permalink( $id )); ?>">
										<?php echo esc_html( $_product->get_title() ); ?>
									</a>
								</div>
								<div class="suggest-list__price"><?php echo wp_kses_post( $price ); ?></div>
							</div>
						</li>
					<?php
				}
			?>
        </ul>
		<?php
	}
}
