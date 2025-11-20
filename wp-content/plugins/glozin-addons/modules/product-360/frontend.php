<?php
/**
 * Single Product hooks.
 *
 * @package Glozin
 */

namespace Glozin\Addons\Modules\Product_360;

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

		add_filter( 'glozin_product_gallery_summary_data', array( $this, 'product_360_data' ) );

		add_filter( 'glozin_product_get_gallery_image', array( $this, 'get_gallery_thumbnail' ), 10, 2 );
		add_filter( 'glozin_product_get_gallery_thumbnail', array( $this, 'get_gallery_thumbnail' ), 10, 2 );

		add_filter( 'woocommerce_single_product_image_thumbnail_html', array( $this, 'product_360_gallery' ), 10, 2 );
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
		wp_enqueue_script( 'product-360-js', GLOZIN_ADDONS_URL . 'modules/product-360/assets/plugins/js-cloudimage-360-view.min.js', array('jquery') );
		wp_enqueue_script( 'product-360-frontend', GLOZIN_ADDONS_URL . 'modules/product-360/assets/product-360' . $debug . '.js', array('jquery'), GLOZIN_ADDONS_VER, array('strategy' => 'defer') );
		wp_enqueue_style( 'glozin-product-360', GLOZIN_ADDONS_URL . 'modules/product-360/assets/product-360' . $debug . '.css', array(), GLOZIN_ADDONS_VER );
	}

	/**
	 * Get product product_360
	 *
	 * @static
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_gallery_thumbnail( $html, $index ) {
		$product_360_position = intval( get_post_meta( get_the_ID(), 'product_360_position', true ) );

		if ( $product_360_position == 0 ) {
			return $html;
		}

		if ( $product_360_position != $index ) {
			return $html;
		}

		$product_360_ids = get_post_meta( get_the_ID(), 'product_360_thumbnail_ids', true );

		if ( empty( $product_360_ids ) ) {
			return $html;
		}
		
		$attachments = ! empty( $product_360_ids ) ? explode(',', $product_360_ids) : '';

		return wc_get_gallery_image_html( $attachments[0] ) . $html;
	}

	/**
	 * Get product product_360
	 *
	 * @static
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_product_360() {
		$product_360_ids = get_post_meta( get_the_ID(), 'product_360_thumbnail_ids', true );

		if ( empty($product_360_ids ) ) {
			return;
		}

		$attachments = ! empty( $product_360_ids ) ? explode(',', $product_360_ids) : '';
		$list_thumbnails_url = [];

		foreach( $attachments as $attachment_id ) {
			$list_thumbnails_url[] = wp_get_attachment_url($attachment_id);
		}

		$product_360_thumb_src = wp_get_attachment_url( $attachments[0] );

		$product_360_html = sprintf( '<div data-thumb="%s" data-zoom_status="false" class="woocommerce-product-gallery__image glozin-product-360"><span class="glozin-i-360" role="button"></span>
										<div class="glozin-product-360__viewer cloudimage-360" data-fullscreen="true" data-image-list-x="%s" data-autoplay="true" data-play-once="true" data-speed="150" draggable="false">
											<div class="control_360">
												<button class="cloudimage-360-left">%s</button>
												<button class="cloudimage-360-right">%s</button>
											</div>
										</div>
										<a class="glozin-product-360__image">
											<img src="%s" />
										</a>
									</div>',
									esc_url( $product_360_thumb_src ),
									esc_attr( json_encode( $list_thumbnails_url ) ),
									\Glozin\Addons\Helper::inline_svg( [ 'icon' => 'icon-back' ] ),
									\Glozin\Addons\Helper::inline_svg( [ 'icon' => 'icon-next' ] ),
									esc_url( $product_360_thumb_src ),
								);

		return $product_360_html;
	}

	/**
	 * Get product product_360
	 *
	 * @static
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function product_360_gallery( $html, $attachment_id ) {
		global $product;
		$product_360_position = get_post_meta( $product->get_id(), 'product_360_position', true );

		if ( $product_360_position == 0 ) {
			return $html;
		}

		if ( $product_360_position == '1' ) {
			if ( $product->get_image_id() == $attachment_id ) {
				$html = self::get_product_360() . $html;
			}
		} else {
			$attachment_ids 	= $product->get_gallery_image_ids();

			$key = array_search ($attachment_id, $attachment_ids);

			if ( $key === false && $product_360_position == '2' ) {
				$html = $html . self::get_product_360();
			} elseif( $key && $product_360_position == $key + 2 ) {
				$html = self::get_product_360() . $html;
			}
		}

		return $html;
	}

	/**
	 * Get product product_360
	 *
	 * @static
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function product_360_data( $data ) {
		$product_360_ids = get_post_meta( get_the_ID(), 'product_360_thumbnail_ids', true );
		$product_360_position = get_post_meta( get_the_ID(), 'product_360_position', true );

		if ( empty( $product_360_ids ) ) {
			return;
		}

		$data = 'data-product_360='. esc_attr( $product_360_position ) .'';

		return $data;
	}
}
