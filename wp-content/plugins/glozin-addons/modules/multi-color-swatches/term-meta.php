<?php

namespace Glozin\Addons\Modules\Multi_Color_Swatches;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use WCBoost\VariationSwatches\Helper;

/**
 * Main class of plugin for admin
 */
class Term_Meta  {

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

	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		$attribute_taxonomies = wc_get_attribute_taxonomies();

		if ( ! empty( $attribute_taxonomies ) ) {
			foreach ( $attribute_taxonomies as $tax ) {
				if ( $tax->attribute_type !== 'color' ) {
					continue;
				}
				add_action( 'pa_' . $tax->attribute_name . '_add_form_fields', [ $this, 'add_attribute_fields' ] );
				add_action( 'pa_' . $tax->attribute_name . '_edit_form_fields', [ $this, 'edit_attribute_fields' ], 10, 2 );
			}
		}
      

        add_action( 'created_term', [ $this, 'save_term_meta' ] );
        add_action( 'edit_term', [ $this, 'save_term_meta' ] );

        add_filter( 'wcboost_variation_swatches_attribute_thumb_column_content', [ $this, 'add_multi_color_swatches' ], 10, 4 );
	}

    /**
	 * Enqueue stylesheet and javascript
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();

		if ( strpos( $screen->id, 'edit-pa_' ) === false && strpos( $screen->id, 'product' ) === false ) {
			return;
		}

        wp_enqueue_style( 'glozin-multi-color-swatches-admin-css', GLOZIN_ADDONS_URL . 'modules/multi-color-swatches/assets/admin/multi-color-swatches-admin.css', array(), '20250325' );
        wp_enqueue_script( 'glozin-multi-color-swatches-admin-js', GLOZIN_ADDONS_URL . 'modules/multi-color-swatches/assets/admin/multi-color-swatches-admin.js', array(), '20250325', true );
	}

    public function add_attribute_fields($taxonomy) {
        $attribute = Helper::get_attribute_taxonomy( $taxonomy );
        
        if ( ! Helper::attribute_is_swatches( $attribute, 'edit' ) ) {
			return;
		}
        ?>
		<div class="form-field term-swatches-wrap" data-type="is-dual-color">
            <span class="label"><?php esc_html_e( 'Is Dual Color', 'glozin-addons' ); ?></span>
            <div class="wcboost-variation-swatches-field">
				<select name="wcboost_variation_swatches_is_dual_color">
					<option value=""><?php esc_html_e( 'No', 'glozin-addons' ); ?></option>
					<option value="yes"><?php esc_html_e( 'Yes', 'glozin-addons' ); ?></option>
				</select>
			</div>
            <p class="description"><?php esc_html_e( 'Make dual color', 'glozin-addons' ); ?></p>
		</div>
        <div class="form-field term-swatches-wrap condition--is-dual-color hidden">
            <span class="label"><?php esc_html_e( 'Swatches Secondary Color', 'glozin-addons' ); ?></span>
            <div class="wcboost-variation-swatches-field wcboost-variation-swatches__field-color">
				<input type="text" name="wcboost_variation_swatches_second_color" value="">
			</div>
            <p class="description"><?php esc_html_e( 'Add another color for the swatches', 'glozin-addons' ); ?></p>
		</div>
        <?php
    }

    /**
	 * Create hook to fields to edit attribute term screen
	 *
	 * @param object $term
	 * @param string $taxonomy
	 */
	public function edit_attribute_fields( $term, $taxonomy ) {
		$attribute = Helper::get_attribute_taxonomy( $taxonomy );

		if ( ! Helper::attribute_is_swatches( $attribute, 'edit' ) ) {
			return;
		}
        $secondary_color = '';
		$is_dual_color = '';

		if ( $term && is_object( $term ) ) {
            $secondary_color = get_term_meta( $term->term_id, 'wcboost_variation_swatches_second_color', true );
			$is_dual_color = get_term_meta( $term->term_id, 'wcboost_variation_swatches_is_dual_color', true );
		}
		
		?>
		<tr class="form-field form-required" data-type="is-dual-color">
			<th scope="row" valign="top">
				<label><?php esc_html_e( 'Is Dual Color', 'glozin-addons' ); ?></label>
			</th>
			<td>
                <div class="wcboost-variation-swatches-field">
					<select name="wcboost_variation_swatches_is_dual_color">
						<option value="" <?php selected( $is_dual_color, '' ); ?>><?php esc_html_e( 'No', 'glozin-addons' ); ?></option>
						<option value="yes" <?php selected( $is_dual_color, 'yes' ); ?>><?php esc_html_e( 'Yes', 'glozin-addons' ); ?></option>
					</select>
                </div>
                <p class="description"><?php esc_html_e( 'Make dual color', 'glozin-addons' ); ?></p>
			</td>
		</tr>
        <tr class="form-field form-required condition--is-dual-color <?php echo $is_dual_color ? '' : 'hidden'; ?>">
			<th scope="row" valign="top">
				<label><?php esc_html_e( 'Swatches Secondary Color', 'glozin-addons' ); ?></label>
			</th>
			<td>
                <div class="wcboost-variation-swatches-field wcboost-variation-swatches__field-color">
                    <input type="text" name="wcboost_variation_swatches_second_color" value="<?php echo esc_attr( $secondary_color ); ?>">
                </div>
                <p class="description"><?php esc_html_e( 'Add another color for the swatches', 'glozin-addons' ); ?></p>
			</td>
		</tr>
		<?php
	}

    /**
	 * Save term meta
	 *
	 * @param int $term_id
	 */
	public function save_term_meta( $term_id ) {
		$is_dual_color = isset( $_POST['wcboost_variation_swatches_is_dual_color'] ) ? sanitize_text_field( wp_unslash( $_POST['wcboost_variation_swatches_is_dual_color'] ) ) : '';
		update_term_meta( $term_id, 'wcboost_variation_swatches_is_dual_color', $is_dual_color );

        $term_meta = isset( $_POST['wcboost_variation_swatches_second_color'] ) ? sanitize_text_field( wp_unslash( $_POST['wcboost_variation_swatches_second_color'] ) ) : '';
        update_term_meta( $term_id, 'wcboost_variation_swatches_second_color', $term_meta );
	}

    /**
     * Add multi color swatches
     *
     * @param string $html
     * @param string $value
     * @param object $attr
     * @param int $term_id
     *
     * @return string
     */
    public function add_multi_color_swatches( $html, $value, $attr, $term_id ) {
        $secondary_color = get_term_meta( $term_id, 'wcboost_variation_swatches_second_color', true );
        $is_dual_color = get_term_meta( $term_id, 'wcboost_variation_swatches_is_dual_color', true );
		
        if( $attr->attribute_type !== 'color' ) {
            return $html;
        }

        if ( ! empty( $secondary_color ) && $is_dual_color === 'yes' ) {
            $html = sprintf(
					'<div class="wcboost-variation-swatches-item wcboost-variation-swatches-item--color wcboost-variation-swatches-item--multi-color" style="--wcboost-swatches-color: %s; --wcboost-swatches-color-secondary: %s"></div>',
					esc_attr( $value ),
                    esc_attr( $secondary_color )
                );
        }
        
        return $html;
    }
}