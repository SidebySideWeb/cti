<?php
/**
 * Single Product Layout hooks.
 *
 * @package Glozin
 */

namespace Glozin\WooCommerce\Single_Product;

use Glozin\Helper;
use Glozin\Icon;
use WC_Brands;

use function WPML\FP\Strings\remove;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class product layout of Single Product
 */
class Product_Base {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * Post ID
	 *
	 * @var $post_id
	 */
	protected static $post_id = null;

	/**
	 * Tabs unset
	 *
	 * @var $unset_tabs
	 */
	protected static $unset_tabs = array();

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
		self::$post_id = get_the_ID();
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ), 20 );
		add_filter( 'glozin_wp_script_data', array( $this, 'single_product_script_data' ), 10, 3 );

		// Site content container
		add_filter( 'glozin_site_content_container_class', array( $this, 'site_content_container_class' ), 10, 1 );

		add_action( 'woocommerce_before_single_product', array( $this, 'add_post_class' ) );
		add_action( 'woocommerce_before_single_product_summary', array( $this, 'remove_post_class' ) );

		// Page Header
		add_filter( 'glozin_get_page_header_elements', array( $this, 'page_header_elements' ) );

		// Breadcrumb Navigation.
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
		add_action( 'woocommerce_before_single_product_summary', array( $this, 'breadcrumb_navigation'), 1 );

		// Gallery summary wrapper
		add_action( 'woocommerce_before_single_product_summary', array(	$this, 'open_gallery_summary_wrapper' ), 1 );
		add_action( 'woocommerce_after_single_product_summary', array( $this, 'close_gallery_summary_wrapper' ), 2 );

		// Gallery thumbnail
		add_filter( 'woocommerce_single_product_image_gallery_classes', array( $this, 'single_product_image_gallery_classes' ), 20, 1 );
		add_action( 'woocommerce_product_thumbnails', array( $this, 'product_gallery_thumbnails' ), 20 );

		// Featured icons mobile
		add_action( 'woocommerce_before_single_product_summary', array( $this, 'product_featured_buttons_mobile' ), 20 );

		// Replace the default sale flash.
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash' );

		// Taxonomy and Brand
		add_action( 'woocommerce_single_product_summary', array( $this, 'product_taxonomy' ), 2 );

		// Change rating
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
		add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 7 );

		// Price
		add_action('woocommerce_single_product_summary', array( $this, 'open_product_price' ), 9 );
		add_action('woocommerce_single_product_summary', array( $this, 'close_product_price' ), 12 );

		// Remove excerpt
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
		add_action( 'woocommerce_single_product_summary', array( $this, 'short_description' ), 20 );

		// Shipping & Promotions Information
		if( Helper::get_option( 'product_shipping_promotions_position' ) == 'description' ) {
			add_action( 'woocommerce_single_product_summary', array( $this, 'product_shipping_promotions' ), 21 );
		} else {
			add_action( 'woocommerce_single_product_summary', array( $this, 'product_shipping_promotions' ), 35 );
		}

		// Add product countdown
		add_action( 'woocommerce_single_product_summary', array( $this, 'product_countdown' ), 25 );

		// Extra link
		if( ! empty( $product = wc_get_product( self::$post_id ) ) && $product->is_type('variable') ) {
			add_action( 'woocommerce_after_variations_table', array( $this, 'product_extra_link' ), 35 );
		} else {
			add_action( 'woocommerce_single_product_summary', array( $this, 'product_extra_link' ), 29 );
		}

		// Add data product variations
		add_filter( 'woocommerce_available_variation', array( $this, 'data_product_variations' ), 10, 3 );

		// Guarantee Safe Checkout
		add_action( 'woocommerce_single_product_summary', array( $this, 'guarantee_safe_checkout' ), 90 );

		// Product Highlights
		add_action( 'woocommerce_after_single_product_summary', array( $this, 'product_highlights' ), 9 );

		// Product Tabs
		if( Helper::get_option( 'product_tabs_layout' ) == 'accordion' ) {
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
			if( Helper::get_option( 'product_tabs_position' ) == 'under-summary' ) {
				add_filter( 'woocommerce_product_tabs', array( $this, 'unset_review_tab' ), 98 );
				add_action( 'woocommerce_single_product_summary', array( $this, 'product_tabs' ), 95 );
				add_action( 'woocommerce_after_single_product_summary', array( $this, 'show_reviews' ), 10 );
			} else {
				add_action( 'woocommerce_after_single_product_summary', array( $this, 'product_tabs' ), 10 );
			}
		}

		if( class_exists('WC_Brands') ) {
			$global_brand = $GLOBALS['WC_Brands'];
			remove_action( 'woocommerce_product_meta_end',  array( $global_brand, 'show_brand' ) );
		}

		if( Helper::get_option('product_brands') ) {
			add_action( 'woocommerce_product_meta_end',  array( $this, 'show_brand' ) );
		}

		// Reviews
		add_action( 'woocommerce_review_before', array( $this, 'review_before_open' ), 1 );
		add_action( 'woocommerce_review_before_comment_text', array( $this, 'review_before_close' ), 1 );

		add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'single_add_to_cart_text' ) );

		add_filter( 'woocommerce_gallery_image_html_attachment_image_params', array( $this, 'woocommerce_gallery_image_html_attachment_image_params' ), 20, 4 );
	}

	/**
	 * WooCommerce specific scripts & stylesheets.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function scripts() {
		if(wp_script_is('glozin-countdown', 'registered')) {
			wp_enqueue_script( 'glozin-countdown' );
		}
		$args = array(
			'jquery'
		);

		if( Helper::get_option( 'product_image_zoom' ) !== 'none' ) {
			wp_enqueue_style( 'driff-style', get_template_directory_uri() . '/assets/css/plugins/drift-basic.css');
			wp_enqueue_script( 'driff-js', get_template_directory_uri() . '/assets/js/plugins/drift.min.js', array(), '', true );

			$args[] = 'driff-js';
		}

		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script( 'glozin-single-product', get_template_directory_uri() . '/assets/js/woocommerce/single-product' . $debug . '.js', $args, \Glozin\Helper::get_theme_version(), array('strategy' => 'defer') );
		wp_enqueue_style( 'glozin-single-product', apply_filters( 'glozin_get_style_directory_uri', get_template_directory_uri() ) . '/assets/css/woocommerce/single-product' . $debug . '.css', array(), \Glozin\Helper::get_theme_version() );
	}

	/**
	 * Single product script data.
	 *
	 * @since 1.0.0
	 *
	 * @param $data
	 *
	 * @return array
	 */
	public function single_product_script_data( $data ) {
		$data['product_gallery_slider'] = self::product_gallery_is_slider();
		$data['product_tabs_layout']    = Helper::get_option( 'product_tabs_layout' );
		$data['product_image_zoom']     = Helper::get_option( 'product_image_zoom' );
		$data['product_image_lightbox'] = Helper::get_option( 'product_image_lightbox' );
		$data['product_card_hover'] 	= Helper::get_option( 'product_card_hover' );
		$data['product_highlights'] 	= Helper::get_option( 'product_highlights' );
		$data['product_clickable_outofstock_variations'] = Helper::get_option( 'product_clickable_outofstock_variations' );

		if( Helper::get_option( 'product_tabs_layout' ) == 'accordion' ) {
			$data['product_tabs_status'] = Helper::get_option( 'product_tabs_status' );
		}

		return $data;
	}

	/**
	 * Site content container class
	 *
	 * @return string $classes
	 */
	public function site_content_container_class( $classes ) {
		return 'container';
	}

	public function add_post_class() {
		add_filter( 'post_class', array( $this, 'product_class' ), 10, 3 );
	}

	public function remove_post_class() {
		$rm_filter = 'remove_filter';
		$rm_filter( 'post_class', array( $this, 'product_class' ), 10, 3 );
	}

	/**
	 * Adds classes to products
     *
	 * @since 1.0.0
	 *
	 * @param string $class Post class.
	 *
	 * @return array
	 */
	public function product_class( $classes ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return $classes;
		}

		if( class_exists( '\WCBoost\Wishlist\Frontend') ) {
			$classes[] = 'has-wishlist';
		}

		if( class_exists( '\WCBoost\ProductsCompare\Frontend') ) {
			$classes[] = 'has-compare';
		}

		if( Helper::get_option('product_clickable_outofstock_variations') ) {
			$classes[] = 'has-clickable-outofstock-variations';
		}

		return $classes;
	}

	/**
	 * Products header.
	 *
	 *  @return void
	 */
	public function page_header_elements( $items ) {
		$items = [];

		return $items;
	}

	/**
	 * Navigation
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function breadcrumb_navigation() {
		$taxonomy    = 'product_cat';
		$terms = \Glozin\WooCommerce\Helper::get_product_taxonomy( $taxonomy );
		$term_link = $term_name = '';
		if( !is_wp_error( $terms ) && !empty($terms) ) {
			$term_link = get_term_link( $terms[0], $taxonomy );
			$term_name = $terms[0]->name;
		} else {
			$shop_page_id = wc_get_page_id( 'shop' );
			$shop_page    = get_post( $shop_page_id );
			$term_name = get_the_title( $shop_page );
			$term_link = get_the_permalink( $shop_page );
		}
		$prevProduct = get_previous_post( true, '', $taxonomy );
		$nextProduct = get_next_post( true, '', $taxonomy );
		?>
		<div class="glozin-breadcrumb-navigation-wrapper position-relative d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-10">
			<?php woocommerce_breadcrumb(); ?>

			<div class="product-navigation position-relative d-none d-flex-md align-items-center gap-10">
				<?php if( is_rtl() ) : ?>
					<?php if( ! empty( $nextProduct ) ) : ?>
						<a class="product-navigation__button product-navigation__button-next d-inline-flex align-items-center justify-content-center text-dark-grey fs-11 py-10 pe-10" href="<?php echo esc_url( get_permalink( $nextProduct ) ); ?>" aria-label="<?php echo esc_attr__( 'Next product', 'glozin' ); ?>">
							<?php echo Icon::inline_svg(['icon' => 'icon-next', 'class' => 'has-vertical-align']); ?>
						</a>
					<?php endif; ?>
				<?php else : ?>
					<?php if( ! empty( $prevProduct ) ) : ?>
						<a class="product-navigation__button product-navigation__button-prev d-inline-flex align-items-center justify-content-center text-dark-grey fs-11 py-10 pe-10" href="<?php echo esc_url( get_permalink( $prevProduct ) ); ?>" aria-label="<?php echo esc_attr__( 'Previous product', 'glozin' ); ?>">
							<?php echo Icon::inline_svg(['icon' => 'icon-back', 'class' => 'has-vertical-align']); ?>
						</a>
					<?php endif; ?>
				<?php endif; ?>

				<a class="product-navigation__button d-inline-flex text-dark gz-tooltip fs-16" href="<?php echo esc_url($term_link); ?>" data-tooltip="<?php echo esc_attr__( 'Back to products', 'glozin' ); ?>" aria-label="<?php echo esc_attr__( 'Back to products', 'glozin' ); ?>">
					<?php echo Icon::get_svg( 'object-column' ); ?>
				</a>

				<?php if( is_rtl() ) : ?>
					<?php if( ! empty( $prevProduct ) ) : ?>
						<a class="product-navigation__button product-navigation__button-prev d-inline-flex align-items-center justify-content-center text-dark-grey fs-11 py-10 ps-10" href="<?php echo esc_url( get_permalink( $prevProduct ) ); ?>" aria-label="<?php echo esc_attr__( 'Previous product', 'glozin' ); ?>">
							<?php echo Icon::inline_svg(['icon' => 'icon-back', 'class' => 'has-vertical-align']); ?>
						</a>
					<?php endif; ?>
				<?php else : ?>
					<?php if( ! empty( $nextProduct ) ) : ?>
						<a class="product-navigation__button product-navigation__button-next d-inline-flex align-items-center justify-content-center text-dark-grey fs-11 py-10 ps-10" href="<?php echo esc_url( get_permalink( $nextProduct ) ); ?>" aria-label="<?php echo esc_attr__( 'Next product', 'glozin' ); ?>">
							<?php echo Icon::inline_svg(['icon' => 'icon-next', 'class' => 'has-vertical-align']); ?>
						</a>
					<?php endif; ?>
				<?php endif; ?>
				<?php if( ! empty( $nextProduct ) ) : ?>
					<div class="product-navigation__tooltip product-navigation__tooltip-next position-absolute end-0 d-none d-flex-md gap-10 px-10 py-10 bg-light z-2 rounded-5 invisible pe-none">
						<div class="product-navigation__tooltip-image">
							<a href="<?php echo esc_url( get_permalink( $nextProduct ) ); ?>" aria-label="<?php echo esc_attr__( 'Next product', 'glozin' ); ?>">
								<?php 
								$product = wc_get_product($nextProduct->ID);
								echo ! empty( $product ) ? $product->get_image('woocommerce_gallery_thumbnail') : '';
								?>
							</a>
						</div>
						<div class="product-navigation__tooltip-summary">
							<div class="product-navigation__tooltip-title fs-14 fw-medium lh-normal">
								<a href="<?php echo esc_url( get_permalink( $nextProduct ) ); ?>" aria-label="<?php echo esc_attr__( 'Next product', 'glozin' ); ?>">
									<?php echo esc_html( $nextProduct->post_title ); ?>
								</a>
							</div>
							<div class="product-navigation__tooltip-price fs-13 mt-8 lh-normal"><p class="price"><?php echo wc_get_product($nextProduct->ID)->get_price_html(); ?></p></div>
						</div>
					</div>
				<?php endif; ?>
				<?php if( ! empty( $prevProduct ) ) : ?>
					<div class="product-navigation__tooltip product-navigation__tooltip-prev position-absolute end-0 d-none d-flex-md gap-10 px-10 py-10 bg-light z-2 rounded-5 invisible pe-none">
						<div class="product-navigation__tooltip-image">
							<a href="<?php echo esc_url( get_permalink( $prevProduct ) ); ?>" aria-label="<?php echo esc_attr__( 'Previous product', 'glozin' ); ?>">
								<?php 
								$product = wc_get_product($prevProduct->ID);
								echo ! empty( $product ) ? $product->get_image('woocommerce_gallery_thumbnail') : '';
								?>
							</a>
						</div>
						<div class="product-navigation__tooltip-summary">
							<div class="product-navigation__tooltip-title fs-14 fw-medium lh-normal">
								<a href="<?php echo esc_url( get_permalink( $prevProduct ) ); ?>" aria-label="<?php echo esc_attr__( 'Previous product', 'glozin' ); ?>">
									<?php echo esc_html( $prevProduct->post_title ); ?>
								</a>
							</div>
							<div class="product-navigation__tooltip-price fs-13 mt-8 lh-normal"><p class="price"><?php echo wc_get_product($prevProduct->ID)->get_price_html(); ?></p></div>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Open gallery summary wrapper
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function open_gallery_summary_wrapper() {
		$data = '';
		$data = apply_filters( 'glozin_product_gallery_summary_data', $data );
		echo '<div class="product-gallery-summary position-relative d-flex flex-column flex-md-row gap-30"'. esc_attr( $data ) .'>';
	}

	/**
	 * Close gallery summary wrapper
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function close_gallery_summary_wrapper() {
		echo '</div>';
	}

	/**
	 * Single product image gallery classes
	 *
	 * @param array $args
	 * @return array
	 */
	public static function single_product_image_gallery_classes( $classes ) {
		global $product;

		$gallery_layout = self::product_gallery_layout();

		if( empty( $gallery_layout ) ) {
            $classes[] = 'woocommerce-product-gallery--vertical';
            $classes[] = 'd-flex-md';
            $classes[] = 'flex-md-row-reverse';
			$classes[] = 'gap-10';
			$classes[] = 'position-sticky-md';
		} elseif( in_array( $gallery_layout, array( 'grid-1', 'grid-2', 'stacked' ) ) ) {
            $classes[] = 'woocommerce-product-gallery--grid';
            $classes[] = 'woocommerce-product-gallery--' . esc_attr( $gallery_layout );
			$classes[] = 'position-sticky-md';
        } else {
			$classes[] = 'woocommerce-product-gallery--horizontal';
			$classes[] = 'position-sticky-md';
		}

		$key = array_search( 'images', $classes );
		if ( $key !== false ) {
			unset( $classes[ $key ] );
		}

		$attachment_ids = $product->get_gallery_image_ids();

		if ( $attachment_ids && $product->get_image_id() ) {
			$classes[] = 'woocommerce-product-gallery--has-thumbnails';
		}

		if( Helper::get_option( 'product_image_zoom' ) !== 'none' ) {
			$classes[] = 'woocommerce-product-gallery--has-zoom';
		}

		if( Helper::get_option( 'mobile_single_product_gallery_arrows' ) ) {
			$classes[] = 'woocommerce-product-gallery--has-arrows-mobile';
		}

		return $classes;
	}

	/**
	 * Product gallery thumbnails
	 *
	 * @return void
	 */
	public function product_gallery_thumbnails() {
		global $product;

		$attachment_ids = apply_filters( 'glozin_single_product_gallery_image_ids', $product->get_gallery_image_ids() );

		if ( $attachment_ids && $product->get_image_id() ) {
			add_filter( 'woocommerce_single_product_flexslider_enabled', '__return_false' );
			
			if( ! in_array( self::product_gallery_layout(), array( 'grid-1', 'grid-2', 'stacked', 'hidden-thumbnails' ) ) ) {
				echo '<div class="glozin-product-gallery-thumbnails">';
					echo apply_filters( 'glozin_product_get_gallery_image', wc_get_gallery_image_html( $product->get_image_id() ), 1 );
					$index = 2;
					foreach ( $attachment_ids as $attachment_id ) {
						echo apply_filters( 'glozin_product_get_gallery_thumbnail', wc_get_gallery_image_html( $attachment_id ), $index );
						$index++;
					}
				echo '</div>';
			}
			$rm_filter = 'remove_filter';
			$rm_filter( 'woocommerce_single_product_flexslider_enabled', '__return_false' );
		}
	}

	public function product_featured_buttons_mobile() {
		if( class_exists('\WCBoost\Wishlist\Frontend') || class_exists('\WCBoost\ProductsCompare\Frontend') ) {
			echo '<div class="product-featured-icons product-featured-icons--mobile d-flex d-none-md flex-column gap-10 position-absolute top-15 end-15 z-2">';
				if( class_exists('\WCBoost\Wishlist\Frontend') ) {
					\WCBoost\Wishlist\Frontend::instance()->single_add_to_wishlist_button();
				}

				if( class_exists('\WCBoost\ProductsCompare\Frontend') ) {
					\WCBoost\ProductsCompare\Frontend::instance()->single_add_to_compare_button();
				}
			echo '</div>';
		}
	}

	

	/**
	 * Check if product gallery is slider.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function product_gallery_is_slider() {
		$support = true;

		if( in_array( self::product_gallery_layout(), array( 'grid-1', 'grid-2', 'stacked' ) ) ) {
			$support = false;
		}

		return apply_filters( 'glozin_product_gallery_is_slider', $support );
	}

	/**
	 * Glozin gallery layout
	 *
	 * @return void
	 */
	public static function product_gallery_layout() {
		return apply_filters( 'glozin_gallery_layout', Helper::get_option( 'product_gallery_layout' ) );
	}

	/**
	 * Product Short Description
	 *
	 * @return  void
	 */
	public static function short_description() {
		if( ! Helper::get_option( 'product_description' ) ) {
			return;
		}

		global $product;

		$content = $product->get_short_description();
		if( empty( $content ) ) {
			return;
		}
		echo '<div class="short-description">';
			$option = array(
				'more'   => esc_html__( 'Show More', 'glozin' ),
				'less'   => esc_html__( 'Show Less', 'glozin' )
			);

			echo sprintf('<div class="short-description__content">%s</div>', wpautop( do_shortcode( $content ) ));
			echo sprintf('
				<button class="short-description__more gz-button-subtle show hidden" data-settings="%s">%s</button>',
				htmlspecialchars(json_encode( $option )),
				esc_html__('Show More', 'glozin')
			);
		echo '</div>';

	}

	/**
	 * Shipping & Promotions Information
	 *
	 * @return void
	 */
	public function product_shipping_promotions() {
		if( ! Helper::get_option( 'product_shipping_promotions' ) ) {
			return;
		}

		$lists = apply_filters( 'glozin_product_shipping_promotions_list', (array) Helper::get_option( 'product_shipping_promotions_list' ) );
		if( empty( $lists ) || empty( $lists[0] ) ) {
			return;
		}

		$classes = array(
			'shipping-promotions-information--' . esc_attr( Helper::get_option( 'product_shipping_promotions_type' ) ),
		);

		if( Helper::get_option( 'product_shipping_promotions_type' ) == 'grid' ) {
			$classes[] = 'd-grid';
			$classes[] = 'pb-20';
		} else {
			$classes[] = 'py-md-5';
			$classes[] = 'px-20';
		}

		$classes_item = Helper::get_option( 'product_shipping_promotions_type' ) == 'list' ? 'border-bottom-dashed border-last-0 py-15' : 'flex-column justify-content-center text-center px-20';

		echo '<div class="shipping-promotions-information border mb-20 rounded-5 ' . esc_attr( implode( ' ', $classes ) ) . '">';
			foreach( $lists as $item ) {
				echo '<div class="shipping-promotions-information__item d-flex align-items-center gap-10 ' . esc_attr( $classes_item ) . '">';
					if( ! empty( $item['image'] ) ) {
						echo '<img src="' . esc_url( $item['image'] ) . '" alt="' . esc_html( 'Glozin' ) . '">';
					}
					
					if( ! empty( $item['description'] ) ) {
						echo '<div class="shipping-promotions-information__description">'. wp_kses_post( $item['description'] ) . '</div>';
					}
				echo '</div>';
			}
		echo '</div>';
	}

	/**
	 * Product countdown
	 *
	 * @param string $output  The sale flash HTML.
	 * @param object $post    The post object.
	 * @param object $product The product object.
	 *
	 * @return string
	 */
	public function product_countdown() {
		global $product;

		if ( 'grouped' == $product->get_type() ) {
			return '';
		}

		$layout = Helper::get_option( 'product_countdown_layout' );

		$sale = array(
			'weeks'   => esc_html__( 'weeks', 'glozin' ),
			'week'    => esc_html__( 'week', 'glozin' ),
			'days'    => esc_html__( 'days', 'glozin' ),
			'day'     => esc_html__( 'day', 'glozin' ),
			'hours'   => esc_html__( 'hours', 'glozin' ),
			'hour'    => esc_html__( 'hour', 'glozin' ),
			'minutes' => esc_html__( 'mins', 'glozin' ),
			'minute'  => esc_html__( 'min', 'glozin' ),
			'seconds' => esc_html__( 'secs', 'glozin' ),
			'second'  => esc_html__( 'sec', 'glozin' ),
		);

		$text = '<span class="gz-countdown-icon"></span><span class="gz-countdown-text text-dark fw-semibold">' . esc_html__( 'Hurry Up! Sale ends in:', 'glozin' ) . '</span>';

		$classes = 'gz-countdown-single-product d-flex flex-column align-items-start gap-5 mb-20 px-20 py-20 rounded-5';

		if( $layout == 'v2' ) {
			$classes = 'gz-countdown-single-product d-inline-flex flex-column align-items-start justify-content-center gap-7 mb-20 px-20 pt-15 pb-20 rounded-5 layout-v2';
		}

		if ( 'variable' == $product->get_type() ) {
			$classes .= ' hidden';
		}

		if ( $product->is_on_sale() ) {
			echo \Glozin\WooCommerce\Helper::get_product_countdown( $sale, $text, $classes );
		}
	}

	/**
	 * Guarantee Safe Checkout
	 *
	 * @return void
	 */
	public function guarantee_safe_checkout() {
		if( ! Helper::get_option( 'product_guarantee_safe_checkout' ) ) {
			return;
		}

		$html = Helper::get_option( 'product_guarantee_safe_checkout_html' );
		if( empty( $html ) ) {
			return;
		}

		echo '<div class="guarantee-safe-checkout py-20 px-20 rounded-10 bg-light-grey text-center mb-36 lh-normal">' . wp_kses_post( $html ) . '</div>';
	}

	/**
	 * Data variation
	 *
	 * @return array
	 */
	public function data_product_variations( $data, $product, $variation ) {
		$availability = $variation->get_availability();
		$data['availability_status'] = $availability['availability'];
		$data['description'] = $variation->get_description();
		
		if ( $variation->is_on_sale() ) {
			$date_on_sale_to  = $variation->get_date_on_sale_to();
			$expire = '';
			if( ! empty( $date_on_sale_to ) ) {
				$now         = strtotime( current_time( 'Y-m-d H:i:s' ) );
				$expire_date = strtotime($date_on_sale_to);
				$expire      = ! empty( $expire_date ) ? $expire_date - $now : -1;
			}

			$expire = apply_filters( 'glozin_countdown_product_second', $expire );
			if( ! empty( $expire ) ) {
				$data['countdown_expire'] = $expire;
			}
			$data['is_on_sale'] = true;
		}

		return $data;
	}

	/**
	 * Product Highlights
	 *
	 * @return void
	 */
	public function product_highlights() {
		if( ! Helper::get_option( 'product_highlights' ) ) {
			return;
		}

		$lists = apply_filters( 'glozin_product_highlights_list', (array) Helper::get_option( 'product_highlights_list' ) );
		$image = Helper::get_option( 'product_highlights_image' );
		if( empty( $lists ) || empty( $lists[0] ) ) {
			return;
		}

		$speed = Helper::get_option( 'product_highlights_speed' );
		$image_dimension = Helper::get_option( 'product_highlights_image_dimension' );
		echo '<div class="product-highlights glozin-marquee hover-stop border-top border-bottom" data-speed="' . esc_attr( $speed ) . '">';
			echo '<div class="glozin-marquee__inner">';
				echo '<div class="glozin-marquee__items d-inline-flex">';
					foreach( $lists as $item ) {
						if( ! empty( $item['text'] ) ) {
							echo '<div class="product-highlights__item glozin-marquee__item d-inline-flex align-items-center">';
							if( ! empty( $image ) ) {
								echo '<div class="product-highlights__image glozin-marquee__image">';
									echo '<img src="' . esc_url( $image ) . '" alt="' . esc_html( 'Glozin' ) . '" width="' . esc_attr( ! empty( $image_dimension['width'] ) ? $image_dimension['width'] . 'px' : 'auto' ) . '" height="' . esc_attr( ! empty( $image_dimension['height'] ) ? $image_dimension['height'] . 'px' : 'auto' ) . '">';
								echo '</div>';
							}
								echo '<div class="product-highlights__text glozin-marquee__text fs-20 fw-medium text-dark">' . wp_kses_post( $item['text'] ) . '</div>';
							echo '</div>';
						}
					}
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
	/**
	 * Unset review tab
	 *
	 * @return array
	 */
	public function unset_review_tab( $tabs ) {
		if( isset( $tabs[ 'reviews' ] ) ) {
			self::$unset_tabs['reviews'] = $tabs[ 'reviews' ];
			unset( $tabs[ 'reviews' ] );
		}

		return $tabs;
	}

	/**
	 * Review
	 *
	 * @return void
	 */
	public function show_reviews() {
		if ( empty( self::$unset_tabs ) || empty( self::$unset_tabs['reviews'] ) ) {
			return;
		}

		echo '<div id="tab-reviews" class="woocommerce-tabs woocommerce-tabs--reviews">';
			call_user_func( self::$unset_tabs['reviews']['callback'], 'reviews', self::$unset_tabs['reviews'] );
		echo '</div>';
	}

	/**
	 * Show product tabs type dropdowm, list
	 *
	 * @return void
	 */
	public function product_tabs() {
		$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );

		if( empty( $product_tabs ) ) {
			return;
		}

		$type = 'dropdown';
		$arrKey = array_keys($product_tabs);
		$lastKey = end($arrKey);
		$i = 0;
		
		echo '<div class="' . ( Helper::get_option( 'product_tabs_position' ) == 'under-summary' ? 'gz-product-tabs mb-30' : 'woocommerce-tabs' ) . '">';
			foreach( $product_tabs as $key => $product_tab ) :
				$firstKey = ( $i == 0 ) ? $key : '';
				$tab_class = $title_class = '';

				if ( $key == $firstKey && Helper::get_option( 'product_tabs_status' ) == 'first' ) {
					$tab_class = 'wc-tabs-first--opened';
					$title_class = 'active';
				}

				$tab_class .= ( $key == $lastKey ) ? ' last' : '';
			?>
				<div id="tab-<?php echo esc_attr( $key ); ?>" class="glozin-woocommerce-tabs woocommerce-tabs--<?php echo esc_attr( $type ); ?> woocommerce-tabs--<?php echo esc_attr( $key ); ?> <?php echo esc_attr($tab_class) ?>">
					<div class="woocommerce-tabs-title <?php echo esc_attr($title_class); ?>"><?php echo esc_html( $product_tab['title'] ); ?><span class="woocommerce-tabs-title__icon"></span></div>
					<div class="woocommerce-tabs-content">
						<?php
						if ( isset( $product_tab['callback'] ) ) {
							call_user_func( $product_tab['callback'], $key, $product_tab );
						}
						?>
					</div>
				</div>
			<?php
			$i++;
			endforeach;
		echo '</div>';
	}

	/**
	 * Product extra link
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_extra_link() {
		echo '<div class="glozin-product-extra-link d-flex flex-wrap align-items-center column-gap-30 row-gap-15 mb-20 pb-25 border-bottom">';
			do_action( 'glozin_product_extra_link' );
		echo '</div>';
	}

	/**
	 * Get product taxonomy
	 *
	 * @static
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function product_taxonomy( $taxonomy = 'product_cat' ) {
		global $product;

		$taxonomy = Helper::get_option( 'product_taxonomy' );
		if( empty($taxonomy ) ) {
			return;
		}

		$terms = \Glozin\WooCommerce\Helper::get_product_taxonomy( $taxonomy );

		if ( ! empty( $terms )  ) {
			echo sprintf(
				'<div class="glozin-product-taxonomy"><a class="text-dark" href="%s">%s</a></div>',
				esc_url( get_term_link( $terms[0] ), $taxonomy ),
				esc_html( $terms[0]->name ) );
		}
	}

	public function open_product_price() {
		echo '<div class="glozin-product-price">';
	}

	public function close_product_price() {
		echo '</div>';
	}

	/**
	 * Show stock
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function stock() {
		global $product;
		if( $product->is_type( 'grouped' ) ) {
			return;
		}

		echo '<div class="glozin-product-availability">' . wc_get_stock_html( $product ) .'</div>';
	}

	/**
	 * Show brand
	 *
	 * @return void
	 */
	public function show_brand() {
		global $product;

		if ( is_singular( 'product' ) ) {
			$terms       = get_the_terms( $product->get_id(), 'product_brand' );
			$brand_count = is_array( $terms ) ? count( $terms ) : 0;

			$taxonomy = get_taxonomy( 'product_brand' );
			$labels   = $taxonomy->labels;

			/* translators: %s - Label name */
			echo wc_get_brands( $product->get_id(), ', ', ' <span class="posted_in"><span class="meta__label">' . sprintf( _n( '%s: ', '%s: ', $brand_count, 'glozin' ), $labels->singular_name, $labels->name ).'</span>', '</span>' );
		}
	}

	public function review_before_open() {
		echo '<div class="glozin-review-avatar-name d-flex flex-wrap align-items-center gap-10 mb-20">';
	}

	public function review_before_close() {
		echo '</div>';
	}

	/**
	 * Single add to cart text
	 *
	 * @param string $text
	 * @return string
	 */
	public function single_add_to_cart_text( $text ) {
		global $product;
		if( $product->is_type( 'simple' ) & ( ! $product->is_purchasable() || ! $product->is_in_stock() ) ) {
			return esc_html__( 'Out of stock', 'glozin' );
		}

		return $text;
	}

	public function woocommerce_gallery_image_html_attachment_image_params( $image_params, $attachment_id, $image_size, $main_image ) {
		global $product;
		if( $product->get_image_id() == $attachment_id ) {
			$image_params['loading'] = 'eager';
			$image_params['fetchpriority'] = 'high';
		}

		return $image_params;
	}
}