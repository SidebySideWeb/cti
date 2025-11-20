<?php
namespace Glozin\Addons\Elementor\Builder\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Linked_Variant extends Widget_Base {
	use \Glozin\Addons\Elementor\Builder\Traits\Product_Id_Trait;

	public function get_name() {
		return 'glozin-linked-variant';
	}

	public function get_title() {
		return esc_html__( '[Glozin] Linked Variant', 'glozin-addons' );
	}

	public function get_icon() {
		return 'eicon-woocommerce';
	}

	public function get_categories() {
		return ['glozin-addons-product'];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'linked', 'variant', 'product' ];
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
			'section_attribute_style',
			[
				'label'     => __( 'Attributes', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_responsive_control(
			'attribute_spacing',
			[
				'label' => esc_html__( 'Spacing bottom', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-linked-variant__attribute' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'attribute_labels',
			[
				'label'     => esc_html__( 'Labels', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'attribute_labels_typography',
				'selector' => '{{WRAPPER}} .glozin-linked-variant__attribute-label',
			]
		);

		$this->add_control(
			'attribute_labels_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-linked-variant__attribute-label' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'attribute_labels_spacing',
			[
				'label' => esc_html__( 'Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-linked-variant__attribute-label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'attribute_values',
			[
				'label'     => esc_html__( 'Values', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'attribute_labels_gap',
			[
				'label' => esc_html__( 'Gap', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-linked-variant__attribute-value' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
			'attribute_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-linked-variant__attribute-value .wcboost-variation-swatches__item' => 'border-color: {{VALUE}};',
				],
			]
		);

        $this->add_control(
			'attribute_selected_heading',
			[
				'label'     => esc_html__( 'Selected', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'attribute_selected_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-linked-variant__attribute-value .wcboost-variation-swatches__item:hover' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .glozin-linked-variant__attribute-value .wcboost-variation-swatches__item.selected' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_attribute_image_style',
			[
				'label'     => __( 'Image Attribute', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'attribute_image_width',
			[
				'label' => esc_html__( 'Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--image .wcboost-variation-swatches__item' => '--wcboost-swatches-item-width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'attribute_image_height',
			[
				'label' => esc_html__( 'Height', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--image .wcboost-variation-swatches__item' => '--wcboost-swatches-item-height: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'attribute_image_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--image .wcboost-variation-swatches__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--image .wcboost-variation-swatches__item img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--image .wcboost-variation-swatches__item' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--image .wcboost-variation-swatches__item img' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

        $this->start_controls_section(
			'section_color_attribute_style',
			[
				'label'     => __( 'Color Attribute', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_responsive_control(
			'attribute_color_width',
			[
				'label' => esc_html__( 'Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--color .wcboost-variation-swatches__item' => '--wcboost-swatches-item-width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'attribute_color_height',
			[
				'label' => esc_html__( 'Height', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--color .wcboost-variation-swatches__item' => '--wcboost-swatches-item-height: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'attribute_color_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--color .wcboost-variation-swatches__item' => '--wcboost-swatches-item-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--color .wcboost-variation-swatches__item' => '--wcboost-swatches-item-padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'attribute_color_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--color .wcboost-variation-swatches__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--color .wcboost-variation-swatches__item .wcboost-variation-swatches__name' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--color .wcboost-variation-swatches__item' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--color .wcboost-variation-swatches__item .wcboost-variation-swatches__name' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_label_attribute_style',
			[
				'label'     => __( 'Label Attribute', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_responsive_control(
			'attribute_label_width',
			[
				'label' => esc_html__( 'Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--label .wcboost-variation-swatches__item' => '--wcboost-swatches-item-width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'attribute_label_height',
			[
				'label' => esc_html__( 'Height', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--label .wcboost-variation-swatches__item' => '--wcboost-swatches-item-height: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'attribute_label_typography',
				'selector' => '{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--label .wcboost-variation-swatches__item',
			]
		);

		$this->add_control(
			'attribute_label_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--label .wcboost-variation-swatches__item' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'attribute_label_selected_color',
			[
				'label' => esc_html__( 'Selected Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--label .wcboost-variation-swatches__item:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--label .wcboost-variation-swatches__item.selected' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'attribute_label_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--label .wcboost-variation-swatches__item' => '--wcboost-swatches-item-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--label .wcboost-variation-swatches__item' => '--wcboost-swatches-item-padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'attribute_label_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--label .wcboost-variation-swatches__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--label .wcboost-variation-swatches__item .wcboost-variation-swatches__name' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--label .wcboost-variation-swatches__item' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--label .wcboost-variation-swatches__item .wcboost-variation-swatches__name' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_button_attribute_style',
			[
				'label'     => __( 'Button Attribute', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_responsive_control(
			'attribute_button_width',
			[
				'label' => esc_html__( 'Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--button .wcboost-variation-swatches__item' => '--wcboost-swatches-item-width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'attribute_button_height',
			[
				'label' => esc_html__( 'Height', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--button .wcboost-variation-swatches__item' => '--wcboost-swatches-item-height: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'attribute_button_typography',
				'selector' => '{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--button .wcboost-variation-swatches__item',
			]
		);

		$this->add_control(
			'attribute_button_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--button .wcboost-variation-swatches__item' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'attribute_button_selected_color',
			[
				'label' => esc_html__( 'Selected Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--button .wcboost-variation-swatches__item:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--button .wcboost-variation-swatches__item.selected' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'attribute_button_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--button .wcboost-variation-swatches__item' => '--wcboost-swatches-item-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--button .wcboost-variation-swatches__item' => '--wcboost-swatches-item-padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'attribute_button_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--button .wcboost-variation-swatches__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--button .wcboost-variation-swatches__item .wcboost-variation-swatches__name' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--button .wcboost-variation-swatches__item' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--button .wcboost-variation-swatches__item .wcboost-variation-swatches__name' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_select_attribute_style',
			[
				'label'     => __( 'Select Attribute', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'attribute_select_min_width',
			[
				'label' => esc_html__( 'Min Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--select .glozin-linked-variant__select' => 'min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'attribute_select_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--select .glozin-linked-variant__select' => '--gz-input-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'attribute_select_border_width',
			[
				'label' => esc_html__( 'Border Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--select .glozin-linked-variant__select' => '--gz-input-border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'attribute_select_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--select .glozin-linked-variant__select' => '--gz-input-border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'attribute_select_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--select .glozin-linked-variant__select' => '--gz-input-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--select .glozin-linked-variant__select' => '--gz-input-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'attribute_select_hover_heading',
			[
				'label'     => esc_html__( 'Hover', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'attribute_select_hover_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--select .glozin-linked-variant__select:hover' => '--gz-input-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'attribute_select_hover_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-linked-variant__attribute-value.wcboost-variation-swatches--select .glozin-linked-variant__select' => '--gz-input-border-color-hover: {{VALUE}};',
				],
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
		$settings = $this->get_settings_for_display();

		global $product;

		$product = $this->get_product();

		if ( ! $product ) {
			return;
		}

        if ( \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
            $this->get_html( $product );
            return;
        }

        do_action( 'glozin_linked_variant_elementor' );
	}

    public function get_html( $product ) {
        ?>
		<div id="glozin-linked-variant" class="linked-variant">
            <div class="glozin-linked-variant__attribute">
                <div class="glozin-linked-variant__attribute-label text-dark mb-10 fw-semibold heading-letter-spacing lh-normal"><?php esc_html_e( 'More colors:', 'glozin-addons' ); ?></div>
                <div class="glozin-linked-variant__attribute-value wcboost-variation-swatches--image wcboost-variation-swatches-- wcboost-variation-swatches--has-tooltip d-flex gap-10">
                    <a class="wcboost-variation-swatches__item selected" href="" aria-label="Blue" data-value="Blue" tabindex="0" role="button">
                        <?php echo $product->get_image(); ?>
                        <span class="wcboost-variation-swatches__name"><?php esc_html_e( 'Blue', 'glozin-addons' ); ?></span>
                    </a>
                    <a class="wcboost-variation-swatches__item" href="" aria-label="Gray" data-value="Gray" tabindex="0" role="button">
                        <?php echo $product->get_image(); ?>
                        <span class="wcboost-variation-swatches__name"><?php esc_html_e( 'Gray', 'glozin-addons' ); ?></span>
                    </a>
                </div>
            </div>
		</div>
	<?php
    }
}