<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/mini-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.0.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_mini_cart' ); ?>

<?php if ( WC()->cart && ! WC()->cart->is_empty() ) : ?>

	<?php do_action( 'glozin_before_woocommerce_mini_cart_items' ); ?>

	<ul class="woocommerce-mini-cart cart_list product_list_widget <?php echo esc_attr( $args['list_class'] ); ?>">
		<?php
		do_action( 'woocommerce_before_mini_cart_contents' );
		$position = 0;
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				/**
				 * This filter is documented in woocommerce/templates/cart/cart.php.
				 *
				 * @since 2.1.0
				 */
				$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
				$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image('woocommerce_gallery_thumbnail'), $cart_item, $cart_item_key );
				$product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
				$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
				$unit_measure 	   = maybe_unserialize( get_post_meta( $_product->get_id(), 'unit_measure', true ) );
				?>
				<li class="woocommerce-mini-cart-item mini-cart-item-<?php echo esc_attr( $_product->get_id() ); ?> <?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?>">
					<div class="woocommerce-mini-cart-item__thumbnail">
						<?php if ( $product_permalink ) : ?>
							<a href="<?php echo esc_url( $product_permalink ); ?>">
						<?php endif; ?>
							<?php echo wp_kses_post( $thumbnail ); ?>
						<?php if ( $product_permalink ) : ?>
							</a>
						<?php endif; ?>
					</div>
					<div class="woocommerce-mini-cart-item__summary">
						<div class="woocommerce-mini-cart-item__box">
							<span class="woocommerce-mini-cart-item__data">
								<span class="woocommerce-mini-cart-item__name">
								<?php if ( $product_permalink ) : ?>
									<a href="<?php echo esc_url( $product_permalink ); ?>">
								<?php endif; ?>
										<?php echo wp_kses_post( $product_name ); ?>
									<?php if ( $product_permalink ) : ?>
										</a>
									<?php endif; ?>
								</span>
								<?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>
								<?php if( $unit_measure ) : ?>
									<div class="woocommerce-mini-cart-item__price d-flex align-items-center">
								<?php endif; ?>
								<?php
									if( WC()->cart->display_prices_including_tax() ) {
										$_product_regular_price = wc_get_price_including_tax( $_product, array( 'price' => $_product->get_regular_price() ) );
										$_product_sale_price = wc_get_price_including_tax( $_product, array( 'price' => $_product->get_price() ) );
									} else {
										$_product_regular_price = $_product->get_regular_price();
										$_product_sale_price = $_product->get_price();
									}
								?>
								<?php if( ! empty( $_product_sale_price ) && floatval( $_product_regular_price ) > floatval( $_product_sale_price ) ) : ?>
									<span class="price"><?php echo wc_format_sale_price( $_product_regular_price, $_product_sale_price ); ?></span>
								<?php else : ?>
									<span class="woocommerce-Price-amount amount">
										<bdi><?php echo ! empty( $product_price ) ? $product_price : '' ?></bdi>
									</span>
								<?php endif; ?>
								<?php if( $unit_measure ) : ?>
									<span class="gz-price-unit"><span class="divider">/</span> <?php echo esc_html( $unit_measure ); ?></span>
									</div>
								<?php endif; ?>
								<span class="woocommerce-mini-cart-item__qty--text hidden">
									<?php esc_html_e( 'QTY:', 'glozin' ); ?>
									<?php echo wp_kses_post( $cart_item['quantity'] ); ?>
								</span>
							</span>
							<span class="woocommerce-mini-cart-item__qty" data-nonce="<?php echo wp_create_nonce( 'glozin-update-cart-qty--' . $cart_item_key ); ?>">
								<?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times;', $cart_item['quantity'] ) . '</span>', $cart_item, $cart_item_key ); ?>
								<span class="woocommerce-mini-cart-item__actions d-flex flex-column ms-10">
									<?php
									$matchingKeys = array_filter(array_keys($cart_item), function($key) {
										return preg_match('/^glozin_/', $key);
									});

									if( empty( $matchingKeys ) && $_product->get_type() === 'variation' ) {
										echo apply_filters(
											'woocommerce_cart_item_edit_link',
											sprintf(
												'<a href="%s" class="glozin-quickedit-button gz-button gz-button-text" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-position="%s">%s</a>',
												esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
												esc_attr__( 'Update this item', 'glozin' ),
												esc_attr( $product_id ),
												esc_attr( $cart_item_key ),
												esc_attr( $position ),
												\Glozin\Icon::get_svg('edit', 'ui', array( 'class' => 'icon-fill-none' )),
											),
											$cart_item_key
										);
									}
									?>
									<?php
										echo apply_filters(
											'woocommerce_cart_item_remove_link',
											sprintf(
												'<a href="%s" class="remove gz-button gz-button-text remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s" data-success_message="%s">%s</a>',
												esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
												esc_attr__( 'Remove this item', 'glozin' ),
												esc_attr( $product_id ),
												esc_attr( $cart_item_key ),
												esc_attr( $_product->get_sku() ),
												esc_attr( sprintf( __( '&ldquo;%s&rdquo; has been removed from your cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ),
												\Glozin\Icon::inline_svg( 'icon=trash'),
											),
											$cart_item_key
										);
									?>
								</span>
							</span>
						</div>
					</div>
					<input type="hidden" name="details_product" data-id="<?php echo esc_attr( $product_id ); ?>" data-price="<?php echo esc_attr($_product->get_price()); ?>" value="<?php echo esc_attr( $_product->get_id() ). '|' .esc_attr( json_encode( $cart_item['variation'] ) ); ?>" />
				</li>
				<?php
			}

			$position++;
		}

		do_action( 'woocommerce_mini_cart_contents' );
		?>
	</ul>

	<?php do_action( 'glozin_after_woocommerce_mini_cart_items' ); ?>

	<div class="widget_shopping_cart_footer">
		<?php do_action( 'glozin_before_widget_shopping_cart_total' ); ?>

		<p class="woocommerce-mini-cart__total total">
			<?php
			/**
			 * Hook: woocommerce_widget_shopping_cart_total.
			 *
			 * @hooked woocommerce_widget_shopping_cart_subtotal - 10
			 */
			do_action( 'woocommerce_widget_shopping_cart_total' );
			?>
		</p>

		<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

		<p class="woocommerce-mini-cart__buttons buttons"><?php do_action( 'woocommerce_widget_shopping_cart_buttons' ); ?></p>

		<?php do_action( 'woocommerce_widget_shopping_cart_after_buttons' ); ?>
	</div>

<?php else : ?>

	<div class="woocommerce-mini-cart__empty-message">
		<span class="gz-svg-icon gz-svg-icon--cart">
			<svg width="70" height="78" fill="none">
                <path fill="#888" fill-rule="evenodd" d="m2.357 32.177.732 3.764a1.13 1.13 0 1 1-2.216.433L.14 32.609c-.891-4.581 2.577-8.87 7.23-8.87H62.63c4.597 0 8.053 4.194 7.254 8.738l-6.933 39.386C62.329 75.406 59.278 78 55.698 78H15.73c-3.438 0-6.41-2.398-7.179-5.767l-1.08-4.735a1.129 1.129 0 1 1 2.201-.504l1.08 4.735c.538 2.355 2.607 4.01 4.978 4.01h39.968c2.468 0 4.594-1.79 5.03-4.268l6.933-39.386C68.22 28.899 65.798 26 62.63 26H7.37c-3.206 0-5.638 2.965-5.013 6.177Z" clip-rule="evenodd"></path>
                <path fill="#888" d="M32.633 2.802a1.805 1.805 0 0 0-.489-2.496 1.786 1.786 0 0 0-2.485.49L11.027 28.684a1.805 1.805 0 0 0 .489 2.497A1.786 1.786 0 0 0 14 30.689L32.633 2.802ZM56.038 30.501a1.786 1.786 0 0 0 2.447-.657c.495-.86.203-1.96-.654-2.458L35.096 14.172a1.786 1.786 0 0 0-2.447.656c-.495.86-.203 1.96.654 2.459L56.038 30.5Z"></path>
                <path fill="#888" fill-rule="evenodd" d="M35.012 53.02c-.298.07-.663.362-.897.674-.514.683-1.412.76-2.008.17-.595-.588-.662-1.62-.148-2.303.477-.635 1.358-1.48 2.488-1.742a2.917 2.917 0 0 1 1.943.207c.67.319 1.247.882 1.727 1.643.46.731.319 1.752-.318 2.281-.637.53-1.527.366-1.988-.365-.237-.375-.42-.498-.51-.54a.412.412 0 0 0-.29-.025Z" clip-rule="evenodd"></path>
                <path fill="#888" d="M25.402 47.478a1.695 1.695 0 1 0-.002-3.389 1.695 1.695 0 0 0 .003 3.39ZM44.596 47.478c.936 0 1.693-.759 1.693-1.695a1.694 1.694 0 1 0-3.387 0c0 .936.758 1.695 1.694 1.695Z"></path>
            </svg>
		</span>
		<div class="fs-18 mb-20 mt-15 h4"><?php echo esc_html__( 'Your cart is currently empty!.', 'glozin' );?></div>
		<p class="mb-20"><?php echo esc_html__( 'You may check out all the available products and buy some in the shop.', 'glozin' );?></p>
		<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ) ?>" class="gz-button gz-button-hover-effect"><?php echo esc_html__( 'Continue Shopping', 'glozin' );?></a>
	</div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
