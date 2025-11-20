<?php

namespace Glozin\Addons\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Glozin\Addons\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Banner widget
 */
class Banner extends Widget_Base {
	use \Glozin\Addons\Elementor\Base\Aspect_Ratio_Base;
	use \Glozin\Addons\Elementor\Base\Button_Base;
	use \Glozin\Addons\Elementor\Base\Video_Base;

	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-banner';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( '[Glozin] Banner', 'glozin-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-image';
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
		return [ 'glozin-coundown', 'glozin-countdown-widget' ];
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
		return [ 'glozin-banner-css' ];
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
				'label' => __( 'Banner', 'glozin-addons' ),
			]
		);

		$this->add_control(
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

        $this->add_responsive_control(
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

		$this->register_video_repeater_controls( $this, [ 'banner_type' => 'video' ] );

		$this->register_aspect_ratio_controls( [], [ 'aspect_ratio_type' => 'horizontal' ] );

		$this->add_control(
			'sub_title',
			[
				'label' => __( 'Sub Title', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'label_block' => true,
			]
		);

        $this->add_control(
			'title',
			[
				'label' => __( 'Title', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'This is the title', 'glozin-addons' ),
				'placeholder' => __( 'Enter your title', 'glozin-addons' ),
				'label_block' => true,
			]
		);

        $this->add_control(
			'title_size',
			[
				'label' => __( 'Title HTML Tag', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h2',
			]
		);

		$this->add_control(
			'description',
			[
				'label' => __( 'Description', 'glozin-addons' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => '',
			]
		);

		$this->add_control(
			'due_date',
			[
				'label'   => esc_html__( 'Countdown Date', 'glozin-addons' ),
				'type'    => Controls_Manager::DATE_TIME,
				'default' => '',
			]
		);

		$this->register_button_controls( false, __( 'Button Text', 'glozin-addons' ), __( 'Button Link', 'glozin-addons' ) );

		$this->add_control(
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
					],
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();


        $this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Banner', 'glozin-addons' ),
                'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'hover_zoom',
			[
				'label'   => esc_html__( 'Zoom when hover image', 'glozin-addons' ),
				'type'    => Controls_Manager::SWITCHER,
			]
		);

        $this->add_responsive_control(
			'horizontal_position',
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
				'selectors'            => [
					'{{WRAPPER}} .glozin-banner' => 'justify-content: {{VALUE}}',
				],
				'selectors_dictionary' => [
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				],
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
					'{{WRAPPER}} .glozin-banner' => 'align-items: {{VALUE}}',
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
				'prefix_class' => 'glozin-banner__text_align--',
				'selectors'   => [
					'{{WRAPPER}} .glozin-banner' => 'text-align: {{VALUE}}',
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
					'{{WRAPPER}} .glozin-banner .glozin-banner__summary' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
			'border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}}' => 'border-start-start-radius: {{TOP}}{{UNIT}}; border-start-end-radius: {{RIGHT}}{{UNIT}}; border-end-end-radius: {{BOTTOM}}{{UNIT}}; border-end-start-radius: {{LEFT}}{{UNIT}};',
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

		$this->start_controls_section(
			'section_digit_style',
			[
				'label' => __( 'Coundown', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'countdown_gap',
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
					'{{WRAPPER}} .glozin-countdown' => 'gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'timer_heading',
			[
				'label' => __( 'Timer', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'timer_gap',
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
					'{{WRAPPER}} .glozin-countdown .timer' => 'gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'timer_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-countdown .timer' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'timer_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-countdown .timer' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'timer_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-countdown .timer' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'timer_border',
				'selector' => '{{WRAPPER}} .glozin-countdown .timer',
			]
		);

		$this->add_control(
			'digits_heading',
			[
				'label' => __( 'Digits', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'digits_typography',
				'selector' => '{{WRAPPER}} .glozin-countdown .timer .digits',
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

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .glozin-countdown .timer .text',
			]
		);

		$this->end_controls_section();

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

	/**
	 * Render icon box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

        $this->add_render_attribute( 'banner', 'class', [ 'glozin-banner', 'position-relative', 'd-flex', 'align-items-end', 'justify-content-center', 'overflow-hidden', 'rounded-10', 'gz-ratio' ,'gz-ratio-mobile', $settings['hover_zoom'] == 'yes' ? 'gz-hover-zoom gz-hover-effect' : '', ] );
		$this->add_render_attribute( 'banner', 'style', $this->render_aspect_ratio_style( '', 1, true ) );

		$this->add_render_attribute( 'image', 'class', [ 'glozin-banner__image', 'glozin-elementor-video', 'gz-ratio', 'align-self-stretch', 'position-absolute', 'z-1', 'w-100', 'h-100'  ] );
		$this->add_render_attribute( 'summary', 'class', [ 'glozin-banner__summary', 'position-relative', 'px-5', 'px-xl-30', 'py-30', 'text-light', 'w-100', 'z-2' ] );
		$this->add_render_attribute( 'sub_title', 'class', [ 'glozin-banner__sub-title', 'mb-20', 'text-light', 'text-uppercase', 'fw-semibold', 'fs-12', 'lh-normal' ] );
		$this->add_render_attribute( 'title', 'class', [ 'glozin-banner__title', 'mt-0', 'mb-10', 'text-light', 'fw-semibold', 'heading-letter-spacing' ] );
		$this->add_render_attribute( 'description', 'class', [ 'glozin-banner__description', 'mb-25', 'text-light', 'fs-14' ] );
		$this->add_render_attribute( 'countdown', 'class', [ 'glozin-banner__countdown', 'glozin-countdown', 'mb-35', 'mb-xl-50', 'text-light', 'd-inline-flex', 'gap-5', 'gap-xl-10' ] );
		$this->add_render_attribute( 'button', 'class', [ 'glozin-banner__button', 'position-relative', 'z-3' ] );

		$this->add_link_attributes( 'link', $settings['button_link'] );
		$this->add_render_attribute( 'link', 'class', [ 'glozin-button-link', 'glozin-banner__button--all', 'position-absolute', 'top-0', 'end-0', 'bottom-0', 'start-0', 'z-2' ] );

		if ( $settings['button_link_type'] == 'slide' ) {
			$this->add_render_attribute( 'summary', 'class', [ 'pe-none' ] );
			$this->add_render_attribute( 'button', 'class', [ 'pe-auto' ] );
		}

		$second = 0;
		if ( $settings['due_date'] ) {
			$second_current  = strtotime( current_time( 'Y/m/d H:i:s' ) );
			$second_discount = strtotime( $this->get_settings( 'due_date' ) );

			if ( $second_discount > $second_current ) {
				$second = $second_discount - $second_current;
			}

			$second = apply_filters( 'glozin_countdown_shortcode_second', $second );
		}

		$this->add_render_attribute( 'countdown', 'data-expire', [$second] );
		$this->add_render_attribute( 'countdown', 'data-text', wp_json_encode( Helper::get_countdown_shorten_texts() ) );
        ?>
		<div <?php echo $this->get_render_attribute_string( 'banner' ); ?>>
			<?php
				if ( $settings['button_link_type'] == 'slide' ) {
					if( ! empty( $settings['button_link']['url'] ) ) {
						$screen_reader_text = ! empty( $settings['button_text'] ) ? $settings['button_text'] : $settings['title'];
						if( empty( $screen_reader_text ) ) {
							$screen_reader_text = __( 'View Banner', 'glozin-addons' );
						}
						echo '<a '. $this->get_render_attribute_string( 'link' ) .'>';
						echo '<span class="screen-reader-text">'. $screen_reader_text .'</span>';
						echo '</a>';
					}
				}
			?>
			<div <?php echo $this->get_render_attribute_string( 'image' ); ?>>
				<?php
					if ( $this->has_video( $settings ) && 'video' == $settings['banner_type'] ) {
						$this->render_video( $settings );
					} else {
						if( ! empty( $settings['image'] ) && ! empty( $settings['image']['url'] ) ) {
							echo \Glozin\Addons\Helper::get_responsive_image_elementor( $settings );
						}
					}
				?>
			</div>
            <div <?php echo $this->get_render_attribute_string( 'summary' ); ?>>
				<?php if( ! empty( $settings['sub_title'] ) ) : ?>
					<div <?php echo $this->get_render_attribute_string( 'sub_title' ); ?>><?php echo wp_kses_post( $settings['sub_title'] ); ?></div>
				<?php endif; ?>
				<?php if( ! empty( $settings['title'] ) ) : ?>
					<<?php echo esc_attr( $settings['title_size'] ); ?> <?php echo $this->get_render_attribute_string( 'title' ); ?>><?php echo wp_kses_post( $settings['title'] ); ?></<?php echo esc_attr( $settings['title_size'] ); ?>>
				<?php endif; ?>
				<?php if( ! empty( $settings['description'] ) ) : ?>
					<div <?php echo $this->get_render_attribute_string( 'description' ); ?>><?php echo wp_kses_post( $settings['description'] ); ?></div>
				<?php endif; ?>
				<?php if( ! empty( $second ) ) : ?>
					<div <?php echo $this->get_render_attribute_string( 'countdown' ); ?>></div>
				<?php endif; ?>
				<div>
					<?php $this->render_button(); ?>
				</div>
            </div>
        </div>
        <?php
	}
}
