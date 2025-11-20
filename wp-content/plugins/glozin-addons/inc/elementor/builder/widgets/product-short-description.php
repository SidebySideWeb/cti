<?php
namespace Glozin\Addons\Elementor\Builder\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_Short_Description extends Widget_Base {
	use \Glozin\Addons\Elementor\Builder\Traits\Product_Id_Trait;

	public function get_name() {
		return 'glozin-product-short-description';
	}

	public function get_title() {
		return esc_html__( '[Glozin] Product Short Description', 'glozin-addons' );
	}

	public function get_icon() {
		return 'eicon-product-description';
	}

	public function get_categories() {
		return ['glozin-addons-product'];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'short', 'description', 'product' ];
	}

	public function get_script_depends() {
		return [
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
			'section_content',
			[ 'label' => __( 'Content', 'glozin-addons' ) ]
		);

		$this->add_control(
            'description_lines',
            [
                'label'     => __( 'Product Description Lines', 'glozin-addons' ),
                'type'      => Controls_Manager::NUMBER,
                'min'       => 1,
                'max'       => 50,
                'step'      => 1,
                'default'   => 4,
                'frontend_available' => true,
				'selectors' => [
					'{{WRAPPER}}' => '--gz-product-description-lines: {{VALUE}}',
				],
            ]
        );

		$this->add_control(
			'more_text',
			[
				'label' => __( 'Show More Text', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Show More', 'glozin-addons' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'less_text',
			[
				'label' => __( 'Show Less Text', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Show Less', 'glozin-addons' ),
				'label_block' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Style', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'selector' => '{{WRAPPER}} .short-description__content',
			]
		);

		$this->add_control(
			'color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .short-description__content' => 'color: {{VALUE}};',
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

		$this->add_control(
			'button_style',
			[
				'label'   => __( 'Style', 'glozin-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'subtle',
				'options' => [
					''             => __( 'Solid Dark', 'glozin-addons' ),
					'light'        => __( 'Solid Light', 'glozin-addons' ),
					'outline-dark' => __( 'Outline Dark', 'glozin-addons' ),
					'outline'      => __( 'Outline Light', 'glozin-addons' ),
					'subtle'       => __( 'Underline', 'glozin-addons' ),
					'text'         => __( 'Text', 'glozin-addons' ),
				],
			]
		);

		$this->add_responsive_control(
			'button_spacing',
			[
				'label' => esc_html__( 'Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .short-description .short-description__more' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .short-description__more',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'button_border',
				'label' => esc_html__( 'Border', 'glozin-addons' ),
				'selector' => '{{WRAPPER}} .short-description__more',
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .short-description__more' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .short-description__more' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .short-description__more' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .short-description__more' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'button_style_tabs' );

		$this->start_controls_tab(
			'button_normal',
			[
				'label' => __( 'Normal', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'button_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .short-description__more' => '--gz-button-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .short-description__more' => '--gz-button-bg-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_hover',
			[
				'label' => __( 'Hover', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .short-description__more' => '--gz-button-color-hover: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_hover_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .short-description__more' => '--gz-button-bg-color-hover: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .short-description__more:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_effect_hover_color',
			[
				'label'     => __( 'Background Effect Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .short-description__more' => '--gz-button-eff-bg-color-hover: {{VALUE}};',
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
			$this->short_description_html( $settings );
			return;
		}

		$content = $product->get_short_description();
		if( empty( $content ) ) {
			return;
		}

		echo '<div class="short-description">';
			$option = array(
				'more'   => ! empty( $settings['more_text'] ) ? $settings['more_text'] : esc_html__( 'Show More', 'glozin' ),
				'less'   => ! empty( $settings['less_text'] ) ? $settings['less_text'] : esc_html__( 'Show Less', 'glozin' )
			);

			echo sprintf('<div class="short-description__content">%s</div>', wp_kses_post( do_shortcode($content) ));
			echo sprintf('
				<button class="short-description__more gz-button-%s show hidden" data-settings="%s">%s</button>',
				esc_attr( $settings['button_style'] ),
				htmlspecialchars(json_encode( $option )),
				$option['more']
			);
		echo '</div>';
	}

	public function short_description_html( $settings ) {
		echo '<div class="short-description">';
			$option = array(
				'more'   => ! empty( $settings['more_text'] ) ? $settings['more_text'] : esc_html__( 'Show More', 'glozin' ),
				'less'   => ! empty( $settings['less_text'] ) ? $settings['less_text'] : esc_html__( 'Show Less', 'glozin' )
			);

			?><div class="short-description__content">
				<?php esc_html_e( "Button-up shirt sleeves and a relaxed silhouette. It’s tailored with drapey, crinkle-texture fabric that’s made from LENZING™ ECOVERO™ Viscose — responsibly sourced wood-based fibres produced through a process that reduces impact on forests, biodiversity and water supply. The Company Private Limited and each of their respective subsidiary, parent and affiliated companies is deemed to operate this Website (“we” or “us”) recognizes that you care how information about you is used and shared. Please be advised that the practices described in this Privacy Policy apply to information gathered by us or our subsidiaries, affiliates or agents: (i) through this Website, (ii) where applicable, through our Customer Service Department in connection with this Website, (iii) through information provided to us in our free standing retail stores, and (iv) through information provided to us in conjunction with marketing promotions and sweepstakes.", 'glozin-addons' ); ?>
			</div><?php
			echo sprintf('
				<button class="short-description__more gz-button-%s show hidden" data-settings="%s">%s</button>',
				esc_attr( $settings['button_style'] ),
				htmlspecialchars(json_encode( $option )),
				$option['more']
			);
		echo '</div>';
	}
}
