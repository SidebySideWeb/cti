<?php
/**
 * Widget Image
 */

namespace Glozin\Addons\Modules\Mega_Menu\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product carousel widget class
 */
class Product extends Widget_Base {

	use \Glozin\Addons\WooCommerce\Products_Base;

	/**
	 * Set the widget name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'product';
	}

	/**
	 * Set the widget label
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Product', 'glozin-addons' );
	}

	/**
	 * Default widget options
	 *
	 * @return array
	 */
	public function get_defaults() {
		return array(
			'title'  		=> '',
			'product_id'  	=> '',
		);
	}

	/**
	 * Render widget content
	 */
	public function render() {
		$data = $this->get_data();

		$classes = $data['classes'] ? ' ' . $data['classes'] : '';
		$attr = [
			'ids' 		=> $data['product_id'],
			'columns' 	=> 1,
			'type'    	=> 'custom_products',
		];

		add_filter('glozin_product_card_layout', array($this, 'product_card_layout'));
		echo '<div class="menu-widget-product'. esc_attr( $classes ) .'">';
		echo '<div class="mega-widget-product__title text-dark fs-16 fw-semibold pb-10 lh-1 d-inline-block">'. wp_kses_post( $data['title'] ) .'</div>';
		printf( '%s', $this->render_products( $attr ) );
		echo '</div>';
		remove_filter('glozin_product_card_layout', array($this, 'product_card_layout'));
	}

	public function product_card_layout() {
		return '1';
	}

	/**
	 * Widget setting fields.
	 */
	public function add_controls() {
		$this->add_control( array(
			'type' => 'text',
			'name' => 'title',
			'label' => esc_html__( 'Title', 'glozin-addons' ),
		) );

		$this->add_control( array(
			'type' => 'text',
			'name' => 'product_id',
			'label' => esc_html__( 'Product ID', 'glozin-addons' ),
			'description' => esc_html__( 'Enter the product ID of the item you want to display (e.g., 88)', 'glozin-addons' ),
		) );
	}

}