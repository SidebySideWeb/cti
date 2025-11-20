<?php
namespace Glozin\Addons\Elementor\Builder\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_Data_Tabs extends Widget_Base {
	use \Glozin\Addons\Elementor\Builder\Traits\Product_Id_Trait;

	public function get_name() {
		return 'glozin-product-data-tabs';
	}

	public function get_title() {
		return esc_html__( '[Glozin] Product Data Tabs', 'glozin-addons' );
	}

	public function get_icon() {
		return 'eicon-product-tabs';
	}

	public function get_categories() {
		return ['glozin-addons-product'];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'data', 'tabs', 'product' ];
	}

	public function get_script_depends() {
		return [
			'glozin-product-elementor-widgets'
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
			'section_tabs',
			[
				'label' => esc_html__( 'Product Data Tabs', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'product_tabs_layout',
			[
				'label' => esc_html__( 'Layout', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'   => esc_html__( 'Defaults', 'glozin-addons' ),
					'accordion' => esc_html__( 'Accordion', 'glozin-addons' ),
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'product_tabs_status',
			[
				'label' => esc_html__( 'Product Tabs Status', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'close',
				'options' => [
					'close' => esc_html__( 'Close all tabs', 'glozin-addons' ),
					'first' => esc_html__( 'Open first tab', 'glozin-addons' ),
				],
				'condition' => [
					'product_tabs_layout' => 'accordion'
				]
			]
		);

		$this->add_control(
			'product_tabs_reviews_display',
			[
				'label' => esc_html__( 'Show Reviews Tab', 'glozin-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => esc_html__( 'Show', 'glozin-addons' ),
				'label_off' => esc_html__( 'Hide', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'product_tabs_reviews_rating_display',
			[
				'label' => esc_html__( 'Reviews Rating Display', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'row',
				'options' => [
					'row' => esc_html__( 'Row', 'glozin-addons' ),
					'column' => esc_html__( 'Column', 'glozin-addons' ),
				],
				'condition' => [
					'product_tabs_reviews_display' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		$this->style_section();
	}

	public function style_section() {
		$this->default_style();
		$this->accordion_style();
	}

	public function default_style() {
		$this->start_controls_section(
			'section_default_style',
			[
				'label' => esc_html__( 'Default', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'default_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-tabs--default' => '--gz-border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'default_gap',
			[
				'label' => __( 'Gap', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					]
				],
				'default' => [],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-tabs--default .woocommerce-tabs .wc-tabs' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'default_heading',
			[
				'label' => esc_html__( 'Heading', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'default_heading_typography',
				'selector' => '{{WRAPPER}} .woocommerce-tabs--default .woocommerce-tabs .wc-tabs li a',
			]
		);

		$this->add_control(
			'default_heading_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-tabs--default .woocommerce-tabs .wc-tabs li a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'default_heading_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-tabs--default .woocommerce-tabs .wc-tabs li a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'default_heading_active_color',
			[
				'label' => esc_html__( 'Active Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-tabs--default .woocommerce-tabs .wc-tabs li.active a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'default_heading_active_border_color',
			[
				'label' => esc_html__( 'Active Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-tabs--default .woocommerce-tabs .wc-tabs li.active a::after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'default_heading_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-tabs--default .woocommerce-tabs .wc-tabs li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .woocommerce-tabs--default .woocommerce-tabs .wc-tabs li a' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_responsive_control(
			'default_heading_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-tabs--default .woocommerce-tabs .wc-tabs' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .woocommerce-tabs--default .woocommerce-tabs .wc-tabs' => 'margin: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'default_content_heading',
			[
				'label' => esc_html__( 'Content', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'default_content_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-tabs--default .woocommerce-tabs .wc-tab' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .woocommerce-tabs--default .woocommerce-tabs .wc-tab' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	public function accordion_style() {
		$this->start_controls_section(
			'section_accordion_style',
			[
				'label' => esc_html__( 'Accordion', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'accordion_spacing',
			[
				'label' => esc_html__( 'Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-tabs--accordion .woocommerce-tabs--dropdown:not(.last)' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'accordion_hide_border',
			[
				'label' => esc_html__( 'Hide Border', 'glozin-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => esc_html__( 'Hide', 'glozin-addons' ),
				'label_off' => esc_html__( 'Show', 'glozin-addons' ),
				'selectors' => [
					'{{WRAPPER}} .woocommerce-tabs--accordion .woocommerce-tabs--dropdown' => 'border: none;',
					'{{WRAPPER}} .woocommerce-tabs--accordion .woocommerce-tabs--dropdown:first-child' => 'border: none;',
				],
			]
		);

		$this->add_control(
			'accordion_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-tabs--accordion .woocommerce-tabs--dropdown' => '--gz-border-color: {{VALUE}};',
				],
				'condition' => [
					'accordion_hide_border' => '',
				]
			]
		);

		$this->add_control(
			'accordion_heading',
			[
				'label' => esc_html__( 'Heading', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'accordion_heading_typography',
				'selector' => '{{WRAPPER}} .woocommerce-tabs--dropdown .woocommerce-tabs-title',
			]
		);

		$this->add_control(
			'accordion_heading_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-tabs--dropdown .woocommerce-tabs-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .woocommerce-tabs--dropdown .woocommerce-tabs-title .woocommerce-tabs-title__icon::before' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .woocommerce-tabs--dropdown .woocommerce-tabs-title .woocommerce-tabs-title__icon::after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'accordion_heading_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-tabs--dropdown .woocommerce-tabs-title' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'accordion_heading_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-tabs--dropdown .woocommerce-tabs-title:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .woocommerce-tabs--dropdown .woocommerce-tabs-title:hover .woocommerce-tabs-title__icon::before' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .woocommerce-tabs--dropdown .woocommerce-tabs-title:hover .woocommerce-tabs-title__icon::after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'accordion_heading_hover_background_color',
			[
				'label' => esc_html__( 'Hover Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-tabs--dropdown .woocommerce-tabs-title:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'accordion_heading_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-tabs--accordion .woocommerce-tabs--dropdown .woocommerce-tabs-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .woocommerce-tabs--accordion .woocommerce-tabs--dropdown .woocommerce-tabs-title' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'accordion_heading_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-tabs--accordion .woocommerce-tabs--dropdown .woocommerce-tabs-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .woocommerce-tabs--accordion .woocommerce-tabs--dropdown .woocommerce-tabs-title' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'accordion_content_heading',
			[
				'label' => esc_html__( 'Content', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'accordion_content_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-tabs--accordion .woocommerce-tabs--dropdown .woocommerce-tabs-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .woocommerce-tabs--accordion .woocommerce-tabs--dropdown .woocommerce-tabs-content' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
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

		setup_postdata( $product->get_id() );

		if ( \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
			$original_post = $GLOBALS['post'];
			$GLOBALS['post'] = get_post( $product->get_id() );
			setup_postdata( $GLOBALS['post'] );
		}

		if( $settings['product_tabs_reviews_display'] !== 'yes' ) {
			add_filter( 'woocommerce_product_tabs', array( $this, 'unset_review_tab' ), 98 );
		}

		add_action( 'woocommerce_review_before', array( $this, 'review_before_open' ), 1 );
		add_action( 'woocommerce_review_before_comment_text', array( $this, 'review_before_close' ), 1 );

		if( ! empty( woocommerce_default_product_tabs() ) ) {
			echo '<div class="product woocommerce-tabs--'. esc_attr( $settings['product_tabs_layout'] ) . ' ' . ( $settings[ 'product_tabs_layout' ] == 'accordion' ? 'gz-product-tabs' : 'woocommerce-tabs' ) .'">';
		}

		if( $settings[ 'product_tabs_layout' ] == 'accordion' ) {
			$this->product_tabs();
		} else {
			wc_get_template( 'single-product/tabs/tabs.php' );
		}

		if( ! empty( woocommerce_default_product_tabs() ) ) {
			echo '</div>';
		}

		remove_action( 'woocommerce_review_before', array( $this, 'review_before_open' ), 1 );
		remove_action( 'woocommerce_review_before_comment_text', array( $this, 'review_before_close' ), 1 );

		if( $settings['product_tabs_reviews_display'] !== 'yes' ) {
			remove_filter( 'woocommerce_product_tabs', array( $this, 'unset_review_tab' ), 98 );
		}

		if ( \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
			$GLOBALS['post'] = $original_post;
			wp_reset_postdata();
			?>
			<script>
				jQuery( '.wc-tabs-wrapper, .woocommerce-tabs, #rating' ).trigger( 'init' );
			</script>
			<?php
		}
	}

	/**
	 * Show product tabs type dropdowm, list
	 *
	 * @return void
	 */
	public function product_tabs() {
		$settings = $this->get_settings_for_display();
		$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );

		if( empty( $product_tabs ) ) {
			return;
		}

		$type = 'dropdown';
		$arrKey = array_keys($product_tabs);
		$lastKey = end($arrKey);
		$i = 0;

		foreach( $product_tabs as $key => $product_tab ) :
			$firstKey = ( $i == 0 ) ? $key : '';
			$tab_class = $title_class = '';

			if ( $key == $firstKey && $settings[ 'product_tabs_status' ] == 'first' ) {
				$tab_class = 'wc-tabs-first--opened';
				$title_class = 'active';
			}

			$tab_class .= ( $key == $lastKey ) ? ' last' : '';
		?>
			<div id="tab-<?php echo esc_attr( $key ); ?>" class="woocommerce-tabs glozin-woocommerce-tabs woocommerce-tabs--<?php echo esc_attr( $type ); ?> woocommerce-tabs--<?php echo esc_attr( $key ); ?> <?php echo esc_attr($tab_class) ?> <?php echo $key == 'reviews' ? 'reviews-rating--' . esc_attr( $settings['product_tabs_reviews_rating_display'] ) : '' ?>">
				<div class="woocommerce-tabs-title <?php echo esc_attr($title_class); ?>"><?php echo esc_html( $product_tab['title'] ); ?><span class="woocommerce-tabs-title__icon"></span></div>
				<div class="woocommerce-tabs-content">
					<?php
					if ( isset( $product_tab['callback'] ) ) {
						call_user_func( $product_tab['callback'], $key, $product_tab );
					}
					?>
				</div>
			</div>
		<?php
		$i++;
		endforeach;
	}

	/**
	 * Unset review tab
	 *
	 * @return array
	 */
	public function unset_review_tab( $tabs ) {
		if( isset( $tabs[ 'reviews' ] ) ) {
			unset( $tabs[ 'reviews' ] );
		}

		return $tabs;
	}

	public function review_before_open() {
		echo '<div class="glozin-review-avatar-name d-flex flex-wrap align-items-center gap-10 mb-20">';
	}

	public function review_before_close() {
		echo '</div>';
	}
}
