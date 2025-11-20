<?php
namespace Glozin\Addons\Elementor\Builder\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_Add_To_Cart_Form extends Widget_Base {
	use \Glozin\Addons\Elementor\Builder\Traits\Product_Id_Trait;

	public function get_name() {
		return 'glozin-product-add-to-cart-form';
	}

	public function get_title() {
		return esc_html__( '[Glozin] Product Add To Cart Form', 'glozin-addons' );
	}

	public function get_icon() {
		return 'eicon-product-add-to-cart';
	}

	public function get_categories() {
		return ['glozin-addons-product'];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'cart', 'product', 'button', 'add to cart' ];
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
		return 'elementor-widget-' . $this->get_name() . ' product-gallery-summary entry-summary';
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
		$this->swatches_style();
		$this->quantity_style();
		$this->add_to_cart_style();
		$this->featured_icon_style();
		$this->buy_now_style();
	}

	protected function swatches_style() {
		$this->start_controls_section(
			'section_swatches_style',
			[
				'label'     => __( 'Swatches', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'swatches_spacing',
			[
				'label' => esc_html__( 'Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} table.variations' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'swatches_labels',
			[
				'label'     => esc_html__( 'Labels', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'swatches_labels_typography',
				'selector' => '{{WRAPPER}} table.variations .label',
			]
		);

		$this->add_control(
			'swatches_labels_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.variations .label' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'swatches_labels_spacing',
			[
				'label' => esc_html__( 'Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} table.variations .label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'swatches_values',
			[
				'label'     => esc_html__( 'Values', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'swatches_labels_gap',
			[
				'label' => esc_html__( 'Gap', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} table.variations .value ul' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'swatches_values_spacing',
			[
				'label' => esc_html__( 'Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} table.variations .value' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_swatches_color_style',
			[
				'label'     => __( 'Swatches Color', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'swatches_color_width',
			[
				'label' => esc_html__( 'Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--color .wcboost-variation-swatches__item' => '--wcboost-swatches-item-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'swatches_color_height',
			[
				'label' => esc_html__( 'Height', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--color .wcboost-variation-swatches__item' => '--wcboost-swatches-item-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'swatches_color_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--color .wcboost-variation-swatches__item:not(.selected)' => 'border-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'swatches_color_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--color .wcboost-variation-swatches__item' => '--wcboost-swatches-item-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} table.variations .wcboost-variation-swatches--color .wcboost-variation-swatches__item' => '--wcboost-swatches-item-padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'swatches_color_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--color .wcboost-variation-swatches__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--color .wcboost-variation-swatches__item .wcboost-variation-swatches__name' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} table.variations .wcboost-variation-swatches--color .wcboost-variation-swatches__item' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
					'.rtl {{WRAPPER}} table.variations .wcboost-variation-swatches--color .wcboost-variation-swatches__item .wcboost-variation-swatches__name' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'swatches_color_selected_heading',
			[
				'label'     => esc_html__( 'Selected', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'swatches_color_selected_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--color .wcboost-variation-swatches__item.selected' => 'border-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'swatches_color_disabled_heading',
			[
				'label'     => esc_html__( 'Disabled', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'swatches_color_disabled_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--color .wcboost-variation-swatches__item.disabled' => 'border-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'swatches_color_disabled_line_color',
			[
				'label' => esc_html__( 'Line Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--color .wcboost-variation-swatches__item.disabled .wcboost-variation-swatches__name::before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_swatches_image_style',
			[
				'label'     => __( 'Swatches Image', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'swatches_image_width',
			[
				'label' => esc_html__( 'Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--image .wcboost-variation-swatches__item' => '--wcboost-swatches-item-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'swatches_image_height',
			[
				'label' => esc_html__( 'Height', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--image .wcboost-variation-swatches__item' => '--wcboost-swatches-item-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'swatches_image_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--image .wcboost-variation-swatches__item:not(.selected)' => 'border-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'swatches_image_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--image .wcboost-variation-swatches__item' => '--wcboost-swatches-item-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} table.variations .wcboost-variation-swatches--image .wcboost-variation-swatches__item' => '--wcboost-swatches-item-padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'swatches_image_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--image .wcboost-variation-swatches__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--image .wcboost-variation-swatches__item img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} table.variations .wcboost-variation-swatches--image .wcboost-variation-swatches__item' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
					'.rtl {{WRAPPER}} table.variations .wcboost-variation-swatches--image .wcboost-variation-swatches__item img' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'swatches_image_selected_heading',
			[
				'label'     => esc_html__( 'Selected', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'swatches_image_selected_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--image .wcboost-variation-swatches__item.selected' => 'border-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'swatches_image_disabled_heading',
			[
				'label'     => esc_html__( 'Disabled', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'swatches_image_disabled_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--image .wcboost-variation-swatches__item.disabled' => 'border-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'swatches_image_disabled_line_color',
			[
				'label' => esc_html__( 'Line Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--image .wcboost-variation-swatches__item.disabled .wcboost-variation-swatches__name::before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_swatches_label_style',
			[
				'label'     => __( 'Swatches Label', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'swatches_label_min_width',
			[
				'label' => esc_html__( 'Min Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--label .wcboost-variation-swatches__item' => '--wcboost-swatches-item-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'swatches_label_height',
			[
				'label' => esc_html__( 'Height', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--label .wcboost-variation-swatches__item' => '--wcboost-swatches-item-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'swatches_label_typography',
				'selector' => '{{WRAPPER}} table.variations .wcboost-variation-swatches--label .wcboost-variation-swatches__item .wcboost-variation-swatches__name',
			]
		);

		$this->add_control(
			'swatches_label_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--label .wcboost-variation-swatches__item:not(.selected)' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'swatches_label_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--label .wcboost-variation-swatches__item:not(.selected)' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'swatches_label_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--label .wcboost-variation-swatches__item:not(.selected)' => 'border-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'swatches_label_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--label .wcboost-variation-swatches__item' => '--wcboost-swatches-item-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} table.variations .wcboost-variation-swatches--label .wcboost-variation-swatches__item' => '--wcboost-swatches-item-padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'swatches_label_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--label .wcboost-variation-swatches__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--label .wcboost-variation-swatches__item .wcboost-variation-swatches__name' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} table.variations .wcboost-variation-swatches--label .wcboost-variation-swatches__item' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
					'.rtl {{WRAPPER}} table.variations .wcboost-variation-swatches--label .wcboost-variation-swatches__item .wcboost-variation-swatches__name' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'swatches_label_selected_heading',
			[
				'label'     => esc_html__( 'Selected', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'swatches_label_selected_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--label .wcboost-variation-swatches__item.selected' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'swatches_label_selected_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--label .wcboost-variation-swatches__item.selected' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'swatches_label_selected_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--label .wcboost-variation-swatches__item.selected' => 'border-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'swatches_label_disabled_heading',
			[
				'label'     => esc_html__( 'Disabled', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'swatches_label_disabled_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--label .wcboost-variation-swatches__item.disabled' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'swatches_label_disabled_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--label .wcboost-variation-swatches__item.disabled' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'swatches_label_disabled_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--label .wcboost-variation-swatches__item.disabled' => 'border-color: {{VALUE}} !important;',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_swatches_button_style',
			[
				'label'     => __( 'Swatches Label', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'swatches_button_min_width',
			[
				'label' => esc_html__( 'Min Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--button .wcboost-variation-swatches__item' => '--wcboost-swatches-item-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'swatches_button_min_height',
			[
				'label' => esc_html__( 'Min Height', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--button .wcboost-variation-swatches__item' => '--wcboost-swatches-item-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'swatches_button_typography',
				'selector' => '{{WRAPPER}} table.variations .wcboost-variation-swatches--button .wcboost-variation-swatches__item .wcboost-variation-swatches__name',
			]
		);

		$this->add_control(
			'swatches_button_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--button .wcboost-variation-swatches__item:not(.selected)' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'swatches_button_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--button .wcboost-variation-swatches__item:not(.selected)' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'swatches_button_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--button .wcboost-variation-swatches__item:not(.selected)' => 'border-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'swatches_button_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--button .wcboost-variation-swatches__item' => '--wcboost-swatches-item-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} table.variations .wcboost-variation-swatches--button .wcboost-variation-swatches__item' => '--wcboost-swatches-item-padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'swatches_button_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--button .wcboost-variation-swatches__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--button .wcboost-variation-swatches__item .wcboost-variation-swatches__name' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} table.variations .wcboost-variation-swatches--button .wcboost-variation-swatches__item' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
					'.rtl {{WRAPPER}} table.variations .wcboost-variation-swatches--button .wcboost-variation-swatches__item .wcboost-variation-swatches__name' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'swatches_button_selected_heading',
			[
				'label'     => esc_html__( 'Selected', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'swatches_button_selected_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--button .wcboost-variation-swatches__item.selected' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'swatches_button_selected_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--button .wcboost-variation-swatches__item.selected' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'swatches_button_selected_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--button .wcboost-variation-swatches__item.selected' => 'border-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'swatches_button_disabled_heading',
			[
				'label'     => esc_html__( 'Disabled', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'swatches_button_disabled_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--button .wcboost-variation-swatches__item.disabled' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'swatches_button_disabled_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--button .wcboost-variation-swatches__item.disabled' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'swatches_button_disabled_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.variations .wcboost-variation-swatches--button .wcboost-variation-swatches__item.disabled' => 'border-color: {{VALUE}} !important;',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_swatches_select_style',
			[
				'label'     => __( 'Swatches Select', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'swatches_select_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.variations .value select' => '--gz-input-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'swatches_select_border_width',
			[
				'label' => esc_html__( 'Border Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} table.variations .value select' => '--gz-input-border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'swatches_select_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.variations .value select' => '--gz-input-border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'swatches_select_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} table.variations .value select' => '--gz-input-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} table.variations .value select' => '--gz-input-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'swatches_select_hover_heading',
			[
				'label'     => esc_html__( 'Hover', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'swatches_select_hover_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.variations .value select:hover' => '--gz-input-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'swatches_select_hover_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.variations .value select' => '--gz-input-border-color-hover: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function quantity_style() {
		$this->start_controls_section(
			'section_quantity_style',
			[
				'label'     => __( 'Quantity', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'product_quantity_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .quantity' => 'color: {{VALUE}}',
					'{{WRAPPER}} .quantity input' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'product_quantity_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .quantity .glozin-qty-button:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'product_quantity_hover_background_color',
			[
				'label' => esc_html__( 'Icon background color on hover', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .quantity .glozin-qty-button:hover::before' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'product_quantity_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .quantity' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'product_quantity_border',
				'label' => esc_html__( 'Border', 'glozin-addons' ),
				'selector' => '{{WRAPPER}} .quantity',
			]
		);

		$this->add_responsive_control(
			'product_quantity_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .quantity' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .quantity' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_to_cart_style() {
		$this->start_controls_section(
			'section_add_to_cart_style',
			[
				'label'     => __( 'Add to cart button', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'addtocart_button_max_width',
			[
				'label' => esc_html__( 'Max Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .single_add_to_cart_button' => 'max-width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'addtocart_button_typography',
				'selector' => '{{WRAPPER}} .single_add_to_cart_button, {{WRAPPER}} .single_add_to_cart_button .price',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'addtocart_button_border',
				'label' => esc_html__( 'Border', 'glozin-addons' ),
				'selector' => '{{WRAPPER}} .single_add_to_cart_button',
			]
		);

		$this->add_responsive_control(
			'addtocart_button_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .single_add_to_cart_button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .single_add_to_cart_button' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'addtocart_button_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .single_add_to_cart_button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .single_add_to_cart_button' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'addtocart_button_style' );

		$this->start_controls_tab(
			'addtocart_button_normal',
			[
				'label' => __( 'Normal', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'addtocart_button_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .single_add_to_cart_button' => '--gz-button-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'addtocart_button_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .single_add_to_cart_button' => '--gz-button-bg-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'addtocart_button_hover',
			[
				'label' => __( 'Hover', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'addtocart_button_hover_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .single_add_to_cart_button' => '--gz-button-color-hover: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'addtocart_button_hover_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .single_add_to_cart_button' => '--gz-button-bg-color-hover: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'addtocart_button_hover_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .single_add_to_cart_button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'addtocart_button_background_effect_hover_color',
			[
				'label'     => __( 'Background Effect Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .single_add_to_cart_button' => '--gz-button-eff-bg-color-hover: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function featured_icon_style() {
		$this->start_controls_section(
			'section_featured_style',
			[
				'label'     => __( 'Featured icon button', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'featured_button_icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} form.cart .product-featured-icons .product-loop-button .glozin-svg-icon' => 'font-size: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'featured_button_width',
			[
				'label' => esc_html__( 'Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} form.cart .product-featured-icons .product-loop-button' => '--gz-button-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'featured_button_height',
			[
				'label' => esc_html__( 'Height', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} form.cart .product-featured-icons .product-loop-button' => '--gz-button-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'featured_button_border',
				'label' => esc_html__( 'Border', 'glozin-addons' ),
				'selector' => '{{WRAPPER}} form.cart .product-featured-icons .product-loop-button',
			]
		);

		$this->add_responsive_control(
			'featured_button_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} form.cart .product-featured-icons .product-loop-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} form.cart .product-featured-icons .product-loop-button' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'featured_button_style' );

		$this->start_controls_tab(
			'featured_button_normal',
			[
				'label' => __( 'Normal', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'featured_button_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.cart .product-featured-icons .product-loop-button' => '--gz-button-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'featured_button_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.cart .product-featured-icons .product-loop-button' => '--gz-button-bg-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'featured_button_hover',
			[
				'label' => __( 'Hover', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'featured_button_hover_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.cart .product-featured-icons .product-loop-button' => '--gz-button-color-hover: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'featured_button_hover_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.cart .product-featured-icons .product-loop-button' => '--gz-button-bg-color-hover: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'featured_button_hover_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.cart .product-featured-icons .product-loop-button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function buy_now_style() {
		$this->start_controls_section(
			'section_buy_now_style',
			[
				'label'     => __( 'Buy now button', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'buynow_button_icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .gz-buy-now-button .glozin-svg-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'buynow_button_typography',
				'selector' => '{{WRAPPER}} .gz-buy-now-button',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'buynow_button_border',
				'label' => esc_html__( 'Border', 'glozin-addons' ),
				'selector' => '{{WRAPPER}} .gz-buy-now-button',
			]
		);

		$this->add_responsive_control(
			'buynow_button_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .gz-buy-now-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .gz-buy-now-button' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'buynow_button_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .gz-buy-now-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .gz-buy-now-button' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'buynow_button_style' );

		$this->start_controls_tab(
			'buynow_button_normal',
			[
				'label' => __( 'Normal', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'buynow_button_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gz-buy-now-button' => '--gz-button-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'buynow_button_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gz-buy-now-button' => '--gz-button-bg-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'buynow_button_hover',
			[
				'label' => __( 'Hover', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'buynow_button_hover_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gz-buy-now-button' => '--gz-button-color-hover: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'buynow_button_hover_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gz-buy-now-button' => '--gz-button-bg-color-hover: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'buynow_button_hover_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gz-buy-now-button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'buynow_button_background_effect_hover_color',
			[
				'label'     => __( 'Background Effect Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gz-buy-now-button' => '--gz-button-eff-bg-color-hover: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

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
			$args =  array(
				'type' => 'simple',
				'limit' => 1,
				'orderby' => 'date',
				'order' => 'ASC',
			);

			$products = wc_get_products( $args );

			if( ! empty( $products ) ) {
				$product_id = $products[0]->get_id();
				setup_postdata( $product_id );
				$original_post = $GLOBALS['post'];
				$GLOBALS['post'] = get_post( $product_id );
				setup_postdata( $GLOBALS['post'] );
			}

			if( class_exists('\WCBoost\Wishlist\Frontend') ) {
				add_action( 'woocommerce_after_add_to_cart_button', array( \WCBoost\Wishlist\Frontend::instance(), 'single_add_to_wishlist_button' ), 21 );
			}
			if( class_exists('\WCBoost\ProductsCompare\Frontend') ) {
				add_action( 'woocommerce_after_add_to_cart_button', array( \WCBoost\ProductsCompare\Frontend::instance(), 'single_add_to_compare_button' ), 21 );
			}
			if( function_exists('wcboost_products_compare') ) {
				add_filter( 'wcboost_products_compare_single_add_to_compare_link', array( \Glozin\WooCommerce\Compare::instance(), 'single_add_to_compare_link' ), 20, 2 );
			}
			if( function_exists('wcboost_wishlist') ) {
				add_filter( 'wcboost_wishlist_single_add_to_wishlist_link', array( \Glozin\WooCommerce\Wishlist::instance(), 'wishlist_button_single_product' ), 20, 2 );
			}
		}

		if( function_exists('woocommerce_template_single_add_to_cart') ) {
			add_filter( 'woocommerce_available_variation', array( $this, 'data_product_variations' ), 10, 3 );
			add_action( 'woocommerce_grouped_product_columns', array( $this, 'grouped_product_columns' ), 10, 2 );
			add_action( 'wp_footer', [ $this, 'glozin_footer' ] );

			woocommerce_template_single_add_to_cart();
			remove_filter( 'woocommerce_available_variation', array( $this, 'data_product_variations' ), 10, 3 );
			do_action( 'glozin_single_add_to_cart_elementor' );
		}

		if ( \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
			if( function_exists('wcboost_products_compare') ) {
				remove_filter( 'wcboost_products_compare_single_add_to_compare_link', array( \Glozin\WooCommerce\Compare::instance(), 'single_add_to_compare_link' ), 20, 2 );
			}
			if( function_exists('wcboost_wishlist') ) {
				remove_filter( 'wcboost_wishlist_single_add_to_wishlist_link', array( \Glozin\WooCommerce\Wishlist::instance(), 'wishlist_button_single_product' ), 20, 2 );
			}
			if( class_exists('\WCBoost\Wishlist\Frontend') ) {
				remove_action( 'woocommerce_after_add_to_cart_button', array( \WCBoost\Wishlist\Frontend::instance(), 'single_add_to_wishlist_button' ), 21 );
			}
			if( class_exists('\WCBoost\ProductsCompare\Frontend') ) {
				remove_action( 'woocommerce_after_add_to_cart_button', array( \WCBoost\ProductsCompare\Frontend::instance(), 'single_add_to_compare_button' ), 21 );
			}
			remove_action( 'wp_footer', [ $this, 'glozin_footer' ] );
			$GLOBALS['post'] = $original_post;
		}
	}

	/**
	 * Data product variations
	 *
	 * @return void
	 */
	public function data_product_variations( $data, $product, $variation ) {
		global $product;
		$product = $this->get_product();

		if ( ! $product ) {
			return $data;
		}

		if( ! $product->is_type('variable') ) {
			return $data;
		}

		$availability = $variation->get_availability();
		$data['availability_status'] = $availability['availability'];
		$data['description'] = $variation->get_description();
		
		if ( $variation->is_on_sale() ) {
			$date_on_sale_to  = $variation->get_date_on_sale_to();
			$expire = '';
			if( ! empty( $date_on_sale_to ) ) {
				$now         = strtotime( current_time( 'Y-m-d H:i:s' ) );
				$expire_date = strtotime($date_on_sale_to);
				$expire      = ! empty( $expire_date ) ? $expire_date - $now : -1;
			}

			$expire = apply_filters( 'glozin_countdown_product_second', $expire );
			if( ! empty( $expire ) ) {
				$data['countdown_expire'] = $expire;
			}
			$data['is_on_sale'] = true;
		}

		return $data;
	}

	/**
	 * Position columns of product group
	 *
	 * @return array
	 */
	public function grouped_product_columns( $position, $product ) {
		$position = array(
			'label',
			'quantity',
			'price',
		);

		return $position;
	}

	public function get_swatches_html( $product ) {
	?>
		<table class="variations" cellspacing="0" role="presentation">
			<tbody>
				<tr>
					<th class="label"><label for="pa_color"><?php esc_html_e( 'Color', 'glozin-addons' ); ?></label></th>
					<td class="value">
						<div class="wcboost-variation-swatches wcboost-variation-swatches--color wcboost-variation-swatches--rounded wcboost-variation-swatches--has-tooltip">
							<ul class="wcboost-variation-swatches__wrapper" data-attribute_name="attribute_pa_color">
								<li class="wcboost-variation-swatches__item wcboost-variation-swatches__item-blue selected" style="--wcboost-swatches-item-color:#1e73be" aria-label="Blue" data-value="blue" tabindex="0" role="button">
									<span class="wcboost-variation-swatches__name"><?php esc_html_e( 'Blue', 'glozin-addons' ); ?></span>
								</li>
								<li class="wcboost-variation-swatches__item wcboost-variation-swatches__item-green" style="--wcboost-swatches-item-color:#81d742" aria-label="Green" data-value="green" tabindex="0" role="button">
									<span class="wcboost-variation-swatches__name"><?php esc_html_e( 'Green', 'glozin-addons' ); ?></span>
								</li>
								<li class="wcboost-variation-swatches__item wcboost-variation-swatches__item-red disabled" style="--wcboost-swatches-item-color:#dd3333" aria-label="Red" data-value="red" tabindex="0" role="button">
									<span class="wcboost-variation-swatches__name"><?php esc_html_e( 'Red', 'glozin-addons' ); ?></span>
								</li>
							</ul>
						</div>
					</td>
				</tr>
				<tr>
					<th class="label"><label for="pa_image"><?php esc_html_e( 'Image', 'glozin-addons' ); ?></label></th>
					<td class="value">
						<div class="wcboost-variation-swatches wcboost-variation-swatches--image wcboost-variation-swatches--rounded wcboost-variation-swatches--has-tooltip">
							<ul class="wcboost-variation-swatches__wrapper" data-attribute_name="attribute_pa_color">
								<li class="wcboost-variation-swatches__item wcboost-variation-swatches__item-gray selected" aria-label="Gray" data-value="gray" tabindex="0" role="button">
									<?php echo $product->get_image(); ?>
									<span class="wcboost-variation-swatches__name"><?php esc_html_e( 'Gray', 'glozin-addons' ); ?></span>
								</li>
								<li class="wcboost-variation-swatches__item wcboost-variation-swatches__item-green" aria-label="Green" data-value="green" tabindex="0" role="button">
									<?php echo $product->get_image(); ?>
									<span class="wcboost-variation-swatches__name"><?php esc_html_e( 'Green', 'glozin-addons' ); ?></span>
								</li>
								<li class="wcboost-variation-swatches__item wcboost-variation-swatches__item-yellow disabled" aria-label="Yellow" data-value="yellow" tabindex="0" role="button">
									<?php echo $product->get_image(); ?>
									<span class="wcboost-variation-swatches__name"><?php esc_html_e( 'Yellow', 'glozin-addons' ); ?></span>
								</li>
							</ul>
						</div>
					</td>
				</tr>
				<tr>
					<th class="label"><label for="pa_label"><?php esc_html_e( 'Label', 'glozin-addons' ); ?></label></th>
					<td class="value">
						<div class="wcboost-variation-swatches wcboost-variation-swatches--label wcboost-variation-swatches--rounded wcboost-variation-swatches--has-tooltip">
							<ul class="wcboost-variation-swatches__wrapper" data-attribute_name="attribute_logo">
								<li class="wcboost-variation-swatches__item wcboost-variation-swatches__item-Yes selected" aria-label="Yes" data-value="Yes" tabindex="0" role="button">
									<span class="wcboost-variation-swatches__name"><?php esc_html_e( 'Yes', 'glozin-addons' ); ?></span>
								</li>
								<li class="wcboost-variation-swatches__item wcboost-variation-swatches__item-No" aria-label="No" data-value="No" tabindex="0" role="button">
									<span class="wcboost-variation-swatches__name"><?php esc_html_e( 'No', 'glozin-addons' ); ?></span>
								</li>
								<li class="wcboost-variation-swatches__item wcboost-variation-swatches__item-Both disabled" aria-label="No" data-value="No" tabindex="0" role="button">
									<span class="wcboost-variation-swatches__name"><?php esc_html_e( 'Both', 'glozin-addons' ); ?></span>
								</li>
							</ul>
						</div>
					</td>
				</tr>
				<tr>
					<th class="label"><label for="pa_button"><?php esc_html_e( 'Button', 'glozin-addons' ); ?></label></th>
					<td class="value">
						<div class="wcboost-variation-swatches wcboost-variation-swatches--button wcboost-variation-swatches--rounded wcboost-variation-swatches--has-tooltip">
							<ul class="wcboost-variation-swatches__wrapper" data-attribute_name="attribute_pa_size">
								<li class="wcboost-variation-swatches__item wcboost-variation-swatches__item-l selected" aria-label="L" data-value="l" tabindex="0" role="button">
									<span class="wcboost-variation-swatches__name"><?php esc_html_e( 'L', 'glozin-addons' ); ?></span>
								</li>
								<li class="wcboost-variation-swatches__item wcboost-variation-swatches__item-m" aria-label="M" data-value="m" tabindex="0" role="button">
									<span class="wcboost-variation-swatches__name"><?php esc_html_e( 'M', 'glozin-addons' ); ?></span>
								</li>
								<li class="wcboost-variation-swatches__item wcboost-variation-swatches__item-s disabled" aria-label="S" data-value="s" tabindex="0" role="button">
									<span class="wcboost-variation-swatches__name"><?php esc_html_e( 'S', 'glozin-addons' ); ?></span>
								</li>
							</ul>
						</div>
					</td>
				</tr>
				<tr>
					<th class="label"><label for="pa_size"><?php esc_html_e( 'Select', 'glozin-addons' ); ?></label></th>
					<td class="value">
						<select id="pa_size" class="" name="attribute_pa_size" data-attribute_name="attribute_pa_size" data-show_option_none="yes">
							<option value=""><?php esc_html_e( 'Choose an option', 'glozin-addons' ); ?></option>
							<option value="l" class="attached enabled"><?php esc_html_e( 'L', 'glozin-addons' ); ?></option>
							<option value="m" selected="selected" class="attached enabled"><?php esc_html_e( 'M', 'glozin-addons' ); ?></option>
							<option value="s" class="attached enabled"><?php esc_html_e( 'S', 'glozin-addons' ); ?></option>
						</select>
						<a class="reset_variations" href="#"><?php esc_html_e( 'Clear', 'glozin-addons' ); ?></a>
					</td>
				</tr>
			</tbody>
		</table>
	<?php
	}

	public function glozin_footer() {
		do_action( 'glozin_footer_elementor' );
	}
}
