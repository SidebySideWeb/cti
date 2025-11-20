<?php
namespace Glozin\Addons\Elementor\Builder\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

trait Product_Id_Trait {

	public static function get_product( $product_id = false ) {
		if ( 'product_variation' === get_post_type() ) {
			return self::get_product_variation( $product_id );
		}
		// get last product when is elementor editor mode
		if ( \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
			$product_id = self::get_product_id();
		}

		$product = wc_get_product( $product_id );

		if ( ! $product ) {
			$product = wc_get_product();
		}

		return $product;
	}

	public static function get_product_variation( $product_id = false ) {
		return wc_get_product( get_the_ID() );
	}

	protected static function get_product_id() {
		$product_id = get_transient( 'glozin_woocommerce_last_product_id' );
		if ( false !== $product_id ) {
			return $product_id;
		}
		$args = array(
			'limit' => 1,
			'orderby' => 'date',
			'order' => 'ASC',
		);
		$product_id = false;
		$products = wc_get_products( $args );
		if( $products) {
			$product_id = $products[0]->get_id();
		}
		set_transient( 'glozin_woocommerce_last_product_id', $product_id, DAY_IN_SECONDS );
		return $product_id;

	}

	public function get_last_product_id() {
		return self::get_product_id();
	}
}
