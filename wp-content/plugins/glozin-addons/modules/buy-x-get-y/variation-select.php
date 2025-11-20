<?php
namespace Glozin\Addons\Modules\Buy_X_Get_Y;

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
	public function render( $product = null, $quantity = null, $discount = null, $discount_type = null ) {
		if( ! empty( $product ) ) {
			$this->product = $product;
		}

		if ( ! $this->product ) {
			return;
		}

		$options = $this->get_options();
		$selected = false;

		if ( empty( $options ) ) {
			return;
		}

		?>
		<select name="variation_id">
			<option><?php esc_html_e( 'Select an option', 'glozin-addons' ); ?></option>
			<?php foreach ( $options as $option ) : ?>
				<?php $selected = $option['selected'] ? $option : $selected; ?>
				<?php
					$old_price  = 0;
					$price      = $option['price'];
					$price_html = ! empty( $option['price_html'] ) ? $option['price_html'] : wc_price( $option['price'] );

					if( $discount > 0 && $discount_type && $discount_type !== 'free' ) {
						$old_price = $price;
						if( $discount_type == 'fixed_price' ) {
							if( $discount < $price ) {
								$price -= $discount;
							}
						} else {
							if( $discount < 100 ) {
								$price -= ( $price * $discount / 100 );
							}
						}

						$price_html = '<del>'. wc_price( wc_format_decimal( $old_price, wc_get_price_decimals() ) ) .'</del><ins>'. wc_price( wc_format_decimal( $price, wc_get_price_decimals() ) ) .'</ins>';
					}

					if( $discount_type == 'free' ) {
						$old_price = $price;
						$price = 0;
						$price_html = '<del>'. wc_price( wc_format_decimal( $old_price, wc_get_price_decimals() ) ) .'</del><ins>'. wc_price( wc_format_decimal( $price, wc_get_price_decimals() ) ) .'</ins>';
					}

					$data_stock = array(
						'button_text' => $option['button_text'],
						'stock'       => $option['stock'],
					);
				?>
				<option
					value="<?php echo esc_attr( $option['variation_id'] ) ?>"
					data-variation="<?php echo esc_attr( $this->json_encode_attribute( array( $product->get_id() => array(
											'product_id'   => $product->get_id(),
											'variation_id' => $option['variation_id'],
											'attributes'   => $option['attributes'],
											'qty'          => $quantity,
											'price'		   => $price,
											'old_price'	   => $old_price,
											'price_html'   => esc_attr( $price_html )
										) ) ) ); ?>"
					data-attributes="<?php echo esc_attr( $this->json_encode_attribute( $option['attributes'] ) ); ?>"
					data-image="<?php echo esc_attr( $option['thumbnail_src'] ); ?>"
					data-stock="<?php echo esc_attr( json_encode( $data_stock ) ); ?>"
					<?php selected( true, $option['selected'] ); ?>
				>
					<?php echo esc_html( $option['label'] ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
		if ( $selected ) {
			$attributes = $selected['attributes'];
		} else {
			$attributes = array_fill_keys( array_keys( $options[0]['attributes'] ), '' );
		}
		foreach ( $attributes as $attr_name => $attr_value ) {
			printf(
				'<input type="hidden" name="%s" value="%s">',
				esc_attr( $attr_name ),
				esc_attr( $attr_value )
			);
		}
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
				'thumbnail_src' => ! empty( $variation['image']['thumb_src'] ) ? $variation['image']['thumb_src']: '',
				'attributes'    => [],
				'stock'         => 'in_stock',
				'is_purchasable'=> $_variation->is_purchasable(),
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
