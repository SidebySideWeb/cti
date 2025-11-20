<?php

namespace Glozin\Addons\Modules\Buy_X_Get_Y;

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

	const POST_TYPE = 'gz_buy_x_get_y';

	/**
	 * Post id
	 *
	 * @var $post_id
	 */
	private static $post_id;

	/**
	 * Main cart key
	 *
	 * @var $cart_key
	 */
	private static $cart_key;

	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'woocommerce_single_product_summary', array( $this, 'get_buy_x_get_y_render' ), 30 );
		add_action( 'glozin_buy_x_get_y_elementor', array( $this, 'get_buy_x_get_y_render' ) );

		// Add to cart
		add_action( 'wp_loaded', array( $this, 'add_to_cart_action' ), 20 );
		add_action( 'template_redirect', array( $this, 'add_to_cart' ) );

		// Add to cart ajax
		add_action( 'wc_ajax_glozin_buy_x_get_y_add_to_cart_ajax', array( $this, 'add_to_cart_action' ) );

		// Cart item data
		add_filter( 'woocommerce_add_cart_item_data', [ $this, 'add_cart_item_data' ], 10, 2 );

		// Cart contents
		add_action( 'woocommerce_before_mini_cart_contents', [ $this, 'before_mini_cart_contents' ], 10 );
		add_action( 'woocommerce_before_calculate_totals', [ $this, 'before_calculate_totals' ], 9999 );

		// Update cart quantity
		add_action( 'glozin_update_cart_item', [ $this, 'cart_item_quantity' ], 10, 2 );
		add_filter( 'woocommerce_stock_amount_cart_item', [ $this, 'wc_cart_item_quantity' ], 10, 2 );
		add_filter( 'woocommerce_update_cart_validation', [ $this, 'wc_update_cart_item_quantity' ], 10, 4 );

		// Cart remove
		add_action( 'woocommerce_cart_item_removed', [ $this, 'cart_item_removed' ], 10, 2 );

		// Restore remove
		add_action( 'woocommerce_cart_item_restored', [ $this, 'cart_item_restored' ], 10, 2 );

		// Change mini cart item
		add_filter( 'woocommerce_cart_item_quantity', [ $this, 'change_woocommerce_cart_item_quantity' ], 10, 3 );
		add_filter( 'woocommerce_widget_cart_item_quantity', [ $this, 'change_woocommerce_widget_cart_item_quantity' ], 11, 3 );

		add_filter( 'woocommerce_cart_item_price', [ $this, 'change_cart_item_price' ], 10, 3 );
		add_filter( 'woocommerce_get_item_data', [ $this, 'get_item_data' ], 10, 2 );
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
			wp_enqueue_style( 'glozin-buy-x-get-y', GLOZIN_ADDONS_URL . 'modules/buy-x-get-y/assets/buy-x-get-y.css', array(), '1.0.0' );
			wp_enqueue_script('glozin-buy-x-get-y', GLOZIN_ADDONS_URL . 'modules/buy-x-get-y/assets/buy-x-get-y.js',  array('jquery'), '1.0.0' );

			$glozin_data = array(
				'currency_pos'       => get_option( 'woocommerce_currency_pos' ),
				'currency_symbol'    => function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '',
				'thousand_sep'       => function_exists( 'wc_get_price_thousand_separator' ) ? wc_get_price_thousand_separator() : '',
				'decimal_sep'        => function_exists( 'wc_get_price_decimal_separator' ) ? wc_get_price_decimal_separator() : '',
				'price_decimals'     => function_exists( 'wc_get_price_decimals' ) ? wc_get_price_decimals() : '',
				'add_to_cart_notice' => esc_html__( 'Successfully added to your cart', 'glozin-addons' ),
				'view_cart_text'     => esc_html__( 'View cart', 'glozin-addons' ),
				'view_cart_link'     => esc_url( wc_get_cart_url() ),
			);

			wp_localize_script(
				'glozin-buy-x-get-y', 'glozinBXGY', $glozin_data
			);
		}
	}

	public function get_buy_x_get_y_render() {
		$post_id = self::get_post_id();

		if( empty( $post_id ) ) {
			return;
		}

		$display  = get_post_meta( $post_id, '_buy_x_get_y_display', true );
		$products = get_post_meta( $post_id, '_product_buy_x_get_y_ids', true );

		// Move current product to first in array
		if( is_array( $products ) ) {
			$key      = array_search( get_the_ID(), $products );
			unset($products[$key]);
			$products = array_values($products);
			array_unshift($products, get_the_ID());
		}

		$products_qty = get_post_meta( $post_id, '_products_buy_x_get_y_product_qty', true );
		$products_qty = ! empty( $products_qty ) ? $products_qty : [];
		$cat_qty    = get_post_meta( $post_id, '_cat_buy_x_get_y_product_qty', true );
		$cat_qty	= ! empty( $cat_qty ) ? $cat_qty : 1;
		$items      = get_post_meta( $post_id, 'glozin_buy_x_get_y_items', true );
		$total_main = 0;
		$total_sub  = 0;
		$main_products = [];

		if( $display == 'products' && ! empty( $products ) && ! wc_get_product( $products[0] )->is_type( 'variable' ) && ! wc_get_product( $products[0] )->is_type( 'simple' ) ) {
			return;
		}

		if( $display == 'categories' && ! wc_get_product( get_the_ID() )->is_type( 'variable' ) && ! wc_get_product( get_the_ID() )->is_type( 'simple' ) ) {
			return;
		}

		?>
			<div class="glozin-buy-x-get-y mt-25 mb-15 rounded-5 border">
				<div class="glozin-buy-x-get-y__heading text-dark fs-18 fw-semibold mb-20"><?php echo get_the_title( $post_id ); ?></div>
				<div class="glozin-buy-x-get-y__products main-products position-relative d-flex flex-column gap-15">
					<?php if( $display == 'products' ) : ?>
						<?php foreach( $products as $key => $id ) : $_product = wc_get_product( $id ); ?>
							<div class="glozin-buy-x-get-y__product position-relative d-flex gap-15 <?php echo $key == 0 ? 'main' : 'sub'; ?> <?php echo esc_attr( $_product->get_type() ); ?>">
								<div class="product-thumbnail position-relative">
									<?php echo wp_get_attachment_image( $_product->get_image_id(), 'woocommerce_thumbnail' ); ?>
								</div>
								<div class="glozin-buy-x-get-y__product-summary d-flex flex-column gap-10 flex-1">
									<div class="d-flex flex-column flex-xl-row-reverse align-items-center justify-content-between gap-5 gap-xl-10">
										<div class="woocommerce-badge onsale gap-3 flex-shrink-0"><?php printf( '%s %s', __( 'Buy', 'glozin-addons'), ! empty( $products_qty[$id] ) ? $products_qty[$id] : 1 ); ?></div>
										<h2 class="woocommerce-loop-product__title m-0 text-dark fs-15 fw-semibold">
											<a class="woocommerce-LoopProduct-link woocommerce-loop-product__link" href="<?php echo esc_url( $_product->get_permalink() ) ?>">
												<?php echo esc_html( $_product->get_name() ); ?>
											</a>
										</h2>
									</div>
									<?php if( $_product->is_type( 'variable' ) ) : ?>
										<div class="attributes d-flex align-items-center gap-10">
											<form class="variations_form cart product-select__variation" action="<?php echo esc_url( $_product->get_permalink() ); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $_product->get_id() ); ?>">
												<?php echo \Glozin\Addons\Modules\Buy_X_Get_Y\Variation_Select::instance()->render( $_product, 1 ); ?>
											</form>
											<div class="qty fs-12 text-dark fw-medium" data-qty="<?php echo esc_attr( ! empty( $products_qty[$id] ) ? $products_qty[$id] : 1 ); ?>">
												<?php echo sprintf( __( 'Qty: %s', 'glozin-addons' ), ! empty( $products_qty[$id] ) ? $products_qty[$id] : 1 ); ?>
											</div>
										</div>
									<?php else: ?>
										<div class="qty fs-12 text-dark fw-medium" data-qty="<?php echo esc_attr( ! empty( $products_qty[$id] ) ? $products_qty[$id] : 1 ); ?>">
											<?php echo sprintf( __( 'Qty: %s', 'glozin-addons' ), ! empty( $products_qty[$id] ) ? $products_qty[$id] : 1 ); ?>
										</div>
									<?php endif; ?>
									<div class="price-wrapper d-flex align-items-center gap-10">
										<div class="price-label text-dark fw-semibold"><?php esc_html_e( 'Total:', 'glozin-addons' ); ?></div>
										<div class="price" data-price="<?php echo floatval( $_product->get_price() ); ?>"><?php echo wp_kses_post( $_product->get_price_html() ); $total_main += floatval( $_product->get_price() ); ?></div>
									</div>
								</div>
								<?php
									if( $key == 0 ) {
										$main_products[] = array(
											'product_id' => $id,
											'qty' => ! empty( $products_qty[$id] ) ? $products_qty[$id] : 1,
										);
									}
								?>
							</div>
						<?php endforeach; ?>
					<?php else: ?>
						<?php $_product = wc_get_product( get_the_ID() ); ?>
						<div class="glozin-buy-x-get-y__product position-relative d-flex gap-15 main <?php echo esc_attr( $_product->get_type() ); ?>">
							
							<div class="product-thumbnail position-relative">
								<?php echo wp_get_attachment_image( $_product->get_image_id(), 'woocommerce_thumbnail' ); ?>
							</div>
							<div class="glozin-buy-x-get-y__product-summary d-flex flex-column gap-10 flex-1">
								<div class="d-flex flex-column flex-xl-row-reverse align-items-center justify-content-between gap-5 gap-xl-10">
									<div class="woocommerce-badge onsale gap-3 flex-shrink-0"><?php printf( '%s %s', __( 'Buy', 'glozin-addons'), $cat_qty ); ?></div>
									<h2 class="woocommerce-loop-product__title m-0 text-dark fs-15 fw-semibold">
										<a class="woocommerce-LoopProduct-link woocommerce-loop-product__link" href="<?php echo esc_url( $_product->get_permalink() ) ?>">
											<?php echo esc_html( $_product->get_name() ); ?>
										</a>
									</h2>
								</div>
								<?php if( $_product->is_type( 'variable' ) ) : ?>
									<div class="attributes d-flex align-items-center gap-10">
										<form class="variations_form cart product-select__variation" action="<?php echo esc_url( $_product->get_permalink() ); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $_product->get_id() ); ?>">
											<?php echo \Glozin\Addons\Modules\Buy_X_Get_Y\Variation_Select::instance()->render( $_product, 1 ); ?>
										</form>
										<div class="qty fs-12 text-dark fw-medium" data-qty="<?php echo esc_attr( $cat_qty ); ?>">
											<?php echo sprintf( __( 'Qty: %s', 'glozin-addons' ), $cat_qty ); ?>
										</div>
									</div>
								<?php else: ?>
									<div class="qty fs-12 text-dark fw-medium" data-qty="<?php echo esc_attr( $cat_qty ); ?>">
										<?php echo sprintf( __( 'Qty: %s', 'glozin-addons' ), $cat_qty ); ?>
									</div>
								<?php endif; ?>
								<div class="price-wrapper d-flex align-items-center gap-10">
									<div class="price-label text-dark fw-semibold"><?php esc_html_e( 'Total:', 'glozin-addons' ); ?></div>
									<div class="price" data-price="<?php echo floatval( $_product->get_price() ); ?>"><?php echo wp_kses_post( $_product->get_price_html() ); $total_main += floatval( $_product->get_price() ); ?></div>
								</div>
							</div>
						</div>
						<?php
							$main_products[] = array(
								'product_id' => get_the_ID(),
								'qty' => $cat_qty,
							);
						?>
					<?php endif; ?>
				</div>
				<div class="glozin-buy-x-get-y__line d-flex align-items-center justify-content-center position-relative mt-15 mb-15">
					<span class="plus-icon position-relative d-flex align-items-center justify-content-center rounded-100"></span>
				</div>
				<div class="glozin-buy-x-get-y__products sub-products position-relative d-flex flex-column gap-15">
					<?php foreach( $items as $item ) : ?>
						<?php
							$discount_text = '';
							if( empty( $item ) || empty( $item['product_id'] )) {
								continue;
							}
							$_product = wc_get_product( $item['product_id'] );
							if( empty( $_product )) {
								continue;
							}
							$qty = ! empty( $item['product_qty'] ) && intval( $item['product_qty'] ) > 0 ? $item['product_qty'] : 1;
							$discount = ! empty( $item['product_discount'] ) && floatval( $item['product_discount'] ) > 0 ? floatval( $item['product_discount'] ) : 0;
							$discount_type = ! empty( $item['product_discount_type'] ) ? $item['product_discount_type'] : '';
							$price = floatval( $_product->get_price() );
							$old_price = 0;

							if( $discount > 0 && $discount_type && $discount_type !== 'free' ) {
								$old_price = $price;
								if( $discount_type == 'fixed_price' ) {
									if( $discount < $price ) {
										$price -= $discount;

										$discount_text = sprintf( '%s %s %s %s', __( 'Get', 'glozin-addons'), $qty, __( 'Off', 'glozin-addons' ), wc_price( $discount ) );
									}
								} else {
									if( $discount < 100 ) {
										$price -= ( $price * $discount / 100 );

										$discount_text = sprintf( '%s %s %s %s', __( 'Get', 'glozin-addons'), $qty, __( 'Off', 'glozin-addons' ), $discount . '%' );
									}
								}
							}

							if( $discount_type == 'free' ) {
								$old_price = $price;
								$price = 0;
								$discount_text = esc_html__( 'Free', 'glozin-addons' );
							}
						?>
						<div class="glozin-buy-x-get-y__product position-relative d-flex gap-15 sub <?php echo esc_attr( $_product->get_type() ); ?>">
							<div class="product-thumbnail position-relative">
								<?php echo wp_get_attachment_image( $_product->get_image_id(), 'woocommerce_thumbnail' ); ?>
							</div>
							<div class="glozin-buy-x-get-y__product-summary d-flex flex-column gap-10 flex-1">
								<?php if( ! empty( $discount_text ) ) : ?>
									<div class="d-flex flex-column flex-xl-row-reverse align-items-center justify-content-between gap-5 gap-xl-10">
										<div class="woocommerce-badge onsale gap-3 flex-shrink-0"><?php echo wp_kses_post( $discount_text ); ?></div>
								<?php endif; ?>
									<h3 class="woocommerce-loop-product__title m-0 text-dark fs-15 fw-semibold">
										<a class="woocommerce-LoopProduct-link woocommerce-loop-product__link" href="<?php echo esc_url( $_product->get_permalink() ) ?>">
											<?php echo esc_html( $_product->get_name() ); ?>
										</a>
									</h3>
								<?php if( ! empty( $discount_text ) ) : ?>
									</div>
								<?php endif; ?>
								<?php if( $_product->is_type( 'variable' ) ) : ?>
									<div class="attributes d-flex align-items-center gap-10">
										<form class="variations_form cart product-select__variation" action="<?php echo esc_url( $_product->get_permalink() ); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $_product->get_id() ); ?>">
											<?php echo \Glozin\Addons\Modules\Buy_X_Get_Y\Variation_Select::instance()->render( $_product, $qty, $discount, $discount_type ); ?>
										</form>
										<div class="qty fs-12 text-dark fw-medium" data-qty="<?php echo esc_attr( $qty ); ?>">
											<?php printf( __('Qty: %s', 'glozin-addons'), $qty ); ?>
										</div>
									</div>
								<?php else : ?>
									<div class="qty fs-12 text-dark fw-medium" data-qty="<?php echo esc_attr( $qty ); ?>">
										<?php printf( __('Qty: %s', 'glozin-addons'), $qty ); ?>
									</div>
								<?php endif; ?>
								<div class="price-wrapper d-flex align-items-center gap-10">
									<div class="price-label text-dark fw-semibold"><?php esc_html_e( 'Total:', 'glozin-addons' ); ?></div>
									<div class="price" data-price="<?php echo wc_format_decimal( $price, wc_get_price_decimals() ); ?>">
										<?php echo $old_price > 0 ? '<del>'. wc_price( wc_format_decimal( $old_price, wc_get_price_decimals() ) ) .'</del><ins>'. wc_price( wc_format_decimal( $price, wc_get_price_decimals() ) ) .'</ins>' : wc_price( wc_format_decimal( $price, wc_get_price_decimals() ) ); $total_sub += ( $price * $qty ); ?>
									</div>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="glozin-buy-x-get-y__button mt-20">
					<form class="buy-x-get-y__form cart" action="<?php echo esc_url( get_the_permalink() ); ?>" method="post" enctype="multipart/form-data" data-id="<?php echo intval( $post_id ); ?>">
						<input type="hidden" name="glozin_buy_x_get_y_main_product" value="">
						<input type="hidden" name="glozin_buy_x_get_y_sub_product" value="">
						<input type="hidden" name="glozin_buy_x_get_y_id" value="<?php echo intval( $post_id ); ?>">
						<button type="submit" name="glozin_buy_x_get_y_add_to_cart" value="<?php echo esc_attr( json_encode( $main_products ) ); ?>" class="gz-button glozin-buy-x-get-y-add-to-cart disabled"><?php esc_html_e( 'Grab This Deals', 'glozin-addons' ); ?></button>
					</form>
				</div>
			</div>
		<?php
	}

	/**
	 * Get post buy x get y id
     *
	 * @since 1.0.0
	 *
	 * @return intval
	 */
	public function get_post_id() {
		// Check if the post ID is already set, if so, return it immediately
		if (!empty(self::$post_id)) {
			return self::$post_id;
		}

		// Get the product categories of the current product
		$terms = get_the_terms(get_the_ID(), 'product_cat');
		$terms = wp_list_pluck($terms, 'term_id');

		// Convert the term IDs array into a comma-separated string
		$term_ids_sql = !empty($terms) ? implode(',', array_map('intval', $terms)) : '';

		// Build the combined SQL query
		global $wpdb;

		$query = "
			SELECT p.ID
			FROM {$wpdb->posts} AS p
			LEFT JOIN {$wpdb->postmeta} AS pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_buy_x_get_y_status'
			LEFT JOIN {$wpdb->postmeta} AS pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_buy_x_get_y_display'
			LEFT JOIN {$wpdb->postmeta} AS pm3 ON p.ID = pm3.post_id AND pm3.meta_key = '_product_buy_x_get_y_ids'
			LEFT JOIN {$wpdb->postmeta} AS pm4 ON p.ID = pm4.post_id AND pm4.meta_key = '_product_buy_x_get_y_exclude_ids'
			LEFT JOIN {$wpdb->term_relationships} AS tr ON p.ID = tr.object_id
			LEFT JOIN {$wpdb->term_taxonomy} AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'product_cat'
			WHERE p.post_type = %s
			AND p.post_status = 'publish'
			AND pm1.meta_value = 'yes'
			AND (
				(pm2.meta_value = 'products' AND pm3.meta_value LIKE %s)
				OR
				(
					pm2.meta_value = 'categories'
					AND tt.term_id IN ($term_ids_sql)
					AND pm4.meta_value NOT LIKE %s
				)
			)
			ORDER BY FIELD(pm2.meta_value, 'products', 'categories') ASC, p.menu_order ASC, p.post_date DESC
			LIMIT 1
		";

		// Prepare the query parameters for post type and product ID
		$post_type = self::POST_TYPE;
		$product_id = get_the_ID(); // Get the current product's ID
		$like_value = "%$product_id%"; // LIKE pattern for '_product_buy_x_get_y_ids'

		// Prepare the query with the necessary parameters
		$query = $wpdb->prepare($query, $post_type, $like_value, $like_value);

		// Execute the query and get the result
		$results = $wpdb->get_var($query);

		// Set the post ID if a result is found, otherwise keep the previous value
		self::$post_id = $results ? $results : self::$post_id;

		// Return the post ID
		return self::$post_id;
	}

	/**
	 * Add to cart
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_to_cart_action() {
		if ( empty( $_REQUEST['glozin_buy_x_get_y_add_to_cart'] ) ) {
			return;
		}

		wc_nocache_headers();

		$post_id	= isset( $_REQUEST['glozin_buy_x_get_y_id'] ) ? $_REQUEST['glozin_buy_x_get_y_id'] : 0;
		if( empty( $post_id ) ) {
			return;
		}

		$display     = get_post_meta( $post_id, '_buy_x_get_y_display', true );
		$quantity    = 1;
		$product_ids = json_decode( stripslashes( $_REQUEST['glozin_buy_x_get_y_add_to_cart'] ), true );
		$product_id  = $product_ids[0]['product_id'];

		if( $display == 'products' ) {
			$products_qty = get_post_meta( $post_id, '_products_buy_x_get_y_product_qty', true );
			$quantity 	= ! empty( $products_qty[$product_id] ) ? $products_qty[$product_id]: $quantity;
		} else {
			$cat_qty  	= get_post_meta( $post_id, '_cat_buy_x_get_y_product_qty', true );
			$quantity 	= ! empty( $cat_qty ) ? $cat_qty: $quantity;
		}

		$was_added_to_cart = false;
		$variation_id      = 0;
		$variations        = array();
		$adding_to_cart    = wc_get_product( $product_id );

		if ( ! $adding_to_cart ) {
			return;
		}

		if ( $adding_to_cart->is_type( 'variable' ) ) {
			$variations   = self::json_decode_to_array( $_REQUEST['glozin_buy_x_get_y_main_product'] );
			$variation_id = $variations[$product_id]['variation_id'];
			$variations   = $variations[$product_id]['attributes'];
		}

		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations );

		if ( $passed_validation && false !==  self::$cart_key = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations ) ) {
			wc_add_to_cart_message( array( $product_id => $quantity ), true );
			$was_added_to_cart = true;
		}

		// If we added the product to the cart we can now optionally do a redirect.
		if ( $was_added_to_cart && 0 === wc_notice_count( 'error' ) ) {
			if( ! empty( $_REQUEST[ 'glozin-bxgy-add-to-cart-ajax' ] ) ) {
				self::add_to_cart(self::$cart_key);
				self::get_refreshed_fragments();
			}

			if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
				wp_safe_redirect( wc_get_cart_url() );
				exit;
			}
		} else {
			if( ! empty( $_REQUEST[ 'glozin-bxgy-add-to-cart-ajax' ] ) ) {
				$notice = WC()->session->get( 'wc_notices', array() )['error'][0]['notice'];
				wc_clear_notices();
				wp_send_json(
					array( 'error' =>  $notice )
				);
			}
		}
	}

	/**
	 * Get a refreshed cart fragment, including the mini cart HTML.
	 */
	public static function get_refreshed_fragments() {
		ob_start();

		woocommerce_mini_cart();

		$mini_cart = ob_get_clean();

		$data = array(
			'fragments' => apply_filters(
				'woocommerce_add_to_cart_fragments',
				array(
					'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>',
				)
			),
			'cart_hash' => WC()->cart->get_cart_hash(),
		);
		wc_clear_notices();
		wp_send_json( $data );
	}

	public function add_cart_item_data( $cart_item_data, $product_id ) {
		if ( ! empty( $_REQUEST['glozin_buy_x_get_y_add_to_cart'] ) ) {
			$post_id = ! empty( $_REQUEST['glozin_buy_x_get_y_id'] ) ? intval( $_REQUEST['glozin_buy_x_get_y_id'] ) : 0;

			if ( ! empty( $post_id ) ) {
				$display  = get_post_meta( $post_id, '_buy_x_get_y_display', true );
				$quantity = 1;

				if( $display == 'products' ) {
					$products_qty = get_post_meta( $post_id, '_products_buy_x_get_y_product_qty', true );
					$quantity 	= ! empty( $products_qty[$product_id] ) ? $products_qty[$product_id]: $quantity;
				} else {
					$cat_qty  = get_post_meta( $post_id, '_cat_buy_x_get_y_product_qty', true );
					$quantity = ! empty( $cat_qty ) ? $cat_qty: $quantity;
				}

				$cart_item_data['glozin_bxgy_parent_id'] = $product_id;
				$cart_item_data['glozin_bxgy_id'] = $post_id;
				$cart_item_data['glozin_bxgy_quantity'] = $quantity;
			}
		}

		return $cart_item_data;
	}

	public function add_to_cart( $primary_cart_key ) {
		$cart_item_key = ! empty( self::$cart_key ) ? self::$cart_key : '';
		$cart_item_key = ! empty( $primary_cart_key ) ? $primary_cart_key : $cart_item_key;

		if ( ! empty( $_REQUEST['glozin_buy_x_get_y_add_to_cart'] ) && ! empty( $cart_item_key ) ) {
			$product_ids   = json_decode( stripslashes( $_REQUEST['glozin_buy_x_get_y_add_to_cart'] ), true );
			$product_id    = $product_ids[0]['product_id'];
			$post_id       = ! empty( $_REQUEST['glozin_buy_x_get_y_id'] ) ? intval( $_REQUEST['glozin_buy_x_get_y_id'] ) : 0;
			$main_products = ! empty( $_REQUEST['glozin_buy_x_get_y_main_product'] ) ? self::json_decode_to_array( $_REQUEST['glozin_buy_x_get_y_main_product'] ) : [];
			$sub_products  = ! empty( $_REQUEST['glozin_buy_x_get_y_sub_product'] ) ? self::json_decode_to_array( $_REQUEST['glozin_buy_x_get_y_sub_product'] ) : [];

			if ( ! empty( $post_id ) ) {
				// add child products
				self::add_to_cart_items( $post_id, $main_products, $sub_products, $cart_item_key, $product_id );
			}
		}
	}

	public function add_to_cart_items( $post_id, $main_products, $sub_products, $cart_item_key, $product_id ) {
		$check_out_of_stock = false;
		$display      = get_post_meta( $post_id, '_buy_x_get_y_display', true );
		$products     = get_post_meta( $post_id, '_product_buy_x_get_y_ids', true );
		$products_qty = get_post_meta( $post_id, '_products_buy_x_get_y_product_qty', true );

		// add main child products
		if( $display == 'products' ) {
			foreach( $products as $item_id ) {
				if( intval( $item_id ) == intval( $product_id ) ) {
					continue;
				}

				if( empty( $item_id ) ) {
					continue;
				}

				$_product = wc_get_product( $item_id );

				if( ! empty( $sub_products[$item_id]['variation_id'] ) ) {
					$_product = wc_get_product( $main_products[$item_id]['variation_id'] );
				}

				if ( $_product && $_product->is_in_stock() && $_product->is_purchasable() && ( 'trash' !== $_product->get_status() ) ) {
					$_product = wc_get_product( $item_id );
					$item_qty 	       = ! empty( $products_qty[$item_id] ) ? $products_qty[$item_id] : 1;
					$item_variation_id = 0;
					$item_variations   = array();
					$cart_item_data    = array( 'glozin_bxgy_main_id' => $item_id, 'glozin_bxgy_parent_key' => $cart_item_key );

					if ( $_product->is_type( 'variable' ) && ! empty( $main_products[$item_id] ) ) {
						$item_variation_id = $main_products[$item_id]['variation_id'];
						$item_variations   = $main_products[$item_id]['attributes'];
					}

					// add to cart
					$item_key = WC()->cart->add_to_cart( $item_id, $item_qty, $item_variation_id, $item_variations, $cart_item_data );

					if ( $item_key ) {
						WC()->cart->cart_contents[ $item_key ]['glozin_bxgy_parent_id']   = $product_id;
						WC()->cart->cart_contents[ $item_key ]['glozin_bxgy_key']         = $item_key;
						WC()->cart->cart_contents[ $item_key ]['glozin_bxgy_parent_key']  = $cart_item_key;
						WC()->cart->cart_contents[ $item_key ]['glozin_bxgy_quantity']    = $item_qty;
						WC()->cart->cart_contents[ $cart_item_key ]['glozin_bxgy_keys'][] = $item_key;
					} else {
						$check_out_of_stock = true;
						return;
					}
				}
			}
		}

		if( $check_out_of_stock ) {
			return;
		}

		// add sub child products
		$items = get_post_meta( $post_id, 'glozin_buy_x_get_y_items', true );
		foreach( $items as $item ) {
			if( empty( $item['product_id'] ) ) {
				continue;
			}

			$_product = wc_get_product( $item['product_id'] );

			if( ! empty( $sub_products[$item['product_id']]['variation_id'] ) ) {
				$_product = wc_get_product( $sub_products[$item['product_id']]['variation_id'] );
			}

			if ( $_product && $_product->is_in_stock() && $_product->is_purchasable() && ( 'trash' !== $_product->get_status() ) ) {
				$_product = wc_get_product( $item['product_id'] );
				$item_qty = ! empty( $item['product_qty'] ) && intval( $item['product_qty'] ) > 0 ? $item['product_qty'] : 1;
				$discount = ! empty( $item['product_discount'] ) && floatval( $item['product_discount'] ) > 0 ? floatval( $item['product_discount'] ) : 0;
				$discount_type = ! empty( $item['product_discount_type'] ) ? $item['product_discount_type'] : '';
				$price = floatval( $_product->get_price() );
				$old_price = 0;

				$item_variation_id = 0;
				$item_variations   = array();
				$cart_item_data = array( 'glozin_bxgy_sub_id' => $item['product_id'], 'glozin_bxgy_parent_key' => $cart_item_key );

				if( $_product->is_type( 'variable' ) && ! empty( $sub_products[$item['product_id']]  ) ) {
					$item_variation_id = $sub_products[$item['product_id']]['variation_id'];
					$item_variations   = $sub_products[$item['product_id']]['attributes'];
					$variation_product = wc_get_product( $item_variation_id );
					$price             = floatval( $variation_product->get_price() );
				}

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
				}

				if( $discount_type == 'free' ) {
					$old_price = $price;
					$price = 0;
				}

				$cart_item_data['glozin_bxgy_price'] = floatval( $price );
				if( ! empty( $old_price ) ) {
					$cart_item_data['glozin_bxgy_old_price'] = floatval( $old_price );
				}

				// add to cart
				$item_key = WC()->cart->add_to_cart( $item['product_id'], $item_qty, $item_variation_id, $item_variations, $cart_item_data );

				if ( $item_key ) {
					WC()->cart->cart_contents[ $item_key ]['glozin_bxgy_post_id']     = $post_id;
					WC()->cart->cart_contents[ $item_key ]['glozin_bxgy_parent_id']   = $product_id;
					WC()->cart->cart_contents[ $item_key ]['glozin_bxgy_key']         = $item_key;
					WC()->cart->cart_contents[ $item_key ]['glozin_bxgy_parent_key']  = $cart_item_key;
					WC()->cart->cart_contents[ $item_key ]['glozin_bxgy_quantity']  = $item_qty;
					WC()->cart->cart_contents[ $cart_item_key ]['glozin_bxgy_keys'][] = $item_key;
					WC()->cart->cart_contents[ $item_key ]['data']->set_price( floatval( $price ) );
				}
			}
		}
	}

	public function before_mini_cart_contents() {
		WC()->cart->calculate_totals();
	}

	public function before_calculate_totals( $cart_object ) {
		if ( ! defined( 'DOING_AJAX' ) && is_admin() ) {
			// This is necessary for WC 3.0+
			return;
		}

		$cart_contents = $cart_object->cart_contents;
		foreach ( $cart_contents as $cart_item_key => $cart_item ) {
			if( ! empty( $cart_item['glozin_bxgy_post_id'] ) ) {
				$post_id = $cart_item['glozin_bxgy_post_id'];
				$items = get_post_meta( $post_id, 'glozin_buy_x_get_y_items', true );

				if ( $cart_item['variation_id'] > 0 ) {
					$item_product = wc_get_product( $cart_item['variation_id'] );
					$item_product_id = $item_product->get_parent_id();
				} else {
					$item_product = wc_get_product( $cart_item['product_id'] );
					$item_product_id = $item_product->get_id();
				}

				foreach( $items as $item ) {
					if( empty($item['product_id'] ) ) {
						continue;
					}
					if( intval( $item['product_id'] ) !== intval( $item_product_id ) ) {
						continue;
					}

					$discount = ! empty( $item['product_discount'] ) && floatval( $item['product_discount'] ) > 0 ? floatval( $item['product_discount'] ) : 0;
					$discount_type = ! empty( $item['product_discount_type'] ) ? $item['product_discount_type'] : '';
					$price = floatval( $item_product->get_price() );

					if( ! empty( $discount_type ) ) {
						if( $discount > 0 && $discount_type !== 'free' ) {
							if( $discount_type == 'fixed_price' ) {
								if( $discount < $price ) {
									$price -= $discount;
								}
							} else {
								if( $discount < 100 ) {
									$price -= ( $price * $discount / 100 );
								}
							}
						}

						if( $discount_type == 'free' ) {
							$price = 0;
						}
					}

					$cart_item['data']->set_price( $price );
				}
			}
		}
	}

	public function cart_item_quantity( $cart_item_key, $quantity ) {
		$cart_item_length = isset( $_POST['cart_item_length'] ) ? $_POST['cart_item_length'] : 0;

		if( ! empty( WC()->cart->cart_contents[ $cart_item_key ]['glozin_bxgy_parent_key'] ) || ! empty( WC()->cart->cart_contents[ $cart_item_key ]['glozin_bxgy_keys'] ) ) {
			$check  = false;
			$number =  1;
			$parent_key   = ! empty( WC()->cart->cart_contents[ $cart_item_key ]['glozin_bxgy_keys'] ) ? $cart_item_key : WC()->cart->cart_contents[ $cart_item_key ]['glozin_bxgy_parent_key'];
			$keys         = WC()->cart->cart_contents[ $parent_key ]['glozin_bxgy_keys'];
			$ori_quantity = ! empty( WC()->cart->cart_contents[ $cart_item_key ]['glozin_bxgy_keys'] ) ? WC()->cart->cart_contents[ $parent_key ]['glozin_bxgy_quantity'] : WC()->cart->cart_contents[ $cart_item_key ]['glozin_bxgy_quantity'];
			$_quantity    = ! empty( WC()->cart->cart_contents[ $cart_item_key ]['glozin_bxgy_keys'] ) ? WC()->cart->cart_contents[ $parent_key ]['quantity'] : WC()->cart->cart_contents[ $cart_item_key ]['quantity'];

			if( $quantity > $_quantity ) {
				$number = $quantity % $ori_quantity == 0 ? $quantity / $ori_quantity : ( $_quantity / $ori_quantity ) + 1;
			}

			if( $quantity < $_quantity ) {
				$number = $quantity % $ori_quantity == 0 ? $quantity / $ori_quantity : ( $_quantity / $ori_quantity ) - 1;
			}

			if( ( WC()->cart->cart_contents[ $parent_key ]['glozin_bxgy_quantity'] * $number ) < 1 ) {
				$check = true;
			}

			WC()->cart->set_quantity( $parent_key, ( WC()->cart->cart_contents[ $parent_key ]['glozin_bxgy_quantity'] * $number ) );
			foreach ( $keys as $key ) {
				if( empty( WC()->cart->cart_contents[ $key ] ) ) {
					$check = true;
					continue;
				}
				WC()->cart->set_quantity( $key, ( WC()->cart->cart_contents[ $key ]['glozin_bxgy_quantity'] * $number ) );
			}

			if ( intval( $cart_item_length ) == ( count($keys) + 1 ) && $check ) {
				WC()->cart->empty_cart();
			}

			\WC_AJAX::get_refreshed_fragments();
		}
	}

	public function wc_cart_item_quantity( $quantity, $cart_item_key ) {
		if( ! empty( WC()->cart->cart_contents[ $cart_item_key ]['glozin_bxgy_keys'] ) ) {
			$number 	  =  1;
			$keys         = WC()->cart->cart_contents[ $cart_item_key ]['glozin_bxgy_keys'];
			$ori_quantity = WC()->cart->cart_contents[ $cart_item_key ]['glozin_bxgy_quantity'];
			$_quantity    = WC()->cart->cart_contents[ $cart_item_key ]['quantity'];

			if( $quantity > $_quantity ) {
				$number = $quantity % $ori_quantity == 0 ? $quantity / $ori_quantity : ( $_quantity / $ori_quantity ) + 1;
			}

			if( $quantity < $_quantity ) {
				$number = $quantity % $ori_quantity == 0 ? $quantity / $ori_quantity : ( $_quantity / $ori_quantity ) - 1;
			}

			WC()->cart->set_quantity( $cart_item_key, ( WC()->cart->cart_contents[ $cart_item_key ]['glozin_bxgy_quantity'] * $number ) );
			foreach ( $keys as $key ) {
				if( empty( WC()->cart->cart_contents[ $key ] ) ) {
					continue;
				}
				WC()->cart->set_quantity( $key, ( WC()->cart->cart_contents[ $key ]['glozin_bxgy_quantity'] * $number ) );
			}

			$quantity = WC()->cart->cart_contents[ $cart_item_key ]['glozin_bxgy_quantity'] * $number;
		}

		return $quantity;
	}

	public function wc_update_cart_item_quantity( $changed, $cart_item_key, $values, $quantity ) {
		if( $changed && ( ! empty( WC()->cart->cart_contents[ $cart_item_key ]['glozin_bxgy_keys'] ) || ! empty( WC()->cart->cart_contents[ $cart_item_key ]['glozin_bxgy_parent_key'] ) ) ) {
			$parent_key   = ! empty( WC()->cart->cart_contents[ $cart_item_key ]['glozin_bxgy_keys'] ) ? $cart_item_key : WC()->cart->cart_contents[ $cart_item_key ]['glozin_bxgy_parent_key'];

			if( $parent_key !== $cart_item_key ) {
				return false;
			}
		}

		return true;
	}

	public function cart_item_removed( $cart_item_key, $cart ) {
		if ( ! empty( $cart->removed_cart_contents[$cart_item_key]['glozin_bxgy_keys'] ) || ! empty( $cart->removed_cart_contents[ $cart_item_key ]['glozin_bxgy_parent_key'] ) ) {
			$parent_key = ! empty( $cart->removed_cart_contents[ $cart_item_key ]['glozin_bxgy_keys'] ) ? $cart_item_key: $cart->removed_cart_contents[ $cart_item_key ]['glozin_bxgy_parent_key'];
			$keys       = $cart->removed_cart_contents[ $parent_key ]['glozin_bxgy_keys'];

			if( $parent_key !== $cart_item_key ) {
				WC()->cart->remove_cart_item( $parent_key );
			}

			foreach ( $keys as $key ) {
				WC()->cart->remove_cart_item( $key );
			}
		}
	}

	public function cart_item_restored( $cart_item_key, $cart ) {
		if ( ! empty( $cart->cart_contents[ $cart_item_key ]['glozin_bxgy_keys'] ) || ! empty( $cart->cart_contents[ $cart_item_key ]['glozin_bxgy_parent_key'] ) ) {
			$parent_key = ! empty( $cart->cart_contents[ $cart_item_key ]['glozin_bxgy_keys'] ) ? $cart_item_key : $cart->cart_contents[ $cart_item_key ]['glozin_bxgy_parent_key'];
			$keys       = $cart->cart_contents[ $parent_key ]['glozin_bxgy_keys'];

			if( $parent_key !== $cart_item_key ) {
				WC()->cart->restore_cart_item( $parent_key );
			}

			foreach ( $keys as $key ) {
				WC()->cart->restore_cart_item( $key );
			}
		}
	}

	public function json_decode_to_array( $json ) {
		$args = [];
		$_jsons = json_decode( stripslashes( $json ), true );
		foreach( $_jsons as $_json ) {
			$__jsons = json_decode( stripslashes( $_json ), true );
			foreach( $__jsons as $k => $v ) {
				$args[$k] = $v;
			}
		}

		return $args;
	}

	/**
	 * Change quantity in cart page
	 *
	 * @return void
	 */
	public function change_woocommerce_cart_item_quantity( $product_quantity, $cart_item_key, $cart_item ) {
		if( ! empty( $cart_item['glozin_bxgy_parent_key'] ) ) {
			$product_quantity = '<span class="glozin-product-quantity__text">' . $cart_item['quantity'] . '</span>';
		}

		return $product_quantity;
	}

	/**
	 * Change quantity in mini cart
	 *
	 * @return void
	 */
	public function change_woocommerce_widget_cart_item_quantity( $product_quantity, $cart_item, $cart_item_key ) {
		if( ! empty( $cart_item['glozin_bxgy_keys'] ) || ! empty( $cart_item['glozin_bxgy_parent_key'] ) ) {
			if( empty( $cart_item['glozin_bxgy_keys'] ) ) {
				$product_quantity = '<span class="glozin-product-quantity__text">' . sprintf( 'Qty: %s', $cart_item['quantity'] ) . '</span>';
			}
		}

		return $product_quantity;
	}

	/**
	 * Change price to free when price = 0
	 *
	 * @return void
	 */
	public function change_cart_item_price( $product_price, $cart_item, $cart_item_key ) {
		if( floatval( $cart_item['data']->get_price() ) <= 0 ) {
			$product_price = __( 'Free', 'glozin-addons' );
		}

		return $product_price;
	}

	public function get_item_data( $item_data, $cart_item ) {
		if( ! empty( $cart_item['glozin_bxgy_keys'] ) || ! empty( $cart_item['glozin_bxgy_parent_key'] ) ) {
			$item_data[] = array(
				'key'   => 'glozin',
				'value' => esc_html__( 'Buy one get one and save', 'glozin-addons' ),
			);
		}

		return $item_data;
	}
}