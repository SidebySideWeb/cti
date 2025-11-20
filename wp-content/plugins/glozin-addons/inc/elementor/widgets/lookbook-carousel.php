<?php

namespace Glozin\Addons\Elementor\Widgets;

use Glozin\Addons\Elementor\Base\Products_Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Images Hotspot Carousel widget
 */
class Lookbook_Carousel extends Products_Widget_Base {
	use \Glozin\Addons\Elementor\Base\Aspect_Ratio_Base;

	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-lookbook-carousel';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( '[Glozin] Lookbook Carousel', 'glozin-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-nested-carousel';
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

	public function get_style_depends() {
		return [
			'glozin-lookbook-carousel-css'
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
		$this->section_content_slides();
		$this->section_slider_options();
	}

	protected function section_content_slides() {
		$control = apply_filters( 'glozin_lookbook_carousel_section_number', 4 );
		for ( $i = 1; $i <= $control; $i ++ ) {
			$this->start_controls_section(
				'section_contents_' . $i,
				[
					'label' => __( 'Carousel Item', 'glozin-addons' ) . ' ' . $i,
				]
			);

			$default_url = '';
			if( $i == 1 || $i == 2 ) {
				$default_url = \Elementor\Utils::get_placeholder_image_src();
			}

			$this->add_responsive_control(
				'image_'. $i,
				[
					'label'     => __( 'Image', 'glozin-addons' ),
					'type'      => Controls_Manager::MEDIA,
					'default' => [
						'url' => $default_url,
					],
				]
			);

			$repeater = new \Elementor\Repeater();

			$repeater->add_control(
				'product_items_ids',
				[
					'label'       => esc_html__( 'Product', 'glozin-addons' ),
					'placeholder' => esc_html__( 'Click here and start typing...', 'glozin-addons' ),
					'type'        => 'glozin-autocomplete',
					'default'     => '',
					'label_block' => true,
					'multiple'    => false,
					'source'      => 'product',
					'sortable'    => true,
				]
			);

			$repeater->add_control(
				'point_popover_toggle',
				[
					'label' => esc_html__( 'Point', 'glozin-addons' ),
					'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
					'label_off' => esc_html__( 'Default', 'glozin-addons' ),
					'label_on' => esc_html__( 'Custom', 'glozin-addons' ),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);

			$repeater->start_popover();

			$repeater->add_responsive_control(
				'product_items_position_x',
				[
					'label'      => esc_html__( 'Point Position X', 'glozin-addons' ),
					'type'       => Controls_Manager::SLIDER,
					'range'      => [
						'px' => [
							'min' => 0,
							'max' => 1000,
						],
						'%'  => [
							'min' => 0,
							'max' => 100,
						],
					],
					'default'    => [
						'unit' => '%',
						'size' => 30 + $i * 10,
					],
					'size_units' => [ '%', 'px' ],
					'selectors'  => [
						'{{WRAPPER}} .glozin-lookbook-carousel .glozin-lookbook-carousel__item-'. $i . ' {{CURRENT_ITEM}}' => 'left: {{SIZE}}{{UNIT}};',
						'.rtl {{WRAPPER}} .glozin-lookbook-carousel .glozin-lookbook-carousel__item-'. $i . ' {{CURRENT_ITEM}}' => 'right: {{SIZE}}{{UNIT}}; left: auto;',
					],
				]
			);

			$repeater->add_responsive_control(
				'product_items_position_y',
				[
					'label'      => esc_html__( 'Point Position Y', 'glozin-addons' ),
					'type'       => Controls_Manager::SLIDER,
					'range'      => [
						'px' => [
							'min' => 0,
							'max' => 1000,
						],
						'%'  => [
							'min' => 0,
							'max' => 100,
						],
					],
					'default'    => [
						'unit' => '%',
						'size' => 30 + $i * 10,
					],
					'size_units' => [ '%', 'px' ],
					'selectors'  => [
						'{{WRAPPER}} .glozin-lookbook-carousel .glozin-lookbook-carousel__item-'. $i . ' {{CURRENT_ITEM}}' => 'top: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$repeater->end_popover();

			$repeater->add_control(
				'content_popover_toggle',
				[
					'label' => esc_html__( 'Content', 'glozin-addons' ),
					'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
					'label_off' => esc_html__( 'Default', 'glozin-addons' ),
					'label_on' => esc_html__( 'Custom', 'glozin-addons' ),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);

			$repeater->start_popover();

			$repeater->add_responsive_control(
				'product_content_vertical_position',
				[
					'label'                => esc_html__( 'Vertical Position', 'glozin-addons' ),
					'type'                 => Controls_Manager::CHOOSE,
					'label_block'          => false,
					'options'              => [
						'top'   => [
							'title' => esc_html__( 'Left', 'glozin-addons' ),
							'icon'  => 'eicon-v-align-top',
						],
						'bottom'  => [
							'title' => esc_html__( 'Right', 'glozin-addons' ),
							'icon'  => 'eicon-v-align-bottom',
						],
					],
					'default' => 'bottom'
				]
			);

			$repeater->add_responsive_control(
				'product_content_items_position',
				[
					'label'                => esc_html__( 'Content Position', 'glozin-addons' ),
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
						'custom'  => [
							'title' => esc_html__( 'Custom', 'glozin-addons' ),
							'icon'  => 'eicon-pencil',
						],
					],
					'selectors'            => [
						'{{WRAPPER}} .glozin-lookbook-carousel__item-'. $i . ' {{CURRENT_ITEM}} .glozin-lookbook-carousel__product-inner' => '{{VALUE}}',
					],
					'selectors_dictionary' => [
						'left'   => 'left: 0; right: auto; transform: translateX(0);',
						'center' => 'left: 50%; transform: translateX(-50%); right: auto;',
						'right'  => 'right: 0; left: auto; transform: translateX(0);',
					],
				]
			);

			$repeater->add_responsive_control(
				'product_content_items_position_custom',
				[
					'label'      => esc_html__( 'Content Position Custom', 'glozin-addons' ),
					'type'       => Controls_Manager::SLIDER,
					'range'      => [
						'px' => [
							'min' => - 1000,
							'max' => 1000,
						],
					],
					'default'    => [],
					'size_units' => [ 'px' ],
					'selectors'  => [
						'{{WRAPPER}} .glozin-lookbook-carousel__item-'. $i . ' {{CURRENT_ITEM}} .glozin-lookbook-carousel__product-inner' => 'left: {{SIZE}}{{UNIT}}; transform: translateX(0);',
						'.rtl {{WRAPPER}} .glozin-lookbook-carousel__item-'. $i . ' {{CURRENT_ITEM}} .glozin-lookbook-carousel__product-inner' => 'right: {{SIZE}}{{UNIT}}; left: auto; transform: translateX(0);',
					],
					'condition' => [
						'product_content_items_position' => 'custom',
					],
				]
			);

			$repeater->add_responsive_control(
				'product_arrow_items_position',
				[
					'label'                => esc_html__( 'Arrow Position', 'glozin-addons' ),
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
						'custom'  => [
							'title' => esc_html__( 'Custom', 'glozin-addons' ),
							'icon'  => 'eicon-pencil',
						],
					],
					'selectors'            => [
						'{{WRAPPER}} .glozin-lookbook-carousel__item-'. $i . ' {{CURRENT_ITEM}} .glozin-lookbook-carousel__product-inner:after' => '{{VALUE}}',
					],
					'selectors_dictionary' => [
						'left'   => 'left: 5px; right: auto; transform: translateX(0) translateY(-100%);',
						'center' => 'left: 50%; transform: translateX(-50%) translateY(-100%); right: auto;',
						'right'  => 'right: 20px; left: auto; transform: translateX(0) translateY(-100%);',
					],
					'condition' => [
						'product_content_vertical_position' => 'bottom',
					],
				]
			);

			$repeater->add_responsive_control(
				'product_arrow_items_position_custom',
				[
					'label'      => esc_html__( 'Arrow Position Custom', 'glozin-addons' ),
					'type'       => Controls_Manager::SLIDER,
					'range'      => [
						'px' => [
							'min' => - 1000,
							'max' => 1000,
						],
					],
					'default'    => [],
					'size_units' => [ 'px' ],
					'selectors'  => [
						'{{WRAPPER}} .glozin-lookbook-carousel__item-'. $i . ' {{CURRENT_ITEM}} .glozin-lookbook-carousel__product-inner:after' => 'left: {{SIZE}}{{UNIT}}; transform: translateX(0) translateY(-100%);',
						'.rtl {{WRAPPER}} .glozin-lookbook-carousel__item-'. $i . ' {{CURRENT_ITEM}} .glozin-lookbook-carousel__product-inner:after' => 'right: {{SIZE}}{{UNIT}}; left: auto; transform: translateX(0) translateY(-100%);',
					],
					'condition' => [
						'product_arrow_items_position' => 'custom',
						'product_content_vertical_position' => 'bottom',
					],
				]
			);

			$repeater->add_responsive_control(
				'product_arrow_items_position_vertical',
				[
					'label'                => esc_html__( 'Arrow Position', 'glozin-addons' ),
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
						'custom'  => [
							'title' => esc_html__( 'Custom', 'glozin-addons' ),
							'icon'  => 'eicon-pencil',
						],
					],
					'selectors'            => [
						'{{WRAPPER}} .glozin-lookbook-carousel__item-'. $i . ' {{CURRENT_ITEM}}.glozin-lookbook-carousel__position-top .glozin-lookbook-carousel__product-inner:after' => '{{VALUE}}',
					],
					'selectors_dictionary' => [
						'left'   => 'left: 5px; right: auto; transform: translateX(0) translateY(100%) rotate(180deg);',
						'center' => 'left: 50%; transform: translate(-50%) translateY(100%) rotate(180deg); right: auto;',
						'right'  => 'right: 20px; left: auto; transform: translateX(0) translateY(100%) rotate(180deg);',
					],
					'condition' => [
						'product_content_vertical_position' => 'top',
					],
				]
			);

			$repeater->add_responsive_control(
				'product_arrow_items_position_custom_vertical',
				[
					'label'      => esc_html__( 'Arrow Position Custom', 'glozin-addons' ),
					'type'       => Controls_Manager::SLIDER,
					'range'      => [
						'px' => [
							'min' => - 1000,
							'max' => 1000,
						],
					],
					'default'    => [],
					'size_units' => [ 'px' ],
					'selectors'  => [
						'{{WRAPPER}} .glozin-lookbook-carousel__item-'. $i . ' {{CURRENT_ITEM}}.glozin-lookbook-carousel__position-top .glozin-lookbook-carousel__product-inner:after' => 'left: {{SIZE}}{{UNIT}}; transform: translateX(0) translateY(100%) rotate(180deg);',
						'.rtl {{WRAPPER}} .glozin-lookbook-carousel__item-'. $i . ' {{CURRENT_ITEM}}.glozin-lookbook-carousel__position-top .glozin-lookbook-carousel__product-inner:after' => 'right: {{SIZE}}{{UNIT}}; left: auto; transform: translateX(0) translateY(100%) rotate(180deg);',
					],
					'condition' => [
						'product_arrow_items_position' => 'custom',
						'product_content_vertical_position' => 'top',
					],
				]
			);

			$repeater->end_popover();

			$this->add_control(
				'items_'. $i,
				[
					'label' => esc_html__( 'Hotspot items', 'glozin-addons' ),
					'type'       => Controls_Manager::REPEATER,
					'show_label' => true,
					'fields'     => $repeater->get_controls(),
					'default'    => [],
				]
			);

			$this->end_controls_section();
		}
	}

	protected function section_slider_options() {
		$this->start_controls_section(
			'section_slider_options',
			[
				'label' => esc_html__( 'Carousel Options', 'glozin-addons' ),
				'type'  => Controls_Manager::SECTION,
			]
		);

		$controls = [
			'slides_to_show'   => 1,
			'slides_to_scroll' => 1,
			'space_between'    => 0,
			'navigation'       => 'arrows',
			'autoplay'         => '',
			'autoplay_speed'   => 3000,
			'pause_on_hover'   => 'yes',
			'animation_speed'  => 800,
			'infinite'         => '',
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
		$this->section_style_content();
		$this->section_style_carousel();
	}

	protected function section_style_content() {
		$this->start_controls_section(
			'section_style',
			[
				'label'     => __( 'Contents', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'image_heading',
			[
				'label' => esc_html__( 'Content', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'content_image_heading',
			[
				'label'     => esc_html__( 'Image', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->register_aspect_ratio_controls();

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

		$this->add_control(
			'content_dots_heading',
			[
				'label'     => esc_html__( 'Dots', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs(
			'style_tabs_dots'
		);

		$this->start_controls_tab(
			'style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'item_dots_bgcolor',
			[
				'label'     => esc_html__( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-lookbook-carousel__button' => '--gz-button-bg-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'item_dots_color',
			[
				'label'     => esc_html__( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-lookbook-carousel__button' => '--gz-button-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'item_dots_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-lookbook-carousel__button' => '--gz-button-border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'item_dots_bgcolor_hover',
			[
				'label'     => esc_html__( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-lookbook-carousel__button' => '--gz-button-bg-color-hover: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'item_dots_color_hover',
			[
				'label'     => esc_html__( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-lookbook-carousel__button' => '--gz-button-color-hover: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'item_dots_border_color_hover',
			[
				'label'     => esc_html__( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-lookbook-carousel__button' => '--gz-button-border-color-hover: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_active_tab',
			[
				'label' => esc_html__( 'Active', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'item_dots_bgcolor_active',
			[
				'label'     => esc_html__( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-lookbook-carousel__product.active .glozin-lookbook-carousel__button' => '--gz-button-bg-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'item_dots_color_active',
			[
				'label'     => esc_html__( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-lookbook-carousel__product.active .glozin-lookbook-carousel__button' => '--gz-button-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'item_dots_border_color_active',
			[
				'label'     => esc_html__( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-lookbook-carousel__product.active .glozin-lookbook-carousel__button' => '--gz-button-border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_product_style',
			[
				'label'     => __( 'Product', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'item_image_heading',
			[
				'label'     => esc_html__( 'Image', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'item_image_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-lookbook-carousel__product-image' => '--gz-image-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-lookbook-carousel__product-image' => '--gz-image-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_image_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-lookbook-carousel__product-inner' => 'gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'item_title_heading',
			[
				'label'     => esc_html__( 'Title', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'item_title_typography',
				'selector' => '{{WRAPPER}} .glozin-lookbook-carousel__product-title',
			]
		);

		$this->add_control(
			'item_title_color',
			[
				'label'     => esc_html__( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-lookbook-carousel__product-title a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'item_title_hover_color',
			[
				'label'     => esc_html__( 'Hover Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-lookbook-carousel__product-title a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'item_price_heading',
			[
				'label'     => esc_html__( 'Price', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'item_price_typography',
				'selector' => '{{WRAPPER}} .glozin-lookbook-carousel__product-price',
			]
		);

		$this->add_control(
			'item_price_color',
			[
				'label'     => esc_html__( 'Regular Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-lookbook-carousel__product-price' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'item_price_color_ins',
			[
				'label'     => esc_html__( 'Sale Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-lookbook-carousel__product-price ins' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'item_button_heading',
			[
				'label'     => esc_html__( 'Button', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
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
			'button_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-button' => '--gz-button-bg-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-button' => '--gz-button-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-button' => '--gz-button-border-color: {{VALUE}};',
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
			'button_background_hover_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-button' => '--gz-button-bg-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-button' => '--gz-button-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-button' => '--gz-button-border-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_effect_hover_color',
			[
				'label'     => __( 'Background Effect Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-button' => '--gz-button-eff-bg-color-hover: {{VALUE}};',
				],
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
				'label' => esc_html__( 'Carousel Style', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->register_carousel_style_controls();

		$this->add_control(
			'arrows_dots_style_heading',
			[
				'label'     => esc_html__( 'Arrows And Dots', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'arrows_dots_background_color',
			[
				'label'     => esc_html__( 'Background color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination--dots-arrow .swiper-pagination--dots-arrow__wrapper' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'arrows_dots_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination--dots-arrow .swiper-pagination--dots-arrow__wrapper' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrows_dots_border_radius',
			[
				'label'      => __( 'Border radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination--dots-arrow .swiper-pagination--dots-arrow__wrapper' => 'border-start-start-radius: {{TOP}}{{UNIT}}; border-start-end-radius: {{RIGHT}}{{UNIT}}; border-end-end-radius: {{BOTTOM}}{{UNIT}}; border-end-start-radius: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs(
			'arrows_dots_tabs'
		);

			$this->start_controls_tab(
				'arrows_dots_tab_arrows',
				[
					'label' => esc_html__( 'Arrows', 'glozin-addons' ),
				]
			);

				$this->add_responsive_control(
					'arrows_dots_size_arrows',
					[
						'label'     => esc_html__( 'Size', 'glozin-addons' ),
						'type'      => Controls_Manager::SLIDER,
						'size_units' => [ 'px', '%', 'vh' ],
						'range'     => [
							'px' => [
								'min' => 0,
								'max' => 1000,
							],
						],
						'selectors' => [
							'{{WRAPPER}} .swiper-pagination--dots-arrow .swiper-button' => 'font-size: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'arrows_dots_spacing_arrows',
					[
						'label'     => esc_html__( 'Spacing', 'glozin-addons' ),
						'type'      => Controls_Manager::SLIDER,
						'size_units' => [ 'px', '%', 'vh' ],
						'range'     => [
							'px' => [
								'min' => 0,
								'max' => 1000,
							],
						],
						'selectors' => [
							'{{WRAPPER}} .swiper-pagination--dots-arrow .swiper-pagination' => 'margin: 0 {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
					'arrows_dots_color_arrows',
					[
						'label'     => esc_html__( 'Color', 'glozin-addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .swiper-pagination--dots-arrow .swiper-button' => 'color: {{VALUE}}; opacity: 1;',
						],
					]
				);

				$this->add_control(
					'arrows_dots_disable_color_arrows',
					[
						'label'     => esc_html__( 'Disable Color', 'glozin-addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .swiper-pagination--dots-arrow .swiper-button.swiper-button-disabled' => 'color: {{VALUE}};',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'arrows_dots_tab_dots',
				[
					'label' => esc_html__( 'Dots', 'glozin-addons' ),
				]
			);

				$this->add_responsive_control(
					'arrows_dots_size_dots',
					[
						'label'     => esc_html__( 'Size', 'glozin-addons' ),
						'type'      => Controls_Manager::SLIDER,
						'size_units' => [ 'px', '%', 'vh' ],
						'range'     => [
							'px' => [
								'min' => 0,
								'max' => 1000,
							],
						],
						'selectors' => [
							'{{WRAPPER}} .swiper-pagination--dots-arrow .swiper-pagination-bullets .swiper-pagination-bullet:before' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'arrows_dots_gap_dots',
					[
						'label'     => __( 'Gap', 'glozin-addons' ),
						'type'      => Controls_Manager::SLIDER,
						'range'     => [
							'px' => [
								'max' => 50,
								'min' => 0,
							],
						],
						'selectors' => [
							'{{WRAPPER}} .swiper-pagination--dots-arrow .swiper-pagination-bullet' => 'margin: 0 {{SIZE}}{{UNIT}}',
						],
					]
				);

				$this->add_control(
					'arrows_dots_color_dots',
					[
						'label'     => esc_html__( 'Color', 'glozin-addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .swiper-pagination--dots-arrow .swiper-pagination-bullets .swiper-pagination-bullet:before' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'arrows_dots_active_color_dots',
					[
						'label'     => esc_html__( 'Active Color', 'glozin-addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .swiper-pagination--dots-arrow .swiper-pagination-bullets .swiper-pagination-bullet.swiper-pagination-bullet-active, .swiper-pagination--dots-arrow .swiper-pagination-bullets .swiper-pagination-bullet:hover' => 'color: {{VALUE}};',
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

		$random_id = rand();

		$col = $settings['slides_to_show'];
		$col_tablet = ! empty( $settings['slides_to_show_tablet'] ) ? $settings['slides_to_show_tablet'] : $col;
		$col_mobile = ! empty( $settings['slides_to_show_mobile'] ) ? $settings['slides_to_show_mobile'] : $col;

        $this->add_render_attribute( 'wrapper', 'class', [ 'glozin-lookbook-carousel', 'glozin-carousel--elementor', 'swiper' ] );
		$this->add_render_attribute( 'wrapper', 'data-desktop', $col );
		$this->add_render_attribute( 'wrapper', 'data-tablet', $col_tablet );
		$this->add_render_attribute( 'wrapper', 'data-mobile', $col_mobile );
        $this->add_render_attribute( 'wrapper', 'style', [ $this->render_space_between_style() ] );
		$this->add_render_attribute( 'wrapper', 'style', $this->render_aspect_ratio_style() );
		
		$this->add_render_attribute( 'inner', 'class', [ 'glozin-lookbook-carousel__inner', 'd-flex', 'swiper-wrapper'] );
	
		$this->add_render_attribute( 'item', 'class', [ 'glozin-lookbook-carousel__item', 'position-relative', 'swiper-slide' ] );

        $this->add_render_attribute( 'image', 'class', [ 'glozin-lookbook-carousel__image', 'gz-ratio' ] );

        $this->add_render_attribute( 'product', 'class', [ 'glozin-lookbook-carousel__product', 'position-absolute' ] );
        $this->add_render_attribute( 'product_inner', 'class', [ 'glozin-lookbook-carousel__product-inner', 'position-absolute', 'align-items-center', 'py-10', 'px-10', 'd-none', 'd-flex-xl', 'gap-20', 'rounded-5', 'bg-light', 'start-50', 'translate-middle-x', 'z-3', 'hidden' ] );
        $this->add_render_attribute( 'product_summary', 'class', [ 'glozin-lookbook-carousel__product-summary' ] );
        $this->add_render_attribute( 'product_image', 'class', [ 'glozin-lookbook-carousel__product-image', 'gz-ratio' ] );
        $this->add_render_attribute( 'product_title', 'class', [ 'glozin-lookbook-carousel__product-title' ] );
        $this->add_render_attribute( 'product_price', 'class', [ 'glozin-lookbook-carousel__product-price' ] );
    ?>
        <div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<div <?php echo $this->get_render_attribute_string( 'inner' ); ?>>
			<?php $control = apply_filters( 'glozin_images_hotspot_carousel_section_number', 4 );
				for ( $i = 1; $i <= $control; $i++ ) :
					if( ! empty( $settings['image_'. $i]['url'] ) ) : ?>
					<div class="glozin-lookbook-carousel__item-<?php echo esc_attr( $i ); ?> glozin-lookbook-carousel__item position-relative swiper-slide">
						<div <?php echo $this->get_render_attribute_string( 'image' ); ?>>
							<?php
								$image_args = [
									'image'        => ! empty( $settings['image_'. $i] ) ? $settings['image_'. $i] : '',
									'image_tablet' => ! empty( $settings['image_'. $i . '_tablet'] ) ? $settings['image_'. $i . '_tablet'] : '',
									'image_mobile' => ! empty( $settings['image_'. $i . '_mobile'] ) ? $settings['image_'. $i . '_mobile'] : '',
								];
							?>
							<?php echo \Glozin\Addons\Helper::get_responsive_image_elementor( $image_args ); ?>
						</div>
					<?php
						foreach( $settings['items_'. $i] as $index => $item ) :
							$attr = [
								'type'           => 'recent_products',
								'orderby'        => 'date',
								'order'          => '',
								'category'       => '',
								'tag'            => '',
								'product_brands' => '',
								'ids'            => $item['product_items_ids'],
								'limit'          => 1,
							];
							$product_ids = self::products_shortcode( $attr );
							$product_ids = ! empty($product_ids) ? $product_ids['ids'] : 0;

							$button_key = $this->get_repeater_setting_key( 'button', 'categories_carousel', $index );
							$this->add_render_attribute( $button_key, 'class', [ 'glozin-lookbook-carousel__button', 'gz-button-light', 'gz-button-icon', 'position-relative', 'z-1' ] );

							if( ! empty( $product_ids ) ) : ?>
									<div class="elementor-repeater-item-<?php echo esc_attr( $item['_id'] ); ?> glozin-lookbook-carousel__product position-absolute glozin-lookbook-carousel__position-<?php echo $item['product_content_vertical_position'] ?>">
										<div <?php echo $this->get_render_attribute_string( 'product_inner' ); ?>>
											<ul class="products">
												<?php \Glozin\Addons\Helper::products_list_shortcode_template( $product_ids, [ 'show_rating' => true ] ); ?>
											</ul>
										</div>
										<div class="glozin-lookbook-carousel__button-wrapper d-flex align-items-center justify-content-center position-relative">
											<button <?php echo $this->get_render_attribute_string( $button_key ); ?> data-target="lookbook-carousel-modal-<?php echo esc_attr( $random_id ); ?>" data-device="mobile" aria-label="<?php esc_attr_e('Hotpot Button', 'glozin-addons') ?>">
												<?php echo \Glozin\Addons\Helper::get_svg('plus-mini'); ?>
											</button>
										</div>
									</div>
							<?php endif;
						endforeach; ?>
					</div>
			<?php 	endif;
				endfor; ?>
			</div>
			<?php
			echo '<div class="swiper-arrows">'. $this->render_arrows() .'</div>';
			echo $this->render_pagination(); ?>
        </div>
		<div id="lookbook-carousel-modal-<?php echo esc_attr( $random_id ); ?>" class="lookbook-carousel-modal-<?php echo esc_attr( $random_id ); ?> glozin-lookbook-carousel lookbook-carousel-modal modal d-none-xl">
			<div class="modal__backdrop"></div>
			<div class="modal__container">
				<div class="modal__wrapper">
					<a href="#" class="glozin-lookbook-carousel__close modal__button-close gz-button gz-button-icon gz-button-light position-absolute top-10 end-10 z-3">
						<?php echo \Glozin\Addons\Helper::get_svg( 'close' ); ?>
					</a>
					<div class="modal__content lookbook-carousel-modal-content em-flex em-flex-align-center"></div>
				</div>
			</div>
		</div>
    <?php
	}
}