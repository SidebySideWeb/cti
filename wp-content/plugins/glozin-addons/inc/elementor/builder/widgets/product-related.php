<?php
namespace Glozin\Addons\Elementor\Builder\Widgets;

use Glozin\Addons\Elementor\Base\Products_Widget_Base;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_Related extends Products_Widget_Base {
	use \Glozin\Addons\Elementor\Builder\Traits\Product_Id_Trait;

	public function get_name() {
		return 'glozin-product-related';
	}

	public function get_title() {
		return esc_html__( '[Glozin] Product Related', 'glozin-addons' );
	}

	public function get_icon() {
		return 'eicon-product-related';
	}

	public function get_categories() {
		return ['glozin-addons-product'];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'product', 'related' ];
	}

	public function get_script_depends() {
		return [
			'glozin-product-elementor-widgets',
			'imagesLoaded',
		];
	}

	public function get_style_depends() {
		return [
			'glozin-products-carousel-css',
		];
	}

	/**
	 * Register heading widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_heading_content',
			[
				'label' => esc_html__( 'Heading', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'heading',
			[
				'label' => __( 'Heading', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter your title', 'glozin-addons' ),
				'default' => esc_html__( 'Related Products', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'description',
			[
				'label' => __( 'Description', 'glozin-addons' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => __( 'Enter your description', 'glozin-addons' ),
				'default' => '',
			]
		);

		$this->add_control(
			'title_size',
			[
				'label' => __( 'HTML Tag', 'glozin-addons' ),
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

		$this->end_controls_section();

		$this->start_controls_section(
			'section_related_products_content',
			[
				'label' => esc_html__( 'Related Products', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label' => esc_html__( 'Products Per Page', 'glozin-addons' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 8,
				'range' => [
					'px' => [
						'max' => 20,
					],
				],
			]
		);

		$this->add_control(
			'orderby',
			[
				'label' => esc_html__( 'Order By', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'rand',
				'options' => [
					'date'       => esc_html__( 'Date', 'glozin-addons' ),
					'title'      => esc_html__( 'Title', 'glozin-addons' ),
					'price'      => esc_html__( 'Price', 'glozin-addons' ),
					'popularity' => esc_html__( 'Popularity', 'glozin-addons' ),
					'rating'     => esc_html__( 'Rating', 'glozin-addons' ),
					'rand'       => esc_html__( 'Random', 'glozin-addons' ),
					'menu_order' => esc_html__( 'Menu Order', 'glozin-addons' ),
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label' => esc_html__( 'Order', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'desc',
				'options' => [
					'asc'  => esc_html__( 'ASC', 'glozin-addons' ),
					'desc' => esc_html__( 'DESC', 'glozin-addons' ),
				],
			]
		);

		$this->end_controls_section();

		$this->section_content_carousel();

		$this->start_controls_section(
			'section_style',
			[
				'label'     => __( 'Style', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .related-product-carousel' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .related-product-carousel' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .related-product-carousel' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .related-product-carousel' => 'margin: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'content_border',
				'label' => esc_html__( 'Border', 'glozin-addons' ),
				'selector' => '{{WRAPPER}} .related-product-carousel',
			]
		);

		$this->add_control(
			'heading_heading',
			[
				'label'     => esc_html__( 'Heading', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'heading_text_align',
			[
				'label'       => esc_html__( 'Text Align', 'glozin-addons' ),
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
					'{{WRAPPER}} .related-product-carousel__heading' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'heading_typography',
				'selector' => '{{WRAPPER}} .related-product-carousel__heading',
			]
		);

		$this->add_control(
			'heading_color',
			[
				'label'     => esc_html__( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .related-product-carousel__heading' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'heading_spacing',
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
					'{{WRAPPER}} .related-product-carousel__heading' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'description_heading',
			[
				'label'     => esc_html__( 'Description', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'description_text_align',
			[
				'label'       => esc_html__( 'Text Align', 'glozin-addons' ),
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
					'{{WRAPPER}} .related-product-carousel__description' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .related-product-carousel__description',
			]
		);

		$this->add_control(
			'description_color',
			[
				'label'     => esc_html__( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .related-product-carousel__description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'description_spacing',
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
					'{{WRAPPER}} .related-product-carousel__description' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_carousel_style',
			[
				'label'     => __( 'Carousel Settings', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->register_carousel_style_controls();

		$this->end_controls_section();
	}

	protected function section_content_carousel() {
		$this->start_controls_section(
			'section_products_carousel',
			[
				'label' => __( 'Carousel Settings', 'glozin-addons' ),
			]
		);

		$controls = [
			'slides_to_show'   => 4,
			'slides_to_scroll' => 1,
			'space_between'    => 30,
			'navigation'       => '',
			'autoplay'         => '',
			'autoplay_speed'   => 3000,
			'pause_on_hover'   => 'yes',
			'animation_speed'  => 800,
			'infinite'         => '',
			'reveal_on_scroll' => '',
			'slidesperview_auto' => '',
		];

		$this->register_carousel_controls( $controls );

		$this->end_controls_section();
	}

	/**
	 * Render heading widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		global $product;
		$product = $this->get_product();

		if ( ! $product ) {
			return;
		}

		$settings = $this->get_settings_for_display();

		$args = [
			'posts_per_page' => $settings['posts_per_page'],
			'columns' => ! empty( $settings['slides_to_show'] ) ? $settings['slides_to_show'] : 4,
			'orderby' => $settings['orderby'],
			'order' => $settings['order'],
		];

		if( function_exists('wc_set_loop_prop') ) {
			wc_set_loop_prop( 'name', 'related' );
			wc_set_loop_prop( 'columns', apply_filters( 'woocommerce_related_products_columns', $args['columns'] ) );
		}

		$args = array_map( 'sanitize_text_field', $args );

		$args['related_products'] = array_filter( array_map( 'wc_get_product', wc_get_related_products( $product->get_id(), $args['posts_per_page'], $product->get_upsell_ids() ) ), 'wc_products_array_filter_visible' );
		$related_products = wc_products_array_orderby( $args['related_products'], $args['orderby'], $args['order'] );

		$this->add_render_attribute( 'wrapper', 'class', [
			'related-product-carousel',
			'glozin-products-carousel',
			'glozin-carousel--elementor',
			'woocommerce',
		] );

		$this->add_render_attribute( 'heading', 'class', [ 'related-product-carousel__heading', 'text-center', 'mt-0', 'mb-10', 'heading-letter-spacing' ] );
		$this->add_render_attribute( 'description', 'class', [ 'related-product-carousel__description', 'text-center', 'mb-33' ] );

		$col = $settings['slides_to_show'];
		$col_tablet = ! empty( $settings['slides_to_show_tablet'] ) ? $settings['slides_to_show_tablet'] : $col;
		$col_mobile = ! empty( $settings['slides_to_show_mobile'] ) ? $settings['slides_to_show_mobile'] : $col_tablet;

		$this->add_render_attribute( 'swiper', 'class', [ 'swiper', 'product-swiper--elementor' ] );
		$this->add_render_attribute( 'swiper', 'data-desktop', $col );
		$this->add_render_attribute( 'swiper', 'data-tablet', $col_tablet );
		$this->add_render_attribute( 'swiper', 'data-mobile', $col_mobile );
		$this->render_slidesperview_auto_class_style( 'swiper' );

		if( empty( $related_products ) ) {
			return;
		}
		?>
			<div <?php echo $this->get_render_attribute_string( 'wrapper' ) ?>>
				<?php if( ! empty( $settings['heading'] ) ) : ?>
					<<?php echo esc_attr( $settings['title_size'] ); ?> <?php echo $this->get_render_attribute_string( 'heading' ); ?>>
						<?php echo wp_kses_post( $settings['heading'] ); ?>
					</<?php echo esc_attr( $settings['title_size'] ); ?>>
				<?php endif; ?>
				<?php if( ! empty( $settings['description'] ) ) : ?>
					<div <?php echo $this->get_render_attribute_string( 'description' ); ?>><?php echo wp_kses_post( $settings['description'] ); ?></div>
				<?php endif; ?>
				<div <?php echo $this->get_render_attribute_string( 'swiper' ) ?>>
					<?php woocommerce_product_loop_start(); ?>
						<?php foreach ( $related_products as $related_product ) : ?>
							<?php
								$post_object = get_post( $related_product->get_id() );

								setup_postdata( $GLOBALS['post'] = $post_object );

								wc_get_template_part( 'content', 'product' );
							?>
						<?php endforeach; ?>
					<?php woocommerce_product_loop_end(); ?>
					<?php echo $this->render_arrows(); ?>
					<?php echo $this->render_pagination(); ?>
				</div>
			</div>
		<?php
		wp_reset_postdata();
	}
}
