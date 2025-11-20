<?php
/**
 * Quick Edit Product Template
 *
 * @var WC_Product $product
 * @var string $cart_item_key
 * @var array $cart_item
 */

if (!defined('ABSPATH')) {
    exit;
}

$attribute_keys  = array_keys( $attributes );
$variations_json = wp_json_encode( $available_variations );
$variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );
?>

<div class="edit-popup entry-summary">
    <form class="variations_form edit-cart-form" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo esc_attr( $variations_attr ); ?>">
        <div class="product-content position-relative d-flex flex-wrap flex-nowrap-md gap-15 gap-md-30 mb-30">
            <div class="column-custom d-flex gap-15 pe-md-30 border-md-end">
                <div class="product-thumbnail w-custom" data-image_original="<?php echo esc_url( wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_gallery_thumbnail' ) ); ?>">
                    <?php $product_image = $product->get_image('woocommerce_gallery_thumbnail'); ?>
                    <?php echo ! empty( $product_image ) ? $product_image : ''; ?>
                </div>
                <div class="product-summary">
                    <?php
                        the_title( '<h3 class="product_title entry-title fs-15 heading-letter-spacing mt-0 mb-10">', '</h3>' );
                        echo '<div class="product-price mb-15">';
                            echo '<p class="price">' . $product->get_price_html() . '</p>';
                        echo '</div>';
                        woocommerce_quantity_input(
                            array(
                                'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
                                'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
                                'input_value' => isset( $quantity ) ? wc_stock_amount( wp_unslash( $quantity ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
                            )
                        );
                    ?>
                </div>
            </div>
            <table class="variations column-custom-remaining" cellspacing="0" role="presentation">
                <tbody>
                    <?php foreach ( $attributes as $attribute_name => $options ) : ?>
                        <tr>
                            <th class="label"><label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>"><?php echo wc_attribute_label( $attribute_name ); // WPCS: XSS ok. ?></label></th>
                            <td class="value">
                                <?php
                                    wc_dropdown_variation_attribute_options(
                                        array(
                                            'options'   => $options,
                                            'attribute' => $attribute_name,
                                            'product'   => $product,
                                            'selected'  => $selected_attributes['attribute_' . sanitize_title( $attribute_name )],
                                        )
                                    );
                                    /**
                                     * Filters the reset variation button.
                                     *
                                     * @since 2.5.0
                                     *
                                     * @param string  $button The reset variation button HTML.
                                     */
                                    echo end( $attribute_keys ) === $attribute_name ? wp_kses_post( apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#" aria-label="' . esc_attr__( 'Clear options', 'glozin' ) . '">' . esc_html__( 'Clear', 'glozin' ) . '</a>' ) ) : '';
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="reset_variations_alert screen-reader-text" role="alert" aria-live="polite" aria-relevant="all"></div>
        </div>

        <div class="woocommerce-variation-add-to-cart variations_button">
            <button type="submit" class="quick_edit_add_to_cart_button w-100 button alt<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" value="<?php echo absint( $product->get_id() ); ?>"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
            <input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>" />
            <input type="hidden" name="variation_id" class="variation_id" value="<?php echo esc_attr($variation_id); ?>" />
            <input type="hidden" name="action" value="quick_edit_update_cart_item">
            <input type="hidden" name="cart_item_key" value="<?php echo esc_attr($cart_item_key); ?>">
        </div>
    </form>
</div>