<?php
namespace Glozin\Addons\Elementor\Widgets;

use Glozin\Addons\Elementor\Base\Carousel_Widget_Base;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;

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
class Image_Box_Carousel extends Carousel_Widget_Base {
	use \Glozin\Addons\Elementor\Base\Aspect_Ratio_Base;
	use \Glozin\Addons\Elementor\Base\Button_Base;
	use \Glozin\Addons\Elementor\Base\Video_Base;

	/**
	 * Get widget name.
	 *
	 * Retrieve Stores Location widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-image-box-carousel';
	}

	/**
	 * Get widget title
	 *
	 * Retrieve Stores Location widget title
	 *
	 * @return string Widget title
	 */
	public function get_title() {
		return __( '[Glozin] Image Box Carousel', 'glozin-addons' );
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
	 * Get script depends
	 *
	 * @return array
	 */
	public function get_script_depends() {
		return [
			'glozin-elementor-widgets'
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

		$repeater = new Repeater();

		$repeater->add_control(
			'image_type',
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

		$this->register_video_repeater_controls( $repeater, [ 'image_type' => 'video' ] );

        $repeater->add_responsive_control(
			'image',
			[
				'label'    => __( 'Image', 'glozin-addons' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => wc_placeholder_img_src(),
				],
			]
		);

		$repeater->add_control(
			'title', [
				'label' => esc_html__( 'Title', 'glozin-addons' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'Title', 'glozin-addons' ),
			]
		);

		$repeater->add_control(
			'description', [
				'label' => esc_html__( 'Description', 'glozin-addons' ),
				'type' => Controls_Manager::TEXTAREA,
			]
		);

        $repeater->add_control(
			'link',
			[
				'label'       => __( 'Link', 'glozin-addons' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'https://your-link.com', 'glozin-addons' ),
				'default'     => [
					'url' => '#',
				],
			]
		);

		$repeater->add_control(
			'button_text', [
				'label' => esc_html__( 'Button Text', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
			]
		);

		$repeater->add_control(
			'cus_content_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .glozin-image-box__content' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'items',
			[
				'label' => esc_html__( 'Items', 'glozin-addons' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'image' => [
							'url' => wc_placeholder_img_src(),
						],
						'link' => [
							'url' => '#',
						],
						'title' => esc_html__( 'Title 1', 'glozin-addons' ),
					],
					[
						'image' => [
							'url' => wc_placeholder_img_src(),
						],
						'link' => [
							'url' => '#',
						],
						'title' => esc_html__( 'Title 2', 'glozin-addons' ),
					],
					[
						'image' => [
							'url' => wc_placeholder_img_src(),
						],
						'link' => [
							'url' => '#',
						],
						'title' => esc_html__( 'Title 3', 'glozin-addons' ),
					],
					[
						'image' => [
							'url' => wc_placeholder_img_src(),
						],
						'link' => [
							'url' => '#',
						],
						'title' => esc_html__( 'Title 4', 'glozin-addons' ),
					],
					[
						'image' => [
							'url' => wc_placeholder_img_src(),
						],
						'link' => [
							'url' => '#',
						],
						'title' => esc_html__( 'Title 5', 'glozin-addons' ),
					],
					[
						'image' => [
							'url' => wc_placeholder_img_src(),
						],
						'link' => [
							'url' => '#',
						],
						'title' => esc_html__( 'Title 6', 'glozin-addons' ),
					],
				],
			]
		);

		$this->register_aspect_ratio_controls( [], [ 'aspect_ratio_type' => 'square' ] );

		$this->end_controls_section();

		// Carousel Settings
		$this->start_controls_section(
			'section_products_carousel',
			[
				'label' => __( 'Carousel Settings', 'glozin-addons' ),
			]
		);

		$controls = [
			'slides_to_show'     => 6,
			'slides_to_scroll'   => 1,
			'space_between'      => 30,
			'navigation'   	     => '',
			'autoplay' 		     => '',
			'autoplay_speed'     => 3000,
			'pause_on_hover'     => 'yes',
			'animation_speed'    => 800,
			'infinite'  	     => '',
			'reveal_on_scroll'   => '',
		];

		$this->register_carousel_controls( $controls );

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
				'default'     => '',
				'selectors'   => [
					'{{WRAPPER}} .glozin-image-box' => 'text-align: {{VALUE}}',
				],
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
			'content_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-image-box__content' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .glozin-image-box__content' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-image-box__content' => 'border-start-start-radius: {{TOP}}{{UNIT}}; border-start-end-radius: {{RIGHT}}{{UNIT}}; border-end-end-radius: {{BOTTOM}}{{UNIT}}; border-end-start-radius: {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
			'image_icon_heading',
			[
				'label' => esc_html__( 'Image', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
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
					'.rtl {{WRAPPER}}' => '--gz-image-rounded: {{RIGHT}}{{UNIT}} {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_width',
			[
				'label'   => esc_html__( 'Max Width', 'glozin-addons' ),
				'type'    => Controls_Manager::SLIDER,
				'range'   => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-image-box__image' => 'max-width: {{SIZE}}{{UNIT}}; margin-inline-start: auto; margin-inline-end: auto;',
				],
			]
		);

		$this->add_responsive_control(
			'image_spacing',
			[
				'label'   => esc_html__( 'Spacing', 'glozin-addons' ),
				'type'    => Controls_Manager::SLIDER,
				'range'   => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-image-box__image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
				'selector' => '{{WRAPPER}} .glozin-image-box__title',
			]
		);

        $this->add_control(
			'title_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-image-box__title' => 'color: {{VALUE}};',
				],
			]
		);

        $this->add_control(
			'title_color_hover',
			[
				'label'     => __( 'Hover Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-image-box__title:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label'   => esc_html__( 'Spacing', 'glozin-addons' ),
				'type'    => Controls_Manager::SLIDER,
				'range'   => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-image-box__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
				'selector' => '{{WRAPPER}} .glozin-image-box__description',
			]
		);

        $this->add_control(
			'description_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-image-box__description' => 'color: {{VALUE}};',
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

		$this->add_responsive_control(
			'button_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-button' => 'margin-top: {{TOP}}{{UNIT}}; margin-inline-end: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}}; margin-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->register_button_style_controls();

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

		$this->add_render_attribute( 'image_box', 'class', [ 'glozin-image-box-carousel', 'glozin-carousel--elementor', 'swiper' ] );
		$this->add_render_attribute( 'image_box', 'data-desktop', $col );
		$this->add_render_attribute( 'image_box', 'data-tablet', $col_tablet );
		$this->add_render_attribute( 'image_box', 'data-mobile', $col_mobile );
		$this->add_render_attribute( 'image_box', 'style', $this->render_space_between_style() );
		$this->add_render_attribute( 'image_box', 'style', $this->render_aspect_ratio_style() );

		$this->add_render_attribute( 'wrapper', 'class', [ 'glozin-image-box-carousel__wrapper', 'swiper-wrapper' ] );

		echo '<div ' . $this->get_render_attribute_string( 'image_box' ) . '>';
		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>';

		foreach ( $settings['items'] as $index => $item ) :
			$slide_key 			= $this->get_repeater_setting_key( 'slide', 'image_box', $index );
			$image_key 			= $this->get_repeater_setting_key( 'image', 'image_box', $index );
			$image_link_key 	= $this->get_repeater_setting_key( 'image_link', 'image_box', $index );
			$content_key 		= $this->get_repeater_setting_key( 'content', 'image_box', $index );
			$title_key 			= $this->get_repeater_setting_key( 'title', 'image_box', $index );
			$description_key    = $this->get_repeater_setting_key( 'description', 'image_box', $index );

			$this->add_render_attribute( $slide_key, 'class', [ 'glozin-image-box', 'text-center', 'swiper-slide', 'elementor-repeater-item-' . $item['_id'] ] );
			$this->add_render_attribute( $description_key, 'class', [ 'glozin-image-box__description' ] );
			$this->add_render_attribute( $content_key, 'class', [ 'glozin-image-box__content', 'w-100' ] );
			$this->add_render_attribute( $image_key, 'class', [ 'glozin-image-box__image', 'gz-image-rounded', 'overflow-hidden', 'position-relative', 'mb-15' ] );
			$this->add_render_attribute( $title_key, 'class', [ 'glozin-image-box__title', 'lh-1', 'fw-semibold', 'mt-0', 'mb-10', 'd-inline-block' ] );

			$this->add_link_attributes( $image_link_key, $item['link'] );
			$this->add_render_attribute( $image_link_key, 'class', [ 'glozin-image-box__image-link', 'gz-hover-zoom', 'w-100', 'gz-hover-effect', 'gz-ratio', 'glozin-elementor-video' ] );
			$aria_label = ! empty( $item['title'] ) ? __( 'View', 'glozin-addons' ) . ' ' . $item['title'] : __( 'View Image', 'glozin-addons' );
			$this->add_render_attribute( $image_link_key, 'aria-label', $aria_label );

			$link_check = true;
			if ( empty( $item['link']['url'] ) ) {
				$link_check = false;
			} 

			echo '<div ' . $this->get_render_attribute_string( $slide_key ) . '>';
				if ( ! empty( $item['image'] ) && ! empty( $item['image']['url'] ) ) {
					echo '<div ' . $this->get_render_attribute_string( $image_key ) . '>';
						if ( $link_check ) {
							echo '<a ' . $this->get_render_attribute_string( $image_link_key ) . '>';
						} else {
							echo '<div ' . $this->get_render_attribute_string( $image_link_key ) . '>';
						}
						if ( $this->has_video( $item ) && 'video' == $item['image_type'] ) {
							$this->render_video( $item );
						} else {
							$image_args = [
								'image'        => ! empty( $item['image'] ) ? $item['image'] : '',
								'image_tablet' => ! empty( $item['image_tablet'] ) ? $item['image_tablet'] : '',
								'image_mobile' => ! empty( $item['image_mobile'] ) ? $item['image_mobile'] : '',
							];
							echo \Glozin\Addons\Helper::get_responsive_image_elementor( $image_args );
						}
						if ( $link_check ) {
							echo '</a>';
						} else {
							echo '</div>';
						}
					echo '</div>';
				}
				echo '<div ' . $this->get_render_attribute_string( $content_key ) . '>';
				if ( ! empty( $item['title'] ) ) {
					if ( $link_check ) {
						$this->add_link_attributes( $title_key, $item['link'] );
						echo '<a ' . $this->get_render_attribute_string( $title_key ) . '>';
					} else {
						echo '<h3 ' . $this->get_render_attribute_string( $title_key ) . '>';
					}
					echo wp_kses_post( $item['title'] );
					if ( $link_check ) {
						echo '</a>';
					} else {
						echo '</h3>';
					}
				}
				if ( ! empty( $item['description'] ) ) {
					echo '<div ' . $this->get_render_attribute_string( $description_key ) . '>';
					echo wp_kses_post( $item['description'] );
					echo '</div>';
				}
				if ( ! empty( $item['button_text'] ) ) {
					$item['button_classes'] = ' mt-25';
					$this->render_button( $item, $index, $item['link'] );
				}
				echo '</div>';
			echo '</div>';

		endforeach;

        echo '</div>';
		echo '<div class="swiper-arrows">'. $this->render_arrows() .'</div>';
	   	echo $this->render_pagination();
		echo '</div>';
	}
}