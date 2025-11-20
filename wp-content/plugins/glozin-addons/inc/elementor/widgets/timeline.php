<?php

namespace Glozin\Addons\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Timeline widget
 */
class Timeline extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-timeline';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( '[Glozin] Timeline', 'glozin-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-time-line';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'glozin-addons' ];
	}

	public function get_style_depends() {
		return [ 'glozin-timeline-css' ];
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
			'section_contents',
			[
				'label' => __( 'Contents', 'glozin-addons' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'year',
			[
				'label' => __( 'Year', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter year', 'glozin-addons' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'title',
			[
				'label' => __( 'Title', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter title', 'glozin-addons' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'description',
			[
				'label' => esc_html__( 'Description', 'glozin-addons' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__( 'Enter your description', 'glozin-addons' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'timeline',
			[
				'label'      => esc_html__( 'Timeline', 'glozin-addons' ),
				'type'       => Controls_Manager::REPEATER,
				'fields'     => $repeater->get_controls(),
				'title_field' => '{{{ year }}}',
				'default' => [
					[
						'year'        => __( '0000', 'glozin-addons' ),
						'title'       => __( 'Title', 'glozin-addons' ),
						'description' => __( 'Description', 'glozin-addons' ),
					],
					[
						'year'        => __( '1000', 'glozin-addons' ),
						'title'       => __( 'Title', 'glozin-addons' ),
						'description' => __( 'Description', 'glozin-addons' ),
					],
				],
			]
		);

		$this->end_controls_section();
	}

	protected function style_sections() {
		$this->start_controls_section(
			'section_style',
			[
				'label'     => __( 'Style', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'year_style_heading',
			[
				'label' => __( 'Year', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'year_typography',
				'selector' => '{{WRAPPER}} .glozin-timeline__year',
			]
		);

		$this->add_control(
			'year_color',
			[
				'label' => __( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-timeline__year' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'year_background_color',
			[
				'label' => __( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-timeline__year' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'year_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-timeline__year' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'year_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-timeline__year' => 'border-start-start-radius: {{TOP}}{{UNIT}}; border-start-end-radius: {{RIGHT}}{{UNIT}}; border-end-end-radius: {{BOTTOM}}{{UNIT}}; border-end-start-radius: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'title_style_heading',
			[
				'label' => __( 'Title', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .glozin-timeline__title',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-timeline__title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'description_style_heading',
			[
				'label' => __( 'Description', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .glozin-timeline__description',
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => __( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-timeline__description' => 'color: {{VALUE}}',
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

		$this->add_render_attribute( 'wrapper', 'class', [ 'glozin-timeline', 'position-relative' ] );
		$this->add_render_attribute( 'line', 'class', [ 'glozin-timeline__line', 'position-absolute', 'top-0', 'start-50', 'translate-middle-x', 'z-1', 'h-100' ] );
		$this->add_render_attribute( 'inner', 'class', [ 'glozin-timeline__inner', 'position-relative', 'd-flex', 'justify-content-between' ] );
		$this->add_render_attribute( 'year', 'class', [ 'glozin-timeline__year', 'position-absolute', 'top-0', 'start-50', 'translate-middle-x', 'z-3', 'rounded-30', 'lh-1', 'fs-12', 'fw-semibold', 'text-dark' ] );
		$this->add_render_attribute( 'content', 'class', [ 'glozin-timeline__content', 'px-15' ] );
		$this->add_render_attribute( 'content-inner', 'class', [ 'glozin-timeline__content-inner', 'rounded-10', 'border' ] );
		$this->add_render_attribute( 'title', 'class', [ 'glozin-timeline__title', 'fs-16', 'fw-semibold', 'lh-1', 'text-dark' ] );
		$this->add_render_attribute( 'description', 'class', [ 'glozin-timeline__description', 'fs-14' ] );

		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<span <?php echo $this->get_render_attribute_string( 'line' ); ?>></span>
			<?php
				$i = 0;
				foreach( $settings['timeline'] as $index => $timeline ):
				$item = $this->get_repeater_setting_key( 'item', 'timeline', $index );
				$this->add_render_attribute( $item, 'class', [ 'glozin-timeline__item', 'position-relative', 'pb-40' ] );

				if ( $i === 0 ) {
					$this->add_render_attribute( $item, 'class', [ 'first-item' ] );
				}
			?>
				<div <?php echo $this->get_render_attribute_string( $item ); ?>>
					<div <?php echo $this->get_render_attribute_string( 'inner' ); ?>>
						<span <?php echo $this->get_render_attribute_string( 'year' ); ?>><?php echo wp_kses_post( $timeline['year'] ); ?></span>
						<div <?php echo $this->get_render_attribute_string( 'content' ); ?>>
							<div <?php echo $this->get_render_attribute_string( 'content-inner' ); ?>>
								<div <?php echo $this->get_render_attribute_string( 'title' ); ?>><?php echo wp_kses_post( $timeline['title'] ); ?></div>
								<div <?php echo $this->get_render_attribute_string( 'description' ); ?>><?php echo wp_kses_post( $timeline['description'] ); ?></div>
							</div>
						</div>
					</div>
				</div>
			<?php
				$i++;
				endforeach;
			?>
		</div>
		<?php
	}
}
