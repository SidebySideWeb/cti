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
 * Woocommerce Notices widget
 */
class WC_Notices extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-wc-notices';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( '[Glozin] Woo Notices', 'glozin-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-woocommerce-notices';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'glozin-addons-product' ];
	}

	/**
	 * Get widget keywords.
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'woocommerce', 'notices', 'message', 'glozin-addons' ];
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
        $this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Style', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .woocommerce-message, {{WRAPPER}} .woocommerce-info, {{WRAPPER}} .woocommerce-error, {{WRAPPER}} .woocommerce-noreviews, {{WRAPPER}} p.no-comments',
			]
		);

        $this->add_control(
			'color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-message, {{WRAPPER}} .woocommerce-info, {{WRAPPER}} .woocommerce-error, {{WRAPPER}} .woocommerce-noreviews, {{WRAPPER}} p.no-comments' => 'color: {{VALUE}};',
				],
			]
		);

        $this->add_control(
			'background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-message, {{WRAPPER}} .woocommerce-info, {{WRAPPER}} .woocommerce-error, {{WRAPPER}} .woocommerce-noreviews, {{WRAPPER}} p.no-comments' => 'background-color: {{VALUE}};',
				],
			]
		);

        $this->add_responsive_control(
			'padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-message, {{WRAPPER}} .woocommerce-info, {{WRAPPER}} .woocommerce-error, {{WRAPPER}} .woocommerce-noreviews, {{WRAPPER}} p.no-comments' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .woocommerce-message, .rtl {{WRAPPER}} .woocommerce-info, .rtl {{WRAPPER}} .woocommerce-error,  .rtl {{WRAPPER}} .woocommerce-noreviews, .rtl {{WRAPPER}} p.no-comments' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-message, {{WRAPPER}} .woocommerce-info, {{WRAPPER}} .woocommerce-error, {{WRAPPER}} .woocommerce-noreviews, {{WRAPPER}} p.no-comments' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .woocommerce-message, .rtl {{WRAPPER}} .woocommerce-info, .rtl {{WRAPPER}} .woocommerce-error,  .rtl {{WRAPPER}} .woocommerce-noreviews, .rtl {{WRAPPER}} p.no-comments' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
			'info_heading',
			[
				'label' => esc_html__( 'Info', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_control(
			'info_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-info, {{WRAPPER}} .woocommerce-noreviews, {{WRAPPER}} p.no-comments' => 'color: {{VALUE}};',
				],
			]
		);

        $this->add_control(
			'info_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-info, {{WRAPPER}} .woocommerce-noreviews, {{WRAPPER}} p.no-comments' => 'background-color: {{VALUE}};',
				],
			]
		);

        $this->add_control(
			'error_heading',
			[
				'label' => esc_html__( 'Error', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_control(
			'error_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-error' => 'color: {{VALUE}};',
				],
			]
		);

        $this->add_control(
			'error_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-error' => 'background-color: {{VALUE}};',
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

		if( \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
            $this->get_notices_html();
            return;
		}

		echo '<div class="woocommerce-notices-wrapper">';
			wc_print_notices();
		echo '</div>';
	}

    public function get_notices_html() {
        ?>
        <div class="woocommerce-notices-wrapper">
            <div class="woocommerce-message" role="alert">
                <?php esc_html_e( 'Notification message.', 'glozin-addons' ); ?> <a class="restore-item"><?php esc_html_e( 'Link', 'glozin-addons' ); ?></a>
            </div>
        </div>
        <?php
    }
}