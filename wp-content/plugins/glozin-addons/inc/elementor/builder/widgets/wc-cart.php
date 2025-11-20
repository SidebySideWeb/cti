<?php
namespace Glozin\Addons\Elementor\Builder\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Woocommerce Cart widget
 */
class WC_Cart extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-wc-cart';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( '[Glozin] Cart', 'glozin-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-woo-cart';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'glozin-addons-cart-page' ];
	}

	/**
	 * Get widget keywords.
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'woocommerce', 'cart', 'page', 'glozin-addons' ];
	}

	public function get_script_depends() {
		return [
			'wc-cart',
			'glozin-product-elementor-widgets'
		];
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
		return 'woocommerce-cart woocommerce-page elementor-widget-' . $this->get_name();
	}

	/**
	 * Register heading widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->content_controls();
		$this->style_controls();
	}

	protected function content_controls() {
		$this->start_controls_section(
			'section_general',
			[
				'label' => esc_html__( 'General', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'columns',
			[
				'label' => __( 'Columns', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => '2',
				'options' => array(
					'1' => __( '1 Column', 'glozin-addons' ),
					'2' => __( '2 Columns', 'glozin-addons' ),
				),
			]
		);

		$this->add_control(
			'hide_notices',
			[
				'label'        => esc_html__( 'Hide Notices', 'glozin-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Hide', 'glozin-addons' ),
				'label_off' => esc_html__( 'Show', 'glozin-addons' ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_order_summary',
			[ 'label' => esc_html__( 'Order Summary', 'glozin-addons' ) ]
		);

		$this->add_control(
			'cart_button',
			[
				'label' => esc_html__( 'Update Cart Button', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'cart_button_text',
			[
				'label' => __( 'Text', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Update cart', 'glozin-addons' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'cart_button_update_auto',
			[
				'label'        => esc_html__( 'Update Auto', 'glozin-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'frontend_available' => true,
				'selectors'  => [
					'{{WRAPPER}} .gz-button-update-cart' => 'display: none;',
				],
			]
		);

		$this->add_control(
			'coupon_button',
			[
				'label' => esc_html__( 'Apply Coupon Button', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'coupon_button_text',
			[
				'label' => __( 'Text', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Apply coupon', 'glozin-addons' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'order_notes_heading',
			[
				'label' => esc_html__( 'Order Notes', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'order_notes_label',
			[
				'label' => __( 'Label', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Order notes', 'glozin-addons' ),
				'label_block' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_totals',
			[ 'label' => esc_html__( 'Cart Totals', 'glozin-addons' ) ]
		);

		$this->add_control(
			'section_title_totals',
			[
				'label' => __( 'Section Title', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Cart totals', 'glozin-addons' ),
				'label_block' => true,
			]
		);

		$this->add_responsive_control(
			'section_title_totals_alignment',
			[
				'label'       => esc_html__( 'Alignment', 'glozin-addons' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'left'   => [
						'title' => esc_html__( 'Left', 'glozin-addons' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'glozin-addons' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'glozin-addons' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'     => '',
				'selectors'   => [
					'{{WRAPPER}} .cart_totals h2' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'shipping_button',
			[
				'label' => esc_html__( 'Update Shipping Button', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator'  => 'before',
			]
		);

		$this->add_control(
			'shipping_button_text',
			[
				'label' => __( 'Text', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Update', 'glozin-addons' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'checkout_button',
			[
				'label' => esc_html__( 'Checkout Button', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator'  => 'before',
			]
		);

		$this->add_control(
			'checkout_button_text',
			[
				'label' => __( 'Text', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Proceed to checkout', 'glozin-addons' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'extra_content_heading',
			[
				'label' => esc_html__( 'Extra Content', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator'  => 'before',
			]
		);

		$this->add_control(
			'extra_content_page_id',
			[
				'label' => __( 'Select the section located under the totals column', 'glozin-addons' ),
				'type' => 'glozin-autocomplete',
				'placeholder' => esc_html__( 'Click here and start typing...', 'glozin-addons' ),
				'default' => '',
				'multiple'    => false,
				'source'      => 'page,elementor_library',
				'sortable'    => true,
				'label_block' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_cart_empty',
			[ 'label' => esc_html__( 'Select Cart Empty', 'glozin-addons' ) ]
		);

		$this->add_control(
			'cart_empty_page_id',
			[
				'label' => __( 'Select Page', 'glozin-addons' ),
				'type' => 'glozin-autocomplete',
				'placeholder' => esc_html__( 'Click here and start typing...', 'glozin-addons' ),
				'default' => '',
				'multiple'    => false,
				'source'      => 'page,elementor_library',
				'sortable'    => true,
				'label_block' => true,
			]
		);

		$this->end_controls_section();
	}

	protected function style_controls() {
		$this->notices_style();

		$this->start_controls_section(
			'section_style_order_summary',
			[
				'label' => esc_html__( 'Order Summary', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'table_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.woocommerce-cart-form__contents' => '--gz-color__dark: {{VALUE}}; --gz-color__base: {{VALUE}}; --gz-link-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'table_hover_color',
			[
				'label'     => __( 'Hover Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.woocommerce-cart-form__contents' => '--gz-link-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'table_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.woocommerce-cart-form__contents' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'table_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.woocommerce-cart-form__contents' => '--gz-border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'table_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-cart-form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .woocommerce-cart-form' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'table_head_summary',
			[
				'label' => esc_html__( 'Head Summary', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'table_head_typography',
				'selector' => '{{WRAPPER}} table.woocommerce-cart-form__contents thead th',
			]
		);

		$this->add_control(
			'table_head_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.woocommerce-cart-form__contents thead th' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'table_head_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} table.woocommerce-cart-form__contents thead th' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} table.woocommerce-cart-form__contents thead th' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'table_head_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.woocommerce-cart-form__contents thead' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'table_head_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.woocommerce-cart-form__contents thead' => '--gz-border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'table_body_summary',
			[
				'label' => esc_html__( 'Body Summary', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'table_body_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.woocommerce-cart-form__contents tbody' => '--gz-color__dark: {{VALUE}}; --gz-color__base: {{VALUE}}; --gz-link-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'table_body_hover_color',
			[
				'label'     => __( 'Hover Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.woocommerce-cart-form__contents tbody' => '--gz-link-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'table_body_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.woocommerce-cart-form__contents tbody' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'table_body_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.woocommerce-cart-form__contents tbody' => '--gz-border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'table_body_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} table.woocommerce-cart-form__contents tbody td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} table.woocommerce-cart-form__contents tbody td' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'table_body_product_name',
			[
				'label' => esc_html__( 'Product Name', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'table_body_product_name_typography',
				'selector' => '{{WRAPPER}} table.woocommerce-cart-form__contents td.product-name a:not(.remove)',
			]
		);

		$this->add_control(
			'table_body_product_name_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.woocommerce-cart-form__contents td.product-name a:not(.remove)' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'table_body_product_name_hover_color',
			[
				'label'     => __( 'Hover Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.woocommerce-cart-form__contents td.product-name a:not(.remove):hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'table_body_product_remove',
			[
				'label' => esc_html__( 'Product Remove', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'table_body_product_remove_typography',
				'selector' => '{{WRAPPER}} table.woocommerce-cart-form__contents td.product-name a.remove',
			]
		);

		$this->add_control(
			'table_body_product_remove_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.woocommerce-cart-form__contents td.product-name a.remove' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'table_body_product_remove_hover_color',
			[
				'label'     => __( 'Hover Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.woocommerce-cart-form__contents td.product-name a.remove:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'table_body_product_price_heading',
			[
				'label' => esc_html__( 'Product Price', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'table_body_product_price_typography',
				'selector' => '{{WRAPPER}} table.woocommerce-cart-form__contents td.product-price',
			]
		);

		$this->add_control(
			'table_body_product_price_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.woocommerce-cart-form__contents td.product-price' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'table_body_product_save_price_style',
			[
				'label' => esc_html__( 'Save Price', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'table_body_product_save_price_typography',
				'selector' => '{{WRAPPER}} table.woocommerce-cart-form__contents td.product-price ins',
			]
		);

		$this->add_control(
			'table_body_product_save_price_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.woocommerce-cart-form__contents td.product-price ins' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'table_body_product_old_price_style',
			[
				'label' => esc_html__( 'Old Price', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'table_body_product_old_price_typography',
				'selector' => '{{WRAPPER}} table.woocommerce-cart-form__contents td.product-price del',
			]
		);

		$this->add_control(
			'table_body_product_old_price_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.woocommerce-cart-form__contents td.product-price del' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'table_body_product_quantity_heading',
			[
				'label' => esc_html__( 'Product Quantity', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'table_body_product_quantity_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.woocommerce-cart-form__contents td.product-quantity .quantity' => 'color: {{VALUE}}',
					'{{WRAPPER}} table.woocommerce-cart-form__contents td.product-quantity .quantity input' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'table_body_product_quantity_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.woocommerce-cart-form__contents td.product-quantity .quantity .glozin-qty-button:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'table_body_product_quantity_hover_background_color',
			[
				'label' => esc_html__( 'Icon background color on hover', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.woocommerce-cart-form__contents td.product-quantity .quantity .glozin-qty-button:hover::before' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'table_body_product_quantity_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.woocommerce-cart-form__contents td.product-quantity .quantity' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'table_body_product_quantity_border',
				'label' => esc_html__( 'Border', 'glozin-addons' ),
				'selector' => '{{WRAPPER}} table.woocommerce-cart-form__contents td.product-quantity .quantity',
			]
		);

		$this->add_responsive_control(
			'table_body_product_quantity_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} table.woocommerce-cart-form__contents td.product-quantity .quantity' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} table.woocommerce-cart-form__contents td.product-quantity .quantity' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'table_body_total_price_heading',
			[
				'label' => esc_html__( 'Total Price', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'table_body_total_price_typography',
				'selector' => '{{WRAPPER}} table.woocommerce-cart-form__contents td.product-subtotal',
			]
		);

		$this->add_control(
			'table_body_total_price_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.woocommerce-cart-form__contents td.product-subtotal' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'table_body_total_save_price_style',
			[
				'label' => esc_html__( 'Save Price', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'table_body_total_save_price_typography',
				'selector' => '{{WRAPPER}} table.woocommerce-cart-form__contents td.product-subtotal ins',
			]
		);

		$this->add_control(
			'table_body_total_save_price_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.woocommerce-cart-form__contents td.product-subtotal ins' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'table_body_total_old_price_style',
			[
				'label' => esc_html__( 'Old Price', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'table_body_total_old_price_typography',
				'selector' => '{{WRAPPER}} table.woocommerce-cart-form__contents td.product-subtotal del',
			]
		);

		$this->add_control(
			'table_body_total_old_price_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.woocommerce-cart-form__contents td.product-subtotal del' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_coupon',
			[
				'label' => esc_html__( 'Coupon', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'form_coupon_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .coupon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .coupon' => 'margin: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'coupon_field_style',
			[
				'label' => esc_html__( 'Field', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'field_coupon_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .coupon .input-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .coupon .input-text' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'field_coupon_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .coupon .input-text' => '--gz-input-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .coupon .input-text' => '--gz-input-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'field_coupon_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .coupon .input-text' => '--gz-input-bg-color: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_coupon_field_style' );

		$this->start_controls_tab(
			'tab_coupon_field_placeholder',
			[
				'label' => __( 'PlaceHolder', 'glozin-addons' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'field_placeholder_coupon_typography',
				'selector' => '{{WRAPPER}} .coupon .input-text::placeholder',
			]
		);

		$this->add_control(
			'field_coupon_placeholder_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .coupon .input-text::placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_coupon_field_text',
			[
				'label' => __( 'Text', 'glozin-addons' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'field_coupon_typography',
				'selector' => '{{WRAPPER}} .coupon .input-text',
			]
		);

		$this->add_control(
			'field_coupon_text_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .coupon .input-text' => '--gz-input-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'coupon_button_style',
			[
				'label' => esc_html__( 'Button', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'button_coupon_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .coupon .button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .coupon .button' => 'margin: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_coupon_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .coupon .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .coupon .button' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_coupon_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .coupon .button' => '--gz-button-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .coupon .button' => '--gz-button-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_coupon_typography',
				'selector' => '{{WRAPPER}} .coupon .button',
			]
		);

		$this->add_responsive_control(
			'button_coupon_border_width',
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
					'{{WRAPPER}} .coupon .button' => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_coupon_button_style' );

		$this->start_controls_tab(
			'tab_coupon_button_normal',
			[
				'label' => __( 'Normal', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'button_coupon_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .coupon .button' => '--gz-button-bg-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_coupon_text_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .coupon .button' => '--gz-button-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_coupon_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .coupon .button' => '--gz-button-border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_coupon_button_hover',
			[
				'label' => __( 'Hover', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'button_coupon_background_hover_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .coupon .button' => '--gz-button-bg-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_coupon_hover_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .coupon .button' => '--gz-button-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_coupon_hover_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .coupon .button' => '--gz-button-border-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_coupon_background_effect_hover_color',
			[
				'label'     => __( 'Background Effect Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .coupon .button' => '--gz-button-eff-bg-color-hover: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'style_cart_button',
			[
				'label' => esc_html__( 'Update Cart Button', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'button_cart_button_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .gz-button-update-cart' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .gz-button-update-cart' => 'margin: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_cart_button_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .gz-button-update-cart' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .gz-button-update-cart' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_cart_button_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .gz-button-update-cart' => '--gz-button-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .gz-button-update-cart' => '--gz-button-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_cart_button_typography',
				'selector' => '{{WRAPPER}} .gz-button-update-cart',
			]
		);

		$this->add_responsive_control(
			'button_cart_button_border_width',
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
					'{{WRAPPER}} .gz-button-update-cart' => 'border-width: {{SIZE}}{{UNIT}};',
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
			'button_cart_button_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gz-button-update-cart' => '--gz-button-bg-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_cart_button_text_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .gz-button-update-cart' => '--gz-button-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_cart_button_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .gz-button-update-cart' => '--gz-button-border-color: {{VALUE}};',
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
			'button_cart_button_background_hover_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gz-button-update-cart' => '--gz-button-bg-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_cart_button_hover_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gz-button-update-cart' => '--gz-button-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_cart_button_hover_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gz-button-update-cart' => '--gz-button-border-color-hover: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->order_notes_style();
		$this->free_shipping_bar_style();
		$this->totals_style();
		$this->cart_empty_style();
	}

	protected function notices_style() {
		$this->start_controls_section(
			'section_notices_style',
			[
				'label' => esc_html__( 'Notices', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'hide_notices!' => 'yes'
				]
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .woocommerce-message, {{WRAPPER}} .woocommerce-info, {{WRAPPER}} .woocommerce-error, {{WRAPPER}} .woocommerce-noreviews, {{WRAPPER}} p.no-comments',
				'condition' => [
					'hide_notices!' => 'yes'
				]
			]
		);

        $this->add_control(
			'notices_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-message, {{WRAPPER}} .woocommerce-info, {{WRAPPER}} .woocommerce-error, {{WRAPPER}} .woocommerce-noreviews, {{WRAPPER}} p.no-comments' => 'color: {{VALUE}};',
				],
				'condition' => [
					'hide_notices!' => 'yes'
				]
			]
		);

        $this->add_control(
			'notices_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-message, {{WRAPPER}} .woocommerce-info, {{WRAPPER}} .woocommerce-error, {{WRAPPER}} .woocommerce-noreviews, {{WRAPPER}} p.no-comments' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'hide_notices!' => 'yes'
				]
			]
		);

        $this->add_responsive_control(
			'notices_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-message, {{WRAPPER}} .woocommerce-info, {{WRAPPER}} .woocommerce-error, {{WRAPPER}} .woocommerce-noreviews, {{WRAPPER}} p.no-comments' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .woocommerce-message, .rtl {{WRAPPER}} .woocommerce-info, .rtl {{WRAPPER}} .woocommerce-error,  .rtl {{WRAPPER}} .woocommerce-noreviews, .rtl {{WRAPPER}} p.no-comments' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
				'condition' => [
					'hide_notices!' => 'yes'
				]
			]
		);

        $this->add_responsive_control(
			'notices_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-message, {{WRAPPER}} .woocommerce-info, {{WRAPPER}} .woocommerce-error, {{WRAPPER}} .woocommerce-noreviews, {{WRAPPER}} p.no-comments' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .woocommerce-message, .rtl {{WRAPPER}} .woocommerce-info, .rtl {{WRAPPER}} .woocommerce-error,  .rtl {{WRAPPER}} .woocommerce-noreviews, .rtl {{WRAPPER}} p.no-comments' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
				'condition' => [
					'hide_notices!' => 'yes'
				]
			]
		);

        $this->add_control(
			'notices_info_heading',
			[
				'label' => esc_html__( 'Info', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'hide_notices!' => 'yes'
				]
			]
		);

        $this->add_control(
			'notices_info_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-info, {{WRAPPER}} .woocommerce-noreviews, {{WRAPPER}} p.no-comments' => 'color: {{VALUE}};',
				],
				'condition' => [
					'hide_notices!' => 'yes'
				]
			]
		);

        $this->add_control(
			'notices_info_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-info, {{WRAPPER}} .woocommerce-noreviews, {{WRAPPER}} p.no-comments' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'hide_notices!' => 'yes'
				]
			]
		);

        $this->add_control(
			'notices_error_heading',
			[
				'label' => esc_html__( 'Error', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'hide_notices!' => 'yes'
				]
			]
		);

        $this->add_control(
			'notices_error_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-error' => 'color: {{VALUE}};',
				],
				'condition' => [
					'hide_notices!' => 'yes'
				]
			]
		);

        $this->add_control(
			'notices_error_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-error' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'hide_notices!' => 'yes'
				]
			]
		);

        $this->end_controls_section();
	}

	protected function order_notes_style() {
		$this->start_controls_section(
			'section_style_order_notes',
			[
				'label' => esc_html__( 'Order Notes', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_responsive_control(
			'order_notes_field_max_width',
			[
				'label' => esc_html__( 'Max Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'size_units' => [ 'px', 'em', 'rem', '%', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-cart-form .notes textarea' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
			'order_notes_field_rows',
			[
				'label'     => esc_html__( 'Rows', 'glozin-addons' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 4,
				'min'       => 1,
				'max'       => 10,
				'step'      => 1,
			]
		);

		$this->add_responsive_control(
			'order_notes_field_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-cart-form .notes textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .woocommerce-cart-form .notes textarea' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'order_notes_field_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-cart-form .notes textarea' => '--gz-input-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .woocommerce-cart-form .notes textarea' => '--gz-input-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'order_notes_field_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-cart-form .notes textarea' => '--gz-input-bg-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_notes_field_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-cart-form .notes textarea' => '--gz-input-border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_notes_field_hover_border_color',
			[
				'label'     => __( 'Hover Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-cart-form .notes textarea:hover' => '--gz-input-border-color-hover: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_order_notes_style' );

		$this->start_controls_tab(
			'tab_order_notes_placeholder',
			[
				'label' => __( 'PlaceHolder', 'glozin-addons' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'field_placeholder_order_notes_typography',
				'selector' => '{{WRAPPER}} .woocommerce-cart-form .notes textarea::placeholder',
			]
		);

		$this->add_control(
			'order_notes_field_placeholder_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-cart-form .notes textarea::placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_order_notes_field_text',
			[
				'label' => __( 'Text', 'glozin-addons' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'order_notes_field_typography',
				'selector' => '{{WRAPPER}} .woocommerce-cart-form .notes textarea',
			]
		);

		$this->add_control(
			'order_notes_field_text_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-cart-form .notes textarea' => '--gz-input-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function free_shipping_bar_style() {
		$this->start_controls_section(
			'section_free_shipping_bar_style',
			[
				'label' => esc_html__( 'Free Shipping Bar', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'free_shipping_bar_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-free-shipping-bar' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'free_shipping_bar_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-free-shipping-bar' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'free_shipping_bar_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-free-shipping-bar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-free-shipping-bar' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'free_shipping_bar_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-free-shipping-bar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-free-shipping-bar' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'free_shipping_bar_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-free-shipping-bar' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-free-shipping-bar' => 'margin: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'progress_bar_style',
			[
				'label' => esc_html__( 'Progress Bar', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator'  => 'before',
			]
		);

		$this->add_control(
			'progress_bar_normal_color',
			[
				'label'     => __( 'Normal Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-free-shipping-bar__progress' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'progress_bar_active_color',
			[
				'label'     => __( 'Active Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-free-shipping-bar' => '--gz-background-color-primary: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'progress_bar_unreached_color',
			[
				'label'     => __( 'Unreached Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-free-shipping-bar.gz-is-unreached' => '--gz-background-color-primary: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'progress_bar_success_color',
			[
				'label'     => __( 'Success Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-free-shipping-bar.gz-is-success' => '--gz-background-color-primary: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_style',
			[
				'label' => esc_html__( 'Text', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator'  => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .glozin-free-shipping-bar__message',
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-free-shipping-bar__message' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'text_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-free-shipping-bar__message' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-free-shipping-bar__message' => 'margin: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function totals_style() {
		$this->start_controls_section(
			'section_totals_style',
			[
				'label' => esc_html__( 'Cart Totals', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'order_right_column',
			[
				'label' => esc_html__( 'Order of right column entries', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'order_of_free_shipping_bar',
			[
				'label' => esc_html__( 'Free Shipping Bar', 'glozin-addons' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 10,
				'step' => 1,
				'default' => 1,
				'selectors' => [
					'{{WRAPPER}} .glozin-free-shipping-bar' => 'order: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_of_cart_totals_summary',
			[
				'label' => esc_html__( 'Cart Totals', 'glozin-addons' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 10,
				'step' => 1,
				'default' => 2,
				'selectors' => [
					'{{WRAPPER}} .cart_totals_summary' => 'order: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_of_extra_content',
			[
				'label' => esc_html__( 'Extra Content', 'glozin-addons' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 10,
				'step' => 1,
				'default' => 3,
				'selectors' => [
					'{{WRAPPER}} .glozin-extra-content' => 'order: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_totals_heading',
			[
				'label' => esc_html__( 'Cart Totals', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator'  => 'before',
			]
		);

		$this->add_control(
			'totals_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .cart_totals_summary' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'totals_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .cart_totals_summary' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'totals_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .cart_totals_summary' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .cart_totals_summary' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'totals_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .cart_totals_summary' => '--gz-rounded-xs: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .cart_totals_summary' => '--gz-rounded-xs: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'section_title_style',
			[
				'label' => esc_html__( 'Cart Totals Text', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator'  => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'section_title_typography',
				'selector' => '{{WRAPPER}} .cart_totals h2',
			]
		);

		$this->add_control(
			'section_title_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .cart_totals h2' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'section_title_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .cart_totals h2' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .cart_totals h2' => 'margin: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'save_price_style',
			[
				'label' => esc_html__( 'Save Price', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator'  => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'save_price_typography',
				'selector' => '{{WRAPPER}} .glozin-price-saved',
			]
		);

		$this->add_control(
			'save_price_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-price-saved' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'save_price_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-price-saved' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'save_price_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-price-saved' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'save_price_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-price-saved' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-price-saved' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'save_price_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-price-saved' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-price-saved' => 'margin: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'style_totals_button',
			[
				'label' => esc_html__( 'Button', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator'  => 'before',
			]
		);

		$this->add_responsive_control(
			'button_totals_button_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .wc-proceed-to-checkout .checkout-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .wc-proceed-to-checkout .checkout-button' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_totals_button_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .wc-proceed-to-checkout .checkout-button' => '--gz-button-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .wc-proceed-to-checkout .checkout-button' => '--gz-button-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_totals_button_typography',
				'selector' => '{{WRAPPER}} .wc-proceed-to-checkout .checkout-button',
			]
		);

		$this->start_controls_tabs( 'tabs_totals_button_style' );

		$this->start_controls_tab(
			'tab_totals_button_normal',
			[
				'label' => __( 'Normal', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'button_totals_button_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wc-proceed-to-checkout .checkout-button' => '--gz-button-bg-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_totals_button_text_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .wc-proceed-to-checkout .checkout-button' => '--gz-button-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_totals_button_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .wc-proceed-to-checkout .checkout-button' => '--gz-button-border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_totals_button_hover',
			[
				'label' => __( 'Hover', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'button_totals_button_background_hover_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wc-proceed-to-checkout .checkout-button' => '--gz-button-bg-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_totals_button_hover_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wc-proceed-to-checkout .checkout-button' => '--gz-button-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_totals_button_hover_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wc-proceed-to-checkout .checkout-button' => '--gz-button-border-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_totals_button_background_effect_hover_color',
			[
				'label'     => __( 'Background Effect Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wc-proceed-to-checkout .checkout-button' => '--gz-button-eff-bg-color-hover: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'extra_content_style',
			[
				'label' => esc_html__( 'Extra Content', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator'  => 'before',
			]
		);

		$this->add_responsive_control(
			'extra_content_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-extra-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-extra-content' => 'margin: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function cart_empty_style() {
		$this->start_controls_section(
			'section_cart_empty_style',
			[
				'label' => esc_html__( 'Cart Empty', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'cart_empty_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-woocommerce-cart-elementor.cart-empty-elementor' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-woocommerce-cart-elementor.cart-empty-elementor' => 'margin: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render icon box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		if( empty( WC()->cart ) ) {
			return;
		}

		if( empty( WC()->shipping()->get_packages() ) ) {
			$this->get_shipping_packages();
		}

		add_filter( 'gettext', [ $this, 'change_text' ], 20, 3 );
		add_action( 'woocommerce_after_cart_totals', [ $this, 'get_extra_content' ] );
		remove_action( 'woocommerce_after_cart_table', array( \Glozin\WooCommerce\Cart::instance(), 'order_comments' ) );
		add_action( 'woocommerce_after_cart_table', array( $this, 'order_comments' ) );

		if( ! empty( $settings['cart_empty_page_id'] ) ) {
			add_action( 'woocommerce_cart_is_empty', array( $this, 'cart_empty_content_builder' ), 10 );
			remove_action( 'woocommerce_cart_is_empty', array( \Glozin\WooCommerce\Cart::instance(), 'cart_empty_text' ), 20 );
		}

		if( \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
			if( empty( $settings['cart_empty_page_id'] ) ) {
				add_action( 'woocommerce_cart_is_empty', array( $this, 'cart_empty_text' ), 20 );
			}

			add_action( 'woocommerce_before_cart_totals', array( $this, 'free_shipping_bar_html' ), 5 );

			if( $settings['hide_notices'] !== 'yes' ) {
				remove_action( 'woocommerce_before_cart', 'woocommerce_output_all_notices', 10 );
				add_action( 'woocommerce_before_cart', array( $this, 'notices_html' ), 10 );
			}
		}

		$classes = [
			'columns-'. esc_attr( $settings['columns'] ),
			$settings['hide_notices'] == 'yes' ? 'notices-hidden' : '',
			WC()->cart->is_empty() ? 'cart-empty-elementor' : '',
		];

		echo '<div class="glozin-woocommerce-cart-elementor '. esc_attr( implode( ' ', $classes ) ) .'">';
			echo do_shortcode( '[woocommerce_cart]' );
		echo '</div>';

		remove_filter( 'gettext', [ $this, 'change_text' ], 20, 3 );
		remove_action( 'woocommerce_after_cart_totals', [ $this, 'get_extra_content' ] );
	}

	protected function get_shipping_packages( $atts = array() ) {
		wc_maybe_define_constant( 'WOOCOMMERCE_CART', true );

		$atts        = shortcode_atts( array(), $atts, 'woocommerce_cart' );
		$nonce_value = wc_get_var( $_REQUEST['woocommerce-shipping-calculator-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.

		// Update Shipping. Nonce check uses new value and old value (woocommerce-cart). @todo remove in 4.0.
		if ( ! empty( $_POST['calc_shipping'] ) && ( wp_verify_nonce( $nonce_value, 'woocommerce-shipping-calculator' ) || wp_verify_nonce( $nonce_value, 'woocommerce-cart' ) ) ) { // WPCS: input var ok.
			\WC_Shortcode_Cart::calculate_shipping();

			// Also calc totals before we check items so subtotals etc are up to date.
			WC()->cart->calculate_totals();
		}

		// Check cart items are valid.
		do_action( 'woocommerce_check_cart_items' );

		// Calc totals.
		WC()->cart->calculate_totals();
	}

	public function change_text( $translated, $text, $domain ) {
		$settings = $this->get_settings_for_display();
		if( is_cart() ) {
			if( $translated == 'Update cart' && ! empty( $settings['cart_button_text'] ) ) {
				$translated = $settings['cart_button_text'];
			}

			if( $translated == 'Apply coupon' && ! empty( $settings['coupon_button_text'] ) ) {
				$translated = $settings['coupon_button_text'];
			}
		}

		return $translated;
	}

	/**
	 * Add cart empty heading and sub heading
	 *
	 * @return void
	 */
	public function cart_empty_text() {
		echo sprintf(
			'<div class="gz-cart-text-empty text-center"><h5>%s</h5><p>%s</p></div>',
			esc_html__( 'Your cart is empty', 'glozin' ),
			esc_html__( 'You may check out all the available products and buy some in the shop', 'glozin' )
		);
	}

	public function free_shipping_bar_html() {
		?>
		<div class="glozin-free-shipping-bar glozin-free-shipping-bar--preload" style="--gz-progress:33.00%">
			<div class="glozin-free-shipping-bar__progress">
				<div class="glozin-free-shipping-bar__progress-bar">
					<div class="glozin-free-shipping-bar__icon"><span class="glozin-svg-icon glozin-svg-icon--delivery"><svg aria-hidden="true" role="img" focusable="false" xmlns="http://www.w3.org/2000/svg" width="22" height="18" viewBox="0 0 22 18" fill="currentColor"><path d="M21.7872 10.4724C21.7872 9.73685 21.5432 9.00864 21.1002 8.4217L18.7221 5.27043C18.2421 4.63481 17.4804 4.25532 16.684 4.25532H14.9787V2.54885C14.9787 1.14111 13.8334 0 12.4255 0H9.95745V1.69779H12.4255C12.8948 1.69779 13.2766 2.07962 13.2766 2.54885V14.5957H8.15145C7.80021 13.6052 6.85421 12.8936 5.74468 12.8936C4.63515 12.8936 3.68915 13.6052 3.33792 14.5957H2.55319C2.08396 14.5957 1.70213 14.2139 1.70213 13.7447V2.54885C1.70213 2.07962 2.08396 1.69779 2.55319 1.69779H9.95745V0H2.55319C1.14528 0 0 1.14111 0 2.54885V13.7447C0 15.1526 1.14528 16.2979 2.55319 16.2979H3.33792C3.68915 17.2884 4.63515 18 5.74468 18C6.85421 18 7.80021 17.2884 8.15145 16.2979H13.423C13.7742 17.2884 14.7202 18 15.8297 18C16.9393 18 17.8853 17.2884 18.2365 16.2979H21.7872V10.4724ZM16.684 5.95745C16.9494 5.95745 17.2034 6.08396 17.3634 6.29574L19.5166 9.14894H14.9787V5.95745H16.684ZM5.74468 16.2979C5.27545 16.2979 4.89362 15.916 4.89362 15.4468C4.89362 14.9776 5.27545 14.5957 5.74468 14.5957C6.21392 14.5957 6.59575 14.9776 6.59575 15.4468C6.59575 15.916 6.21392 16.2979 5.74468 16.2979ZM15.8298 16.2979C15.3606 16.2979 14.9787 15.916 14.9787 15.4468C14.9787 14.9776 15.3606 14.5957 15.8298 14.5957C16.299 14.5957 16.6809 14.9776 16.6809 15.4468C16.6809 15.916 16.299 16.2979 15.8298 16.2979ZM18.2366 14.5957C17.8853 13.6052 16.9393 12.8936 15.8298 12.8936C15.5398 12.8935 15.252 12.943 14.9787 13.04V10.8511H20.0851V14.5957H18.2366Z"></path></svg></span></div>
				</div>
			</div>
			<div class="glozin-free-shipping-bar__percent-value">33.00%</div>
			<div class="glozin-free-shipping-bar__message">
				Buy <strong><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>134.00</bdi></span></strong> more to enjoy <strong>Free Shipping</strong>
			</div>
		</div>
		<?php
	}

	public function get_extra_content() {
        $settings = $this->get_settings_for_display();
		if ( empty( $settings['extra_content_page_id'] ) ) {
			return;
		}

		echo '<div class="glozin-extra-content">'. \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( intval( $settings['extra_content_page_id'] ), true ) .'</div>';
    }

	public function notices_html() {
		?>
        <div class="woocommerce-notices-wrapper">
            <div class="woocommerce-message" role="alert">
                <?php esc_html_e( 'Notification message.', 'glozin-addons' ); ?> <a class="restore-item"><?php esc_html_e( 'Link', 'glozin-addons' ); ?></a>
            </div>
        </div>
        <?php
	}

	public function order_comments() {
		$settings = $this->get_settings_for_display();
		$label = ! empty( $settings['order_notes_label'] ) ? $settings['order_notes_label' ] : esc_html__( 'Order notes', 'woocommerce' );
		echo '<div class="form-row notes" id="order_comments_field">';
		echo '<label for="order_comments">' . $label . '</label>';
		echo '<textarea name="order_comments" class="input-text" id="order_comments" placeholder="' . esc_attr__( 'Notes about your order, e.g. special notes for delivery.', 'woocommerce' ) . '" rows="4" cols="5"></textarea>';
		echo '</div>';
	}

	public function cart_empty_content_builder() {
		$settings = $this->get_settings_for_display();
		echo '<div class="gz-cart-text-empty-elementor">'. \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( intval( $settings['cart_empty_page_id'] ), true ) .'</div>';
	}
}