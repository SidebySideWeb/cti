<?php

namespace Glozin\Addons\Elementor\Widgets;

use Glozin\Addons\Elementor\Base\Carousel_Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Stroke;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Slides widget
 */
class Slides extends Carousel_Widget_Base {
	use \Glozin\Addons\Elementor\Base\Aspect_Ratio_Base;
	use \Glozin\Addons\Elementor\Base\Button_Base;

	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-slides';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( '[Glozin] Slides', 'glozin-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-post-slider';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'glozin-addons' ];
	}

	/**
	 * Scripts
	 *
	 * @return void
	 */
	public function get_script_depends() {
		return [
			'glozin-elementor-widgets'
		];
	}

	/**
	 * Get style dependencies.
	 *
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return [ 'glozin-slides-css' ];
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

	// Tab Content
	protected function section_content() {
		$this->section_content_slides();
		$this->section_slider_options();
	}

	protected function section_content_slides() {
		$this->start_controls_section(
			'section_slides',
			[
				'label' => esc_html__( 'Slides', 'glozin-addons' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->start_controls_tabs( 'slides_repeater' );


		$repeater->start_controls_tab( 'text_content', [ 'label' => esc_html__( 'Content', 'glozin-addons' ) ] );

		$repeater->add_responsive_control(
			'banner_background_img',
			[
				'label'    => __( 'Image', 'glozin-addons' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => wc_placeholder_img_src(),
				],
			]
		);

		$repeater->add_control(
			'before_title',
			[
				'label'       => esc_html__( 'Before Title', 'glozin-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'glozin-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Slide Title', 'glozin-addons' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'description',
			[
				'label'       => esc_html__( 'Description', 'glozin-addons' ),
				'type'    => Controls_Manager::TEXTAREA,
			]
		);

		$repeater->add_control(
			'sub_description',
			[
				'label'     => esc_html__( 'Sub Description', 'glozin-addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__( 'Yes', 'glozin-addons' ),
				'label_off' => esc_html__( 'No', 'glozin-addons' ),
				'return_value' => 'yes',
				'default'   => '',
			]
		);

		$repeater->add_control(
			'sub_description_rating',
			[
				'label'   => esc_html__( 'Rating', 'glozin-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'0'    => __( 'None', 'glozin-addons' ),
					'1'    => __( '1 Star', 'glozin-addons' ),
					'2'    => __( '2 Stars', 'glozin-addons' ),
					'3'    => __( '3 Stars', 'glozin-addons' ),
					'4'    => __( '4 Stars', 'glozin-addons' ),
					'5'    => __( '5 Stars', 'glozin-addons' ),
				],
				'default'            => 5,
				'condition' => [
					'sub_description' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'sub_description_text',
			[
				'label'       => esc_html__( 'Text', 'glozin-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'condition' => [
					'sub_description' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'button_heading',
			[
				'label' => esc_html__( 'Button', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->register_button_repeater_controls($repeater);

		$repeater->add_control(
			'button_link_type',
			[
				'label'   => esc_html__( 'Apply Primary Link On', 'glozin-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'only' => esc_html__( 'Button Only', 'glozin-addons' ),
					'slide'  => esc_html__( 'Whole Slide', 'glozin-addons' ),
				],
				'default' => 'only',
				'conditions' => [
					'terms' => [
						[
							'name' => 'button_link[url]',
							'operator' => '!=',
							'value' => '',
						],
						[
							'name' => 'button_second_text',
							'operator' => '==',
							'value' => '',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'button_second_heading',
			[
				'label' => esc_html__( 'Button Second', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'button_second_text',
			[
				'label'       => __( 'Text', 'glozin-addons' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Click here', 'glozin-addons' ),
			]
		);

		$repeater->add_control(
			'button_second_link',
			[
				'label'       => __( 'Link', 'glozin-addons' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'https://your-link.com', 'glozin-addons' ),
				'default'     => [],
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab( 'style', [ 'label' => esc_html__( 'Style', 'glozin-addons' ) ] );

		$repeater->add_control(
			'custom_style',
			[
				'label'       => esc_html__( 'Custom', 'glozin-addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'Set custom style that will only affect this specific slide.', 'glozin-addons' ),
			]
		);

		$repeater->add_responsive_control(
			'custom_slides_horizontal_position',
			[
				'label'                => esc_html__( 'Horizontal Position', 'glozin-addons' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'options'              => [
					'left'   => [
						'title' => esc_html__( 'Left', 'glozin-addons' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'glozin-addons' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'glozin-addons' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'     => '',
				'selectors'            => [
					'{{WRAPPER}} .glozin-slides-elementor {{CURRENT_ITEM}} .glozin-slide' => 'justify-content: {{VALUE}}',
				],
				'selectors_dictionary' => [
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_responsive_control(
			'custom_slides_vertical_position',
			[
				'label'                => esc_html__( 'Vertical Position', 'glozin-addons' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'options'              => [
					'top'   => [
						'title' => esc_html__( 'Top', 'glozin-addons' ),
						'icon'  => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => esc_html__( 'Middle', 'glozin-addons' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom'  => [
						'title' => esc_html__( 'Bottom', 'glozin-addons' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'     => '',
				'selectors'            => [
					'{{WRAPPER}} .glozin-slides-elementor {{CURRENT_ITEM}} .glozin-slide' => 'align-items: {{VALUE}}',
				],
				'selectors_dictionary' => [
					'top'   => 'flex-start',
					'middle' => 'center',
					'bottom'  => 'flex-end',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_responsive_control(
			'custom_slides_text_align',
			[
				'label'       => esc_html__( 'Text Align', 'glozin-addons' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'start'   => [
						'title' => esc_html__( 'Left', 'glozin-addons' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'glozin-addons' ),
						'icon'  => 'eicon-text-align-center',
					],
					'end'  => [
						'title' => esc_html__( 'Right', 'glozin-addons' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'     => '',
				'selectors'   => [
					'{{WRAPPER}} .glozin-slides-elementor {{CURRENT_ITEM}} .glozin-slide' => 'text-align: {{VALUE}}',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'content_heading_name',
			[
				'label' => esc_html__( 'Content', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => [
					'terms' => [
						[
							'name'  => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_responsive_control(
			'content_custom_bg_color',
			[
				'label'      => esc_html__( 'Background Color', 'glozin-addons' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .glozin-slides-elementor {{CURRENT_ITEM}} .glozin-slide .glozin-slide__content' => 'background-color: {{VALUE}}',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'title_heading_name',
			[
				'label' => esc_html__( 'Title', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => [
					'terms' => [
						[
							'name'  => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_responsive_control(
			'title_custom_color',
			[
				'label'      => esc_html__( 'Color', 'glozin-addons' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .glozin-slides-elementor {{CURRENT_ITEM}} .glozin-slide .glozin-slide__title' => 'color: {{VALUE}}',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'title_custom_text_stroke',
				'selector' => '{{WRAPPER}} .glozin-slides-elementor {{CURRENT_ITEM}} .glozin-slide .glozin-slide__title',
				'conditions' => [
					'terms' => [
						[
							'name'  => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'desc_heading_name',
			[
				'label' => esc_html__( 'Description', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => [
					'terms' => [
						[
							'name'  => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_responsive_control(
			'content_custom_color',
			[
				'label'      => esc_html__( 'Color', 'glozin-addons' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .glozin-slides-elementor {{CURRENT_ITEM}} .glozin-slide .glozin-slide__description' => 'color: {{VALUE}}',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'custom_style',
							'value' => 'yes',
						],
					],
				]
			]
		);

		$repeater->add_control(
			'custom_button_options',
			[
				'label'        => __( 'Button', 'glozin-addons' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'label_off'    => __( 'Default', 'glozin-addons' ),
				'label_on'     => __( 'Custom', 'glozin-addons' ),
				'return_value' => 'yes',
				'separator' => 'before',
				'conditions' => [
					'terms' => [
						[
							'name'  => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->start_popover();

		$repeater->add_control(
			'custom_button_style_normal_heading',
			[
				'label' => esc_html__( 'Normal', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => [
					'terms' => [
						[
							'name'  => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'custom_button_background_color',
			[
				'label'      => esc_html__( 'Background Color', 'glozin-addons' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .glozin-slides-elementor {{CURRENT_ITEM}} .glozin-slide__button' => 'background-color: {{VALUE}}',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'custom_button_color',
			[
				'label'      => esc_html__( 'Color', 'glozin-addons' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .glozin-slides-elementor {{CURRENT_ITEM}} .glozin-slide__button' => 'color: {{VALUE}}',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'custom_button_border_color',
			[
				'label' => __( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-slides-elementor {{CURRENT_ITEM}} .glozin-slide__button' => 'border-color: {{VALUE}};',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'custom_button_style_hover_heading',
			[
				'label' => esc_html__( 'Hover', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => [
					'terms' => [
						[
							'name'  => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

			$repeater->add_control(
				'custom_button_hover_background_color',
				[
					'label' => __( 'Background Color', 'glozin-addons' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .glozin-slides-elementor {{CURRENT_ITEM}} .glozin-slide__button:hover' => 'background-color: {{VALUE}};',
					],
					'conditions' => [
						'terms' => [
							[
								'name'  => 'custom_style',
								'value' => 'yes',
							],
						],
					],
				]
			);

			$repeater->add_control(
				'custom_button_hover_color',
				[
					'label' => __( 'Color', 'glozin-addons' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .glozin-slides-elementor {{CURRENT_ITEM}} .glozin-slide__button:hover' => 'color: {{VALUE}};',
					],
					'conditions' => [
						'terms' => [
							[
								'name'  => 'custom_style',
								'value' => 'yes',
							],
						],
					],
				]
			);

			$repeater->add_control(
				'custom_button_hover_border_color',
				[
					'label' => __( 'Border Color', 'glozin-addons' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .glozin-slides-elementor {{CURRENT_ITEM}} .glozin-slide__button:hover' => 'border-color: {{VALUE}};',
					],
					'conditions' => [
						'terms' => [
							[
								'name'  => 'custom_style',
								'value' => 'yes',
							],
						],
					],
				]
			);

		$repeater->end_popover();

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'slides',
			[
				'label'      => esc_html__( 'Slides', 'glozin-addons' ),
				'type'       => Controls_Manager::REPEATER,
				'show_label' => true,
				'fields'     => $repeater->get_controls(),
				'default'    => [
					[
						'title'            => esc_html__( 'Slide 1 Title', 'glozin-addons' ),
						'description'      => esc_html__( 'Click edit button to change this text. Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'glozin-addons' ),
						'button_text'      => esc_html__( 'Click Here', 'glozin-addons' ),
					],
					[
						'title'          => esc_html__( 'Slide 2 Title', 'glozin-addons' ),
						'description'      => esc_html__( 'Click edit button to change this text. Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'glozin-addons' ),
						'button_text'      => esc_html__( 'Click Here', 'glozin-addons' ),
					],
					[
						'title'          => esc_html__( 'Slide 3 Title', 'glozin-addons' ),
						'description'      => esc_html__( 'Click edit button to change this text. Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'glozin-addons' ),
						'button_text'      => esc_html__( 'Click Here', 'glozin-addons' ),
					],
				],
			]
		);

		$this->register_aspect_ratio_controls( [], [ 'aspect_ratio_type' => '' ] );

		$this->end_controls_section();
	}

	protected function section_slider_options() {
		$this->start_controls_section(
			'section_slider_options',
			[
				'label' => esc_html__( 'Slider Options', 'glozin-addons' ),
				'type'  => Controls_Manager::SECTION,
			]
		);

		$this->add_control(
			'effect',
			[
				'label'   => esc_html__( 'Effect', 'glozin-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'fade'   	 => esc_html__( 'Fade', 'glozin-addons' ),
					'slide' 	 => esc_html__( 'Slide', 'glozin-addons' ),
				],
				'default' => 'slide',
				'toggle'  => false,
				'frontend_available' => true,
			]
		);

		$controls = [
			'slides_to_show'   => 1,
			'slides_to_scroll' => 1,
			'space_between'    => 30,
			'navigation'       => 'dots',
			'autoplay'         => '',
			'autoplay_speed'   => 3000,
			'pause_on_hover'   => 'yes',
			'animation_speed'  => 800,
			'infinite'         => '',
		];

		$this->register_carousel_controls($controls);

		$this->add_control(
			'center_mode',
			[
				'label'       => __( 'Center Mode', 'glozin-addons' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_off' => __( 'Off', 'glozin-addons' ),
				'label_on'  => __( 'On', 'glozin-addons' ),
				'frontend_available' => true,
				'prefix_class' => 'glozin-centermode-auto--'
			]
		);

		$this->add_control(
			'effect_zoom',
			[
				'label'       => __( 'Effect Zoom', 'glozin-addons' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_off' => __( 'Off', 'glozin-addons' ),
				'label_on'  => __( 'On', 'glozin-addons' ),
				'frontend_available' => true,
				'condition' => [
					'effect' => 'fade',
				],
			]
		);

		$this->add_control(
			'overflow_visible',
			[
				'label'       => __( 'Show Partial Slides', 'glozin-addons' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_off' => __( 'Off', 'glozin-addons' ),
				'label_on'  => __( 'On', 'glozin-addons' ),
				'frontend_available' => true,
				'prefix_class' => 'glozin-overflow-visible-auto--'
			]
		);

		$this->end_controls_section();
	}

	// Tab Style
	protected function section_style() {
		$this->section_style_content();
		$this->section_style_button();
		$this->section_style_button_second();
		$this->section_style_carousel();
	}

	// Els
	protected function section_style_title() {
		$this->add_control(
			'heading_title',
			[
				'label'     => esc_html__( 'Title', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator'  => 'before',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'      => esc_html__( 'Color', 'glozin-addons' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .glozin-slides-elementor .glozin-slide .glozin-slide__title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'title_text_stroke',
				'selector' => '{{WRAPPER}} .glozin-slides-elementor .glozin-slide .glozin-slide__title',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .glozin-slides-elementor .glozin-slide .glozin-slide__title',
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-slides-elementor .glozin-slide .glozin-slide__title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);
	}

	protected function section_style_before_title() {
		$this->add_control(
			'heading_before_title',
			[
				'label'     => esc_html__( 'Before Title', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator'  => 'before',
			]
		);

		$this->add_control(
			'before_title_color',
			[
				'label'      => esc_html__( 'Color', 'glozin-addons' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .glozin-slides-elementor .glozin-slide .glozin-slide__before-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'before_title_typography',
				'selector' => '{{WRAPPER}} .glozin-slides-elementor .glozin-slide .glozin-slide__before-title',
			]
		);


		$this->add_responsive_control(
			'before_title_background_color',
			[
				'label'      => esc_html__( 'Background Color', 'glozin-addons' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .glozin-slides-elementor .glozin-slide .glozin-slide__before-title' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'before_title_padding',
			[
				'label'      => esc_html__( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-slides-elementor .glozin-slide .glozin-slide__before-title' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'before_title_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-slides-elementor .glozin-slide .glozin-slide__before-title' => 'border-start-start-radius: {{TOP}}{{UNIT}}; border-start-end-radius: {{RIGHT}}{{UNIT}}; border-end-end-radius: {{BOTTOM}}{{UNIT}}; border-end-start-radius: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'before_title_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-slides-elementor .glozin-slide .glozin-slide__before-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);
	}

	protected function section_style_desc() {
		// Description
		$this->add_control(
			'heading_description',
			[
				'label'     => esc_html__( 'Description', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator'  => 'before',
			]
		);

		$this->add_control(
			'description_color',
			[
				'label'     => esc_html__( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-slides-elementor .glozin-slide__description' => 'color: {{VALUE}}',

				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .glozin-slides-elementor .glozin-slide__description',
			]
		);

		$this->add_responsive_control(
			'description_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-slides-elementor .glozin-slide .glozin-slide__description' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'description_padding',
			[
				'label'      => esc_html__( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-slides-elementor .glozin-slide .glozin-slide__description' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);
	}

	protected function section_style_sub_desc() {
		// Description
		$this->add_control(
			'heading_sub_description',
			[
				'label'     => esc_html__( 'Sub Description Text', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator'  => 'before',
			]
		);

		$this->add_control(
			'sub_description_color',
			[
				'label'     => esc_html__( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-slides-elementor .glozin-slide__sub-description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sub_description_typography',
				'selector' => '{{WRAPPER}} .glozin-slides-elementor .glozin-slide__sub-description',
			]
		);

		$this->add_responsive_control(
			'sub_description_margin',
			[
				'label'      => esc_html__( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-slides-elementor .glozin-slide .glozin-slide__sub-description' => 'margin-top: {{TOP}}{{UNIT}}; margin-inline-end: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}}; margin-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'heading_sub_description_rating',
			[
				'label'     => esc_html__( 'Sub Description Rating', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator'  => 'before',
			]
		);

		$this->add_responsive_control(
			'sub_description_rating_size',
			[
				'label'     => __( 'Size', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 200,
						'min' => 0,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-slide__sub-description .star-rating' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'sub_description_rating_gap',
			[
				'label'     => __( 'Gap', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 200,
						'min' => 0,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-slide__sub-description .star-rating' => '--gz-rating-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'sub_description_rating_color',
			[
				'label'     => esc_html__( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-slide__sub-description .star-rating .max-rating' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'sub_description_rating_color_active',
			[
				'label'     => esc_html__( 'Color Active', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-slide__sub-description .star-rating .user-rating' => 'color: {{VALUE}};',
				],
			]
		);
	}

	protected function section_style_content() {
		$this->start_controls_section(
			'section_style_slides',
			[
				'label' => esc_html__( 'Slides', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'slides_container_width',
			[
				'label'      => esc_html__( 'Container Width', 'glozin-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 1900,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .container-xxl' => 'max-width: {{SIZE}}{{UNIT}};',
					'(desktop){{WRAPPER}}.glozin-centermode-auto--yes .swiper' => 'max-width: {{SIZE}}{{UNIT}};',
					'(desktop){{WRAPPER}}.glozin-overflow-visible-auto--yes .swiper' => 'overflow: visible !important;',
					'(desktop){{WRAPPER}}.glozin-overflow-visible-auto--yes' => 'overflow: hidden !important;',
				],
			]
		);

		$this->add_responsive_control(
			'slides_padding',
			[
				'label'      => esc_html__( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .container-xxl' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'slides_horizontal_position',
			[
				'label'                => esc_html__( 'Horizontal Position', 'glozin-addons' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'options'              => [
					'left'   => [
						'title' => esc_html__( 'Left', 'glozin-addons' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'glozin-addons' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'glozin-addons' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'     => '',
				'selectors'            => [
					'{{WRAPPER}} .glozin-slides-elementor .glozin-slide' => 'justify-content: {{VALUE}}',
					'{{WRAPPER}} .glozin-slide__sub-description' => 'justify-content: {{VALUE}}',
				],
				'selectors_dictionary' => [
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				],
			]
		);

		$this->add_responsive_control(
			'slides_vertical_position',
			[
				'label'                => esc_html__( 'Vertical Position', 'glozin-addons' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'options'              => [
					'top'   => [
						'title' => esc_html__( 'Top', 'glozin-addons' ),
						'icon'  => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => esc_html__( 'Middle', 'glozin-addons' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom'  => [
						'title' => esc_html__( 'Bottom', 'glozin-addons' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'     => '',
				'selectors'            => [
					'{{WRAPPER}} .glozin-slides-elementor .glozin-slide' => 'align-items: {{VALUE}}',
				],
				'selectors_dictionary' => [
					'top'   => 'flex-start',
					'middle' => 'center',
					'bottom'  => 'flex-end',
				],
			]
		);

		$this->add_responsive_control(
			'slides_text_align',
			[
				'label'       => esc_html__( 'Text Align', 'glozin-addons' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'start'   => [
						'title' => esc_html__( 'Left', 'glozin-addons' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'glozin-addons' ),
						'icon'  => 'eicon-text-align-center',
					],
					'end'  => [
						'title' => esc_html__( 'Right', 'glozin-addons' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'     => '',
				'selectors'   => [
					'{{WRAPPER}} .glozin-slides-elementor .glozin-slide' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'slides_background_color_overlay',
			[
				'label'      => esc_html__( 'Background Color Overlay', 'glozin-addons' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .glozin-slides-elementor__item::after' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'slides_full_screen_desktop',
			[
				'label'     => esc_html__( 'Full Screen in Desktop', 'glozin-addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__( 'Yes', 'glozin-addons' ),
				'label_off' => esc_html__( 'No', 'glozin-addons' ),
				'return_value' => 'yes',
				'default'   => '',
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();
		$this->start_controls_section(
			'section_style_content',
			[
				'label' => esc_html__( 'Content', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'slides_content_width',
			[
				'label'      => esc_html__( 'Width', 'glozin-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 1900,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-slides-elementor .glozin-slide__content' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'slides_content_padding',
			[
				'label'      => esc_html__( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-slides-elementor .glozin-slide__content' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'slides_content_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-slides-elementor .glozin-slide__content' => 'border-start-start-radius: {{TOP}}{{UNIT}}; border-start-end-radius: {{RIGHT}}{{UNIT}}; border-end-end-radius: {{BOTTOM}}{{UNIT}}; border-end-start-radius: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'slides_content_background_color',
			[
				'label'      => esc_html__( 'Background Color', 'glozin-addons' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .glozin-slides-elementor .glozin-slide__content' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'heading_image',
			[
				'label'     => esc_html__( 'Image', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator'  => 'before',
			]
		);

		$this->add_responsive_control(
			'slides_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}}' => '--gz-image-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}}' => '--gz-image-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->section_style_before_title();

		$this->section_style_title();

		$this->section_style_desc();

		$this->section_style_sub_desc();

		$this->end_controls_section();
	}

	protected function section_style_button() {
		$this->start_controls_section(
			'section_style_button',
			[
				'label' => esc_html__( 'Button', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->register_button_style_controls();

		$this->end_controls_section();
	}

	protected function section_style_button_second() {
		$this->start_controls_section(
			'section_style_button_second',
			[
				'label' => esc_html__( 'Button Second', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'button_second_spacing_left',
			[
				'label'     => esc_html__( 'Spacing Left', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'vh' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-button__second' => 'margin-inline-start: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_second_style',
			[
				'label'   => __( 'Style', 'glozin-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
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
			'button_second_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-button__second' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_second_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-button__second' => '--gz-button-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-button__second' => '--gz-button-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_second_typography',
				'selector' => '{{WRAPPER}} .glozin-button__second',
			]
		);

		$this->add_responsive_control(
			'button_second_min_width',
			[
				'label' => esc_html__( 'Min Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'size_units' => [ 'px', 'em', 'rem', '%', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-button__second' => 'min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_second_min_height',
			[
				'label' => esc_html__( 'Min Height', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'size_units' => [ 'px', 'em', 'rem' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-button__second' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_second_border_width',
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
					'{{WRAPPER}} .glozin-button__second' => 'border-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'button_style' => [ 'outline-dark', 'outline' ],
				],
			]
		);

		$this->start_controls_tabs( 'tabs_button_second_style' );

		$this->start_controls_tab(
			'tab_button_second_normal',
			[
				'label' => __( 'Normal', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'button_second_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-button__second' => '--gz-button-bg-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_second_text_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-button__second' => '--gz-button-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_second_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-button__second' => '--gz-button-border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_second_hover',
			[
				'label' => __( 'Hover', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'button_second_background_hover_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-button__second' => '--gz-button-bg-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_second_hover_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-button__second' => '--gz-button-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_second_hover_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-button__second' => '--gz-button-border-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_second_background_effect_hover_color',
			[
				'label'     => __( 'Background Effect Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-button__second' => '--gz-button-eff-bg-color-hover: {{VALUE}};',
				],
				'condition' => [
					'button_style' => ['']
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function section_style_carousel() {
		$this->start_controls_section(
			'section_style_slider',
			[
				'label' => esc_html__( 'Slider Options', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->register_carousel_style_controls();

		$this->end_controls_section();
	}

	/**
	 * Render icon box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['slides'] ) ) {
			return;
		}

		$this->add_render_attribute( 'wrapper', 'class', [ 'glozin-slides-elementor', 'glozin-carousel--elementor', 'gz-image-rounded', 'swiper' ] );
		$this->add_render_attribute( 'wrapper', 'style', $this->render_aspect_ratio_style('', 1, true) );

		$this->add_render_attribute( 'inner', 'class', [ 'glozin-slides-elementor__inner', 'swiper-wrapper' ] );

		$this->add_render_attribute( 'slide', 'class', [ 'glozin-slide', 'container-xxl', 'd-flex', 'w-100' ] );
		$this->add_render_attribute( 'content', 'class', [ 'glozin-slide__content' ] );
		$this->add_render_attribute( 'title', 'class', [ 'glozin-slide__title' ] );
		$this->add_render_attribute( 'before_title', 'class', [ 'glozin-slide__before-title' ] );
		$this->add_render_attribute( 'description', 'class', [ 'glozin-slide__description' ] );
		$this->add_render_attribute( 'button', 'class', [ 'glozin-slide__button', 'gz-button' ] );

		if ( $settings['effect_zoom'] == 'yes' && $settings['effect'] == 'fade' ) {
			$this->add_render_attribute( 'wrapper', 'class', 'glozin-slides__effect-zoom' );
		}

		if ( ! empty( $settings['slides_content_background_color'] ) ) {
			$this->add_render_attribute( 'slide', 'class', [ 'glozin-slide__content-background' ] );
		}

		if ( $settings['slides_full_screen_desktop'] == 'yes' ) {
			$this->add_render_attribute( 'wrapper', 'class', ( 'glozin-slides__full-screen' ) );
		}
	?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<div <?php echo $this->get_render_attribute_string( 'inner' ); ?>>
			<?php
				$slides_count = count( $settings['slides'] );
				$slide_classes = $slides_count == 1 ? 'swiper-slide-active' : '';
				foreach ( $settings['slides'] as $index => $slide ) {
					$button_classes = ' glozin-slide__button';

					$sub_description 		= $this->get_repeater_setting_key( 'sub_description', 'slides', $index );
					$sub_desc_rating_key 	= $this->get_repeater_setting_key( 'sub_desc_rating', 'slides', $index );
					$sub_desc_text_key 		= $this->get_repeater_setting_key( 'sub_desc_text', 'slides', $index );
					$link_key 	  			= $this->get_repeater_setting_key( 'link', 'slides', $index );
					$image_key 				= $this->get_repeater_setting_key( 'image', 'slides', $index );

					$this->add_render_attribute( $sub_description, 'class', [ 'glozin-slide__sub-description', 'd-flex', 'align-items-center' ] );
					$this->add_render_attribute( $sub_desc_rating_key, 'class', [ 'glozin-slide__sub-description--rating', 'star-rating' ] );
					$this->add_render_attribute( $sub_desc_text_key, 'class', [ 'glozin-slide__sub-description--text' ] );
					$this->add_link_attributes( $link_key, $slide['button_link'] );
					$this->add_render_attribute( $link_key, 'class', [ 'glozin-slide__button--all', 'position-absolute' ] );
					$this->add_render_attribute( $image_key, 'class', [ 'glozin-slide__image', 'align-self-stretch', 'position-absolute', 'w-100', 'h-100', 'z-1' ] );

				?>
					<div class="elementor-repeater-item-<?php echo esc_attr( $slide['_id'] ); ?> glozin-slides-elementor__item swiper-slide gz-ratio gz-ratio-mobile <?php echo esc_attr( $slide_classes ); ?>">
						<div <?php echo $this->get_render_attribute_string( $image_key ); ?>>
							<?php
								if( ! empty( $slide['banner_background_img'] ) && ! empty( $slide['banner_background_img']['url'] ) ) {
									$image_args = [
										'image'        => ! empty( $slide['banner_background_img'] ) ? $slide['banner_background_img'] : '',
										'image_tablet' => ! empty( $slide['banner_background_img_tablet'] ) ? $slide['banner_background_img_tablet'] : '',
										'image_mobile' => ! empty( $slide['banner_background_img_mobile'] ) ? $slide['banner_background_img_mobile'] : '',
									];
									echo \Glozin\Addons\Helper::get_responsive_image_elementor( $image_args );
								}
							?>
						</div>
						<div <?php echo $this->get_render_attribute_string( 'slide' ); ?>>
							<?php
								if ( $slide['button_link_type'] == 'slide' ) {
									if( ! empty( $slide['button_link']['url'] ) ) {
										echo '<a '. $this->get_render_attribute_string( $link_key ) .'>';
										echo '<span class="screen-reader-text">'. $slide['button_text'] .'</span>';
										echo '</a>';
									}
								}
							?>
							<div <?php echo $this->get_render_attribute_string( 'content' ); ?>>
								<?php if ( $slide['before_title'] ) : ?>
									<div <?php echo $this->get_render_attribute_string( 'before_title' ); ?>><?php echo wp_kses_post( $slide['before_title'] ); ?></div>
								<?php endif; ?>

								<?php if ( $slide['title'] ) : ?>
									<h2 <?php echo $this->get_render_attribute_string( 'title' ); ?>><?php echo wp_kses_post( $slide['title'] ); ?></h2>
								<?php endif; ?>

								<?php if ( $slide['description'] ) : ?>
									<div <?php echo $this->get_render_attribute_string( 'description' ); ?>><?php echo wp_kses_post( $slide['description'] ); ?></div>
								<?php endif; ?>

								<?php if ( $slide['sub_description'] == 'yes' ) : ?>
									<div <?php echo $this->get_render_attribute_string( $sub_description ); ?>>
										<div <?php echo $this->get_render_attribute_string( $sub_desc_rating_key ); ?>>
											<?php echo $this->star_rating_html( $slide['sub_description_rating'] ); ?>
										</div>
										<?php if ( $slide['sub_description_text'] ) : ?>
											<div <?php echo $this->get_render_attribute_string( $sub_desc_text_key ); ?>><?php echo wp_kses_post( $slide['sub_description_text'] ); ?></div>
										<?php endif; ?>
									</div>
								<?php endif; ?>

								<?php
									$slide['button_style'] = $settings['button_style'];
									$slide['button_classes'] = $button_classes;
									$this->render_button( $slide, $index );

									if( ! empty( $slide['button_second_text'] ) && ! empty( $slide['button_second_link']['url'] ) ) {
										$button_second = array(
											'button_text'    => $slide['button_second_text'],
											'button_link'    => $slide['button_second_link'],
											'button_style'   => $settings['button_second_style'],
											'button_classes' => ' glozin-slide__button glozin-button__second'
										);
										$this->render_button( $button_second, $index . '_second' );
									}
								?>
							</div>
						</div>
					</div>
				<?php
				}
			?>
			</div>
			<div class="swiper-arrows">
				<?php echo $this->render_arrows(); ?>
			</div>
			<?php
				$classes = ' container-xxl';
				echo $this->render_pagination( $classes );
			?>
		</div>
	<?php
	}

	public function star_rating_html( $count ) {
		$html = '<span class="max-rating rating-stars">'
		        . \Glozin\Addons\Helper::inline_svg('icon=star')
		        . \Glozin\Addons\Helper::inline_svg('icon=star')
		        . \Glozin\Addons\Helper::inline_svg('icon=star')
		        . \Glozin\Addons\Helper::inline_svg('icon=star')
		        . \Glozin\Addons\Helper::inline_svg('icon=star')
		        . '</span>';
		$html .= '<span class="user-rating rating-stars" style="width:' . ( ( $count / 5 ) * 100 ) . '%">'
				. \Glozin\Addons\Helper::inline_svg('icon=star')
				. \Glozin\Addons\Helper::inline_svg('icon=star')
				. \Glozin\Addons\Helper::inline_svg('icon=star')
				. \Glozin\Addons\Helper::inline_svg('icon=star')
				. \Glozin\Addons\Helper::inline_svg('icon=star')
		         . '</span>';

		$html .= '<span class="screen-reader-text">';

		$html .= '</span>';

		return $html;
	}
}