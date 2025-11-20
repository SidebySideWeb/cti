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
 * Woocommerce Checkout widget
 */
class WC_Checkout extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-wc-checkout';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( '[Glozin] Checkout', 'glozin-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-checkout';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'glozin-addons-checkout-page' ];
	}

	/**
	 * Get widget keywords.
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'woocommerce', 'checkout', 'glozin-addons' ];
	}

	public function get_script_depends() {
		return [
			'wc-checkout',
			'wc-password-strength-meter',
			'selectWoo',
		];
	}

	public function get_style_depends() {
		return [ 'select2' ];
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
		return 'woocommerce-checkout woocommerce-page elementor-widget-' . $this->get_name();
	}

	/**
	 * Register heading widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
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
			'wc_notice_use_customizer',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'To change the Fields or Privacy policy or Terms and conditions, go to Appearance > Customize > WooCommerce > Checkout.', 'glozin-addons' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'billing_details_content',
			[ 'label' => esc_html__( 'Billing Details', 'glozin-addons' ) ]
		);

		$this->add_control(
			'billing_details_heading_text',
			[
				'label' => __( 'Billing details text', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Billing details', 'glozin-addons' ),
				'label_block' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_your_order',
			[ 'label' => esc_html__( 'Your Order', 'glozin-addons' ) ]
		);

		$this->add_control(
			'your_order_heading_text',
			[
				'label' => __( 'Your order text', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Your order', 'glozin-addons' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'extra_content_heading',
			[
				'label' => esc_html__( 'Extra content', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'extra_content_page_id',
			[
				'label' => __( 'Select the section located under Your Order column', 'glozin-addons' ),
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

		$this->section_styles();
	}

	protected function section_styles() {
		$this->login_style();
		$this->coupon_style();
		$this->form_billing_order_style();
		$this->billing_style();
		$this->order_review_style();
	}

	protected function login_style() {
		$this->start_controls_section(
			'section_login_style',
			[
				'label' => esc_html__( 'Login', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'login_info_heading',
			[
				'label' => esc_html__( 'Info', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'login_info_alignment',
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
					'{{WRAPPER}} .checkout-form-cols .checkout-login .woocommerce-info' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'login_form_heading',
			[
				'label' => esc_html__( 'Form', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'login_form_alignment',
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
					'{{WRAPPER}} .woocommerce-form-login' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'login_form_text_button',
			[
				'label' => esc_html__( 'Text', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'login_form_text_typography',
				'selector' => '{{WRAPPER}} .woocommerce-form-login p',
			]
		);

		$this->add_control(
			'login_form_text_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-form-login p' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'login_form_label_button',
			[
				'label' => esc_html__( 'Label', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'login_form_label_typography',
				'selector' => '{{WRAPPER}} .woocommerce-form-login label, {{WRAPPER}} .woocommerce-form-login label span',
			]
		);

		$this->add_control(
			'login_form_label_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-form-login label, {{WRAPPER}} .woocommerce-form-login label span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'login_form_field_style',
			[
				'label' => esc_html__( 'Field', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'login_form_field_width',
			[
				'label' => esc_html__( 'Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', '%', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-form-login .input-text' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'login_form_field_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-form-login .input-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .woocommerce-form-login .input-text' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'login_form_field_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-form-login .input-text' => '--gz-input-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .woocommerce-form-login .input-text' => '--gz-input-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'login_form_field_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-form-login .input-text' => '--gz-input-bg-color: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_login_form_field_style' );

		$this->start_controls_tab(
			'tab_login_form_field_placeholder',
			[
				'label' => __( 'PlaceHolder', 'glozin-addons' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'login_form_field_placeholder_typography',
				'selector' => '{{WRAPPER}} .woocommerce-form-login .input-text::placeholder',
			]
		);

		$this->add_control(
			'login_form_field_placeholder_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-form-login .input-text::placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_login_form_field_text',
			[
				'label' => __( 'Text', 'glozin-addons' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'login_form_field_typography',
				'selector' => '{{WRAPPER}} .woocommerce-form-login .input-text',
			]
		);

		$this->add_control(
			'login_form_field_text_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-form-login .input-text' => '--gz-input-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'login_form_button_style',
			[
				'label' => esc_html__( 'Button', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'login_form_button_width',
			[
				'label' => esc_html__( 'Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', '%', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-form-login button' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'login_form_button_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-form-login .button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .woocommerce-form-login .button' => 'margin: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'login_form_button_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-form-login .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .woocommerce-form-login .button' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'login_form_button_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-form-login .button' => '--gz-button-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .woocommerce-form-login .button' => '--gz-button-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'login_form_button_typography',
				'selector' => '{{WRAPPER}} .woocommerce-form-login .button',
			]
		);

		$this->add_responsive_control(
			'login_form_button_border_width',
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
					'{{WRAPPER}} .woocommerce-form-login .button' => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_login_form_button_style' );

		$this->start_controls_tab(
			'tab_login_form_button_normal',
			[
				'label' => __( 'Normal', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'login_form_button_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-form-login .button' => '--gz-button-bg-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'login_form_button_text_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-form-login .button' => '--gz-button-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'login_form_button_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-form-login .button' => '--gz-button-border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_login_form_button_hover',
			[
				'label' => __( 'Hover', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'login_form_button_background_hover_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-form-login .button' => '--gz-button-bg-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'login_form_button_hover_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-form-login .button' => '--gz-button-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'login_form_button_hover_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-form-login .button' => '--gz-button-border-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'login_form_button_background_effect_hover_color',
			[
				'label'     => __( 'Background Effect Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-form-login .button' => '--gz-button-eff-bg-color-hover: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'login_lost_password',
			[
				'label' => esc_html__( 'Lost Password', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'login_lost_password_typography',
				'selector' => '{{WRAPPER}} .lost_password a',
			]
		);

		$this->add_control(
			'login_lost_password_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .lost_password a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .lost_password a::after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'login_lost_password_hover_color',
			[
				'label'     => __( 'Hover Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .lost_password a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .lost_password a:hover::after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function coupon_style() {
		$this->start_controls_section(
			'section_coupon_style',
			[
				'label' => esc_html__( 'Coupon', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'coupon_info_heading',
			[
				'label' => esc_html__( 'Info', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'coupon_info_alignment',
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
					'{{WRAPPER}} .woocommerce-info' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'coupon_info_typography',
				'selector' => '{{WRAPPER}} .woocommerce-info',
			]
		);

		$this->add_control(
			'coupon_info_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-info' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'coupon_info_link_button',
			[
				'label' => esc_html__( 'Link', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'coupon_info_link_typography',
				'selector' => '{{WRAPPER}} .woocommerce-info a',
			]
		);

		$this->add_control(
			'coupon_info_link_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-info a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'coupon_info_link_hover_color',
			[
				'label'     => __( 'Hover Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-info a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'coupon_form_heading',
			[
				'label' => esc_html__( 'Form', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'coupon_form_alignment',
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
					'{{WRAPPER}} .woocommerce-form-coupon' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'coupon_form_text_button',
			[
				'label' => esc_html__( 'Text', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'coupon_form_text_typography',
				'selector' => '{{WRAPPER}} .woocommerce-form-coupon p',
			]
		);

		$this->add_control(
			'coupon_form_text_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-form-coupon p' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'coupon_form_text_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-form-coupon p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .woocommerce-form-coupon p' => 'margin: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'coupon_form_field_style',
			[
				'label' => esc_html__( 'Field', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'coupon_form_field_width',
			[
				'label' => esc_html__( 'Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', '%', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-form-coupon .input-text' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'coupon_form_field_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-form-coupon .input-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .woocommerce-form-coupon .input-text' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'coupon_form_field_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-form-coupon .input-text' => '--gz-input-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .woocommerce-form-coupon .input-text' => '--gz-input-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'coupon_form_field_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-form-coupon .input-text' => '--gz-input-bg-color: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_coupon_form_field_style' );

		$this->start_controls_tab(
			'tab_coupon_form_field_placeholder',
			[
				'label' => __( 'PlaceHolder', 'glozin-addons' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'coupon_form_field_placeholder_typography',
				'selector' => '{{WRAPPER}} .woocommerce-form-coupon .input-text::placeholder',
			]
		);

		$this->add_control(
			'coupon_form_field_placeholder_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-form-coupon .input-text::placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_coupon_form_field_text',
			[
				'label' => __( 'Text', 'glozin-addons' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'coupon_form_field_typography',
				'selector' => '{{WRAPPER}} .woocommerce-form-coupon .input-text',
			]
		);

		$this->add_control(
			'coupon_form_field_text_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-form-coupon .input-text' => '--gz-input-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'coupon_form_button_style',
			[
				'label' => esc_html__( 'Button', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'coupon_form_button_width',
			[
				'label' => esc_html__( 'Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', '%', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-form-coupon button' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'coupon_form_button_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-form-coupon .button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .woocommerce-form-coupon .button' => 'margin: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'coupon_form_button_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-form-coupon .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .woocommerce-form-coupon .button' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'coupon_form_button_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-form-coupon .button' => '--gz-button-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .woocommerce-form-coupon .button' => '--gz-button-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'coupon_form_button_typography',
				'selector' => '{{WRAPPER}} .woocommerce-form-coupon .button',
			]
		);

		$this->add_responsive_control(
			'coupon_form_button_border_width',
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
					'{{WRAPPER}} .woocommerce-form-coupon .button' => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_coupon_form_button_style' );

		$this->start_controls_tab(
			'tab_coupon_form_button_normal',
			[
				'label' => __( 'Normal', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'coupon_form_button_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-form-coupon .button' => '--gz-button-bg-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'coupon_form_button_text_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-form-coupon .button' => '--gz-button-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'coupon_form_button_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-form-coupon .button' => '--gz-button-border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_coupon_form_button_hover',
			[
				'label' => __( 'Hover', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'coupon_form_button_background_hover_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-form-coupon .button' => '--gz-button-bg-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'coupon_form_button_hover_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-form-coupon .button' => '--gz-button-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'coupon_form_button_hover_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-form-coupon .button' => '--gz-button-border-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'coupon_form_button_background_effect_hover_color',
			[
				'label'     => __( 'Background Effect Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-form-coupon .button' => '--gz-button-eff-bg-color-hover: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function form_billing_order_style() {
		$this->start_controls_section(
			'section_form_billing_order_style',
			[
				'label' => esc_html__( 'Form Billing & Order', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'form_billing_order_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'form_billing_order_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} form.checkout' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} form.checkout' => 'margin: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'form_billing_order_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} form.checkout' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} form.checkout' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function billing_style() {
		$this->start_controls_section(
			'section_billing_style',
			[
				'label' => esc_html__( 'Billing Details', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'billing_border',
				'label' => esc_html__( 'Border', 'glozin-addons' ),
				'selector' => '{{WRAPPER}} .woocommerce-billing-fields__field-wrapper',
			]
		);

		$this->add_control(
			'billing_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-billing-fields__field-wrapper' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'billing_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-billing-fields__field-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .woocommerce-billing-fields__field-wrapper' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'billing_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .col2-set' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .col2-set' => 'margin: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'billing_heading',
			[
				'label' => esc_html__( 'Heading', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'billing_heading_typography',
				'selector' => '{{WRAPPER}} .woocommerce-billing-fields h3',
			]
		);

		$this->add_control(
			'billing_heading_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-billing-fields h3' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'billing_heading_spacing',
			[
				'label' => esc_html__( 'Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'size_units' => [ 'px', 'em', 'rem', '%', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-billing-fields h3' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'billing_label',
			[
				'label' => esc_html__( 'Label', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'billing_label_typography',
				'selector' => '{{WRAPPER}} form.checkout .form-row label',
			]
		);

		$this->add_control(
			'billing_label_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout .form-row label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'billing_label_spacing',
			[
				'label' => esc_html__( 'Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'size_units' => [ 'px', 'em', 'rem', '%', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} form.checkout .form-row label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'billing_field',
			[
				'label' => esc_html__( 'Field', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'billing_field_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} form.checkout .form-row input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} form.checkout .form-row select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} form.checkout .form-row textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} form.checkout .form-row input' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
					'.rtl {{WRAPPER}} form.checkout .form-row select' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
					'.rtl {{WRAPPER}} form.checkout .form-row textarea' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'billing_field_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} form.checkout .form-row input' => '--gz-input-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} form.checkout .form-row select' => '--gz-input-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} form.checkout .form-row textarea' => '--gz-input-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} form.checkout .form-row input' => '--gz-input-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
					'.rtl {{WRAPPER}} form.checkout .form-row select' => '--gz-input-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
					'.rtl {{WRAPPER}} form.checkout .form-row textarea' => '--gz-input-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'billing_field_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout .form-row input' => '--gz-input-bg-color: {{VALUE}};',
					'{{WRAPPER}} form.checkout .form-row select' => '--gz-input-bg-color: {{VALUE}};',
					'{{WRAPPER}} form.checkout .form-row textarea' => '--gz-input-bg-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'billing_field_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout .form-row input' => '--gz-input-border-color: {{VALUE}};',
					'{{WRAPPER}} form.checkout .form-row select' => '--gz-input-border-color: {{VALUE}};',
					'{{WRAPPER}} form.checkout .form-row textarea' => '--gz-input-border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'billing_field_hover_border_color',
			[
				'label'     => __( 'Hover Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout .form-row input' => '--gz-input-border-color-hover: {{VALUE}};',
					'{{WRAPPER}} form.checkout .form-row select' => '--gz-input-border-color-hover: {{VALUE}};',
					'{{WRAPPER}} form.checkout .form-row textarea' => '--gz-input-border-color-hover: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_billing_order_notes_style' );

		$this->start_controls_tab(
			'tab_billing_order_notes_placeholder',
			[
				'label' => __( 'PlaceHolder', 'glozin-addons' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'billing_field_placeholder_order_notes_typography',
				'selector' => '{{WRAPPER}} form.checkout .form-row input::placeholder, {{WRAPPER}} form.checkout .form-row select::placeholder, {{WRAPPER}} form.checkout .form-row textarea::placeholder',
			]
		);

		$this->add_control(
			'billing_field_placeholder_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} form.checkout .form-row input::placeholder, {{WRAPPER}} form.checkout .form-row select::placeholder, {{WRAPPER}} form.checkout .form-row textarea::placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_billing_field_text',
			[
				'label' => __( 'Text', 'glozin-addons' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'billing_field_typography',
				'selector' => '{{WRAPPER}} form.checkout .form-row input, {{WRAPPER}} form.checkout .form-row select, {{WRAPPER}} form.checkout .form-row textarea',
			]
		);

		$this->add_control(
			'billing_field_text_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} form.checkout .form-row input, {{WRAPPER}} form.checkout .form-row select, {{WRAPPER}} form.checkout .form-row textarea' => '--gz-input-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function order_review_style() {
		$this->start_controls_section(
			'section_order_style',
			[
				'label' => esc_html__( 'Your Order', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'order_border',
				'label' => esc_html__( 'Border', 'glozin-addons' ),
				'selector' => '{{WRAPPER}} form.checkout .woocommerce-checkout-review-order',
			]
		);

		$this->add_control(
			'order_text_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout .woocommerce-checkout-review-order' => '--gz-color__base: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout .woocommerce-checkout-review-order' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_table_border_color',
			[
				'label'     => __( 'Border Color In Table', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout table.shop_table tbody' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} form.checkout table.shop_table tr' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} form.checkout table.shop_table th' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} form.checkout table.shop_table td' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} form.checkout .woocommerce-checkout-payment' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} form.checkout .woocommerce-checkout-payment ul.wc_payment_methods li' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'order_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} form.checkout .woocommerce-checkout-review-order' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} form.checkout .woocommerce-checkout-review-order' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'order_heading',
			[
				'label' => esc_html__( 'Heading', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'order_heading_typography',
				'selector' => '{{WRAPPER}} form.checkout #order_review_heading',
			]
		);

		$this->add_control(
			'order_heading_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout #order_review_heading' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'order_heading_spacing',
			[
				'label' => esc_html__( 'Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'size_units' => [ 'px', 'em', 'rem', '%', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} form.checkout #order_review_heading' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'extra_content_heading_style',
			[
				'label' => esc_html__( 'Extra Content', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
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

		$this->start_controls_section(
			'section_order_product_style',
			[
				'label' => esc_html__( 'Your Order Product', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'order_product_title_typography',
				'selector' => '{{WRAPPER}} form.checkout .woocommerce-checkout-review-order th.product-name',
			]
		);

		$this->add_control(
			'order_product_title_color',
			[
				'label'     => __( 'Title Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout .woocommerce-checkout-review-order th.product-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_product_name',
			[
				'label' => esc_html__( 'Product Name', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'order_product_name_typography',
				'selector' => '{{WRAPPER}} form.checkout table.shop_table tbody td.product-name .checkout-review-product-name',
			]
		);

		$this->add_control(
			'order_product_name_color',
			[
				'label'     => __( 'Title Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout table.shop_table tbody td.product-name .checkout-review-product-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_product_quantity',
			[
				'label' => esc_html__( 'Product Quantity', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'order_product_quantity_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout table.shop_table tbody td.product-name .checkout-review-product-image .product-quantity' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_product_quantity_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout table.shop_table tbody td.product-name .checkout-review-product-image .product-quantity' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_product_quantity_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout table.shop_table tbody td.product-name .checkout-review-product-image .product-quantity' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_product_price',
			[
				'label' => esc_html__( 'Product Price', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'order_product_price_typography',
				'selector' => '{{WRAPPER}} form.checkout table.shop_table tbody td.product-name .checkout-review-product-price',
			]
		);

		$this->add_control(
			'order_product_price_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout table.shop_table tbody td.product-name .checkout-review-product-price' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_product_save_price_style',
			[
				'label' => esc_html__( 'Save Price', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'order_product_save_price_typography',
				'selector' => '{{WRAPPER}} form.checkout table.shop_table tbody td.product-name .checkout-review-product-price ins',
			]
		);

		$this->add_control(
			'order_product_save_price_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout table.shop_table tbody td.product-name .checkout-review-product-price ins' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_product_old_price_style',
			[
				'label' => esc_html__( 'Old Price', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'order_product_old_price_typography',
				'selector' => '{{WRAPPER}} form.checkout table.shop_table tbody td.product-name .checkout-review-product-price del',
			]
		);

		$this->add_control(
			'order_product_old_price_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout table.shop_table tbody td.product-name .checkout-review-product-price del' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_product_attributes',
			[
				'label' => esc_html__( 'Product Attributes', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'order_product_attributes_label',
			[
				'label' => esc_html__( 'Label', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'order_product_attributes_label_typography',
				'selector' => '{{WRAPPER}} table.shop_table dl.variation dt',
			]
		);

		$this->add_control(
			'order_product_attributes_label_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.shop_table dl.variation dt' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_product_attributes_value',
			[
				'label' => esc_html__( 'Value', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'order_product_attributes_value_typography',
				'selector' => '{{WRAPPER}} table.shop_table dl.variation dd',
			]
		);

		$this->add_control(
			'order_product_attributes_value_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.shop_table dl.variation dd' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_product_total_price',
			[
				'label' => esc_html__( 'Product Total Price', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'order_product_total_price_typography',
				'selector' => '{{WRAPPER}} form.checkout table.shop_table tbody td.product-total',
			]
		);

		$this->add_control(
			'order_product_total_price_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout table.shop_table tbody td.product-total' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_product_total_save_price_style',
			[
				'label' => esc_html__( 'Save Price', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'order_product_total_save_price_typography',
				'selector' => '{{WRAPPER}} form.checkout table.shop_table tbody td.product-total ins',
			]
		);

		$this->add_control(
			'order_product_total_save_price_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout table.shop_table tbody td.product-total ins' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_product_total_old_price_style',
			[
				'label' => esc_html__( 'Old Price', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'order_product_total_old_price_typography',
				'selector' => '{{WRAPPER}} form.checkout table.shop_table tbody td.product-total del',
			]
		);

		$this->add_control(
			'order_product_total_old_price_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout table.shop_table tbody td.product-total del' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_order_coupon_style',
			[
				'label' => esc_html__( 'Your Order Coupon', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'order_coupon_title_typography',
				'selector' => '{{WRAPPER}} form.checkout table.shop_table tr.cart-discount th',
			]
		);

		$this->add_control(
			'order_coupon_title_color',
			[
				'label'     => __( 'Title Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout table.shop_table tr.cart-discount th' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_coupon_price_style',
			[
				'label' => esc_html__( 'Price', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'order_coupon_price_typography',
				'selector' => '{{WRAPPER}} form.checkout table.shop_table tr.cart-discount td',
			]
		);

		$this->add_control(
			'order_coupon_price_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout table.shop_table tr.cart-discount td' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_coupon_link_style',
			[
				'label' => esc_html__( 'Link', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'order_coupon_link_typography',
				'selector' => '{{WRAPPER}} form.checkout table.shop_table tr.cart-discount td a',
			]
		);

		$this->add_control(
			'order_coupon_link_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout table.shop_table tr.cart-discount td a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_coupon_link_hover_color',
			[
				'label'     => __( 'Hover Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout table.shop_table tr.cart-discount td a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_order_shipping_style',
			[
				'label' => esc_html__( 'Your Order Shipping', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'order_shipping_title_style',
			[
				'label' => esc_html__( 'Title', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'order_shipping_title_typography',
				'selector' => '{{WRAPPER}} table.shop_table .woocommerce-shipping-totals h3',
			]
		);

		$this->add_control(
			'order_shipping_title_color',
			[
				'label'     => __( 'Title Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.shop_table .woocommerce-shipping-totals h3' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'order_shipping_title_alignment',
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
					'{{WRAPPER}} table.shop_table .woocommerce-shipping-totals h3' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'order_shipping_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} table.shop_table .woocommerce-shipping-totals h3' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} table.shop_table .woocommerce-shipping-totals h3' => 'margin: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'order_shipping_text_style',
			[
				'label' => esc_html__( 'Text', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'order_shipping_text_color',
			[
				'label'     => __( 'Title Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.shop_table .woocommerce-shipping-totals td' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_order_total_style',
			[
				'label' => esc_html__( 'Your Order Total', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'order_total_title_style',
			[
				'label' => esc_html__( 'Title', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'order_total_title_typography',
				'selector' => '{{WRAPPER}} table.shop_table .order-total th',
			]
		);

		$this->add_control(
			'order_total_title_color',
			[
				'label'     => __( 'Title Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.shop_table .order-total th' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_total_price_style',
			[
				'label' => esc_html__( 'Price', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'order_total_price_typography',
				'selector' => '{{WRAPPER}} table.shop_table .order-total td',
			]
		);

		$this->add_control(
			'order_total_price_color',
			[
				'label'     => __( 'Title Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.shop_table .order-total td' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_order_payment_style',
			[
				'label' => esc_html__( 'Your Order Payment', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'order_payment_text_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout .woocommerce-checkout-payment p' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_payment_link_color',
			[
				'label'     => __( 'Link Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout .woocommerce-checkout-payment a' => '--gz-link-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_payment_link_hover_color',
			[
				'label'     => __( 'Link Hover Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout .woocommerce-checkout-payment a' => '--gz-link-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_payment_radio_style',
			[
				'label' => esc_html__( 'Radio Button', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'order_payment_radio_border_color',
			[
				'label'     => __( 'Radio Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout .woocommerce-checkout-payment input[type="radio"]::before' => '--gz-border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_payment_radio_background_color',
			[
				'label'     => __( 'Radio Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout .woocommerce-checkout-payment input[type="radio"]::before' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} form.checkout .woocommerce-checkout-payment input[type="radio"]:checked::after' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_payment_radio_checked_color',
			[
				'label'     => __( 'Radio Checked Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout .woocommerce-checkout-payment input[type="radio"]:checked::before' => '--gz-color__dark: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_payment_radio_checked_background_color',
			[
				'label'     => __( 'Radio Checked Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout .woocommerce-checkout-payment input[type="radio"]:checked::after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_payment_radio_label_color',
			[
				'label'     => __( 'Label Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout .woocommerce-checkout-payment label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_payment_radio_checked_label_color',
			[
				'label'     => __( 'Checked Label Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout .woocommerce-checkout-payment input[type="radio"]:checked + label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_order_privacy_policy_style',
			[
				'label' => esc_html__( 'Your Order Privacy policy', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'order_privacy_policy_typography',
				'selector' => '{{WRAPPER}} .woocommerce-privacy-policy-text',
			]
		);

		$this->add_control(
			'order_privacy_policy_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-privacy-policy-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_privacy_policy_link_style',
			[
				'label' => esc_html__( 'Link', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'order_privacy_policy_link_typography',
				'selector' => '{{WRAPPER}} .woocommerce-privacy-policy-text a',
			]
		);

		$this->add_control(
			'order_privacy_policy_link_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-privacy-policy-text a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_privacy_policy_link_hover_color',
			[
				'label'     => __( 'Hover Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-privacy-policy-text a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_order_terms_and_conditions_style',
			[
				'label' => esc_html__( 'Your Order Terms And Conditions', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'order_terms_and_conditions_typography',
				'selector' => '{{WRAPPER}} .woocommerce-terms-and-conditions-checkbox-text',
			]
		);

		$this->add_control(
			'order_terms_and_conditions_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-terms-and-conditions-checkbox-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_terms_and_conditions_link_style',
			[
				'label' => esc_html__( 'Link', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'order_terms_and_conditions_link_typography',
				'selector' => '{{WRAPPER}} .woocommerce-terms-and-conditions-checkbox-text a',
			]
		);

		$this->add_control(
			'order_terms_and_conditions_link_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-terms-and-conditions-checkbox-text a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_terms_and_conditions_link_hover_color',
			[
				'label'     => __( 'Hover Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-terms-and-conditions-checkbox-text a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_order_button_place_order_style',
			[
				'label' => esc_html__( 'Your Order Place Order Button', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'order_button_place_order_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} form.checkout .woocommerce-checkout-payment .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} form.checkout .woocommerce-checkout-payment .button' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'order_button_place_order_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} form.checkout .woocommerce-checkout-payment .button' => '--gz-button-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} form.checkout .woocommerce-checkout-payment .button' => '--gz-button-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'order_button_place_order_typography',
				'selector' => '{{WRAPPER}} form.checkout .woocommerce-checkout-payment .button',
			]
		);

		$this->start_controls_tabs( 'tabs_order_button_place_order_style' );

		$this->start_controls_tab(
			'tab_order_button_place_order_normal',
			[
				'label' => __( 'Normal', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'order_button_place_order_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout .woocommerce-checkout-payment .button' => '--gz-button-bg-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_button_place_order_text_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} form.checkout .woocommerce-checkout-payment .button' => '--gz-button-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_button_place_order_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} form.checkout .woocommerce-checkout-payment .button' => '--gz-button-border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_order_button_place_order_hover',
			[
				'label' => __( 'Hover', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'order_button_place_order_background_hover_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout .woocommerce-checkout-payment .button' => '--gz-button-bg-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_button_place_order_hover_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout .woocommerce-checkout-payment .button' => '--gz-button-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_button_place_order_hover_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout .woocommerce-checkout-payment .button' => '--gz-button-border-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_button_place_order_background_effect_hover_color',
			[
				'label'     => __( 'Background Effect Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.checkout .woocommerce-checkout-payment .button' => '--gz-button-eff-bg-color-hover: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

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

		if ( WC()->cart->is_empty() ) {
			return;
		}

		add_filter( 'gettext', [ $this, 'change_text' ], 20, 3 );

		add_action( 'woocommerce_checkout_before_order_review', [ $this, 'open_checkout_content_form' ], 0 );
		add_action( 'woocommerce_checkout_after_order_review', [ $this, 'get_extra_content' ], 90 );
		add_action( 'woocommerce_checkout_after_order_review', [ $this, 'close_checkout_content_form' ], 99 );

		if( \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
			add_action( 'woocommerce_before_checkout_form', [ $this, 'get_login_coupon_html' ], 10 );
			remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );
			remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
			add_filter( 'woocommerce_cart_item_name', array( $this, 'review_product_name_html' ), 10, 3);
			add_filter( 'woocommerce_checkout_cart_item_quantity', array( $this, 'review_cart_item_quantity_html' ), 10, 3);
		}

		$classes = [
			'columns-'. esc_attr( $settings['columns'] ),
		];

		echo '<div class="glozin-woocommerce-checkout-elementor '. esc_attr( implode( ' ', $classes ) ) .'">';
			echo do_shortcode( '[woocommerce_checkout]' );
		echo '</div>';

		remove_action( 'woocommerce_checkout_before_order_review', [ $this, 'open_checkout_content_form' ], 0 );
		remove_action( 'woocommerce_checkout_after_order_review', [ $this, 'get_extra_content' ], 99 );
		remove_action( 'woocommerce_checkout_after_order_review', [ $this, 'close_checkout_content_form' ], 99 );

		remove_filter( 'gettext', [ $this, 'change_text' ], 20, 3 );
	}

	public function change_text( $translated, $text, $domain ) {
		$settings = $this->get_settings_for_display();

		if ( 'woocommerce' !== $domain ) {
			return $translated;
		}

		if( $translated == 'Billing details' && ! empty( $settings['billing_details_heading_text'] ) ) {
			$translated = $settings['billing_details_heading_text'];
		}

		if( $translated == 'Your order' && ! empty( $settings['your_order_heading_text'] ) ) {
			$translated = $settings['your_order_heading_text'];
		}

		return $translated;
	}

	public function get_login_coupon_html() {
		echo '<div class="checkout-form-cols">';
			$this->get_login_html();
			$this->get_coupon_html();
		echo '</div>';
	}

	public function get_login_html() {
		?>
		<div class="checkout-login checkout-form-col col-flex gz-md-6 gz-sm-6 gz-xs-12">
			<div class="woocommerce-form-login-toggle">
				<?php wc_print_notice( apply_filters( 'woocommerce_checkout_login_message', esc_html__( 'Returning customer?', 'woocommerce' ) ) . ' <a href="#" class="showlogin">' . esc_html__( 'Click here to login', 'woocommerce' ) . '</a>', 'notice' ); ?>
			</div>
			<form class="woocommerce-form woocommerce-form-login login" method="post" style="display:none">
				<?php do_action( 'woocommerce_login_form_start' ); ?>
				<?php echo ( esc_html__( 'If you have shopped with us before, please enter your details below. If you are a new customer, please proceed to the Billing section.', 'woocommerce' ) ) ? wpautop( wptexturize( esc_html__( 'If you have shopped with us before, please enter your details below. If you are a new customer, please proceed to the Billing section.', 'woocommerce' ) ) ) : ''; // @codingStandardsIgnoreLine ?>
				<p class="form-row form-row-first">
					<label for="username"><?php esc_html_e( 'Username or email', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
					<input type="text" class="input-text" name="username" id="username" autocomplete="username" required aria-required="true" />
				</p>
				<p class="form-row form-row-last">
					<label for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
					<input class="input-text woocommerce-Input" type="password" name="password" id="password" autocomplete="current-password" required aria-required="true" />
				</p>
				<div class="clear"></div>

				<?php do_action( 'woocommerce_login_form' ); ?>

				<p class="form-row">
					<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
						<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></span>
					</label>
					<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
					<button type="submit" class="woocommerce-button button woocommerce-form-login__submit<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="login" value="<?php esc_attr_e( 'Login', 'woocommerce' ); ?>"><?php esc_html_e( 'Login', 'woocommerce' ); ?></button>
				</p>
				<p class="lost_password">
					<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>
				</p>
				<div class="clear"></div>
				<?php do_action( 'woocommerce_login_form_end' ); ?>
			</form>
		</div>
		<?php
	}

	public function get_coupon_html() {
		?>
			<div class="checkout-coupon checkout-form-col col-flex gz-md-6 gz-sm-6 gz-xs-12">
				<div class="woocommerce-form-coupon-toggle">
					<?php wc_print_notice( apply_filters( 'woocommerce_checkout_coupon_message', esc_html__( 'Have a coupon?', 'woocommerce' ) . ' <a href="#" class="showcoupon">' . esc_html__( 'Click here to enter your code', 'woocommerce' ) . '</a>' ), 'notice' ); ?>
				</div>
				<form class="checkout_coupon woocommerce-form-coupon" method="post" style="display:none">
					<p><?php esc_html_e( 'If you have a coupon code, please apply it below.', 'woocommerce' ); ?></p>
					<p class="form-row form-row-first">
						<label for="coupon_code" class="screen-reader-text"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label>
						<input type="text" name="coupon_code" class="input-text" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" id="coupon_code" value="" />
					</p>
					<p class="form-row form-row-last">
						<button type="submit" class="button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_html_e( 'Apply coupon', 'woocommerce' ); ?></button>
					</p>
					<div class="clear"></div>
				</form>
			</div>
		<?php
	}

	/**
	 * Review product name html
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function review_product_name_html( $name, $cart_item, $cart_item_key ) {
		if( WC()->cart->display_prices_including_tax() ) {
			$_product_regular_price = floatval( wc_get_price_including_tax( $cart_item['data'], array( 'price' => $cart_item['data']->get_regular_price() ) ) );
			$_product_sale_price = floatval( wc_get_price_including_tax( $cart_item['data'], array( 'price' => $cart_item['data']->get_price() ) ) );
		} else {
			$_product_regular_price = floatval( $cart_item['data']->get_regular_price() );
			$_product_sale_price = floatval( $cart_item['data']->get_price() );
		}

		if( $_product_sale_price > 0 && $_product_regular_price > $_product_sale_price ) {
			$product_price = wc_format_sale_price( $_product_regular_price, $_product_sale_price );
		} else {
			$product_price = WC()->cart->get_product_price( $cart_item['data'] );
		}

		return sprintf( '<span class="checkout-review-product-image">
						%s
						<strong class="product-quantity">%s</strong>
						</span>
						<span class="checkout-review-product-name">%s</span>
						<span class="checkout-review-product-price price">%s</span>',
				$cart_item['data']->get_image(),
				$cart_item['quantity'],
				$name,
				$product_price
			);
	}

	/**
	 * Review product quantity html
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function review_cart_item_quantity_html( $quantity, $cart_item, $cart_item_key ) {
		return '';
	}

	public function open_checkout_content_form() {
		echo '<div class="checkout-content-form">';
	}

	public function get_extra_content() {
        $settings = $this->get_settings_for_display();
		if ( empty( $settings['extra_content_page_id'] ) ) {
			return;
		}

		echo '<div class="glozin-extra-content">'. \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( intval( $settings['extra_content_page_id'] ), true ) .'</div>';
    }

	public function close_checkout_content_form() {
		echo '</div>';
	}
}