<?php

namespace Glozin\Addons\Elementor\Widgets;

use Glozin\Addons\Elementor\Base\Carousel_Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Shoppable Video Carousel widget
 */
class Shoppable_Video_Carousel extends Carousel_Widget_Base {
    use \Glozin\Addons\Elementor\Base\Aspect_Ratio_Base;
    use \Glozin\Addons\Elementor\Base\Video_Base;
	use \Glozin\Addons\Elementor\Base\Button_Base;

	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-shoppable-video-carousel';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( '[Glozin] Shoppable Video Carousel', 'glozin-addons' );
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
	 * Scripts
	 *
	 * @return void
	 */
	public function get_script_depends() {
		return [
			'wc-add-to-cart-variation',
			'glozin-countdown-widget',
			'glozin-elementor-widgets',
			'glozin-shoppable-video-widget'
		];
	}

	/**
	 * Styles
	 *
	 * @return void
	 */
	public function get_style_depends() {
		return [
			'glozin-elementor-css',
			'glozin-countdown-css'
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
			'section_contents',
			[
				'label' => __( 'Contents', 'glozin-addons' ),
			]
		);

        $repeater = new \Elementor\Repeater();

		$this->register_video_repeater_controls( $repeater, [] );

        $repeater->add_control(
            'product_id',
            [
                'label'       => __( 'Product', 'glozin-addons' ),
                'type'        => 'glozin-autocomplete',
                'multiple'    => false,
                'source'      => 'product',
                'sortable'    => true,
                'label_block' => true,
            ]
        );

        $this->add_control(
			'video',
			[
				'label'  => __( 'Video', 'glozin-addons' ),
				'type'   => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
			]
		);

        $this->register_aspect_ratio_controls( [], [ 'aspect_ratio_type' => 'vertical' ] );

		$this->add_control(
			'modal_settings_heading',
			[
				'label' => esc_html__( 'Modal Settings', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'modal_mute',
			[
				'label' => esc_html__( 'Mute video Popup', 'glozin-addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__( 'Yes', 'glozin-addons' ),
				'label_off' => esc_html__( 'No', 'glozin-addons' ),
				'return_value' => 'yes',
				'default'   => 'yes',
				'frontend_available' => true,
			]
		);

		$this->register_button_controls(true, esc_html__( 'Button Text on Mobile', 'glozin-addons' ), '', esc_html__( 'Shop Now', 'glozin-addons' ));

		$this->end_controls_section();

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
			'slidesperview_auto' => ''
		];

		$this->register_carousel_controls($controls);

		$this->end_controls_section();
	}

	protected function style_sections() {
		$this->start_controls_section(
			'section_style',
			[
				'label'     => __( 'Content', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_control(
			'video_video_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-shoppable-video-carousel__video' => '--gz-image-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-shoppable-video-carousel__video' => '--gz-image-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'product_gradient_image',
			[
				'label' => __( 'Gradient', 'glozin-addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'glozin-addons' ),
				'label_on'  => __( 'Show', 'glozin-addons' ),
				'default'   => '',
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
					'product_gradient_image' => 'yes',
				],
			]
		);

		$this->start_popover();

		$this->add_control(
			'gradient_image_heading',
			[
				'type'  => Controls_Manager::HEADING,
				'label' => esc_html__( 'Background', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'gradient_image_color_primary',
			[
				'label' => __( 'Color Primary', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-shoppable-video-carousel--gradient' => '--gz-gradient-color-primary: {{VALUE}};',
				],
				'condition' => [
					'product_gradient_image' => 'yes',
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
					'{{WRAPPER}} .glozin-shoppable-video-carousel--gradient' => '--gz-gradient-color-secondary: {{VALUE}};',
				],
				'condition' => [
					'product_gradient_image' => 'yes',
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
					'{{WRAPPER}} .glozin-shoppable-video-carousel--gradient' => '--gz-gradient-angle: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'product_gradient_image' => 'yes',
				],
			]
		);

		$this->end_popover();

		$this->add_control(
			'product_heading',
			[
				'label' => esc_html__( 'Product', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'product_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-shoppable-video-carousel__product' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'product_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-loop-product__title,
					{{WRAPPER}} .glozin-shoppable-video-carousel__product-price' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'product_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-shoppable-video-carousel__product' => 'border-start-start-radius: {{TOP}}{{UNIT}}; border-start-end-radius: {{RIGHT}}{{UNIT}}; border-end-end-radius: {{BOTTOM}}{{UNIT}}; border-end-start-radius: {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-shoppable-video-carousel__product' => 'border-start-start-radius: {{TOP}}{{UNIT}}; border-start-end-radius: {{LEFT}}{{UNIT}}; border-end-end-radius: {{BOTTOM}}{{UNIT}}; border-end-start-radius: {{RIGHT}}{{UNIT}};',
					'{{WRAPPER}} .glozin-shoppable-video-carousel--filter-color::after' => 'border-top-left-radius: {{TOP}}{{UNIT}}; border-top-right-radius: {{RIGHT}}{{UNIT}}; border-bottom-right-radius: {{BOTTOM}}{{UNIT}}; border-bottom-left-radius: {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-shoppable-video-carousel--filter-color::after' => 'border-top-left-radius: {{TOP}}{{UNIT}}; border-top-right-radius: {{LEFT}}{{UNIT}}; border-bottom-right-radius: {{BOTTOM}}{{UNIT}}; border-bottom-left-radius: {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'product_filter_color',
			[
				'label'     => esc_html__( 'Filter', 'glozin-addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'glozin-addons' ),
				'label_on'  => __( 'Show', 'glozin-addons' ),
				'default'	=> '',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'product_filter_color_custom',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-shoppable-video-carousel--filter-color' => '--gz-shoppable-video-filter-color: {{VALUE}};',
				],
				'condition' => [
					'product_filter_color' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'product_filter_blur',
			[
				'label' => __( 'Blur', 'glozin-addons' ),
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
					'{{WRAPPER}} .glozin-shoppable-video-carousel--filter-color' => '--gz-shoppable-video-filter-blur: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'product_filter_color' => 'yes',
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

		$this->add_control(
			'image_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-shoppable-video-carousel__product-thumbnail' => '--gz-image-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-shoppable-video-carousel__product-thumbnail' => '--gz-image-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_modal_style',
			[
				'label'     => __( 'Modal Style', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->register_button_style_controls('light');

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
	 * Render icon box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

        if( empty( $settings['video'] ) ) {
            return;
        }

        $col = $settings['slides_to_show'];
		$col_tablet = ! empty( $settings['slides_to_show_tablet'] ) ? $settings['slides_to_show_tablet'] : $col;
		$col_mobile = ! empty( $settings['slides_to_show_mobile'] ) ? $settings['slides_to_show_mobile'] : $col;

        $this->add_render_attribute( 'container', 'class', [ 'glozin-shoppable-video-carousel', 'glozin-carousel--elementor', 'swiper' ] );
        $this->add_render_attribute( 'container', 'data-desktop', $col );
		$this->add_render_attribute( 'container', 'data-tablet', $col_tablet );
		$this->add_render_attribute( 'container', 'data-mobile', $col_mobile );
		$this->add_render_attribute( 'container', 'style', $this->render_space_between_style() );
        $this->add_render_attribute( 'container', 'style', $this->render_aspect_ratio_style() );
		$this->render_slidesperview_auto_class_style( 'container' );

        $this->add_render_attribute( 'wrapper', 'class', [ 'glozin-shoppable-video-carousel__wrapper', 'swiper-wrapper' ] );
        $this->add_render_attribute( 'item', 'class', [ 'glozin-shoppable-video-carousel__item', 'swiper-slide', 'position-relative' ] );
		$this->add_render_attribute( 'item', 'data-toggle', 'modal' );
        $this->add_render_attribute( 'item', 'data-target', 'shoppable-video-modal' );

        $this->add_render_attribute( 'video', 'class', [ 'glozin-shoppable-video-carousel__video', 'glozin-elementor-video', 'gz-ratio', 'gz-image-rounded', 'overflow-hidden', $settings['product_gradient_image'] ? 'glozin-shoppable-video-carousel--gradient' : '' ] );
        $this->add_render_attribute( 'product', 'class', [ 'glozin-shoppable-video-carousel__product', 'position-absolute', 'start-15', 'end-15', 'bottom-15', 'z-3', 'rounded-5', 'd-flex', 'gap-10', 'align-items-center', $settings['product_filter_color'] ? 'glozin-shoppable-video-carousel--filter-color' : '' ] );
		$this->add_render_attribute( 'product', 'data-toggle', 'modal' );
        $this->add_render_attribute( 'product', 'data-target', 'shoppable-video-mobile-modal' );

		$this->add_render_attribute( 'product_thumbnail', 'class', [ 'glozin-shoppable-video-carousel__product-thumbnail' ] );
		$this->add_render_attribute( 'product_summary', 'class', [ 'glozin-shoppable-video-carousel__product-summary' ] );
		$this->add_render_attribute( 'product_price', 'class', [ 'glozin-shoppable-video-carousel__product-price', 'price' ] );
        ?>
        <div <?php echo $this->get_render_attribute_string( 'container' );?>>
            <div <?php echo $this->get_render_attribute_string( 'wrapper' );?>>
                <?php foreach( $settings['video'] as $item ) : ?>
                    <div <?php echo $this->get_render_attribute_string( 'item' );?> data-product_id="<?php echo ! empty( $item['product_id'] ) ? esc_attr( $item['product_id'] ) : ''; ?>">
						<?php if ( $this->has_video( $item ) ) : ?>
							<div <?php echo $this->get_render_attribute_string( 'video' );?>>
								<?php $this->render_video( $item ); ?>
							</div>
						<?php endif; ?>
                        <?php
							$product_id = $item['product_id'];
							$product = wc_get_product( $product_id );
							if ( ! empty( $product ) ):
						?>
							<div <?php echo $this->get_render_attribute_string( 'product' );?> data-product_id="<?php echo ! empty( $item['product_id'] ) ? esc_attr( $item['product_id'] ) : ''; ?>">
								<div <?php echo $this->get_render_attribute_string( 'product_thumbnail' );?>>
									<?php echo $product->get_image('woocommerce_gallery_thumbnail'); ?>
								</div>
								<div <?php echo $this->get_render_attribute_string( 'product_summary' );?>>
									<h2 class="woocommerce-loop-product__title my-0 fs-15 text-light lh-normal">
										<?php echo wp_kses_post( $product->get_title() ); ?>
									</h2>
									<div <?php echo $this->get_render_attribute_string( 'product_price' );?>>
										<?php echo $product->get_price_html(); ?>
									</div>
								</div>
								<?php $this->render_button( '', '', '#', [ 'classes' => 'hidden'] ); ?>
							</div>
						<?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php echo $this->render_pagination(); ?>
            <?php echo '<div class="swiper-arrows">' . $this->render_arrows() . '</div>'; ?>
        </div>
        <?php
        $this->render_shoppable_video_modal();
	}

    public function render_shoppable_video_modal() {
        ?>
        <div class="shoppable-video-modal shoppable-video--modal modal" style="<?php echo $this->render_aspect_ratio_style(); ?>">
            <div class="modal__backdrop"></div>
            <div class="modal__container">
                <div class="modal__wrapper-shopable position-relative bg-light">
                    <a href="#" class="modal__button-close position-absolute z-1 gz-button gz-button-icon">
                        <?php echo \Glozin\Addons\Helper::get_svg( 'close' ); ?>
                    </a>
                    <div class="modal__shoppable-video single-product woocommerce">
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}