<?php
namespace Glozin\Addons\Elementor\Builder\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Dynamic_Pricing_Discounts extends Widget_Base {
	use \Glozin\Addons\Elementor\Builder\Traits\Product_Id_Trait;

	public function get_name() {
		return 'glozin-dynamic-pricing-discounts';
	}

	public function get_title() {
		return esc_html__( '[Glozin] Dynamic Pricing Discounts', 'glozin-addons' );
	}

	public function get_icon() {
		return 'eicon-woocommerce';
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'dynamic', 'pricing', 'discounts', 'products', 'product' ];
	}

	public function get_categories() {
		return [ 'glozin-addons-product' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_style',
			[
				'label'     => __( 'List', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_responsive_control(
			'gap',
			[
				'label' => esc_html__( 'Gap', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

        $this->add_control(
			'background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item' => 'background-color: {{VALUE}}',
				],
			]
		);

        $this->add_control(
			'active_background_color',
			[
				'label' => esc_html__( 'Active Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item.active' => 'background-color: {{VALUE}}',
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
					'{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'label' => esc_html__( 'Border', 'glozin-addons' ),
				'selector' => '{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item',
			]
		);

        $this->add_control(
			'active_border_color',
			[
				'label' => esc_html__( 'Active Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item.active' => 'border-color: {{VALUE}}',
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
					'{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item' => 'border-start-start-radius: {{TOP}}{{UNIT}}; border-start-end-radius: {{RIGHT}}{{UNIT}}; border-end-end-radius: {{BOTTOM}}{{UNIT}}; border-end-start-radius: {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
			'radio_heading',
			[
				'label' => esc_html__( 'Radio button', 'glozin-addon' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_control(
			'radio_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item__summary input[type="radio"]::before' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item__summary input[type="radio"]::after' => 'background-color: {{VALUE}}',
				],
			]
		);

        $this->add_control(
			'radio_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item:hover .dynamic-pricing-discounts-item__summary input[type="radio"]::before' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item:hover .dynamic-pricing-discounts-item__summary input[type="radio"]::after' => 'background-color: {{VALUE}}',
				],
			]
		);

        $this->add_control(
			'radio_active_color',
			[
				'label' => esc_html__( 'Active Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item__summary input[type="radio"]:checked::before' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item__summary input[type="radio"]:checked::after' => 'background-color: {{VALUE}}',
				],
			]
		);

        $this->add_responsive_control(
			'radio_spacing',
			[
				'label' => esc_html__( 'Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item input[type="radio"]' => 'margin-right: {{SIZE}}{{UNIT}}',
					'.glozin-rtl-smart {{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item input[type="radio"]' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: 0;',
				],
			]
		);

        $this->add_control(
			'title_heading',
			[
				'label' => esc_html__( 'Title', 'glozin-addon' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item__label',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item .dynamic-pricing-discounts-item__label' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'title_active_color',
			[
				'label' => esc_html__( 'Active Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item.active .dynamic-pricing-discounts-item__label' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'title_span_color',
			[
				'label' => esc_html__( 'Text Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item .dynamic-pricing-discounts-item__label span' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'title_span_active_color',
			[
				'label' => esc_html__( 'Active Text Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item.active .dynamic-pricing-discounts-item__label span' => 'color: {{VALUE}}',
				],
			]
		);

        $this->add_control(
			'save_heading',
			[
				'label' => esc_html__( 'Save', 'glozin-addon' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'save_typography',
				'selector' => '{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item__discount',
			]
		);

		$this->add_control(
			'save_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item__discount' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'save_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item__discount' => 'background-color: {{VALUE}}',
				],
			]
		);

        $this->add_responsive_control(
			'save_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item__discount' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'save_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item__discount' => 'border-start-start-radius: {{TOP}}{{UNIT}}; border-start-end-radius: {{RIGHT}}{{UNIT}}; border-end-end-radius: {{BOTTOM}}{{UNIT}}; border-end-start-radius: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'save_border',
				'label' => esc_html__( 'Border', 'glozin-addons' ),
				'selector' => '{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item__discount',
			]
		);

        $this->add_control(
			'price_text_heading',
			[
				'label' => esc_html__( 'Price Text', 'glozin-addon' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'price_text_typography',
				'selector' => '{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item__price > span:first-child',
			]
		);

		$this->add_control(
			'price_text_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item__price > span:first-child' => 'color: {{VALUE}}',
				],
			]
		);

        $this->add_responsive_control(
			'price_text_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item__price > span:first-child' => 'margin-top: {{TOP}}{{UNIT}}; margin-inline-end: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}}; margin-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
			'price_heading',
			[
				'label' => esc_html__( 'Price', 'glozin-addon' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_responsive_control(
			'price_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item__price .price' => 'margin-top: {{TOP}}{{UNIT}}; margin-inline-end: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}}; margin-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
			'price_sale_heading',
			[
				'label' => esc_html__( 'Sale Price', 'glozin-addon' ),
				'type' => Controls_Manager::HEADING,
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'price_sale_typography',
				'selector' => '{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item__price .price ins',
			]
		);

		$this->add_control(
			'price_sale_color',
			[
				'label' => esc_html__( 'Sale Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item__price .price ins' => 'color: {{VALUE}}',
				],
			]
		);

        $this->add_responsive_control(
			'price_sale_spacing',
			[
				'label' => esc_html__( 'Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item__price .price ins' => 'margin-left: {{SIZE}}{{UNIT}}',
					'.glozin-rtl-smart {{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item__price .price ins' => 'margin-right: {{SIZE}}{{UNIT}}; margin-left: 0;',
				],
			]
		);

        $this->add_control(
			'price_old_heading',
			[
				'label' => esc_html__( 'Old Price', 'glozin-addon' ),
				'type' => Controls_Manager::HEADING,
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'price_old_typography',
				'selector' => '{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item__price .price del',
			]
		);

		$this->add_control(
			'price_old_color',
			[
				'label' => esc_html__( 'Old Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--list .dynamic-pricing-discounts-item__price .price del' => 'color: {{VALUE}}; opacity: 1;',
				],
			]
		);
        
        $this->end_controls_section();

		$this->start_controls_section(
			'grid_section_style',
			[
				'label'     => __( 'Grid', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_responsive_control(
			'grid_gap',
			[
				'label' => esc_html__( 'Gap', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--grid' => 'gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

        $this->add_control(
			'grid_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item' => 'background-color: {{VALUE}}',
				],
			]
		);

        $this->add_control(
			'grid_active_background_color',
			[
				'label' => esc_html__( 'Active Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item.active' => 'background-color: {{VALUE}}',
				],
			]
		);

        $this->add_responsive_control(
			'grid_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'grid_border',
				'label' => esc_html__( 'Border', 'glozin-addons' ),
				'selector' => '{{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item',
			]
		);

        $this->add_control(
			'grid_active_border_color',
			[
				'label' => esc_html__( 'Active Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item.active' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'grid_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item' => 'border-start-start-radius: {{TOP}}{{UNIT}}; border-start-end-radius: {{RIGHT}}{{UNIT}}; border-end-end-radius: {{BOTTOM}}{{UNIT}}; border-end-start-radius: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'grid_badges_heading',
			[
				'label' => esc_html__( 'Badges', 'glozin-addon' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'grid_badges_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item__popular-badges' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'grid_badges_active_color',
			[
				'label' => esc_html__( 'Active Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item.active .dynamic-pricing-discounts-item__popular-badges' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'grid_badges_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item .dynamic-pricing-discounts-item__popular' => '--gz-popular-bg-color: {{VALUE}}',
				],
			]
		);

        $this->add_control(
			'grid_badges_active_background_color',
			[
				'label' => esc_html__( 'Active Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item.active .dynamic-pricing-discounts-item__popular' => '--gz-popular-bg-color: {{VALUE}}',
				],
			]
		);

        $this->add_control(
			'grid_radio_heading',
			[
				'label' => esc_html__( 'Radio button', 'glozin-addon' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_control(
			'grid_radio_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item__summary input[type="radio"]::before' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item__summary input[type="radio"]::after' => 'background-color: {{VALUE}}',
				],
			]
		);

        $this->add_control(
			'grid_radio_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item:hover .dynamic-pricing-discounts-item__summary input[type="radio"]::before' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item:hover .dynamic-pricing-discounts-item__summary input[type="radio"]::after' => 'background-color: {{VALUE}}',
				],
			]
		);

        $this->add_control(
			'grid_radio_active_color',
			[
				'label' => esc_html__( 'Active Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item__summary input[type="radio"]:checked::before' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item__summary input[type="radio"]:checked::after' => 'background-color: {{VALUE}}',
				],
			]
		);

        $this->add_responsive_control(
			'grid_radio_spacing',
			[
				'label' => esc_html__( 'Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item input[type="radio"]' => 'margin-right: {{SIZE}}{{UNIT}}',
					'.glozin-rtl-smart {{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item input[type="radio"]' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: 0;',
				],
			]
		);

        $this->add_control(
			'grid_image_heading',
			[
				'label' => esc_html__( 'Image', 'glozin-addon' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'grid_image_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item__thumbnail' => 'border-start-start-radius: {{TOP}}{{UNIT}}; border-start-end-radius: {{RIGHT}}{{UNIT}}; border-end-end-radius: {{BOTTOM}}{{UNIT}}; border-end-start-radius: {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
			'grid_save_heading',
			[
				'label' => esc_html__( 'Save', 'glozin-addon' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'grid_save_typography',
				'selector' => '{{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item__discount',
			]
		);

		$this->add_control(
			'grid_save_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item__discount' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'grid_save_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item__discount' => 'background-color: {{VALUE}}',
				],
			]
		);

        $this->add_responsive_control(
			'grid_save_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item__discount' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'grid_save_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item__discount' => 'border-start-start-radius: {{TOP}}{{UNIT}}; border-start-end-radius: {{RIGHT}}{{UNIT}}; border-end-end-radius: {{BOTTOM}}{{UNIT}}; border-end-start-radius: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'grid_save_border',
				'label' => esc_html__( 'Border', 'glozin-addons' ),
				'selector' => '{{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item__discount',
			]
		);

        $this->add_control(
			'grid_price_heading',
			[
				'label' => esc_html__( 'Price', 'glozin-addon' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_responsive_control(
			'grid_price_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item__price .price' => 'margin-top: {{TOP}}{{UNIT}}; margin-inline-end: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}}; margin-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
			'grid_price_sale_heading',
			[
				'label' => esc_html__( 'Sale Price', 'glozin-addon' ),
				'type' => Controls_Manager::HEADING,
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'grid_price_sale_typography',
				'selector' => '{{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item__price .price ins',
			]
		);

		$this->add_control(
			'grid_price_sale_color',
			[
				'label' => esc_html__( 'Sale Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item__price .price ins' => 'color: {{VALUE}}',
				],
			]
		);

        $this->add_responsive_control(
			'grid_price_sale_spacing',
			[
				'label' => esc_html__( 'Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item__price .price ins' => 'margin-left: {{SIZE}}{{UNIT}}',
					'.glozin-rtl-smart {{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item__price .price ins' => 'margin-right: {{SIZE}}{{UNIT}}; margin-left: 0;',
				],
			]
		);

        $this->add_control(
			'grid_price_old_heading',
			[
				'label' => esc_html__( 'Old Price', 'glozin-addon' ),
				'type' => Controls_Manager::HEADING,
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'grid_price_old_typography',
				'selector' => '{{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item__price .price del',
			]
		);

		$this->add_control(
			'grid_price_old_color',
			[
				'label' => esc_html__( 'Old Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-pricing-discounts--grid .dynamic-pricing-discounts-item__price .price del' => 'color: {{VALUE}}; opacity: 1;',
				],
			]
		);
        
        $this->end_controls_section();
	}

	protected function render() {
		global $product;

		$product = $this->get_product();

		if ( ! $product ) {
			return;
		}

        if ( \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
            $this->get_dynamic_pricing_discounts_html();
        } else {
		    do_action( 'glozin_dynamic_pricing_discounts_elementor' );
        }
	}

    public function get_dynamic_pricing_discounts_html() {
        ?>
            <div id="glozin-dynamic-pricing-discounts" class="dynamic-pricing-discounts dynamic-pricing-discounts--list">
                <div class="dynamic-pricing-discounts-item position-relative rounded-5 py-20 px-20 active">
					<div class="dynamic-pricing-discounts-item__summary d-flex align-items-center gap-10">
						<input type="radio" class="dynamic-pricing-discounts-item__quantity" name="dynamic_pricing_discounts_item_quantity" value="5">
						<div class="dynamic-pricing-discounts-item__label fs-15 text-dark fw-semibold heading-letter-spacing lh-normal">Buy from 3 to 5 items for<span> 10% Off per item.</span></div>
						<div class="dynamic-pricing-discounts-item__discount-price ms-auto text-right">
							<span class="dynamic-pricing-discounts-item__discount d-inline-block text-light fw-medium fs-12 lh-1">Save 10%</span>
							<input type="hidden" name="dynamic_pricing_discounts_item_discount" value="10">
							<div class="dynamic-pricing-discounts-item__price d-flex">
								<span class="price"><del aria-hidden="true"><span class="woocommerce-Price-amount amount"><bdi>$155.00</bdi></span></del> <span class="screen-reader-text">Original price was: $155.00.</span><ins aria-hidden="true"><span class="woocommerce-Price-amount amount"><bdi>$139.50</bdi></span></ins><span class="screen-reader-text">Current price is: $139.50.</span></span>
							</div>
						</div>
                    </div>
                </div>
                <div class="dynamic-pricing-discounts-item position-relative rounded-5 py-20 px-20">
                    <div class="dynamic-pricing-discounts-item__summary d-flex align-items-center gap-10">
						<input type="radio" class="dynamic-pricing-discounts-item__quantity" name="dynamic_pricing_discounts_item_quantity" value="0">
						<div class="dynamic-pricing-discounts-item__label fs-15 text-dark fw-semibold heading-letter-spacing lh-normal">Buy 15+ items for<span> 35% Off per item.</span></div>
						<div class="dynamic-pricing-discounts-item__discount-price ms-auto text-right">
							<span class="dynamic-pricing-discounts-item__discount d-inline-block text-light fw-medium fs-12 lh-1">Save 35%</span>
							<input type="hidden" name="dynamic_pricing_discounts_item_discount" value="35">
							<div class="dynamic-pricing-discounts-item__price d-flex">
								<span class="price"><del aria-hidden="true"><span class="woocommerce-Price-amount amount"><bdi>$155.00</bdi></span></del> <span class="screen-reader-text">Original price was: $155.00.</span><ins aria-hidden="true"><span class="woocommerce-Price-amount amount"><bdi>$100.75</bdi></span></ins><span class="screen-reader-text">Current price is: $100.75.</span></span>
							</div>
						</div>
					</div>
                </div>
            </div>
        <?php
    }
}
