<?php

namespace Glozin\Addons\Modules\Linked_Variant;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main class of plugin for admin
 */
class Frontend {

	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private static $instance;

	/**
	 * Product id
	 *
	 * @var $product_id
	 */
	private static $product_id;

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

	const POST_TYPE = 'em_linked_variant';

	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'woocommerce_single_product_summary', array( $this, 'linked_variant' ), 26 );
		add_action( 'glozin_linked_variant_elementor', array( $this, 'linked_variant' ), 27 );
	}

		/**
	 * Enqueue scripts
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		if ( is_singular( 'product' ) || is_singular( 'glozin_builder' ) ) {
			$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_style( 'glozin-linked-variant', GLOZIN_ADDONS_URL . 'modules/linked-variant/assets/linked-variant' . $debug . '.css', array(), GLOZIN_ADDONS_VER );
			wp_enqueue_script( 'glozin-linked-variant', GLOZIN_ADDONS_URL . 'modules/linked-variant/assets/linked-variant' . $debug . '.js', array( 'jquery' ), GLOZIN_ADDONS_VER, array('strategy' => 'defer') );
		}
	}

	/**
	 * Get product bought together
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function linked_variant() {
		global $product;

		if ( ! $product->is_type( 'simple' ) && ! $product->is_type( 'affiliate' ) ) {
            return;
        }

		self::$product_id = ! empty( self::$product_id ) ? self::$product_id : $this->get_product_id();

		if( empty( self::$product_id ) ) {
			return;
		}

		$items       = get_post_meta( self::$product_id, 'glozin_linked_variant_items', true );
		$product_ids = get_post_meta( self::$product_id, '_product_linked_variant_ids', true );
		$product_ids = array_diff( $product_ids, [ $product->get_id() ] );

		if ( empty( $items['attributes'] ) ) {
			return;
		}
	?>
		<div id="glozin-linked-variant" class="glozin-linked-variant linked-variant">
		<?php
			foreach( $items['attributes'] as $attribute ) :
				$attribute_id = (int) filter_var( $attribute, FILTER_SANITIZE_NUMBER_INT );
				$use_image    = isset( $items['images'] ) && in_array( $attribute, $items['images'] ) ? true: false;
				$shape        = isset( $items['shape']['id:' . $attribute_id ] ) ? 'wcboost-variation-swatches--' . $items['shape']['id:' . $attribute_id ] : 'wcboost-variation-swatches--' . self::get_settings_swatches( 'shape' );
				
				$size = self::get_settings_swatches( 'size' );
				if( isset( $items['size']['id:' . $attribute_id ] ) && ! empty( $items['size']['id:' . $attribute_id ]['width'] ) ) {
					$size['width'] = $items['size']['id:' . $attribute_id ]['width'];
				}

				if( isset( $items['size']['id:' . $attribute_id ] ) && ! empty( $items['size']['id:' . $attribute_id ]['height'] ) ) {
					$size['height'] = $items['size']['id:' . $attribute_id ]['height'];
				}

				$attribute    = wc_get_attribute( $attribute_id );

				if ( ! $attribute ) {
					continue;
				}

				$args = [
					'taxonomy'   => $attribute->slug,
					'hide_empty' => false
				];

				$terms         = get_terms( $args );
				$current_terms = wc_get_product_terms( $product->get_id(), $attribute->slug, [ 'fields' => 'ids' ] );

				if ( empty( $terms ) || empty( $current_terms ) ) {
					continue;
				}

				if( $attribute->type == 'select' && wc_string_to_bool( self::get_settings_swatches( 'auto_button' ) ) ) {
					$attribute->type = 'button';
				}

				$attribute->type = $use_image ? 'image' : $attribute->type;

				$size = ! empty( $size ) ? sprintf( '--wcboost-swatches-item-width: %1$dpx; --wcboost-swatches-item-height: %2$dpx;', absint( $size['width'] ), absint( $size['height'] ) ) : '';
		?>
				<div class="glozin-linked-variant__attribute mb-20">
					<div class="glozin-linked-variant__attribute-label text-dark mb-10 fw-semibold heading-letter-spacing lh-normal">
						<?php esc_html_e( 'More', 'glozin-addons' ); ?>
						<?php echo esc_html( strtolower( wc_attribute_label( $attribute->name ) ) . __('s:', 'glozin-addons' ) ); ?>
					</div>
					<div class="glozin-linked-variant__attribute-value wcboost-variation-swatches--<?php echo esc_attr( $attribute->type ); ?> <?php echo esc_attr( $shape ); ?> <?php echo esc_attr( $product->get_stock_status() ); ?> wcboost-variation-swatches--has-tooltip d-flex flex-wrap gap-10">
						<?php if( $attribute->type == 'select' ) : ?>
							<select class="glozin-linked-variant__select">
                                <option value=""><?php echo esc_html( __( 'Select', 'woocommerce' ) . ' ' . $attribute->name ); ?></option>
						<?php endif; ?>

							<?php $count = 0; foreach ( $terms as $term ) :
								if ( in_array( $term->term_id, $current_terms ) ) {
									if( $count !== 0 ) {
										self::get_attributes_swaches_html( $product->get_id(), $term, $attribute, $size, true );
									}
									
									$count++;
								} else {
									$product_id = self::get_product_id_has_attribute( $term, $product_ids );

									if( $product_id ) {
										self::get_attributes_swaches_html( $product_id, $term, $attribute, $size );
									}
								}
							endforeach; ?>

						<?php if( $attribute->type == 'select' ) : ?>
							</select>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php
	}

	public function get_attributes_swaches_html( $product_id, $term, $attribute, $size, $active = false ) {
		$product = wc_get_product( $product_id );
		if( $attribute->slug == $term->taxonomy ) {
			switch($attribute->type) {
				case 'color':
						$color = ! empty( get_term_meta( $term->term_id, 'swatches_color' ) ) ? '--wcboost-swatches-item-color:' . get_term_meta( $term->term_id, 'swatches_color' )[0] : '';
						echo sprintf(
							'<a class="wcboost-variation-swatches__item %s %s" href="%s" style="%s" aria-label="%s" data-value="%s" tabindex="0" role="button">
								<span class="wcboost-variation-swatches__name">%s</span>
							</a>',
							$active ? 'selected' : '',
							$product->get_stock_status() == 'outofstock' ? 'out-of-stock' : '',
							get_the_permalink( $product_id ),
							esc_attr( $size . $color ),
							esc_attr( $term->name ),
							esc_attr( $term->name ),
							esc_html( $term->name )
						);
                    break;
                case 'image':
					$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
					$gallery_thumbnail_size = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
					$image = ! empty( get_post_thumbnail_id( $product_id ) ) ? wp_get_attachment_image( get_post_thumbnail_id( $product_id ), $gallery_thumbnail_size ) : wc_placeholder_img_src();
					$image = ! empty( $image ) ? $image : wc_placeholder_img_src();

					echo sprintf(
						'<a class="wcboost-variation-swatches__item %s %s" href="%s" style="%s" aria-label="%s" data-value="%s" tabindex="0" role="button">
							%s
							<span class="wcboost-variation-swatches__name">%s</span>
						</a>',
						$active ? 'selected' : '',
						$product->get_stock_status() == 'outofstock' ? 'out-of-stock' : '',
						get_the_permalink( $product_id ),
						esc_attr( $size ),
						esc_attr( $term->name ),
						esc_attr( $term->name ),
						$image,
						esc_html( $term->name )
					);
					break;
                case 'button':
                case 'label':
					echo sprintf(
						'<a class="wcboost-variation-swatches__item %s %s" href="%s" style="%s" aria-label="%s" data-value="%s" tabindex="0" role="button">
							<span class="wcboost-variation-swatches__name">%s</span>
						</a>',
						$active ? 'selected' : '',
						$product->get_stock_status() == 'outofstock' ? 'out-of-stock' : '',
						get_the_permalink( $product_id ),
						esc_attr( $size ),
						esc_attr( $term->name ),
						esc_attr( $term->name ),
						esc_html( $term->name )
					);
                    break;
                default:
					echo sprintf(
						'<option value="%s" %s>
							%s
						</option>',
						get_the_permalink( $product_id ),
						$active ? 'selected' : '',
						esc_attr( $term->name )
					);
                    break;
			}
		}
	}

	/**
	 * Get product id
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function get_product_id() {
		$product_id = ! empty( self::$product_id ) ? self::$product_id : 0;

		$query = new \WP_Query( array(
			'post_type'        => self::POST_TYPE,
			'post_status'      => 'publish',
			'posts_per_page'   => 1,
			'fields'           => 'ids',
			'meta_key'         => '_product_linked_variant_ids',
			'orderby'          => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
			'no_found_rows'    => true,
			'suppress_filters' => false,
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key'     => '_product_linked_variant_ids',
					'value'   =>  get_the_ID(),
					'compare' => 'LIKE',
				),
				array(
					'key' => '_linked_variant_status',
					'value' =>  'yes',
				),
			)
		));

		$product_id = $query->posts ? $query->posts[0] : 0;
		self::$product_id = $product_id;
		wp_reset_postdata();
		return self::$product_id;
	}

	/**
	 * Get plugin settings.
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function get_settings_swatches( $name ) {
		return \WCBoost\VariationSwatches\Admin\Settings::instance()->get_option( $name );
	}

	public function get_swatches_meta( $product_id = null ) {
		$product_id = $product_id ? $product_id : get_the_ID();

		return \WCBoost\VariationSwatches\Admin\Product_Data::instance()->get_meta( $product_id );
	}

	public function get_image( $attachment_id, $size ) {
		if ( is_string( $size ) ) {
			return wp_get_attachment_image_src( $attachment_id, $size );
		}

		$width     = $size[0];
		$height    = $size[1];
		$image_src = wp_get_attachment_image_src( $attachment_id, 'full' );
		$file_path = get_attached_file( $attachment_id );

		if ( $file_path ) {
			$file_info = pathinfo( $file_path );
			$extension = '.' . $file_info['extension'];

			if ( $image_src[1] >= $width || $image_src[2] >= $height ) {
				$no_ext_path      = $file_info['dirname'] . '/' . $file_info['filename'];
				$cropped_img_path = $no_ext_path . '-' . $width . 'x' . $height . $extension;

				// the file is larger, check if the resized version already exists
				if ( file_exists( $cropped_img_path ) ) {
					$cropped_img_url = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );

					return [
						0 => $cropped_img_url,
						1 => $width,
						2 => $height,
					];
				}

				// No resized file, let's crop it
				$image_editor = wp_get_image_editor( $file_path );

				if ( is_wp_error( $image_editor ) || is_wp_error( $image_editor->resize( $width, $height, true ) ) ) {
					return false;
				}

				$new_img_path = $image_editor->generate_filename();

				if ( is_wp_error( $image_editor->save( $new_img_path ) ) ) {
					false;
				}

				if ( ! is_string( $new_img_path ) ) {
					return false;
				}

				$new_img_size = getimagesize( $new_img_path );
				$new_img      = str_replace( basename( $image_src[0] ), basename( $new_img_path ), $image_src[0] );

				return [
					0 => $new_img,
					1 => $new_img_size[0],
					2 => $new_img_size[1],
				];
			}
		}

		return false;
	}

	public function get_product_id_has_attribute( $term, $product_ids = [] ) {
		$post_in = $product_ids;

		if ( ! empty( $post_in ) ) {
			$args = [
				'post_type'      => 'product',
				'posts_per_page' => 1,
				'order'          => 'ASC',
				'fields'         => 'ids',
				'post__in'       => $post_in,
				'tax_query'      => array(
					[
						'taxonomy' => $term->taxonomy,
						'field'    => 'id',
						'terms'    => $term->term_id
					]
				)
			];

			if ( $_product = get_posts( $args ) ) {
				return $_product[0];
			}
		}

		return false;
	}
}