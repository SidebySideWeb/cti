<?php
namespace Glozin\Addons\Elementor\Builder\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_Price extends Widget_Base {
	use \Glozin\Addons\Elementor\Builder\Traits\Product_Id_Trait;

	public function get_name() {
		return 'glozin-product-price';
	}

	public function get_title() {
		return esc_html__( '[Glozin] Product Price', 'glozin-addons' );
	}

	public function get_icon() {
		return 'eicon-product-price';
	}

	public function get_categories() {
		return ['glozin-addons-product'];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'price', 'product' ];
	}

	/**
	 * Get HTML wrapper class.
	 *
	 * Retrieve the widget container class. Can be used to override the
	 * container class for specific widgets.
	 *
	 * @since 2.0.9
	 * @access protected
	 */
	protected function get_html_wrapper_class() {
		return 'elementor-widget-' . $this->get_name() . ' entry-summary';
	}

	/**
	 * Register heading widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_price_style',
			[
				'label' => esc_html__( 'Price', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'gap',
			[
				'label' => __( 'Gap', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					]
				],
				'default' => [],
				'selectors' => [
					'.single-product.single-product-elementor div.product {{WRAPPER}} .glozin-product-price .price' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'price_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.single-product.single-product-elementor div.product {{WRAPPER}} .glozin-product-price .price' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'selector' => '.single-product.single-product-elementor div.product {{WRAPPER}} .glozin-product-price .price',
			]
		);

		$this->add_control(
			'sale_heading',
			[
				'label' => esc_html__( 'Sale Price', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sale_price_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.single-product.single-product-elementor div.product {{WRAPPER}} .glozin-product-price .price ins' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => esc_html__( 'Typography', 'glozin-addons' ),
				'name' => 'sale_price_typography',
				'selector' => '.single-product.single-product-elementor div.product {{WRAPPER}} .glozin-product-price .price ins',
			]
		);

		$this->add_control(
			'old_heading',
			[
				'label' => esc_html__( 'Old Price', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'old_price_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.single-product.single-product-elementor div.product {{WRAPPER}} .glozin-product-price .price del' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => esc_html__( 'Typography', 'glozin-addons' ),
				'name' => 'old_price_typography',
				'selector' => '.single-product.single-product-elementor div.product {{WRAPPER}} .glozin-product-price .price del',
			]
		);

		$this->add_control(
			'suffix_heading',
			[
				'label' => esc_html__( 'Suffix', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'suffix_price_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.single-product.single-product-elementor div.product {{WRAPPER}} .glozin-product-price .woocommerce-price-suffix' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => esc_html__( 'Typography', 'glozin-addons' ),
				'name' => 'suffix_price_typography',
				'selector' => '.single-product.single-product-elementor div.product {{WRAPPER}} .glozin-product-price .woocommerce-price-suffix',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render heading widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		global $product;
		$product = $this->get_product();

		if ( ! $product ) {
			return;
		}

		if( function_exists('woocommerce_template_single_price') ) {
			echo '<div class="glozin-product-price">';
				woocommerce_template_single_price();
			echo '</div>';
		}
	}
}
