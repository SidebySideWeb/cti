<?php

namespace Glozin\Addons\Modules\Dynamic_Pricing_Discounts;

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

	const POST_TYPE = 'gz_pricing_discount';

	/**
	 * Post id
	 *
	 * @var $dynamic_post_id
	 */
	private static $dynamic_post_id;

	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_filter('woocommerce_add_cart_item_data', array($this, 'cart_item_data'), 10, 4);

		if( apply_filters( 'glozin_dynamic_pricing_discounts_position_elementor', true ) ) {
			if( get_option( 'glozin_dynamic_pricing_discounts_position', 'above' ) == 'above' ) {
				add_action( 'woocommerce_single_product_summary', array( $this, 'dynamic_pricing_discounts' ), 27 );
			} else {
				add_action( 'woocommerce_single_product_summary', array( $this, 'dynamic_pricing_discounts' ), 30 );
			}
		}

		add_action('woocommerce_after_add_to_cart_button', array( $this, 'render_dynamic_post_id' ));


		add_action( 'glozin_dynamic_pricing_discounts_elementor', array( $this, 'pricing_discounts_elementor' ), 27 );

		// Cart
		add_action( 'woocommerce_before_calculate_totals', [ $this, 'before_calculate_totals' ], 9999 );
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
			wp_enqueue_style( 'glozin-dynamic-pricing-discounts', GLOZIN_ADDONS_URL . 'modules/dynamic-pricing-discounts/assets/dynamic-pricing-discounts.css', array(), '1.0.0' );
			wp_enqueue_script('glozin-dynamic-pricing-discounts', GLOZIN_ADDONS_URL . 'modules/dynamic-pricing-discounts/assets/dynamic-pricing-discounts.js',  array('jquery'), '1.0.0' );

			$data = array(
				'currency_pos'       => get_option( 'woocommerce_currency_pos' ),
				'currency_symbol'    => function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '',
				'thousand_sep'       => function_exists( 'wc_get_price_thousand_separator' ) ? wc_get_price_thousand_separator() : '',
				'decimal_sep'        => function_exists( 'wc_get_price_decimal_separator' ) ? wc_get_price_decimal_separator() : '',
				'price_decimals'     => function_exists( 'wc_get_price_decimals' ) ? wc_get_price_decimals() : '',
			);

			wp_localize_script(
				'glozin-dynamic-pricing-discounts', 'glozinDpd', $data
			);
		}
	}

	public function pricing_discounts_elementor() {
		$this->dynamic_pricing_discounts();
	}

	public function cart_item_data($cart_item_data, $product_id, $variation_id, $quantity) {
		$dynamic_post_id = ! empty( $_REQUEST['glozin_dynamic_post_id'] ) ? intval( $_REQUEST['glozin_dynamic_post_id'] ) : 0;
		if ( ! empty( $dynamic_post_id ) ) {
			$cart_item_data['dynamic_post_id'] = $dynamic_post_id;
		}
		return $cart_item_data;
	}

	/**
	 * Get product bought together
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function dynamic_pricing_discounts() {
		global $product;

		if( empty( $product ) ) {
			return;
		}

		if ( ! $product->is_type( 'simple' ) && ! $product->is_type( 'variable' ) ) {
            return;
        }

		$dynamic_post_id = $this->get_dynamic_post_id();

		if( empty( $dynamic_post_id ) ) {
			return;
		}

		$items = (array) get_post_meta( $dynamic_post_id, 'glozin_dynamic_pricing_discounts_items', true );

		if( empty( $items ) ) {
			return;
		}
		$layout = get_post_meta( $dynamic_post_id, '_dynamic_pricing_discounts_layout', true );
		$layout = ! empty( $layout ) ? $layout : 'list';
		?>
		<div id="glozin-dynamic-pricing-discounts" class="dynamic-pricing-discounts mb-25 dynamic-pricing-discounts--<?php echo esc_attr( $layout ); ?> <?php echo ! empty( get_option( 'glozin_dynamic_pricing_discounts_position', 'above' ) ) ? 'dynamic-pricing-discounts--' . esc_attr( get_option( 'glozin_dynamic_pricing_discounts_position', 'above') ) : ''; ?>">
			<?php foreach ( $items as $item ) : ?>
				<?php if( ( $item['from' ] >= 0 || $item['to' ] > 0 ) && $item['discount'] > 0 ) : ?>
				<?php
					$discount_price         = ( $product->get_price() / 100 ) * ( 100 - intval( $item['discount'] ) );
					$item['price']          = $product->get_price();
					$item['discount_price'] = $discount_price;
				?>
					<div class="dynamic-pricing-discounts-item position-relative rounded-5 py-20 px-20" data-discount="<?php echo esc_attr( json_encode( $item ) ); ?>">
						<?php if( $layout == 'grid' && ! empty( $item['popular'] ) && $item['popular'] == 'yes' ) : ?>
							<span class="dynamic-pricing-discounts-item__popular position-absolute">
								<span class="dynamic-pricing-discounts-item__popular-badges">
									<?php echo \Glozin\Addons\Helper::get_svg( 'tag' ); ?>
									<span class="text"><?php esc_html_e( 'Most popular', 'glozin-addons' ); ?></span>
								</span>
							</span>
						<?php endif; ?>
						<div class="dynamic-pricing-discounts-item__summary d-flex align-items-center gap-10">
							<input type="radio" class="dynamic-pricing-discounts-item__quantity" name="dynamic_pricing_discounts_item_quantity" value="<?php echo esc_attr( $item['to'] ); ?>" />
							<?php if( $layout == 'list' ) : ?>
								<div class="dynamic-pricing-discounts-item__label fs-15 text-dark fw-semibold heading-letter-spacing lh-normal">
									<?php if( $item['from' ] >= 0 && empty( $item['to' ] ) ) : ?>
										<?php printf( __( 'Buy %s+ items for', 'glozin-addons' ), $item['from'] ); ?>
									<?php elseif( $item['from' ] == $item['to' ] ) : ?>
										<?php printf( __( 'Buy %s items for', 'glozin-addons' ), $item['from'] ); ?>
									<?php else : ?>
										<?php printf( __( 'Buy from %s to %s items for', 'glozin-addons' ), $item['from'], $item['to'] ); ?>
									<?php endif; ?>
									<span><?php printf( __( ' %s Off per item.', 'glozin-addons' ), $item['discount'] . '%' ); ?></span>
								</div>
							<?php else : ?>
								<?php if( ! empty( $item['thumbnail_id'] ) ) : ?>
									<span class="dynamic-pricing-discounts-item__thumbnail">
										<?php
											$gallery_thumbnail                = wc_get_image_size( 'gallery_thumbnail' );
											$gallery_thumbnail_size           = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
											echo wp_get_attachment_image( $item['thumbnail_id'], $gallery_thumbnail_size );
										?>
									</span>
								<?php endif; ?>
								<?php if( $item['from'] ) : ?>
									<span class="dynamic-pricing-discounts-item__form text-dark fs-12 fw-semibold">
										<?php printf( __( '%sx %s', 'glozin-addons' ), $item['from'], $item['unit'] ); ?>
									</span>
								<?php endif; ?>
							<?php endif; ?>
							<div class="dynamic-pricing-discounts-item__discount-price ms-auto text-right">
								<span class="dynamic-pricing-discounts-item__discount d-inline-block text-light fw-medium fs-12 lh-1"><?php printf( __( 'Save %s', 'glozin-addons' ), $item['discount'] . '%' ); ?></span>
								<input type="hidden" name="dynamic_pricing_discounts_item_discount" value="<?php echo esc_attr( $item['discount'] ); ?>" />
								<div class="dynamic-pricing-discounts-item__price d-flex <?php echo $product->is_type( 'variable' ) ? 'hidden' : ''; ?>">
									<span class="price"><?php echo wc_format_sale_price( $product->get_price(), $discount_price ); ?></span>

									<?php
										$unit_measure = maybe_unserialize( get_post_meta( $product->get_id(), 'unit_measure', true ) );
										if ( $unit_measure ) {
											echo '<span class="gz-price-unit"><span class="divider">/</span> '. esc_html( $unit_measure ) .'</span>';
										}
									?>
								</div>
							</div>
						</div>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>

		<?php
	}

	public function render_dynamic_post_id() {
		$dynamic_post_id = $this->get_dynamic_post_id();

		if( empty( $dynamic_post_id ) ) {
			return;
		}
		?>
		<input type="hidden" name="glozin_dynamic_post_id" value="<?php echo intval( $dynamic_post_id ); ?>">
		<?php
	}

	public function get_dynamic_post_id() {
		global $wpdb;
		if (isset(self::$dynamic_post_id)) {
			return self::$dynamic_post_id;
		}

		$post_type = self::POST_TYPE;
		$dynamic_post_id = get_the_ID();
		$terms = get_the_terms($dynamic_post_id, 'product_cat');
		$term_ids = $terms ? wp_list_pluck($terms, 'term_id') : [];
		$term_ids_sql = '';
		if (!empty($term_ids)) {
			$term_ids_sql = implode(',', array_map('intval', $term_ids));
		}

		$query = $wpdb->prepare("
			SELECT p.ID
			FROM {$wpdb->posts} AS p
			LEFT JOIN {$wpdb->postmeta} AS pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_pricing_discount_status'
			LEFT JOIN {$wpdb->postmeta} AS pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_dynamic_pricing_discounts_display'
			LEFT JOIN {$wpdb->postmeta} AS pm3 ON p.ID = pm3.post_id AND pm3.meta_key = '_product_pricing_discount_ids'
			LEFT JOIN {$wpdb->term_relationships} AS tr ON p.ID = tr.object_id
			LEFT JOIN {$wpdb->term_taxonomy} AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'product_cat'
			WHERE p.post_type = %s
			  AND p.post_status = 'publish'
			  AND pm1.meta_value = 'yes'
			  AND (
				  (pm2.meta_value = 'products' AND pm3.meta_value LIKE %s)
				  OR (pm2.meta_value = 'categories' AND tt.term_id IN ($term_ids_sql))
			  )
			ORDER BY
			  FIELD(pm2.meta_value, 'products', 'categories') ASC,
			  p.menu_order ASC,
			  p.post_date DESC
			LIMIT 1
		", $post_type, '%' . $wpdb->esc_like($dynamic_post_id) . '%');

		$result = $wpdb->get_var($query);

		if ($result) {
			self::$dynamic_post_id = $result;
		}

		return self::$dynamic_post_id;
	}

	function before_calculate_totals( $cart_object ) {
		if ( ! defined( 'DOING_AJAX' ) && is_admin() ) {
			// This is necessary for WC 3.0+
			return;
		}

		$cart_contents = $cart_object->cart_contents;

		foreach ( $cart_contents as $cart_item_key => $cart_item ) {
			$matchingKeys = array_filter(array_keys($cart_item), function($key) {
				return preg_match('/^glozin_/', $key);
			});

			if( empty( $matchingKeys ) ) {
				if ( $cart_item['variation_id'] > 0 ) {
					$item_product = wc_get_product( $cart_item['variation_id'] );
				} else {
					$item_product = wc_get_product( $cart_item['product_id'] );
				}
				$product_price = $item_product->get_price();

				$dynamic_post_id = isset($cart_item['dynamic_post_id']) ? $cart_item['dynamic_post_id'] : 0;
				if( !empty( $dynamic_post_id ) ) {
					$items = (array) get_post_meta( $dynamic_post_id, 'glozin_dynamic_pricing_discounts_items', true );

					if ( ! empty( $items ) ) {
						foreach( $items as $item ) {
							if( ! empty( $item['discount'] ) && $item['discount'] !== '0' ) {
								if( ( intval( $item['from'] ) <= intval( $cart_item['quantity'] ) && intval( $cart_item['quantity'] ) <= intval( $item['to'] ) ) || ( ( empty( $item['to'] ) || intval( $item['to'] ) == 0 ) && intval( $item['from'] ) <= intval( $cart_item['quantity'] ) ) ) {
									$discount_price = $product_price * ( 100 - (float) $item['discount'] ) / 100;

									$cart_item['data']->set_price( $discount_price );
								}
							}
						}
					}
				}
			}
		}
	}
}