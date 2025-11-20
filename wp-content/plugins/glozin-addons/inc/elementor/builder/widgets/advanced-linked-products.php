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

class Advanced_Linked_Products extends Products_Widget_Base {
	use \Glozin\Addons\Elementor\Builder\Traits\Product_Id_Trait;

	public function get_name() {
		return 'glozin-advanced-linked-products';
	}

	public function get_title() {
		return esc_html__( '[Glozin] Advanced Linked Products', 'glozin-addons' );
	}

	public function get_icon() {
		return 'eicon-woocommerce';
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'advanced', 'linked', 'products', 'product' ];
	}

	public function get_categories() {
		return [ 'glozin-addons-product' ];
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
				'default' => esc_html__( 'Pairs well with', 'glozin-addons' ),
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
				'default' => 'h5',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_products_content',
			[
				'label' => esc_html__( 'Products', 'glozin-addons' ),
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

		$this->end_controls_section();

		$this->start_controls_section(
			'section_products_carousel',
			[
				'label' => __( 'Carousel Settings', 'glozin-addons' ),
			]
		);

		$controls = [
			'slides_to_show'   => 3,
			'slides_to_scroll' => 1,
			'space_between'    => 30,
			'navigation'       => '',
			'autoplay'         => '',
			'autoplay_speed'   => 3000,
			'pause_on_hover'   => 'yes',
			'animation_speed'  => 800,
			'infinite'         => '',
		];

		$this->register_carousel_controls( $controls );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label'     => __( 'Style', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .advanced-linked-products' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .advanced-linked-products' => 'margin: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .advanced-linked-products' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .advanced-linked-products' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'content_border',
				'label' => esc_html__( 'Border', 'glozin-addons' ),
				'selector' => '{{WRAPPER}} .advanced-linked-products',
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
					'{{WRAPPER}} .advanced-linked-products__heading' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'heading_typography',
				'selector' => '{{WRAPPER}} .advanced-linked-products__heading',
			]
		);

		$this->add_control(
			'heading_color',
			[
				'label'     => esc_html__( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .advanced-linked-products__heading' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .advanced-linked-products__heading' => 'margin-bottom: {{SIZE}}{{UNIT}}',
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

	protected function render() {
		global $product;

		$product = $this->get_product();

		if ( ! $product ) {
			return;
		}

		$settings = $this->get_settings_for_display();

		$product_ids = maybe_unserialize( get_post_meta( $product->get_id(), 'glozin_advanced_linked_product_ids', true ) );
		$product_ids = apply_filters( 'glozin_advanced_linked_product_ids', $product_ids, $product );

		if ( empty( $product_ids ) && \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
			$product_ids = self::advanced_linked_product_ids( $settings );
		}

		$this->add_render_attribute( 'wrapper', 'class', [
			'advanced-linked-products',
			'glozin-products-carousel',
			'glozin-carousel--elementor',
			'woocommerce',
		] );

		$this->add_render_attribute( 'heading', 'class', [ 'advanced-linked-products__heading', 'mt-0', 'mb-10', 'heading-letter-spacing' ] );

		$col = $settings['slides_to_show'];
		$col_tablet = ! empty( $settings['slides_to_show_tablet'] ) ? $settings['slides_to_show_tablet'] : $col;
		$col_mobile = ! empty( $settings['slides_to_show_mobile'] ) ? $settings['slides_to_show_mobile'] : $col_tablet;

		$this->add_render_attribute( 'swiper', 'class', [ 'swiper', 'product-swiper--elementor' ] );
		$this->add_render_attribute( 'swiper', 'data-desktop', $col );
		$this->add_render_attribute( 'swiper', 'data-tablet', $col_tablet );
		$this->add_render_attribute( 'swiper', 'data-mobile', $col_mobile );

		if( empty( $product_ids ) ) {
			return;
		}

		$product_ids = $settings['posts_per_page'] > 0 ? array_slice( $product_ids, 0, $settings['posts_per_page'] ) : $product_ids;
		?>
			<div <?php echo $this->get_render_attribute_string( 'wrapper' ) ?>>
				<?php if( ! empty( $settings['heading'] ) ) : ?>
					<<?php echo esc_attr( $settings['title_size'] ); ?> <?php echo $this->get_render_attribute_string( 'heading' ); ?>>
						<?php echo wp_kses_post( $settings['heading'] ); ?>
					</<?php echo esc_attr( $settings['title_size'] ); ?>>
				<?php endif; ?>
				<div <?php echo $this->get_render_attribute_string( 'swiper' ) ?>>
					<ul class="products">
					<?php foreach ( $product_ids as $product_id ) : ?>
						<?php $linked_product = wc_get_product( $product_id );?>
						<li class="product">
							<div class="product-thumbnail position-relative rounded-product-image overflow-hidden">
								<a href="<?php echo esc_url( $linked_product->get_permalink() ); ?>" class="woocommerce-LoopProduct-link woocommerce-loop-product__link gz-ratio gz-ratio--product-image rounded-product-image gz-hover-effect product-thumbnails--fadein" aria-label="<?php echo esc_attr( $linked_product->get_name() ); ?>">
									<?php echo $linked_product->get_image();?>
									<?php $image_ids = $linked_product->get_gallery_image_ids(); ?>
									<?php 
										if ( ! empty( $image_ids ) ) {
											echo wp_get_attachment_image( $image_ids[0], 'woocommerce_thumbnail', false, array( 'class' => 'attachment-woocommerce_thumbnail size-woocommerce_thumbnail product-thumbnails--fadein-image' ) );
										}
									?>
									<div class="gz-lazy-load-image">
										<span class="gz-lazy-load-image__loader"></span>
									</div>
								</a>
								<?php do_action( 'glozin_advanced_linked_products_product_thumbnail', $linked_product ); ?>
							</div>
							<div class="product-summary mt-15 d-flex flex-column align-items-center text-center">
								<h2 class="woocommerce-loop-product__title my-0 fs-15">
									<a href="<?php echo esc_url( $linked_product->get_permalink() ); ?>" class="woocommerce-LoopProduct-link woocommerce-loop-product__link" aria-label="<?php echo esc_attr( $linked_product->get_name() ); ?>">
										<?php echo $linked_product->get_title();?>
									</a>
								</h2>
								<span class="price">
									<?php echo $linked_product->get_price_html(); ?>
								</span>
							</div>
						</li>
					<?php endforeach; ?>
					</ul>
					<?php echo $this->render_arrows(); ?>
					<?php echo $this->render_pagination(); ?>
				</div>
			</div>
		<?php
		wp_reset_postdata();
	}

	public function advanced_linked_product_ids( $settings ) {
		$product_ids = [];
        $args =  array(
			'type' => 'variable',
			'limit' => $settings['posts_per_page'],
			'orderby' => 'date',
			'order' => 'ASC',
		);

		$products = wc_get_products( $args );

		if( ! empty( $products ) ) {
			$product_ids = array( $products[0]->get_id() );
		}

		return $product_ids;

    }
}
