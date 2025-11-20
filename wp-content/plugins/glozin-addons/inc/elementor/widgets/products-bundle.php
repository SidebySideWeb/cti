<?php
namespace Glozin\Addons\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Glozin\Addons\Elementor\Base\Products_Widget_Base;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Products Bundle
 */
class Products_Bundle extends Products_Widget_Base {
	use \Glozin\Addons\Elementor\Base\Button_Base;

	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-products-bundle';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Glozin] Products Bundle', 'glozin-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-product-upsell';
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
		return [ 'products bundle', 'products', 'bundle', 'woocommerce', 'glozin-addons' ];
	}

	public function get_script_depends() {
		return [
			'glozin-products-carousel-widget'
		];
	}

	public function get_style_depends() {
		return [
			'glozin-products-carousel-css'
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

	// Tab Content
	protected function section_content() {
		$this->section_content_products();
		$this->section_bundle_settings();
	}

	protected function section_content_products() {
		$this->start_controls_section(
			'section_products',
			[
				'label' => __( 'Products', 'glozin-addons' ),
			]
		);

		$this->add_responsive_control (
			'columns',
			[
				'label'     => esc_html__( 'Columns', 'glozin-addons' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'1' => esc_html__( '1', 'glozin-addons' ),
					'2' => esc_html__( '2', 'glozin-addons' ),
					'3' => esc_html__( '3', 'glozin-addons' ),
					'4' => esc_html__( '4', 'glozin-addons' ),
					'5' => esc_html__( '5', 'glozin-addons' ),
					'6' => esc_html__( '6', 'glozin-addons' ),
				],
				'default'   => '3',
				'frontend_available' => true,
			]
		);

		$this->register_products_controls( 'all', true );

		$this->add_control(
			'hide_rating',
			[
				'label'     => esc_html__( 'Hide Rating', 'glozin-addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Show', 'glozin-addons' ),
				'label_on'  => __( 'Hide', 'glozin-addons' ),
				'return_value' => 'none',
				'default'      => '',
				'selectors' => [
					'{{WRAPPER}} ul.products li.product .glozin-rating' => 'display: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'hide_attributes',
			[
				'label'     => esc_html__( 'Hide Attributes', 'glozin-addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Show', 'glozin-addons' ),
				'label_on'  => __( 'Hide', 'glozin-addons' ),
				'return_value' => 'none',
				'default'      => '',
				'selectors' => [
					'{{WRAPPER}} ul.products li.product .product-variation-items' => 'display: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function section_bundle_settings() {
		$this->start_controls_section(
			'section_bundle_settings',
			[
				'label' => __( 'Bundle Settings', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'bundle_title',
			[
				'label'       => esc_html__( 'Title', 'glozin-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Bundle Contents', 'glozin-addons' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'bundle_description',
			[
				'label'       => esc_html__( 'Description', 'glozin-addons' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Add at least 3 products and Save 30%.', 'glozin-addons' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'bundle_min',
			[
				'label'     => __( 'Min items discount', 'glozin-addons' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 2,
				'max'       => 100,
				'step'      => 1,
				'default'   => 3,
				'frontend_available' => true
			]
		);

		$this->add_control(
			'bundle_max',
			[
				'label'     => __( 'Max items discount', 'glozin-addons' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 3,
				'max'       => 100,
				'step'      => 1,
				'frontend_available' => true
			]
		);

		$this->add_control(
			'bundle_discount',
			[
				'label'     => __( 'Discount (%)', 'glozin-addons' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 100,
				'step'      => 1,
				'frontend_available' => true
			]
		);

		$this->end_controls_section();
	}

	// Tab Content
	protected function section_style() {
		$this->start_controls_section(
			'section_products_style',
			[
				'label'     => __( 'Products', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'product_columns_spacing',
			[
				'label'        => esc_html__( 'Columns Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default' => [
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} ul.products li.product' => 'padding-inline-start: calc( {{SIZE}}{{UNIT}} / 2);padding-inline-end: calc( {{SIZE}}{{UNIT}} / 2);',
					'{{WRAPPER}} ul.products' => 'margin-inline-start: calc( ( {{SIZE}}{{UNIT}} / 2 ) * -1 );margin-inline-end: calc( ( {{SIZE}}{{UNIT}} / 2 ) * -1 );',
				],
			]
		);
		$this->add_responsive_control(
			'product_rows_spacing',
			[
				'label'        => esc_html__( 'Rows Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default' => [
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} ul.products' => 'row-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'product_heading',
			[
				'label' => esc_html__( 'Product', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'product_item_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} ul.products li.product .product-inner' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'product_item_border',
				'label' => esc_html__( 'Border', 'glozin-addons' ),
				'selector' => '{{WRAPPER}} ul.products li.product .product-inner',
			]
		);

		$this->add_responsive_control(
			'product_item_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} ul.products li.product .product-inner' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'product_item_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} ul.products li.product .product-inner' => 'border-start-start-radius: {{TOP}}{{UNIT}}; border-start-end-radius: {{RIGHT}}{{UNIT}}; border-end-end-radius: {{BOTTOM}}{{UNIT}}; border-end-start-radius: {{LEFT}}{{UNIT}};',
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
					ul.products li.product .product-variation-items .product-variation-item.selected' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} ul.products li.product .product-variation-items .product-variation-item' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$settings['exclude_grouped'] = true;
		$settings['exclude_external'] = true;

		$this->add_render_attribute( 'wrapper', 'class', [
			'glozin-products-bundle',
			'd-flex-xl',
			'gap-xl-40'
		] );

		$this->add_render_attribute( 'products', 'class', [ 'glozin-products-bundle__products', 'glozin-product-grid', 'column-xl-custom-remaining', ! empty( $settings['columns'] ) ? 'glozin-product-columns--' . $settings['columns'] : '', ! empty( $settings['columns_tablet'] ) ? 'glozin-product-columns-tablet--' . $settings['columns_tablet'] : '', ! empty( $settings['columns_mobile'] ) ? 'glozin-product-columns-mobile--' . $settings['columns_mobile'] : '' ] );

		$this->add_render_attribute( 'bundle__sidebar', 'class', [ 'glozin-products-bundle__sidebar', 'column-xl-custom', 'd-flex', 'flex-column', 'position-sticky', 'bottom-0', 'bottom-auto-xl', 'top-90-xl', 'px-xl-25', 'py-xl-30', 'px-20', 'py-20', 'z-4' ] );
		$this->add_render_attribute( 'bundle_header', 'class', [ 'glozin-products-bundle__sidebar-header' ] );
		$this->add_render_attribute( 'bundle_title', 'class', [ 'glozin-products-bundle__sidebar-title', 'mt-0', 'mb-3', 'fs-24', 'heading-letter-spacing', 'position-relative' ] );
		$this->add_render_attribute( 'bundle_description', 'class', [ 'glozin-products-bundle__sidebar-description', 'mb-20' ] );
		$this->add_render_attribute( 'bundle_progressbar', 'class', [ 'glozin-products-bundle__progressbar', 'position-relative', 'rounded-10', 'overflow-hidden' ] );
		$this->add_render_attribute( 'bundle_products', 'class', [ 'glozin-products-bundle__sidebar-products', 'woocommerce', 'd-none', 'd-block-xl', 'mt-30' ] );
		$this->add_render_attribute( 'bundle_subtotal', 'class', [ 'glozin-products-bundle__sidebar-subtotal', 'd-flex', 'align-items-center', 'justify-content-between', 'pt-20', 'mt-20', 'mt-xl-30', 'border-top' ] );
		$this->add_render_attribute( 'bundle_subtotal_text', 'class', [ 'glozin-products-bundle__sidebar-subtotal-text', 'my-0' ] );
		$this->add_render_attribute( 'bundle_subtotal_price', 'class', [ 'glozin-products-bundle__sidebar-subtotal-price', 'fs-18', 'text-dark', 'fw-semibold' ] );
		$this->add_render_attribute( 'bundle_button', 'class', [ 'glozin-products-bundle__sidebar-button' ] );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ) ?>>
			<div <?php echo $this->get_render_attribute_string( 'products' ) ?>>
				<?php
					add_filter( 'glozin_product_loop_primary_attribute', '__return_false' );
					add_action( 'woocommerce_after_shop_loop_item', array( $this, 'add_to_cart_bundle_form' ), 30 );
					printf( '%s', self::render_products($settings) );
					remove_action( 'woocommerce_after_shop_loop_item', array( $this, 'add_to_cart_bundle_form' ), 30 );
					add_filter( 'glozin_product_loop_primary_attribute', '__return_true' );
				?>
			</div>
			<div <?php echo $this->get_render_attribute_string( 'bundle__sidebar' ) ?>>
				<form class="cart grouped_form add-to-cart-bundle" method="post" enctype='multipart/form-data'>
					<div <?php echo $this->get_render_attribute_string( 'bundle_header' ) ?>>
						<?php if( ! empty( $settings['bundle_title'] ) ) : ?>
							<h4 <?php echo $this->get_render_attribute_string( 'bundle_title' ) ?>>
								<?php echo wp_kses_post( $settings['bundle_title'] ); ?>
								<span class="glozin-bundle__toggle position-absolute top-50 end-0 translate-middle-y d-inline-flex d-none-xl">
									<span class="w-100 h-100 d-inline-flex align-items-center justify-content-end position-relative"></span>
								</span>
							</h4>
						<?php endif; ?>
						<?php if( ! empty( $settings['bundle_description'] ) ) : ?>
							<div <?php echo $this->get_render_attribute_string( 'bundle_description' ) ?>>
								<?php echo wp_kses_post( $settings['bundle_description'] ); ?>
							</div>
						<?php endif; ?>
						<div <?php echo $this->get_render_attribute_string( 'bundle_progressbar' ) ?>></div>
					</div>
					<div <?php echo $this->get_render_attribute_string( 'bundle_products' ) ?>>
						<?php $this->products_loading($settings); ?>
					</div>
					<div <?php echo $this->get_render_attribute_string( 'bundle_subtotal' ) ?>>
						<h5 <?php echo $this->get_render_attribute_string( 'bundle_subtotal_text' ) ?>><?php esc_html_e( 'Subtotal', 'glozin-addons' ); ?></h5>
						<span <?php echo $this->get_render_attribute_string( 'bundle_subtotal_price' ) ?>>
							<?php echo wc_price(0); ?>
						</span>
					</div>
					<div <?php echo $this->get_render_attribute_string( 'bundle_button' ) ?>>
						<button class="glozin-add-to-cart-bundle align-items-center justify-content-center gz-button gz-button-no-icon w-100 mt-20 disabled">
							<span class="add-to-cart__text"><?php esc_html_e( 'Add all to Cart', 'glozin-addons' ); ?></span>
						</button>
					</div>
				</form>
			</div>
		</div>
		<?php
	}

	public function add_to_cart_bundle_form() {
		$product = wc_get_product(get_the_ID());
		if( $product->is_type( 'variable') ) {
			$available_variations = $product->get_available_variations();
			$attributes = $product->get_variation_attributes();
			$selected_attributes = $product->get_default_attributes();
			$variations_json = wp_json_encode( $available_variations );
			$variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );

		?>
			<form class="variations_form cart product_variabel_bundle_form w-100 " action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo $variations_attr; // WPCS: XSS ok. ?>">
				<div class="variations">
					<?php foreach ( $attributes as $attribute_name => $options ) : ?>
					<?php
						wc_dropdown_variation_attribute_options(
							array(
								'options'   => $options,
								'attribute' => $attribute_name,
								'product'   => $product,
								'selected'  => ! empty( $selected_attributes[sanitize_title( $attribute_name )] ) ? $selected_attributes[sanitize_title( $attribute_name )] : '',
							)
						);
					?>
					<?php endforeach; ?>
				</div>
				<?php self::add_to_cart_button_bundle($product); ?>
			</form>
		<?php
		} else {
			self::add_to_cart_button_bundle($product);
		}
	}

	public function add_to_cart_button_bundle($product) {
	?>
		<button class="glozin-add-to-bundle align-items-center justify-content-center gz-button gz-button-no-icon w-100 mt-15" data-product_id="<?php echo get_the_ID(); ?>" data-product_type="<?php echo esc_attr( $product->get_type() ); ?>" data-text="<?php esc_attr_e( 'Add to Bundle', 'glozin-addons' ); ?>" data-text_added="<?php esc_attr_e( 'Added to Bundle', 'glozin-addons'); ?>">
			<?php esc_html_e( 'Add to Bundle', 'glozin-addons' ); ?>
		</button>
	<?php
	}

	public function products_loading($settings) {
		$limit = ! empty( $settings['bundle_max'] ) ? $settings['bundle_max'] : $settings['bundle_min'];

		for($i=0; $i < $limit; $i++) : ?>
			<div class="product-bundle__loading d-flex gap-10 mb-15 last-0">
				<div class="product-loading__thumbnail bg-bundle column-custom rounded-100"></div>
				<div class="product-loading__summary column-custom-remaining d-flex flex-column gap-10 justify-content-center">
					<div class="product-loading__text bg-bundle h-10 w-100 text-1 rounded-10"></div>
					<div class="product-loading__text bg-bundle h-10 w-100 text-2 rounded-10"></div>
					<div class="product-loading__text bg-bundle h-10 w-100 text-3 rounded-10"></div>
				</div>
			</div>
		<?php endfor;
	}
}