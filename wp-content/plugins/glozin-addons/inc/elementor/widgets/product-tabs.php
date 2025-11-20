<?php

namespace Glozin\Addons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Product Tabs widget
 */
class Product_Tabs extends Widget_Base {
    use \Glozin\Addons\Woocommerce\Products_Base;
	use \Glozin\Addons\Elementor\Base\Button_Base;

	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-product-tabs';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Glozin] Product Tabs', 'glozin-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-product-tabs';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'glozin-addons' ];
	}

	/**
	 * Scripts
	 *
	 * @return void
	 */
	public function get_script_depends() {
		return [
			'glozin-product-tabs-widget'
		];
	}

	/**
	 * Style
	 *
	 * @return void
	 */
	public function get_style_depends() {
		return [ 'glozin-product-tabs-css' ];
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
		$this->start_controls_section(
			'section_products',
			[ 'label' => esc_html__( 'Product Tabs', 'glozin-addons' ) ]
		);

		$this->add_control(
			'limit',
			[
				'label'   => esc_html__( 'Total Products', 'glozin-addons' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 8,
				'min'     => 1,
				'max'     => 50,
				'step'    => 1,
			]
		);

		$this->add_control(
			'columns',
			[
				'label'     => esc_html__( 'Columns', 'glozin-addons' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'1' => esc_html__( '1 Column', 'glozin-addons' ),
					'2' => esc_html__( '2 Columns', 'glozin-addons' ),
					'3' => esc_html__( '3 Columns', 'glozin-addons' ),
					'4' => esc_html__( '4 Columns', 'glozin-addons' ),
					'5' => esc_html__( '5 Columns', 'glozin-addons' ),
					'6' => esc_html__( '6 Columns', 'glozin-addons' ),
				],
				'default'   => '4',
			]
		);

        $repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'heading',
			[
				'label'       => esc_html__( 'Heading', 'glozin-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'This is heading', 'glozin-addons' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'products',
			[
				'label'     => esc_html__( 'Product', 'glozin-addons' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'recent_products'       => esc_html__( 'Recent', 'glozin-addons' ),
					'featured_products'     => esc_html__( 'Featured', 'glozin-addons' ),
					'best_selling_products' => esc_html__( 'Best Selling', 'glozin-addons' ),
					'top_rated_products'    => esc_html__( 'Top Rated', 'glozin-addons' ),
					'sale_products'         => esc_html__( 'On Sale', 'glozin-addons' ),
					'custom_products'       => esc_html__( 'Custom', 'glozin-addons' ),
				],
				'default'   => 'recent_products',
				'toggle'    => false,
			]
		);

		$repeater->add_control(
			'orderby',
			[
				'label'     => esc_html__( 'Order By', 'glozin-addons' ),
				'type'      => Controls_Manager::SELECT,
				'options' => $this->get_options_product_orderby(),
				'condition' => [
					'products' => ['featured_products', 'sale_products', 'custom_products']
				],
				'default'   => 'date',
			]
		);

		$repeater->add_control(
			'order',
			[
				'label'     => esc_html__( 'Order', 'glozin-addons' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					''     => esc_html__( 'Default', 'glozin-addons' ),
					'asc'  => esc_html__( 'Ascending', 'glozin-addons' ),
					'desc' => esc_html__( 'Descending', 'glozin-addons' ),
				],
				'condition' => [
					'products' => ['featured_products', 'sale_products', 'custom_products'],
					'orderby!' => ['rand'],
				],
				'default'   => '',
			]
		);

		$repeater->add_control(
			'ids',
			[
				'label' => __( 'Products', 'glozin-addons' ),
				'type' => 'glozin-autocomplete',
				'placeholder' => esc_html__( 'Click here and start typing...', 'glozin-addons' ),
				'default' => '',
				'multiple'    => true,
				'source'      => 'product',
				'sortable'    => true,
				'label_block' => true,
				'condition' => [
					'products' => ['custom_products']
				],
			]
		);

		$repeater->add_control(
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
				'condition' => [
					'products!' => ['custom_products']
				],
			]
		);

		$repeater->add_control(
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
				'condition' => [
					'products!' => ['custom_products']
				],
			]
		);

		$repeater->add_control(
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
				'condition' => [
					'products!' => ['custom_products']
				],
			]
		);

        $this->add_control(
			'tabs',
			[
				'label'         => '',
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $repeater->get_controls(),
				'default'       => [
					[
						'heading' => esc_html__( 'Best seller', 'glozin-addons' ),
						'products'  => 'best_selling_products'
					],
					[
						'heading' => esc_html__( 'New arrivals', 'glozin-addons' ),
						'products'  => 'recent_products'
					],
					[
						'heading' => esc_html__( 'On Sale', 'glozin-addons' ),
						'products'  => 'sale_products'
					]
				],
				'title_field'   => '{{{ heading }}}',
				'prevent_empty' => false,
			]
		);

		$this->add_control(
			'pagination',
			[
				'label' => __( 'Pagination', 'glozin-addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'glozin-addons' ),
				'label_on'  => __( 'Show', 'glozin-addons' ),
				'default'   => '',
			]
		);

		$this->add_control(
			'pagination_type',
			[
				'label' => __( 'Pagination Type', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'infinite' => esc_attr__( 'Infinite Scroll', 'glozin-addons' ),
					'loadmore' => esc_attr__( 'Load More', 'glozin-addons' ),
				],
				'default' => 'loadmore',
				'condition'   => [
					'pagination' => 'yes',
				],
			]
		);

		$this->add_control(
			'pagination_text',
			[
				'label'       => esc_html__( 'Pagination Text', 'glozin-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'condition'   => [
					'pagination' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	// Tab Style
	protected function section_style() {
		$this->start_controls_section(
			'section_style_heading',
			[
				'label' => esc_html__( 'Heading', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'heading_gap',
			[
				'label'     => __( 'Gap', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 200,
						'min' => 0,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-product-tabs__heading' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'heading_spacing',
			[
				'label'     => __( 'Spacing', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 200,
						'min' => 0,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-product-tabs__heading' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'tab_heading',
			[
				'label' => esc_html__( 'Tab heading', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'heading_horizontal_position',
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
				'selectors'            => [
					'{{WRAPPER}} .glozin-product-tabs__heading' => 'justify-content: {{VALUE}}',
				],
				'selectors_dictionary' => [
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				],
			]
		);

		$this->register_button_style_controls( '', 'glozin-product-tabs__heading-button', 'hb' );

		$this->add_control(
			'hb_active_heading',
			[
				'label' => esc_html__( 'Button Active', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( 'hb_active_tabs_button_style' );

		$this->start_controls_tab(
			'hb_active_tab_button_normal',
			[
				'label' => __( 'Normal', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'hb_active_button_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-product-tabs__heading-button.active' => '--gz-button-bg-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hb_active_button_text_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-product-tabs__heading-button.active' => '--gz-button-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hb_active_button_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-product-tabs__heading-button.active' => '--gz-button-border-color: {{VALUE}};',
				],
				'condition' => [
					'hb_button_style' => [ 'outline-dark', 'outline', 'subtle' ],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hb_active_tab_button_hover',
			[
				'label' => __( 'Hover', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'hb_active_button_background_hover_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-product-tabs__heading-button.active' => '--gz-button-bg-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hb_active_hover_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-product-tabs__heading-button.active' => '--gz-button-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hb_active_button_hover_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-product-tabs__heading-button.active' => '--gz-button-border-color-hover: {{VALUE}};',
				],
				'condition' => [
					'hb_button_style' => [ 'outline-dark', 'outline', 'subtle' ],
				],
			]
		);

		$this->add_control(
			'hb_active_button_background_effect_hover_color',
			[
				'label'     => __( 'Background Effect Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-product-tabs__heading-button.active' => '--gz-button-eff-bg-color-hover: {{VALUE}};',
				],
				'condition' => [
					'button_style' => ['', 'light', 'outline-dark']
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_products',
			[
				'label' => esc_html__( 'Products', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'border',
			[
				'label'        => __( 'Border', 'glozin-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_off'    => __( 'Hide', 'glozin-addons' ),
				'label_on'     => __( 'Show', 'glozin-addons' ),
				'default'      => '',
				'separator'    => 'before',
				'prefix_class' => 'glozin-show-border-',
			]
		);

		$this->add_control(
			'product_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}}.glozin-show-border-yes ul.products li.product' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'product_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} ul.products li.product' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ul.products' => 'margin-inline-start: calc(-{{LEFT}}{{UNIT}}); margin-inline-end: calc(-{{RIGHT}}{{UNIT}});',
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

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_pagination',
			[
				'label' => esc_html__( 'Pagination', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'pagination_spacing',
			[
				'label'        => esc_html__( 'Pagination Spacing', 'glozin-addons' ),
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
					'{{WRAPPER}} .woocommerce-pagination' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->register_button_style_controls( 'outline-dark', 'woocommerce-pagination-button' );

		$this->end_controls_section();
	}

	/**
	 * Render icon box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

        $query_args = [];

        $this->add_render_attribute( 'wrapper', 'class', 'glozin-product-tabs' );
        $this->add_render_attribute( 'heading', 'class', [ 'glozin-product-tabs__heading', 'd-flex', 'flex-nowrap', 'flex-wrap-md', 'align-items-center', 'justify-content-center', 'gap-15', 'mb-40' ] );

        $this->add_render_attribute( 'items', 'class', [ 'glozin-product-tabs__items', 'position-relative' ] );
        $this->add_render_attribute( 'item', 'class', [ 'glozin-product-tabs__item', 'active' ] );
        $this->add_render_attribute( 'item', 'data-panel', '1' );

        $this->add_render_attribute( 'loading', 'class', [ 'glozin-product-tabs__loading', 'position-absolute', 'gz-loading-spin' ] );

        echo '<div '. $this->get_render_attribute_string( 'wrapper' ) . '>';
            echo '<div '. $this->get_render_attribute_string( 'heading' ) . '>';
				$a = 1;
				foreach( $settings['tabs'] as $key => $tab ):
					if( ! empty( $tab['heading'] ) ) :
						$attr = [
							'type'           => $tab['products'],
							'orderby'        => $tab['orderby'],
							'order'          => $tab['order'],
							'category'       => $tab['product_cat'],
							'tag'            => $tab['product_tag'],
							'product_brands' => $tab['product_brand'],
							'ids'            => $tab['ids'],
							'per_page'       => $settings['limit'],
							'columns'        => $settings['columns'],
							'pagination'     => $settings['pagination'],
							'pagination_type' => $settings['pagination_type'],
							'pagination_text' => $settings['pagination_text'],
							'button_style'   => $settings['button_style'],
						];

						$tab_key = $this->get_repeater_setting_key( 'tab', 'products_tab', $key );

						$this->add_render_attribute( $tab_key, [ 'data-target' => $a, 'data-atts' => json_encode( $attr ) ] );
						$this->add_render_attribute( $tab_key, 'class', [ 'glozin-product-tabs__heading-button', 'glozin-button', 'gz-button', ! empty( $settings['hb_button_style'] ) ? ' gz-button-'  . $settings['hb_button_style'] : 'gz-button-default' ] );
						if ( 1 === $a ) {
							$this->add_render_attribute( $tab_key, 'class', 'active' );
							$query_args = $attr;
						}

						?>
						<span <?php echo $this->get_render_attribute_string( $tab_key ); ?>><?php echo wp_kses_post( $tab['heading'] ); ?></span>
						<?php
					endif;
				$a++;
				endforeach;
            echo '</div>';
        ?>
            <div <?php echo $this->get_render_attribute_string( 'items' ) ?>>
                <div <?php echo $this->get_render_attribute_string( 'loading' ) ?>></div>
                <div <?php echo $this->get_render_attribute_string( 'item' ) ?>>
                    <?php echo $this->render_products( $query_args ); ?>
                </div>
            </div>
        <?php
        echo '</div>';
	}
}