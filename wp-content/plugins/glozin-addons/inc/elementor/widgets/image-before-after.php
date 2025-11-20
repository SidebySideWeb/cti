<?php

namespace Glozin\Addons\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Image_Size;

use Glozin\Addons\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Before After Images widget
 */
class Image_Before_After extends Widget_Base {
	use \Glozin\Addons\Elementor\Base\Button_Base;
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-image-before-after';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( '[Glozin] Image Before & After', 'glozin-addons' );
	}

	/**
	 * Retrieve the widget circle.
	 *
	 * @return string Widget circle.
	 */
	public function get_icon() {
		return 'eicon-image-before-after';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'glozin-addons' ];
	}

	public function get_script_depends() {
		return [
			'glozin-image-slide',
			'glozin-eventmove',
		];
	}

	public function get_style_depends() {
		return [
			'glozin-elementor-css',
		];
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->section_content();
		$this->section_style();
	}


	/**
	 * Section Content
	 */
	protected function section_content() {
		$this->start_controls_section(
			'section_content',
			[ 'label' => esc_html__( 'Content', 'glozin-addons' ) ]
		);

		$this->add_control(
			'before_image',
			[
				'label'   => esc_html__( 'Before Image', 'glozin-addons' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => wc_placeholder_img_src(),
				],
			]
		);

		$this->add_control(
			'after_image',
			[
				'label'   => esc_html__( 'After Image', 'glozin-addons' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => wc_placeholder_img_src(),
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				'default'   => 'full',
				'separator' => 'none',
			]
		);

		$this->add_control(
			'text_heading',
			[
				'label' => __( 'Text', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'before_text',
			[
				'label' => __( 'Before Text', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Before', 'glozin-addons' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'after_text',
			[
				'label' => __( 'After Text', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'After', 'glozin-addons' ),
				'label_block' => true,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Section Style
	 */

	protected function section_style() {
		// Content
		$this->start_controls_section(
			'section_content_style',
			[
				'label' => __( 'Content', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'line_control_style',
			[
				'label' => esc_html__( 'Control', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'line_control_color',
			[
				'label'     => __( 'Line Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-image-before-after .imageslide-container' => '--glozin-image-slide-line-control: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'handle_control_bg_color',
			[
				'label'     => __( 'Handle Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-image-before-after .imageslide-container' => '--glozin-image-slide-bg-control: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'handle_control_color',
			[
				'label'     => __( 'Handle Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-image-before-after .imageslide-container' => '--glozin-image-slide-bg-color-control: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'handle_control_icon_color',
			[
				'label'     => __( 'Handle Icon Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-image-before-after .imageslide-handle .glozin-svg-icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render circle box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$classes = [
			'glozin-image-before-after',
		];

		$this->add_render_attribute( 'wrapper', 'class', $classes );

		$handler = '<div class="imageslide-handle d-flex align-items-center justify-content-center z-3">'. Helper::get_svg('move-left-right').'</div>';

		$before_image = $after_image ='';
		if ($settings['before_image']) {
			$settings['image']      = $settings['before_image'];
			$before_image = Group_Control_Image_Size::get_attachment_image_html( $settings );
			$before_text = $settings['before_text'] ? '<div class="glozin-image-before-after__button glozin-image-before-after__button-before position-absolute top-20 z-1 glozin-button gz-button gz-button-light">'. $settings['before_text'] .'</div>' : '';
			$before_image = '<div class="glozin-image-before-after__image glozin-image-before-after__image-before position-absolute top-0 w-100 h-100 z-2">'. $before_image . $before_text .'</div>';
		}

		if ($settings['after_image']) {
			$settings['image']      = $settings['after_image'];
			$after_image = Group_Control_Image_Size::get_attachment_image_html( $settings );
			$after_text = $settings['after_text'] ? '<div class="glozin-image-before-after__button glozin-image-before-after__button-after position-absolute bottom-20 z-1 glozin-button gz-button gz-button-light">'. $settings['after_text'] .'</div>' : '';
			$after_image = '<div class="glozin-image-before-after__image glozin-image-before-after__image-after position-absolute top-0 z-1 w-100 h-100">'. $after_image . $after_text .'</div>';
		}

		$image =  sprintf('<div class="box-thumbnail">%s%s%s</div>',$before_image, $after_image, $handler);

		echo sprintf(
			'<div %s>
				<div class="glozin-image-before-after__inner"> %s</div>
			</div>',
			$this->get_render_attribute_string( 'wrapper' ),
			$image
		);
	}
}