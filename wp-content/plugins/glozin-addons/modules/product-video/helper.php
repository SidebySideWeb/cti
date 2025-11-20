<?php
/**
 * Glozin Addons Helper init
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Glozin
 */

namespace Glozin\Addons\Modules\Product_Video;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Helper
 */
class Helper {

	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private static $instance;


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
	 * Get product video
	 *
	 * @static
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_product_video_html( $video_autoplay, $video_controls = true, $has_thumb = true, $class_thumb = '', $thumbnail_size = 'woocommerce_single', $product = null ) {
		if( empty( $product ) ) {
			$product = wc_get_product( get_the_ID() );
		}
		$video_url    	= get_post_meta( $product->get_id(), 'video_url', true );
		$video_image_id = get_post_meta( $product->get_id(), 'video_thumbnail_id', true );

		$video_width  	= 1200;
		$video_height 	= 500;
		$video_autoplay = $video_autoplay ? 'autoplay' : '';
		$video_html   	= $video_class = '';

		if ( strpos( $video_url, 'youtube' ) !== false ) {
			$video_class = 'video-youtube';
		} elseif ( strpos( $video_url, 'vimeo' ) !== false ) {
			$video_class = 'video-vimeo';
		}

		if( $video_autoplay ) {
			$video_class .= ' video-autoplay';
		}

		// If URL: show oEmbed HTML
		if ( filter_var( $video_url, FILTER_VALIDATE_URL ) ) {

			$atts = array(
				'width'    => $video_width,
				'height'   => $video_height,
			);

			if ( strpos( $video_url, 'youtube' ) !== false ) {
				$video_url = add_query_arg('autoplay', '0', $video_url);
			}

			if ( $oembed = @wp_oembed_get( $video_url, $atts ) ) {
				$video_html = $oembed;
			} else {
				$atts = array(
					'src'    => $video_url,
					'width'  => $video_width,
					'height' => $video_height
				);

				$controls = $video_controls ? 'controls' : '';

				$poster = ! empty( $video_image_id ) ? 'poster="' . esc_url( wp_get_attachment_image_url( $video_image_id, $thumbnail_size ) ) . '"' : '';

				$video_html = '<video '. urldecode( http_build_query( $atts, '', ' ' ) ) .' loop="true" muted="muted" '. esc_attr( $controls ) . ' playsinline preload="metadata" '. $video_autoplay . ' '. $poster .'></video>';
			}
			
		}


		if ( empty( $video_image_id ) ) {
			$video_thumb = wc_placeholder_img( $thumbnail_size );
			$video_thumb_src = wc_placeholder_img_src( $thumbnail_size );

			$video_thumb = '<a href="' . esc_url( $video_thumb_src ) . '">' . $video_thumb . '</a>';
		} else {
			$video_thumb = wp_get_attachment_image( $video_image_id, $thumbnail_size );
		}

		if ( $video_html ) {
			$btn_play = '<span class="glozin-i-video" role="button"></span>';
			$video_thumb = $has_thumb ? '<div class="glozin-video-thumbnail ' . esc_attr( $class_thumb ) . '">'. $btn_play . $video_thumb .'</div>' : '';

			$video_html = '<div class="glozin-video-wrapper ' . esc_attr( $video_class ) . '">' . $video_html . '</div>';
			$video_html = $video_thumb . $video_html;
		}

		return $video_html;
	}

	/**
	 * Get product video
	 *
	 * @static
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_product_video($video_controls = true) {
		$product = wc_get_product( get_the_ID() );
		$video_autoplay = get_post_meta( $product->get_id(), 'video_autoplay', true );
		$video_html  = self::get_product_video_html( $video_autoplay, $video_controls );
		if ( $video_html ) {
			$video_image  = get_post_meta( $product->get_id(), 'video_thumbnail_id', true );

			if ( empty( $video_image ) ) {
				$video_thumb = wc_placeholder_img_src( 'shop_thumbnail' );
			} else {
				$video_thumb = wp_get_attachment_image_src( $video_image, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
				$video_thumb = ! empty( $video_thumb ) ? $video_thumb[0] : wc_placeholder_img_src( 'shop_thumbnail' );
			}
			$video_html = '<div data-thumb="' . esc_url( $video_thumb ) . '" class="woocommerce-product-gallery__image glozin-product-video">' . $video_html . '</div>';
		}

		return $video_html;
	}
}
