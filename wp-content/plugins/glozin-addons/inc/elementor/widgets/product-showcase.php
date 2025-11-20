<?php

namespace Glozin\Addons\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Featured Product widget
 */
class Product_Showcase extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-product-showcase';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Glozin] Product Showcase', 'glozin-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-single-product';
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
            'imagesLoaded',
			'flexslider',
			'wc-add-to-cart-variation',
			'glozin-countdown-widget',
			'glozin-products-carousel-widget',
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
			'glozin-countdown-css'
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
		$this->start_controls_section(
			'section_products',
			[ 'label' => esc_html__( 'Products', 'glozin-addons' ) ]
		);

		$this->add_control(
			'product_id',
			[
				'label' => __( 'Products', 'glozin-addons' ),
				'type' => 'glozin-autocomplete',
				'placeholder' => esc_html__( 'Click here and start typing...', 'glozin-addons' ),
				'default' => '',
				'multiple'    => false,
				'source'      => 'product',
				'sortable'    => true,
				'label_block' => true,
			]
		);

		$this->add_control(
			'hide_wishlist_button',
			[
				'label'     => esc_html__( 'Hide Wishlist Button', 'glozin-addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Show', 'glozin-addons' ),
				'label_on'  => __( 'Hide', 'glozin-addons' ),
				'default'	=> '',
				'return_value' => 'none',
				'selectors' => [
					'{{WRAPPER}} .glozin-product-atc-group .wcboost-wishlist-button' => 'display: {{VALUE}} !important;',
				],
				'prefix_class' => 'glozin-product-showcase-wishlist-button--'
			]
		);

		$this->add_control(
			'hide_compare_button',
			[
				'label'     => esc_html__( 'Hide Compare Button', 'glozin-addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Show', 'glozin-addons' ),
				'label_on'  => __( 'Hide', 'glozin-addons' ),
				'default'	=> '',
				'return_value' => 'none',
				'selectors' => [
					'{{WRAPPER}} .glozin-product-atc-group .wcboost-products-compare-button' => 'display: {{VALUE}} !important;',
				],
				'prefix_class' => 'glozin-product-showcase-compare-button--'
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Style', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-product-gallery' => 'border-start-start-radius: {{TOP}}{{UNIT}}; border-start-end-radius: {{RIGHT}}{{UNIT}}; border-end-end-radius: {{BOTTOM}}{{UNIT}}; border-end-start-radius: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .woocommerce-product-gallery .woocommerce-product-gallery__image' => 'border-start-start-radius: {{TOP}}{{UNIT}}; border-start-end-radius: {{RIGHT}}{{UNIT}}; border-end-end-radius: {{BOTTOM}}{{UNIT}}; border-end-start-radius: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .entry-summary' => 'border-start-start-radius: {{TOP}}{{UNIT}}; border-start-end-radius: {{RIGHT}}{{UNIT}}; border-end-end-radius: {{BOTTOM}}{{UNIT}}; border-end-start-radius: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'gap',
			[
				'label'        => esc_html__( 'Gap', 'glozin-addons' ),
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
					'{{WRAPPER}} .product-gallery-summary' => 'gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .entry-summary' => 'width: calc(50% - {{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'gallery_heading',
			[
				'label' => esc_html__( 'Gallery', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'gallery_layout',
			[
				'label' => __( 'Gallery Layout', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default'           => esc_html__( 'Default', 'glozin' ),
					''                  => esc_html__( 'Left thumbnails', 'glozin' ),
					'bottom-thumbnails' => esc_html__( 'Bottom thumbnails', 'glozin' ),
					'grid-1'            => esc_html__( 'Grid 1', 'glozin' ),
					'grid-2'            => esc_html__( 'Grid 2', 'glozin' ),
					'stacked'           => esc_html__( 'Stacked', 'glozin' ),
					'hidden-thumbnails' => esc_html__( 'Hidden thumbnails', 'glozin' ),
				],
				'default' => 'default',
			]
		);

		$this->add_responsive_control(
			'gallery_width',
			[
				'label'        => esc_html__( 'Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-product-gallery' => 'width: {{SIZE}}{{UNIT}};',
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

		$this->add_responsive_control(
			'summary_horizontal_position',
			[
				'label'                => esc_html__( 'Horizontal Position', 'glozin-addons' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'options'              => [
					'left'   => [
						'title' => esc_html__( 'Left', 'glozin-addons' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'glozin-addons' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'glozin-addons' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'     => '',
				'selectors'            => [
					'{{WRAPPER}} .entry-summary' => 'justify-content: {{VALUE}}',
				],
				'selectors_dictionary' => [
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				],
			]
		);

		$this->add_responsive_control(
			'summary_vertical_position',
			[
				'label'                => esc_html__( 'Vertical Position', 'glozin-addons' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'options'              => [
					'top'   => [
						'title' => esc_html__( 'Top', 'glozin-addons' ),
						'icon'  => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => esc_html__( 'Middle', 'glozin-addons' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom'  => [
						'title' => esc_html__( 'Bottom', 'glozin-addons' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'     => '',
				'selectors'            => [
					'{{WRAPPER}} .entry-summary' => 'align-items: {{VALUE}}',
				],
				'prefix_class' => 'glozin-product-showcase__summary-vertical summary-vertical--',
				'selectors_dictionary' => [
					'top'   => 'flex-start',
					'middle' => 'center',
					'bottom'  => 'flex-end',
				],
			]
		);

		$this->add_responsive_control(
			'summary_text_align',
			[
				'label'       => esc_html__( 'Text Align', 'glozin-addons' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'start'   => [
						'title' => esc_html__( 'Left', 'glozin-addons' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'glozin-addons' ),
						'icon'  => 'eicon-text-align-center',
					],
					'end'  => [
						'title' => esc_html__( 'Right', 'glozin-addons' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'     => '',
				'selectors'   => [
					'{{WRAPPER}} .entry-summary' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'summary_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .entry-summary' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .entry-summary' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'summary_width',
			[
				'label'        => esc_html__( 'Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .entry-summary' => 'width: calc(50% - {{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'buy_now_button_heading',
			[
				'label' => esc_html__( 'Buy Now Button', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->start_controls_tabs( 'buy_now_button_style' );

		$this->start_controls_tab(
			'buy_now_button_normal',
			[
				'label' => __( 'Normal', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'buy_now_button_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gz-buy-now-button' => '--gz-button-bg-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'buy_now_button_text_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .gz-buy-now-button' => '--gz-button-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'buy_now_button_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .gz-buy-now-button' => '--gz-button-border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'buy_now_button_hover',
			[
				'label' => __( 'Hover', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'buy_now_button_background_hover_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gz-buy-now-button' => '--gz-button-bg-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'buy_now_button_hover_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gz-buy-now-button' => '--gz-button-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'buy_now_button_hover_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gz-buy-now-button' => '--gz-button-border-color-hover: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render icon box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
        $settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', [ 'glozin-product-showcase', 'single-product', 'woocommerce' ] );

        if( empty( $settings['product_id'] ) ) {
            return;
        }

        $product = wc_get_product( intval( $settings['product_id'] ) );
	?>
        <div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
            <?php
			if(  class_exists('\Elementor\Plugin') && \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
				if( class_exists('\WCBoost\ProductsCompare\Frontend') ) {
					add_filter( 'wcboost_products_compare_single_add_to_compare_link', array( $this, 'single_add_to_compare_link' ), 20, 2 );
					add_action( 'woocommerce_after_add_to_cart_button', array( \WCBoost\ProductsCompare\Frontend::instance(), 'single_add_to_compare_button' ), 21 );
				}

				if(class_exists('\WCBoost\Wishlist\Frontend') ) {
					add_filter( 'wcboost_wishlist_single_add_to_wishlist_link', array( $this, 'wishlist_button_single_product' ), 20, 2 );
					add_action( 'woocommerce_after_add_to_cart_button', array( \WCBoost\Wishlist\Frontend::instance(), 'single_add_to_wishlist_button' ), 21 );
				}
			}
            if( empty( $product ) ) {
                echo '<p>'. esc_html__( 'No products were found matching your selection.', 'glozin-addons' ) .'</p>';
            } else {
				add_filter( 'glozin_gallery_layout', [ $this, 'gallery_layout' ] );
                $original_post = $GLOBALS['post'];

                $GLOBALS['post'] = get_post( intval( $settings['product_id'] ) );
                setup_postdata( $GLOBALS['post'] );
				wc_get_template_part( 'content', 'single-product-summary' );
				$GLOBALS['post'] = $original_post;
                wp_reset_postdata();
				remove_filter( 'glozin_gallery_layout', [ $this, 'gallery_layout' ] );
            }
			if(  class_exists('\Elementor\Plugin') && \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
				if( class_exists('\WCBoost\ProductsCompare\Frontend') ) {
					remove_filter( 'wcboost_products_compare_single_add_to_compare_link', array( $this, 'single_add_to_compare_link' ), 20, 2 );
					remove_action( 'woocommerce_after_add_to_cart_button', array( \WCBoost\ProductsCompare\Frontend::instance(), 'single_add_to_compare_button' ), 21 );
				}
				
				if(class_exists('\WCBoost\Wishlist\Frontend') ) {
					remove_filter( 'wcboost_wishlist_single_add_to_wishlist_link', array( $this, 'wishlist_button_single_product' ), 20, 2 );
					remove_action( 'woocommerce_after_add_to_cart_button', array( \WCBoost\Wishlist\Frontend::instance(), 'single_add_to_wishlist_button' ), 21 );
				}
			}
            ?>
        </div>
    <?php
	}

	public function gallery_layout( $layout ) {
		$settings = $this->get_settings_for_display();

		if( $settings['gallery_layout'] !== 'default' ) {
			$layout = $settings['gallery_layout'];
		}

		return $layout;
	}

	/**
	 * Change wishlist button single product
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function wishlist_button_single_product( $html, $args ) {
		$label_add = \WCBoost\Wishlist\Helper::get_button_text( 'add' );
		$label_added = \WCBoost\Wishlist\Helper::get_button_text( 'remove' );

		if( get_option( 'wcboost_wishlist_exists_item_button_behaviour', 'view_wishlist' ) == 'view_wishlist' ) {
			$label_added = \WCBoost\Wishlist\Helper::get_button_text( 'view' );
		}

		return sprintf(
			'<a href="%s" class="gz-button-icon gz-button-outline gz-tooltip-inside %s" %s data-product_title="%s" data-tooltip="%s" data-tooltip_added="%s">' .
				( ! empty( $args['icon'] ) ? '<span class="wcboost-wishlist-button__icon">' . $args['icon'] . '</span>' : '' ) .
				'<span class="wcboost-wishlist-button__text">%s</span>' .
			'</a>',
			esc_url( isset( $args['url'] ) ? $args['url'] : add_query_arg( [ 'add-to-wishlist' => $args['product_id'] ] ) ),
			esc_attr( isset( $args['class'] ) ? $args['class'] : 'wcboost-wishlist-single-button wcboost-wishlist-button button' ),
			isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
			esc_attr( isset( $args['product_title'] ) ? $args['product_title'] : wc_get_product( $args['product_id'] )->get_title() ),
			wp_kses_post( $label_add ),
			wp_kses_post( $label_added ),
			isset( $args['label'] ) ? esc_html( $args['label'] ) : esc_html__( 'Add to wishlist', 'glozin' )
		);
	}

	public function single_add_to_compare_link($html, $args) {
		$product_title = isset( $args['product_title'] ) ? $args['product_title'] : '';
		if( empty( $product_title ) ) {
			$product = isset($args['product_id']) ? wc_get_product( $args['product_id'] ) : '';
			$product_title = $product ? $product->get_title() : '';
		}

		$label_add = \WCBoost\ProductsCompare\Helper::get_button_text( 'add' );
		$label_added = \WCBoost\ProductsCompare\Helper::get_button_text( 'view' );

		if( get_option( 'wcboost_products_compare_exists_item_button_behaviour', 'remove' ) == 'remove' ) {
			$label_added = \WCBoost\ProductsCompare\Helper::get_button_text( 'remove' );
		}
		
		return sprintf(
			'<a href="%s" class="gz-button-icon gz-button-outline gz-tooltip-inside wcboost-products-compare-button wcboost-products-compare-button--single button" role="button" %s data-product_title="%s" data-tooltip="%s" data-tooltip_added="%s">
				%s
				<span class="wcboost-products-compare-button__text">%s</span>
			</a>',
			esc_url( isset( $args['url'] ) ? $args['url'] : add_query_arg( [ 'add-to-compare' => $args['product_id'] ] ) ),
			isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
			esc_attr( $product_title ),
			wp_kses_post( $label_add ),
			wp_kses_post( $label_added ),
			empty( $args['icon'] ) ? '' : '<span class="wcboost-products-compare-button__icon">' . $args['icon'] . '</span>',
			esc_html( isset( $args['label'] ) ? $args['label'] : __( 'Compare', 'glozin' ) )
		);
	}
}