<?php

namespace Glozin\Addons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Glozin\Addons\Elementor\Base\Carousel_Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Product Tabs widget
 */
class Product_Sale_Tabs extends Carousel_Widget_Base {
    use \Glozin\Addons\Woocommerce\Products_Base;
	use \Glozin\Addons\Elementor\Base\Button_Base;

	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-product-sale-tabs';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Glozin] Product Sale Tabs', 'glozin-addons' );
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
				'label'     => esc_html__( 'Products', 'glozin-addons' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'sale_products'         => esc_html__( 'Sale Products', 'glozin-addons' ),
					'percentage_discount'   => esc_html__( 'Percentage Discount', 'glozin-addons' ),
				],
				'default'   => 'sale_products',
				'toggle'    => false,
			]
		);

		$repeater->add_control(
			'percent_from',
			[
				'label' => esc_html__( 'Percent From', 'glozin-addons' ),
				'type'  => Controls_Manager::NUMBER,
				'min'   => 0,
				'max'   => 100,
				'default' => 0,
				'condition' => [
					'products' => 'percentage_discount',
				]
			]
		);

		$repeater->add_control(
			'percent_to',
			[
				'label' => esc_html__( 'Percent To', 'glozin-addons' ),
				'type'  => Controls_Manager::NUMBER,
				'min'   => 0,
				'max'   => 100,
				'default' => 30,
				'condition' => [
					'products' => 'percentage_discount',
				]
			]
		);

		$repeater->add_control(
			'orderby',
			[
				'label'     => esc_html__( 'Order By', 'glozin-addons' ),
				'type'      => Controls_Manager::SELECT,
				'options' => $this->get_options_product_orderby(),
				'condition' => [
					'products' => ['featured_products', 'sale_products', 'custom_products', 'percentage_discount']
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
					'products' => ['featured_products', 'sale_products', 'custom_products', 'percentage_discount'],
					'orderby!' => ['rand'],
				],
				'default'   => '',
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
						'heading' => esc_html__( 'All Products', 'glozin-addons' ),
						'products'  => 'sale_products'
					],
					[
						'heading' => esc_html__( 'Best of Sale', 'glozin-addons' ),
						'products'  => 'percentage_discount',
						'percent_from' => 76,
						'percent_to' => 100,
					],
					[
						'heading' => esc_html__( 'Sale 75% Off', 'glozin-addons' ),
						'products'  => 'percentage_discount',
						'percent_from' => 51,
						'percent_to' => 75,
					],
					[
						'heading' => esc_html__( 'Sale 50% Off', 'glozin-addons' ),
						'products'  => 'percentage_discount',
						'percent_from' => 31,
						'percent_to' => 50,
					],
					[
						'heading' => esc_html__( 'Sale 30% Off', 'glozin-addons' ),
						'products'  => 'percentage_discount',
						'percent_from' => 0,
						'percent_to' => 30,
					]
				],
				'title_field'   => '{{{ heading }}}',
				'prevent_empty' => false,
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
			'slides_rows'	   => 1,
			'slides_to_show'   => 4,
			'slides_to_scroll' => 1,
			'space_between'    => 30,
			'navigation'       => '',
			'autoplay'         => '',
			'autoplay_speed'   => 3000,
			'pause_on_hover'   => 'yes',
			'animation_speed'  => 800,
			'infinite'         => '',
			'slidesperview_auto' => '',
		];

		$this->register_carousel_controls( $controls );

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
					'{{WRAPPER}} .glozin-product-tabs-carousel__heading' => 'justify-content: {{VALUE}}',
				],
				'selectors_dictionary' => [
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				],
			]
		);

		$this->add_responsive_control(
			'heading_gap',
			[
				'label'     => __( 'Gap', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 500,
						'min' => 0,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-product-tabs-carousel__heading' => 'gap: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .glozin-product-tabs-carousel__heading' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
			'section_style',
			[
				'label'     => __( 'Carousel Settings', 'glozin-addons' ),
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
		$settings = $this->get_settings_for_display();

        $query_args = [];

		$col = $settings['slides_to_show'];
		$col_tablet = ! empty( $settings['slides_to_show_tablet'] ) ? $settings['slides_to_show_tablet'] : $col;
		$col_mobile = ! empty( $settings['slides_to_show_mobile'] ) ? $settings['slides_to_show_mobile'] : $col;

        $this->add_render_attribute( 'wrapper', 'class', 'glozin-product-tabs-carousel', 'glozin-product-tabs', 'glozin-product-sale-tabs' );
        $this->add_render_attribute( 'heading', 'class', [ 'glozin-product-tabs-carousel__heading', 'glozin-product-tabs__heading', 'd-flex', 'flex-nowrap', 'flex-wrap-md', 'align-items-center', 'justify-content-center', 'gap-15', 'mb-40' ] );

        $this->add_render_attribute( 'items', 'class', [ 'glozin-product-tabs-carousel__items', 'glozin-product-tabs__items', 'position-relative', intval($settings['slides_rows']) > 1 ? 'has-rows-carousel' : '' ] );
        $this->add_render_attribute( 'item', 'class', [ 'glozin-product-tabs-carousel__item', 'glozin-product-tabs__item', 'glozin-carousel--elementor', 'active' ] );
        $this->add_render_attribute( 'item', 'data-panel', '1' );
		$this->add_render_attribute( 'swiper', 'class', 'swiper' );
		$this->add_render_attribute( 'swiper', 'data-desktop', $col );
		$this->add_render_attribute( 'swiper', 'data-tablet', $col_tablet );
		$this->add_render_attribute( 'swiper', 'data-mobile', $col_mobile );
		$this->add_render_attribute( 'swiper', 'style', $this->render_space_between_style() );
		$this->render_slidesperview_auto_class_style( 'swiper' );

		$this->add_render_attribute( 'swiper_original', 'class', ['swiper-original', 'hidden'] );
		$this->add_render_attribute( 'swiper_original', 'data-desktop', $col );
		$this->add_render_attribute( 'swiper_original', 'data-tablet', $col_tablet );
		$this->add_render_attribute( 'swiper_original', 'data-mobile', $col_mobile );
		$this->add_render_attribute( 'swiper_original', 'style', $this->render_space_between_style() );
		$this->render_slidesperview_auto_class_style( 'swiper_original' );

        $this->add_render_attribute( 'loading', 'class', [ 'glozin-product-tabs-carousel__loading', 'glozin-product-tabs__loading', 'position-absolute', 'gz-loading-spin' ] );

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
                                'per_page'       => $settings['limit'],
                                'columns'        => $settings['slides_to_show'],
                                'swiper'         => true,
								'percent_from'   => $tab['percent_from'],
								'percent_to'     => $tab['percent_to'],
                            ];

                            $tab_key = $this->get_repeater_setting_key( 'tab', 'products_tab', $key );

					        $this->add_render_attribute( $tab_key, [ 'data-target' => $a, 'data-atts' => json_encode( $attr ) ] );
							$this->add_render_attribute( $tab_key, 'class', [ 'glozin-product-tabs-carousel__heading-button', 'glozin-product-tabs__heading-button', 'glozin-button', 'gz-button', ! empty( $settings['hb_button_style'] ) ? ' gz-button-'  . $settings['hb_button_style'] : 'gz-button-default' ] );

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
					<div <?php echo $this->get_render_attribute_string( 'swiper' ) ?>>
						<?php echo $this->render_products( $query_args ); ?>
					</div>
                </div>
                <div class="navigation-original hidden">
					<?php echo '<div class="swiper-arrows">' . $this->render_arrows('arrow-primary') . '</div>'; ?>
				    <?php echo $this->render_pagination(); ?>
                </div>
				<div <?php echo $this->get_render_attribute_string( 'swiper_original' ) ?>></div>
            </div>
        <?php
        echo '</div>';
	}
}