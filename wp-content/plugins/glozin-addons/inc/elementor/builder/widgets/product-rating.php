<?php
namespace Glozin\Addons\Elementor\Builder\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_Rating extends Widget_Base {
	use \Glozin\Addons\Elementor\Builder\Traits\Product_Id_Trait;

	public function get_name() {
		return 'glozin-product-rating';
	}

	public function get_title() {
		return esc_html__( '[Glozin] Product Rating', 'glozin-addons' );
	}

	public function get_icon() {
		return 'eicon-product-rating';
	}

	public function get_categories() {
		return ['glozin-addons-product'];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'rating', 'review', 'comments', 'stars', 'product' ];
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
			'section_product_rating_style',
			[
				'label' => esc_html__( 'Style', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'star_color',
			[
				'label' => esc_html__( 'Star Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-product-rating .star-rating .user-rating' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'empty_star_color',
			[
				'label' => esc_html__( 'Empty Star Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-product-rating .star-rating .max-rating' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'star_size',
			[
				'label' => esc_html__( 'Star Size', 'glozin-addons' ),
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
					'{{WRAPPER}} .woocommerce-product-rating .star-rating' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'star_spacing',
			[
				'label' => esc_html__( 'Star Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-product-rating .star-rating' => '--gz-rating-spacing: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'link_heading',
			[
				'label' => esc_html__( 'Link', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography',
				'selector' => '{{WRAPPER}} .woocommerce-product-rating .woocommerce-review-link',
			]
		);

		$this->add_control(
			'link_color',
			[
				'label' => esc_html__( 'Link Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-product-rating .woocommerce-review-link' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'link_hover_color',
			[
				'label' => esc_html__( 'Hover Link Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-product-rating .woocommerce-review-link:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'link_spacing',
			[
				'label' => esc_html__( 'Link Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-product-rating .woocommerce-review-link' => 'margin-inline-start: {{SIZE}}{{UNIT}}',
					'.rtl {{WRAPPER}} .woocommerce-product-rating .woocommerce-review-link' => 'margin-inline-end: {{SIZE}}{{UNIT}}; margin-inline-start: 0;',
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

		if ( ! function_exists('wc_review_ratings_enabled') || ! wc_review_ratings_enabled() ) {
			return;
		}

		if ( \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
			$original_post = $GLOBALS['post'];
			$GLOBALS['post'] = get_post( $product->get_id() );
			setup_postdata( $GLOBALS['post'] );
		}

		if( function_exists('woocommerce_template_single_rating') ) {
			if( $product->get_rating_count() > 0 ) {
				woocommerce_template_single_rating();
			} else {
				if ( \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
					$this->get_staring_html();
				}
			}
		}

		if ( \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
			$GLOBALS['post'] = $original_post;
		}
	}

	public function get_staring_html() {
		echo '<div class="woocommerce-product-rating woocommerce-product-rating--elementor-mode">';
			echo sprintf( '<div class="star-rating" role="img">
								<span class="max-rating rating-stars">
									%1$s
									%1$s
									%1$s
									%1$s
									%1$s
								</span>
								<span class="user-rating rating-stars" style="%2$s">
									%1$s
									%1$s
									%1$s
									%1$s
									%1$s
								</span>
							</div>',
						\Glozin\Addons\Helper::inline_svg( 'icon=star' ),
						esc_attr( '--gz-rating-width:80%' )
				);
			echo '<a href="#reviews" class="woocommerce-review-link" rel="nofollow">( <span class="count">1</span> review )</a>';
		echo '</div>';
	}
}
