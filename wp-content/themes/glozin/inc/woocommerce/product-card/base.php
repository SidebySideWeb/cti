<?php
/**
 * Product Card hooks.
 *
 * @package Glozin
 */

namespace Glozin\WooCommerce\Product_Card;

use Glozin\Helper, Glozin\Icon;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Product Card
 */
class Base {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * Product card layout
	 *
	 * @var $product_card_layout
	 */
	protected static $product_card_layout = null;

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
		if( is_admin() ) {
			// add actions for elementor
			add_action('admin_init', array($this, 'actions'), 10);
		} else {
			// add actions for frontend
			add_action('wp', array($this, 'actions'), 10 );
		}

	}

	public function actions(){
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ), 20 );

		add_filter( 'woocommerce_product_loop_start', array( $this, 'loop_start' ), 20 );
		add_filter( 'woocommerce_product_loop_start', 'woocommerce_maybe_show_product_subcategories', 20 );

		// Product inner wrapper
		add_action( 'woocommerce_before_shop_loop_item', array( $this, 'product_inner_open' ), 1 );
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'product_inner_close' ), 1000 );

		// Remove wrapper link
		remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

		// Change product thumbnail.
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail' );
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'product_loop_thumbnail' ), 1 );

		// Product summary
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'product_summary_open' ), 1 );
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'product_summary_close' ), 1000 );

		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'product_taxonomy' ), 10 );

		// Change the product title.
		remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title' );
		add_action( 'woocommerce_shop_loop_item_title', array( $this, 'product_card_title' ) );

		// Rating
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
		if( Helper::get_option('product_card_rating') ) {
			add_action( 'woocommerce_shop_loop_item_title', array( $this, 'product_rating' ), 10 );
		}

		// Change add to cart text
		add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'product_add_to_cart_text' ), 20, 2 );

		// Add units of measure to price
		add_filter('woocommerce_get_price_html', array( $this, 'product_unit_measure' ), 20, 2 );

		// Add to cart button
		add_filter('woocommerce_loop_add_to_cart_link', array( $this, 'product_add_to_cart_link' ), 20, 3 );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
	}

	/**
	 * WooCommerce specific scripts & stylesheets.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function scripts() {
		if( Helper::get_option( 'sale_display_type') == 'countdown' ) {
			if(wp_script_is('glozin-countdown', 'registered')) {
				wp_enqueue_script( 'glozin-countdown' );
			}
		}
	}

	/**
	 * Get product card layout
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_default_layout() {
		if( isset( self::$product_card_layout )  ) {
			return self::$product_card_layout;
		}

		self::$product_card_layout = \Glozin\Helper::get_option( 'product_card_layout' );

		return apply_filters( 'glozin_product_card_layout_default', self::$product_card_layout);
	}

	/**
	 * Get product card layout
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_layout() {
		$layout = apply_filters( 'glozin_product_card_layout', self::get_default_layout() );

		return $layout;
	}

	/**
	 * Loop start.
	 *
	 * @since 1.0.0
	 *
	 * @param string $html Open loop wrapper with the <ul class="products"> tag.
	 *
	 * @return string
	 */
	public function loop_start( $html ) {
		global $wp_query;
		$html            = '';
		$classes = array(
			'products',
			'd-flex',
			'flex-wrap',
		);

		$product_card_layout = self::get_layout();

		$classes[] = $product_card_layout ? 'product-card-layout-' . $product_card_layout : '';

		if( \Glozin\WooCommerce\Catalog\View::get_default_view() == 'list' ) {
			$classes[] = 'product-card-layout-list';
		}

		if ( $mobile_pl_col = apply_filters( 'glozin_mobile_product_columns', intval( Helper::get_option( 'mobile_product_columns' ) ) ) ) {
			$classes[] = 'mobile-col-' . $mobile_pl_col;
		}

		if ( intval( Helper::get_option( 'mobile_product_card_atc' ) ) ) {
			$classes[] = 'product-card-mobile-show-atc';
		}

		if ( intval( Helper::get_option( 'mobile_product_card_featured_icons' ) ) ) {
			$classes[] = 'mobile-show-featured-icons';
		}

		if ( ! intval( Helper::get_option( 'mobile_product_card_wishlist' ) ) ) {
			$classes[] = 'mobile-wishlist-button--hidden';
		}

		if ( ! intval( Helper::get_option( 'mobile_product_card_compare' ) ) ) {
			$classes[] = 'mobile-compare-button--hidden';
		}

		if ( ! intval( Helper::get_option( 'mobile_product_card_quick_view' ) ) ) {
			$classes[] = 'mobile-quick-view-button--hidden';
		}

		if ( $product_card_layout == '1' && Helper::get_option( 'product_card_wishlist_display' ) == 'always' ) {
			$classes[] = 'product-card-wishlist-always-show';
		}

		$classes[] = 'columns-' . apply_filters( 'glozin_product_card_loop_columns', wc_get_loop_prop( 'columns' ) );
		$classes = apply_filters( 'glozin_product_card_loop_classes', $classes );
		$style = apply_filters( 'glozin_product_card_loop_style', '' );
		if( ! empty( $style ) ) {
			$style = 'style="' . esc_attr( $style ) . '"';
		}

		$html = '<ul class="' . esc_attr( implode( ' ', $classes ) ) . '" data-layout="' . esc_attr( self::get_layout() ) . '" ' . $style . '>';

		return $html;
	}

	/**
	 * Open product inner.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_inner_open() {
		echo '<div class="product-inner">';
	}

	/**
	 * Close product ineer.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_inner_close() {
		echo '</div>';
	}

	/**
	 * Open product summary.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_summary_open() {
		$class = '';
		if( self::get_layout() !== 'list' ) {
			if(  Helper::get_option( 'product_card_summary' ) == 'center' ) {
				$class .= ' d-flex flex-column align-items-center text-center';
			} elseif(Helper::get_option( 'product_card_summary' ) == 'flex-end') {
				$class .= ' d-flex flex-column align-items-end text-right';
			}

		}
		echo '<div class="product-summary mt-15'. esc_attr( $class ) .'">';
	}

	/**
	 * Close product ineer.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_summary_close() {
		echo '</div>';
	}

	/**
	 * Product thumbnail.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_loop_thumbnail() {
		global $product;

		switch ( Helper::get_option( 'product_card_hover' ) ) {
			case 'fadein':
				$image_ids = $product->get_gallery_image_ids();
				echo '<div class="product-thumbnail position-relative rounded-product-image overflow-hidden">';
					$this->loop_product_link_open('thumbnail-fadein');
						woocommerce_template_loop_product_thumbnail();

						if ( ! empty( $image_ids ) ) {
							$image_size = apply_filters( 'single_product_archive_thumbnail_size', 'woocommerce_thumbnail' );
							echo wp_get_attachment_image( $image_ids[0], $image_size, false, array( 'class' => 'attachment-woocommerce_thumbnail size-woocommerce_thumbnail product-thumbnails--fadein-image' ) );
						}
						\Glozin\WooCommerce\Helper::lazy_load_image_effect_html();
						$this->loop_product_link_close();
					do_action( 'glozin_product_loop_thumbnail' );
				echo '</div>';
				break;
			default:
				echo '<div class="product-thumbnail position-relative rounded-product-image overflow-hidden">';
					$this->loop_product_link_open('thumbnail-static');
						woocommerce_template_loop_product_thumbnail();
						\Glozin\WooCommerce\Helper::lazy_load_image_effect_html();
					$this->loop_product_link_close();
					do_action( 'glozin_product_loop_thumbnail' );
				echo '</div>';
				break;
		}
	}


	/**
	 * Featured icons open
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_featured_icons_open() {
		echo '<div class="product-featured-icons product-featured-icons--primary position-absolute bottom-10 end-10 top-15-xl bottom-auto-xl end-15-xl d-flex gap-5 align-items-center justify-content-center flex-column product-card-animation z-2">';
	}

	/**
	 * Featured icons second open
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_featured_icons_second_open() {
		echo '<div class="product-featured-icons product-featured-icons--second position-absolute bottom-15 start-15 end-15 gap-5 align-items-center justify-content-center product-card-animation product-card-animation--bottom z-2 '.apply_filters( 'glozin_product_featured_icons_second_classes' , 'd-none d-flex-xl' ).'">';
	}

	/**
	 * Featured icons close
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_featured_icons_close() {
		echo '</div>';
	}

	/**
	 * Change add to cart text
	 *
	 * @return void
	 */
	public function product_add_to_cart_text($button_text, $product) {
		if( $product->get_stock_status() === 'outofstock' ) {
			$button_text = esc_html__( 'Out of Stock', 'glozin' );
		}
		if( $product->get_stock_status() === 'outofstock'  && class_exists('CWG_Instock_Notifier') ) {
			$button_text = esc_html__( 'Notify Me', 'glozin' );
		}

		return $button_text;
	}

	public function product_add_to_cart_link( $html, $product, $args ) {
		$button_classes = isset( $args['button_classes'] ) ? $args['button_classes'] : 'gz-button-light';

		if( isset( $args['button_classes'] ) ) {
			$button_classes = $args['button_classes'];
		} else {
			$button_classes = 'gz-button-light gz-button-hover-effect';
			$button_classes = apply_filters( 'glozin_add_to_cart_button_classes', $button_classes );
		}

		if( $product->get_type() == 'variable' && Helper::get_option( 'product_card_quickadd' ) ) {
			$args['attributes']['data-toggle'] = 'modal';
			$args['attributes']['data-target'] = 'quick-view-modal';
			$button_classes .= ' glozin-quickview-button';
			\Glozin\Theme::set_prop( 'modals', 'quickview' );
		}

		if( $product->get_stock_status() === 'outofstock' && ! class_exists('CWG_Instock_Notifier') ) {
			$button_classes .= ' disabled';
		}

		if( $product->get_stock_status() === 'outofstock' && class_exists('CWG_Instock_Notifier') ) {
			$args['attributes']['data-toggle'] = 'modal';
			$args['attributes']['data-target'] = 'quick-view-modal';
			$button_classes .= ' glozin-quickview-button';
			\Glozin\Theme::set_prop( 'modals', 'quickview' );
		}

		$classes[] = 'glozin-button product-loop-button-atc align-items-center justify-content-center ' . $button_classes;

        $args['class'] .= ' ' . esc_attr( implode( ' ', $classes ) );

		$args['class'] = apply_filters( 'glozin_add_to_cart_button_class', $args['class'] );

		$cart_icons = \Glozin\Helper::get_cart_icons();

        return sprintf(
            '<a href="%s" data-quantity="%s" class="%s" %s data-tooltip="%s">%s <span class="add-to-cart__text">%s</span></a>',
            esc_url( $product->add_to_cart_url() ),
            esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
            esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
            isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
			esc_html( $product->add_to_cart_text() ),
            $cart_icons,
            esc_html( $product->add_to_cart_text() )
        );
    }

	public static function add_to_cart_button_base() {
		woocommerce_template_loop_add_to_cart(
			array(
				'button_classes' => 'gz-button-add-to-cart-mobile gz-button gz-button-no-icon mt-15',
			)
		);
	}

	/**
	 * Rating count open.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function product_rating() {
		global $product;

		if(Helper::get_option('product_card_empty_rating') && ! $product->get_rating_count() ) {
			return;
		}

		echo '<div class="glozin-rating d-flex align-items-center mt-10">';
			if ( $product->get_rating_count() ) {
				woocommerce_template_loop_rating();
			} else {
			?>
				<div class="star-rating" role="img">
					<span class="max-rating rating-stars">
						<?php echo Icon::inline_svg( 'icon=star' ); ?>
						<?php echo Icon::inline_svg( 'icon=star' ); ?>
						<?php echo Icon::inline_svg( 'icon=star' ); ?>
						<?php echo Icon::inline_svg( 'icon=star' ); ?>
						<?php echo Icon::inline_svg( 'icon=star' ); ?>
					</span>
				</div>
			<?php
			}
		echo '</div>';
	}

	/**
	 * Get product card title
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_card_title() {
		$heading_tag = apply_filters( 'glozin_product_card_title_heading_tag', Helper::get_option( 'product_card_title_heading_tag' ) );
		echo '<'. esc_attr( $heading_tag ) .' class="woocommerce-loop-product__title my-0 fs-15">';
			$this->loop_product_link_open();
				the_title();
			$this->loop_product_link_close();
		echo '</'. esc_attr( $heading_tag ) .'>';
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
	public function product_taxonomy( $taxonomy = 'product_cat' ) {
		$taxonomy = Helper::get_option( 'product_card_taxonomy' );
		if( empty($taxonomy ) ) {
			return;
		}

		$terms = \Glozin\WooCommerce\Helper::get_product_taxonomy( $taxonomy );

		if ( ! empty( $terms )  ) {
			echo sprintf(
				'<div class="product--cat"><a href="%s">%s</a></div>',
				esc_url( get_term_link( $terms[0] ), $taxonomy ),
				esc_html( $terms[0]->name ) );
		}
	}

	/**
	 * Insert the opening anchor tag for products in the loop.
	 */
	public function loop_product_link_open( $class = '' ) {
		global $product;

		$link = apply_filters( 'woocommerce_loop_product_link', get_the_permalink(), $product );

		$link_class = 'woocommerce-LoopProduct-link woocommerce-loop-product__link';

		if( $class == 'thumbnail-fadein' ) {
			$link_class .= ' product-thumbnails--fadein gz-hover-zoom gz-hover-effect gz-ratio gz-ratio--product-image gz-lazy-load rounded-product-image';
		} elseif( $class == 'thumbnail-static' ) {
			$link_class .= ' gz-ratio gz-ratio--product-image gz-lazy-load rounded-product-image';
		}

		echo '<a href="' . esc_url( $link ) . '" class="' . esc_attr( $link_class ) . '" aria-label="' . esc_attr( get_the_title() )  . '">';
	}

	/**
	 * Insert the closing anchor tag for products in the loop.
	 */
	function loop_product_link_close() {
		echo '</a>';
	}

	/**
	 * Get product card title
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_unit_measure( $price, $product ) {
		if( $product->get_type() == 'variable' || $product->get_type() == 'grouped' ) {
			return $price;
		}

		$unit = maybe_unserialize( get_post_meta( $product->get_id(), 'unit_measure', true ) );

		if ( $unit ) {
			$unit = '<span class="gz-price-unit"><span class="divider">/</span> '. esc_html( $unit ) .'</span>';
			$price = $price . $unit;
		}

		return $price;
	}

}