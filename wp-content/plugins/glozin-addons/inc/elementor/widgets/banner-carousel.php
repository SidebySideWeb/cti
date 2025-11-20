<?php

namespace Glozin\Addons\Elementor\Widgets;

use Glozin\Addons\Elementor\Base\Carousel_Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Banner Carousel widget
 */
class Banner_Carousel extends Carousel_Widget_Base {
	use \Glozin\Addons\Elementor\Base\Aspect_Ratio_Base;
	use \Glozin\Addons\Elementor\Base\Button_Base;
	use \Glozin\Addons\Elementor\Base\Video_Base;

	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-banner-carousel';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( '[Glozin] Banner Carousel', 'glozin-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-carousel';
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
	 * Retrieve the list of scripts the widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return [
			'glozin-elementor-widgets'
		];
	}

	/**
	 * Retrieve the list of styles the widget depended on.
	 *
	 * Used to set styles dependencies required to run the widget.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget styles dependencies.
	 */
	public function get_style_depends() {
		return [
			'glozin-elementor-css',
			'glozin-banner-css',
		 ];
	}

	/**
	 * Register heading widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->content_sections();
		$this->style_sections();
	}

	protected function content_sections() {
		$this->start_controls_section(
			'section_options',
			[
				'label' => __( 'Banner', 'glozin-addons' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'banner_type',
			[
				'label' => __( 'Banner Type', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'image' => __( 'Image', 'glozin-addons' ),
					'video' => __( 'Video', 'glozin-addons' ),
				],
				'default' => 'image',
			]
		);

        $repeater->add_responsive_control(
			'image',
			[
				'label'   => esc_html__( 'Image', 'glozin-addons' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => wc_placeholder_img_src(),
				],
				'condition' => [
					'banner_type' => 'image',
				],
			]
		);

		$this->register_video_repeater_controls( $repeater, [ 'banner_type' => 'video' ] );

		$repeater->add_control(
			'sub_title',
			[
				'label' => __( 'Sub Title', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'label_block' => true,
			]
		);

        $repeater->add_control(
			'title',
			[
				'label' => __( 'Title', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => __( 'Enter your title', 'glozin-addons' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'description',
			[
				'label' => __( 'Description', 'glozin-addons' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => '',
			]
		);

		$repeater->add_control(
			'show_icon',
			[
				'label'     => esc_html__( 'Show Icon', 'glozin-addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'glozin-addons' ),
				'label_on'  => __( 'Show', 'glozin-addons' ),
				'default'	=> '',
				'return_value' => 'yes',
			]
		);

		$repeater->add_control(
			'icon_type',
			[
				'label' => __( 'Icon Type', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'icon' => __( 'Icon', 'glozin-addons' ),
					'image' => __( 'Image', 'glozin-addons' ),
				],
				'default' => 'icon',
				'condition' => [
					'show_icon' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'icon_type_icon',
			[
				'label' => __( 'Icon', 'glozin-addons' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fa fa-star',
					'library' => 'fa-solid',
				],
				'condition' => [
					'icon_type' => 'icon',
					'show_icon' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'icon_type_image',
			[
				'label' => __( 'Choose Image Icon', 'glozin-addons' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'icon_type' => 'image',
					'show_icon' => 'yes',
				],
			]
		);

		$this->register_button_repeater_controls( $repeater );

		$this->add_control(
			'banners',
			[
				'label'       => __( 'Banners', 'glozin-addons' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default' => [
					[
						'banner_type' => 'image',
						'image' => [
							'url' => wc_placeholder_img_src(),
						],
						'button_text' => __( 'Button Text', 'glozin-addons' ),
						'button_link' => [
							'url' => '#',
						],
					],
					[
						'banner_type' => 'image',
						'image' => [
							'url' => wc_placeholder_img_src(),
						],
						'button_text' => __( 'Button Text', 'glozin-addons' ),
						'button_link' => [
							'url' => '#',
						],
					],
					[
						'banner_type' => 'image',
						'image' => [
							'url' => wc_placeholder_img_src(),
						],
						'button_text' => __( 'Button Text', 'glozin-addons' ),
						'button_link' => [
							'url' => '#',
						],
					],
					[
						'banner_type' => 'image',
						'image' => [
							'url' => wc_placeholder_img_src(),
						],
						'button_text' => __( 'Button Text', 'glozin-addons' ),
						'button_link' => [
							'url' => '#',
						],
					],
				],
				'separator' => 'before',
			]
		);

		$this->register_aspect_ratio_controls( [], [ 'aspect_ratio_type' => 'vertical' ] );

		$this->add_control(
			'button_link_type',
			[
				'label'   => esc_html__( 'Apply Primary Link On', 'glozin-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'only' => esc_html__( 'Button Only', 'glozin-addons' ),
					'slide'  => esc_html__( 'Whole Banner', 'glozin-addons' ),
				],
				'default' => 'only',
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		// Carousel Settings
		$this->start_controls_section(
			'section_products_carousel',
			[
				'label' => __( 'Carousel Settings', 'glozin-addons' ),
			]
		);

		$controls = [
			'slides_to_show'    				=> 4,
			'slides_to_scroll'     				=> 1,
			'space_between'  					=> 10,
			'navigation'    					=> '',
			'autoplay' 							=> '',
			'autoplay_speed'      				=> 3000,
			'pause_on_hover'    				=> 'yes',
			'animation_speed'  					=> 800,
			'infinite'  						=> '',
			'slidesperview_auto' 				=> '',
		];

		$this->register_carousel_controls( $controls );

		$this->end_controls_section();
	}

	protected function style_sections() {
		$this->content_style_sections();
		$this->icon_style_sections();
		$this->button_style_sections();
		$this->carousel_style_sections();
	}

	protected function content_style_sections() {
		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Banner', 'glozin-addons' ),
                'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'vertical_position',
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
				'selectors' => [
					'{{WRAPPER}} .glozin-banner-carousel__slide' => 'align-items: {{VALUE}}',
				],
				'selectors_dictionary' => [
					'top'   => 'flex-start',
					'middle' => 'center',
					'bottom'  => 'flex-end',
				],
			]
		);

        $this->add_responsive_control(
			'text_align',
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
				'default' => 'center',
				'selectors'   => [
					'{{WRAPPER}} .glozin-banner-carousel__slide' => 'text-align: {{VALUE}}',
				],
			]
		);

        $this->add_responsive_control(
			'banner_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-banner__summary' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'content_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-banner-carousel' => 'border-start-start-radius: {{TOP}}{{UNIT}}; border-start-end-radius: {{RIGHT}}{{UNIT}}; border-end-end-radius: {{BOTTOM}}{{UNIT}}; border-end-start-radius: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'content_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-banner-carousel__slide' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'image_heading',
			[
				'label' => esc_html__( 'Image', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}}' => '--gz-image-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}}' => '--gz-image-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'sub_title_heading',
			[
				'label' => esc_html__( 'Sub Title', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sub_title_typography',
				'selector' => '{{WRAPPER}} .glozin-banner__sub-title',
			]
		);

        $this->add_control(
			'sub_title_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-banner__sub-title' => 'color: {{VALUE}};',
				],
			]
		);

        $this->add_responsive_control(
			'sub_title_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-banner__sub-title' => 'margin-top: {{TOP}}{{UNIT}}; margin-inline-end: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}}; margin-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
			'title_heading',
			[
				'label' => esc_html__( 'Title', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .glozin-banner__title',
			]
		);

        $this->add_control(
			'title_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-banner__title' => 'color: {{VALUE}};',
				],
			]
		);

        $this->add_responsive_control(
			'title_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-banner__title' => 'margin-top: {{TOP}}{{UNIT}}; margin-inline-end: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}}; margin-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'description_heading',
			[
				'label' => esc_html__( 'Description', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .glozin-banner__description',
			]
		);

        $this->add_control(
			'description_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-banner__description' => 'color: {{VALUE}};',
				],
			]
		);

        $this->add_responsive_control(
			'description_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-banner__description' => 'margin-top: {{TOP}}{{UNIT}}; margin-inline-end: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}}; margin-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function icon_style_sections() {
		// Style Icon
		$this->start_controls_section(
			'section_style_icon',
			[
				'label'     => __( 'Icon', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Primary Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-banner-carousel__icon' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'icon_background_color',
			[
				'label' => __( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-banner-carousel__icon' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Size', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					]
				],
				'default' => [],
				'selectors' => [
					'{{WRAPPER}} .glozin-banner-carousel__icon' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .icon-type-image .glozin-banner-carousel__icon' => 'max-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'icon_border',
				'label' => esc_html__( 'Border', 'glozin-addons' ),
				'selector' => '{{WRAPPER}} .glozin-banner-carousel__icon',
			]
		);

		$this->add_control(
			'icon_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-banner-carousel__icon' => '--gz-image-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-banner-carousel__icon' => '--gz-image-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-banner-carousel__icon' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_spacing',
			[
				'label' => __( 'Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					]
				],
				'default' => [],
				'selectors' => [
					'{{WRAPPER}} .glozin-banner-carousel__icon' => '--glozin-icon-margin: {{SIZE}}{{UNIT}};',

				],
			]
		);

		$this->end_controls_section();
	}

	protected function button_style_sections() {
		$this->start_controls_section(
			'section_button_style',
			[
				'label' => __( 'Button', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->register_button_style_controls('light');

		$this->end_controls_section();
	}

	protected function carousel_style_sections() {
		$this->start_controls_section(
			'section_carousel_style',
			[
				'label'     => __( 'Carousel Settings', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
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

		$col = $settings['slides_to_show'];
		$col_tablet = ! empty( $settings['slides_to_show_tablet'] ) ? $settings['slides_to_show_tablet'] : $col;
		$col_mobile = ! empty( $settings['slides_to_show_mobile'] ) ? $settings['slides_to_show_mobile'] : $col;

        $this->add_render_attribute( 'banner', 'class', [ 'glozin-banner-carousel', 'glozin-carousel--elementor', 'swiper' ] );
		$this->add_render_attribute( 'banner', 'data-desktop', $col );
		$this->add_render_attribute( 'banner', 'data-tablet', $col_tablet );
		$this->add_render_attribute( 'banner', 'data-mobile', $col_mobile );
		$this->add_render_attribute( 'banner', 'style', $this->render_space_between_style() );
		$this->add_render_attribute( 'banner', 'style', $this->render_aspect_ratio_style('', 1, true) );
		$this->render_slidesperview_auto_class_style( 'banner' );

        $this->add_render_attribute( 'wrapper', 'class', [ 'glozin-banner-carousel__wrapper', 'swiper-wrapper' ] );

       echo '<div ' . $this->get_render_attribute_string( 'banner' ) . '>';
       echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>';
	   foreach ( $settings['banners'] as $index => $slide ) :
			$wrapper_key 		= $this->get_repeater_setting_key( 'wrapper', 'banner', $index );
			$image_key     		= $this->get_repeater_setting_key( 'image', 'banner', $index );
			$summary_key     	= $this->get_repeater_setting_key( 'summary', 'banner', $index );
			$sub_title_key     	= $this->get_repeater_setting_key( 'sub_title', 'banner', $index );
			$title_key     		= $this->get_repeater_setting_key( 'title', 'banner', $index );
			$description_key    = $this->get_repeater_setting_key( 'description', 'banner', $index );
			$button_key     	= $this->get_repeater_setting_key( 'button', 'banner', $index );
			$icon_key     		= $this->get_repeater_setting_key( 'icon_type', 'banner', $index );
			$link_key     		= $this->get_repeater_setting_key( 'button_link', 'banner', $index );

			$this->add_render_attribute( $wrapper_key, 'class', ['elementor-repeater-item-' . $slide['_id'], 'd-flex', 'align-items-end', 'justify-content-center', 'overflow-hidden', 'gz-image-rounded', 'glozin-banner-carousel__slide', 'gz-hover-zoom', 'gz-hover-effect', 'overflow-hidden', 'swiper-slide', 'position-relative', 'gz-ratio', 'gz-ratio-mobile' ] );
			$this->add_render_attribute( $image_key, 'class', [ 'glozin-banner__image', 'glozin-banner-carousel__image', 'position-absolute', 'glozin-elementor-video', 'gz-ratio', 'gz-ratio-mobile', 'align-self-stretch', 'z-1', 'w-100', 'h-100' ] );
			$this->add_render_attribute( $summary_key, 'class', [ 'glozin-banner__summary', 'glozin-banner-carousel__summary', 'position-relative', 'px-5', 'px-xl-30', 'py-40', 'text-light', 'w-100', 'z-2', $slide['show_icon'] == 'yes' ? 'icon-type-' . $slide['icon_type'] : '' ] );
			$this->add_render_attribute( $sub_title_key, 'class', [ 'glozin-banner__sub-title', 'glozin-banner-carousel__sub-title', 'mb-5', 'text-light', 'text-uppercase', 'fw-semibold', 'fs-13', 'lh-normal' ] );
			$this->add_render_attribute( $title_key, 'class', [ 'glozin-banner__title', 'glozin-banner-carousel__title', 'mt-0', 'mb-20', 'text-light', 'fw-semibold', 'fs-28', 'heading-letter-spacing' ] );
			$this->add_render_attribute( $description_key, 'class', [ 'glozin-banner__description', 'mb-25', 'text-light' ] );
			$this->add_render_attribute( $button_key, 'class', [ 'glozin-banner__button' ] );
			$this->add_render_attribute( $icon_key, 'class', [ 'glozin-banner__icon', 'glozin-banner-carousel__icon', 'lh-1', 'd-inline-block' ] );
			$this->add_render_attribute( $link_key, 'class', [ 'glozin-button-link', 'glozin-banner__button--all', 'position-absolute', 'top-0', 'end-0', 'bottom-0', 'start-0', 'z-2' ] );

			$this->add_link_attributes( $link_key, $slide['button_link'] );
			$slide['button_classes'] = ' glozin-banner__button glozin-banner-carousel__button position-relative z-3';

			echo '<div '. $this->get_render_attribute_string( $wrapper_key ) .'>';
				echo '<div '. $this->get_render_attribute_string( $image_key ) .'>';
					if ( $this->has_video( $slide ) && 'video' == $slide['banner_type'] ) {
						$this->render_video( $slide );
					} else {
						if( ! empty( $slide['image'] ) && ! empty( $slide['image']['url'] ) ) {
							echo \Glozin\Addons\Helper::get_responsive_image_elementor( $slide );
						}
					}
				echo '</div>';
				echo '<div '. $this->get_render_attribute_string( $summary_key ) .'>';
					if ( $slide['show_icon'] == 'yes' ) {
						echo '<div '. $this->get_render_attribute_string( $icon_key ) .'>';
							if ( 'image' == $slide['icon_type'] ) {
								if( ! empty( $slide['icon_type_image'] ) && ! empty( $slide['icon_type_image']['url'] ) ) {
									$settings['image'] = $slide['icon_type_image'];
									$settings['image_size'] = 'thumbnail';

									echo wp_kses_post( \Elementor\Group_Control_Image_Size::get_attachment_image_html( $settings ) );
								}
							} else {
								if(!empty($slide['icon_type_icon']['value'])) {
									echo '<span class="glozin-svg-icon">';
										Icons_Manager::render_icon( $slide['icon_type_icon'], [ 'aria-hidden' => 'true' ] );
									echo '</span>';
								}
							}
						echo '</div>';
					}
					echo '<div '. $this->get_render_attribute_string( $sub_title_key ) .'>';
						echo wp_kses_post( $slide['sub_title'] );
					echo '</div>';
					echo '<div '. $this->get_render_attribute_string( $title_key ) .'>';
						echo wp_kses_post( $slide['title'] );
					echo '</div>';
					if( ! empty( $slide['description'] ) ) {
						echo '<div '.$this->get_render_attribute_string( $description_key ).'>'. wp_kses_post( $slide['description'] ).'</div>';
					}
					echo '<div '. $this->get_render_attribute_string( $button_key ) .'>';
						$this->render_button($slide, $index);
					echo '</div>';
				echo '</div>';
				if ( $settings['button_link_type'] == 'slide' ) {
					if( ! empty( $slide['button_link']['url'] ) ) {
						$screen_reader_text = ! empty( $slide['button_text'] ) ? $slide['button_text'] : $slide['title'];	
						if( empty( $screen_reader_text ) ) {
							$screen_reader_text = __( 'View Banner', 'glozin-addons' );
						}
						echo '<a '. $this->get_render_attribute_string( $link_key ) .'>';
						echo '<span class="screen-reader-text">'. $screen_reader_text .'</span>';
						echo '</a>';
					}
				}
			echo '</div>';
	   endforeach;
       echo '</div>';
	   echo '<div class="swiper-arrows">'. $this->render_arrows() .'</div>';
	   echo $this->render_pagination();
       echo '</div>';
	}
}
