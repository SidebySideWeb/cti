<?php
namespace Glozin\Addons\Elementor\Builder\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Archive_Product_Ordering extends Widget_Base {

	public function get_name() {
		return 'glozin-archive-product-ordering';
	}

	public function get_title() {
		return esc_html__( '[Glozin] Product Archive Ordering', 'glozin-addons' );
	}

	public function get_icon() {
		return 'eicon-sort-amount-desc';
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'archive', 'product', 'ordering' ];
	}

	public function get_categories() {
		return [ 'glozin-addons-archive-product' ];
	}

	public function get_style_depends() {
		return [ 'select2' ];
	}

	public function get_script_depends() {
		return [
			'glozin-product-elementor-widgets'
		];
	}

	protected function register_controls() {
		$this->start_controls_section(
            'content',
            [
                'label' => __( 'Content', 'glozin-addons' ),
            ]
        );

		$this->add_responsive_control(
			'alignment',
			[
				'label'       => esc_html__( 'Alignment', 'glozin-addons' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'flex-start'   => [
						'title' => esc_html__( 'Left', 'glozin-addons' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'glozin-addons' ),
						'icon'  => 'eicon-text-align-center',
					],
					'flex-end'  => [
						'title' => esc_html__( 'Right', 'glozin-addons' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'     => '',
				'selectors'   => [
					'{{WRAPPER}} .catalog-toolbar__orderby-default' => 'justify-content: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'mobile_content',
			[
				'label' => esc_html__( 'Mobile', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'mobile_icon',
			[
				'label' => __( 'Icon', 'glozin-addons' ),
				'type' => Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'mobile_text',
			[
				'label' => __( 'Text', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Sort by:', 'glozin-addons' ),
				'label_block' => true,
			]
		);

		$this->end_controls_section();

        $this->start_controls_section(
            'desktop_style',
            [
                'label' => __( 'Desktop Style', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'desktop_typography',
				'selector' => '{{WRAPPER}} .catalog-toolbar__orderby-default span',
			]
		);

		$this->add_control(
			'desktop_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .catalog-toolbar__orderby-default span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'desktop_gap',
			[
				'label' => esc_html__( 'Gap', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .catalog-toolbar__orderby-default' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'desktop_list',
			[
				'label' => esc_html__( 'List', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'desktop_list_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .catalog-toolbar__orderby-list' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .catalog-toolbar__orderby-list' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'desktop_list_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .catalog-toolbar__orderby-list' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'desktop_list_item',
			[
				'label' => esc_html__( 'Item', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'desktop_list_item_typography',
				'selector' => '{{WRAPPER}} .catalog-toolbar__orderby-list a',
			]
		);

		$this->add_control(
			'desktop_list_item_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .catalog-toolbar__orderby-list a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'desktop_list_item_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .catalog-toolbar__orderby-list a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .catalog-toolbar__orderby-list a' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
            'mobile_style',
            [
                'label' => __( 'Mobile Style', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_control(
			'mobile_button_style',
			[
				'label'   => __( 'Style', 'glozin-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'outline',
				'options' => [
					''             => __( 'Solid Dark', 'glozin-addons' ),
					'light'        => __( 'Solid Light', 'glozin-addons' ),
					'outline-dark' => __( 'Outline Dark', 'glozin-addons' ),
					'outline'      => __( 'Outline Light', 'glozin-addons' ),
					'subtle'       => __( 'Underline', 'glozin-addons' ),
					'text'         => __( 'Text', 'glozin-addons' ),
				],
			]
		);

		$this->add_responsive_control(
			'mobile_button_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .catalog-toolbar__orderby-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .catalog-toolbar__orderby-button' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'mobile_button_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .catalog-toolbar__orderby-button' => '--gz-button-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .catalog-toolbar__orderby-button' => '--gz-button-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'mobile_button_typography',
				'selector' => '{{WRAPPER}} .catalog-toolbar__orderby-button',
			]
		);

		$this->add_responsive_control(
			'mobile_button_border_width',
			[
				'label' => esc_html__( 'Border Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'size_units' => [ 'px', 'em', 'rem' ],
				'selectors' => [
					'{{WRAPPER}} .catalog-toolbar__orderby-button' => 'border-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'mobile_button_style' => [ 'outline-dark', 'outline' ],
				],
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => __( 'Normal', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'mobile_button_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .catalog-toolbar__orderby-button' => '--gz-button-bg-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'mobile_button_text_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .catalog-toolbar__orderby-button' => '--gz-button-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'mobile_button_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .catalog-toolbar__orderby-button' => '--gz-button-border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => __( 'Hover', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'mobile_button_background_hover_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .catalog-toolbar__orderby-button' => '--gz-button-bg-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'mobile_button_hover_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .catalog-toolbar__orderby-button' => '--gz-button-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'mobile_button_hover_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .catalog-toolbar__orderby-button' => '--gz-button-border-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'mobile_button_background_effect_hover_color',
			[
				'label'     => __( 'Background Effect Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .catalog-toolbar__orderby-button' => '--gz-button-eff-bg-color-hover: {{VALUE}};',
				],
				'condition' => [
					'mobile_button_style' => ['']
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		
		add_filter( 'woocommerce_catalog_orderby', array( $this, 'catalog_orderby' ) );
		add_filter( 'woocommerce_get_catalog_ordering_args', array( $this, 'catalog_ordering_args' ) );

		$icon = '';
		if( ! empty( $settings['mobile_icon']['value'] ) ) {
			$icon = '<span class="glozin-svg-icon glozin-svg-icon--arrow-bottom">' . \Elementor\Icons_Manager::try_get_icon_html( $settings['mobile_icon'], [ 'aria-hidden' => 'true' ] ) . '</span>';
		} else {
			$icon = \Glozin\Addons\Helper::get_svg( 'arrow-bottom' );
		}

		$text = '';
		if( ! empty( $settings['mobile_text'] ) ) {
			$text = esc_html( $settings['mobile_text'] );
		} else {
			$text = esc_html__('Sort by:', 'glozin');
		}

		$orderby_list = (array) self::orderby_list();
		$default_orderby = isset($_GET['orderby']) ? $_GET['orderby'] : get_option('woocommerce_default_catalog_orderby', 'menu_order');
		$default_orderby_name = isset($orderby_list[$default_orderby]) ? $orderby_list[$default_orderby] : esc_html__('Default Sorting', 'glozin');

		if( \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
			?>
			<div class="catalog-toolbar__item">
				<div class="catalog-toolbar__orderby-form position-relative d-none d-block-md">
					<?php woocommerce_catalog_ordering(); ?>
					<div class="catalog-toolbar__orderby-default d-flex align-items-center gap-20 position-relative">
						<span><?php echo esc_html__( 'Sort by:', 'glozin' ); ?></span>
						<span class="catalog-toolbar__orderby-default-name text-dark fw-medium"><?php echo $default_orderby_name; ?></span>
						<span class="gz-collapse-icon fs-10"><?php echo \Glozin\Icon::get_svg( 'arrow-bottom' ); ?></span>
					</div>
					<ul class="catalog-toolbar__orderby-list list-unstyled shadow position-absolute top-100 end-0 z-3 bg-light rounded-5 px-25 py-20">
						<?php foreach ( $orderby_list as $id => $name ) { ?>
							<li><a class="catalog-toolbar__orderby-item text-base py-4 d-block" href="#" data-id="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $name ); ?></a></li>
						<?php } ?>
					</ul>
				</div>
				<button class="gz-button-<?php echo esc_attr( $settings['mobile_button_style'] ); ?> catalog-toolbar__orderby-button d-none-md" data-toggle="popover" data-target="mobile-orderby-popover"><?php echo $text; ?> <?php echo $icon; ?></button>
			</div>
			<?php
			return;
		}

		\Glozin\Theme::set_prop( 'popovers', 'mobile-orderby' );

		?>
		<div class="catalog-toolbar__item">
			<div class="catalog-toolbar__orderby-form position-relative d-none d-block-md">
				<?php woocommerce_catalog_ordering(); ?>
				<div class="catalog-toolbar__orderby-default d-flex align-items-center gap-20 position-relative">
					<span><?php echo esc_html__( 'Sort by:', 'glozin' ); ?></span>
					<span class="catalog-toolbar__orderby-default-name text-dark fw-medium"><?php echo $default_orderby_name; ?></span>
					<span class="gz-collapse-icon fs-10"><?php echo \Glozin\Icon::get_svg( 'arrow-bottom' ); ?></span>
				</div>
				<ul class="catalog-toolbar__orderby-list list-unstyled shadow position-absolute top-100 end-0 z-3 bg-light rounded-5 px-25 py-20">
					<?php foreach ( $orderby_list as $id => $name ) { ?>
						<li><a class="catalog-toolbar__orderby-item text-base py-4 d-block" href="#" data-id="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $name ); ?></a></li>
					<?php } ?>
				</ul>
			</div>
			<button class="gz-button-<?php echo esc_attr( $settings['mobile_button_style'] ); ?> catalog-toolbar__orderby-button d-none-md" data-toggle="popover" data-target="mobile-orderby-popover"><?php echo $text; ?> <?php echo $icon; ?></button>
		</div>
		<?php
	}

	/**
	 * Order by list
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function orderby_list() {
		$show_default_orderby    = 'menu_order' === apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby', 'menu_order' ) );
		$orderby = apply_filters(
			'woocommerce_catalog_orderby',
			array(
				'menu_order' => __( 'Default sorting', 'glozin' ),
				'popularity' => __( 'Popularity', 'glozin' ),
				'rating'     => __( 'Average rating', 'glozin' ),
				'price'      => __( 'Price, low to high', 'glozin' ),
				'price-desc' => __( 'Price, high to low', 'glozin' ),
				'date'       => __( 'Date, new to old', 'glozin' ),
				'date-asc'   => __( 'Date, old to new', 'glozin' ),
			)
		);

		if ( wc_get_loop_prop( 'is_search' ) ) {
			$orderby = array_merge( array( 'relevance' => __( 'Relevance', 'woocommerce' ) ), $orderby );

			unset( $orderby['menu_order'] );
		}

		if ( ! $show_default_orderby ) {
			unset( $orderby['menu_order'] );
		}

		if ( function_exists('wc_review_ratings_enabled') && ! wc_review_ratings_enabled() ) {
			unset( $orderby['rating'] );
		}

		return $orderby;
	}

	public function catalog_orderby( $orderby ) {
		$orderby['date-asc'] = __( 'Date, old to new', 'glozin' );

		return $orderby;
	}

	public function catalog_ordering_args( $args ) {
		if ( isset( $_GET['orderby'] ) && 'date-asc' === $_GET['orderby'] ) {
			$args['orderby'] = 'date';
			$args['order'] = 'ASC';
		}
		return $args;
	}
}
