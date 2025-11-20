<?php
namespace Glozin\Addons\Elementor\Builder\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_SKU extends Widget_Base {
	use \Glozin\Addons\Elementor\Builder\Traits\Product_Id_Trait;

	public function get_name() {
		return 'glozin-product-sku';
	}

	public function get_title() {
		return esc_html__( '[Glozin] Product SKU', 'glozin-addons' );
	}

	public function get_icon() {
		return 'eicon-product-meta';
	}

	public function get_categories() {
		return ['glozin-addons-product'];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'sku', 'product' ];
	}

	/**
	 * Get HTML wrapper class.
	 *
	 * Retrieve the widget container class. Can be used to override the
	 * container class for specific widgets.
	 *
	 * @since 2.0.9
	 * @access protected
	 */
	protected function get_html_wrapper_class() {
		return 'elementor-widget-' . $this->get_name() . ' entry-summary';
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
			'section_content',
			[ 'label' => __( 'Content', 'glozin-addons' ) ]
		);

		$this->add_control(
			'label',
			[
				'label' => __( 'Label', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'SKU:', 'glozin-addons' ),
				'label_block' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Style', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'min_width',
			[
				'label' => esc_html__( 'Min Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'vw', '%', 'em', 'rem'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'.glozin-woocommerce-elementor.single-product div.product {{WRAPPER}} .product_meta' => '--gz-min-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'label_heading',
			[
				'label' => esc_html__( 'Label', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} .product_meta .sku_wrapper .meta__label',
			]
		);

		$this->add_control(
			'label_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product_meta .sku_wrapper .meta__label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'sku_heading',
			[
				'label' => esc_html__( 'SKU', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sku_typography',
				'selector' => '{{WRAPPER}} .product_meta .sku_wrapper .sku',
			]
		);

		$this->add_control(
			'sku_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product_meta .sku_wrapper .sku' => 'color: {{VALUE}};',
				],
			]
		);
		
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
		$settings = $this->get_settings_for_display();

		global $product;

		$product = $this->get_product();

		if ( ! $product ) {
			return;
		}

		if ( ! \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
			if( ! wc_product_sku_enabled() ) {
				return;
			}
		}
		?>
		<div class="product_meta">
			<span class="sku_wrapper">
				<span class="meta__label"><?php echo ! empty( $settings['label'] ) ? $settings['label'] : esc_html_e( 'SKU:', 'glozin' ); ?></span>
				<span class="sku">
					<?php if ( $sku = $product->get_sku() ) {
						echo !empty( $sku ) ? $sku : '';
					} else {
						esc_html_e( 'N/A', 'glozin' );
					}   ?>
				</span>
			</span>
		</div>
		<?php
	}
}
