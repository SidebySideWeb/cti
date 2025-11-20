<?php
namespace Glozin\Addons\Elementor\Builder\Widgets;

use Glozin\Addons\Elementor\Builder\Current_Query_Renderer;
use Glozin\Addons\Elementor\Builder\Products_Renderer;
use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Archive_Product_View extends Widget_Base {
	/**
	 * @var string catalog view
	 */
	public static $catalog_view;

	protected static $view_cookie_name = 'catalog_view';

	public function get_name() {
		return 'glozin-archive-product-view';
	}

	public function get_title() {
		return esc_html__( '[Glozin] Product Archive View', 'glozin-addons' );
	}

	public function get_icon() {
		return 'eicon-preview-thin';
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'archive', 'product', 'view' ];
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
            'view_content',
            [
                'label' => __( 'View', 'glozin-addons' ),
            ]
        );

		$this->add_control(
			'views',
			[
				'label' => esc_html__( 'Show Views', 'glozin-addons' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'options' => [
					'1'       => esc_html__( 'List', 'glozin' ),
					'2'       => esc_html__( 'Grid 2 Columns', 'glozin' ),
					'3'       => esc_html__( 'Grid 3 Columns', 'glozin' ),
					'4'       => esc_html__( 'Grid 4 Columns', 'glozin' ),
				],
				'default' => [ '1', '2', '3', '4' ],
			]
		);

		$this->add_control(
			'views_default',
			[
				'label' => esc_html__( 'Show Active View Default', 'glozin-addons' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'options' => [
					'default' => esc_html__( 'Default', 'glozin' ),
					'1'       => esc_html__( 'List', 'glozin' ),
					'2'       => esc_html__( 'Grid 2 Columns', 'glozin' ),
					'3'       => esc_html__( 'Grid 3 Columns', 'glozin' ),
					'4'       => esc_html__( 'Grid 4 Columns', 'glozin' ),
				],
				'default' => 'default',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
            'view_style',
            [
                'label' => __( 'View', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
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
					'{{WRAPPER}} .glozin-toolbar-view' => 'justify-content: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'gap',
			[
				'label' => esc_html__( 'Gap', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', '%', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-toolbar-view' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-toolbar-view a' => 'color: {{VALUE}}',
				],
			]
		);


		$this->add_control(
			'active_color',
			[
				'label' => esc_html__( 'Active Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-toolbar-view a.current, {{WRAPPER}} .glozin-toolbar-view a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		global $wp;

		$default_column = ! empty( $settings['views_default'] ) && $settings['views_default'] !== 'default' ? $settings['views_default'] : get_option( 'woocommerce_catalog_columns', 4 );

		if( isset( $_GET['view'] ) && ! empty( $_GET['view'] ) && ! \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
			$default = $_GET['view'] == 'grid' ? $_GET['view'] : $_GET['view'];
		} else {
			if( isset( $_COOKIE['catalog_view'] ) && ! \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
				$default = $_COOKIE['catalog_view'];
			} else {
				$default = $settings['views_default'] == '1' ? 'list' : 'grid';
			}
		}
		
		$class = '';
		foreach( $settings['views'] as $column ) {
			if( $column == '1' ) {
				$view = 'list';
				$icon = 'list';
				$class = 'gz-tooltip-inside list';
				$tooltip = esc_html__('List', 'glozin');

				$class .= $default == $view ? ' current' : '';
			} else {
				$view = 'grid';
				$icon = 'grid-' . $column;
				$class = 'gz-tooltip-inside grid grid-' . $column;
				$tooltip = $column . ' ' . esc_html__('Columns', 'glozin');

				$class .= $column == $default_column && $default == $view ? ' current' : '';
			}

			if( $column == $default_column ) {
				$class .= ' default';
			}

			$link_url = array(
				'view' => $view
			);

			if( isset( $_GET ) ) {
				$link_url = wp_parse_args(
					$link_url,
					$_GET
				);
			}

			$current_url = add_query_arg(
				$link_url,
				home_url($wp->request)
			);

			$output_type[] = sprintf(
				'<a href="%s" class="gz-shop-view-item %s" data-column="%s" data-tooltip="%s">%s</a>',
				esc_url($current_url),
				esc_attr( $class ),
				esc_attr( $column ),
				esc_attr($tooltip),
				\Glozin\Addons\Helper::get_svg( $icon ),
			);
		}

		echo sprintf(
			'<div id="glozin-toolbar-view" class="glozin-toolbar-view view-%s d-flex align-items-center gap-15">%s</div>',
			esc_attr($view),
			implode( $output_type )
		);
	}
}
