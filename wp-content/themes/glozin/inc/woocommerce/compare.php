<?php
/**
 * Hooks of Compare.
 *
 * @package Glozin
 */

namespace Glozin\WooCommerce;

use \Glozin\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Compare template.
 */
class Compare {
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
		add_filter( 'wcboost_products_compare_button_template_args', array( $this, 'products_compare_button_template_args' ), 10, 2 );
		add_filter( 'wcboost_products_compare_add_to_compare_fragments', array( $this, 'products_compare_add_to_compare_fragments' ), 10, 1 );
		add_filter( 'wcboost_products_compare_single_add_to_compare_link', array( $this, 'single_add_to_compare_link' ), 20, 2 );
		add_filter( 'wcboost_products_compare_loop_add_to_compare_link', array( $this, 'loop_add_to_compare_link' ), 20, 2 );

		add_filter( 'wcboost_products_compare_button_icon', array( $this, 'compare_svg_icon' ), 20, 2 );

		// Remove Default Compare button.
		if( class_exists('\WCBoost\ProductsCompare\Frontend') ) {
			$compare = \WCBoost\ProductsCompare\Frontend::instance();
			remove_action( 'woocommerce_after_add_to_cart_form', array( $compare, 'single_add_to_compare_button' ) );
			remove_action( 'woocommerce_after_shop_loop_item', array( $compare, 'loop_add_to_compare_button' ), 15 );
		}

		// Single Product Compare
		if( class_exists('\WCBoost\ProductsCompare\Frontend') ) {
			add_action( 'woocommerce_after_add_to_cart_button', array( \WCBoost\ProductsCompare\Frontend::instance(), 'single_add_to_compare_button' ), 21 );
		}

		// Compare Page
		add_filter( 'wcboost_products_compare_empty_message', array( $this, 'products_compare_empty_message' ), 20, 1 );
		add_filter( 'wcboost_products_compare_fields', array( $this, 'products_compare_fields' ) );
		add_action( 'wcboost_products_compare_custom_field', array( $this, 'products_compare_custom_field' ), 20, 3 );
		add_action( 'wcboost_products_compare_field_content', array( $this, 'products_compare_badge_field' ), 20, 3 );
	}


	/**
	 * Show button compare.
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function products_compare_button_template_args( $args, $product ) {
		$args['class'][] = 'product-loop-button button';

		return $args;
	}

	/**
	 * Ajaxify update count compare
	 *
	 * @since 1.0
	 *
	 * @param array $fragments
	 *
	 * @return array
	 */
	public static function products_compare_add_to_compare_fragments( $data ) {
		$compare_counter = intval(\WCBoost\ProductsCompare\Plugin::instance()->list->count_items());
		$compare_class = $compare_counter == 0 ? ' empty-counter' : '';
		$data['span.header-compare__counter'] = '<span class="header-counter header-compare__counter' . $compare_class . '">'. $compare_counter . '</span>';

		return $data;
	}

	/**
	 * Compare icon
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function compare_svg_icon($svg, $icon) {
		if( $icon == 'arrows' ) {
			$svg = \Glozin\Icon::inline_svg('icon=icon-compare');
		} else if( $icon == 'check' ) {
			$svg = \Glozin\Icon::inline_svg('icon=icon-compare');
		}

		return $svg;
	}

	/**
	 * Change compare link
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function loop_add_to_compare_link($html, $args) {
		$classes = is_singular( 'product' ) ? 'wcboost-products-compare-button wcboost-products-compare-button--single button' : 'wcboost-products-compare-button wcboost-products-compare-button--loop button';
		$product_title = isset( $args['product_title'] ) ? $args['product_title'] : '';
		if( empty( $product_title ) ) {
			$product = isset($args['product_id']) ? wc_get_product( $args['product_id'] ) : '';
			$product_title = $product ? $product->get_title() : '';
		}

		$label_add = \WCBoost\ProductsCompare\Helper::get_button_text( 'add' );
		$label_added = \WCBoost\ProductsCompare\Helper::get_button_text( 'view' );

		if( get_option( 'wcboost_products_compare_exists_item_button_behaviour', 'remove' ) == 'remove' ) {
			$label_added = \WCBoost\ProductsCompare\Helper::get_button_text( 'remove' );
		}

		return sprintf(
			'<a href="%s" class="gz-button-icon gz-button-light gz-tooltip-inside %s" role="button" %s data-product_title="%s" data-tooltip="%s" data-tooltip_added="%s" data-tooltip_position="%s">
				%s
				<span class="wcboost-products-compare-button__text">%s</span>
			</a>',
			esc_url( isset( $args['url'] ) ? $args['url'] : add_query_arg( [ 'add-to-compare' => $args['product_id'] ] ) ),
			esc_attr( isset( $args['class'] ) ? $args['class'] : esc_attr( $classes ) ),
			isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
			esc_attr( $product_title ),
			esc_attr( $label_add ),
			esc_attr( $label_added ),
			apply_filters( 'glozin_compare_loop_tooltip_position', 'left' ),
			empty( $args['icon'] ) ? '' : '<span class="wcboost-products-compare-button__icon">' . $args['icon'] . '</span>',
			esc_html( isset( $args['label'] ) ? $args['label'] : __( 'Compare', 'glozin' ) )
		);
	}

		/**
	 * Change compare link
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function single_add_to_compare_link($html, $args) {
		$classes = is_singular( 'product' ) ? 'wcboost-products-compare-button wcboost-products-compare-button--single button' : 'wcboost-products-compare-button wcboost-products-compare-button--loop button';
		$product_title = isset( $args['product_title'] ) ? $args['product_title'] : '';
		if( empty( $product_title ) ) {
			$product = isset($args['product_id']) ? wc_get_product( $args['product_id'] ) : '';
			$product_title = $product ? $product->get_title() : '';
		}

		$label_add = \WCBoost\ProductsCompare\Helper::get_button_text( 'add' );
		$label_added = \WCBoost\ProductsCompare\Helper::get_button_text( 'view' );

		if( get_option( 'wcboost_products_compare_exists_item_button_behaviour', 'remove' ) == 'remove' ) {
			$label_added = \WCBoost\ProductsCompare\Helper::get_button_text( 'remove' );
		}
		
		return sprintf(
			'<a href="%s" class="gz-button-icon gz-button-outline gz-tooltip-inside %s" role="button" %s data-product_title="%s" data-tooltip="%s" data-tooltip_added="%s">
				%s
				<span class="wcboost-products-compare-button__text">%s</span>
			</a>',
			esc_url( isset( $args['url'] ) ? $args['url'] : add_query_arg( [ 'add-to-compare' => $args['product_id'] ] ) ),
			esc_attr( isset( $args['class'] ) ? $args['class'] : esc_attr( $classes ) ),
			isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
			esc_attr( $product_title ),
			wp_kses_post( $label_add ),
			wp_kses_post( $label_added ),
			empty( $args['icon'] ) ? '' : '<span class="wcboost-products-compare-button__icon">' . $args['icon'] . '</span>',
			esc_html( isset( $args['label'] ) ? $args['label'] : __( 'Compare', 'glozin' ) )
		);
	}

	/**
	 * Product compare empty messages
	 *
	 * @return void
	 */
	public function products_compare_empty_message() {
		return sprintf(
						'<h3>%s</h3>
						<p>%s</p>
						<p>%s</p>',
					 	esc_html__( 'Compare list is empty', 'glozin' ),
					 	esc_html__( 'No products added in the compare list. You must add some products to compare them.', 'glozin' ),
						esc_html__( 'You will find a lot of interesting products on our "Shop" page.', 'glozin' ),
					);
	}

	public function products_compare_fields($fields) {
		$options = (array) Helper::get_option('compare_page_columns');
		$fields = $this->get_default_compare_fields($fields, $options);
		$attributes = $this->attribute_taxonomies($options);

		if( $attributes ) {
			if( isset($fields['add-to-cart']) ) {
				unset( $fields['add-to-cart'] );
			}
			if(in_array( 'add-to-cart', $options ) ) {
				$attributes['add-to-cart'] = '';
			}
			$fields = array_merge( $fields, $attributes );
		}

		$stock_value = null;
		if (isset($fields['stock'])) {
			$stock_value = $fields['stock'];
			unset($fields['stock']);
		}

		$default_columns = $this->get_default_compare_fields([], $options);
		$sorted_fields = [];

		foreach ($default_columns as $key => $label) {
			if (isset($fields[$key])) {
				$sorted_fields[$key] = $fields[$key];
			}
		}

		$sorted_fields = array_merge($sorted_fields, $fields);

		if ($stock_value !== null) {
			$sorted_fields['stock'] = $stock_value;
		}

		return $sorted_fields;
	}

	private function get_default_compare_fields($fields, $options) {
		$default_columns = [
			'remove'      => '',
			'badge'       => '',
			'thumbnail'   => esc_html__('Products', 'glozin'),
			'name'        => '',
			'price'       => esc_html__('Price', 'glozin'),
			'add-to-cart' => esc_html__('Add To Cart', 'glozin'),
			'rating'      => esc_html__('Review', 'glozin'),
			'weight'      => esc_html__('Weight', 'glozin'),
			'sku'         => esc_html__('SKU', 'glozin'),
			'dimensions'  => esc_html__('Dimensions', 'glozin'),
			'stock'       => esc_html__('Availability', 'glozin'),
		];

		$filtered_fields = [];
		foreach ($default_columns as $key => $name) {
			if (in_array($key, $options)) {
				$filtered_fields[$key] = $name;
			}
		}

		if (isset($fields['attributes'])) {
			unset($fields['attributes']);
		}

		return $filtered_fields;

	}

	/**
	 * Get Woocommerce Attribute Taxonomies
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function attribute_taxonomies($options) {

		$attributes = array();

		$attribute_taxonomies = wc_get_attribute_taxonomies();
		if ( empty( $attribute_taxonomies ) ) {
			return array();
		}
		foreach ( $attribute_taxonomies as $attribute ) {
			$tax_name =  $attribute->attribute_name;
			$tax = wc_attribute_taxonomy_name( $tax_name );

			if ( taxonomy_exists( $tax ) && in_array( $tax_name, $options ) ) {
				$attributes[ $tax ] = ucfirst( $attribute->attribute_label );
			}
		}


		return $attributes;
	}

	public function products_compare_custom_field($field, $product, $key) {
		if ( taxonomy_exists( $field ) ) {
			$attributes = array();
			$terms                     = get_the_terms( $product->get_id(), $field );
			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					$term                        = sanitize_term( $term, $field );
					$attributes[] = $term->name;
				}
			}
			echo implode( ', ', $attributes );
		}

	}

	public function products_compare_badge_field($field, $product, $key) {
		switch ( $field ) { 
			case 'badge':
				echo \Glozin\WooCommerce\Badges::badges( $product, 'woocommerce-badges position-absolute top-30 start-0 ms-35 z-2 pe-none' );
				break;

			case 'add-to-cart':
				if ( $product->get_type() !== 'grouped' ) {
					return;
				}

				$GLOBALS['product'] = $product;

				woocommerce_template_loop_add_to_cart();

				wc_setup_product_data( $GLOBALS['post'] );
				
				break;

		}
	}
}
