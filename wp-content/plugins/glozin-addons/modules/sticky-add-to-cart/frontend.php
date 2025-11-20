<?php

namespace Glozin\Addons\Modules\Sticky_Add_To_Cart;
use Glozin\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

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
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );

		// Sticky add to cart
		add_action( 'wp_footer', array( $this, 'sticky_single_add_to_cart' ) );
	}

	public function scripts() {
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_style( 'glozin-sticky-add-to-cart', GLOZIN_ADDONS_URL . 'modules/sticky-add-to-cart/assets/sticky-add-to-cart' . $debug . '.css', array(), '1.0.1' );

		wp_enqueue_script('glozin-sticky-add-to-cart', GLOZIN_ADDONS_URL . 'modules/sticky-add-to-cart/assets/sticky-add-to-cart' . $debug . '.js', array('jquery'), '1.0.1', array('strategy' => 'defer') );
	}

	/**
	 * Check has sticky add to cart
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function has_sticky_atc() {
		global $product;

		if ( $product->is_purchasable() && $product->is_in_stock() ) {
			return true;
		} elseif ( $product->is_type( 'external' ) || $product->is_type( 'grouped' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Add sticky add to cart HTML
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function sticky_single_add_to_cart( $sticky_class ) {
		global $product;

		if ( ! $this->has_sticky_atc() ) {
			return;
		}

		$product_type    = $product->get_type();
		$sticky_class    = 'glozin-sticky-add-to-cart position-fixed bottom-0 start-0 end-0 overflow-hidden bg-light box-shadow z-n1 pe-none product-' . $product_type;

		$post_thumbnail_id =  $product->get_image_id();

		if ( $post_thumbnail_id ) {
			$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
			$thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
			$thumbnail_src     = wp_get_attachment_image_src( $post_thumbnail_id, $thumbnail_size );
			$alt_text          = trim( wp_strip_all_tags( get_post_meta( $post_thumbnail_id, '_wp_attachment_image_alt', true ) ) );
		} else {
			$thumbnail_src = wc_placeholder_img_src( 'gallery_thumbnail' );
			$alt_text      = esc_html__( 'Awaiting product image', 'glozin-addons' );
		}
		$multi_attributes = true;
		$number = apply_filters('glozin_sticky_atc_number_variations_select', 3);
		if ( $product->is_type( 'variable' ) ) {
			$attributes = $product->get_variation_attributes();
			$multi_attributes = count($attributes) > $number ? true : false;
			$sticky_class    .= $multi_attributes ? '' : ' variations-custom';
		}

		if ( $product->is_sold_individually() ) {
			$sticky_class .= ' sold-individually';
		}

		$sticky_class = apply_filters('glozin_sticky_add_to_cart_classes', $sticky_class);
		$elements = get_option('glozin_sticky_add_to_cart_elements', 'quantity_and_add_to_cart');
		if ( $elements == 'quantity_and_add_to_cart' ) {
			$sticky_class .= ' quantity-and-add-to-cart';
		} elseif ( $elements == 'quantity_and_buy_now' ) {
			$sticky_class .= ' quantity-and-buy-now';
		} elseif ( $elements == 'buy_now_and_add_to_cart' ) {
			$sticky_class .= ' buy-now-and-add-to-cart';
		}

		?>
        <section id="glozin-sticky-add-to-cart" class="<?php echo esc_attr( $sticky_class ) ?>">
				<div class="container">
					<div class="glozin-sticky-atc__content d-flex align-items-center justify-content-center py-15">
						<div class="glozin-sticky-atc__image d-none d-block-md position-relative me-15 gz-ratio">
							<img class="rounded-100" src="<?php echo esc_url( $thumbnail_src[0] ); ?>" alt="<?php echo esc_attr( $alt_text ); ?>" data-o_src="<?php echo esc_url( $thumbnail_src[0] );?>">
						</div>
						<div class="glozin-sticky-atc__product-info d-none d-block-md pe-30">
							<div class="glozin-sticky-atc__title fs-15 fw-semibold text-dark lh-normal"><a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php the_title(); ?></a></div>
							<?php if ( function_exists('wc_review_ratings_enabled') && wc_review_ratings_enabled() ) : ?>
								<div class="glozin-sticky-atc__rating">
									<?php echo wc_get_rating_html( $product->get_average_rating() ); ?>
								</div>
							<?php endif; ?>
						</div>
						<div class="glozin-sticky-atc__actions">
							<?php
								add_filter( 'glozin_show_product_featured_buttons', '__return_false' ); 
								add_filter( 'glozin_show_quantity_label', '__return_false' );
								add_filter( 'glozin_products_stock_progress_bar', '__return_false' );
								add_filter( 'glozin_is_pre_order_active', '__return_false' );
								if ( $product->is_type( 'grouped' ) ) {
									$this->add_to_cart_button('gz-add-to-cart-options');
								} elseif ( $product->is_type( 'variable' ) ) {
									if ( $multi_attributes ) {
										$this->get_default_product_variable_form();
										$this->add_to_cart_button('gz-add-to-cart-options');
									} else {
										$this->get_custom_product_variable_form();
									}
								} else {
									woocommerce_template_single_add_to_cart();
								}

								remove_filter( 'glozin_show_product_featured_buttons', '__return_false' );
								remove_filter( 'glozin_show_quantity_label', '__return_false' );
								remove_filter( 'glozin_products_stock_progress_bar', '__return_false' );
								remove_filter( 'glozin_is_pre_order_active', '__return_false' );
							?>

							<?php do_action( 'glozin_after_sticky_add_to_cart_button' ); ?>
						</div>
					</div>
                </div>
        </section><!-- .glozin-sticky-add-to-cart -->
		<?php
	}

	public function add_to_cart_button($class = '') {
		global $product;
		echo '<button type="submit" class="single_add_to_cart_button button ' . esc_attr( $class ) . '">' . esc_html( $product->single_add_to_cart_text() ) . '</button>';
	}

	/**
	 *
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function glozin_sticky_add_to_cart__percentage( $html, $percentage ) {
		$html = '-' . $percentage . '%' . '';
		return $html;
	}

	public function get_custom_product_variable_form() {
		global $product;

		?>
		<form class="cart" action="<?php echo esc_url($product->get_permalink()) ?>" method="post" enctype="multipart/form-data">
			<div class="glozin-sticky-atc__variations">
				<?php
					\Glozin\Addons\Modules\Sticky_Add_To_Cart\Variation_Select::instance()->render();
				?>
			</div>
			<div class="glozin-sticky-atc__buttons d-flex align-items-center mt-10 mt-md-0 w-100 w-auto-md">
				<?php
				woocommerce_quantity_input(
					array(
						'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
						'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
						'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
					)
				);
				$this->add_to_cart_button();
				do_action('woocommerce_after_add_to_cart_button');

				?>
				<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ) ?>">
				<input type="hidden" name="product_id" value="<?php echo esc_attr( $product->get_id() ) ?>">
			</div>
		</form>
		<?php

	}

	/**
	 *
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function get_default_product_variable_form() {
		global $product;
		$available_variations = $product->get_available_variations();

		if ( ! $available_variations ) {
			return;
		}

		if ( class_exists( 'WCBoost\VariationSwatches\Swatches' ) ) {
			remove_filter( 'woocommerce_dropdown_variation_attribute_options_html', [ \WCBoost\VariationSwatches\Swatches::instance(), 'swatches_html' ], 100, 2 );
		}

		woocommerce_template_single_add_to_cart();

	}

}