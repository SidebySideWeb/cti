<?php
namespace Glozin\Addons\Elementor\Builder\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Buy_X_Get_Y extends Widget_Base {
	use \Glozin\Addons\Elementor\Builder\Traits\Product_Id_Trait;

	public function get_name() {
		return 'glozin-buy-x-get-y';
	}

	public function get_title() {
		return esc_html__( '[Glozin] Buy X Get Y', 'glozin-addons' );
	}

	public function get_icon() {
		return 'eicon-woocommerce';
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'buy', 'x', 'get', 'y', 'one get one', 'product' ];
	}

	public function get_categories() {
		return [ 'glozin-addons-product' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_style',
			[
				'label'     => __( 'Style', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-buy-x-get-y' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-buy-x-get-y' => 'border-start-start-radius: {{TOP}}{{UNIT}}; border-start-end-radius: {{RIGHT}}{{UNIT}}; border-end-end-radius: {{BOTTOM}}{{UNIT}}; border-end-start-radius: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'label' => esc_html__( 'Border', 'glozin-addons' ),
				'selector' => '{{WRAPPER}} .glozin-buy-x-get-y',
			]
		);

		$this->add_control(
			'line_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Line', 'glozin-addons' ),
				'separator' => 'before',
			]	
		);

		$this->add_responsive_control(
			'line_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-buy-x-get-y__line' => 'margin-top: {{TOP}}{{UNIT}}; margin-inline-end: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}}; margin-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'line_border',
				'label' => esc_html__( 'Border', 'glozin-addons' ),
				'selector' => '{{WRAPPER}} .glozin-buy-x-get-y__line::after',
			]
		);

		$this->add_control(
			'dot_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Dot', 'glozin-addons' ),
			]	
		);

		$this->add_control(
			'dot_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__line span' => 'color: {{VALUE}}',
					'{{WRAPPER}} .glozin-buy-x-get-y__line span::before' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .glozin-buy-x-get-y__line span::after' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'dot_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__line span' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'dot_width',
			[
				'label' => esc_html__( 'Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__line span' => 'width: {{SIZE}}{{UNIT}}'
				],
			]
		);

		$this->add_responsive_control(
			'dot_height',
			[
				'label' => esc_html__( 'Height', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__line span' => 'height: {{SIZE}}{{UNIT}}'
				],
			]
		);

		$this->add_control(
			'icon_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Icon', 'glozin-addons' ),
			]	
		);

		$this->add_responsive_control(
			'icon_width',
			[
				'label' => esc_html__( 'Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__line span::before' => 'width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .glozin-buy-x-get-y__line span::after' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'icon_height',
			[
				'label' => esc_html__( 'Height', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__line span::before' => 'height: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .glozin-buy-x-get-y__line span::after' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);
        
        $this->end_controls_section();

		$this->start_controls_section(
			'section_product_style',
			[
				'label'     => __( 'Product', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_responsive_control(
			'products_gap',
			[
				'label' => esc_html__( 'Distance between products', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__products' => 'gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'product_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__product' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'product_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-buy-x-get-y__product' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'product_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-buy-x-get-y__product' => 'border-start-start-radius: {{TOP}}{{UNIT}}; border-start-end-radius: {{RIGHT}}{{UNIT}}; border-end-end-radius: {{BOTTOM}}{{UNIT}}; border-end-start-radius: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'product_border',
				'label' => esc_html__( 'Border', 'glozin-addons' ),
				'selector' => '{{WRAPPER}} .glozin-buy-x-get-y__product',
			]
		);

		$this->add_responsive_control(
			'product_gap',
			[
				'label' => esc_html__( 'Gap', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__product' => 'gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'badge_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Product Badge', 'glozin-addons' ),
				'separator' => 'before',
			]	
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'product_badge_typography',
				'selector' => '{{WRAPPER}} .glozin-buy-x-get-y__product .woocommerce-badge',
			]
		);

		$this->add_control(
			'product_badge_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__product .woocommerce-badge' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'product_badge_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__product .woocommerce-badge' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'product_badge_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-buy-x-get-y__product .woocommerce-badge' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'product_badge_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-buy-x-get-y__product .woocommerce-badge' => 'border-start-start-radius: {{TOP}}{{UNIT}}; border-start-end-radius: {{RIGHT}}{{UNIT}}; border-end-end-radius: {{BOTTOM}}{{UNIT}}; border-end-start-radius: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'product_badge_border',
				'label' => esc_html__( 'Border', 'glozin-addons' ),
				'selector' => '{{WRAPPER}} .glozin-buy-x-get-y__product .woocommerce-badge',
			]
		);

		$this->add_control(
			'thumbnail_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Thumbnail', 'glozin-addons' ),
				'separator' => 'before',
			]	
		);

		$this->add_responsive_control(
			'product_thumbnail_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-buy-x-get-y__product .product-thumbnail img' => 'border-start-start-radius: {{TOP}}{{UNIT}}; border-start-end-radius: {{RIGHT}}{{UNIT}}; border-end-end-radius: {{BOTTOM}}{{UNIT}}; border-end-start-radius: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'product_summary_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Product Summary', 'glozin-addons' ),
				'separator' => 'before',
			]	
		);

		$this->add_responsive_control(
			'product_summary_gap',
			[
				'label' => esc_html__( 'Gap', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__product-summary' => 'gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'title_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Product Title', 'glozin-addons' ),
				'separator' => 'before',
			]	
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'product_title_typography',
				'selector' => '{{WRAPPER}} .glozin-buy-x-get-y__product-summary .woocommerce-loop-product__title a',
			]
		);

		$this->add_control(
			'product_title_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__product-summary .woocommerce-loop-product__title a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'product_title_color_hover',
			[
				'label' => esc_html__( 'Color Hover', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__product-summary .woocommerce-loop-product__title a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'quantity_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Product Quantity', 'glozin-addons' ),
				'separator' => 'before',
			]	
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'product_quantity_typography',
				'selector' => '{{WRAPPER}} .glozin-buy-x-get-y__product-summary .qty',
			]
		);

		$this->add_control(
			'product_quantity_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__product-summary .qty' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'select_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Product Select', 'glozin-addons' ),
				'separator' => 'before',
			]	
		);

		$this->add_control(
			'product_select_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__product-summary .attributes select' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'product_select_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__product-summary .attributes select' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'product_select_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-buy-x-get-y__product .attributes select' => 'border-start-start-radius: {{TOP}}{{UNIT}}; border-start-end-radius: {{RIGHT}}{{UNIT}}; border-end-end-radius: {{BOTTOM}}{{UNIT}}; border-end-start-radius: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'product_select_border',
				'label' => esc_html__( 'Border', 'glozin-addons' ),
				'selector' => '{{WRAPPER}} .glozin-buy-x-get-y__product .attributes select',
			]
		);

		$this->add_control(
			'price_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Product Price', 'glozin-addons' ),
				'separator' => 'before',
			]	
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'product_price_typography',
				'selector' => '{{WRAPPER}} .glozin-buy-x-get-y__product-summary .price',
			]
		);

		$this->add_control(
			'product_price_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__product-summary .price' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'product_price_column_gap',
			[
				'label' => esc_html__( 'Column Gap', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__product-summary .price' => 'column-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'product_price_row_gap',
			[
				'label' => esc_html__( 'Row Gap', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__product-summary .price' => 'row-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'price_sales_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Product Sales Price', 'glozin-addons' ),
			]	
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'product_price_sales_typography',
				'selector' => '{{WRAPPER}} .glozin-buy-x-get-y__product-summary .price ins',
			]
		);

		$this->add_control(
			'product_price_sales_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__product-summary .price ins' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'price_old_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Product Old Price', 'glozin-addons' ),
			]	
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'product_price_old_typography',
				'selector' => '{{WRAPPER}} .glozin-buy-x-get-y__product-summary .price del',
			]
		);

		$this->add_control(
			'product_price_old_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__product-summary .price del' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'price_text_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Total text', 'glozin-addons' ),
			]	
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'product_price_text_typography',
				'selector' => '{{WRAPPER}} .glozin-buy-x-get-y__product-summary .price-label',
			]
		);

		$this->add_control(
			'product_price_text_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__product-summary .price-label' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'product_price_text_gap',
			[
				'label' => esc_html__( 'Gap', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__product-summary .price-wrapper' => 'gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

        $this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label' => esc_html__( 'Button', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'button_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-buy-x-get-y__button' => 'margin-top: {{TOP}}{{UNIT}}; margin-inline-end: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}}; margin-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-buy-x-get-y__button button' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-buy-x-get-y__button button' => 'border-start-start-radius: {{TOP}}{{UNIT}}; border-start-end-radius: {{RIGHT}}{{UNIT}}; border-end-end-radius: {{BOTTOM}}{{UNIT}}; border-end-start-radius: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .glozin-buy-x-get-y__button button',
			]
		);

		$this->add_responsive_control(
			'button_border_width',
			[
				'label' => esc_html__( 'Border Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'size_units' => [ 'px', 'em', 'rem' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__button button' => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => __( 'Normal', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'button_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__button button' => '--gz-button-bg-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__button button' => '--gz-button-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__button button' => '--gz-button-border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => __( 'Hover', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'button_background_hover_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__button button' => '--gz-button-bg-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__button button' => '--gz-button-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__button button' => '--gz-button-border-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_effect_hover_color',
			[
				'label'     => __( 'Background Effect Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-buy-x-get-y__button button' => '--gz-button-eff-bg-color-hover: {{VALUE}};',
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

		if ( ! $product ) {
			return;
		}

        if ( \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
            $this->get_buy_x_get_y_html( $product );
        } else {
		    do_action( 'glozin_buy_x_get_y_elementor' );
        }
	}

    public function get_buy_x_get_y_html( $product ) {
        ?>
            <div class="glozin-buy-x-get-y rounded-5 border">
				<div class="glozin-buy-x-get-y__heading text-dark fs-18 fw-semibold mb-20">Special Deals — Buy 1 Get 1 💥</div>
				<div class="glozin-buy-x-get-y__products main-products position-relative d-flex flex-column gap-15">
					<div class="glozin-buy-x-get-y__product position-relative d-flex gap-15 main simple">
						<div class="product-thumbnail position-relative">
							<?php echo $product->get_image(); ?>
						</div>
						<div class="glozin-buy-x-get-y__product-summary d-flex flex-column gap-10 flex-1">
							<div class="d-flex flex-column flex-xl-row-reverse align-items-center justify-content-between gap-5 gap-xl-10">
								<div class="woocommerce-badge onsale gap-3 flex-shrink-0">Buy 1</div>
								<h2 class="woocommerce-loop-product__title m-0 text-dark fs-15 fw-semibold">
									<a class="woocommerce-LoopProduct-link woocommerce-loop-product__link" href="#">Back Printed T-Shirt</a>
								</h2>
							</div>
							<div class="qty fs-12 text-dark fw-medium" data-qty="1">Qty: 1</div>
							<div class="price-wrapper d-flex align-items-center gap-10">
								<div class="price-label text-dark fw-semibold">Total:</div>
									<div class="price">
										<del aria-hidden="true"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>180.00</span></del>
										<span class="screen-reader-text">Original price was: $180.00.</span>
										<ins aria-hidden="true"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>89.00</span></ins>
										<span class="screen-reader-text">Current price is: $89.00.</span>
									</div>
							</div>
						</div>
					</div>
				</div>
				<div class="glozin-buy-x-get-y__line d-flex align-items-center justify-content-center position-relative mt-15 mb-15">
					<span class="plus-icon position-relative d-flex align-items-center justify-content-center rounded-100"></span>
				</div>
				<div class="glozin-buy-x-get-y__products sub-products position-relative d-flex flex-column gap-15">
					<div class="glozin-buy-x-get-y__product position-relative d-flex gap-15 sub variable">
						<div class="product-thumbnail position-relative">
							<?php echo $product->get_image(); ?>
						</div>
						<div class="glozin-buy-x-get-y__product-summary d-flex flex-column gap-10 flex-1">
							<div class="d-flex flex-column flex-xl-row-reverse align-items-center justify-content-between gap-5 gap-xl-10">
								<div class="woocommerce-badge onsale gap-3 flex-shrink-0">Get 1 Off 10%</div>
								<h3 class="woocommerce-loop-product__title m-0 text-dark fs-15 fw-semibold">
									<a class="woocommerce-LoopProduct-link woocommerce-loop-product__link" href="#">Braid Detailed Sleeveless Flow Top</a>
								</h3>
							</div>
							<div class="attributes d-flex align-items-center gap-10">
								<form>
									<select name="variation_id">
										<option>Select an option</option>
										<option>Azure / L</option>
										<option>Azure / M</option>
										<option selected="selected">Azure / S</option>
										<option>Pink / L</option>
										<option>Pink / M</option>
										<option>Pink / S</option>
										<option>Gray / L</option>
										<option>Gray / M</option>
										<option>Gray / S</option>
									</select>
								</form>
								<div class="qty fs-12 text-dark fw-medium" data-qty="1">Qty: 1</div>
							</div>
							<div class="price-wrapper d-flex align-items-center gap-10">
								<div class="price-label text-dark fw-semibold">Total:</div>
								<div class="price" data-price="139.5">
									<del><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>155.00</bdi></span></del>
									<ins><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>139.50</bdi></span></ins>
								</div>
							</div>
						</div>
					</div>
					<div class="glozin-buy-x-get-y__product position-relative d-flex gap-15 sub variable">
						<div class="product-thumbnail position-relative">
							<?php echo $product->get_image(); ?>
						</div>
						<div class="glozin-buy-x-get-y__product-summary d-flex flex-column gap-10 flex-1">
							<div class="d-flex flex-column flex-xl-row-reverse align-items-center justify-content-between gap-5 gap-xl-10">
								<div class="woocommerce-badge onsale gap-3 flex-shrink-0">Get 2 Off <span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>10.00</span></div>
								<h3 class="woocommerce-loop-product__title m-0 text-dark fs-15 fw-semibold">
									<a class="woocommerce-LoopProduct-link woocommerce-loop-product__link" href="#">Balloon Sleeve Blouse - Square Neck</a>
								</h3>
							</div>
							<div class="attributes d-flex align-items-center gap-10">
								<form>
									<select name="variation_id">
										<option>Select an option</option>
										<option>Blue / L</option>
										<option>Blue / M</option>
										<option selected="selected">Blue / S</option>
										<option>White / L</option>
										<option>White / M</option>
										<option>White / S</option>
									</select>
								</form>
								<div class="qty fs-12 text-dark fw-medium" data-qty="2">Qty: 2</div>
							</div>
							<div class="price-wrapper d-flex align-items-center gap-10">
								<div class="price-label text-dark fw-semibold">Total:</div>
								<div class="price">
									<del>
										<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>360.00</bdi></span>
									</del>
									<ins>
										<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>350.00</bdi></span>
									</ins>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="glozin-buy-x-get-y__button mt-20">
					<form class="buy-x-get-y__form cart" action="#" method="post" enctype="multipart/form-data">
						<button type="submit" name="glozin_buy_x_get_y_add_to_cart" value="" class="gz-button glozin-buy-x-get-y-add-to-cart">Grab This Deals</button>
					</form>
				</div>
			</div>
        <?php
    }
}
