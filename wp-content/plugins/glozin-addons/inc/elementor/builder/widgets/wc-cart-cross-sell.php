<?php
namespace Glozin\Addons\Elementor\Builder\Widgets;

use Elementor\Controls_Manager;
use Glozin\Addons\Elementor\Base\Products_Widget_Base;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Woocommerce Cart Cross Sell widget
 */
class WC_Cart_Cross_Sell extends Products_Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-wc-cart-cross-sell';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( '[Glozin] Cart Cross Sell', 'glozin-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-woo-cart';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'glozin-addons-cart-page' ];
	}

	/**
	 * Get widget keywords.
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'woocommerce', 'cart', 'cross', 'sell', 'page', 'glozin-addons' ];
	}

	public function get_script_depends() {
		return [
			'glozin-product-elementor-widgets'
		];
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
		return 'woocommerce-cart woocommerce-page elementor-widget-' . $this->get_name();
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
			'section_products',
			[ 'label' => esc_html__( 'Products', 'glozin-addons' ) ]
		);

		$this->add_control(
			'heading',
			[
				'label' => __( 'Heading', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter your heading', 'glozin-addons' ),
				'label_block' => true,
			]
		);

		$this->register_products_controls( [
			'limit'    				=> 10,
			'orderby'  				=> '',
			'order'    				=> '',
		], true );

		$this->add_control(
			'cross_sells_heading',
			[
				'label' => esc_html__( 'Displayed when empty', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->register_products_controls( [
			'type'     				=> 'recent_products',
		], true );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_carousel',
			[ 'label' => esc_html__( 'Carousel Settings', 'glozin-addons' ) ]
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
		];

		$this->register_carousel_controls( $controls );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label'     => __( 'Content', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'heading_text_align',
			[
				'label'       => esc_html__( 'Alignment', 'glozin-addons' ),
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
					'{{WRAPPER}} .cross-sells__heading' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'heading_typography',
				'selector' => '{{WRAPPER}} .cross-sells__heading',
			]
		);

        $this->add_control(
			'heading_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .cross-sells__heading' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'heading_spacing',
			[
				'label'      => esc_html__( 'Heading Spacing', 'glozin-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 1900,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .cross-sells__heading' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_carousel_style',
			[
				'label'     => __( 'Carousel Style', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
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
		if( empty( WC()->cart ) ) {
			return;
		}

		$settings = $this->get_settings_for_display();

		$limit = '5';
		$columns = 5;
		$orderby = 'rand';
		$order = 'desc';

		if ( ! empty( $settings['limit'] ) ) {
			$limit = $settings['limit'];
		}

		if ( ! empty( $settings['slides_to_show'] ) ) {
			$columns = $settings['slides_to_show'];
		}

		if ( ! empty( $settings['orderby'] ) ) {
			$orderby = $settings['orderby'];
		}

		if ( ! empty( $settings['order'] ) ) {
			$order = $settings['order'];
		}

		add_filter( 'woocommerce_product_cross_sells_products_heading', '__return_false' );
		$heading = ! empty( $settings['heading'] ) ? sprintf( '<h2 class="cross-sells__heading">%s</h2>', $settings['heading'] ) : '';

		$this->add_render_attribute( 'wrapper', 'class', [
			'cross-sells-product__carousel',
			'glozin-products-carousel--elementor',
			'glozin-carousel--elementor',
			'gz-relative'
		] );

		ob_start();
		woocommerce_cross_sell_display(
			sanitize_text_field( $limit ),
			sanitize_text_field( $columns ),
			sanitize_text_field( $orderby ),
			sanitize_text_field( $order )
		);

		$cross_sells_html = ob_get_clean();

		if( empty( $cross_sells_html ) ) {
			$cross_sells_html = '<div class="cross-sells">' . $this->render_products( array( 'type' => 'recent_products', 'limit' => $limit, 'columns' => $columns, 'orderby' => $orderby, 'order' => $order ) ) .'</div>';
		}

		?>
		<?php echo $heading; ?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ) ?>>
			<?php echo $cross_sells_html; ?>
			<?php echo $this->render_arrows(); ?>
			<?php echo $this->render_pagination(); ?>
		</div>
		<?php
	}
}