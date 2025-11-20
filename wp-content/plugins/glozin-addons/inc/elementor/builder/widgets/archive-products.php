<?php
namespace Glozin\Addons\Elementor\Builder\Widgets;

use Glozin\Addons\Elementor\Builder\Current_Query_Renderer;
use Glozin\Addons\Elementor\Builder\Products_Renderer;
use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Archive_Products extends Widget_Base {

	public function get_name() {
		return 'glozin-archive-products';
	}

	public function get_title() {
		return esc_html__( '[Glozin] Archive Products', 'glozin-addons' );
	}

	public function get_icon() {
		return 'eicon-products';
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'archive', 'product' ];
	}

	public function get_categories() {
		return [ 'glozin-addons-archive-product' ];
	}

	public function get_script_depends() {
		return [
			'glozin-product-elementor-widgets',
		];
	}

	protected function register_controls() {
        $this->start_controls_section(
			'section_content',
			[
				'label'     => __( 'Content', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'show_default_view',
			[
				'label' => esc_html__( 'Shop Default View', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'list'       => esc_html__( 'List', 'glozin' ),
					'grid'       => esc_html__( 'Grid', 'glozin' ),
				],
				'default' => 'grid',
			]
		);

		$this->add_control(
			'important_note',
			[
				'label' => esc_html__( 'Note', 'glozin-addons' ),
				'type'  => Controls_Manager::RAW_HTML,
				'raw'   => '<p>' . esc_html__( 'To edit columns with rows, go to Customizing > Woocommerce > Product Catalog: ', 'glozin-addons' ) . '</p><a href="' . admin_url( 'customize.php' ) . '" target="_blank">
								' . esc_html__( 'Edit Columns & Rows', 'glozin-addons' ) . '
							</a>',
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
		);

        $this->add_control(
			'pagination',
			[
				'label'     => esc_html__( 'Pagination', 'glozin-addons' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'numeric'  => esc_attr__( 'Numeric', 'glozin' ),
					'infinite' => esc_attr__( 'Infinite Scroll', 'glozin' ),
					'loadmore' => esc_attr__( 'Load More', 'glozin' ),
				],
				'default'   => 'numeric',
			]
		);

		$this->add_control(
			'loadmore_text',
			[
				'label' => __( 'Text', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Load More', 'glozin-addons' ),
				'label_block' => true,
				'condition' => [
					'pagination' => 'loadmore',
				]
			]
		);

        $this->end_controls_section();

		$this->start_controls_section(
			'section_pagination_style',
			[
				'label' => esc_html__( 'Pagination', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'pagination_spacing_top',
			[
				'label' => __( 'Spacing Top', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-pagination' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'pagination_gap',
			[
				'label' => __( 'Gap', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-pagination ul' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'pagination_typography',
				'selector' => '{{WRAPPER}} .woocommerce-pagination a, {{WRAPPER}} .woocommerce-pagination ul .page-numbers',
			]
		);

		$this->add_control(
			'pagination_icon_size',
			[
				'label' => __( 'Icon Size', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-pagination ul .page-numbers.prev' => '--gz-button-icon-size: {{SIZE}}{{UNIT}}; font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .woocommerce-pagination ul .page-numbers.next' => '--gz-button-icon-size: {{SIZE}}{{UNIT}}; font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_min_width',
			[
				'label' => __( 'Min Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-pagination.woocommerce-pagination--loadmore .woocommerce-pagination-button' => 'min-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'pagination' => 'loadmore',
				]
			]
		);

		$this->add_control(
			'pagination_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-pagination a, {{WRAPPER}} .woocommerce-pagination ul .page-numbers' => '--gz-button-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-pagination a, {{WRAPPER}} .woocommerce-pagination ul .page-numbers' => '--gz-button-bg-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-pagination a, {{WRAPPER}} .woocommerce-pagination ul .page-numbers' => '--gz-button-border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-pagination a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .woocommerce-pagination ul .page-numbers' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-cart-tracking__badges' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .woocommerce-pagination ul .page-numbers' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'pagination_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-pagination a' => '--gz-button-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .woocommerce-pagination ul .page-numbers' => '--gz-button-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-cart-tracking__badges' => '--gz-button-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .woocommerce-pagination ul .page-numbers' => '--gz-button-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'pagination_hover_active_heading',
			[
				'label'     => esc_html__( 'Hover & Active', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'pagination_hover_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-pagination a' => '--gz-button-color-hover: {{VALUE}};',
					'{{WRAPPER}} .woocommerce-pagination ul .page-numbers.current' => '--gz-button-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_hover_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-pagination a' => '--gz-button-bg-color-hover: {{VALUE}};',
					'{{WRAPPER}} .woocommerce-pagination ul .page-numbers.current' => '--gz-button-bg-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_hover_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-pagination a' => '--gz-button-border-color-hover: {{VALUE}};',
					'{{WRAPPER}} .woocommerce-pagination ul .page-numbers.current' => '--gz-button-border-color: {{VALUE}};',
				],
			]
		);

        $this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$settings['paginate'] = true;

		remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination' );

		if ( \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
			$shortcode = new Products_Renderer( $settings, 'products' );
			add_action( 'woocommerce_after_shop_loop', [ $this, 'pagination' ] );
		} else {
			$shortcode = new Current_Query_Renderer( $settings, 'current_query' );
			add_action( 'woocommerce_after_shop_loop', [ $this, 'pagination' ] );
		}

		$content = $shortcode->get_content();

		echo '<div id="glozin-shop-content" class="glozin-archive-products glozin-shop-content">';
		if ( $content ) {
			$content = str_replace( '<ul class="products', '<ul class="products products-elementor' , $content );

			if( $settings['show_default_view'] == 'list' ) {
				if( ! strpos( $content, 'product-card-layout-list' ) ) {
					$content = str_replace( '<ul class="products', '<ul class="products product-card-layout-list', $content );
				}
			} else {
				if( strpos( $content, 'product-card-layout-list' ) ) {
					$content = str_replace( 'product-card-layout-list', '', $content );
				}
			}

			if( isset( $_COOKIE['catalog_view'] ) && $_COOKIE['catalog_view'] == 'list' ) {
				if( ! strpos( $content, 'product-card-layout-list' ) ) {
					$content = str_replace( '<ul class="products', '<ul class="products product-card-layout-list', $content );
				}
			}

			if( isset( $_COOKIE['catalog_view'] ) && $_COOKIE['catalog_view'] == 'grid' ) {
				if( strpos( $content, 'product-card-layout-list' ) ) {
					$content = str_replace( 'product-card-layout-list', '', $content );
				}
			}

			if( isset( $_GET['view'] ) && $_GET['view'] == 'list' ) {
				if( ! strpos( $content, 'product-card-layout-list' ) ) {
					$content = str_replace( '<ul class="products', '<ul class="products product-card-layout-list', $content );
				}
			}

			if( isset( $_GET['view'] ) && $_GET['view'] == 'grid' ) {
				if( strpos( $content, 'product-card-layout-list' ) ) {
					$content = str_replace( 'product-card-layout-list', '', $content );
				}
			}

			echo $content;
		} else {
			echo '<div class="elementor-nothing-found glozin-products-nothing-found woocommerce-info">' . esc_html__('No products were found matching your selection.', 'glozin-addons') . '</div>';
			echo '<ul class="products products-elementor '. ( $settings['show_default_view'] == 'list' ? 'product-card-layout-list' : '' ) .' columns-'. esc_attr( get_option( 'woocommerce_catalog_columns', 4 ) ) .'"></ul>';
		}

		echo '</div>';

		remove_action( 'woocommerce_after_shop_loop', [ $this, 'pagination' ] );
	}

	/**
	 * Products pagination.
	 */
	public function pagination() {
		$settings = $this->get_settings_for_display();
		$nav_type = apply_filters( 'glozin_product_catalog_pagination_builder', $settings['pagination'] );
		global $wp_query;

		if ( 'numeric' == $nav_type ) {
			woocommerce_pagination();
			return;
		}

		$max_page = $wp_query->max_num_pages;
		$total = $wp_query->found_posts;
		$post_count = $wp_query->post_count;
		
		if( \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
			$max_page = 2;
			$total = 16;
			$post_count = 8;
		}

		if( $max_page <= 1 && ! \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
			return;
		}


		$classes = array(
			'woocommerce-pagination',
			'woocommerce-pagination--catalog',
			'next-posts-pagination',
			'woocommerce-pagination--ajax',
			'woocommerce-pagination--' . esc_attr( $nav_type ),
			'text-center'
		);

		add_filter( 'next_posts_link_attributes', array( $this, 'glozin_next_posts_link_attributes' ), 10, 1 );

		echo '<nav class="' . esc_attr( implode( ' ', $classes ) ) . '">';
			$this->get_posts_found( $post_count, $total );
			
			if ( \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
				?>
					<a href="#" class="woocommerce-pagination-button gz-button py-17 px-30 mt-30 gz-button-hover-effect min-w-180 <?php echo $nav_type == 'infinite' ? 'loading' : ''; ?>">
						<span><?php echo ! empty( $settings['loadmore_text'] ) ? $settings['loadmore_text'] : esc_html__( 'Load more', 'glozin' ); ?></span>
					</a>
				<?php
			} else {
				$text = ! empty( $settings['loadmore_text'] ) ? $settings['loadmore_text'] : esc_html__( 'Load more', 'glozin' );
				next_posts_link( '<span>' . $text . '</span>', $max_page );
			}
		echo '</nav>';
	}

	/**
	 * Next posts link attributes
	 *
	 * @return string $attr
	 */
	public function glozin_next_posts_link_attributes( $attr ) {
		$settings = $this->get_settings_for_display();
		$nav_type = apply_filters( 'glozin_product_catalog_pagination_builder', $settings['pagination'] );
		if( $nav_type !== 'numeric' ) {
			$attr = 'class="woocommerce-pagination-button gz-button py-17 px-30 mt-30 gz-button-hover-effect min-w-180"';
		}

		return $attr;
	}

	/**
	 * Get posts found
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function get_posts_found( $post_count, $found_posts ) {
		echo sprintf( '<div class="gz-posts-found gz-progress text-center">
								<div class="gz-posts-found__inner gz-progress__inner">
									%s
									<span class="current-post"> %s </span>
									%s
									<span class="found-post"> %s </span>
									%s
									<span class="count-bar gz-progress__count-bar"></span>
								</div>
							</div>',
					esc_html__( "You've viewed", 'glozin' ),
					$post_count,
					esc_html__( 'of', 'glozin' ),
					$found_posts,
					esc_html__( 'result', 'glozin' )
		);
	}
}