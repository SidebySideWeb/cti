<?php

namespace Glozin\Addons\Modules\Variation_Images;

use Acowebs\WCPA\Free\Product;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main class of plugin for admin
 */
class Frontend {

	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private static $instance;

	/**
	 * Has variation images
	 *
	 * @var $has_variation_images
	 */
	protected static $has_variation_images = null;


	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'woocommerce_before_single_product', array( $this, 'add_post_class' ) );
		add_action( 'glozin_before_single_product', array( $this, 'add_post_class' ) );
		add_action( 'woocommerce_before_single_product_summary', array( $this, 'remove_post_class' ) );
		add_action( 'glozin_before_woocommerce_product_content', array( $this, 'remove_post_class' ) );

		add_action( 'wc_ajax_glozin_get_variation_images', array( $this, 'get_variation_images' ) );
	}

	public function has_variation_images() {
		if( isset( self::$has_variation_images ) ) {
			return self::$has_variation_images;
		}

		global $product;
		self::$has_variation_images = false;
		if( empty( $product ) ) {
			return self::$has_variation_images;
		}
		if( $product->get_type() != 'variable' ) {
			return self::$has_variation_images;
		}
		$variation_ids        = $product->get_children();
		if( empty($variation_ids) ) {
			return self::$has_variation_images;
		}
		foreach( $variation_ids as $variation_id ) {
			$variation_images = get_post_meta( $variation_id, 'glozin_variation_images', true );
			if( $variation_images ) {
				self::$has_variation_images = true;
				return self::$has_variation_images;
			}
		}

	}

	public function add_post_class() {
		add_filter( 'post_class', array( $this, 'product_class' ), 10, 3 );
	}

	public function remove_post_class() {
		remove_filter( 'post_class', array( $this, 'product_class' ), 10, 3 );
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
		if ( is_admin() || get_post_type(get_the_ID()) != 'product') {
			return $classes;
		}
		if( $this->has_variation_images() ) {
			$classes[] = 'product-has-variation-images';
		}

		return $classes;
	}

	/**
	 * Enqueue Scripts
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script( 'glozin_variation_images', GLOZIN_ADDONS_URL . '/modules/variation-images/assets/variation-images-frontend' . $debug . '.js', array( 'jquery' ), '20220319', array('strategy' => 'defer') );

		wp_localize_script( 'glozin_variation_images', 'glozinVariationImages', array(
			'variation_id_default' => $this->get_variation_id_default(),
		));
	}

	public function get_variation_images() {
		check_ajax_referer( '_glozin_nonce', 'nonce' );

		$product_id = '';
		if ( isset( $_POST['variation_id'] ) && ! empty( $_POST['variation_id'] ) ) {
			$product_id = $_POST['variation_id'];
		} elseif( isset( $_POST['product_id'] ) && ! empty( $_POST['product_id'] ) ) {
			$product_id = $_POST['product_id'];
		}
		if ( $product_id ) {
			$GLOBALS['post'] = get_post( $product_id  ); // WPCS: override ok.
			setup_postdata( $GLOBALS['post'] );
			ob_start();
			remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
			add_action( 'woocommerce_product_thumbnails', array( $this, 'show_product_images' ), 20 );
			add_action( 'woocommerce_product_thumbnails', array( $this, 'show_product_thumbnails' ), 30 );
			woocommerce_show_product_images();
			wp_reset_postdata();
			wp_send_json_success( ob_get_clean() );
			die();
		}

	}

	public function show_product_images( ) {
		$thumbnail_ids = $this->get_attachment_image_ids();
		$image_id = $thumbnail_ids['image_id'];
		$attachment_ids = $thumbnail_ids['attachment_ids'];
		if ( $attachment_ids && $image_id ) {
			foreach ( $attachment_ids as $attachment_id ) {
				if( empty( $attachment_id ) ) {
					continue;
				}
				echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', wc_get_gallery_image_html( $attachment_id ), $attachment_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
			}
		}
	}

	public function show_product_thumbnails( ) {
		$thumbnail_ids = $this->get_attachment_image_ids();
		$this->get_product_gallery_thumbnails( $thumbnail_ids['image_id'], $thumbnail_ids['attachment_ids'] );
	}

	public function get_attachment_image_ids() {
		$image_id = $attachment_ids = '';
		if ( isset( $_POST['variation_id'] ) && ! empty( $_POST['variation_id'] ) ) {
			$variation_id       = absint( $_POST['variation_id'] );
			$variation_images = get_post_meta( $variation_id, 'glozin_variation_images', true );
			$attachment_ids = $variation_images ? explode(',', $variation_images) : '';
			$variation = wc_get_product( $variation_id );
			$image_id = $variation ? $variation->get_image_id() : '';
		}

		if( empty($attachment_ids) && isset( $_POST['product_id'] ) && ! empty( $_POST['product_id'] ) ) {
			$product_id       = absint( $_POST['product_id'] );
			$product = wc_get_product( $product_id );
			$attachment_ids = $product ? $product->get_gallery_image_ids() : '';
			$image_id = $product && empty($image_id ) ? $product->get_image_id() : $image_id;
		}

		return array(
			'image_id' => $image_id,
			'attachment_ids' => $attachment_ids
		);
	}

	/**
	 * Product gallery thumbnails
	 *
	 * @return void
	 */
	public function get_product_gallery_thumbnails($image_id, $attachment_ids) {
		if ( $attachment_ids && $image_id ) {
			add_filter( 'woocommerce_single_product_flexslider_enabled', '__return_false' );
			
			echo '<div class="glozin-product-gallery-thumbnails">';
				echo apply_filters( 'glozin_product_get_gallery_image', wc_get_gallery_image_html( $image_id ), 1 );
				$index = 2;
				foreach ( $attachment_ids as $attachment_id ) {
					if( empty( $attachment_id ) ) {
						continue;
					}
					echo apply_filters( 'glozin_product_get_gallery_thumbnail', wc_get_gallery_image_html( $attachment_id ), $index );
					$index++;
				}

			echo '</div>';

			remove_filter( 'woocommerce_single_product_flexslider_enabled', '__return_false' );
		}
	}

	/**
	 * Get variation id default
	 *
	 * @return int
	 */
	public function get_variation_id_default() {
		$variation_id = 0;
		$product = wc_get_product( get_the_ID() );

		if( empty( $product ) ) {
			return $variation_id;
		}

		if( $product->get_type() == 'variable' ) {
			$default_variation = $product->get_default_attributes();
			$available_variations = $product->get_available_variations();

			foreach ($available_variations as $variation) {
				foreach ($default_variation as $key => $value) {
					if( isset( $variation['attributes']['attribute_'.$key] ) && $variation['attributes']['attribute_'.$key] == $value ) {
						$variation_id = $variation['variation_id'];
					}
				}
			}
		}

		return $variation_id;
	}
}