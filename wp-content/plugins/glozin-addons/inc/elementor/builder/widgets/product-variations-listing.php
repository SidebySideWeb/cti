<?php
namespace Glozin\Addons\Elementor\Builder\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_Variations_Listing extends Widget_Base {
	use \Glozin\Addons\Elementor\Builder\Traits\Product_Id_Trait;

	public function get_name() {
		return 'glozin-product-variations-listing';
	}

	public function get_title() {
		return esc_html__( '[Glozin] Quick order list', 'glozin-addons' );
	}

	public function get_icon() {
		return 'eicon-woocommerce';
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'variation', 'quick', 'order', 'list', 'product' ];
	}

	public function get_categories() {
		return [ 'glozin-addons-product' ];
	}

	protected function register_controls() {
		$this->section_header_style();
		$this->section_body_style();
		$this->section_footer_style();
	}

	protected function section_header_style() {
		$this->start_controls_section(
			'section_header_style',
			[
				'label' => esc_html__( 'Header', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'header_typography',
				'selector' => '{{WRAPPER}} .product-variations-listing__header span',
			]
		);

		$this->add_control(
			'header_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-variations-listing__header span' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'header_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-variations-listing__header' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'header_border',
				'label' => esc_html__( 'Border', 'glozin-addons' ),
				'selector' => '{{WRAPPER}} .product-variations-listing__header',
			]
		);

		$this->add_responsive_control(
			'header_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .product-variations-listing__header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .product-variations-listing__header' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function section_body_style() {
		$this->start_controls_section(
			'section_body_style',
			[
				'label' => esc_html__( 'Body', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'body_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-variations-listing__body .product-variations-listing__item' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'body_border',
				'label' => esc_html__( 'Border', 'glozin-addons' ),
				'selector' => '{{WRAPPER}} .product-variations-listing__body .product-variations-listing__item',
			]
		);

		$this->add_responsive_control(
			'body_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .product-variations-listing__body .product-variations-listing__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .product-variations-listing__body .product-variations-listing__item' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'product_name_heading',
			[
				'label' => esc_html__( 'Product Name', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'product_name_typography',
				'selector' => '{{WRAPPER}} .product-variations-listing__body .product-variations-listing__title',
			]
		);

		$this->add_control(
			'product_name_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-variations-listing__body .product-variations-listing__title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'product_quantity_heading',
			[
				'label' => esc_html__( 'Product Quantity', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'product_quantity_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-variations-listing__quantity .quantity' => 'color: {{VALUE}}',
					'{{WRAPPER}} .product-variations-listing__quantity .quantity input' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'product_quantity_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-variations-listing__quantity .quantity .glozin-qty-button:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'product_quantity_hover_background_color',
			[
				'label' => esc_html__( 'Icon background color on hover', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-variations-listing__quantity .quantity .glozin-qty-button:hover::before' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'product_quantity_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-variations-listing__quantity .quantity' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'product_quantity_border',
				'label' => esc_html__( 'Border', 'glozin-addons' ),
				'selector' => '{{WRAPPER}} .product-variations-listing__quantity .quantity',
			]
		);

		$this->add_responsive_control(
			'product_quantity_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .product-variations-listing__quantity .quantity' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .product-variations-listing__quantity .quantity' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'product_price_heading',
			[
				'label' => esc_html__( 'Product Price', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'product_price_typography',
				'selector' => '{{WRAPPER}} .product-variations-listing__price .price, {{WRAPPER}} .product-variations-listing__total .price',
			]
		);

		$this->add_control(
			'product_price_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-variations-listing__price .price' => 'color: {{VALUE}}',
					'{{WRAPPER}} .product-variations-listing__total .price' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'product_sale_price_heading',
			[
				'label' => esc_html__( 'Sale Price', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'product_sale_price_typography',
				'selector' => '{{WRAPPER}} .product-variations-listing__price .price ins, {{WRAPPER}} .product-variations-listing__total .price ins',
			]
		);

		$this->add_control(
			'product_sale_price_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-variations-listing__price .price ins' => 'color: {{VALUE}}',
					'{{WRAPPER}} .product-variations-listing__total .price ins' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'product_old_price_heading',
			[
				'label' => esc_html__( 'Old Price', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'product_old_price_typography',
				'selector' => '{{WRAPPER}} .product-variations-listing__price .price del, {{WRAPPER}} .product-variations-listing__total .price del',
			]
		);

		$this->add_control(
			'product_old_price_color',
			[
				'label' => esc_html__( 'Old Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-variations-listing__price .price del' => 'color: {{VALUE}}',
					'{{WRAPPER}} .product-variations-listing__total .price del' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function section_footer_style() {
		$this->start_controls_section(
			'section_footer_style',
			[
				'label' => esc_html__( 'Footer', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'footer_typography',
				'selector' => '{{WRAPPER}} .product-variations-listing__footer span',
			]
		);

		$this->add_control(
			'footer_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-variations-listing__footer span' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'footer_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-variations-listing__footer' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'footer_border',
				'label' => esc_html__( 'Border', 'glozin-addons' ),
				'selector' => '{{WRAPPER}} .product-variations-listing__footer',
			]
		);

		$this->add_responsive_control(
			'footer_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .product-variations-listing__footer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .product-variations-listing__footer' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_heading',
			[
				'label' => esc_html__( 'Button', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'footer_button_typography',
				'selector' => '{{WRAPPER}} .product-variations-listing__button .button',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'footer_button_border',
				'label' => esc_html__( 'Border', 'glozin-addons' ),
				'selector' => '{{WRAPPER}} .product-variations-listing__button .button',
			]
		);

		$this->add_responsive_control(
			'footer_button_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .product-variations-listing__button .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .product-variations-listing__button .button' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'footer_button_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .product-variations-listing__button .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .product-variations-listing__button .button' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'footer_button_style' );

		$this->start_controls_tab(
			'footer_button_normal',
			[
				'label' => __( 'Normal', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'footer_button_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-variations-listing__button .button' => '--gz-button-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'footer_button_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-variations-listing__button .button' => '--gz-button-bg-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'footer_button_hover',
			[
				'label' => __( 'Hover', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'footer_button_hover_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-variations-listing__button .button' => '--gz-button-color-hover: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'footer_button_hover_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-variations-listing__button .button' => '--gz-button-bg-color-hover: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'footer_button_hover_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-variations-listing__button .button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'footer_button_background_effect_hover_color',
			[
				'label'     => __( 'Background Effect Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-variations-listing__button .button' => '--gz-button-eff-bg-color-hover: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render() {
		global $product;

		$product = $this->get_product();

		if ( \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
			$args =  array(
				'type' => 'variable',
				'limit' => 1,
				'orderby' => 'date',
				'order' => 'ASC',
			);
			$products = wc_get_products( $args );
			$product_id = $products[0]->get_id();
			setup_postdata( $product_id );
			$original_post = $GLOBALS['post'];
			$GLOBALS['post'] = get_post( $product_id );
			setup_postdata( $GLOBALS['post'] );

			add_filter( 'glozin_product_variations_listing_check', '__return_true' );
		}

		if ( ! $product ) {
			return;
		}

		do_action( 'glozin_single_product_variations_listing_elementor' );

		if ( \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
			$GLOBALS['post'] = $original_post;
		}
	}
}
