<?php

namespace Glozin\Addons\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Promo Card widget
 */
class Promo_Card extends Widget_Base {
	use \Glozin\Addons\Elementor\Base\Video_Base;

	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-promo-card';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( '[Glozin] Promo Card', 'glozin-addons' );
	}

	/**
	 * Retrieve the widget circle.
	 *
	 * @return string Widget circle.
	 */
	public function get_icon() {
		return 'eicon-banner';
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
	 * Get style depends
	 *
	 * @return array
	 */
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

	protected function section_content() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'glozin-addons' ),
			]
		);
        $this->add_control(
			'promo_card_subtle',
			[
				'label'       => esc_html__( 'Subtle', 'glozin-addons' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

        $this->add_control(
			'promo_card_title',
			[
				'label'       => esc_html__( 'Title', 'glozin-addons' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

        $this->add_control(
			'promo_card_description',
			[
				'label'       => esc_html__( 'Description', 'glozin-addons' ),
				'type'    => Controls_Manager::TEXTAREA,
			]
		);

        $this->add_control(
			'promo_card_code_label',
			[
				'label'       => esc_html__( 'Code Label', 'glozin-addons' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		
        $this->add_control(
			'promo_card_code_value',
			[
				'label'       => esc_html__( 'Code Value', 'glozin-addons' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);
        

		$this->end_controls_section();
	}

	// Tab Style
	protected function section_style() {
		$this->start_controls_section(
			'section_content_style',
			[
				'label' => __( 'Content', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
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
				'selectors'   => [
					'{{WRAPPER}} .glozin-promo-card__inner' => 'text-align: {{VALUE}}',
					'{{WRAPPER}} .glozin-promo-card__promo-content' => 'text-align: {{VALUE}}',
					'{{WRAPPER}} .glozin-promo-card__promo-code' => 'text-align: {{VALUE}}',
				],
			]
		);


        $this->add_control(
			'heading_promo_card_content',
			[
				'label'     => esc_html__( 'Content', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

        $this->add_responsive_control(
			'promo_card_content_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-promo-card__promo-content' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

        $this->add_control(
			'promo_card_content_bg_color',
			[
				'label'      => esc_html__( 'Background Color', 'glozin-addons' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .glozin-promo-card__promo-content' => 'background-color: {{VALUE}}',
				],
			]
		);

        $this->add_responsive_control(
			'promo_card_content_padding',
			[
				'label'      => esc_html__( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-promo-card__promo-content' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'promo_card_content_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-promo-card__promo-content' => 'border-start-start-radius: {{TOP}}{{UNIT}}; border-start-end-radius: {{RIGHT}}{{UNIT}}; border-end-end-radius: {{BOTTOM}}{{UNIT}}; border-end-start-radius: {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'promo_card_content_border',
				'selector' => '{{WRAPPER}} .glozin-promo-card__promo-content',
			]
		);

        $this->add_control(
			'heading_promo_card_subtle',
			[
				'label'     => esc_html__( 'Subtle', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'promo_card_subtle_typography',
				'selector' => '{{WRAPPER}} .glozin-promo-card__promo-subtle',
			]
		);

		$this->add_control(
			'promo_card_subtle_color',
			[
				'label'      => esc_html__( 'Color', 'glozin-addons' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .glozin-promo-card__promo-subtle' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'promo_card_subtle_bg_color',
			[
				'label'      => esc_html__( 'Background Color', 'glozin-addons' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .glozin-promo-card__promo-subtle' => 'background-color: {{VALUE}}',
				],
			]
		);

        $this->add_responsive_control(
			'promo_card_subtle_padding',
			[
				'label'      => esc_html__( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-promo-card__promo-subtle' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'promo_card_subtle_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-promo-card__promo-subtle' => 'border-start-start-radius: {{TOP}}{{UNIT}}; border-start-end-radius: {{RIGHT}}{{UNIT}}; border-end-end-radius: {{BOTTOM}}{{UNIT}}; border-end-start-radius: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'promo_card_subtle_spacing',
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
					'{{WRAPPER}} .glozin-promo-card__promo-subtle' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

        $this->add_control(
			'heading_promo_card_title',
			[
				'label'     => esc_html__( 'Title', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'promo_card_title_typography',
				'selector' => '{{WRAPPER}} .glozin-promo-card__promo-title',
			]
		);

		$this->add_control(
			'promo_card_title_color',
			[
				'label'      => esc_html__( 'Color', 'glozin-addons' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .glozin-promo-card__promo-title' => 'color: {{VALUE}}',
				],
			]
		);

        $this->add_responsive_control(
			'promo_card_title_spacing',
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
					'{{WRAPPER}} .glozin-promo-card__promo-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

        $this->add_control(
			'heading_promo_card_description',
			[
				'label'     => esc_html__( 'Description', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'promo_card_description_typography',
				'selector' => '{{WRAPPER}} .glozin-promo-card__promo-description',
			]
		);

		$this->add_control(
			'promo_card_description_color',
			[
				'label'      => esc_html__( 'Color', 'glozin-addons' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .glozin-promo-card__promo-description' => 'color: {{VALUE}}',
				],
			]
		);

        $this->add_control(
			'heading_promo_card_code',
			[
				'label'     => esc_html__( 'Code', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
                'separator' => 'before'
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'promo_card_code_typography',
				'selector' => '{{WRAPPER}} .glozin-promo-card__promo-code',
			]
		);

		$this->add_control(
			'promo_card_code_color',
			[
				'label'      => esc_html__( 'Color', 'glozin-addons' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .glozin-promo-card__promo-code' => 'color: {{VALUE}}',
				],
			]
		);

        $this->add_control(
			'promo_card_code_bg_color',
			[
				'label'      => esc_html__( 'Background Color', 'glozin-addons' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .glozin-promo-card__promo-code' => 'background-color: {{VALUE}}',
				],
			]
		);

        $this->add_responsive_control(
			'promo_card_code_padding',
			[
				'label'      => esc_html__( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-promo-card__promo-code' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'promo_card_code_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-promo-card__promo-code' => 'border-start-start-radius: {{TOP}}{{UNIT}}; border-start-end-radius: {{RIGHT}}{{UNIT}}; border-end-end-radius: {{BOTTOM}}{{UNIT}}; border-end-start-radius: {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
			'heading_promo_card_code_text',
			[
				'label'     => esc_html__( 'Text', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'promo_card_code_text_typography',
				'selector' => '{{WRAPPER}} .glozin-promo-card__promo-code-text',
			]
		);

		$this->add_control(
			'promo_card_code_text_color',
			[
				'label'      => esc_html__( 'Color', 'glozin-addons' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .glozin-promo-card__promo-code-text' => 'color: {{VALUE}}',
				],
			]
		);

        $this->add_responsive_control(
			'promo_card_code_text_spacing',
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
					'{{WRAPPER}} .glozin-promo-card__promo-code-text' => 'margin-bottom: {{SIZE}}{{UNIT}}',
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

        $this->add_render_attribute( 'element', 'class', [ 'glozin-promo-card'] );

        $this->add_render_attribute( 'promo_card', 'class', [ 'glozin-promo-card__promo-item' ] );
        $this->add_render_attribute( 'promo_card_content', 'class', [ 'glozin-promo-card__promo-content', 'text-center', 'rounded-15', 'pt-40', 'pb-30', 'px-15', 'border' ] );
        $this->add_render_attribute( 'promo_card_subtle', 'class', [ 'glozin-promo-card__promo-subtle', 'd-inline-block', 'bg-light', 'text-dark', 'rounded-30', 'py-8', 'px-22', 'lh-1', 'fs-12', 'fw-semibold', 'heading-letter-spacing', 'mb-20' ] );
        $this->add_render_attribute( 'promo_card_title', 'class', [ 'glozin-promo-card__promo-title', 'text-light', 'mt-0', 'lh-1' ] );
        $this->add_render_attribute( 'promo_card_description', 'class', [ 'glozin-promo-card__promo-description', 'text-light', 'lh-1' ] );
        $this->add_render_attribute( 'promo_card_code', 'class', [ 'glozin-promo-card__promo-code', 'bg-light', 'text-dark', 'text-center', 'rounded-15', 'px-15', 'py-30', 'lh-1', 'text-uppercase', 'fs-18', 'fw-semibold' ] );
        ?>
        <div <?php echo $this->get_render_attribute_string( 'element' );?>>
			<div <?php echo $this->get_render_attribute_string( 'promo_card' );?>>
				<div <?php echo $this->get_render_attribute_string( 'promo_card_content' );?>>
					<?php if( ! empty( $settings['promo_card_subtle'] ) ) : ?>
						<div <?php echo $this->get_render_attribute_string( 'promo_card_subtle' ); ?>>
							<?php echo esc_html( $settings['promo_card_subtle'] ); ?>
						</div>
					<?php endif; ?>
					<?php if( ! empty( $settings['promo_card_title'] ) ) : ?>
						<h4 <?php echo $this->get_render_attribute_string( 'promo_card_title' ); ?>>
							<?php echo esc_html( $settings['promo_card_title'] ); ?>
						</h4>
					<?php endif; ?>
					<?php if( ! empty( $settings['promo_card_description'] ) ) : ?>
						<div <?php echo $this->get_render_attribute_string( 'promo_card_description' ); ?>>
							<?php echo wp_kses_post( $settings['promo_card_description'] ); ?>
						</div>
					<?php endif; ?>
				</div>
				<?php if( ! empty( $settings['promo_card_code_value'] ) && ! empty( $settings['promo_card_code_value'] ) ) : ?>
					<div <?php echo $this->get_render_attribute_string( 'promo_card_code' ); ?>>
						<?php if( ! empty( $settings['promo_card_code_label'] ) ) : ?>
							<div class="glozin-promo-card__promo-code-text fs-12 mb-5 fw-normal">
								<?php echo esc_html( $settings['promo_card_code_label'] ); ?>
							</div>
						<?php endif; ?>
						<?php echo esc_html( $settings['promo_card_code_value'] ); ?>
					</div>
				<?php endif; ?>
			</div>
        </div>
        <?php
	}
}