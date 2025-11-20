<?php

namespace Glozin\Addons\Modules\Variation_Compare;
use Glozin\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Frontend {

	/**
	 * Check compare attribute
	 *
	 * @var $is_compare
	 */
	protected  $is_compare = null;

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

		add_action( 'glozin_product_extra_link', array( $this, 'compare_attribute' ), 26 );
		add_action( 'glozin_variation_compare_elementor', array( $this, 'compare_attribute' ), 33 );

		add_action( 'wp_footer', array( $this, 'compare_attribute_modal' ) );
	}

	public function scripts() {
		$attribute = $this->has_compare_attribute_primary();
		if( ! $attribute ) {
			return;
		}
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';	
		wp_enqueue_style( 'glozin-variation-compare', GLOZIN_ADDONS_URL . 'modules/variation-compare/assets/variation-compare' . $debug . '.css', array(), '1.0.1' );
		wp_enqueue_script('glozin-variation-compare', GLOZIN_ADDONS_URL . 'modules/variation-compare/assets/variation-compare' . $debug . '.js', array('jquery'), '1.0.1', array('strategy' => 'defer') );
	}

	/**
	 * Product Compare Attribute button
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function compare_attribute() {
		$attribute = $this->has_compare_attribute_primary();
		if( ! $attribute ) {
			return;
		}
		echo '<a href="#" class="glozin-extra-link-item glozin-extra-link-item--variation-compare d-inline-flex align-items-center gap-10 lh-normal text-base text-hover-color" data-toggle="modal" data-target="product-variation-compare-modal">'. apply_filters( 'glozin_product_variation_compare_icon', \Glozin\Addons\Helper::get_svg( 'compare-color' ) ) . apply_filters( 'glozin_product_variation_compare_text', esc_html__( 'Compare', 'glozin-addons' ) . ' ' . $attribute . __('s', 'glozin-addons' ) ) . '</a>';

	}

	/**
	 * Product Compare Attribute content
	 */
	public function compare_attribute_modal() {
		if( ! $this->has_compare_attribute_primary() ) {
			return;
		}
		global $product;
		$attribute_name = $this->get_product_attribute_name( $product );
		?>
		<div id="product-variation-compare-modal" class="product-variation-compare-modal modal product-extra-link-modal <?php echo apply_filters( 'product_variation_compare_class', '' ); ?>">
			<div class="modal__backdrop"></div>
			<div class="modal__container">
				<div class="modal__wrapper">
					<a href="#" class="modal__button-close">
						<?php echo \Glozin\Addons\Helper::get_svg( 'close', 'ui' ); ?>
					</a>
					<div class="modal__header">
						<h3 class="modal__title h5"><?php echo  esc_html__( 'Compare', 'glozin-addons' ) . ' ' . $attribute_name; ?></h3>
					</div>
					<div class="modal__content">
						<?php \Glozin\Addons\Modules\Variation_Compare\Variation_Form::instance()->render(); ?>
					</div>
				</div>
			</div>
			<span class="modal__loader"><span class="glozinSpinner"></span></span>
		</div>
	<?php
	}

	/**
	 * Check Product Compare Attribute
	 */
	public function has_compare_attribute_primary() {
		if( isset( $this->is_compare ) ) {
			return $this->is_compare;
		}
		global $product;
		$product = is_object( $product ) ? $product : wc_get_product( get_the_ID() );
		if( $product->get_type() != 'variable' ) {
			$this->is_compare = false;
		} else {
			$attributes = $product->get_variation_attributes();
			$attribute_name = $this->get_product_attribute_name( $product );
			if( $attribute_name ==  'none' ) {
				$this->is_compare = false;
			} else {
				if( ! empty($attributes['pa_' . $attribute_name]) ) {
					$this->is_compare = $attribute_name;
				} else {
					$this->is_compare = false;
				}
			}
		}

		return $this->is_compare;

	}

	public function get_product_attribute_name ( $product ) {
		$attribute_name = get_post_meta( $product->get_id(), 'glozin_product_variation_attribute', true );
		$attribute_name = (0 === strpos( $attribute_name, 'pa_' )) ? str_replace( 'pa_', '', $attribute_name ) : $attribute_name;
		$attribute_name = $attribute_name ? $attribute_name : get_option( 'glozin_variation_compare_primary' );

		return $attribute_name;
	}

}