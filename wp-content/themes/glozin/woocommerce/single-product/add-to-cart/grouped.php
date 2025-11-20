<?php
/**
 * Grouped product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/grouped.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.2.0
 */

defined( 'ABSPATH' ) || exit;

global $product, $post;

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="cart grouped_form" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
	<div class="woocommerce-grouped-product-list group_table border px-20 py-20 rounded-10 mb-30">
		<?php
		$quantites_required      = false;
		$previous_post           = $post;
		$show_add_to_cart_button = false;

		do_action( 'woocommerce_grouped_product_list_before', $quantites_required, $product );

		foreach ( $grouped_products as $grouped_product_child ) {
			$post_object        = get_post( $grouped_product_child->get_id() );
			$quantites_required = $quantites_required || $grouped_product_child->is_purchasable();
			$post               = $post_object; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			setup_postdata( $post );

			if ( $grouped_product_child->is_in_stock() ) {
				$show_add_to_cart_button = true;
			}

			$product_image = $grouped_product_child->get_image('woocommerce_gallery_thumbnail');
			$product_name = $grouped_product_child->get_name();
			echo '<div id="product-' . esc_attr( $grouped_product_child->get_id() ) . '" class="woocommerce-grouped-product-list-item d-flex align-items-center gap-15 pb-15 mb-15 border-bottom-dashed last-0 ' . esc_attr( implode( ' ', wc_get_product_class( '', $grouped_product_child ) ) ) . '">';
				?>
				<div class="woocommerce-grouped-product-list__thumbnail">
					<?php if ( $grouped_product_child->is_visible() ) : ?>
						<a href="<?php echo esc_url( apply_filters( 'woocommerce_grouped_product_list_link', $grouped_product_child->get_permalink(), $grouped_product_child->get_id() ) ); ?>">
							<?php echo ! empty( $product_image ) ? $product_image : ''; ?>
						</a>
					<?php else : ?>
						<?php echo ! empty( $product_image ) ? $product_image : ''; ?>
					<?php endif; ?>
				</div>
				<div class="woocommerce-grouped-product-list__summary flex-1">
					<div class="woocommerce-grouped-product-list__title heading lh-normal">
						<?php if ( $grouped_product_child->is_visible() ) : ?>
							<a href="<?php echo esc_url( apply_filters( 'woocommerce_grouped_product_list_link', $grouped_product_child->get_permalink(), $grouped_product_child->get_id() ) ); ?>">
								<?php echo ! empty( $product_name ) ? $product_name : ''; ?>
							</a>
						<?php else : ?>
							<?php echo ! empty( $product_name ) ? $product_name : ''; ?>
						<?php endif; ?>
					</div>
					<div class="woocommerce-grouped-product-list__quantity d-flex flex-wrap align-items-center gap-5 mt-10" data-grouped_product_id="<?php echo esc_attr( $grouped_product_child->get_id() ); ?>">
					<?php
						if ( ! $grouped_product_child->is_purchasable() || $grouped_product_child->has_options() || ! $grouped_product_child->is_in_stock() ) {
							if( $grouped_product_child->is_type( 'variable' ) && class_exists( 'Glozin\Addons\Modules\Base\Variation_Select' ) ) {
								echo \Glozin\Addons\Modules\Base\Variation_Select::instance()->render($grouped_product_child);

								woocommerce_quantity_input(
									array(
										'input_name'  => 'quantity[' . $grouped_product_child->get_id() . ']',
										'input_value' => isset( $_POST['quantity'][ $grouped_product_child->get_id() ] ) ? wc_stock_amount( wc_clean( wp_unslash( $_POST['quantity'][ $grouped_product_child->get_id() ] ) ) ) : 1, // phpcs:ignore WordPress.Security.NonceVerification.Missing
										'min_value'   => apply_filters( 'woocommerce_quantity_input_min', 0, $grouped_product_child ),
										'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $grouped_product_child->get_max_purchase_quantity(), $grouped_product_child ),
										'placeholder' => '0',
										'classes_style' => 'quantity-outline'
									)
								);
							} else {
								woocommerce_template_loop_add_to_cart();
							}
						} elseif ( $grouped_product_child->is_sold_individually() ) {
							echo '<input type="checkbox" name="' . esc_attr( 'quantity[' . $grouped_product_child->get_id() . ']' ) . '" value="1" class="wc-grouped-product-add-to-cart-checkbox" id="' . esc_attr( 'quantity-' . $grouped_product_child->get_id() ) . '" />';
							echo '<label for="' . esc_attr( 'quantity-' . $grouped_product_child->get_id() ) . '" class="screen-reader-text">' . esc_html__( 'Buy one of this item', 'glozin' ) . '</label>';
						} else {
							woocommerce_quantity_input(
								array(
									'input_name'  => 'quantity[' . $grouped_product_child->get_id() . ']',
									'input_value' => isset( $_POST['quantity'][ $grouped_product_child->get_id() ] ) ? wc_stock_amount( wc_clean( wp_unslash( $_POST['quantity'][ $grouped_product_child->get_id() ] ) ) ) : 1, // phpcs:ignore WordPress.Security.NonceVerification.Missing
									'min_value'   => apply_filters( 'woocommerce_quantity_input_min', 0, $grouped_product_child ),
									'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $grouped_product_child->get_max_purchase_quantity(), $grouped_product_child ),
									'placeholder' => '0',
									'classes_style' => 'quantity-outline'
								)
							);
						}
					?>
					</div>
					<div class="woocommerce-grouped-product-list-item__price mt-14">
						<?php echo '<p class="price">' . $grouped_product_child->get_price_html() . '</p>'; ?>
					</div>
				</div>
				<?php
			echo '</div>';
		}
		$post = $previous_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		setup_postdata( $post );

		do_action( 'woocommerce_grouped_product_list_after', $quantites_required, $product );
		?>
	</div>

	<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" />

	<?php if ( $quantites_required && $show_add_to_cart_button ) : ?>

		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

		<button type="submit" class="single_add_to_cart_button button alt<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

	<?php endif; ?>
</form>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
