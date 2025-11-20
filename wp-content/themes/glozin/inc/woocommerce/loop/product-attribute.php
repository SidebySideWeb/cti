<?php
/**
 * WooCommerce product attribute template hooks.
 *
 * @package Glozin
 */

namespace Glozin\WooCommerce\Loop;
use Glozin\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Product_Attribute
 */
class Product_Attribute {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * Product Attribute Types
	 *
	 * @var $product_attr_types
	 */
	protected static $product_attr_types_second = null;

	/**
	 * Product Attribute
	 *
	 * @var $product_attribute
	 */
	protected static $product_attribute = null;


	/**
	 * Product Attribute Number
	 *
	 * @var $product_attribute_number
	 */
	protected static $product_attribute_number = null;

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

	public function loop_primary_attribute() {
		if( ! empty( apply_filters( 'glozin_product_loop_primary_attribute', Helper::get_option('product_card_attribute') ) ) ) {
			return $this->product_attribute();
		}
	}

	/**
	 * Display product attribute
	 *
	 * @since 1.0
	 */
	public function product_attribute( $product = null ) {
		if( empty( $product ) ) {
			global $product;
		}

		if( $product->get_type() != 'variable' ) {
			return;
		}

		if ( empty( self::$product_attribute ) ) {
			self::$product_attribute = Helper::get_option( 'product_card_attribute' );
		}

		if ( empty( self::$product_attribute_number ) ) {
			self::$product_attribute_number = Helper::get_option( 'product_card_attribute_number' );
		}

		$attrs_number = get_post_meta( $product->get_id(), 'glozin_product_attribute_number', true );
		if( ! empty( $attrs_number ) ) {
			self::$product_attribute_number = $attrs_number;
		}

		$attribute_taxonomy = maybe_unserialize( get_post_meta( $product->get_id(), 'glozin_product_attribute', true ) );
		$attribute_taxonomy = empty( $attribute_taxonomy ) ? 'pa_' . sanitize_title( self::$product_attribute ) : $attribute_taxonomy;
		if ( $attribute_taxonomy == 'none' ) {
			return;
		}

		$product_attributes         = $product->get_attributes();
		if ( ! $product_attributes ) {
			return;
		}
		$product_attribute = isset( $product_attributes[$attribute_taxonomy] ) ? $product_attributes[$attribute_taxonomy] : '';
		if ( ! $product_attribute ) {
			return;
		}

		$output = '';
		$swatches_args  = [];
		$variation_args  = [];
		if ( function_exists( 'wcboost_variation_swatches' ) ) {
			$swatches_args = self::get_product_data( $product_attribute['name'], $product->get_id() );
		}
		$swatches_args['taxonomy'] = $attribute_taxonomy;

		$product_image_id = $product->get_image_id();



		if ( isset( $product_attribute['variation'] ) && $product_attribute['variation'] ) {
			$available_variations = $product->get_available_variations();
			$index = 1;
			$variations_atts = array();
			$variations_total = $product->get_variation_attributes();
			if( empty( $available_variations['attributes'] ) && ! empty( $variations_total ) && empty( $available_variations[0]['attributes']['attribute_' . $attribute_taxonomy] ) ) {
				foreach( $variations_total[urldecode($attribute_taxonomy)] as $key => $name ) {
					$swatches_args['attribute_name'] = $name;
					$output .= self::swatch_html($swatches_args, $variation_args, false);

					if( $index >= self::$product_attribute_number ) {
						$count_more = count(array_unique( $variations_total[$attribute_taxonomy] ) ) - $index;
						if( $index < count(array_unique( $variations_total[$attribute_taxonomy] ) ) ) {
							$output .= sprintf('<a href="%s" class="product-variation-item-more">+%s</a>', esc_url( $product->get_permalink() ), $count_more);
						}
						break;
					}

					$index++;
				}
			} else {
				$has_selected = false;
				foreach( $available_variations as $variation ) {
					if( ! $variation['attributes'] ) {
						continue;
					}

					$v_attribute = $variation['attributes'];
					if( ! isset( $v_attribute['attribute_' . $attribute_taxonomy] ) ) {
						continue;
					}

					$swatches_args['attribute_name'] =  sanitize_title($v_attribute['attribute_' . $attribute_taxonomy]);
					if( empty ($swatches_args['attribute_name'])  ) {
						continue;
					}

					if( in_array( $swatches_args['attribute_name'], $variations_atts ) ) {
						continue;
					}

					$variations_atts[] = $swatches_args['attribute_name'];

					if( $attachment_id = $variation['image_id']) {
						$thumbnail = wp_get_attachment_image_src( $attachment_id,  'woocommerce_thumbnail' );
						$variation_args['img_src']   = $thumbnail  ? $thumbnail[0] : '';
						$variation_args['img_srcset']  = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $attachment_id ) : '';
						$variation_image =  wp_get_attachment_image_src( $attachment_id, 'full' );
						$variation_args['img_original']  = $variation_image ? $variation_image[0] : '';
					}

					$selected = false;
					if( $product_image_id == $variation['image_id'] && ! $has_selected ) {
						$has_selected = true;
						$selected = true;
					}

					$output .= self::swatch_html($swatches_args, $variation_args, $selected);

					if( $index >= self::$product_attribute_number ) {
						$count_more = count(array_unique( $variations_total[$attribute_taxonomy] ) ) - $index;
						if( $index < count(array_unique( $variations_total[$attribute_taxonomy] ) ) ) {
							$output .= sprintf('<a href="%s" class="product-variation-item-more">+%s</a>', esc_url( $product->get_permalink() ), $count_more);
						}
						break;
					}

					$index++;
				}
			}
		}

		$classes = intval( Helper::get_option( 'product_card_attribute_image_swap_hover' ) ) ? ' gz-variation-hover': '';

		if ( function_exists( 'wcboost_variation_swatches' ) ) {
			if ( Helper::get_option('product_card_attribute_variation_swatches') == 'swatches' ) {
				$classes .= ' wcboost-variation-swatches--' . \WCBoost\VariationSwatches\Admin\Settings::instance()->get_option( 'shape' );
			}
		}

		if( $output ) {
			echo sprintf('<div class="product-variation-items d-flex align-items-center%s">%s</div>',$classes, $output);
		}
	}


	/**
	 * Print HTML of a single swatch
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public function swatch_html( $swatches_args, $variation_args, $selected ) {
		$html           = '';
		$term           = isset( $swatches_args['term'] ) && $swatches_args['term'] ? $swatches_args['term'] : get_term_by( 'slug', $swatches_args['attribute_name'], $swatches_args['taxonomy'] );
		$key            = is_object( $term ) ? $term->term_id : sanitize_title( $term );
		$_attribute_name = isset( $swatches_args['attribute_name'] ) && isset( $swatches_args['taxonomy'] ) && is_object( get_term_by( 'slug', urldecode($swatches_args['attribute_name']), urldecode($swatches_args['taxonomy'])) ) ? get_term_by( 'slug', urldecode($swatches_args['attribute_name']), urldecode($swatches_args['taxonomy']))->name : urldecode( $swatches_args['attribute_name'] );
		$attribute_name = is_object( $term ) ? $term->name : $_attribute_name;
		$type = 'label';
		if( isset( $swatches_args['type'] ) ) {
			$type           = in_array( $swatches_args['type'], array('select', 'button') ) ? 'label' : $swatches_args['type'];
		}

		if ( isset( $swatches_args['attributes'][ $key ] ) && isset( $swatches_args['attributes'][ $key ][ $type ] ) ) {
			$swatches_value = $swatches_args['attributes'][ $key ][ $type ];
		}  else {
			$swatches_value = is_object( $term ) ? self::get_attribute_swatches( $term->term_id, $type) : '';
		}
		$css_class = $variation_attrs = $data_tooltip = '';
		if( $variation_args ) {
			$variation_json =  wp_json_encode( $variation_args );
			$variation_attrs = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variation_json ) : _wp_specialchars( $variation_json, ENT_QUOTES, 'UTF-8', true );
			$variation_attrs = $variation_args ? sprintf('data-product_variations="%s"', $variation_attrs) : '';
			$css_class = $variation_args  ? ' product-variation-item--attrs' : '';
		}

		$css_class .= ' gz-tooltip-inside';

		$css_class = apply_filters( 'glozin_product_attribute_'.$type.'_css_class', $css_class, $type, $term->term_id );
		$variation_attrs = apply_filters( 'glozin_product_attribute_'.$type.'_variation_attrs', $variation_attrs, $type, $term->term_id, $swatches_args );

		if( $selected ) {
			$css_class .= ' selected';
		}

		switch ( $type ) {
			case 'color':
				$html = sprintf(
					'<span class="product-variation-item product-variation-item--color%s" %s data-tooltip="%s"><span class="product-variation-item__color wcboost-variation-swatches__name" style="background-color:%s;"></span></span>',
					esc_attr( $css_class ),
					$variation_attrs,
					esc_attr( $attribute_name ),
					esc_attr( $swatches_value ),
				);
				break;

			case 'image':
				if ( $swatches_value ) {
					$image = wp_get_attachment_image( $swatches_value, 'thumbnail' );
					$html  = sprintf(
						'<span class="product-variation-item product-variation-item--image%s" %s data-tooltip="%s"><span class="product-variation-item__image gz-ratio">%s</span></span>',
						esc_attr( $css_class ),
						$variation_attrs,
						esc_attr( $attribute_name ),
						$image
					);
				}

				break;

			default:
				$label = $swatches_value ? $swatches_value : $attribute_name;

				$html  = sprintf(
					'<span class="product-variation-item product-variation-item--label%s" %s data-tooltip="%s">%s</span>',
					esc_attr( $css_class ),
					$variation_attrs,
					esc_attr( $attribute_name ),
					esc_html( $label )
				);
				break;

		}

		return $html;
	}

	public function get_attribute_swatches( $term_id, $type = 'color' ) {
		if ( class_exists( '\WCBoost\VariationSwatches\Admin\Term_Meta' ) ) {
			$data = \WCBoost\VariationSwatches\Admin\Term_Meta::instance()->get_meta( $term_id, $type );
		} else {
			$data = get_term_meta( $term_id, $type, true );
		}

		return $data;
	}

	/**
	 * Get product type
	 *
	 * @since 1.0.0
	 *
	 * @param string $attribute
	 *
	 * @return object
	 */
	protected function get_product_data( $attribute_name, $product_id ) {
		if ( class_exists( '\WCBoost\VariationSwatches\Admin\Product_Data' ) ) {
			$swatches_meta = \WCBoost\VariationSwatches\Admin\Product_Data::instance()->get_meta( $product_id );
			$attribute_key = sanitize_title( $attribute_name );
			$swatches_args = [];
			if ( $swatches_meta && ! empty( $swatches_meta[ $attribute_key ] ) ) {
				$swatches_args = [
					'type'       => $swatches_meta[ $attribute_key ]['type'],
					'attributes' => $swatches_meta[ $attribute_key ]['swatches'],
				];
			}

			if( ! $swatches_args || ( isset($swatches_args['type'] ) && ! $swatches_args['type'] ) ) {
				$attribute_slug     = wc_attribute_taxonomy_slug( $attribute_name );
				$taxonomies         = wc_get_attribute_taxonomies();
				$attribute_taxonomy = wp_list_filter( $taxonomies, [ 'attribute_name' => $attribute_slug ] );
				$attribute_taxonomy = ! empty( $attribute_taxonomy ) ? array_shift( $attribute_taxonomy ) : null;

				if( $attribute_taxonomy ) {
					$swatches_args = [
						'type'       => $attribute_taxonomy->attribute_type,
					];
				}
			}

			return $swatches_args;
		}
	}

}
