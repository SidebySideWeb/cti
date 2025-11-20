<?php

namespace Glozin\Addons\Modules\Advanced_Linked_Products;

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
	 * Has variation images
	 *
	 * @var $has_variation_images
	 */
	protected static $has_variation_images = null;


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

	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'woocommerce_single_product_summary', array( $this, 'advanced_linked_products' ), 97 );
		add_action( 'glozin_advanced_linked_products_elementor', array( $this, 'advanced_linked_products' ), 17 );
	}

	public function advanced_linked_products() {
		global $product;

		$product_ids = maybe_unserialize( get_post_meta( $product->get_id(), 'glozin_advanced_linked_product_ids', true ) );
		$product_ids = apply_filters( 'glozin_advanced_linked_product_ids', $product_ids, $product );

		if ( empty( $product_ids ) ) {
            return;
        }

		$swiper_options = array(
			'slidesPerView' => array(
				'desktop' => 3,
				'tablet' => 2,
				'mobile' => 2,
			),
			'spaceBetween' => array(
				'desktop' => 30,
				'tablet' => 30,
				'mobile' => 15,
			),
		);
	?>
		<div id="glozin-advanced-linked-products" class="advanced-linked-products">
			<h2 class="advanced-linked-products__heading h5 heading-letter-spacing mt-0"><?php esc_html_e( 'Pairs well with', 'glozin-addons' ); ?></h2>
			<div class="glozin-product-carousel glozin-swiper swiper gz-hover-zoom navigation-class-dots navigation-class--tabletdots" data-swiper=<?php echo esc_attr( json_encode( $swiper_options ) ); ?> data-desktop="<?php echo esc_attr( $swiper_options['slidesPerView']['desktop'] ); ?>" data-tablet="<?php echo esc_attr( $swiper_options['slidesPerView']['tablet'] ); ?>" data-mobile="<?php echo esc_attr( $swiper_options['slidesPerView']['mobile'] ); ?>">
				<ul class="products">
				<?php foreach ( $product_ids as $product_id ) : ?>
					<?php $linked_product = wc_get_product( $product_id );?>
					<li class="product">
						<div class="product-thumbnail position-relative rounded-product-image overflow-hidden">
							<a href="<?php echo esc_url( $linked_product->get_permalink() ); ?>" class="woocommerce-LoopProduct-link woocommerce-loop-product__link gz-ratio gz-ratio--product-image rounded-product-image gz-hover-effect product-thumbnails--fadein" aria-label="<?php echo esc_attr( $linked_product->get_name() ); ?>">
								<?php echo $linked_product->get_image();?>
								<?php $image_ids = $linked_product->get_gallery_image_ids(); ?>
								<?php 
									if ( ! empty( $image_ids ) ) {
										echo wp_get_attachment_image( $image_ids[0], 'woocommerce_thumbnail', false, array( 'class' => 'attachment-woocommerce_thumbnail size-woocommerce_thumbnail product-thumbnails--fadein-image' ) );
									}
								?>
								<div class="gz-lazy-load-image">
									<span class="gz-lazy-load-image__loader"></span>
								</div>
							</a>
							<?php do_action( 'glozin_advanced_linked_products_product_thumbnail', $linked_product ); ?>
						</div>
						<div class="product-summary mt-15 d-flex flex-column align-items-center text-center">
							<h2 class="woocommerce-loop-product__title my-0 fs-15">
								<a href="<?php echo esc_url( $linked_product->get_permalink() ); ?>" class="woocommerce-LoopProduct-link woocommerce-loop-product__link" aria-label="<?php echo esc_attr( $linked_product->get_name() ); ?>">
									<?php echo $linked_product->get_title();?>
								</a>
							</h2>
							<span class="price">
								<?php echo $linked_product->get_price_html(); ?>
							</span>
						</div>
					</li>
				<?php endforeach; ?>
				</ul>
				<div class="swiper-pagination"></div>
			</div>
		</div>
	<?php
	}
}