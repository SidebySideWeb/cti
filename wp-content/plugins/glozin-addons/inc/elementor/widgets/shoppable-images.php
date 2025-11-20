<?php

namespace Glozin\Addons\Elementor\Widgets;

use Glozin\Addons\Elementor\Base\Products_Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Glozin\Addons\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Shoppable Images Carousel widget
 */
class Shoppable_Images extends Products_Widget_Base {
    use \Glozin\Addons\Elementor\Base\Video_Base;
    use \Glozin\Addons\Elementor\Base\Button_Base;

	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-shoppable-images';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( '[Glozin] Shoppable Image Content', 'glozin-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-hotspot';
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
			'glozin-shoppable-images-widget'
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

        $this->add_control(
			'source',
			[
				'label' => esc_html__( 'Media Source', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'image' 	=> esc_html__( 'Image', 'glozin-addons' ),
					'video' 	=> esc_html__( 'Video', 'glozin-addons' ),
				],
				'default' => 'image',
			]
		);

		$this->add_control(
			'image',
			[
				'label'    => __( 'Image', 'glozin-addons' ),
				'type' => Controls_Manager::MEDIA,
				'media_types' => [ 'image' ],
				'default' => [
					'url' => wc_placeholder_img_src(),
				],
				'condition' => [
					'source' => 'image',
				],
			]
		);

		$this->register_video_repeater_controls( $this, [ 'source' => 'video' ] );

        $hotspot_repeater = new \Elementor\Repeater();

        $hotspot_repeater->add_control(
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

        $hotspot_repeater->add_control(
            'point_popover_toggle',
            [
                'label'        => esc_html__( 'Point', 'glozin-addons' ),
                'type'         => Controls_Manager::POPOVER_TOGGLE,
                'label_off'    => esc_html__( 'Default', 'glozin-addons' ),
                'label_on'     => esc_html__( 'Custom', 'glozin-addons' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $hotspot_repeater->start_popover();

        $hotspot_repeater->add_responsive_control(
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
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'left: {{SIZE}}{{UNIT}};--gz-shoppable-images-hotspot-x: -{{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $hotspot_repeater->add_responsive_control(
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
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'top: {{SIZE}}{{UNIT}};--gz-shoppable-images-hotspot-y: -{{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $hotspot_repeater->end_popover();

        $this->add_control(
            'hotspots',
            [
                'label' => esc_html__( 'Hotspot items', 'glozin-addons' ),
                'type'       => Controls_Manager::REPEATER,
                'show_label' => true,
                'fields'     => $hotspot_repeater->get_controls(),
                'default'    => [],
            ]
        );

        $this->add_control(
			'content',
			[
				'label' => esc_html__( 'Content', 'glozin-addons' ),
				'type' => Controls_Manager::WYSIWYG,
				'placeholder' => esc_html__( 'Enter your content', 'glozin-addons' ),
				'dynamic' => [
					'active' => true,
				],
                'separator' => 'before',
			]
		);

        $tag_repeater = new \Elementor\Repeater();

        $tag_repeater->add_control(
			'tag',
			[
				'label' => __( 'Tag', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Tag', 'glozin-addons' ),
				'label_block' => true,
			]
		);

        $tag_repeater->add_control(
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

        $this->add_control(
            'tags',
            [
                'label' => esc_html__( 'Tags', 'glozin-addons' ),
                'type'       => Controls_Manager::REPEATER,
                'show_label' => true,
                'fields'     => $tag_repeater->get_controls(),
                'title_field' => '{{{ tag }}}',
                'default'    => [],
                'separator' => 'before',
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

        $this->register_button_controls();
        
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

		$this->add_responsive_control(
			'image_video_width',
			[
				'label'     => __( 'Thumbnail Width', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-shoppable-images' => '--col-width: {{SIZE}}{{UNIT}};',
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
			'summary_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-shoppable-images__summary' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'products_heading',
			[
				'label' => esc_html__( 'Products', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'products_gap',
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
					'{{WRAPPER}} .glozin-shoppable-images__products ul.products' => 'gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'products_spacing',
			[
				'label' => __( 'Spacing', 'glozin-addons' ),
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
					'{{WRAPPER}} .glozin-shoppable-images__products' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'product_image_heading',
			[
				'label' => esc_html__( 'Product Image', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->register_aspect_ratio_controls();

		$this->add_responsive_control(
			'product_image_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-shoppable-images__products' => '--gz-image-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'product_title_heading',
			[
				'label' => esc_html__( 'Product Name', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'product_title_typography',
				'selector' => '{{WRAPPER}} .glozin-shoppable-images__products .woocommerce-loop-product__title a',
			]
		);

		$this->add_control(
			'product_title_color',
			[
				'label'     => esc_html__( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-shoppable-images__products .woocommerce-loop-product__title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'product_title_color_hover',
			[
				'label'     => esc_html__( 'Hover Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-shoppable-images__products .woocommerce-loop-product__title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'product_price_heading',
			[
				'label' => esc_html__( 'Price', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'product_price_typography',
				'selector' => '{{WRAPPER}} .glozin-shoppable-images__products .price',
			]
		);

		$this->add_control(
			'product_price_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-shoppable-images__products .price' => 'color: {{VALUE}};',
					'{{WRAPPER}} .glozin-shoppable-images__products .price del' => 'color: {{VALUE}};',
					'{{WRAPPER}} .glozin-shoppable-images__products .gz-price-unit' => 'color: {{VALUE}};',
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

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'content_typography',
				'selector' => '{{WRAPPER}} .glozin-shoppable-images__content',
			]
		);

		$this->add_control(
			'content_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-shoppable-images__content' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_spacing',
			[
				'label'     => __( 'Spacing', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 200,
						'min' => 0,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-shoppable-images__content' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'tags_heading',
			[
				'label' => esc_html__( 'Tags', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'tags_gap',
			[
				'label'     => __( 'Gap', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 200,
						'min' => 0,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-shoppable-images__tags' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'tags_typography',
				'selector' => '{{WRAPPER}} .glozin-shoppable-images__tags a',
			]
		);

		$this->add_control(
			'tags_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-shoppable-images__tags a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tags_color_hover',
			[
				'label'     => __( 'Hover Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-shoppable-images__tags a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'tags_spacing',
			[
				'label'     => __( 'Spacing', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 200,
						'min' => 0,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-shoppable-images__tags' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label' => esc_html__( 'Button Style', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->register_button_style_controls( 'text' );

		$this->end_controls_section();
	}

	/**
	 * Render icon box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$product_ids = [];
		$hotspots = [];

		if( empty( $settings['hotspots'] ) ) {
			return;
		}
		foreach( $settings['hotspots'] as $hotspot ) {
			$product = wc_get_product( $hotspot['product_ids'] );
			if( ! empty( $product ) && $product->is_visible() ) {
				$product_ids[] = $hotspot['product_ids'];

				if( ! empty( $hotspot['product_ids'] ) ) {
					$hotspots[] = sprintf('<div class="glozin-shoppable-images__hotspot d-inline-flex align-items-center justify-content-center glozin-button-hotspot--animation rounded-100 position-absolute z-2 elementor-repeater-item-%s" data-product_id="%s">%s</div>',
									$hotspot['_id'],
									$hotspot['product_ids'],
									Helper::get_svg( 'icon-lookbook-plus', 'ui', [ 'class' => 'gz-button gz-button-light gz-button-icon' ] )
								);
				}
			}
		}

		$this->add_render_attribute( 'wraper', 'class', [ 'glozin-shoppable-images', 'd-flex-md', 'flex-md-row', 'w-100', 'overflow-hidden' ] );
		$this->add_render_attribute( 'thumbnail', 'class', [ 'glozin-shoppable-images__thumbnail', 'position-relative', 'column-md-custom' ] );
		$this->add_render_attribute( 'thumbnail', 'style', $this->render_aspect_ratio_style() );
		$this->add_render_attribute( 'image', 'class', [ 'glozin-shoppable-images__image', 'position-relative', 'glozin-elementor-video', 'gz-ratio', 'gz-image-rounded', 'overflow-hidden' ] );
		$this->add_render_attribute( 'summary', 'class', [ 'glozin-shoppable-images__summary', 'column-md-custom-remaining', 'px-15', 'px-md-30', 'py-30' ] );

		$this->add_render_attribute( 'products', 'class', [ 'glozin-shoppable-images__products', 'mb-25' ] );
		$this->add_render_attribute( 'content', 'class', [ 'glozin-shoppable-images__content', 'mb-15' ] );
		$this->add_render_attribute( 'tags', 'class', [ 'glozin-shoppable-images__tags', 'd-flex', 'flex-wrap', 'align-items-center', 'gap-5', 'mb-20' ] );
		$this->add_render_attribute( 'tag', 'class', [ 'glozin-shoppable-images__tag' ] );

		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'add_to_cart_button' ), 30 );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wraper' );?>>
			<div <?php echo $this->get_render_attribute_string( 'thumbnail' );?>>
				<div <?php echo $this->get_render_attribute_string( 'image' );?>>
					<?php if ( ! empty( $settings['image']['url'] ) && 'image' == $settings['source'] ) :
							$args = [];
							$args['image'] = $settings['image'];
							$args['image_size'] = 'full';
							echo wp_kses_post( \Elementor\Group_Control_Image_Size::get_attachment_image_html( $args ) );
						?>
					<?php endif; ?>
					<?php if ( $this->has_video( $settings ) && 'video' == $settings['source'] ) : ?>
						<?php $this->render_video( $settings ); ?>
					<?php endif; ?>
				</div>
				<?php echo ! empty( $hotspots ) ? implode( '', $hotspots ) : ''; ?>
			</div>
			<div <?php echo $this->get_render_attribute_string( 'summary' );?>>
				<?php if( ! empty( $product_ids ) ) : ?>
				<div <?php echo $this->get_render_attribute_string( 'products' );?>>
					<?php
						$args = [
							'type'    => 'custom_products',
							'ids'     => implode( ',', $product_ids ),
							'columns' => 2,
						];
						echo $this->render_products( $args );
					?>
				</div>
				<?php endif; ?>
				<?php if( ! empty( $settings['content'] ) ) : ?>
					<div <?php echo $this->get_render_attribute_string( 'content' );?>>
						<?php echo wp_kses_post( wpautop( $settings['content'] ) ); ?>
					</div>
				<?php endif; ?>
				<?php if( ! empty( $settings['tags'] ) ) : ?>
					<div <?php echo $this->get_render_attribute_string( 'tags' );?>>
						<?php foreach( $settings['tags'] as $tag ) : ?>
							<?php if( ! empty( $tag['link']['url'] ) ) : ?>
								<a href="<?php echo esc_url( $tag['link']['url'] ); ?>" <?php echo $this->get_render_attribute_string( 'tag' );?>>
									<?php echo esc_html( $tag['tag'] ); ?>
								</a>
							<?php else : ?>
								<span <?php echo $this->get_render_attribute_string( 'tag' );?>>
									<?php echo esc_html( $tag['tag'] ); ?>
								</span>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<?php $this->render_button( '', '', '', [ 'classes' => 'glozin-shoppable-images__button', 'icon_default' => Helper::get_svg( 'icon-instagram' ) ] ); ?>
			</div>
		</div>
		<?php

		remove_action( 'woocommerce_after_shop_loop_item', array( $this, 'add_to_cart_button' ), 30 );
	}

	/**
	 * Add to cart button
	 */
	public static function add_to_cart_button() {
		woocommerce_template_loop_add_to_cart(
			array(
				'button_classes' => 'gz-button-add-to-cart gz-button gz-button-no-icon mt-15 gz-button gz-button-subtle',
			)
		);
	}
}
