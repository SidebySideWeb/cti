<?php
namespace Glozin\Addons\Modules\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Variation_Select {
	/**
	 * Variable product
	 *
	 * @var WC_Product_Variable
	 */
	protected $product;

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
	public function __construct( $product = null ) {
		$product = $product ? $product : $GLOBALS['product'];
		if ( $product && $product->is_type( 'variable' ) ) {
			$this->product = $product;
		}
	}

	/**
	 * Render variation dropdown
	 *
	 * @return void
	 */
	public function render( $product = null ) {
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
					$data_stock = array(
						'button_text' => $option['button_text'],
						'stock'       => $option['stock'],
						'is_pre_order'=> !empty( $option['is_pre_order'] ) ? true : false,
					);
					$price_html = ! empty( $option['price_html'] ) ? $option['price_html']: '<span class="price">' . $this->product->get_price_html() . '</span>';
				?>
				<option
					value="<?php echo esc_attr( $option['variation_id'] ) ?>"
					data-attributes="<?php echo esc_attr( $this->json_encode_attribute( $option['attributes'] ) ); ?>"
					data-image="<?php echo esc_attr( $option['thumbnail_src'] ); ?>"
					data-stock="<?php echo esc_attr( json_encode( $data_stock ) ); ?>"
					data-price_html="<?php echo esc_attr( $price_html ); ?>"
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
				'button_text'   => $_variation->single_add_to_cart_text()
			];

			if ( ! $_variation->is_in_stock() ) {
				$option['button_text'] = esc_html__( 'Sold out', 'glozin' );
				$option['stock'] = 'out_of_stock';
			}

			if( $_variation->get_meta( '_pre_order_status' ) == 'yes' ) {
				$option['is_pre_order'] = true;
				$option['button_text'] = esc_html__( 'Pre-Order', 'glozin' );
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

	/**
	 * Get the name of an attribute term
	 *
	 * @param  string $slug Term slug
	 * @param  string $attribute_taxonomy Attribute taxonomy name
	 *
	 * @return void
	 */
	public function get_attribute_term_name( $slug, $attribute_taxonomy ) {
		$terms = wc_get_product_terms(
			$this->product->get_id(),
			$attribute_taxonomy
		);

		$term = wp_list_filter( $terms, array( 'slug' => $slug ) );
		$term = $term ? array_shift( $term ) : null;

		return $term ? $term->name : '';
	}

	/**
	 * Create combinations from variation attributes
	 *
	 * @param  array $terms_array Array of array of attribute term
	 *
	 * @return array[]
	 */
	public function create_attribute_combinations( $terms_array ) {
		$combinations = array( array() );

		foreach ( $terms_array as $attribute_name => $attribute_terms ) {
			$temp = array();

			foreach ( $combinations as $result_item ) {
				foreach ( $attribute_terms as $term ) {
					$temp[] = array_merge( $result_item, array( $attribute_name => $term ) );
				}
			}

			$combinations = $temp;
		}

		return $combinations;
	}

	/**
	 * Join attribute names into one
	 *
	 * @param  array $attributes
	 *
	 * @return string
	 */
	public function create_attribute_combination_name( $attributes ) {
		$parts = [];

		foreach ( $attributes as $attribute_name => $attribute_slug ) {
			$taxonomy_name = (0 === strpos( $attribute_name, 'attribute_' )) ? str_replace( 'attribute_', '', $attribute_name ) : $attribute_name;
			$attribute_name = $attribute_slug;

			if ( taxonomy_exists( urldecode( $taxonomy_name ) ) ) {
				$attribute_name = $this->get_attribute_term_name( $attribute_slug, urldecode( $taxonomy_name ));
			}

			$parts[] = $attribute_name;
		}

		return implode( ' / ', $parts );
	}

	public function json_encode_attribute( $attribute ) {
		return json_encode( $attribute, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE );
	}
}