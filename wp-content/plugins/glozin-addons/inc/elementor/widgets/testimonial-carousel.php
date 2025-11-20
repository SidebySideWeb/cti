<?php
namespace Glozin\Addons\Elementor\Widgets;

use Glozin\Addons\Elementor\Base\Carousel_Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Glozin\Addons\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Testimonial Carousel widget.
 *
 * Elementor widget that displays an eye-catching headlines.
 *
 * @since 1.0.0
 */
class Testimonial_Carousel extends Carousel_Widget_Base {
	use \Glozin\Addons\Elementor\Base\Video_Base;
	use \Glozin\Addons\Elementor\Base\Aspect_Ratio_Base;

	/**
	 * Get widget name.
	 *
	 * Retrieve Image Box widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-testimonial-carousel';
	}

	/**
	 * Get widget title
	 *
	 * Retrieve Image Box widget title
	 *
	 * @return string Widget title
	 */
	public function get_title() {
		return __( '[Glozin] Testimonial Carousel', 'glozin-addons' );
	}

	/**
	 * Get widget icon
	 *
	 * Retrieve Image Box widget icon
	 *
	 * @return string Widget icon
	 */
	public function get_icon() {
		return 'eicon-testimonial-carousel';
	}

	/**
	 * Get widget categories
	 *
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @return string Widget categories
	 */
	public function get_categories() {
		return [ 'glozin-addons' ];
	}

	/**
	 * Get widget keywords.
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'testimonial carousel', 'carousel', 'testimonial', 'glozin' ];
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
	 * Get style depends
	 *
	 * @return array
	 */
	public function get_style_depends() {
		return [
			'glozin-elementor-css',
			'glozin-testimonial-carousel-css'
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
		$this->section_content();
		$this->section_style();
	}

	// Tab Content
	protected function section_content() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'glozin-addons' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'source',
			[
				'label' => esc_html__( 'Media Source', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'image' 	=> esc_html__( 'Image', 'glozin-addons' ),
					'video' 	=> esc_html__( 'Video', 'glozin-addons' ),
				],
				'default' => 'image',
			]
		);

		$repeater->add_control(
			'image',
			[
				'label'    => __( 'Image', 'glozin-addons' ),
				'type' => Controls_Manager::MEDIA,
				'media_types' => [ 'image' ],
				'condition' => [
					'source' => 'image',
				],
			]
		);

		$this->register_video_repeater_controls( $repeater, [ 'source' => 'video' ] );

		$repeater->add_control(
			'testimonial_rating',
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
				'default' => 5,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'testimonial_name',
			[
				'label' => __( 'Name', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'John Doe', 'glozin-addons' ),
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'testimonial_icon_type',
			[
				'label' => __( 'Verify Icon Type', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'icon' => __( 'Icon', 'glozin-addons' ),
					'image' => __( 'Image', 'glozin-addons' ),
					'external' => __( 'External', 'glozin-addons' ),
				],
				'default' => 'icon',
			]
		);

		$repeater->add_control(
			'testimonial_icon',
			[
				'label' => __( 'Verify Icon', 'glozin-addons' ),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'testimonial_icon_type' => 'icon',
				],
			]
		);

		$repeater->add_control(
			'testimonial_image',
			[
				'label' => __( 'Choose Verify Image', 'glozin-addons' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'testimonial_icon_type' => 'image',
				],
			]
		);

		$repeater->add_control(
			'testimonial_icon_url',
			[
				'label' => __( 'External Verify Icon URL', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'testimonial_icon_type' => 'external',
				],
			]
		);

		$repeater->add_control(
			'testimonial_text',
			[
				'label' => __( 'Verify Text', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Verified Buyer', 'glozin-addons' ),
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'testimonial_content',
			[
				'label' => __( 'Content', 'glozin-addons' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'rows' => '10',
				'default' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'glozin-addons' ),
			]
		);

		$repeater->add_control(
			'testimonial_product_id',
			[
				'label'       => esc_html__( 'Product', 'glozin-addons' ),
				'placeholder' => esc_html__( 'Click here and start typing...', 'glozin-addons' ),
				'type'        => 'glozin-autocomplete',
				'default'     => '',
				'label_block' => true,
				'multiple'    => false,
				'source'      => 'product',
				'sortable'    => true,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'testimonials',
			[
				'label'       => __( 'Testimonials', 'glozin-addons' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ testimonial_name }}}',
				'default' => [
					[
						'testimonial_name'    => __( 'Name #1', 'glozin-addons' ),
					],
					[
						'testimonial_name'    => __( 'Name #2', 'glozin-addons' ),
					],
					[
						'testimonial_name'    => __( 'Name #3', 'glozin-addons' ),
					],
					[
						'testimonial_name'    => __( 'Name #4', 'glozin-addons' ),
					]
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'testimonial_display',
			[
				'label' => esc_html__( 'Display', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'split' 	=> esc_html__( 'Split', 'glozin-addons' ),
					'stacked' 	=> esc_html__( 'Stacked', 'glozin-addons' ),
				],
				'default' => 'split',
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		// Carousel Settings
		$this->start_controls_section(
			'section_slider_options',
			[
				'label' => __( 'Carousel Settings', 'glozin-addons' ),
			]
		);

		$controls = [
			'slides_to_show'  => 2,
			'slides_to_scroll' => 1,
			'space_between'   => 30,
			'navigation'      => 'arrows',
			'autoplay'        => '',
			'autoplay_speed'  => 3000,
			'animation_speed' => 800,
			'infinite'        => '',
		];

		$this->register_carousel_controls($controls);

		$this->add_responsive_control(
			'slidesperview_auto',
			[
				'label' => __( 'Slides Per View Auto', 'glozin-addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Off', 'glozin-addons' ),
				'label_on'  => __( 'On', 'glozin-addons' ),
				'default'   => '',
				'responsive' => true,
				'frontend_available' => true,
				'prefix_class' => 'glozin%s-slidesperview-auto--'
			]
		);

		$this->end_controls_section();
	}

	// Tab Style
	protected function section_style() {
		$this->start_controls_section(
			'section_style_item',
			[
				'label' => __( 'Testimonial Item', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'item_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-testimonial-carousel__item' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-testimonial-carousel__item' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'item_border',
				'selector'  => '{{WRAPPER}} .glozin-testimonial-carousel__item::before',
			]
		);

		$this->add_control(
			'item_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-testimonial-carousel__item' => 'border-top-left-radius: {{TOP}}{{UNIT}}; border-top-right-radius: {{RIGHT}}{{UNIT}}; border-bottom-right-radius: {{BOTTOM}}{{UNIT}}; border-bottom-left-radius: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .glozin-testimonial-carousel__item::before' => 'border-top-left-radius: {{TOP}}{{UNIT}}; border-top-right-radius: {{RIGHT}}{{UNIT}}; border-bottom-right-radius: {{BOTTOM}}{{UNIT}}; border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_video_heading',
			[
				'label' => esc_html__( 'Image & Video', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->register_aspect_ratio_controls();

		$this->add_control(
			'image_video_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-testimonial-carousel__image' => '--gz-image-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-testimonial-carousel__image' => '--gz-image-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_video_width',
			[
				'label'     => __( 'Width', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-testimonial-carousel__item' => '--col-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'summary_heading',
			[
				'label' => esc_html__( 'Summary', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'summary_text_align',
			[
				'label'       => esc_html__( 'Text Align', 'glozin-addons' ),
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
					'{{WRAPPER}} .glozin-testimonial-carousel__summary' => 'text-align: {{VALUE}}',
					'{{WRAPPER}} .glozin-testimonial-carousel__name-text' => 'justify-content: {{VALUE}}',
					'{{WRAPPER}} .glozin-testimonial-carousel__product' => 'justify-content: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'summary_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-testimonial-carousel__summary' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'rating_heading',
			[
				'label' => esc_html__( 'Rating', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'rating_size',
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
					'{{WRAPPER}} .glozin-testimonial-carousel__rating.star-rating' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'rating_gap',
			[
				'label' => __( 'Gap', 'glozin-addons' ),
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
					'{{WRAPPER}} .glozin-testimonial-carousel__rating.star-rating' => '--gz-rating-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'rating_color',
			[
				'label'     => esc_html__( 'Color Active', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-testimonial-carousel__rating.star-rating .user-rating' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'rating_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-testimonial-carousel__rating' => 'margin-top: {{TOP}}{{UNIT}}; margin-inline-end: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}}; margin-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'name_text_heading',
			[
				'label' => esc_html__( 'Name & Text', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'name_text_gap',
			[
				'label' => __( 'Gap', 'glozin-addons' ),
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
					'{{WRAPPER}} .glozin-testimonial-carousel__name-text' => 'gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'name_text_spacing',
			[
				'label'     => __( 'Spacing', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 200,
						'min' => 0,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-testimonial-carousel__name-text' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'name_heading',
			[
				'label' => esc_html__( 'Name', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'name_color',
			[
				'label'     => esc_html__( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-testimonial-carousel__name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'name_typography',
				'selector' => '{{WRAPPER}} .glozin-testimonial-carousel__name',
			]
		);

		$this->add_control(
			'icon_heading',
			[
				'label' => esc_html__( 'Icon', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => esc_html__( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-testimonial-carousel__text .glozin-testimonial-carousel__text-icon' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .glozin-testimonial-carousel__text .glozin-testimonial-carousel__text-icon' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .glozin-testimonial-carousel__text[data-icon-type="image"] .glozin-testimonial-carousel__text-icon' => 'max-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'icon_spacing',
			[
				'label'     => __( 'Spacing', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 200,
						'min' => 0,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-testimonial-carousel__text' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'text_heading',
			[
				'label' => esc_html__( 'Text', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => esc_html__( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-testimonial-carousel__text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography',
				'selector' => '{{WRAPPER}} .glozin-testimonial-carousel__text',
			]
		);

		$this->add_control(
			'content_heading',
			[
				'label' => esc_html__( 'Content', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'content_color',
			[
				'label'     => esc_html__( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-testimonial-carousel__content' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'selector' => '{{WRAPPER}} .glozin-testimonial-carousel__content',
			]
		);

		$this->end_controls_section();

		$this->section_style_product();

		$this->section_style_carousel();
	}

	protected function section_style_product() {
		$this->start_controls_section(
			'section_product_style',
			[
				'label'     => __( 'Product', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'product_direction',
			[
				'label' => __( 'Direction', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					''           => esc_html__( 'Default', 'glozin' ),
					'column'                  => esc_html__( 'Column', 'glozin' ),
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-testimonial-carousel__product' => 'flex-direction: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'product_gap',
			[
				'label'      => esc_html__( 'Gap', 'glozin-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-testimonial-carousel__product' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'product_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-testimonial-carousel__product' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'product_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-testimonial-carousel__product' => 'margin-top: {{TOP}}{{UNIT}}; margin-inline-end: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}}; margin-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'product_border',
				'selector'  => '{{WRAPPER}} .glozin-testimonial-carousel__product',
			]
		);

		$this->add_control(
			'product_image_heading',
			[
				'label' => esc_html__( 'Image', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'product_image_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-testimonial-carousel__product-image' => '--gz-image-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-testimonial-carousel__product-image' => '--gz-image-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'product_title_heading',
			[
				'label' => esc_html__( 'Title', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'product_title_typography',
				'selector' => '{{WRAPPER}} .glozin-testimonial-carousel__product-title',
			]
		);

		$this->add_control(
			'product_title_color',
			[
				'label'     => esc_html__( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-testimonial-carousel__product-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'product_title_color_hover',
			[
				'label'     => esc_html__( 'Hover Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-testimonial-carousel__product-title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'product_title_spacing',
			[
				'label'     => __( 'Spacing', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 200,
						'min' => 0,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-testimonial-carousel__product-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'product_price_heading',
			[
				'label' => esc_html__( 'Price', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'product_price_typography',
				'selector' => '{{WRAPPER}} .price',
			]
		);

		$this->add_control(
			'product_price_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .price' => 'color: {{VALUE}};',
					'{{WRAPPER}} .price del' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gz-price-unit' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function section_style_carousel() {
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
	 * Render heading widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$col = $settings['slides_to_show'];
		$col_tablet = ! empty( $settings['slides_to_show_tablet'] ) ? $settings['slides_to_show_tablet'] : $col;
		$col_mobile = ! empty( $settings['slides_to_show_mobile'] ) ? $settings['slides_to_show_mobile'] : $col;

		$this->add_render_attribute( 'container', 'class', [ 'glozin-testimonial-carousel', 'testimonial-carousel--' . $settings['testimonial_display'] ] );
		$this->add_render_attribute( 'wrapper', 'class', [ 'glozin-carousel--elementor', 'swiper' ] );
		$this->add_render_attribute( 'wrapper', 'data-desktop', $col );
		$this->add_render_attribute( 'wrapper', 'data-tablet', $col_tablet );
		$this->add_render_attribute( 'wrapper', 'data-mobile', $col_mobile );
		$this->add_render_attribute( 'wrapper', 'style', $this->render_space_between_style() );
		$this->add_render_attribute( 'wrapper', 'style', $this->render_aspect_ratio_style() );

		$this->add_render_attribute( 'inner', 'class', [ 'glozin-testimonial-carousel__inner', 'swiper-wrapper' ] );
		$this->add_render_attribute( 'item', 'class', [ 'glozin-testimonial-carousel__item', 'swiper-slide', 'overflow-hidden' ] );

		$this->add_render_attribute( 'image', 'class', [ 'glozin-testimonial-carousel__image', 'glozin-elementor-video', 'd-none', 'd-block-md', 'column-md-custom', 'glozin-elementor-video', 'gz-ratio', 'gz-hover-zoom', 'gz-hover-effect', 'overflow-hidden' ] );
		$this->add_render_attribute( 'summary', 'class', [ 'glozin-testimonial-carousel__summary', 'column-md-custom-remaining', 'pt-25', 'px-30', 'pb-30' ] );

		$this->add_render_attribute( 'rating', 'class', [ 'glozin-testimonial-carousel__rating', 'star-rating', 'fs-14' ] );
		$this->add_render_attribute( 'name_text', 'class', [ 'glozin-testimonial-carousel__name-text', 'd-flex', 'flex-wrap', 'gap-10', 'align-items-center', 'text-dark-grey', 'mb-15' ] );
		$this->add_render_attribute( 'name', 'class', [ 'glozin-testimonial-carousel__name', 'fw-semibold', 'text-dark' ] );
		$this->add_render_attribute( 'text', 'class', [ 'glozin-testimonial-carousel__text', 'd-inline-flex', 'align-items-center', 'gap-5', 'fs-13', 'lh-1' ] );
		$this->add_render_attribute( 'content', 'class', [ 'glozin-testimonial-carousel__content' ] );

		$this->add_render_attribute( 'product', 'class', [ 'glozin-testimonial-carousel__product', 'd-flex', 'align-items-center', 'gap-15', 'border-top', 'pt-30', 'mt-25' ] );
		$this->add_render_attribute( 'product_summary', 'class', [ 'glozin-testimonial-carousel__product-summary' ] );
		$this->add_render_attribute( 'product_title', 'class', [ 'glozin-testimonial-carousel__product-title', 'woocommerce-loop-product__title', 'fw-semibold', 'lh-normal' ] );
		$this->add_render_attribute( 'product_price', 'class', [ 'glozin-testimonial-carousel__product-price', 'price' ] );
	?>
		<div <?php echo $this->get_render_attribute_string( 'container' );?>>
			<div <?php echo $this->get_render_attribute_string( 'wrapper' );?>>
				<div <?php echo $this->get_render_attribute_string( 'inner' );?>>
				<?php foreach( $settings['testimonials'] as $index => $testimonial ) : ?>
					<?php
						$icon_exist = true;

						if ( 'image' == $testimonial['testimonial_icon_type'] ) {
							$icon_exist = ! empty($testimonial['testimonial_image']['url']) ? true : false;
						} elseif ( 'external' == $testimonial['testimonial_icon_type'] ) {
							$icon_exist = ! empty($testimonial['testimonial_icon_url']) ? true : false;
						} else {
							$icon_exist = ! empty($testimonial['testimonial_icon']) && ! empty($testimonial['testimonial_icon']['value']) ? true : false;
						}
					?>
					<div <?php echo $this->get_render_attribute_string( 'item' );?> data-image="<?php echo ( ! empty( $testimonial['image']['url'] ) && 'image' == $testimonial['source'] ) || ( ! empty( $testimonial['video']['url'] ) && 'video' == $testimonial['source'] ) ? 'true' : 'false'; ?>">
						<?php if ( ! empty( $testimonial['image']['url'] ) && 'image' == $testimonial['source'] ) : ?>
							<div <?php echo $this->get_render_attribute_string( 'image' );?>>
								<?php
									$settings['image'] = $testimonial['image'];
									$settings['image_size'] = 'full';
									echo wp_kses_post( \Elementor\Group_Control_Image_Size::get_attachment_image_html( $settings ) );
								?>
							</div>
						<?php endif; ?>
						<?php if ( $this->has_video( $testimonial ) && 'video' == $testimonial['source'] ) : ?>
							<div <?php echo $this->get_render_attribute_string( 'image' );?>>
								<?php $this->render_video( $testimonial ); ?>
							</div>
						<?php endif; ?>
						<div <?php echo $this->get_render_attribute_string( 'summary' );?>>
							<div <?php echo $this->get_render_attribute_string( 'rating' ); ?>><?php echo $this->star_rating_html( $testimonial['testimonial_rating'] ); ?></div>
							<div <?php echo $this->get_render_attribute_string( 'name_text' ); ?>>
								<?php if(  ! empty( $testimonial['testimonial_name'] ) ) : ?>
									<div <?php echo $this->get_render_attribute_string( 'name' );?>><?php echo wp_kses_post( $testimonial['testimonial_name'] ); ?></div>
								<?php endif; ?>
								<?php if(  ! empty( $testimonial['testimonial_text'] ) || $icon_exist ) : ?>
									<div <?php echo $this->get_render_attribute_string( 'text' ); ?> data-icon-type="<?php echo $testimonial['testimonial_icon_type']; ?>">
										<?php
										if( $icon_exist ) {
											if ( 'image' == $testimonial['testimonial_icon_type'] ) {
												if( ! empty( $testimonial['testimonial_image'] ) && ! empty( $testimonial['testimonial_image']['url'] ) ) :
													$testimonial['image_size'] = 'thumbnail';
													echo '<span class="glozin-testimonial-carousel__text-icon">';
														echo wp_kses_post( \Elementor\Group_Control_Image_Size::get_attachment_image_html( $testimonial ) );
													echo '</span>';
												endif;
											} elseif ( 'external' == $testimonial['testimonial_icon_type'] ) {
												echo '<span class="glozin-testimonial-carousel__text-icon">';
													echo $testimonial['testimonial_icon_url'] ? sprintf( '<img alt="%s" src="%s">', esc_attr( $testimonial['testimonial_name'] ), esc_url( $testimonial['testimonial_icon_url'] ) ) : '';
												echo '</span>';
											} else {
												echo '<span class="glozin-svg-icon glozin-testimonial-carousel__text-icon">';
													Icons_Manager::render_icon( $testimonial['testimonial_icon'], [ 'aria-hidden' => 'true' ] );
												echo '</span>';
											}
										} else {
											echo Helper::inline_svg( [ 'icon' => 'icon-tick', 'class' => 'glozin-testimonial-carousel__text-icon fs-10 has-vertical-align' ] );
										}
										?>
										<?php echo wp_kses_post( $testimonial['testimonial_text'] ); ?>
									</div>
								<?php endif; ?>
							</div>
							<?php if(  ! empty( $testimonial['testimonial_content'] ) ) : ?>
								<div <?php echo $this->get_render_attribute_string( 'content' );?>><?php echo wp_kses_post( $testimonial['testimonial_content'] ); ?></div>
							<?php endif; ?>
							<?php
								$product_id = $testimonial[ 'testimonial_product_id' ];
								$product = $product_id ? wc_get_product( $product_id ) : '';
								if( ! empty( $product ) ) :
									$link_for = '';
									$link_for = esc_html__( 'View product:', 'glozin-addons' );
									$link_for .= ' ' . $product->get_name();
									$product_image_key = 'product_image_' . $index;
									$this->add_render_attribute( $product_image_key, 'class', [ 'glozin-testimonial-carousel__product-image', 'gz-image-rounded', 'position-relative', 'overflow-hidden', 'gz-hover-zoom', 'gz-hover-effect', 'gz-ratio', 'product-thumbnails--fadein', 'flex-shrink-0', 'd-none', 'd-block-md' ] );
									if( $index == 0 ) {
										$this->add_render_attribute( $product_image_key, 'class', 'd-block-md' );
									}
									$this->add_render_attribute( $product_image_key, 'aria-label', $link_for );
									$this->add_render_attribute( $product_image_key, 'href', get_permalink( $product_id )  ); ?>
									<div <?php echo $this->get_render_attribute_string( 'product' ); ?>>
										<a <?php echo $this->get_render_attribute_string( $product_image_key ); ?>>
											<?php echo wp_get_attachment_image( $product->get_image_id(), 'thumbnail' ); ?>
											<?php
												$image_ids = $product->get_gallery_image_ids();
												if ( ! empty( $image_ids ) ) {
													echo wp_get_attachment_image( $image_ids[0], 'thumbnail', false, array( 'class' => 'attachment-woocommerce_thumbnail size-woocommerce_thumbnail product-thumbnails--fadein-image' ) );
												}
											?>
										</a>
										<div <?php echo $this->get_render_attribute_string( 'product_summary' ); ?>>
											<div <?php echo $this->get_render_attribute_string( 'product_title' ); ?>>
												<a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>" aria-label="<?php echo esc_html( $link_for ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
											</div>
											<div <?php echo $this->get_render_attribute_string( 'product_price' ); ?>>
												<?php echo wp_kses_post( $product->get_price_html() ); ?>
											</div>
										</div>
									</div>
								<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
				</div>
				<?php echo '<div class="swiper-arrows">' . $this->render_arrows() . '</div>'; ?>
				<?php echo $this->render_pagination(); ?>
			</div>
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