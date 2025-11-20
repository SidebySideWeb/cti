<?php
/**
 * Single Product hooks.
 *
 * @package Glozin
 */

namespace Glozin\Addons\Modules\Product_3D_Viewer;

use Glozin\Helper;
use Glozin\Icon;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Single Product
 */
class Frontend {
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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'script_loader_tag', [ $this, 'add_type_attribute' ], 10, 3 );

		add_filter( 'glozin_product_gallery_summary_data', array( $this, 'product_3d_viewer_data' ) );

		add_filter( 'glozin_product_get_gallery_image', array( $this, 'get_gallery_thumbnail' ), 10, 2 );
		add_filter( 'glozin_product_get_gallery_thumbnail', array( $this, 'get_gallery_thumbnail' ), 10, 2 );

		add_filter( 'woocommerce_single_product_image_thumbnail_html', array( $this, 'product_3d_viewer_gallery' ), 10, 2 );
	}

	/**
	 * Enqueue scripts
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script( 'product-3d-viewer', GLOZIN_ADDONS_URL . 'modules/product-3d-viewer/assets/plugins/model-viewer.min.js',  array(), '3.5.0' );
		wp_enqueue_script( 'product-3d-viewer-frontend', GLOZIN_ADDONS_URL . 'modules/product-3d-viewer/assets/product-3d-viewer' . $debug . '.js',  array( 'jquery'), GLOZIN_ADDONS_VER, array('strategy' => 'defer') );
		wp_enqueue_style( 'glozin-product-3d-viewer', GLOZIN_ADDONS_URL . 'modules/product-3d-viewer/assets/product-3d-viewer' . $debug . '.css', array(), GLOZIN_ADDONS_VER );
	}

	/**
	 * Add type atributes script
	 *
	 * @return void
	 */
	public function add_type_attribute($tag, $handle, $src) {
		if ( 'product-3d-viewer' == $handle ) {
			$tag = '<script type="module" src="' . esc_url( $src ) . '"></script>';
		}

		return $tag;
	}

	/**
	 * Get product product_3d_viewer
	 *
	 * @static
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_gallery_thumbnail( $html, $index ) {
		global $product;

		$product_3d_viewer_position = intval( get_post_meta( $product->get_id(), 'product_3d_viewer_position', true ) );

		if ( $product_3d_viewer_position == 0 ) {
			return $html;
		}

		if ( $product_3d_viewer_position != $index ) {
			return $html;
		}

		$product_3d_viewer_url = get_post_meta( $product->get_id(), 'product_3d_viewer_url', true );

		if ( empty( $product_3d_viewer_url ) ) {
			return $html;
		}

		$product_3d_viewer_image_id  = get_post_meta( $product->get_id(), 'product_3d_viewer_thumbnail_id', true );

		if ( empty( $product_3d_viewer_image_id ) ) {
			$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
			$thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
			$product_3d_viewer_thumb = wc_placeholder_img( $thumbnail_size );
			$product_3d_viewer_thumb_src = wc_placeholder_img_src( $thumbnail_size );

			$product_3d_viewer_thumb = '<div data-thumb="' . esc_url( $product_3d_viewer_thumb_src ) . '" class="woocommerce-product-gallery__image"><a href="' . esc_url( $product_3d_viewer_thumb_src ) . '">' . $product_3d_viewer_thumb . '</a></div>';
		} else {
			$product_3d_viewer_thumb = wc_get_gallery_image_html($product_3d_viewer_image_id);
		}

		return $product_3d_viewer_thumb . $html;
	}

	/**
	 * Get product product_3d_viewer
	 *
	 * @static
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_product_3d_viewer() {
		global $product;

		$product_3d_viewer_url = get_post_meta( $product->get_id(), 'product_3d_viewer_url', true );

		if ( empty($product_3d_viewer_url ) ) {
			return;
		}

		$product_3d_viewer_image  = get_post_meta( $product->get_id(), 'product_3d_viewer_thumbnail_id', true );

		if ( empty( $product_3d_viewer_image ) ) {
			$product_3d_viewer_thumb = wc_placeholder_img_src( 'shop_thumbnail' );
		} else {
			$product_3d_viewer_thumb = wp_get_attachment_image_src( $product_3d_viewer_image, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
			$product_3d_viewer_thumb = ! empty( $product_3d_viewer_thumb ) ? $product_3d_viewer_thumb[0] : wc_placeholder_img_src( 'shop_thumbnail' );
		}

		$product_3d_viewer_html = '<div data-thumb="' . esc_url( $product_3d_viewer_thumb ) . '" data-zoom_status="false" class="woocommerce-product-gallery__image glozin-product-3d-viewer">
									<div class="glozin-product-3d-viewer__item disable">
										<model-viewer class="glozin-product-3d-viewer__model" src="'. esc_url( $product_3d_viewer_url ) .'"  alt="'. $product->get_title() .'" poster="' . esc_url( $product_3d_viewer_thumb ) . '" reveal="interaction" disable-tap camera-controls="true" data-js-focus-visible ar-status="not-presenting"></model-viewer>
										<div class="glozin-product-3d-viewer__button">'. \Glozin\Addons\Helper::get_svg( '3d-model' ) .'</div>
										<div class="glozin-product-3d-viewer__buttons">
											'. \Glozin\Addons\Helper::get_svg( 'plus', 'ui', 'class=plus' ) .'
											'. \Glozin\Addons\Helper::get_svg( 'minus', 'ui', 'class=minus' ) .'
											'. \Glozin\Addons\Helper::get_svg( 'fullscreen-2', 'ui', 'class=fullscreen' ) .'
											'. \Glozin\Addons\Helper::get_svg( 'exit-fullscreen', 'ui', 'class=exit-fullscreen' ) .'
										</div>
									</div>
								</div>';

		return $product_3d_viewer_html;
	}

	/**
	 * Get product product_3d_viewer
	 *
	 * @static
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function product_3d_viewer_gallery( $html, $attachment_id ) {
		global $product;
		$product_3d_viewer_position = get_post_meta( $product->get_id(), 'product_3d_viewer_position', true );

		if ( $product_3d_viewer_position == 0 ) {
			return $html;
		}

		if ( $product_3d_viewer_position == '1' ) {
			if ( $product->get_image_id() == $attachment_id ) {
				$html = self::get_product_3d_viewer() . $html;
			}
		} else {
			$attachment_ids 	= $product->get_gallery_image_ids();

			$key = array_search ($attachment_id, $attachment_ids);

			if ( $key === false && $product_3d_viewer_position == '2' ) {
				$html = $html . self::get_product_3d_viewer();
			} elseif( $key && $product_3d_viewer_position == $key + 2 ) {
				$html = self::get_product_3d_viewer() . $html;
			}
		}

		return $html;
	}

	/**
	 * Get product product_3d_viewer
	 *
	 * @static
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function product_3d_viewer_data( $data ) {
		global $product;

		$product_3d_viewer_url      = get_post_meta( $product->get_id(), 'product_3d_viewer_url', true );
		$product_3d_viewer_position = get_post_meta( $product->get_id(), 'product_3d_viewer_position', true );

		if ( ! $product_3d_viewer_url ) {
			return;
		}

		$data = 'data-product_3d_viewer='. esc_attr( $product_3d_viewer_position ) .'';

		return $data;
	}
}
