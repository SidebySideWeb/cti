<?php
/**
 * Single Product hooks.
 *
 * @package Glozin
 */

namespace Glozin\Addons\Modules\Multi_Color_Swatches;

use WCBoost\VariationSwatches\Helper;

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
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		add_filter( 'wcboost_variation_swatches_color_html', [ $this, 'add_multi_color_swatches' ], 10, 4 );
		add_filter( 'glozin_product_attribute_color_css_class', [ $this, 'product_attribute_color_css_class' ], 10, 3 );
		add_filter( 'glozin_product_attribute_color_variation_attrs', [ $this, 'product_attribute_color_variation_attrs' ], 10, 4 );
	}

	/**
	 * Enqueue scripts
	 */
	public function enqueue_scripts() {
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_style( 'glozin-multi-color-swatches-frontend', GLOZIN_ADDONS_URL . 'modules/multi-color-swatches/assets/multi-color-swatches' . $debug . '.css', [], '20250325' );
	}

	/**
	 * Add multi color swatches
	 *
	 * @param string $html
	 * @param array $args
	 * @param array $data
	 * @param object $term
	 * @return string
	 */
	public function add_multi_color_swatches( $html, $args, $data, $term ) {
		$is_dual_color = get_term_meta( $term->term_id, 'wcboost_variation_swatches_is_dual_color', true );
		$secondary_color = get_term_meta( $term->term_id, 'wcboost_variation_swatches_second_color', true );

		if( $is_dual_color !== 'yes' ) {
			return $html;
		}

		if ( empty( $secondary_color ) ) {
			return $html;
		}

		$value = is_object( $term ) ? $term->slug : $term;
		$name  = is_object( $term ) ? $term->name : $term;
		$size  = ! empty( $args['swatches_size'] ) ? sprintf( '--wcboost-swatches-item-width: %1$dpx; --wcboost-swatches-item-height: %2$dpx;', absint( $args['swatches_size']['width'] ), absint( $args['swatches_size']['height'] ) ) : '';

		$swatches_meta = Helper::get_swatches_meta( get_the_ID() );

		if ( $args['swatches_type'] === 'color' ) {
			if( ! empty( $args['swatches_attributes'][$term->term_id][ 'second_color' ] ) ) {
				$secondary_color = $args['swatches_attributes'][$term->term_id][ 'second_color' ];
			}
		}

		$color = '--wcboost-swatches-item-color:' . $data['value'] . ';--wcboost-swatches-item-color-secondary:' . $secondary_color;

		if ( is_object( $term ) ) {
			$selected = sanitize_title( $args['selected'] ) == $value;
		} else {
			// This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
			$selected = sanitize_title( $args['selected'] ) === $args['selected'] ? $args['selected'] == sanitize_title( $value ) : $args['selected'] == $value;
		}

		$class = [
			'wcboost-variation-swatches__item',
			'wcboost-variation-swatches__item-' . $value,
			'wcboost-variation-swatches__item--multi-color',
		];

		if ( $selected ) {
			$class[] = 'selected';
		}

		if ( ! empty( $args['swatches_class'] ) ) {
			$class[] = $args['swatches_class'];
		}

		return sprintf(
			'<li class="%s" style="%s" aria-label="%s" data-value="%s" tabindex="0" role="button" aria-pressed="false">
				<span class="wcboost-variation-swatches__name">
					%s
					<span class="wcboost-variation-swatches__secondary-color"></span>
				</span>
			</li>',
			esc_attr( implode( ' ', $class ) ),
			esc_attr( $size . $color ),
			esc_attr( $name ),
			esc_attr( $value ),
			esc_html( $name )
		);
	}

	/**
	 * Add multi color class to product attribute color
	 *
	 * @param string $css_class
	 * @param string $type
	 * @param int $term_id
	 * @return string
	 */
	public function product_attribute_color_css_class( $css_class, $type, $term_id ) {
		$is_dual_color = get_term_meta( $term_id, 'wcboost_variation_swatches_is_dual_color', true );
		$secondary_color = get_term_meta( $term_id, 'wcboost_variation_swatches_second_color', true );

		if( $type !== 'color' ) {
			return $css_class;
		}

		if( $is_dual_color !== 'yes' ) {
			return $css_class;
		}

		if ( empty( $secondary_color ) ) {
			return $css_class;
		}

		$css_class .= ' product-variation-item__multi-color';

		return $css_class;
	}

	/**
	 * Add secondary color to variation attrs
	 *
	 * @param string $variation_attrs
	 * @param string $type
	 * @param int $term_id
	 * @return string
	 */
	public function product_attribute_color_variation_attrs( $variation_attrs, $type, $term_id, $swatches_args ) {
		$is_dual_color = get_term_meta( $term_id, 'wcboost_variation_swatches_is_dual_color', true );
		$secondary_color = get_term_meta( $term_id, 'wcboost_variation_swatches_second_color', true );

		if( $type !== 'color' ) {
			return $variation_attrs;
		}

		if( $is_dual_color !== 'yes' ) {
			return $variation_attrs;
		}

		if ( empty( $secondary_color ) ) {
			return $variation_attrs;
		}

		if( ! empty( $swatches_args['attributes'] ) ) {
			$secondary_color = ! empty( $swatches_args['attributes'][$term_id]['second_color'] ) ? $swatches_args['attributes'][$term_id]['second_color'] : '';
		}

		$variation_attrs .= ' style="--wcboost-swatches-item-color-secondary:' . esc_attr( $secondary_color ) . ';"';

		return $variation_attrs;
	}
}
