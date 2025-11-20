<?php
namespace Glozin\Addons\Elementor\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Glozin\Addons\Helper;

abstract class Carousel_Widget_Base extends Widget_Base {
	/**
	 * Register controls
	 *
	 * @param array $controls
	 */
	protected function register_carousel_controls( $controls = [] ) {
		$supported_controls = [
			'slides_to_show'    				=> 1,
			'slides_to_scroll'     				=> 1,
			'space_between'  					=> 30,
			'navigation'    					=> '',
			'autoplay' 							=> '',
			'autoplay_speed'      				=> 3000,
			'pause_on_hover'    				=> 'yes',
			'animation_speed'  					=> 800,
			'infinite'  						=> '',
		];

		$controls = 'all' == $controls ? $supported_controls : $controls;

		foreach ( $controls as $option => $default ) {
			switch ( $option ) {
				case 'slides_rows':
					$this->add_responsive_control(
						'slides_rows',
						[
							'label'   => esc_html__( 'Slides Rows', 'glozin-addons' ),
							'type'    => Controls_Manager::NUMBER,
							'default' => $default ? $default : 1,
							'min'     => 1,
							'max'     => 10,
							'step'    => 1,
							'frontend_available' => true,
						]
					);
				break;
				case 'slides_to_show':
					$this->add_responsive_control(
						'slides_to_show',
						[
							'label'     => __( 'Slides to Show', 'glozin-addons' ),
							'type'      => Controls_Manager::NUMBER,
							'min'       => 1,
							'max'       => 50,
							'step'      => 1,
							'default'   => $default,
							'frontend_available' => true,
						]
					);
					break;

				case 'slides_to_scroll':
					$this->add_responsive_control(
						'slides_to_scroll',
						[
							'label'     => __( 'Slides to Scroll', 'glozin-addons' ),
							'type'      => Controls_Manager::NUMBER,
							'min'       => 1,
							'max'       => 50,
							'step'      => 1,
							'default'   => $default,
							'frontend_available' => true,
						]
					);
					break;

				case 'custom_space_between':
					$this->add_control(
						'custom_space_between',
						[
							'label' => __( 'Custom Space Between', 'glozin-addons' ),
							'type'      => Controls_Manager::SWITCHER,
							'label_off' => __( 'Off', 'glozin-addons' ),
							'label_on'  => __( 'On', 'glozin-addons' ),
							'default'   => $default,
							'frontend_available' => true,
						]
					);

					$this->add_responsive_control(
						'custom_space_between_row',
						[
							'label'     => __( 'Space Between Row', 'glozin-addons' ),
							'type'      => Controls_Manager::NUMBER,
							'min'       => 0,
							'max'       => 200,
							'step'      => 5,
							'default'   => 30,
							'frontend_available' => true,
							'condition' => [
								'custom_space_between' => 'yes',
							],
						]
					);
					break;

				case 'space_between':
					$this->add_responsive_control(
						'image_spacing_custom',
						[
							'label'     => __( 'Space Between', 'glozin-addons' ),
							'type'      => Controls_Manager::NUMBER,
							'min'       => 0,
							'max'       => 200,
							'step'      => 5,
							'default'   => $default,
							'frontend_available' => true,
							'render_type' => 'none',
						]
					);
					break;

				case 'navigation':
					$this->add_control(
						'navigation',
						[
							'label' => __( 'Navigation', 'glozin-addons' ),
							'type' => Controls_Manager::HIDDEN,
							'default' => 'both',
							'frontend_available' => true,
						]
					);
					$this->add_responsive_control(
						'navigation_classes',
						[
							'label' => __( 'Navigation', 'glozin-addons' ),
							'type' => Controls_Manager::SELECT,
							'options' => [
								'arrows' => esc_html__('Arrows', 'glozin-addons'),
								'dots' => esc_html__('Dots', 'glozin-addons'),
								'both' => esc_html__('Arrows and Dots', 'glozin-addons'),
								'none' => esc_html__('None', 'glozin-addons'),
							],
							'default' => 'arrows',
							'prefix_class' => 'navigation-class-%s',
						]
					);
					break;

				case 'autoplay':
					$this->add_control(
						'autoplay',
						[
							'label' => __( 'Autoplay', 'glozin-addons' ),
							'type'      => Controls_Manager::SWITCHER,
							'label_off' => __( 'Off', 'glozin-addons' ),
							'label_on'  => __( 'On', 'glozin-addons' ),
							'default'   => '',
							'frontend_available' => true,
						]
					);
					break;

				case 'autoplay_speed':
					$this->add_control(
						'autoplay_speed',
						[
							'label'   => __( 'Autoplay Speed', 'glozin-addons' ),
							'type'    => Controls_Manager::NUMBER,
							'default' => $default,
							'min'     => 100,
							'step'    => 100,
							'frontend_available' => true,
							'condition' => [
								'autoplay' => 'yes',
							],
						]
					);
					break;

				case 'pause_on_hover':
					$this->add_control(
						'pause_on_hover',
						[
							'label'   => __( 'Pause on Hover', 'glozin-addons' ),
							'type'    => Controls_Manager::SWITCHER,
							'label_off' => __( 'Off', 'glozin-addons' ),
							'label_on'  => __( 'On', 'glozin-addons' ),
							'default'   => $default,
							'frontend_available' => true,
							'condition' => [
								'autoplay' => 'yes',
							],
						]
					);
					break;

				case 'animation_speed':
					$this->add_control(
						'speed',
						[
							'label'       => __( 'Animation Speed', 'glozin-addons' ),
							'type'        => Controls_Manager::NUMBER,
							'default'     => $default,
							'min'         => 100,
							'step'        => 50,
							'frontend_available' => true,
						]
					);
					break;

				case 'infinite':
					$this->add_control(
						'infinite',
						[
							'label'       => __( 'Infinite Loop', 'glozin-addons' ),
							'type'    => Controls_Manager::SWITCHER,
							'label_off' => __( 'Off', 'glozin-addons' ),
							'label_on'  => __( 'On', 'glozin-addons' ),
							'default'   => $default,
							'frontend_available' => true,
						]
					);
					break;
				case 'reveal_on_scroll':
					$this->add_control(
						'reveal_on_scroll',
						[
							'label'       => __( 'Reveal on Scroll', 'glozin-addons' ),
							'type'    => Controls_Manager::SWITCHER,
							'label_off' => __( 'Off', 'glozin-addons' ),
							'label_on'  => __( 'On', 'glozin-addons' ),
							'default'   => $default,
							'frontend_available' => true,
							'prefix_class' => 'swiper-reveal-on-scroll-',
						]
					);
					break;
				case 'slidesperview_auto':
					$this->add_responsive_control(
						'slidesperview_auto',
						[
							'label' => __( 'Slides Per View Auto', 'glozin-addons' ),
							'type'      => Controls_Manager::SWITCHER,
							'label_off' => __( 'Off', 'glozin-addons' ),
							'label_on'  => __( 'On', 'glozin-addons' ),
							'default'   => $default,
							'responsive' => true,
							'frontend_available' => true,
						]
					);
					break;
			}

		}
	}

	/**
	 * Register controls style
	 *
	 * @param array $controls
	 */
	protected function register_carousel_style_controls() {
		$this->register_carousel_arrows_style_controls();
		$this->register_carousel_dots_style_controls();
	}
	/**
	 * Register controls style
	 *
	 * @param array $controls
	 */
	protected function register_carousel_arrows_style_controls() {
		// Arrows
		$this->add_control(
			'arrows_style_heading',
			[
				'label' => esc_html__( 'Arrows', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'arrows_show',
			[
				'label'        => __( 'Always show button', 'glozin-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_off'    => __( 'Off', 'glozin-addons' ),
				'label_on'     => __( 'On', 'glozin-addons' ),
				'default'      => '',
				'return_value' => '1',
				'selectors' => [
					'{{WRAPPER}} .swiper-button' => 'opacity: {{VALUE}}; margin-inline-start: 0 !important; margin-inline-end: 0 !important;',
				]
			]
		);

		$this->add_responsive_control(
			'arrows_horizontal_spacing',
			[
				'label'      => esc_html__( 'Horizontal Spacing', 'glozin-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => -100,
						'max' => 1170,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .elementor-swiper-button-prev' => 'left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-swiper-button-next' => 'right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrows_vertical_spacing',
			[
				'label'      => esc_html__( 'Vertical Spacing', 'glozin-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 1170,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .swiper-button' => '--gz-arrow-top: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'arrows_size',
			[
				'label'     => __( 'Icon Size', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 100,
						'min' => 0,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-button' => '--gz-arrow-font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrows_width',
			[
				'label'     => __( 'Width', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 100,
						'min' => 0,
					],
				],
				'default' => [],
				'selectors' => [
					'{{WRAPPER}} .swiper-button' => '--gz-arrow-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrows_height',
			[
				'label'     => __( 'Height', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 100,
						'min' => 0,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-button' => '--gz-arrow-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'arrows_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .swiper-button' => '--gz-arrow-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .swiper-button' => '--gz-arrow-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs(
			'arrows_tabs'
		);
		$this->start_controls_tab(
			'arrows_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'glozin-addons' ),
			]
		);
		$this->add_control(
			'arrows_bgcolor',
			[
				'label'     => esc_html__( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-button' => '--gz-arrow-bg-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrows_color',
			[
				'label'     => esc_html__( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-button' => '--gz-arrow-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrows_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-button' => '--gz-arrow-border-color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'arrows_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'arrows_hover_bgcolor',
			[
				'label'     => esc_html__( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-button' => '--gz-arrow-bg-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrows_hover_color',
			[
				'label'     => esc_html__( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-button' => '--gz-arrow-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrows_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-button' => '--gz-arrow-border-color-hover: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'arrows_disable_tab',
			[
				'label' => esc_html__( 'Disable', 'glozin-addons' ),
			]
		);
		$this->add_control(
			'arrows_disable_bgcolor',
			[
				'label'     => esc_html__( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-button.swiper-button-disabled' => '--gz-arrow-bg-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrows_disable_color',
			[
				'label'     => esc_html__( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-button.swiper-button-disabled' => '--gz-arrow-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrows_disable_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-button.swiper-button-disabled' => '--gz-arrow-border-color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();
	}
	/**
	 * Register controls style
	 *
	 * @param array $controls
	 */
	protected function register_carousel_dots_style_controls() {
		// Dots
		$this->add_control(
			'dots_style_heading',
			[
				'label' => esc_html__( 'Dots', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control (
			'dot_alignment',
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
					'{{WRAPPER}} .swiper-pagination-bullets' => 'justify-content: {{VALUE}}',
				],
				'selectors_dictionary' => [
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				],
			]
		);

		$this->add_control(
			'dots_type',
			[
				'label'   => esc_html__( 'Dots Type', 'glozin-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					''           => esc_html__( 'Border Normal', 'glozin-addons' ),
					'small'      => esc_html__( 'Border Small', 'glozin-addons' ),
				],
				'default' => '',
			]
		);

		$this->add_responsive_control(
			'dots_gap',
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
					'{{WRAPPER}} .swiper-pagination-bullets' => 'gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'dots_size',
			[
				'label'     => __( 'Dot Size', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 100,
						'min' => 0,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullets' => '--swiper-pagination-dot-width: {{SIZE}}{{UNIT}}; --swiper-pagination-dot-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'dots_border_size',
			[
				'label'     => __( 'Border Size', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 100,
						'min' => 0,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullets' => '--swiper-pagination-bullet-width: {{SIZE}}{{UNIT}}; --swiper-pagination-bullet-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'dots_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 1000,
						'min' => 0,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullets' => '--gz-swiper-pagination-spacing: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.glozin-carousel__dots-position-inside .swiper-pagination-bullets' => '--gz-swiper-pagination-spacing: 0; bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'dot_item_color',
			[
				'label'     => esc_html__( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => '--swiper-pagination-dot-color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'dot_item_active_color',
			[
				'label'     => esc_html__( 'Color Active', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet-active, {{WRAPPER}} .swiper-pagination-bullet:hover' => '--swiper-pagination-dot-color: {{VALUE}}; --swiper-pagination-border-color: {{VALUE}};',
				],
			]
		);
	}

	/**
	 * Render pagination for shortcode.
	 *
	 */
	protected function render_pagination( $classes = '' ) {
		$settings = $this->get_settings_for_display();
		$classes .= ! empty( $settings['dots_type'] ) ? ' swiper-pagination-bullets--' . $settings['dots_type'] : '';
		return '<div class="swiper-pagination '. esc_attr( $classes ) .'"></div>';
	}

	/**
	 * Render arrows for shortcode.
	 *
	 */
	protected function render_arrows( $class = "" ) {
		$html = Helper::get_svg('left-mini', 'ui' , [ 'class' => 'elementor-swiper-button-prev swiper-button-prev swiper-button ' . $class ] );
		$html .= Helper::get_svg('right-mini', 'ui' , [ 'class' => 'elementor-swiper-button-next swiper-button-next swiper-button ' . $class ] );

		return $html;
	}

	/**
	 * Render aspect ratio style
	 *
	 * @return void
	 */
    protected function render_space_between_style( $custom_space_between = null, $custom_space_between_tablet = null, $custom_space_between_mobile = null ) {
        $settings = $this->get_settings_for_display();

		$style = '';

		$space_between = ! empty( $settings['image_spacing_custom'] ) ? $settings['image_spacing_custom'] : 0;
		$space_between_tablet = ! empty( $settings['image_spacing_custom_tablet'] ) ? $settings['image_spacing_custom_tablet'] : $space_between;
		$space_between_mobile = ! empty( $settings['image_spacing_custom_mobile'] ) ? $settings['image_spacing_custom_mobile'] : $space_between_tablet;

		if( $space_between !== 30 ) {
			$style .= '--gz-swiper-items-space: '. esc_attr( $space_between ) . 'px;';
		}

		if( $space_between_tablet !== 30 ) {
			$style .= '--gz-swiper-items-space-tablet: '. esc_attr( $space_between_tablet ) . 'px;';
		}

		if( $space_between_mobile !== 15 ) {
			$style .= '--gz-swiper-items-space-mobile: '. esc_attr( $space_between_mobile ) . 'px;';
		}

		if( $custom_space_between !== null ) {
			$space_between = $custom_space_between;
			$space_between_tablet = $custom_space_between_tablet !== null ? $custom_space_between_tablet : $space_between;
			$space_between_mobile = $custom_space_between_mobile !== null ? $custom_space_between_mobile : $space_between_tablet;

			if( isset( $space_between ) ) {
				$style .= '--gz-swiper-items-space: '. esc_attr( $space_between ) . 'px;';
			}

			if( isset( $space_between_tablet ) ) {
				$style .= '--gz-swiper-items-space-tablet: '. esc_attr( $space_between_tablet ) . 'px;';
			}

			if( isset( $space_between_mobile ) ) {
				$style .= '--gz-swiper-items-space-mobile: '. esc_attr( $space_between_mobile ) . 'px;';
			}
		}

		if ( ! empty( $settings['custom_space_between'] ) && $settings['custom_space_between'] == 'yes' ) {
			if( ! empty( $settings['custom_space_between_row'] ) ) {
				$style .= '--gz-swiper-items-row-space: '.esc_attr( $settings['custom_space_between_row'] ).'px;';
			}

			if( ! empty( $settings['custom_space_between_row_tablet'] ) ) {
				$style .= '--gz-swiper-items-row-space-tablet: '.esc_attr( $settings['custom_space_between_row_tablet'] ).'px;';
			}

			if( ! empty( $settings['custom_space_between_row_mobile'] ) ) {
				$style .= '--gz-swiper-items-row-space-mobile: '.esc_attr( $settings['custom_space_between_row_mobile'] ).'px;';
			}
		}

        return $style;
    }

	protected function render_slidesperview_auto_class_style( $key = 'swiper' ) {
		$settings = $this->get_settings_for_display();

		$args_class = [];

		if( $settings['slidesperview_auto'] == 'yes' ) {
			$args_class[] = 'slides-per-view-auto--desktop';
		}

		if( $settings['slidesperview_auto_tablet'] == 'yes' ) {
			$args_class[] = 'slides-per-view-auto--tablet';
		}

		if( $settings['slidesperview_auto_mobile'] == 'yes' ) {
			$args_class[] = 'slides-per-view-auto--mobile';
		}

		if( ! empty( $args_class ) ) {
			$this->add_render_attribute( $key, 'class', $args_class );
		}
	}
}