<?php
namespace Glozin\Addons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Glozin\Addons\Elementor\Base\Products_Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Look Book Products
 */
class Lookbook_Products extends Products_Widget_Base {
	use \Glozin\Addons\Elementor\Base\Button_Base;

	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-lookbook-products';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Glozin] Lookbook Products', 'glozin-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-image-hotspot';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return ['glozin-addons'];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'lookbook', 'products', 'product', 'glozin-addons' ];
	}

	public function get_script_depends() {
		return [
			'glozin-products-carousel-widget',
			'imagesLoaded',
		];
	}

	/**
	 * Styles
	 *
	 * @return void
	 */
	public function get_style_depends() {
		return [
			'glozin-elementor-css'
		];
	}

	/**
	 * Register the widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
	   	$this->start_controls_section(
			'section_lookbook',
			[ 'label' => __( 'Lookbook', 'glozin-addons' ) ]
		);

		$this->add_responsive_control(
			'image',
			[
				'label'    => __( 'Image', 'glozin-addons' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => wc_placeholder_img_src(),
				],
			]
		);

		$this->add_control(
			'sub_title',
			[
				'label' => __( 'Sub Title', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'This is the sub title', 'glozin-addons' ),
				'placeholder' => __( 'Enter your sub title', 'glozin-addons' ),
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
				'separator' => 'before',
			]
		);

		$repeater = new Repeater();

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
                    'size' => 30,
                ],
                'size_units' => [ '%', 'px' ],
                'selectors'  => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'left: {{SIZE}}{{UNIT}};',
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
                    'size' => 30,
                ],
                'size_units' => [ '%', 'px' ],
                'selectors'  => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

		$this->add_control(
			'lookbook_products',
			[
				'label' => esc_html__( 'Products', 'glozin-addons' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'image_heading',
			[
				'label' => esc_html__( 'Image', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->register_aspect_ratio_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_products_carousel',
			[
				'label' => __( 'Carousel Settings', 'glozin-addons' ),
			]
		);

		$controls = [
			'slides_to_show'   => 2,
			'slides_to_scroll' => 1,
			'space_between'    => 20,
			'navigation'       => '',
			'autoplay'         => '',
			'autoplay_speed'   => 3000,
			'pause_on_hover'   => 'yes',
			'animation_speed'  => 800,
			'infinite'         => '',
		];

		$this->register_carousel_controls( $controls );

		$this->end_controls_section();

		// Style
		$this->start_controls_section(
			'section_style_content',
			[
				'label'     => __( 'Content', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'image_width',
			[
				'label'     => __( 'Thumbnail Width', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-lookbook-products' => '--col-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'gap',
			[
				'label'     => __( 'Gap', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-lookbook-products' => '--col-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'summary_heading',
			[
				'label' => esc_html__( 'Summary', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'summary_background_color',
			[
				'label'      => esc_html__( 'Background Color', 'glozin-addons' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .glozin-lookbook-products__summary' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'summary_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-lookbook-products__summary' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'summary_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-lookbook-products__summary' => 'border-start-start-radius: {{TOP}}{{UNIT}}; border-start-end-radius: {{RIGHT}}{{UNIT}}; border-end-end-radius: {{BOTTOM}}{{UNIT}}; border-end-start-radius: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'subtitle_heading',
			[
				'label' => esc_html__( 'Subtitle', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'subtitle_color',
			[
				'label'      => esc_html__( 'Color', 'glozin-addons' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .glozin-lookbook-products__subtitle' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'subtitle_typography',
				'selector' => '{{WRAPPER}} .glozin-lookbook-products__subtitle',
			]
		);

		$this->add_responsive_control(
			'subtitle_spacing',
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
					'{{WRAPPER}} .glozin-lookbook-products__subtitle' => 'margin-bottom: {{SIZE}}{{UNIT}}',
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

		$this->add_control(
			'title_color',
			[
				'label'      => esc_html__( 'Color', 'glozin-addons' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .glozin-lookbook-products__title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .glozin-lookbook-products__title',
			]
		);

		$this->add_responsive_control(
			'title_spacing',
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
					'{{WRAPPER}} .glozin-lookbook-products__title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'product_image_heading',
			[
				'label' => esc_html__( 'Product Image', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'product_image_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}}' => '--gz-image-rounded-product-card: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}}' => '--gz-image-rounded-product-card: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'product_image_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} ul.products li.product .product-thumbnail' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'product_title_heading',
			[
				'label' => esc_html__( 'Product Title', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'product_title_typography',
				'selector' => '{{WRAPPER}} .woocommerce-loop-product__title a',
			]
		);

		$this->add_control(
			'product_title_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-loop-product__title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'product_title_hover_color',
			[
				'label'     => __( 'Hover Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-loop-product__title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'product_price_heading',
			[
				'label' => esc_html__( 'Product Price', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'product_price_typography',
				'selector' => '{{WRAPPER}} .price',
			]
		);

		$this->add_control(
			'product_price_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .price' => 'color: {{VALUE}};',
					'{{WRAPPER}} .price del' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gz-price-unit' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'product_attribute_heading',
			[
				'label' => esc_html__( 'Product Attribute', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'product_attribute_border_hover_color',
			[
				'label'     => __( 'Border Hover Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} ul.products li.product .product-variation-items .product-variation-item:hover,
					ul.products li.product .product-variation-items .product-variation-item.selected' => '--gz-border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'product_attribute_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} ul.products li.product .product-variation-items .product-variation-item' => '--gz-border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_carousel',
			[
				'label' => esc_html__( 'Carousel Settings', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->register_carousel_style_controls();

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$product_ids = [];
		$hotspots = [];

		$col = $settings['slides_to_show'];
		$col_tablet = ! empty( $settings['slides_to_show_tablet'] ) ? $settings['slides_to_show_tablet'] : $col;
		$col_mobile = ! empty( $settings['slides_to_show_mobile'] ) ? $settings['slides_to_show_mobile'] : $col_tablet;

		if( empty( $settings['lookbook_products'] ) ) {
			return;
		}

		foreach( $settings['lookbook_products'] as $key => $hotspot ) {
			$product = wc_get_product( $hotspot['product_ids'] );
			if( ! empty( $product ) && $product->is_visible() ) {
				$product_ids[] = $hotspot['product_ids'];

				if( ! empty( $hotspot['product_ids'] ) ) {
					$hotspots[] = sprintf('<div class="glozin-lookbook-products__hotspot d-inline-flex align-items-center justify-content-center glozin-button-hotspot--animation rounded-100 position-absolute z-2 elementor-repeater-item-%s" data-product_id="%s" data-index="%s">%s</div>',
									$hotspot['_id'],
									$hotspot['product_ids'],
									esc_attr( $key ),
									\Glozin\Addons\Helper::get_svg( 'icon-lookbook-plus', 'ui', [ 'class' => 'gz-button gz-button-light gz-button-icon' ] )
								);
				}
			}
		}

		$this->add_render_attribute( 'wraper', 'class', [ 'glozin-lookbook-products', 'd-flex', 'flex-column', 'flex-md-row', 'w-100', 'overflow-hidden' ] );
		$this->add_render_attribute( 'thumbnail', 'class', [ 'glozin-lookbook-products__thumbnail', 'position-relative', 'column-md-custom' ] );
		$this->add_render_attribute( 'thumbnail', 'style', $this->render_aspect_ratio_style() );
		$this->add_render_attribute( 'image', 'class', [ 'glozin-lookbook-products__image', 'position-relative', 'glozin-elementor-video', 'gz-ratio', 'gz-image-rounded', 'overflow-hidden', 'h-100' ] );
		$this->add_render_attribute( 'summary', 'class', [ 'glozin-lookbook-products__summary', 'd-flex', 'flex-column', 'align-items-center', 'justify-content-center', 'column-md-custom-remaining', 'px-40', 'py-30', 'rounded-10' ] );

		$this->add_render_attribute( 'subtitle', 'class', [ 'glozin-lookbook-products__subtitle', 'text-center', 'fw-bold', 'fs-14', 'text-uppercase', 'text-dark' ] );
		$this->add_render_attribute( 'title', 'class', [ 'glozin-lookbook-products__title', 'text-center', 'heading-letter-spacing', 'mt-0', 'mb-33' ] );

		$this->add_render_attribute( 'products', 'class', [ 'glozin-lookbook-products__products', 'glozin-products-carousel', 'glozin-carousel--elementor', 'w-100' ] );
		$this->add_render_attribute( 'swiper', 'class', [ 'swiper', 'product-swiper--elementor' ] );
		$this->add_render_attribute( 'swiper', 'data-desktop', $col );
		$this->add_render_attribute( 'swiper', 'data-tablet', $col_tablet );
		$this->add_render_attribute( 'swiper', 'data-mobile', $col_mobile );
		$this->add_render_attribute( 'swiper', 'style', $this->render_space_between_style() );

		?>
		<div <?php echo $this->get_render_attribute_string( 'wraper' );?>>
			<div <?php echo $this->get_render_attribute_string( 'thumbnail' );?>>
				<div <?php echo $this->get_render_attribute_string( 'image' );?>>
					<?php if ( ! empty( $settings['image']['url'] ) ) :
							$args = [];
							$args['image'] = $settings['image'];
							$args['image_size'] = 'full';
							echo wp_kses_post( \Elementor\Group_Control_Image_Size::get_attachment_image_html( $args ) );
						?>
					<?php endif; ?>
				</div>
				<?php echo ! empty( $hotspots ) ? implode( '', $hotspots ) : ''; ?>
			</div>
			<div <?php echo $this->get_render_attribute_string( 'summary' );?>>
				<?php if( ! empty( $settings['sub_title'] ) ) : ?>
					<div <?php echo $this->get_render_attribute_string( 'subtitle' );?>>
						<?php echo wp_kses_post( $settings['sub_title'] ); ?>
					</div>
				<?php endif; ?>
				<?php if( ! empty( $settings['title'] ) ) : ?>
					<h2 <?php echo $this->get_render_attribute_string( 'title' );?>>
						<?php echo wp_kses_post( $settings['title'] ); ?>
					</h2>
				<?php endif; ?>
				<?php if( ! empty( $product_ids ) ) : ?>
				<div <?php echo $this->get_render_attribute_string( 'products' );?>>
					<div <?php echo $this->get_render_attribute_string( 'swiper' );?>>
						<?php
							$args = [
								'type'    => 'custom_products',
								'ids'     => implode( ',', $product_ids ),
								'columns' => 2,
							];
							echo $this->render_products( $args );
							echo $this->render_arrows();
							echo $this->render_pagination();
						?>
					</div>
				</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}