<?php

namespace Glozin\Addons\Elementor\Widgets;

use Elementor\Controls_Manager;
use Glozin\Addons\Elementor\Base\Products_Widget_Base;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Glozin\Addons\Helper;

use Glozin\Addons\Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Product Deals Carousel widget
 */
class Product_Deals_Carousel extends Products_Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-product-deals';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Glozin] Product Deals Carousel', 'glozin-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-countdown';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'glozin-addons' ];
	}

	public function get_script_depends() {
		return [
			 'glozin-products-carousel-widget',
			'imagesLoaded',
			'glozin-coundown',
			'glozin-countdown-widget'
		];
	}

	public function get_style_depends(): array {
		return [ 'glozin-elementor-css' ];
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
		$this->section_products_settings_controls();
		$this->section_carousel_settings_controls();
	}

	// Tab Style
	protected function section_style() {
		$this->section_content_style_controls();
		$this->section_carousel_style_controls();
	}

	protected function section_products_settings_controls() {
		$this->start_controls_section(
			'section_products',
			[ 'label' => esc_html__( 'Products Content', 'glozin-addons' ) ]
		);

		// SECTION: Basic Settings
		$this->add_control(
			'heading_basic',
			[
				'label' => esc_html__( 'Basic Settings', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter your title', 'glozin-addons' ),
				'default' => __( "Today's Best Deals", 'glozin-addons' ),
			]
		);

		// SECTION: Countdown Settings
		$this->add_control(
			'heading_countdown',
			[
				'label' => esc_html__( 'Countdown Settings', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'countdown_offer_text',
			[
				'label' => __( 'Countdown Offer Text', 'glozin-addons' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'Hurry up! Offer ends in:', 'glozin-addons' ),
			]
		);

		// SECTION: Product Display Settings
		$this->add_control(
			'heading_product_display',
			[
				'label' => esc_html__( 'Product Display Settings', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'type',
			[
				'label'     => esc_html__( 'Products Type', 'glozin-addons' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'sale_products' => esc_html__( 'Flash Sale Items', 'glozin-addons' ),
					'day'   => esc_html__( 'Daily Hot Deals (Ends at midnight)', 'glozin-addons' ),
					'week'  => esc_html__( 'Weekly Special Offers (Ends Sunday)', 'glozin-addons' ),
					'month' => esc_html__( 'Monthly Featured Deals (Ends this month)', 'glozin-addons' ),
					'recent_products' => esc_html__( 'New Arrivals', 'glozin-addons' ),
				],
				'default'   => 'sale_products',
				'toggle'    => false,
			]
		);

		// SECTION: Sale Timer Configuration
		$this->add_control(
			'heading_sale_timer',
			[
				'label' => esc_html__( 'Sale Timer Configuration', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'type' => 'sale_products',
				],
			]
		);

		$this->add_control(
			'sale_date_usage',
			[
				'label'       => esc_html__( 'Apply Sale End Date To', 'glozin-addons' ),
				'type'        => Controls_Manager::SELECT,
				'description' => esc_html__( 'Decide how the Sale End Date is used — for filtering products, showing countdown, or both.', 'glozin-addons' ),
				'options'     => [
					'filter_products' => esc_html__( 'Filter Products Only', 'glozin-addons' ),
					'countdown_only'  => esc_html__( 'Show Countdown Only', 'glozin-addons' ),
					'both'            => esc_html__( 'Filter & Countdown', 'glozin-addons' ),
				],
				'default' => 'both',
				'condition' => [
					'type' => 'sale_products',
				],
			]
		);

		$this->add_control(
			'sale_date_to',
			[
				'label'     => esc_html__( 'Sale End Date', 'glozin-addons' ),
				'type'      => Controls_Manager::DATE_TIME,
				'default'   => '',
				'condition' => [
					'type' => 'sale_products',
				],
			]
		);

		// SECTION: Product Query Settings
		$this->add_control(
			'heading_query',
			[
				'label' => esc_html__( 'Product Query Settings', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'limit',
			[
				'label'   => esc_html__( 'Total Products', 'glozin-addons' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 10,
				'min'     => 1,
				'max'     => 50,
				'step'    => 1,
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'     => esc_html__( 'Order By', 'glozin-addons' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'menu_order' => __( 'Menu Order', 'glozin-addons' ),
					'date'       => __( 'Date', 'glozin-addons' ),
					'title'      => __( 'Title', 'glozin-addons' ),
					'price'      => __( 'Price', 'glozin-addons' ),
				],
				'default'   => 'menu_order',
			]
		);

		$this->add_control(
			'order',
			[
				'label'     => esc_html__( 'Order', 'glozin-addons' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					''     => esc_html__( 'Default', 'glozin-addons' ),
					'asc'  => esc_html__( 'Ascending', 'glozin-addons' ),
					'desc' => esc_html__( 'Descending', 'glozin-addons' ),
				],
				'default'   => '',
			]
		);

		// SECTION: Product Filtering
		$this->add_control(
			'heading_filtering',
			[
				'label' => esc_html__( 'Product Filtering', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'product_cat',
			[
				'label'       => esc_html__( 'Product Categories', 'glozin-addons' ),
				'placeholder' => esc_html__( 'Click here and start typing...', 'glozin-addons' ),
				'type'        => 'glozin-autocomplete',
				'default'     => '',
				'label_block' => true,
				'multiple'    => true,
				'source'      => 'product_cat',
				'sortable'    => true,
			]
		);

		$this->add_control(
			'product_tag',
			[
				'label'       => esc_html__( 'Product Tags', 'glozin-addons' ),
				'placeholder' => esc_html__( 'Click here and start typing...', 'glozin-addons' ),
				'type'        => 'glozin-autocomplete',
				'default'     => '',
				'label_block' => true,
				'multiple'    => true,
				'source'      => 'product_tag',
				'sortable'    => true,
			]
		);

		$this->add_control(
			'product_brand',
			[
				'label'       => esc_html__( 'Product Brands', 'glozin-addons' ),
				'placeholder' => esc_html__( 'Click here and start typing...', 'glozin-addons' ),
				'type'        => 'glozin-autocomplete',
				'default'     => '',
				'label_block' => true,
				'multiple'    => true,
				'source'      => 'product_brand',
				'sortable'    => true,
			]
		);

		$this->end_controls_section();
	}

	protected function section_carousel_settings_controls() {
		$this->start_controls_section(
			'section_carousel_settings',
			[ 'label' => esc_html__( 'Carousel Settings', 'glozin-addons' ) ]
		);

		$controls = [
			'slides_rows'	   => '',
			'slides_to_show'   => 5,
			'slides_to_scroll' => 1,
			'custom_space_between' => '',
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

	protected function section_content_style_controls() {
		// Content Style
		$this->start_controls_section(
			'section_content_style',
			[
				'label' => esc_html__( 'Heading', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'heading_spacing',
			[
				'label' => __( 'Spacing', 'glozin-addons' ),
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
					'{{WRAPPER}} .glozin-product-deals__content' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'heading_title',
			[
				'label'     => esc_html__( 'Title', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator'  => 'before',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-product-deals__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .glozin-product-deals__title',
			]
		);

		$this->add_control(
			'heading_countdown_offer_text',
			[
				'label'     => esc_html__( 'Countdown Offer Text', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator'  => 'before',
			]
		);

		$this->add_control(
			'countdown_offer_text_color',
			[
				'label' => __( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-product-deals__countdown-offer-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'countdown_offer_text_typography',
				'selector' => '{{WRAPPER}} .glozin-product-deals__countdown-offer-text',
			]
		);

		$this->add_control(
			'heading_time',
			[
				'label'     => esc_html__( 'Time', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator'  => 'before',
			]
		);

		$this->add_control(
			'time_bg_color',
			[
				'label' => __( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-product-deals__countdown .glozin-countdown .digits' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'time_color',
			[
				'label' => __( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-product-deals__countdown .glozin-countdown .digits' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'time_typography',
				'selector' => '{{WRAPPER}} .glozin-product-deals__countdown .glozin-countdown .digits',
			]
		);

		$this->add_responsive_control(
			'time_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-product-deals__countdown .glozin-countdown .digits' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function section_carousel_style_controls() {
		$this->start_controls_section(
			'section_style_content',
			[
				'label' => esc_html__( 'Carousel Settings', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// Arrows
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

		$classes = [
			'glozin-product-deals-carousel',
			'glozin-carousel--elementor',
		];

		$col = $settings['slides_to_show'];
		$col_tablet = ! empty( $settings['slides_to_show_tablet'] ) ? $settings['slides_to_show_tablet'] : $col;
		$col_mobile = ! empty( $settings['slides_to_show_mobile'] ) ? $settings['slides_to_show_mobile'] : $col_tablet;

		$this->add_render_attribute( 'wrapper', 'class', $classes );
		$this->add_render_attribute( 'swiper', 'class', [ 'glozin-product-deals__products', 'swiper', 'product-swiper--elementor' ] );
		$this->add_render_attribute( 'swiper', 'data-desktop', $col );
		$this->add_render_attribute( 'swiper', 'data-tablet', $col_tablet );
		$this->add_render_attribute( 'swiper', 'data-mobile', $col_mobile );
		$this->add_render_attribute( 'swiper', 'style', $this->render_space_between_style() );
		$this->render_slidesperview_auto_class_style( 'swiper' );

		$product_html = self::render_products();

		if ( empty( $product_html ) ) {
			return;
		}

		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) .'>';

		$now         = strtotime( current_time( 'Y-m-d H:i:s' ) );
		$expire_date = 0;
		if ( $settings['type'] == 'day' ) {
			$expire_date = strtotime( '00:00 +1 day', $now );
		} elseif ( $settings['type'] == 'week' ) {
			$expire_date = strtotime( '00:00 next monday', $now );
		} elseif ( $settings['type'] == 'month' ) {
			$expire_date = strtotime( 'last day of this month 23:59:59', $now );
		} elseif ( $settings['type'] == 'sale_products' ) {
			if ( in_array( $settings['sale_date_usage'], ['countdown_only', 'both'] ) ) {
				$expire_date = strtotime( $settings['sale_date_to'] );
			}
		}
		$expire            =  $expire_date ? $expire_date - $now : '';

		$this->add_render_attribute( 'countdown', 'data-expire', $expire );
		$this->add_render_attribute( 'countdown', 'data-text', wp_json_encode( Helper::get_countdown_shorten_texts() ) );

		$countdown_offer_text = ! empty( $settings['countdown_offer_text'] ) ? '<div class="glozin-product-deals__countdown-offer-text fw-semibold text-dark">' . $settings['countdown_offer_text'] . '</div>' : '';

		$countdown = $expire ? sprintf( '<div class="glozin-product-deals__countdown d-flex align-items-md-center flex-column flex-md-row gap-15">' . $countdown_offer_text . '<div class="glozin-countdown" %s></div></div>', $this->get_render_attribute_string( 'countdown' ) ) : '' ;


		if ( ! empty( $settings['title'] ) || ! empty( $countdown ) ||  ! empty( $settings['primary_button_text'] ) ){
			?>
			<div class="glozin-product-deals__content d-flex justify-content-between align-items-md-center flex-column flex-md-row gap-15 mb-35">
				<?php echo ! empty( $settings['title'] ) ? '<h3 class="glozin-product-deals__title heading-letter-spacing my-0">' . $settings['title'] . '</h3>' : ''; ?>
				<div class="glozin-product-deals__group-heading">
					<?php echo $countdown; ?>
				</div>
			</div>
			<?php
		}

		echo '<div '. $this->get_render_attribute_string( 'swiper' ) .'>';
		echo $product_html;
		echo $this->render_arrows();
		echo $this->render_pagination();
		echo '</div>';
		echo '</div>';
	}

}