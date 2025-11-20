<?php
namespace Glozin\Addons\Modules\Variation_Compare;

use Glozin\Addons\Modules\Base\Variation_Select as BaseVariation_Select;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Variation_Form extends BaseVariation_Select {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * primary_attribute
	 *
	 * @var $primary_attribute
	 */
	protected $primary_attribute = null;

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
	public function render( $product = null ) {
		if ( ! $this->product ) {
			return;
		}

		if( ! isset( $this->primary_attribute ) ) {
			$attribute_name = get_post_meta( $this->product->get_id(), 'glozin_product_variation_attribute', true );
			$attribute_name = (0 === strpos( $attribute_name, 'pa_' )) ? str_replace( 'pa_', '', $attribute_name ) : $attribute_name;
			$this->primary_attribute = $attribute_name ? $attribute_name : get_option( 'glozin_variation_compare_primary' );
		}

		$options = $this->get_options();

		if ( empty( $options ) ) {
			return;
		}
		$swatches_args = $this->get_product_data( 'pa_' . $this->primary_attribute, $this->product->get_id() );
		$attribute_default = $this->product->get_default_attributes();
		?>
		<div class="gz-product-compare-attributes__selects">
			<?php $active_key = 0; ?>
			<?php foreach ( $options as $key => $option ) :?>
				<?php
					$active_class = '';
					$swatches_args['attribute_name'] = $key;
					$swatches_args['taxonomy'] = 'pa_' . $this->primary_attribute;
					$swatches_html = ! empty( $attribute_default['pa_' . $this->primary_attribute] ) ? $this->swatch_html($swatches_args, $attribute_default['pa_' . $this->primary_attribute]) : $this->swatch_html($swatches_args);
					if( ! empty( $attribute_default['pa_' . $this->primary_attribute] ) ) {
						if(  ! empty( $swatches_html['default'] ) ) {
							$active_class = 'active';
						}
					} else {
						if( $active_key == 0 ) {
							$active_class = 'active';
						}
					}
				?>
				<div class="gz-product-compare-attributes__item <?php echo $active_class; ?>" data-key="<?php echo esc_attr( $key ); ?>">
					<?php echo $swatches_html['html']; ?>
				</div>
			<?php $active_key++; endforeach; ?>
		</div>
		<div class="gz-product-compare-attributes__products">
			<?php $active_key = 0; ?>
			<?php foreach ( $options as $key => $option ) : ?>
				<?php
					$active_class = '';
					if( ! empty( $attribute_default['pa_' . $this->primary_attribute] ) ) {
						if(  ! empty( $option['attrs'][0]['attributes']['attribute_pa_' . $this->primary_attribute] ) && $option['attrs'][0]['attributes']['attribute_pa_' . $this->primary_attribute] == $attribute_default['pa_' . $this->primary_attribute] ) {
							$active_class = 'active';
						}
					} else {
						if( $active_key == 0 ) {
							$active_class = 'active';
						}
					}			
				?>
				<div class="gz-product-compare-attributes__product <?php echo $active_class; ?>" data-key="<?php echo esc_attr( $key ); ?>">
					<div class="gz-product-compare-attributes__product-image">
						<?php
							$variation = wc_get_product($option['attrs'][0]['variation_id']);
							$product_id = $variation->get_parent_id();
							$product_thumbnail_id = get_post_thumbnail_id( $product_id, 'woocommerce_thumbnail' );
							if ($variation->get_image_id()) {
								$product_thumbnail_id = $variation->get_image_id();
							}
							$swatches_args['attribute_name'] = $key;
							$swatches_args['taxonomy'] = 'pa_' . $this->primary_attribute;
							$term           = isset( $swatches_args['term'] ) && $swatches_args['term'] ? $swatches_args['term'] : get_term_by( 'slug', $swatches_args['attribute_name'], $swatches_args['taxonomy'] );
							$_key            = is_object( $term ) ? $term->term_id : sanitize_title( $term );
							$attribute_name = is_object( $term ) ? $term->name : $swatches_args['attribute_name'];
							$type = ( isset( $swatches_args['type'] ) && in_array( $swatches_args['type'], array('select', 'button') ) ) ? 'label' : $swatches_args['type'];
							if ( isset( $swatches_args['attributes'][ $_key ] ) && isset( $swatches_args['attributes'][ $_key ][ $type ] ) ) {
								$swatches_value = $swatches_args['attributes'][ $_key ][ $type ];
							}  else {
								$swatches_value = is_object( $term ) ? $this->get_attribute_swatches( $term->term_id, $type) : '';
							}

							if( ! empty( $swatches_value ) ) {
								$image = wp_get_attachment_image( $swatches_value, 'woocommerce_thumbnail' );
								if( ! empty( $image ) ) {
									echo wp_kses_post( $image );
								} else {
									echo wp_get_attachment_image( $product_thumbnail_id, 'woocommerce_thumbnail' );
								}
							} else {
								echo wp_get_attachment_image( $product_thumbnail_id, 'woocommerce_thumbnail' );
							}
						?>
					</div>
					<div class="gz-product-compare-attributes__product-label text-dark mt-10 fw-semibold">
						<?php echo esc_html( $key ); ?>
						<?php echo ! empty( $option['attrs'][0]['label'] ) ? ' / ' . esc_html( $option['attrs'][0]['label'] ) : ''; ?>
					</div>
				</div>
			<?php $active_key++; endforeach; ?>
		</div>
		<?php
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
				'variation_id' => $variation['variation_id'],
				'price'        => $variation['display_price'],
				'image_id'     => $variation['image_id'],
				'is_in_stock'  => $variation['is_in_stock'],
				'stock'        => 'in_stock',
				'button_text'  => $_variation->single_add_to_cart_text(),
			];

			if ( ! $_variation->is_in_stock() ) {
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
				$attribute_combination_name = $this->create_attribute_combination_name( $seleted_attributes );
				$option_attrs = array_merge(
					$option,
					array(
						'attributes' => $seleted_attributes,
						'label'      => urldecode( $attribute_combination_name['parts_name'] ),
						'selected'   => 0 == count( array_diff( $seleted_attributes, $default_attributes ) ),
					)
				);
				$primary_name = $attribute_combination_name['primary_name'];
				if( empty( $options[$primary_name]['thumbnail_src'] ) ) {
					$options[$primary_name]['image_id'] = $option['image_id'];
				}

				$options[$primary_name]['attrs'][] = $option_attrs;

			}
		}

		return $options;
	}

	/**
	 * Join attribute names into one
	 *
	 * @param  array $attributes
	 *
	 * @return array
	 */
	public function create_attribute_combination_name( $attributes ) {
		$parts_name = [];
		$primary_name = '';
		foreach ( $attributes as $attribute_name => $attribute_slug ) {
			$primary_attribute_name = (0 === strpos( $attribute_name, 'attribute_pa_' )) ? str_replace( 'attribute_pa_', '', $attribute_name ) : $attribute_name;
			$primary_attribute_name = (0 === strpos( $primary_attribute_name, 'pa_' )) ? str_replace( 'pa_', '', $primary_attribute_name ) : $primary_attribute_name;
			$taxonomy_name = (0 === strpos( $attribute_name, 'attribute_' )) ? str_replace( 'attribute_', '', $attribute_name ) : $attribute_name;
			$attribute_name = $attribute_slug;
			$term_id = 0;
			if ( taxonomy_exists( urldecode( $taxonomy_name ) ) ) {
				$term = $this->get_attribute_term( $attribute_slug, urldecode( $taxonomy_name ) );
				$attribute_name = $term ? $term->name : $attribute_name;
				$term_id = $term ? $term->term_id : $term_id;
			}

			if( urldecode( $primary_attribute_name ) ==  $this->primary_attribute) {
				$primary_name = $attribute_name;
			} else {
				$parts_name[] = $attribute_name;
			}

		}
		
		return array(
			'primary_name' => $primary_name,
			'parts_name' =>  implode(' / ', $parts_name )
		);
	}

	/**
	 * Get the name of an attribute term
	 *
	 * @param  string $slug Term slug
	 * @param  string $attribute_taxonomy Attribute taxonomy name
	 *
	 * @return object
	 */
	public function get_attribute_term( $slug, $attribute_taxonomy ) {
		$terms = wc_get_product_terms(
			$this->product->get_id(),
			$attribute_taxonomy
		);

		$term = wp_list_filter( $terms, array( 'slug' => $slug ) );
		$term = $term ? array_shift( $term ) : null;

		return $term;
	}

	/**
	 * Print HTML of a single swatch
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public function swatch_html( $swatches_args, $attribute_default = null ) {
		$args = [];
		$html           = '';
		$term           = isset( $swatches_args['term'] ) && $swatches_args['term'] ? $swatches_args['term'] : get_term_by( 'slug', $swatches_args['attribute_name'], $swatches_args['taxonomy'] );
		$key            = is_object( $term ) ? $term->term_id : sanitize_title( $term );
		$attribute_name = is_object( $term ) ? $term->name : $swatches_args['attribute_name'];
		$type           = ( isset( $swatches_args['type'] ) && in_array( $swatches_args['type'], array('select', 'button') ) ) ? 'label' : $swatches_args['type'];

		if ( isset( $swatches_args['attributes'][ $key ] ) && isset( $swatches_args['attributes'][ $key ][ $type ] ) ) {
			$swatches_value = $swatches_args['attributes'][ $key ][ $type ];
		}  else {
			$swatches_value = is_object( $term ) ? $this->get_attribute_swatches( $term->term_id, $type) : '';
		}

		switch ( $type ) {
			case 'color':
				$html = sprintf(
					'<span class="gz-product-compare__attribute-label gz-product-compare__attribute-color" data-text="%s" style="background-color:%s;"></span>',
					esc_attr( $attribute_name ),
					esc_attr( $swatches_value )
				);
				break;

			case 'image':
				if ( $swatches_value ) {
					$image = wp_get_attachment_image( $swatches_value, 'thumbnail');
					$html  = sprintf(
						'<span class="gz-product-compare__attribute-label gz-product-compare__attribute-image" data-text="%s">%s</span>',
						esc_attr( $attribute_name ),
						$image
					);
				}
				break;
			case 'button':
			case 'label':
				$html  = sprintf(
					'<span class="gz-product-compare__attribute-label gz-product-compare__attribute-%s" data-text="%s">%s</span>',
					esc_attr( $type ),
					esc_attr( $attribute_name ),
					! empty( $swatches_value ) ? esc_html( $swatches_value ) : esc_html( $attribute_name )
				);
				break;
			default:
				break;

		}

		$args['html'] = $html;

		if( !empty( $term ) && is_object( $term ) && $term->slug == $attribute_default ) {
			$args['default'] = true;
		}

		return $args;
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

	/**
	 * Get atribute swatches data
	 *
	 * @param int $term_id
	 * @param string $type
	 * @return mixed
	 */
	public function get_attribute_swatches( $term_id, $type = 'color' ) {
		if ( class_exists( '\WCBoost\VariationSwatches\Admin\Term_Meta' ) ) {
			$data = \WCBoost\VariationSwatches\Admin\Term_Meta::instance()->get_meta( $term_id, $type );
		} else {
			$data = get_term_meta( $term_id, $type, true );
		}

		return $data;
	}

}
