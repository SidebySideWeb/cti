<?php
namespace Glozin\Addons\Modules\Product_Bought_Together;

use Glozin\Addons\Modules\Base\Variation_Select as BaseVariation_Select;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Variation_Select extends BaseVariation_Select {

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
	 * Constructor
	 *
	 * @param WC_Product_Variable $product
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Render variation dropdown
	 *
	 * @return void
	 */
	public function render( $_product = null ) {
		global $product;

		if( ! empty( $product ) ) {
			$this->product = $_product;
		}

		if ( ! $this->product ) {
			return;
		}

		$output        = [];
		$outputOptions = [];
		$options       = $this->get_options();
		$selected      = false;
		$discount      = intval( get_post_meta( $product->get_id(), 'glozin_pbt_discount_all', true ) );

		if ( empty( $options ) ) {
			return;
		}

		foreach ( $options as $option ) :
			if( $discount && $discount > 0 ) {
				$variation_product = new \WC_Product_Variation( $option['variation_id'] );

				$price     = $variation_product->get_price();
				$priceHTML = ! empty( $option['price_html'] ) ? $option['price_html']: '<span class="price">' . $variation_product->get_price_html() . '</span>';
				$priceSale = $price * ( ( 100 - (float) $discount ) / 100 );

				if( WC()->cart->display_prices_including_tax() ) {
					$price = wc_get_price_including_tax( $variation_product, array( 'price' => $price ) );
					$priceSale = wc_get_price_including_tax( $variation_product, array( 'price' => $priceSale ) );
				}

				$option['price_html'] = '<div class="product-variation-price">' . $priceHTML . '<span class="price price-variation-new">' . wc_format_sale_price( $price, $priceSale ) . $variation_product->get_price_suffix( $priceSale ) . '</span></div>';
			}

			$data_stock = array(
				'button_text' => $option['button_text'],
				'stock'       => $option['stock'],
			);

			$outputOptions[] = sprintf( '<option value="%s" data-attributes="%s" data-image="%s" data-price="%s" data-price_html="%s" data-stock="%s" %s >%s</option>',
								esc_attr( $option['variation_id'] ),
								esc_attr( $this->json_encode_attribute( $option['attributes'] ) ),
								esc_attr( $option['thumbnail_src'] ),
								esc_attr( $option['price'] ),
								esc_attr( $option['price_html'] ),
								esc_attr( json_encode( $data_stock ) ),
								selected( true, $option['selected'], false ),
								esc_html( $option['label'] )
							);
		endforeach;

		$output[] = sprintf( '<select name="variation_id">
								<option>%s</option>
								%s
							</select>',
							esc_attr__( 'Select an option', 'glozin-addons' ),
							implode( '', $outputOptions )
						);

		if ( $selected ) {
			$attributes = $selected['attributes'];
		} else {
			$attributes = array_fill_keys( array_keys( $options[0]['attributes'] ), '' );
		}
		foreach ( $attributes as $attr_name => $attr_value ) {
			$output[] = sprintf(
				'<input class="variation-attribute" type="hidden" name="%s" value="%s">',
				esc_attr( $attr_name ),
				esc_attr( $attr_value )
			);
		}

		return implode( '', $output );
	}

	/**
	 * Get dropdown options
	 *
	 * @return array
	 */
	public function get_options() {
		if ( ! $this->product ) {
			return [];
		}

		$attributes = $this->product->get_variation_attributes();
		$variations = $this->product->get_available_variations();
		$options    = [];

		$default_attributes = $this->product->get_default_attributes();

		foreach ( $variations as $variation ) {
			$_variation = wc_get_product( $variation['variation_id'] );

			$option = [
				'variation_id'  => $variation['variation_id'],
				'price'         => $variation['display_price'],
				'price_html'    => $variation['price_html'],
				'thumbnail_src' => ! empty( $variation['image']['thumb_src'] ) ? $variation['image']['thumb_src'] : '',
				'attributes'    => [],
				'stock'         => 'in_stock',
				'button_text'   => $_variation->single_add_to_cart_text()
			];

			if( $_variation->is_on_backorder() ) {
				$option['button_text'] = esc_html__( 'Pre-order', 'glozin' );
				$option['stock'] = 'on_backorder';
			} elseif ( ! $_variation->is_in_stock() ) {
				$option['button_text'] = esc_html__( 'Sold out', 'glozin' );
				$option['stock'] = 'out_of_stock';
			}

			$variation_attributes = [];

			foreach ( $variation['attributes'] as $attribute_name => $value ) {
				if ( ! empty( $value ) ) {
					$terms = [ $value ];
				} else {
					$attr_name = (0 === strpos( $attribute_name, 'attribute_' )) ? str_replace( 'attribute_', '', $attribute_name ) : $attribute_name;
					$attr_name = urldecode($attr_name);

					if ( isset( $attributes[ $attr_name ] ) ) {
						$terms = $attributes[ $attr_name ];
					} else {
						$terms = [];

						foreach ( $attributes as $attr_raw_name => $attr_raw_values ) {
							if ( strtolower( $attr_raw_name ) == strtolower( $attr_name ) ) {
								$terms = $attr_raw_values;
								break;
							}
						}
					}
				}
				
				$variation_attributes[ $attribute_name ] = $terms;
			}

			// Create combinations.
			$attribute_combinations = $this->create_attribute_combinations( $variation_attributes );

			foreach ( $attribute_combinations as $seleted_attributes ) {
				$options[] = array_merge(
					$option,
					array(
						'attributes' => $seleted_attributes,
						'label'      => $this->create_attribute_combination_name( $seleted_attributes ),
						'selected'   => 0 == count( array_diff( $seleted_attributes, $default_attributes ) ),
					)
				);
			}
		}

		return $options;
	}
}
