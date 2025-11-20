<?php
namespace Glozin\Addons\Elementor\Widgets;

use Glozin\Addons\Elementor\Base\Carousel_Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor widget.
 *
 * Elementor widget that displays an eye-catching headlines.
 *
 * @since 1.0.0
 */
class Categories_Carousel extends Carousel_Widget_Base {
	use \Glozin\Addons\Elementor\Base\Aspect_Ratio_Base;
	use \Glozin\Addons\Elementor\Base\Button_Base;

	/**
	 * Get widget name.
	 *
	 * Retrieve Stores Location widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-categories-carousel';
	}

	/**
	 * Get widget title
	 *
	 * Retrieve Stores Location widget title
	 *
	 * @return string Widget title
	 */
	public function get_title() {
		return __( '[Glozin] Product Categories Carousel', 'glozin-addons' );
	}

	/**
	 * Get widget icon
	 *
	 * Retrieve TeamMemberGrid widget icon
	 *
	 * @return string Widget icon
	 */
	public function get_icon() {
		return 'eicon-carousel';
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
				'label' => __( 'Categories', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'source',
			[
				'label'       => esc_html__( 'Source', 'glozin-addons' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'default' => esc_html__( 'Default', 'glozin-addons' ),
					'custom'  => esc_html__( 'Custom', 'glozin-addons' ),
				],
				'default'     => 'default',
				'label_block' => true,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'number',
			[
				'label'           => esc_html__( 'Item Per Page', 'glozin-addons' ),
				'type'            => Controls_Manager::NUMBER,
				'min'             => 1,
				'max'             => 50,
				'default' 		=> '6',
				'frontend_available' => true,
				'condition'   => [
					'source' => 'default',
				],
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'     => esc_html__( 'Order By', 'glozin-addons' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					''           => esc_html__( 'Default', 'glozin-addons' ),
					'date'       => esc_html__( 'Date', 'glozin-addons' ),
					'title'      => esc_html__( 'Title', 'glozin-addons' ),
					'count'      => esc_html__( 'Count', 'glozin-addons' ),
					'menu_order' => esc_html__( 'Menu Order', 'glozin-addons' ),
				],
				'default'   => '',
				'condition'   => [
					'source' => 'default',
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			[
				'label'    => __( 'Image', 'glozin-addons' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [],
			]
		);

		$repeater->add_control(
			'products_type',
			[
				'label'     => esc_html__( 'Products Type', 'glozin-addons' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'best_sellers' => esc_html__( 'Best Sellers', 'glozin-addons' ),
					'new_arrivals' => esc_html__( 'New Arrivals', 'glozin-addons' ),
					'popular'      => esc_html__( 'Popular', 'glozin-addons' ),
					'category'     => esc_html__( 'Category', 'glozin-addons' ),
				],
				'default'   => 'category',
			]
		);

		$repeater->add_control(
			'products_type_link',
			[
				'label'       => __( 'Link', 'glozin-addons' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'https://your-link.com', 'glozin-addons' ),
				'default'     => [],
				'condition'   => [
					'products_type!' => 'category',
				],
			]
		);

		$repeater->add_control(
			'product_cat',
			[
				'label'       => esc_html__( 'Product Categories', 'glozin-addons' ),
				'placeholder' => esc_html__( 'Click here and start typing...', 'glozin-addons' ),
				'type'        => 'glozin-autocomplete',
				'default'     => '',
				'label_block' => true,
				'multiple'    => false,
				'source'      => 'product_cat',
				'sortable'    => true,
				'condition'   => [
					'products_type' => 'category',
				],
			]
		);

		$repeater->add_control(
			'product_cat_title', [
				'label' => esc_html__( 'Title', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
			]
		);

		$repeater->add_control(
			'product_cat_description', [
				'label' => esc_html__( 'Description', 'glozin-addons' ),
				'type' => Controls_Manager::TEXTAREA,
			]
		);

		$this->add_control(
			'items',
			[
				'label' => esc_html__( 'Items', 'glozin-addons' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [],
				'title_field'   => '{{{ product_cat }}}',
				'condition'   => [
					'source' => 'custom',
				],

			]
		);
		$this->add_control(
			'title_tag',
			[
				'label' => __( 'Title HTML Tag', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1'  => __( 'H1', 'glozin-addons' ),
					'h2'  => __( 'H2', 'glozin-addons' ),
					'h3'  => __( 'H3', 'glozin-addons' ),
					'h4'  => __( 'H4', 'glozin-addons' ),
					'h5'  => __( 'H5', 'glozin-addons' ),
					'h6'  => __( 'H6', 'glozin-addons' ),
					'div' => __( 'div', 'glozin-addons' ),
					'span' => __( 'span', 'glozin-addons' ),
					'p' => __( 'p', 'glozin-addons' ),
				],
				'default' => 'h3'
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
			'show_cat_count',
			[
				'label'     => esc_html__( 'Show Count', 'glozin-addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'glozin-addons' ),
				'label_on'  => __( 'Show', 'glozin-addons' ),
				'default'	=> '',
				'return_value' => 'yes',
				'selectors' => [
					'{{WRAPPER}} .glozin-categories-carousel__cat-count' => 'display: inline-block;',
				],
			]
		);

		$this->add_control(
			'show_cat_description',
			[
				'label'     => esc_html__( 'Show Description', 'glozin-addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'glozin-addons' ),
				'label_on'  => __( 'Show', 'glozin-addons' ),
				'default'	=> '',
				'return_value' => 'yes',
				'selectors' => [
					'{{WRAPPER}} .glozin-categories-carousel__cat-description' => 'display: block;',
				],
			]
		);

		$this->add_control(
			'number_word',
			[
				'label'           => esc_html__( 'Description limit', 'glozin-addons' ),
				'type'            => Controls_Manager::NUMBER,
				'min'             => 1,
				'default' 		=> '50',
				'frontend_available' => true,
				'condition'   => [
					'show_cat_description' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_button',
			[
				'label'     => esc_html__( 'Show Button', 'glozin-addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'glozin-addons' ),
				'label_on'  => __( 'Show', 'glozin-addons' ),
				'default'	=> '',
				'return_value' => 'yes',
				'selectors' => [
					'{{WRAPPER}} .glozin-categories-carousel__button' => 'display: inline-flex;',
					'{{WRAPPER}} .glozin-categories-carousel__content' => 'display: flex;',
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

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image',
				'default' => 'full'
			]
		);

		$this->register_aspect_ratio_controls( [], [ 'aspect_ratio_type' => 'square' ] );

		$this->add_control(
			'orginal_image',
			[
				'label'     => esc_html__( 'Orginal Image', 'glozin-addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'No', 'glozin-addons' ),
				'label_on'  => __( 'Yes', 'glozin-addons' ),
				'description' => esc_html__( 'Enable this to remove the fixed aspect ratio and let the image scale naturally based on its height.', 'glozin-addons' ),
				'default'	=> '',
				'return_value' => 'yes',
			]
		);

		$this->end_controls_section();

		$this->section_view_all_button_options();

		$this->start_controls_section(
			'section_slider_options',
			[
				'label' => esc_html__( 'Carousel Settings', 'glozin-addons' ),
				'type'  => Controls_Manager::SECTION,
			]
		);

		$controls = [
			'slides_to_show'   => 4,
			'slides_to_scroll' => 1,
			'space_between'    => 30,
			'navigation'       => 'both',
			'autoplay'         => '',
			'autoplay_speed'   => 3000,
			'pause_on_hover'   => 'yes',
			'animation_speed'  => 800,
			'infinite'         => '',
			'reveal_on_scroll' => '',
			'slidesperview_auto' => '',
		];

		$this->register_carousel_controls($controls);

		$this->end_controls_section();
	}

	protected function section_view_all_button_options() {
		$this->start_controls_section(
			'section_view_all_options',
			[
				'label' => esc_html__( 'View All Options', 'glozin-addons' ),
				'type'  => Controls_Manager::SECTION,
			]
		);

		$this->add_control(
			'view_all_btn_heading',
			[
				'label' => esc_html__( 'View All Button', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_view_all_btn',
			[
				'label'        => __( 'Show View All Button', 'glozin-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_off'    => __( 'Hide', 'glozin-addons' ),
				'label_on'     => __( 'Show', 'glozin-addons' ),
				'default'      => 'no',
			]
		);

        $this->add_responsive_control(
			'view_all_btn_image',
			[
				'label'    => __( 'Image', 'glozin-addons' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => wc_placeholder_img_src(),
				],
                'condition' => [
					'show_view_all_btn' => 'yes',
				],
			]
		);

		$this->add_control(
			'view_all_btn_title', [
				'label' => esc_html__( 'Title', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'default'     => __( 'View All', 'glozin-addons' ),
				'condition'   => [
					'show_view_all_btn' => 'yes',
				],
			]
		);

		$this->add_control(
			'view_all_btn_text', [
				'label' => esc_html__( 'Button Text', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'default'     => __( 'Click here', 'glozin-addons' ),
				'condition'   => [
					'show_view_all_btn' => 'yes',
				],
			]
		);

        $this->add_control(
			'view_all_btn_link',
			[
				'label'       => __( 'Button Link', 'glozin-addons' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'https://your-link.com', 'glozin-addons' ),
				'default'     => [
					'url' => '#',
				],
				'condition'   => [
					'show_view_all_btn' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

    // Tab Style
	protected function section_style() {
		$this->start_controls_section(
			'section_style',
			[
				'label'     => __( 'Content', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'content_position',
			[
				'label'   => esc_html__( 'Content position', 'glozin-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'side'  => esc_html__( 'Side on image', 'glozin-addons' ),
					'below' => esc_html__( 'Below the image', 'glozin-addons' ),
				],
				'default' => 'below',
			]
		);

		$this->add_responsive_control(
			'content_align',
			[
				'label'       => esc_html__( 'Alignment', 'glozin-addons' ),
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
				'default'     => 'start',
				'selectors'   => [
					'{{WRAPPER}} .glozin-categories-carousel__content' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'content_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-categories-carousel__item' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-categories-carousel__item' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'content_position' => 'below'
				]
			]
		);

		$this->add_responsive_control(
			'side_content_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-categories-carousel__content' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'content_position' => 'side'
				]
			]
		);

		$this->add_control(
			'image_style_heading',
			[
				'label' => esc_html__( 'Image', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'image_zoom',
			[
				'label'   => esc_html__( 'Zoom when hover image', 'glozin-addons' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes'
			]
		);

		$this->add_responsive_control(
			'image_max_width',
			[
				'label'      => esc_html__( 'Max Width', 'glozin-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-categories-carousel__image .glozin-categories-carousel__thumbnail' => 'max-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'content_position' => 'below'
				]
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}}' => '--gz-image-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}}' => '--gz-image-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_spacing',
			[
				'label'      => esc_html__( 'Spacing', 'glozin-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-categories-carousel__image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'content_position' => 'below'
				]
			]
		);

		$this->add_control(
			'gradient_image',
			[
				'label' => __( 'Gradient', 'glozin-addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'glozin-addons' ),
				'label_on'  => __( 'Show', 'glozin-addons' ),
				'default'   => '',
				'condition' => [
					'content_position' => 'side',
				],
			]
		);

		$this->add_control(
			'gradient_image_popover_toggle',
			[
				'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'label' => esc_html__( 'Background', 'glozin-addons' ),
				'label_off' => esc_html__( 'Default', 'glozin-addons' ),
				'label_on' => esc_html__( 'Custom', 'glozin-addons' ),
				'return_value' => 'yes',
				'condition' => [
					'gradient_image' => 'yes',
					'content_position' => 'side',
				],
			]
		);

		$this->start_popover();

		$this->add_control(
			'gradient_image_heading',
			[
				'type'  => Controls_Manager::HEADING,
				'label' => esc_html__( 'Background', 'glozin-addons' ),
				'condition' => [
					'content_position' => 'side',
				],
			]
		);

		$this->add_control(
			'gradient_image_color_primary',
			[
				'label' => __( 'Color Primary', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-categories-carousel--side' => '--gz-gradient-color-primary: {{VALUE}};',
				],
				'condition' => [
					'content_position' => 'side',
				],
			]
		);

		$this->add_control(
			'gradient_image_color_secondary',
			[
				'label' => __( 'Color Secondary', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-categories-carousel--side' => '--gz-gradient-color-secondary: {{VALUE}};',
				],
				'condition' => [
					'content_position' => 'side',
				],
			]
		);

		$this->add_control(
			'gradient_image_angle',
			[
				'label' => esc_html__( 'Angle', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'deg', 'grad', 'rad', 'turn', 'custom' ],
				'default' => [],
				'selectors' => [
					'{{WRAPPER}} .glozin-categories-carousel--side' => '--gz-gradient-angle: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'content_position' => 'side',
				],
			]
		);

		$this->end_popover();

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
				'selector' => '{{WRAPPER}} .glozin-categories-carousel__title a',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-categories-carousel__title a' => '--gz-link-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_color_hover',
			[
				'label'     => __( 'Hover Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-categories-carousel__title a:hover' => '--gz-link-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label'     => __( 'Spacing', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-categories-carousel__title' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'count_heading',
			[
				'label' => esc_html__( 'Count', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'count_type',
			[
				'label'       => esc_html__( 'Type', 'glozin-addons' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'beside' => esc_html__( 'Beside Title', 'glozin-addons' ),
					'below'  => esc_html__( 'Below Title', 'glozin-addons' ),
				],
				'default'     => 'beside',
				'label_block' => true,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'count_typography',
				'selector' => '{{WRAPPER}} .glozin-categories-carousel__cat-count',
			]
		);

		$this->add_control(
			'count_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-categories-carousel__cat-count' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'count_spacing',
			[
				'label' => __( 'Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					]
				],
				'default' => [],
				'selectors' => [
					'{{WRAPPER}} .glozin-categories-carousel__title .glozin-categories-carousel__cat-count' => 'margin-inline-start: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .glozin-categories-carousel__cat-count.count_type-below' => 'margin-top: {{SIZE}}{{UNIT}}',
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
				'selector' => '{{WRAPPER}} .glozin-categories-carousel__cat-description',
			]
		);

		$this->add_control(
			'description_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-categories-carousel__cat-description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'description_spacing',
			[
				'label'     => __( 'Spacing', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-categories-carousel__cat-description' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label' => esc_html__( 'Button Style', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->register_button_style_controls( 'light' );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_carousel',
			[
				'label' => esc_html__( 'Carousel Style', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
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

		$this->add_render_attribute( 'wrapper', 'class', [ 'glozin-categories-carousel', 'glozin-carousel--elementor' ] );
		$this->add_render_attribute( 'wrapper', 'style', [ $this->render_aspect_ratio_style() ] );
		$this->add_render_attribute( 'inner', 'class', [ 'glozin-categories-carousel__inner', 'swiper' ] );
		$this->add_render_attribute( 'inner', 'data-desktop', $col );
		$this->add_render_attribute( 'inner', 'data-tablet', $col_tablet );
		$this->add_render_attribute( 'inner', 'data-mobile', $col_mobile );
		$this->add_render_attribute( 'inner', 'style', $this->render_space_between_style() );
		$this->render_slidesperview_auto_class_style( 'inner' );

		$this->add_render_attribute( 'wrapper_inner', 'class', [ 'glozin-categories-carousel__wrapper', 'swiper-wrapper' ] );
		$this->add_render_attribute( 'wrapper', 'class', [ 'glozin-categories-carousel--' . esc_attr( $settings['content_position'] ) ] );
		if ( $settings['gradient_image'] == 'yes' && $settings['content_position'] == 'side' ) {
			$this->add_render_attribute( 'wrapper', 'class', 'glozin-categories-carousel-gradient' );
		}

		$gz_ratio = $settings['orginal_image'] != 'yes' ? ' gz-ratio' : '';

		$title_tag = $settings['title_tag'];

		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>';
			echo '<div ' . $this->get_render_attribute_string( 'inner' ) . '>';
				echo '<div ' . $this->get_render_attribute_string( 'wrapper_inner' ) . '>';
					if ( $settings['source'] == 'default' ) {
						$term_args = [
							'taxonomy' => 'product_cat',
							'orderby'  => $settings['orderby'],
						];

						if( $settings['number'] ) {
							$limit = $settings['show_view_all_btn'] == 'yes' ? intval( $settings['number'] ) - 1 : intval( $settings['number'] );
							$term_args['number'] = $limit;
						}

						$terms = get_terms( $term_args );

						foreach ( $terms as $index => $term ) {
							$item_key  = $this->get_repeater_setting_key( 'item', 'categories_carousel', $index );
							$image_key  = $this->get_repeater_setting_key( 'image', 'categories_carousel', $index );
							$content_key  = $this->get_repeater_setting_key( 'content', 'categories_carousel', $index );
							$this->add_render_attribute( $item_key, 'class', [ 'glozin-categories-carousel__item', 'position-relative', 'gz-image-rounded', 'swiper-slide' ] );
							$this->add_render_attribute( $image_key, 'class', [ 'glozin-categories-carousel__image', 'position-relative', 'overflow-hidden', $settings['content_position'] == 'below' ? 'gz-image-rounded' : '' ] );
							$this->add_render_attribute( $content_key, 'class', [ 'glozin-categories-carousel__content', 'align-items-center', 'justify-content-between', 'gap-10', $settings['content_position'] == 'side' ? 'position-absolute left-0 bottom-0 z-2 w-100' : '', ] );

							$thumbnail_id = absint( get_term_meta( $term->term_id, 'thumbnail_id', true ) );
							$settings['image']['url'] = wp_get_attachment_image_src( $thumbnail_id );
							$settings['image']['id']  = $thumbnail_id;
							$image = Group_Control_Image_Size::get_attachment_image_html( $settings );

							if ( empty( $image ) ) {
								$image = '<img src="'. wc_placeholder_img_src() .'" title="'. esc_attr( $term->name ) .'" alt="'. esc_attr( $term->name ) .'" loading="lazy"/>';
							}

							$count_text = $term->count == 1 ? esc_html__( 'Product', 'glozin-addons' ) : esc_html__( 'Products', 'glozin-addons' );
							$count_text = $settings['count_type'] == 'below' ? $count_text : '';

							$count_html_beside = $settings['count_type'] == 'beside' ? '<span class="glozin-categories-carousel__cat-count ms-5 fs-12 align-top">'. ( $term->count ) .' '. $count_text .'</span>' : '';
							$count_html_below = $settings['count_type'] == 'below' ? '<span class="glozin-categories-carousel__cat-count text-dark mt-5 d-block count_type-below">'. ( $term->count ) .' '. $count_text .'</span>' : '';
							$link_for = esc_html__('Link for', 'glozin-addons');
							$link_for .= ' ' . $term->name;
							?>
								<div <?php echo $this->get_render_attribute_string( $item_key ); ?>>
									<div <?php echo $this->get_render_attribute_string( $image_key ); ?>>
										<a class="glozin-categories-carousel__thumbnail<?php echo esc_attr( $gz_ratio ); ?> gz-hover-zoom gz-hover-effect overflow-hidden" aria-label="<?php echo esc_attr($link_for);?>" href="<?php echo esc_url( get_term_link( $term->term_id, 'product_cat' ) ); ?>"><?php echo $image; ?></a>
									</div>
									<div <?php echo $this->get_render_attribute_string( $content_key ); ?>>
										<div class="glozin-categories-carousel__summary">
											<<?php echo esc_attr( $title_tag ); ?> class="glozin-categories-carousel__title h6 mb-0 fw-medium"><a href="<?php echo esc_url( get_term_link( $term->term_id, 'product_cat' ) ); ?>"><?php echo esc_html( $term->name ); ?><?php echo $count_html_beside; ?></a></<?php echo esc_attr( $title_tag ); ?>>
											<?php if( ! empty( $term->description ) ) : ?>
												<div class="glozin-categories-carousel__cat-description"><?php echo \Glozin\Addons\Helper::get_content_limit( $settings['number_word'], $term->description ); ?></div>
											<?php endif; ?>
											<?php echo $count_html_below; ?>
										</div>
										<?php
											$button_link = [ 'url' => esc_url( get_term_link( $term->term_id, 'product_cat' ) ) ];
											$button_args = [
												'no_text' => true,
												'classes' => 'glozin-categories-carousel__button',
												'icon_default' => \Glozin\Addons\Helper::get_svg( 'arrow-top' ),
												'aria_label' => esc_html__( 'Link for', 'glozin-addons' ) . ' ' . $term->name,
											];
											$this->render_button( '', $index, $button_link, $button_args );
										?>

									</div>
								</div>
							<?php
						}
					} else {
						foreach( $settings['items'] as $index => $item ) {
							$item_key  = $this->get_repeater_setting_key( 'item', 'categories_carousel', $index );
							$image_key  = $this->get_repeater_setting_key( 'image', 'categories_carousel', $index );
							$content_key  = $this->get_repeater_setting_key( 'content', 'categories_carousel', $index );
							$this->add_render_attribute( $item_key, 'class', [ 'glozin-categories-carousel__item', 'position-relative',  $settings['content_position'] == 'side' ? 'gz-image-rounded' : '', 'overflow-hidden', 'swiper-slide', ] );
							$this->add_render_attribute( $image_key, 'class', [ 'glozin-categories-carousel__image', 'position-relative', 'overflow-hidden', $settings['content_position'] == 'below' ? 'gz-image-rounded' : '' ] );
							$this->add_render_attribute( $content_key, 'class', [ 'glozin-categories-carousel__content', 'align-items-center', 'justify-content-between', 'gap-10', $settings['content_position'] == 'side' ? 'position-absolute left-0 bottom-0 z-2 w-100' : '', ] );

							$check_cat = false;
							$term_count = 0;
							$term_name = esc_html( 'glozin' );
							$term_url = [ 'url' => '#' ];

							if( $item['products_type'] == 'category' ) {
								$term = get_term_by( 'slug', $item['product_cat'], 'product_cat' );

								if( is_wp_error( $term ) || empty( $term ) ) {
									continue;
								}

								$term_count = $term->count;
								$term_name = ! empty($item['product_cat_title']) ? $item['product_cat_title'] : $term->name;
								$term_url['url'] = get_term_link( $term->term_id, 'product_cat' );
								$check_cat = true;
							} else {
								$args = [
									'status' => 'publish',
									'stock_status' => 'instock',
									'catalog_visibility' => ['visible', 'catalog', 'search'],
									'limit' => -1,
									'return' => 'ids',
								];

								$query = new \WC_Product_Query($args);
								$term_count = count($query->get_products());

								if ( $item['products_type'] == 'best_sellers' ) {
									$term_name = ! empty($item['product_cat_title']) ? $item['product_cat_title'] : esc_html__( 'Best Sellers', 'glozin-addons' );
									$term_url['url'] = get_permalink( wc_get_page_id( 'shop' ) ) . '?orderby=rating';
								}

								if ( $item['products_type'] == 'new_arrivals' ) {
									$term_name = ! empty($item['product_cat_title']) ? $item['product_cat_title'] : esc_html__( 'New Arrivals', 'glozin-addons' );
									$term_url['url'] = get_permalink( wc_get_page_id( 'shop' ) ) . '?orderby=date';
								}

								if ( $item['products_type'] == 'new_arrivals' ) {
									$term_name = ! empty($item['product_cat_title']) ? $item['product_cat_title'] : esc_html__( 'New Arrivals', 'glozin-addons' );
									$term_url['url'] = get_permalink( wc_get_page_id( 'shop' ) ) . '?orderby=date';
								}

								if ( $item['products_type'] == 'popular' ) {
									$term_name = ! empty($item['product_cat_title']) ? $item['product_cat_title'] : esc_html__( 'Most Popular', 'glozin-addons' );
									$term_url['url'] = get_permalink( wc_get_page_id( 'shop' ) ) . '?orderby=popularity';
								}

								if( ! empty( $item['products_type_link']['url'] ) ) {
									$term_url = $item['products_type_link'];
								}
							}

							$settings['image'] = $item['image'];
							$image_html = wp_kses_post( \Elementor\Group_Control_Image_Size::get_attachment_image_html( $settings ) );

							if ( $check_cat && empty( $item['image']['url'] ) ) {
								$thumbnail_id = absint( get_term_meta( $term->term_id, 'thumbnail_id', true ) );
								if( ! empty( $thumbnail_id ) ) {
									$settings['image']['url'] = wp_get_attachment_image_src( $thumbnail_id );
									$settings['image']['id']  = $thumbnail_id;
									$image_html = Group_Control_Image_Size::get_attachment_image_html( $settings );
								}
							}

							$count_text = $term_count == 1 ? esc_html__( 'Product', 'glozin-addons' ) : esc_html__( 'Products', 'glozin-addons' );
							$count_text = $settings['count_type'] == 'below' ? $count_text : '';

							$count_html_beside = $settings['count_type'] == 'beside' ? '<span class="glozin-categories-carousel__cat-count ms-5 fs-12 align-top">'. ( $term_count ) .' '. $count_text .'</span>' : '';
							$count_html_below = $settings['count_type'] == 'below' ? '<span class="glozin-categories-carousel__cat-count text-dark mt-5 d-block count_type-below">'. ( $term_count ) .' '. $count_text .'</span>' : '';
							$link_for = esc_html__('Link for', 'glozin-addons');
							$link_for .= ' ' . $term_name;
							?>
								<div <?php echo $this->get_render_attribute_string( $item_key ); ?>>
									<div <?php echo $this->get_render_attribute_string( $image_key ); ?>>
										<a class="glozin-categories-carousel__thumbnail gz-image-rounded<?php echo esc_attr( $gz_ratio ); ?> overflow-hidden <?php echo esc_attr( $settings['image_zoom'] == 'yes' ) ? 'gz-hover-zoom gz-hover-effect' : ''; ?>" aria-label="<?php echo esc_attr($link_for);?>" href="<?php echo esc_url( $term_url['url'] ); ?>">
											<?php echo $image_html; ?>
										</a>
									</div>
									<div <?php echo $this->get_render_attribute_string( $content_key ); ?>>
										<div class="glozin-categories-carousel__summary">
											<<?php echo esc_attr( $title_tag ); ?> class="glozin-categories-carousel__title h6 mb-0"><a href="<?php echo esc_url( $term_url['url'] ); ?>"><?php echo esc_html( $term_name ); ?><?php echo $count_html_beside; ?></a></<?php echo esc_attr( $title_tag ); ?>>
											<?php if( ( $check_cat && ! empty( $term->description ) ) || ! empty( $item['product_cat_description'] ) ) : ?>
												<div class="glozin-categories-carousel__cat-description">
													<?php
													if( ! empty( $item['product_cat_description'] ) ) {
														echo wp_kses_post( $item['product_cat_description'] );
													} else {
														if( $check_cat && ! empty( $term->description ) ) {
															echo \Glozin\Addons\Helper::get_content_limit( $settings['number_word'], $term->description );
														}
													}
													?>
												</div>
											<?php endif; ?>
											<?php echo $count_html_below; ?>
										</div>
										<?php
											$button_link = $term_url;
											$button_args = [
												'no_text' => true,
												'classes' => 'glozin-categories-carousel__button',
												'icon_default' => \Glozin\Addons\Helper::get_svg( 'arrow-top' ),
												'aria_label' => esc_html__( 'Link for', 'glozin-addons' ) . ' ' . $term_name,
											];
											$this->render_button( '', $index, $button_link, $button_args ); ?>
									</div>
								</div>
							<?php
						}
					}

					$this->view_all_button( $gz_ratio );

				echo '</div>';
				echo $this->render_pagination();
				echo '<div class="swiper-arrows">';
					echo $this->render_arrows();
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}

	protected function view_all_button( $gz_ratio ) {
		$settings = $this->get_settings_for_display();
		$title_tag = $settings['title_tag'];

		if ( $settings['show_view_all_btn'] != 'yes' ) {
			return;
		}

		$count_html = '<div class="glozin-categories-carousel__cat-count">';
		$count = wp_count_posts('product')->publish;
		$product_text = $count > 1 ? esc_html__('items', 'glozin-addons')  : esc_html__('item', 'glozin-addons');
		$count_html .= sprintf( '%s %s', $count, $product_text );
		$count_html .= '</div>';

		$this->add_render_attribute( 'view_all_item', 'class', [ 'glozin-categories-carousel__item', 'position-relative', 'gz-image-rounded', 'overflow-hidden', 'swiper-slide', ] );
		$this->add_render_attribute( 'view_all_image', 'class', [ 'glozin-categories-carousel__image', 'position-relative', 'overflow-hidden', $settings['content_position'] == 'below' ? 'gz-image-rounded' : '' ] );
		$this->add_render_attribute( 'view_all_content', 'class', [ 'glozin-categories-carousel__content', 'align-items-center', 'justify-content-between', 'gap-10', $settings['content_position'] == 'side' ? 'position-absolute left-0 bottom-0 z-2 w-100' : '', ] );
		?>
			<div <?php echo $this->get_render_attribute_string( 'view_all_item' ); ?>>
				<div <?php echo $this->get_render_attribute_string( 'view_all_image' ); ?>>
					<?php if( !empty( $settings['view_all_btn_link']['url'] ) ) {
						$link_for = esc_html__('Link for', 'glozin-addons');
						$link_for .= ' ' . $settings['view_all_btn_text'];

						$settings['button_text'] = $settings['view_all_btn_text'];
						$settings['button_link'] = $settings['view_all_btn_link'];
					} ?>
					<?php if( !empty( $settings['view_all_btn_link']['url'] ) ) : ?><a class="glozin-categories-carousel__thumbnail gz-image-rounded overflow-hidden<?php echo esc_attr( $gz_ratio ); ?> <?php echo esc_attr( $settings['image_zoom'] == 'yes' ) ? 'gz-hover-zoom gz-hover-effect' : ''; ?>" href="<?php echo esc_url( $settings['view_all_btn_link']['url'] ); ?>" aria-label="<?php echo esc_attr($link_for);?>"><?php else: ?> <div class="glozin-image-box-grid__link"><?php endif; ?>
						<?php if( ! empty( $settings['view_all_btn_image'] ) && ! empty( $settings['view_all_btn_image']['url'] ) ) : ?>
							<?php
								$settings['image'] = $settings['view_all_btn_image'];
								echo wp_kses_post( \Elementor\Group_Control_Image_Size::get_attachment_image_html( $settings ) );
							?>
						<?php endif; ?>
					<?php if( ! empty( $settings['view_all_btn_link']['url'] ) ) : ?></a><?php else: ?></div><?php endif; ?>
					<?php $this->render_button($settings, 'view_all_btn'); ?>
				</div>
				<div <?php echo $this->get_render_attribute_string( 'view_all_content' ); ?>>
					<div class="glozin-categories-carousel__summary">
						<<?php echo esc_attr( $title_tag ); ?> class="glozin-categories-carousel__title h6 mb-0"><a href="<?php echo esc_url( $settings['view_all_btn_link']['url'] ); ?>"><?php echo esc_html( $settings['view_all_btn_title'] ); ?></a></<?php echo esc_attr( $title_tag ); ?>>
						<?php echo $count_html; ?>
					</div>
					<?php
						$button_link = [ 'url' => esc_url( $settings['view_all_btn_link']['url'] ) ];
						$button_args = [
							'no_text' => true,
							'classes' => 'glozin-categories-carousel__button',
							'icon_default' => \Glozin\Addons\Helper::get_svg( 'arrow-top' ),
							'aria_label' => esc_html__( 'Link for', 'glozin-addons' ) . ' ' . $settings['view_all_btn_title'],
						];
						$this->render_button( '', 'all_btn', $button_link, $button_args ); ?>
				</div>
			</div>
		<?php
	}
}