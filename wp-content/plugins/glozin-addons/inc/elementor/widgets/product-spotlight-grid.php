<?php

namespace Glozin\Addons\Elementor\Widgets;

use Glozin\Addons\Elementor\Base\Products_Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Banner Carousel widget
 */
class Product_Spotlight_Grid extends Products_Widget_Base {
	use \Glozin\Addons\Elementor\Base\Aspect_Ratio_Base;
	use \Glozin\Addons\Elementor\Base\Button_Base;

	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-product-spotlight-grid';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( '[Glozin] Product Spotlight Grid', 'glozin-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-gallery-grid';
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
		$this->content_sections();
		$this->style_sections();
	}

	protected function content_sections() {
		$this->start_controls_section(
			'section_options',
			[
				'label' => __( 'Spotlight', 'glozin-addons' ),
			]
		);

		$repeater = new \Elementor\Repeater();

        $repeater->add_responsive_control(
			'image',
			[
				'label'   => esc_html__( 'Image', 'glozin-addons' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => wc_placeholder_img_src(),
				],
			]
		);

        $repeater->add_control(
			'name',
			[
				'label' => __( 'Name', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => __( 'Enter your name', 'glozin-addons' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
            'product_ids',
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

		$this->add_control(
			'items',
			[
				'label'       => __( 'Items', 'glozin-addons' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default' => [
					[
						'image' => [
							'url' => wc_placeholder_img_src(),
						],
						'name' => __( 'Name 1', 'glozin-addons' ),
					],
					[
						'image' => [
							'url' => wc_placeholder_img_src(),
						],
						'name' => __( 'Name 2', 'glozin-addons' ),
					],
					[
						'image' => [
							'url' => wc_placeholder_img_src(),
						],
						'name' => __( 'Name 3', 'glozin-addons' ),
					],
					[
						'image' => [
							'url' => wc_placeholder_img_src(),
						],
						'name' => __( 'Name 4', 'glozin-addons' ),
					],
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
				'default' => '',
				'label_block' => true,
			]
		);

		$this->add_control(
			'description',
			[
				'label' => __( 'Description', 'glozin-addons' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => '',
				'label_block' => true,
			]
		);

		$this->register_button_controls();

		$this->end_controls_section();
	}

	protected function style_sections() {
		$this->content_style_sections();
		$this->button_style_sections();
	}

	protected function content_style_sections() {
		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Spotlight', 'glozin-addons' ),
                'tab'   => Controls_Manager::TAB_STYLE,
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

		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}}' => '--gz-image-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}}' => '--gz-image-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_large_heading',
			[
				'label' => esc_html__( 'Image Large', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->register_aspect_ratio_controls( [], [ 'aspect_ratio_type' => 'vertical' ] );

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
				'selector' => '{{WRAPPER}} .glozin-product-spotlight__sub-title',
			]
		);

        $this->add_control(
			'sub_title_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-product-spotlight__sub-title' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .glozin-product-spotlight__sub-title' => 'margin-top: {{TOP}}{{UNIT}}; margin-inline-end: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}}; margin-inline-start: {{LEFT}}{{UNIT}};',
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
				'selector' => '{{WRAPPER}} .glozin-product-spotlight__title',
			]
		);

        $this->add_control(
			'title_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-product-spotlight__title' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .glozin-product-spotlight__title' => 'margin-top: {{TOP}}{{UNIT}}; margin-inline-end: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}}; margin-inline-start: {{LEFT}}{{UNIT}};',
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
				'selector' => '{{WRAPPER}} .glozin-product-spotlight__description',
			]
		);

        $this->add_control(
			'description_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-product-spotlight__description' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .glozin-product-spotlight__description' => 'margin-top: {{TOP}}{{UNIT}}; margin-inline-end: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}}; margin-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'list_items_heading',
			[
				'label' => esc_html__( 'List Item', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'list_items_gap',
			[
				'label' => __( 'Gap', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					]
				],
				'default' => [],
				'selectors' => [
					'{{WRAPPER}} .glozin-product-spotlight__content-inner' => '--gz-product-spotlight-item: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'list_items_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-product-spotlight__content-inner' => 'margin-top: {{TOP}}{{UNIT}}; margin-inline-end: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}}; margin-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function button_style_sections() {
		$this->start_controls_section(
			'section_button_style',
			[
				'label' => __( 'Button', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->register_button_style_controls();

		$this->end_controls_section();
	}

	/**
	 * Render icon box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

        $this->add_render_attribute( 'spotlight', 'class', [ 'glozin-product-spotlight-grid', 'd-flex', 'flex-column', 'flex-md-row-reverse', 'gap-60' ] );

        $this->add_render_attribute( 'content', 'class', [ 'glozin-product-spotlight__content', 'w-50-md', 'd-flex', 'flex-column', 'align-items-center', 'justify-content-center', 'text-center' ] );
        $this->add_render_attribute( 'content_inner', 'class', [ 'glozin-product-spotlight__content-inner', 'd-flex', 'flex-wrap', 'w-100', 'px-xl-25' ] );
        $this->add_render_attribute( 'sub_title', 'class', [ 'glozin-product-spotlight__sub-title', 'fs-12', 'fw-semibold', 'text-uppercase', 'text-dark', 'mb-5' ] );
        $this->add_render_attribute( 'title', 'class', [ 'glozin-product-spotlight__title', 'mt-0', 'mb-10' ] );
        $this->add_render_attribute( 'description', 'class', [ 'glozin-product-spotlight__description', 'mb-50' ] );
        $this->add_render_attribute( 'image', 'class', [ 'glozin-product-spotlight__image', 'w-50-md', 'position-relative', 'gz-ratio' ] );
		$this->add_render_attribute( 'image', 'style', $this->render_aspect_ratio_style() );

		$settings['button_classes'] = ' glozin-product-spotlight__button';

       echo '<div ' . $this->get_render_attribute_string( 'spotlight' ) . '>';
	   echo '<div ' . $this->get_render_attribute_string( 'image' ) . '>';
		foreach ( $settings['items'] as $index => $item ) :
			$image_key 		= $this->get_repeater_setting_key( 'content_image', 'spotlight', $index );
			$this->add_render_attribute( $image_key, 'class', [ 'glozin-product-spotlight__image-item', 'position-absolute', 'top-0', 'start-0', 'bottom-0', 'end-0' ] );

			if ( $index === 0 ) {
				$this->add_render_attribute( $image_key, 'class', 'active' );
			}

			if( ! empty( $item['image'] ) && ! empty( $item['image']['url'] ) ) {
				$this->add_render_attribute( $image_key, 'data-id', $item['image']['id'] );

				if ( $item['product_ids'] ) {
					$this->add_render_attribute( $image_key, 'href', get_permalink( $item['product_ids'] ) );
					echo '<a '. $this->get_render_attribute_string( $image_key ) .'>';
				} else {
					echo '<div '. $this->get_render_attribute_string( $image_key ) .'>';
				}

					$image_args = [
						'image'        => ! empty( $item['image'] ) ? $item['image'] : '',
						'image_tablet' => ! empty( $item['image_tablet'] ) ? $item['image_tablet'] : '',
						'image_mobile' => ! empty( $item['image_mobile'] ) ? $item['image_mobile'] : '',
					];
					echo \Glozin\Addons\Helper::get_responsive_image_elementor( $image_args );

				if ( $item['product_ids'] ) {
					echo '</a>';
				} else {
					echo '</div>';
				}
			}
		endforeach;
       echo '</div>';
       echo '<div ' . $this->get_render_attribute_string( 'content' ) . '>';

		if ( $settings['sub_title'] ) {
			echo '<div ' . $this->get_render_attribute_string( 'sub_title' ) . '>';
				echo wp_kses_post( $settings['sub_title'] );
			echo '</div>';
		}
		if ( $settings['title'] ) {
			echo '<h2 ' . $this->get_render_attribute_string( 'title' ) . '>';
				echo wp_kses_post( $settings['title'] );
			echo '</h2>';
		}
		if ( $settings['description'] ) {
			echo '<div ' . $this->get_render_attribute_string( 'description' ) . '>';
				echo wp_kses_post( $settings['description'] );
			echo '</div>';
		}

		echo '<div ' . $this->get_render_attribute_string( 'content_inner' ) . '>';
			foreach ( $settings['items'] as $index => $item ) :
				$content_item 			= $this->get_repeater_setting_key( 'content_item', 'spotlight', $index );
				$content_name_key 		= $this->get_repeater_setting_key( 'content_name', 'spotlight', $index );
				$link_key 				= $this->get_repeater_setting_key( 'link', 'spotlight', $index );

				$this->add_render_attribute( $content_item, 'class', [ 'glozin-product-spotlight__item', 'text-center' ] );
				$this->add_render_attribute( $content_name_key, 'class', [ 'glozin-product-spotlight__content-name', 'fw-semibold', 'mt-7' ] );

				$this->add_render_attribute( $link_key, 'class', [ 'glozin-product-spotlight__content-image', 'gz-ratio' ] );
				$this->add_render_attribute( $link_key, 'aria-label', $item['name'] );

				if ( $index === 0 ) {
					$this->add_render_attribute( $content_item, 'class', 'active' );
				}

				$link_check = false;
				if ( $item['product_ids'] ) {
					$link_check = true;
				}

				echo '<div ' . $this->get_render_attribute_string( $content_item ) . '>';
					if( ! empty( $item['image'] ) && ! empty( $item['image']['url'] ) ) {
						$this->add_render_attribute( $link_key, 'data-id', $item['image']['id'] );

						if ( $link_check ) {
							$this->add_render_attribute( $link_key, 'href', get_permalink( $item['product_ids'] ) );
							echo '<a '. $this->get_render_attribute_string( $link_key ) .'>';
						} else {
							echo '<div '. $this->get_render_attribute_string( $link_key ) .'>';
						}
							$image_args = [
								'image'        => ! empty( $item['image'] ) ? $item['image'] : '',
								'image_tablet' => ! empty( $item['image_tablet'] ) ? $item['image_tablet'] : '',
								'image_mobile' => ! empty( $item['image_mobile'] ) ? $item['image_mobile'] : '',
							];
							echo \Glozin\Addons\Helper::get_responsive_image_elementor( $image_args );

						if ( $link_check ) {
							echo '</a>';
						} else {
							echo '</div>';
						}
					}

					if ( $item['name'] ) {
						echo '<div ' . $this->get_render_attribute_string( $content_name_key ) . '>';
							if ( $link_check ) {
								echo '<a href="'. esc_url( get_permalink( $item['product_ids'] ) ) .'" aria-label="'. esc_html( $item['name'] ) .'">';
							}
							echo wp_kses_post( $item['name'] );
							if ( $link_check ) {
								echo '</a>';
							}
						echo '</div>';
					}
				echo '</div>';
			endforeach;
		echo '</div>';
	   $this->render_button($settings);
       echo '</div>';
       echo '</div>';
	}
}
