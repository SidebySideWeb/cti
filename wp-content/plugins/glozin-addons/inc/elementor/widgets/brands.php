<?php

namespace Glozin\Addons\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Brands widget
 */
class Brands extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-brands';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( '[Glozin] Brands', 'glozin-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-posts-grid';
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
	 * Script
	 *
	 * @return void
	 */
	public function get_script_depends() {
		return [
			'glozin-brands-widget'
		];
	}

	public function get_style_depends(): array {
		return [ 'glozin-brands-css' ];
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
			'section_options',
			[
				'label' => __( 'Brands', 'glozin-addons' ),
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'          => esc_html__( 'Columns', 'glozin-addons' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 1,
				'max'            => 5,
				'step'           => 1,
				'default'        => 5,
				'tablet_default' => 3,
				'mobile_default' => 2,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'hide_empty',
			[
				'label'        => esc_html__( 'Hide empty brands', 'glozin-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'glozin-addons' ),
				'label_off'    => esc_html__( 'Hide', 'glozin-addons' ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->end_controls_section();

		$this->style_options();
	}

	public function style_options() {
		$this->tabs_style_options();
		$this->brands_style_options();
	}

	public function tabs_style_options() {
		$this->start_controls_section(
			'section_tabs_style',
			[
				'label' => esc_html__( 'Tabs', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'tabs_gap',
			[
				'label'     => esc_html__( 'Gap', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-brands-filters' => 'gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'tabs_margin',
			[
				'label'     => esc_html__( 'Spacing', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-brands-filters' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'button_heading',
			[
				'label' => esc_html__( 'Button', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .glozin-brands-filters__button',
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-brands-filters__button' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}}; min-width: 0;',
				],
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-brands .glozin-brands-filters__button' => '--gz-button-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; min-width: 0;',
					'.rtl {{WRAPPER}} .glozin-brands .glozin-brands-filters__button' => '--gz-button-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}}; min-width: 0;',
				],
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
					'button_color',
					[
						'label'      => esc_html__( 'Color', 'glozin-addons' ),
						'type'       => Controls_Manager::COLOR,
						'selectors'  => [
							'{{WRAPPER}} .glozin-brands-filters__button' => '--gz-button-color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'button_hover_color',
					[
						'label'      => esc_html__( 'Hover Color', 'glozin-addons' ),
						'type'       => Controls_Manager::COLOR,
						'selectors'  => [
							'{{WRAPPER}} .glozin-brands-filters__button' => '--gz-button-color-hover: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'button_background_color',
					[
						'label'      => esc_html__( 'Background Color', 'glozin-addons' ),
						'type'       => Controls_Manager::COLOR,
						'selectors'  => [
							'{{WRAPPER}} .glozin-brands-filters__button' => '--gz-button-bg-color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'button_background_color_hover',
					[
						'label'      => esc_html__( 'Background Color Hover', 'glozin-addons' ),
						'type'       => Controls_Manager::COLOR,
						'selectors'  => [
							'{{WRAPPER}} .glozin-brands-filters__button' => '--gz-button-bg-color-hover: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'button_border_color',
					[
						'label'      => esc_html__( 'Border Color', 'glozin-addons' ),
						'type'       => Controls_Manager::COLOR,
						'selectors'  => [
							'{{WRAPPER}} .glozin-brands-filters__button' => '--gz-button-border-color: {{VALUE}}',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_button_active',
				[
					'label' => __( 'Active', 'glozin-addons' ),
				]
			);

				$this->add_control(
					'button_color_active',
					[
						'label'      => esc_html__( 'Color', 'glozin-addons' ),
						'type'       => Controls_Manager::COLOR,
						'selectors'  => [
							'{{WRAPPER}} .glozin-brands-filters__button.active' => '--gz-button-color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'button_hover_color_active',
					[
						'label'      => esc_html__( 'Hover Color', 'glozin-addons' ),
						'type'       => Controls_Manager::COLOR,
						'selectors'  => [
							'{{WRAPPER}} .glozin-brands-filters__button.active' => '--gz-button-color-hover: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'button_background_color_active',
					[
						'label'      => esc_html__( 'Background Color', 'glozin-addons' ),
						'type'       => Controls_Manager::COLOR,
						'selectors'  => [
							'{{WRAPPER}} .glozin-brands-filters__button.active' => '--gz-button-bg-color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'button_background_color_hover_active',
					[
						'label'      => esc_html__( 'Background Color Hover', 'glozin-addons' ),
						'type'       => Controls_Manager::COLOR,
						'selectors'  => [
							'{{WRAPPER}} .glozin-brands-filters__button.active' => '--gz-button-bg-color-hover: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'button_border_color_active',
					[
						'label'      => esc_html__( 'Border Color', 'glozin-addons' ),
						'type'       => Controls_Manager::COLOR,
						'selectors'  => [
							'{{WRAPPER}} .glozin-brands-filters__button.active' => '--gz-button-border-color: {{VALUE}}',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_button_disable',
				[
					'label' => __( 'Disable', 'glozin-addons' ),
				]
			);

				$this->add_control(
					'button_color_disable',
					[
						'label'      => esc_html__( 'Color', 'glozin-addons' ),
						'type'       => Controls_Manager::COLOR,
						'selectors'  => [
							'{{WRAPPER}} .glozin-brands-filters__button.disable' => '--gz-button-color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'button_background_color_disable',
					[
						'label'      => esc_html__( 'Background Color', 'glozin-addons' ),
						'type'       => Controls_Manager::COLOR,
						'selectors'  => [
							'{{WRAPPER}} .glozin-brands-filters__button.disable' => '--gz-button-bg-color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'button_border_color_disable',
					[
						'label'      => esc_html__( 'Border Color', 'glozin-addons' ),
						'type'       => Controls_Manager::COLOR,
						'selectors'  => [
							'{{WRAPPER}} .glozin-brands-filters__button.disable' => '--gz-button-border-color: {{VALUE}}',
						],
					]
				);

			$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	public function brands_style_options() {
		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Brands', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-brands .glozin-brands-filters__items' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'heading_heading',
			[
				'label' => esc_html__( 'Heading', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'heading_typography',
				'selector' => '{{WRAPPER}} .glozin-brands .glozin-brands-filters__heading',
			]
		);

		$this->add_control(
			'heading_color',
			[
				'label'      => esc_html__( 'Heading Color', 'glozin-addons' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .glozin-brands .glozin-brands-filters__heading' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'brands_heading',
			[
				'label' => esc_html__( 'Brands', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'brands_gap',
			[
				'label'     => esc_html__( 'Gap', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-brands-filters__content-inner' => 'gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'brands_typography',
				'selector' => '{{WRAPPER}} .glozin-brands-filters__item',
			]
		);

		$this->add_control(
			'brands_color',
			[
				'label'      => esc_html__( 'Color', 'glozin-addons' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .glozin-brands-filters__item a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'brands_color_hover',
			[
				'label'      => esc_html__( 'Hover Color', 'glozin-addons' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .glozin-brands-filters__item a:hover' => 'color: {{VALUE}}',
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

		$classes = [
			'glozin-brands',
		];

		$atts = [
			'taxonomy'   	=> 'product_brand',
			'hide_empty' 	=> $settings['hide_empty'] == 'yes' ? true : false,
		];

		$terms   = get_terms( $atts );
		$outputs = array();
		$enable  = array();

		if ( is_wp_error( $terms ) ) {
			return;
		}

		if ( empty( $terms ) || ! is_array( $terms ) ) {
			return;
		}

		foreach ( $terms as $term ) {
			$key = mb_substr( $term->slug, 0, 1 );
			$key = is_numeric( $key) ? '123' : $key;

			$outputs[$key][] = sprintf(
				'<div class="glozin-brands-filters__item d-flex flex-column w-100">' .
					'<a href="%s">%s</a>' .
				'</div>',
				esc_url( get_term_link( $term->term_id, 'product_brand' ) ),
				esc_html( $term->name )
			);

			$enable[] = $key;
		}

		$enable = array_unique( $enable );

		$this->add_render_attribute('wrapper', 'class', $classes );

		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<div class="glozin-brands-filters d-flex flex-wrap align-items-center gap-10 mb-60">
				<button class="glozin-brands-filters__button gz-button-outline-dark fs-14 px-30 active" data-filter="all"><?php esc_html_e( 'Show All', 'glozin-addons' ); ?></button>
				<?php foreach( range('a', 'z') as $index ) : ?>
					<button class="glozin-brands-filters__button gz-button-outline-dark fs-12 <?php echo in_array( strtolower( $index ), $enable ) ? '' : 'disable'; ?>" data-filter="<?php echo esc_attr( $index ); ?>"><?php echo esc_html( $index ); ?></button>
				<?php endforeach; ?>
				<button class="glozin-brands-filters__button gz-button-outline-dark fs-12 <?php echo in_array( '123', $enable ) ? '' : 'disable'; ?>" data-filter="123"><?php esc_html_e( '0-9', 'glozin-addons' ); ?></button>
			</div>
			<div class="glozin-brands-filters__wrapper">
				<?php
					$col = ! empty( $settings['columns'] ) ? $settings['columns'] : 5;
					$col_tablet = ! empty( $settings['columns_tablet'] ) ? $settings['columns_tablet'] : 3;
					$col_mobile = ! empty( $settings['columns_mobile'] ) ? $settings['columns_mobile'] : 2;

					$column_class = $col_mobile == 5 ? 'gz-col-1-' . $col_mobile : 'gz-col-' . intval( 12 / $col_mobile );
					$column_class .= $col_tablet == 5 ? ' gz-col-md-1-' . $col_tablet : ' gz-col-md-' . intval( 12 / $col_tablet );
					$column_class .= $col == 5 ? ' gz-col-xl-1-' . $col : ' gz-col-xl-' . intval( 12 / $col );

					foreach( $outputs as $key => $items ) :
						$items = array_unique($items);

						$total_items = count($items);
						$columns = min($col, ceil($total_items / $col));
						$items_per_column = max($col, ceil($total_items / $columns));
						$columns_data = array_chunk($items, $items_per_column);
				?>
					<div class="glozin-brands-filters__items border-top py-50 px-15 active" data-filter="<?php echo esc_attr( $key ); ?>">
						<div class="glozin-brands-filters__inner d-flex flex-column flex-lg-row align-items-lg-center row-gap-20">
							<div class="glozin-brands-filters__heading fs-32 fw-semibold text-uppercase lh-1 text-dark w-100 px-lg-30"><?php echo esc_html( $key ); ?></div>
							<div class="glozin-brands-filters__content flex-1 gz-row row-gap-20">
							<?php
								foreach ($columns_data as $column) : ?>
									<div class="glozin-brands-filters__content-inner d-flex flex-column gap-5 gz-col <?php echo esc_attr( $column_class ); ?>">
										<?php echo implode('', $column); ?>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
