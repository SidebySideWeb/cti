<?php

namespace Glozin\Addons\Modules\Multi_Color_Swatches;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Product Settings
 */
class Product_Options {
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
        add_filter( 'wcboost_variation_swatches_field_html', [ $this, 'variation_swatches_field_html' ], 10, 2 );
	}

    public function variation_swatches_field_html( $html, $args ) {
        if ( $args['type'] == 'color' ) {
			if ( is_array( $args['value'] ) && isset( $args['value']['colors'] ) ) {
				$color = $args['value']['colors'][0];
			} else {
				$color = is_array( $args['value'] ) ? current( $args['value'] ) : $args['value'];
			}

			$second_name = str_replace("[color]", "[second_color]", $args['name']);
			preg_match('/\[(\d+)\]/', $args['name'], $matches);

			preg_match('/\[(pa_[a-zA-Z0-9_]+)\]/', $args['name'], $matchAttribute);
			$color_attribute = ! empty( $matchAttribute ) ? $matchAttribute[1] : 'pa_color';

			$is_dual_color = '';
			$second_color = '';
			if( ! empty( $matches ) ) {
				$id = $matches[1];
				$is_dual_color = get_term_meta( $id, 'wcboost_variation_swatches_is_dual_color', true );
				$second_color = get_post_meta( get_the_ID(), 'wcboost_variation_swatches', true );
				$second_color = isset( $second_color[ $color_attribute ]['swatches'][$id]['second_color'] ) ? $second_color[ $color_attribute ]['swatches'][$id]['second_color'] : '';
			}

			$html = '<div class="wcboost-variation-swatches-field wcboost-variation-swatches__field-color">';
			$html .= ! empty( $args['label'] ) ? '<span class="label">' . esc_html( $args['label'] ) . '</span>' : '';
			$html .= sprintf( '<input type="text" name="%s" value="%s">', esc_attr( $args['name'] ), esc_attr( $color ) );
			$html .= ! empty( $is_dual_color ) ? sprintf( '<input type="text" name="%s" value="%s">', esc_attr( $second_name ), esc_attr( $second_color ) ) : '';
			$html .= ! empty( $args['desc'] ) ? '<p class="description">' . esc_html( $args['desc'] ) . '</p>' : '';
			$html .= '</div>';
        }

        return $html;
    }
}
