<?php
namespace Glozin\Addons\Elementor\Builder\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Products_Filter extends Widget_Base {
	use \Glozin\Addons\Elementor\Base\Button_Base;
	private $current_filters = null;
	private $active_fields = null;
	private $current_section = 0;

	public function get_name() {
		return 'glozin-products-filter';
	}

	public function get_title() {
		return esc_html__( '[Glozin] Products Filter', 'glozin-addons' );
	}

	public function get_icon() {
		return 'eicon-filter';
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'archive', 'product', 'filter' ];
	}

	public function get_categories() {
		return [ 'glozin-addons-archive-product' ];
	}

	public function get_script_depends() {
		return [
			'wp-util',
			'select2',
			'jquery-serialize-object',
			'wc-price-slider',
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
			'section_title',
			[
				'label' => esc_html__( 'Products Filter', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'type',
			[
				'label' => __( 'Type', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'sidebar',
				'options' => array(
					'sidebar'    => __( 'Sidebar', 'glozin-addons' ),
					'horizontal' => esc_html__( 'Horizontal', 'glozin' ),
					'popup'      => esc_html__( 'Popup', 'glozin' ),
				),
			]
		);

		$this->add_control(
			'icon',
			[
				'label' => __( 'Icon', 'glozin-addons' ),
				'type' => Controls_Manager::ICONS,
				'default' => [],
				'condition' => [
					'type' => 'popup',
				],
			]
		);

		$this->add_control(
			'title',
			[
				'label' => esc_html__( 'Title', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Filter', 'glozin-addons' ),
				'condition' => [
					'type' => 'popup',
				],
			]
		);

		$this->add_control(
			'horizontal_columns',
			[
				'label' => __( 'Horizontal Columns', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => '5',
				'options' => array(
					'2'    => __( '2 Columns', 'glozin-addons' ),
					'3' => esc_html__( '3 Columns', 'glozin' ),
					'4'      => esc_html__( '4 Columns', 'glozin' ),
					'5'      => esc_html__( '5 Columns', 'glozin' ),
					'6'      => esc_html__( '6 Columns', 'glozin' ),
					'7'      => esc_html__( '7 Columns', 'glozin' ),
				),
				'selectors' => [
					'{{WRAPPER}} .glozin-products-filter--horizontal' => '--gz-filter-horizontal-columns: {{VALUE}};',
				],
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'name',
			[
				'label' => __( 'Filter Name', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
			]
		);

		$repeater->add_control(
			'source',
			[
				'label' => __( 'Filter By', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_filter_source_options(),
				'default' => 'products_group',
			]
		);

		$repeater->add_control(
			'attribute',
			[
				'label' => __( 'Attribute', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_filter_attribute_options(),
				'default' => '',
				'condition'   => [
					'source' => 'attribute',
				],
			]
		);

		$repeater->add_control(
			'display_price',
			[
				'label' => __( 'Display Type', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_filter_display_options('price'),
				'default' => 'ranges',
				'condition'   => [
					'source' => 'price',
				],
			]
		);

		$repeater->add_control(
			'display_attribute',
			[
				'label' => __( 'Display Type', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_filter_display_options('attribute'),
				'default' => 'auto',
				'condition'   => [
					'source' => 'attribute',
				],
			]
		);

		$repeater->add_control(
			'display_rating',
			[
				'label' => __( 'Display Type', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_filter_display_options('rating'),
				'default' => 'checkboxes',
				'condition'   => [
					'source' => 'rating',
				],
			]
		);

		$repeater->add_control(
			'display_default',
			[
				'label' => __( 'Display Type', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_filter_display_options('default'),
				'default' => 'checkboxes',
				'condition'   => [
					'source!' => ['price','attribute', 'rating'],
				],
			]
		);

		$repeater->add_control(
			'ranges',
			[
				'label' => __( 'Ranges', 'glozin-addons' ),
				'description' => __( 'Each range on a line, separate by the <code>-</code> symbol. Do not include the currency symbol.', 'glozin-addons' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => '',
				'condition'   => [
					'display_price' => 'ranges',
					'source'  => 'price',
				],
			]
		);

		$repeater->add_control(
			'multiple_attribute',
			[
				'label' => __( 'Selection Type', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => '0',
				'options' => array(
					0 => __( 'Single select', 'glozin-addons' ),
					1 => __( 'Multiple select', 'glozin-addons' ),
				),
				'condition' => array(
					'source'  => 'attribute',
					'display_attribute!' => ['dropdown'],
				),
			]
		);

		$repeater->add_control(
			'multiple_rating',
			[
				'label' => __( 'Selection Type', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => '0',
				'options' => array(
					0 => __( 'Single select', 'glozin-addons' ),
					1 => __( 'Multiple select', 'glozin-addons' ),
				),
				'condition' => array(
					'source'  => 'rating',
					'display_rating!' => ['dropdown'],
				),
			]
		);

		$repeater->add_control(
			'multiple_default',
			[
				'label' => __( 'Selection Type', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => '0',
				'options' => array(
					0 => __( 'Single select', 'glozin-addons' ),
					1 => __( 'Multiple select', 'glozin-addons' ),
				),
				'condition' => array(
					'source!'  => ['products_group', 'price', 'product_status', 'rating', 'attribute'],
					'display_default!' => ['dropdown'],
				),
			]
		);

		$repeater->add_control(
			'include_categories',
			[
				'label' => esc_html__( 'Include Categories', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'label_block' => true,
				'description' => esc_html__( 'IDs separated by commas. Eg: 1,2,3,..', 'glozin-addons' ),
				'condition' => [
					'source' => 'product_cat',
				],
			]
		);

		$repeater->add_control(
			'exclude_categories',
			[
				'label' => esc_html__( 'Exclude Categories', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'label_block' => true,
				'description' => esc_html__( 'IDs separated by commas. Eg: 1,2,3,..', 'glozin-addons' ),
				'condition' => [
					'source' => 'product_cat',
				],
			]
		);

		$repeater->add_control(
			'orderby_attribute',
			[
				'label' => __( 'Order By', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'title',
				'options' => array(
					'title' => __( 'Title', 'glozin-addons' ),
					'count' => __( 'Count', 'glozin-addons' ),
				),
				'condition' => array(
					'source' => 'attribute',
				),
			]
		);

		$repeater->add_control(
			'order_attribute',
			[
				'label' => __( 'Order', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'asc',
				'options' => array(
					'asc' => __( 'ASC', 'glozin-addons' ),
					'desc' => __( 'DESC', 'glozin-addons' ),
				),
				'condition' => array(
					'source' => 'attribute',
				),
			]
		);

		$repeater->add_control(
			'number_attribute',
			[
				'label' => __( 'Display Number', 'glozin-addons' ),
				'type' => Controls_Manager::NUMBER,
				'default' => '100',
				'min' => 0,
				'condition' => array(
					'source' => 'attribute',
				),
			]
		);

		$repeater->add_control(
			'query_type',
			[
				'label' => __( 'Query Type', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'and',
				'options' => array(
					'and' => __( 'AND', 'glozin-addons' ),
					'or' => __( 'OR', 'glozin-addons' ),
				),
				'condition' => array(
					'source' => 'attribute',
				),
			]
		);

		$repeater->add_control(
			'show_counts',
			[
				'label' => __( 'Show Product Counts', 'glozin-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => esc_html__( 'Yes', 'glozin-addons' ),
				'label_off' => esc_html__( 'No', 'glozin-addons' ),
				'condition' => array(
					'source!' => array( 'price', 'products_group', 'product_status' ),
				),
			]
		);

		$repeater->add_control(
			'searchable',
			[
				'label' => __( 'Show Search Box', 'glozin-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => esc_html__( 'Yes', 'glozin-addons' ),
				'label_off' => esc_html__( 'No', 'glozin-addons' ),
				'condition' => [
					'source!' => ['product_status', 'product_cat', 'rating', 'price' ],
				],
			]
		);

		$repeater->add_control(
			'scrollable',
			[
				'label' => __( 'Limit the height of items list (scrollable)', 'glozin-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => esc_html__( 'Yes', 'glozin-addons' ),
				'label_off' => esc_html__( 'No', 'glozin-addons' ),
				'condition' => array(
					'source!' => array( 'product_status', 'product_cat', 'rating', 'price' ),
				),
			]
		);

		$repeater->add_control(
			'show_children_only',
			[
				'label' => __( 'Only show children of the current category', 'glozin-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => esc_html__( 'Yes', 'glozin-addons' ),
				'label_off' => esc_html__( 'No', 'glozin-addons' ),
				'condition' => array(
					'source'  => array( 'product_cat' ),
					'display_default' => array( 'list', 'checkboxes' ),
					'multiple!' => '1',
				),
			]
		);

		$repeater->add_control(
			'hide_empty_cats',
			[
				'label' => __( 'Hide empty categories', 'glozin-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => esc_html__( 'Yes', 'glozin-addons' ),
				'label_off' => esc_html__( 'No', 'glozin-addons' ),
				'condition' => array(
					'source'  => array( 'product_cat' ),
				),
			]
		);

		$repeater->add_control(
			'show_view_more',
			[
				'label' => __( 'Show See More', 'glozin-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => esc_html__( 'Yes', 'glozin-addons' ),
				'label_off' => esc_html__( 'No', 'glozin-addons' ),
				'condition' => array(
					'source'  => array( 'product_cat' ),
					'display_default' => array( 'list', 'checkboxes' ),
				),
			]
		);

		$repeater->add_control(
			'cats_numbers',
			[
				'label' => __( 'Categories per view', 'glozin-addons' ),
				'type' => Controls_Manager::NUMBER,
				'default' => '6',
				'condition' => array(
					'source'  => array( 'product_cat' ),
					'display_default' => array( 'list', 'checkboxes' ),
					'show_view_more!' => '',
				),
			]
		);

		$repeater->add_control(
			'enable_onsale',
			[
				'label' => __( 'Enable Onsale', 'glozin-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__( 'Yes', 'glozin-addons' ),
				'label_off' => esc_html__( 'No', 'glozin-addons' ),
				'condition' => array(
					'source'  => array( 'product_status' ),
				),
			]
		);

		$repeater->add_control(
			'enable_instock',
			[
				'label' => __( 'Enable In Stock', 'glozin-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__( 'Yes', 'glozin-addons' ),
				'label_off' => esc_html__( 'No', 'glozin-addons' ),
				'condition' => array(
					'source'  => array( 'product_status' ),
				),
			]
		);

		$repeater->add_control(
			'enable_outofstock',
			[
				'label' => __( 'Enable Out Of Stock', 'glozin-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__( 'Yes', 'glozin-addons' ),
				'label_off' => esc_html__( 'No', 'glozin-addons' ),
				'condition' => array(
					'source'  => array( 'product_status' ),
				),
			]
		);

		$repeater->add_control(
			'hide_empty_brands',
			[
				'label' => __( 'Hide empty brands', 'glozin-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__( 'Yes', 'glozin-addons' ),
				'label_off' => esc_html__( 'No', 'glozin-addons' ),
				'condition' => array(
					'source'  => array( 'product_brand' ),
				),
			]
		);

		$repeater->add_control(
			'hide_empty_attributes',
			[
				'label' => __( 'Hide empty attributes', 'glozin-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__( 'Yes', 'glozin-addons' ),
				'label_off' => esc_html__( 'No', 'glozin-addons' ),
				'condition' => array(
					'source'  => array( 'attribute' ),
				),
			]
		);


		$this->add_control(
			'filters',
			[
				'label' => __( 'Filters', 'glozin-addons' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
				],
				'title_field' => '{{{ name }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_filter_additional',
			[
				'label' => __( 'Additional', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'ajax',
			[
				'label' => __( 'Use ajax for filtering', 'glozin-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__( 'Yes', 'glozin-addons' ),
				'label_off' => esc_html__( 'No', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'instant',
			[
				'label' => __( 'Filtering products instantly (no buttons required)', 'glozin-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => esc_html__( 'Yes', 'glozin-addons' ),
				'label_off' => esc_html__( 'No', 'glozin-addons' ),
				'condition' => array(
					'ajax' => 'yes',
				),
			]
		);

		$this->add_control(
			'change_url',
			[
				'label' => __( 'Update URL', 'glozin-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__( 'Yes', 'glozin-addons' ),
				'label_off' => esc_html__( 'No', 'glozin-addons' ),
				'condition' => array(
					'ajax' => 'yes',
				),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_filter_horizontal_style',
			[
				'label' => esc_html__( 'Filter Horizontal', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'type' => 'horizontal',
				],
			]
		);

		$this->add_responsive_control(
			'filter_horizontal_gap',
			[
				'label'     => esc_html__( 'Gap', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 600,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-products-filter--horizontal .products-filter__filters' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_filter_popup_button_style',
			[
				'label' => esc_html__( 'Filter Popup Button', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'type' => 'popup',
				],
			]
		);

		$this->register_button_style_controls( '', 'catalog-toolbar__filter-button', 'filter_popup' );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_filters_style',
			[
				'label' => esc_html__( 'Filter Item', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_responsive_control(
			'item_spacing_top',
			[
				'label'     => esc_html__( 'Spacing Top', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 600,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .products-filter-widget .filter-name' => 'padding-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_spacing_bottom',
			[
				'label'     => esc_html__( 'Spacing Bottom', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 600,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .products-filter-widget .filter-control' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_control(
			'item_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .products-filter-widget .filter' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_filter_name_style',
			[
				'label' => esc_html__( 'Filter Name', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'filter_name_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 600,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .products-filter-widget .filter-name' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'filter_name_color',
			[
				'label' => esc_html__( 'Text Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .products-filter-widget .filter-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'filter_name_typography',
				'selector' => '{{WRAPPER}} .products-filter-widget .filter-name',
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_filter_list_style',
			[
				'label' => esc_html__( 'Filter List', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'filter_list_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 600,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .products-filter-widget .filter-list li:not(:last-child)' => 'padding-bottom: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .products-filter-widget .filter-list li:not(:first-child)' => 'padding-top: calc({{SIZE}}{{UNIT}}/2);',
				],
			]
		);

		$this->add_control(
			'filter_list_color',
			[
				'label' => esc_html__( 'Text Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .products-filter-widget .filter-list li' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'filter_list_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .products-filter-widget .filter-list .products-filter__option-name:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .products-filter-widget .filter-list .products-filter__option-name:hover:after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'filter_list_typography',
				'selector' => '{{WRAPPER}} .products-filter-widget .filter-list .filter__option-name',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_filter_checkbox_style',
			[
				'label' => esc_html__( 'Filter Checkbox', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'filter_checkbox_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 600,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .products-filter-widget .products-filter--checkboxes .filter-checkboxes-item:not(:last-child)' => 'padding-bottom: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .products-filter-widget .products-filter--checkboxes .filter-checkboxes-item:not(:first-child)' => 'padding-top: calc({{SIZE}}{{UNIT}}/2);',
				],
			]
		);

		$this->add_control(
			'filter_checkbox_color',
			[
				'label' => esc_html__( 'Text Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .products-filter-widget .products-filter--checkboxes .products-filter__option-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'filter_checkbox_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .products-filter-widget .products-filter--checkboxes .products-filter__option-name:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'filter_checkbox_typography',
				'selector' => '{{WRAPPER}} .products-filter-widget .products-filter--checkboxes .products-filter__option-name',
			]
		);

		$this->add_control(
			'filter_checkbox_normal_heading',
			[
				'label' => esc_html__( 'Checkbox Normal', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'filter_checkbox_normal_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .products-filter-widget .products-filter--checkboxes .products-filter__option-name::before' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'filter_checkbox_hover_heading',
			[
				'label' => esc_html__( 'Checkbox Hover', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'filter_checkbox_hover_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .products-filter-widget .products-filter--checkboxes .products-filter__option-name:hover:before' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'filter_checkbox_selected_heading',
			[
				'label' => esc_html__( 'Checkbox Selected', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'filter_checkbox_selected_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .products-filter-widget .products-filter--checkboxes .selected > .products-filter__option-name::before' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'filter_checkbox_selected_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .products-filter-widget .products-filter--checkboxes .selected > .products-filter__option-name::before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'filter_checkbox_selected_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .products-filter-widget .products-filter--checkboxes .selected > .products-filter__option-name::after' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_filter_dropdown_style',
			[
				'label' => esc_html__( 'Filter Dropdown', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'filter_dropdown_color',
			[
				'label' => esc_html__( 'Text Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .select2-container--default .select2-selection--single .select2-selection__rendered' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'filter_dropdown_typography',
				'selector' => '{{WRAPPER}} .select2-container--default .select2-selection--single .select2-selection__rendered',
			]
		);

		$this->add_control(
			'filter_dropdown_normal_heading',
			[
				'label' => esc_html__( 'Dropdown Normal', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'filter_dropdown_normal_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} span.select2 .select2-selection--single' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'filter_dropdown_hover_heading',
			[
				'label' => esc_html__( 'Dropdown Hover', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'filter_dropdown_hover_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} span.select2 .select2-selection--single:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_filter_label_style',
			[
				'label' => esc_html__( 'Filter Swatch Label', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'filter_swatch_label_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 600,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .products-filter-widget .swatch-button, {{WRAPPER}} .products-filter-widget .swatch-label' => 'margin-bottom: {{SIZE}}{{UNIT}};margin-inline-end: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'filter_swatch_label_padding',
			[
				'label' => esc_html__( 'Padding', 'glozin-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .products-filter-widget .swatch-button, {{WRAPPER}} .products-filter-widget .swatch-label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'filter_swatch_label_typography',
				'selector' => '{{WRAPPER}} .products-filter-widget .swatch-button, {{WRAPPER}} .products-filter-widget .swatch-label',
			]
		);


		$this->add_control(
			'filter_swatch_label_normal_heading',
			[
				'label' => esc_html__( 'Normal', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'filter_swatch_label_normal_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .products-filter-widget .swatch-button, {{WRAPPER}} .products-filter-widget .swatch-label' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'filter_swatch_label_color',
			[
				'label' => esc_html__( 'Text Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .products-filter-widget .swatch-button, {{WRAPPER}} .products-filter-widget .swatch-label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'filter_swatch_label_hover_heading',
			[
				'label' => esc_html__( 'Hover', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'filter_swatch_label_hover_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .products-filter-widget .swatch-button:hover, {{WRAPPER}} .products-filter-widget .swatch-label:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'filter_swatch_label_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .products-filter-widget .swatch-button:hover, {{WRAPPER}} .products-filter-widget .swatch-label:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'filter_swatch_label_selected_heading',
			[
				'label' => esc_html__( 'Selected', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'filter_swatch_label_selected_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .products-filter-widget .swatch-button.selected, {{WRAPPER}} .products-filter-widget .swatch-label.selected' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'filter_swatch_label_selected_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .products-filter-widget .swatch-button.selected, {{WRAPPER}} .products-filter-widget .swatch-label.selected' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'filter_swatch_label_selected_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .products-filter-widget .swatch-button.selected, {{WRAPPER}} .products-filter-widget .swatch-label.selected' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_filter_swatch_color_style',
			[
				'label' => esc_html__( 'Filter Swatch Color', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'filter_swatch_color_padding',
			[
				'label' => esc_html__( 'Padding', 'glozin-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .products-filter-widget .swatch-color' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .products-filter-widget .products-filter--swatches.swatches-color' => 'margin-inline-start: -{{LEFT}}{{UNIT}}; margin-inline-end: -{{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'filter_swatch_color_typography',
				'selector' => '{{WRAPPER}} .products-filter-widget .swatch-color .name',
			]
		);


		$this->add_control(
			'filter_swatch_color_normal_heading',
			[
				'label' => esc_html__( 'Normal', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'filter_swatch_color_color',
			[
				'label' => esc_html__( 'Text Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .products-filter-widget .swatch-color' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'filter_swatch_color_hover_heading',
			[
				'label' => esc_html__( 'Hover', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'filter_swatch_color_hover_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .products-filter-widget .swatch-color:hover .bg-color:before' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'filter_swatch_color_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .products-filter-widget .swatch-color:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'filter_swatch_color_selected_heading',
			[
				'label' => esc_html__( 'Selected', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'filter_swatch_color_selected_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .products-filter-widget .swatch-color.selected .bg-color:before' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'filter_swatch_color_selected_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .products-filter-widget .swatch-color.selected' => 'color: {{VALUE}};',
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
		add_filter( 'glozin_navigation_bar_filter_elementor', '__return_true' );
		add_filter( 'glozin_navigation_bar_filter_sidebar_elementor', '__return_false' );

		// Get form action url.
		$form_action = wc_get_page_permalink( 'shop' );

		// CSS classes and settings.
		$classes = array();
		$options = array();
		if ( $settings['ajax'] == 'yes' ) {
			$classes[]              = 'ajax-filter';
			$options['ajax']       = true;
			$options['instant']    = $settings['instant'] == 'yes' ? true : false;
			$options['change_url'] = $settings['change_url'] == 'yes' ? true : false;

			if ( $options['instant'] ) {
				$classes[] = 'instant-filter';
			}
		}

		$classes[] = 'has-collapse';

		$panel_id = 'products-filter-sidebar-panel-' . rand(1, 1000);
		$filter_class = 'glozin-products-filter--' . $settings['type'];
		$panel_class = '';
		if( $settings['type'] == 'popup' ) {
			$panel_class = ' filter-sidebar-panel offscreen-panel offscreen-panel--side-right';
		}

		if( $settings['type'] == 'horizontal' ) {
			$filter_class .= ' catalog-filters-horizontal position-relative';
		}
			

		?><div class="products-filter-widget--elementor products-filter-widget glozin-products-filter hide-actived-filters <?php echo esc_attr( $filter_class ); ?>"><?php
		$icon = '';
		if($settings['icon'] && $settings['icon']['value']) {
			$icon = '<span class="glozin-svg-icon glozin-svg-icon--filter">' . \Elementor\Icons_Manager::try_get_icon_html( $settings['icon'], [ 'aria-hidden' => 'true' ] ) . '</span>';
		}

		$title = ! empty( $settings['title'] ) ? $settings['title'] : '';
		if( $settings['type'] == 'popup' ) {
			?><button class="catalog-toolbar__filter-button <?php echo ! empty($settings['filter_popup_button_style']) ? 'not-default gz-button-' . esc_attr($settings['filter_popup_button_style']) : ''; ?>" data-toggle="off-canvas" data-target="<?php echo esc_attr($panel_id); ?>"><?php echo $icon . esc_html( $title ); ?></button><?php
		}

		if ( ! empty( $settings['filters'] ) ) {
			?>
			<?php if( $settings['type'] == 'popup' ) : ?>
			<div class="glozin-products-filter__form catalog-filters-sidebar <?php echo esc_attr( $panel_class ); ?>" data-id="<?php echo esc_attr($panel_id); ?>">
				<div class="sidebar__backdrop"></div>
				<div class="sidebar__container glozin-products-filter__container">
					<?php echo \Glozin\Addons\Helper::get_svg( 'close', 'ui', ['class'=>'sidebar__button-close gz-button gz-button-icon gz-button-text position-absolute top-13 end-20 z-1'] ); ?>
					<div class="sidebar__header h5 m-0 px-30 py-23 position-relative border-bottom d-flex align-items-center justify-content-between">
						<?php echo esc_html__( 'Filter', 'glozin' ); ?>
					</div>
					<?php endif; ?>
					<div class="sidebar__content glozin-products-filter__content">
						<form action="<?php echo esc_url( $form_action ); ?>" method="get" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-settings="<?php echo esc_attr( json_encode( $options ) ); ?>">

							<?php
							$class_activated = empty( $this->get_current_filters() ) ? 'hidden' : '' ;
							?>
							<div class="products-filter__activated <?php echo esc_attr( $class_activated ); ?>">
								<div class="products-filter__activated-heading">
									<h6><?php echo esc_html__( 'Refine by', 'glozin-addons' ); ?></h6>
									<button type="reset" value="<?php echo esc_attr__( 'Clear All', 'glozin-addons' ); ?>" class="glozin-button--subtle alt reset-button"><?php echo esc_html__( 'Clear All', 'glozin-addons' ); ?></button>
								</div>
								<div class="products-filter__activated-items">
									<?php $this->activated_filters( $settings['filters'] ); ?>
								</div>
							</div>

							<div class="products-filter__filters filters">
								<?php foreach ( (array) $settings['filters'] as $index => $filter ) {
									$this->current_section = $index;
									$filter = $this->get_filter_display( $filter );
									$this->display_filter( $filter );
								}

								// Add hidden inputs of other filters.
								$this->hidden_filters( $settings['filters'] );

								// Add param post_type when the shop page is home page
								if ( trailingslashit( $form_action ) == trailingslashit( home_url() ) ) {
									echo '<input type="hidden" name="post_type" value="product">';
								} ?>

								<input type="hidden" name="filter" value="1">
							</div>
							<div class="products-filter__button d-flex align-items-center justify-content-between gap-15 z-3">
								<button type="submit" value="<?php echo esc_attr__( 'Filter', 'glozin-addons' ); ?>" class="button filter-button glozin-button--bg-color-black glozin-button--base"><?php echo esc_html__( 'Apply', 'glozin-addons' ); ?></button>
								<button type="reset" value="<?php echo esc_attr__( 'Reset Filter', 'glozin-addons' ); ?>" class="button gz-button-subtle alt reset-button fs-16"><?php echo esc_html__( 'Clear All', 'glozin-addons' ); ?></button>
							</div>

							<?php if ( $settings['ajax'] == 'yes' ) { ?>
								<span class="products-loader"><span class="spinner"></span></span>
							<?php } ?>
						</form>
					</div>
				<?php if( $settings['type'] == 'popup' ) : ?>
				</div>
			</div>
			<?php endif; ?>
			<?php
		}
		?>
		</div>
		<?php
	}

	/**
	 * Get current filter from the query string.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_current_filters() {
		// Cache the list of current filters in a property.
		if ( isset( $this->current_filters ) ) {
			return $this->current_filters;
		}

		$request = $_GET;
		$current_filters = array();

		if ( get_search_query() ) {
			$current_filters['s'] = get_search_query();

			if ( isset( $request['s'] ) ) {
				unset( $request['s'] );
			}
		}

		if ( isset( $request['paged'] ) ) {
			unset( $request['paged'] );
		}

		if ( isset( $request['filter'] ) ) {
			unset( $request['filter'] );
		}

		if ( isset( $request['orderby'] ) ) {
			unset( $request['orderby'] );
		}

		// Add chosen attributes to the list of current filter.
		if ( $_chosen_attributes = \WC_Query::get_layered_nav_chosen_attributes() ) {
			foreach ( $_chosen_attributes as $name => $data ) {
				$taxonomy_slug = wc_attribute_taxonomy_slug( $name );
				$filter_name   = 'filter_' . $taxonomy_slug;

				if ( ! empty( $data['terms'] ) ) {
					// We use pretty slug name instead of encoded version of WC.
					$terms = array_map( 'urldecode', $data['terms'] );

					// Should we stop joining array? This value is used as array in most situation (except for hidden_filters).
					$current_filters[ $filter_name ] = implode( ',', $terms );
				}

				if ( isset( $request[ $filter_name ] ) ) {
					unset( $request[ $filter_name ] );
				}

				if ( 'or' == $data['query_type'] ) {
					$query_type                     = 'query_type_' . $taxonomy_slug;
					$current_filters[ $query_type ] = 'or';

					if ( isset( $request[ $query_type ] ) ) {
						unset( $request[ $query_type ] );
					}
				}
			}
		}

		// Add taxonomy terms to the list of current filter.
		// This step is required because of the filter url is always the shop url.
		if ( is_product_taxonomy() ) {
			$taxonomy = get_queried_object()->taxonomy;
			$term     = get_query_var( $taxonomy );

			if ( taxonomy_is_product_attribute( $taxonomy ) ) {
				$taxonomy_slug = wc_attribute_taxonomy_slug( $taxonomy );
				$filter_name   = 'filter_' . $taxonomy_slug;

				if ( ! isset( $current_filters[ $filter_name ] ) ) {
					$current_filters[ $filter_name ] = $term;
				}
			} elseif ( ! isset( $current_filters[ $taxonomy ] ) ) {
				$current_filters[ $taxonomy ] = $term;
			}
		}

		foreach ( $request as $name => $value ) {
			$current_filters[ $name ] = $value;
		}

		$this->current_filters = $current_filters;

		return $this->current_filters;
	}

	/**
	 * Display hidden inputs of other filters from the query string.
	 *
	 * @since 1.0.0
	 *
	 * @param array $active_filters The active filters from $instace['filter'].
	 */
	public function hidden_filters( $active_filters ) {
		$current_filters = $this->get_current_filters();

		if( $active_filters ) {
			// Remove active filters from the list of current filters.
			foreach ( $active_filters as $filter ) {
				if ( 'slider' == $filter['display_price'] || 'ranges' == $filter['display_price'] ) {
					$min_name = 'min_' . $filter['source'];
					$max_name = 'max_' . $filter['source'];

					if ( isset( $current_filters[ $min_name ] ) ) {
						unset( $current_filters[ $min_name ] );
					}

					if ( isset( $current_filters[ $max_name ] ) ) {
						unset( $current_filters[ $max_name ] );
					}
				} else {
					$filter_name = 'attribute' == $filter['source'] ? 'filter_' . $filter['attribute'] : $filter['source'];
					$filter_name = 'rating' == $filter['source'] ? 'rating_filter' : $filter_name;

					if ( isset( $current_filters[ $filter_name ] ) ) {
						unset( $current_filters[ $filter_name ] );
					}

					if ( 'attribute' == $filter['source'] && isset( $current_filters['query_type_' . $filter['attribute']] ) ) {
						unset( $current_filters['query_type_' . $filter['attribute']] );
					}
				}
			}

		}

		if( $current_filters ) {
			foreach ( $current_filters as $name => $value ) {
				printf( '<input type="hidden" name="%s" value="%s">', esc_attr( $name ), esc_attr( $value ) );
			}
		}
	}

	/* Display the list of activated filter with the remove icon.
	*
	* @since 1.0.0
	*
	* @param array $active_filters
	*/
   public function activated_filters( $active_filters = array() ) {
	   $current_filters = $this->get_current_filters();

	   if ( empty( $current_filters ) ) {
		   return;
	   }

	   $list = array();

	   foreach ( $active_filters as $filter ) {
		   // For other filters.
		   $filter_name = 'attribute' == $filter['source'] ? 'filter_' . $filter['attribute'] : $filter['source'];
		   $filter_name = 'rating' == $filter['source'] ? 'rating_filter' : $filter_name;
		   $filter_name = 'price' == $filter['source'] ? 'min_price' : $filter_name;

		   if ( ! isset( $current_filters[ $filter_name ] ) ) {
			   continue;
		   }
		   $filter = $this->get_filter_display( $filter );
		   $terms = explode( ',', $current_filters[ $filter_name ] );

		   foreach ( $terms as $term ) {
			   switch ( $filter['source'] ) {
				   case 'products_group':
					   $options = $this->get_filter_options( $filter );
					   $text    = isset( $options[ $term ] ) ? $options[ $term ]['name'] : '';
					   break;

				   case 'price':
					   $price = wc_price($current_filters[ 'min_price' ]);
					   $max_price = isset($current_filters[ 'max_price' ]) ? wc_price($current_filters[ 'max_price' ]) : '';
					   if( $max_price ) {
						   $price .= ' - ' . $max_price;
					   } else {
						   $price .= ' +';
					   }
					   $list[] = sprintf(
						   '<a href="#" class="remove-filtered" data-name="price" data-value="%s">%s: %s</a>',
						   esc_attr( $price ),
						   esc_html__('Price', 'glozin-addons'),
						   $price,
					   );
					   $text = '';
					   break;

				   case 'rating':
					   $text = _n( 'Rated %d star', 'Rated %d stars', $term, 'glozin-addons' );
					   $text = sprintf( $text, $term );
					   break;

				   case 'attribute':
					   $attribute = get_term_by( 'slug', $term, 'pa_' . $filter['attribute'] );
					   $text      = $attribute->name;
					   break;

				   case 'product_status':
					   if ( 'outofstock' == $term ) {
						   $text = esc_html__( 'Out of stock', 'glozin-addons' );
					   } elseif ( 'instock' == $term ) {
						   $text = esc_html__( 'In stock', 'glozin-addons' );
					   } else {
						   $text = esc_html__( 'On sale', 'glozin-addons' );
					   }
					   break;

				   default:
					   if ( ! taxonomy_exists( $filter['source'] ) ) {
						   break;
					   }

					   $term_object = get_term_by( 'slug', $term, $filter['source'] );
					   $text        =  $term_object ? $term_object->name : '';
					   break;
			   }

			   if ( ! empty( $text ) ) {
				   $list[] = sprintf(
					   '<a href="#" class="remove-filtered" data-name="%s" data-value="%s" rel="nofollow" aria-label="%s">%s</a>',
					   esc_attr( $filter_name ),
					   esc_attr( $term ),
					   esc_attr__( 'Remove filter', 'glozin-addons' ),
					   $text,
				   );
			   }
		   }

		   // Delete to avoid duplicating.
		   unset( $current_filters[ $filter_name ] );
	   }

	   if ( ! empty( $list ) ) {
		   echo implode( '', $list );
	   }
   }

   /**
	 * Display a single filter
	 *
	 * @since 1.0.0
	 *
	 * @param array $filter
	 */
	public function display_filter( $filter ) {
		$this->active_fields = isset( $this->active_fields ) ? $this->active_fields : array();

		// Filter name.
		if ( 'attribute' == $filter['source'] ) {
			$filter_name = 'filter_' . $filter['attribute'];
		} elseif ( 'rating' == $filter['source'] ) {
			$filter_name = 'rating_filter';
		} else {
			$filter_name = $filter['source'];
		}

		$filter = wp_parse_args( $filter, array(
			'name'        => '',
			'source'      => 'price',
			'display'     => 'slider',
			'attribute'   => '',
			'orderby_attribute' => 'title',
			'order_attribute' => 'asc',
			'number_attribute' => 0,
			'query_type'  => 'and', // Use for attribute only
			'multiple'    => false, // Use for attribute only
			'searchable'  => false,
			'show_counts' => false,
		) );

		$filter['show_children_only'] = $filter['show_children_only'] == 'yes' ? true : false;

		$options = $this->get_filter_options( $filter );

		// Stop if no options to show.
		if ( 'slider' != $filter['display'] && empty( $options ) ) {
			return;
		}

		$current_filters = $this->get_current_filters();
		$args            = array(
			'name'        			=> $filter_name,
			'current'     			=> array(),
			'options'     			=> $options,
			'multiple'    			=> absint( $filter['multiple'] ),
			'show_counts' 			=> $filter['show_counts'],
			'display_type' 			=> $filter['display'],
			'source' 	   			=> $filter['source'],
		);

		if ( ! empty( $filter['show_children_only'] ) && $filter['source'] == 'product_cat' && in_array( $filter['display'], array( 'list', 'checkboxes' ) ) ) {
			$args['show_children_only'] = true;
		}

		// Add custom arguments.
		if ( 'attribute' == $filter['source'] ) {
			$attr = $this->get_tax_attribute( $filter['attribute'] );

			// Stop if attribute isn't exists.
			if ( ! $attr ) {
				return;
			}

			$args['all']        = sprintf( esc_html__( 'Any %s', 'glozin-addons' ), wc_attribute_label( $attr->attribute_label ) );
			$args['type']       = $attr->attribute_type;
			$args['query_type'] = $filter['query_type'];
			$args['attribute']  = $filter['attribute'];
			$args['number_attribute'] = isset( $filter['number_attribute'] ) ? intval( $filter['number_attribute'] ) : 0;

			// Auto-convert select to button.
			if ( 'select' == $args['type'] && class_exists( '\WCBoost\VariationSwatches\Helper' ) ) {
				$args['type'] = wc_string_to_bool( \WCBoost\VariationSwatches\Helper::get_settings( 'auto_button' ) ) ? 'button' : 'select';
			}

			if( $filter['order_attribute'] == 'asc' ) {
				if( $filter['orderby_attribute'] == 'count' ) {
					uasort($args['options'], function($a, $b) {
						return strcasecmp($a['count'], $b['count']);
					});
				} else {
					uasort($args['options'], function($a, $b) {
						return strcasecmp($a['name'], $b['name']);
					});
				}
			} else {
				if( $filter['orderby_attribute'] == 'count' ) {
					uasort($args['options'], function($a, $b) {
						return strcasecmp($b['count'], $a['count']);
					});
				} else {
					uasort($args['options'], function($a, $b) {
						return strcasecmp($b['name'], $a['name']);
					});
				}
			}

			if( !empty($filter['number_attribute']) && intval($filter['number_attribute']) > 0 ) {
				$args['options'] = array_slice( $args['options'], 0, intval($filter['number_attribute']) );
			}
		} elseif ( taxonomy_exists( $filter['source'] ) ) {
			$taxonomy    = get_taxonomy( $filter['source'] );
			$args['all'] = sprintf( esc_html__( 'Select a %s', 'glozin-addons' ), $taxonomy->labels->singular_name );
		} else {
			$args['all'] = esc_html__( 'All Products', 'glozin-addons' );
		}

		// Correct the "current" argument.
		if ( 'slider' == $filter['display'] || 'ranges' == $filter['display'] ) {
			$args['current']['min'] = isset( $current_filters[ 'min_' . $filter_name ] ) ? $current_filters[ 'min_' . $filter_name ] : '';
			$args['current']['max'] = isset( $current_filters[ 'max_' . $filter_name ] ) ? $current_filters[ 'max_' . $filter_name ] : '';
		} elseif ( isset( $current_filters[ $filter_name ] ) ) {
			$args['current'] = explode( ',', $current_filters[ $filter_name ] );
		}

		// Only apply multiple select to attributes.
		if ( in_array( $filter['source'], array( 'products_group', 'price' ) ) || in_array( $filter['display'], array( 'slider', 'dropdown' ) ) ) {
			$args['multiple'] = false;
		}

		// Button view more
		$view_more = '';
		if ( ! empty( $filter['show_view_more'] ) && empty( $filter['scrollable'] ) && in_array( $filter['display'], array( 'list', 'checkboxes' ) ) ) {
			$view_more = sprintf(
				'<div class="glozin-widget-product-cats-btn">
					<span class="show-more">%s</span>
					<span class="show-less">%s</span>
					<input type="hidden" class="widget-cat-numbers" value="%s">
				</div>',
				esc_html__( 'See More', 'glozin-addons' ),
				esc_html__( 'Less More', 'glozin-addons' ),
				$filter['cats_numbers']
			);
		}

		// Update the active fields.
		$this->active_fields[ $filter_name ] = isset( $current_filters[ $filter_name ] ) ? $current_filters[ $filter_name ] : $args['current'];

		// CSS classes.
		$classes   = array( 'products-filter__filter', 'filter' );
		$classes[] = ! empty( $args['name'] ) ? urldecode( sanitize_title( $args['name'], '', 'query' ) ) : '';
		$classes[] = $filter['source'];
		$classes[] = $filter['display'];
		$classes[] = 'attribute' == $filter['source'] ? $filter['attribute'] : '';
		$classes[] = $args['multiple'] ? 'multiple' : '';
		$classes[] = $filter['display'] == 'dropdown' ? 'glozin-skin--base' : '';
		$classes[] = ! empty( $args['searchable'] ) && ! in_array( $filter['source'], array( 'product_status', 'product_cat', 'rating' ) ) ? 'products-filter--searchable' : '';
		$classes[] = in_array( $filter['display'], array( 'list', 'checkboxes' ) ) ? 'products-filter--collapsible' : '';
		$classes[] = ! empty( $filter['scrollable'] ) && empty( $filter['show_view_more'] ) && in_array( $filter['display'], array( 'auto', 'list', 'checkboxes' ) ) && ! in_array( $filter['source'], array( 'product_status', 'product_cat', 'rating' ) ) ? 'products-filter--scrollable' : '';
		$classes[] = ! empty( $filter['show_counts'] ) && $filter['display'] == 'list' ? 'products-filter--counts' : '';
		$classes[] = ! empty( $filter['show_view_more'] ) && empty( $filter['scrollable'] ) && in_array( $filter['display'], array( 'list', 'checkboxes' ) ) ? 'products-filter--view-more' : '';
		$classes[] = ! empty( $filter['show_children_only'] ) && ( $filter['source'] == 'product_cat' ) && ( $filter['multiple'] == 0 ) && in_array( $filter['display'], array( 'list', 'checkboxes' ) ) ? 'products-filter--show-children-only' : '';
		$classes = array_filter( $classes );

		?>

		<div class="<?php echo esc_attr( join( ' ', $classes ) ) ?>">
			<?php if ( ! empty( $filter['name'] ) ) : ?>
				<span class="products-filter__filter-name filter-name h6 text-dark d-flex align-items-center justify-content-between gap-20 position-relative w-100 m-0 pb-20">
					<?php echo apply_filters( 'wpml_translate_single_string', $filter['name'], 'Widgets', 'products filter - section ' . ( $this->current_section + 1 ) ); ?>
				</span>
			<?php endif; ?>

			<div class="products-filter__filter-control filter-control">
				<?php
				if ( $filter['searchable'] && ! in_array( $filter['display'], array( 'auto', 'slider', 'ranges' ) ) && ! in_array( $filter['source'], array( 'stock', 'product_cat', 'rating' ) ) ) {
					$this->filter_search_box( $filter );
				}

				switch ( $filter['display'] ) {
					case 'slider':
						ob_start();
						the_widget( 'WC_Widget_Price_Filter' );
						$html = ob_get_clean();
						$html = preg_replace( '/<form[^>]*>(.*?)<\/form>/msi', '$1', $html );
						echo $html;
						break;

					case 'ranges':
						$this->display_ranges( $args );
						break;

					case 'dropdown':
						$this->display_dropdown( $args );
						break;

					case 'list':
						$this->display_list( $args );
						echo $view_more;
						break;

					case 'h-list':
						$args['flat'] = true;

						$this->display_list( $args );
						break;

					case 'checkboxes':
						$this->display_checkboxes( $args );
						echo $view_more;
						break;

					case 'auto':
						$this->display_auto( $args );
						break;

					default:
						$this->display_dropdown( $args );
						break;
				}
				?>
			</div>
		</div>

		<?php
	}

	/**
	 * Get filter options
	 *
	 * @since 1.0.0
	 *
	 * @param array $filter
	 *
	 * @return array
	 */
	protected function get_filter_options( $filter ) {
		$options = array();

		switch ( $filter['source'] ) {
			case 'price':
				// Use the default price slider widget.
				if ( empty( $filter['ranges'] ) ) {
					break;
				}

				$ranges = explode( "\n", $filter['ranges'] );

				foreach ( $ranges as $range ) {
					$range       = trim( $range );
					$prices      = explode( '-', $range );
					$price_range = array( 'min' => '', 'max' => '' );
					$name        = array();

					if ( count( $prices ) > 1 ) {
						$price_range['min'] = preg_match( '/\d+\.?\d+/', current( $prices ), $match ) ? floatval( $match[0] ) : 0;
						$price_range['max'] = preg_match( '/\d+\.?\d+/', end( $prices ), $match ) ? floatval( $match[0] ) : 0;
						reset( $prices );
						$name['min'] = preg_replace( '/\d+\.?\d+/', '<span class="price">' . wc_price( $price_range['min'] ) . '</span>', current( $prices ) );
						$name['max'] = preg_replace( '/\d+\.?\d+/', '<span class="price">' . wc_price( $price_range['max'] ) . '</span>', end( $prices ) );
					} elseif ( substr( $range, 0, 1 ) === '<' ) {
						$price_range['max'] = preg_match( '/\d+\.?\d+/', end( $prices ), $match ) ? floatval( $match[0] ) : 0;
						$name['max'] = preg_replace( '/\d+\.?\d+/', '<span class="price">' . wc_price( $price_range['max'] ) . '</span>', ltrim( end( $prices ), '< ' ) );
					} else {
						$price_range['min'] = preg_match( '/\d+\.?\d+/', current( $prices ), $match ) ? floatval( $match[0] ) : 0;
						$name['min'] = preg_replace( '/\d+\.?\d+/', '<span class="price">' . wc_price( $price_range['min'] ) . '</span>', current( $prices ) );
					}

					$options[] = array(
						'name'  => implode( ' - ', $name ),
						'count' => 0,
						'range' => $price_range,
						'level' => 0,
					);
				}
				break;

			case 'attribute':
				$taxonomy = wc_attribute_taxonomy_name( $filter['attribute'] );
				$query_type = isset( $filter['query_type'] ) ? $filter['query_type'] : 'and';

				if ( ! taxonomy_exists( $taxonomy ) ) {
					break;
				}

				$hide_empty = isset( $filter['hide_empty_attributes'] ) && $filter['hide_empty_attributes'] == 'yes' ? true : false;

				$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => $hide_empty ) );

				if ( 0 === count( $terms ) && $hide_empty ) {
					break;
				}

				$term_counts = $this->get_filtered_term_product_counts( wp_list_pluck( $terms, 'term_id' ), $taxonomy, $query_type );
				$_chosen_attributes = \WC_Query::get_layered_nav_chosen_attributes();

				foreach ( $terms as $term ) {
					$current_values = isset( $_chosen_attributes[ $taxonomy ]['terms'] ) ? $_chosen_attributes[ $taxonomy ]['terms'] : array();
					$option_is_set  = in_array( $term->slug, $current_values, true );
					$count          = isset( $term_counts[ $term->term_id ] ) ? $term_counts[ $term->term_id ] : 0;

					// Only show options with count > 0.
					if ( 0 === $count && ! $option_is_set && $hide_empty ) {
						continue;
					}

					$slug = urldecode( $term->slug );

					$options[ $slug ] = array(
						'name'  => $term->name,
						'count' => $count,
						'id'    => $term->term_id,
						'level' => 0,
					);
				}
				break;

			case 'products_group':
				$filter_groups = apply_filters( 'glozin_products_filter_groups', array(
					'best_selling' => esc_attr__( 'Best Sellers', 'glozin-addons' ),
					'new'          => esc_attr__( 'New Products', 'glozin-addons' ),
					'sale'         => esc_attr__( 'Sale Products', 'glozin-addons' ),
					'featured'     => esc_attr__( 'Hot Products', 'glozin-addons' ),
				) );

				if ( 'dropdown' != $filter['display'] ) {
					$options[''] = array(
						'name'  => esc_attr__( 'All Products', 'glozin-addons' ),
						'count' => 0,
						'id'    => 0,
						'level' => 0,
					);
				}
				foreach ( $filter_groups as $group_name => $group_label ) {
					$options[ $group_name ] = array(
						'name'  => $group_label,
						'count' => 0,
						'id'    => 0,
						'level' => 0,
					);
				}
				break;

			case 'rating':
				for ( $rating = 5; $rating >= 1; $rating-- ) {
					$count = $this->get_filtered_rating_product_count( $rating );

					if ( empty( $count ) ) {
						continue;
					}

					$rating_html = '<span class="star-rating">' . wc_get_star_rating_html( $rating ) . '</span>';

					$options[ $rating ] = array(
						'name'  => $rating_html,
						'count' => $count,
						'id'    => $rating,
						'level' => 0,
					);
				}
				break;

			case 'product_status':
				if(isset($filter['enable_onsale'])) {
					$options[ 'on_sale' ] = array(
						'name'  => esc_html__( 'On sale', 'glozin-addons' ),
						'count' => 0,
						'id'    => 'on_sale',
						'level' => 0,
					);
				}

				if(isset($filter['enable_instock'])) {
					$options[ 'instock' ] = array(
						'name'  => esc_html__( 'In stock', 'glozin-addons' ),
						'count' => 0,
						'id'    => 'instock',
						'level' => 0,
					);
				}

				if(isset($filter['enable_outofstock'])) {
					$options[ 'outofstock' ] = array(
						'name'  => esc_html__( 'Out of stock', 'glozin-addons' ),
						'count' => 0,
						'id'    => 'outofstock',
						'level' => 0,
					);
				}

				break;

			default:
				$taxonomy = $filter['source'];

				if ( ! taxonomy_exists( $taxonomy ) ) {
					break;
				}

				$current_filters = $this->get_current_filters();
				$current = ! empty( $current_filters[ $taxonomy ] ) ? explode( ',', $current_filters[ $taxonomy ] ) : array();
				$ancestors = array();

				foreach ( $current as $term_slug ) {
					$term = get_term_by( 'slug', $term_slug, $taxonomy );
					if ( $term ) {
						$ancestors = array_merge( $ancestors, get_ancestors( $term->term_id, $taxonomy ) );
					}
				}

				if( $taxonomy === 'product_cat' ) {
					$hide_empty = isset( $filter['hide_empty_cats'] ) ? $filter['hide_empty_cats'] : false;
				} else if( $taxonomy === 'product_brand' ) {
					$hide_empty = isset( $filter['hide_empty_brands'] ) && $filter['hide_empty_brands'] == 'yes' ? true : false;
				} else {
					$hide_empty = true;
				}

				$show_children_only = $taxonomy === 'product_cat' && in_array( $filter['display'], array( 'list', 'checkboxes' ) ) && isset( $filter['show_children_only'] ) ? $filter['show_children_only'] : false;
				if ( $taxonomy == 'product_cat' && '0' == $filter['multiple'] && ('list' == $filter['display'] || 'checkboxes' == $filter['display'] ) ) {
					$terms = $this->display_subs_categories( $show_children_only, $hide_empty );

					if ( $show_children_only && is_tax( 'product_cat' ) ) {
						$options[''] = array(
							'name'  => esc_attr__( 'All Categories', 'glozin-addons' ),
							'count' => wp_count_posts( 'product' )->publish,
							'id'    => 0,
							'level' => 0,
							'all_items' => true
						);
					}
				} else {
					$terms = \Glozin\Addons\Helper::get_terms_hierarchy( $taxonomy, '', $hide_empty );
				}
				$query_type  = isset( $filter['query_type'] ) ? $filter['query_type'] : 'and';
				$term_counts = $this->get_filtered_term_product_counts( wp_list_pluck( $terms, 'term_id' ), $taxonomy, $query_type );

				foreach ( $terms as $term ) {
					$count = isset( $term_counts[ $term->term_id ] ) ? $term_counts[ $term->term_id ] : 0;
					//Only show options with count > 0.
					if( $taxonomy === 'product_cat'  ) {
						if(  0 === $count && $hide_empty) {
							continue;
						}

						if( ! empty( $filter['include_categories'] ) ) {
							if( ! in_array( $term->term_id, explode( ',', $filter['include_categories'] ) ) ) {
								continue;
							}
						}

						if( ! empty( $filter['exclude_categories'] ) ) {
							if( in_array( $term->term_id, explode( ',', $filter['exclude_categories'] ) ) ) {
								continue;
							}
						}
					} else if( $taxonomy === 'product_brand' ) {
						if(  0 === $count && $hide_empty) {
							continue;
						}
					} elseif(0 === $count ) {
						continue;
					}

					$slug = urldecode( $term->slug );

					$options[ $slug ] = array(
						'name'  => $term->name,
						'count' => $count,
						'id'    => $term->term_id,
						'level' => isset( $term->depth ) ? $term->depth : 0,
						'has_children' => $term->has_children,
						'is_current_ancestor' => in_array( $term->term_id, $ancestors ),
					);
				}
				break;
		}

		return $options;
	}

	/**
	 * Count products of a rating after other filters have occurred by adjusting the main query.
	 *
	 * @since 1.0.0
	 *
	 * @see WC_Widget_Rating_Filter->get_filtered_product_count
	 *
	 * @param  int $rating Rating.
	 *
	 * @return int
	 */
	protected function get_filtered_rating_product_count( $rating ) {
		global $wpdb;

		$tax_query  = \WC_Query::get_main_tax_query();
		$meta_query = \Glozin\Addons\Elementor\Builder\Helper::is_catalog() ? \WC_Query::get_main_meta_query() : false;

		// Unset current rating filter.
		foreach ( $tax_query as $key => $query ) {
			if ( ! empty( $query['rating_filter'] ) ) {
				unset( $tax_query[ $key ] );
				break;
			}
		}

		// Set new rating filter.
		$product_visibility_terms = wc_get_product_visibility_term_ids();
		$tax_query[]              = array(
			'taxonomy'      => 'product_visibility',
			'field'         => 'term_taxonomy_id',
			'terms'         => $product_visibility_terms[ 'rated-' . $rating ],
			'operator'      => 'IN',
			'rating_filter' => true,
		);

		$meta_query     = new \WP_Meta_Query( $meta_query );
		$tax_query      = new \WP_Tax_Query( $tax_query );
		$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
		$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );

		$sql = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) FROM {$wpdb->posts} ";
		$sql .= $tax_query_sql['join'] . $meta_query_sql['join'];
		$sql .= " WHERE {$wpdb->posts}.post_type = 'product' AND {$wpdb->posts}.post_status = 'publish' ";
		$sql .= $tax_query_sql['where'] . $meta_query_sql['where'];

		if( \Glozin\Addons\Elementor\Builder\Helper::is_catalog() ) {
			$search = \WC_Query::get_main_search_query_sql();
			if ( $search ) {
				$sql .= ' AND ' . $search;
			}
		}

		return absint( $wpdb->get_var( $sql ) ); // WPCS: unprepared SQL ok.
	}

	/**
	 * Get attribute's properties
	 *
	 * @since 1.0.0
	 *
	 * @param string $attribute
	 *
	 * @return object
	 */
	protected function get_tax_attribute( $attribute_name ) {
		$attribute_slug     = wc_attribute_taxonomy_slug( $attribute_name );
		$taxonomies         = wc_get_attribute_taxonomies();
		$attribute_taxonomy = wp_list_filter( $taxonomies, [ 'attribute_name' => $attribute_slug ] );
		$attribute_taxonomy = ! empty( $attribute_taxonomy ) ? array_shift( $attribute_taxonomy ) : null;

		return $attribute_taxonomy;
	}


	/**
	 * Print HTML of product categories
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function display_subs_categories( $show_children_only, $hide_empty ) {
		$cat_ancestors = 0;
		if ( $show_children_only && is_tax( 'product_cat' ) ) {
			global $wp_query;
			$current_cat   = $wp_query->queried_object;
			$parent_terms = array();
			if( $current_cat->parent != 0 ) {
				$cat_ancestors = get_ancestors( $current_cat->term_id, 'product_cat' );
				if( $cat_ancestors ) {
					$parent_terms = get_terms(
						array(
							'taxonomy' => 'product_cat',
							'include'   => $cat_ancestors,
							'hierarchical' => true,
							'hide_empty'   => $hide_empty,
						)
					);

				}

			}

			$term_current = get_terms(
				array(
					'taxonomy' => 'product_cat',
					'include'   => array($current_cat->term_id),
					'hierarchical' => true,
					'hide_empty'   => $hide_empty,
				)
			);

			$terms = array_merge(
				$term_current,
				$parent_terms,
				get_terms(
					array(
						'taxonomy' => 'product_cat',
						'parent'       => $current_cat->term_id,
						'hierarchical' => true,
						'hide_empty'   => $hide_empty,
					)
				)
			);

		} else {
			$atts = array(
				'taxonomy' => 'product_cat',
				'hierarchical' => true,
				'hide_empty'   => $hide_empty,
			);

			if ( $show_children_only ) {
				$atts['parent'] = 0;
			}

			$terms = get_terms( $atts );
		}

		$terms = \Glozin\Addons\Helper::sort_terms_hierarchy( $terms);
		$terms = \Glozin\Addons\Helper::flatten_hierarchy_terms( $terms, '' );

		return $terms;
	}

	/**
	 * Add a search box on top of terms
	 *
	 * @since 1.0.0
	 *
	 * @param array $filter
	 */
	protected function filter_search_box( $filter ) {
		if ( 'attribute' == $filter['source'] ) {
			$attributes  = $this->get_filter_attribute_options();
			$placeholder = __( 'Search', 'glozin-addons' ) . ' ' . strtolower( $attributes[ $filter['attribute'] ] );
		} else {
			$sources     = $this->get_filter_source_options();
			$placeholder = __( 'Search', 'glozin-addons' ) . ' ' . strtolower( $sources[ $filter['source'] ] );
		}

		if ( 'dropdown' == $filter['display'] ) {
			printf(
				'<span class="products-filter__search-box screen-reader-text">%s</span>',
				esc_attr( $placeholder )
			);
		} else {
			printf(
				'<input type="text" class="products-filter__search-box glozin-input--base" placeholder="%s" >',
				esc_attr( $placeholder )
			);
		}
	}

	/**
	 * Print HTML of ranges
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function display_ranges( $args ) {
		$args = wp_parse_args( $args, array(
			'name'        => '',
			'current'     => array(),
			'options'     => array(),
			'attribute'   => '',
			'multiple'    => false,
			'show_counts' => false,
		) );

		if ( empty( $args['options'] ) ) {
			return;
		}

		echo '<ul class="products-filter__options products-filter--ranges products-filter--checkboxes filter-ranges">';
		foreach ( $args['options'] as $option ) {
			printf(
				'<li class="products-filter__option filter-ranges__item filter-checkboxes-item d-flex flex-wrap align-items-center py-9 %s" data-value="%s"><span class="products-filter__option-name name">%s</span>%s</li>',
				$args['current']['min'] == $option['range']['min'] && $args['current']['max'] == $option['range']['max'] ? 'selected' : '',
				esc_attr( json_encode( $option['range'] ) ),
				$option['name'],
				$args['show_counts'] ? '<span class="products-filter__count counter">' . $option['count'] . '</span>' : ''
			);

		}
		echo '</ul>';

		echo '<div class="product-filter-box">';

		printf(
			'<input type="number" name="min_%s" value="%s" placeholder="%s" class="glozin-input--base">',
			esc_attr( $args['name'] ),
			esc_attr( $args['current']['min'] ),
			esc_html__( 'Min', 'glozin-addons' )
		);

		echo '<span class="line"></span>';

		printf(
			'<input type="number" name="max_%s" value="%s" placeholder="%s" class="glozin-input--base">',
			esc_attr( $args['name'] ),
			esc_attr( $args['current']['max'] ),
			esc_html__( 'Max', 'glozin-addons' )
		);

		echo '<button type="submit" value="' . esc_attr__( 'Apply', 'glozin-addons' ) . '" class="button filter-button glozin-button--bg-color-black glozin-button-range">' . esc_html__( 'Apply', 'glozin-addons' ) . '</button>';

		echo '</div>';
	}

	/**
	 * Print HTML of list
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function display_list( $args ) {
		$args = wp_parse_args( $args, array(
			'name'        			=> '',
			'current'     			=> array(),
			'options'     			=> array(),
			'attribute'   			=> '',
			'multiple'    			=> false,
			'show_counts' 			=> false,
			'flat'        			=> false,
		) );

		if ( empty( $args['options'] ) ) {
			return;
		}

		$current_level = 0;
		$counter = 0;

		echo '<ul class="filter-options products-filter__options products-filter--list filter-list">';
		foreach ( $args['options'] as $slug => $option ) {
			$class = in_array( $slug, (array) $args['current'] ) ? 'selected' : '';

			if ( ! $args['flat'] && in_array( $slug, (array) $args['current'] ) ) {
				$class .= ' active';
			}

			if ( ! $args['flat'] && ! empty( $option['is_current_ancestor'] ) ) {
				$class .= ' current-term-parent active';
			}

			if ( $option['level'] == $current_level || $args['flat'] ) {
				echo $counter ? '</li>' : '';
			} elseif ( $option['level'] > $current_level ) {
				echo '<ul class="children">';
			} elseif ( $option['level'] < $current_level ) {
				echo str_repeat( '</li></ul>', $current_level - $option['level'] );
				echo '</li>';
			}

			printf(
				'<li class="products-filter__option filter-list-item %s" data-value="%s"><span class="products-filter__option-name name">%s</span>%s%s',
				esc_attr( $class ),
				esc_attr( $slug ),
				wp_kses_post( str_replace( "&nbsp;", '', $option['name'] ) ),
				$args['show_counts'] ? '<span class="products-filter__count counter">' . $option['count'] . '</span>' : '',
				! empty( $option['has_children'] )   ? '<span class="products-filter__option-toggler" aria-hidden="true"></span>' : ''
			);

			$current_level = $option['level'];
			$counter++;
		}

		if ( $args['flat'] ) {
			echo '</li></ul>';
		} else {
			echo str_repeat( '</li></ul>', $current_level + 1 );
		}

		printf(
			'<input type="hidden" name="%s" value="%s" %s>',
			esc_attr( $args['name'] ),
			esc_attr( implode( ',', $args['current'] ) ),
			empty( $args['current'] ) ? 'disabled' : ''
		);

		if ( $args['attribute'] && $args['multiple'] && 'or' == $args['query_type'] ) {
			printf(
				'<input type="hidden" name="query_type_%s" value="or" %s>',
				esc_attr( $args['attribute'] ),
				empty( $args['current'] ) ? 'disabled' : ''
			);
		}
	}

	/**
	 * Print HTML of checkboxes
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function display_checkboxes( $args ) {
		$args = wp_parse_args( $args, array(
			'name'        => '',
			'current'     => array(),
			'options'     => array(),
			'attribute'   => '',
			'multiple'    => '',
			'show_counts' => false,
		) );

		if ( empty( $args['options'] ) ) {
			return;
		}

		$current_level = 0;
		$counter = 0;

		echo '<ul class="products-filter__options products-filter--checkboxes filter-checkboxes">';
		foreach ( $args['options'] as $slug => $option ) {
			$class = in_array( $slug, (array) $args['current'] ) ? 'selected active' : '';
			$option['level'] = isset( $option['level'] ) ? $option['level'] : 0;

			if ( ! empty( $option['is_current_ancestor'] ) ) {
				$class .= ' current-term-parent active';
			}

			if ( ! empty( $option['has_children'] ) ) {
				$class .= ' has-children';
			}

			if ( $option['level'] == $current_level ) {
				echo $counter ? '</li>' : '';
			} elseif ( $option['level'] > $current_level ) {
				echo '<ul class="children">';
			} elseif ( $option['level'] < $current_level ) {
				echo str_repeat( '</li></ul>', $current_level - $option['level'] );
				echo '</li>';
			}

			if($args['name'] == 'product_status') {
				if( $option['count'] == 0 ) {
					$class .= ' product-status-disabled';
				}
			}

			printf(
				'<li class="products-filter__option filter-checkboxes-item d-flex flex-wrap align-items-center py-9 %s" data-value="%s"><span class="products-filter__option-name name">%s</span>%s%s%s%s',
				esc_attr( $class ),
				esc_attr( $slug ),
				$args['name'] == 'rating_filter' ? $option['name'] : wp_kses_post( str_replace( "&nbsp;", '', $option['name'] ) ),
				$args['source'] == 'rating' ? '<span class="number">' . esc_attr( $slug ) . '</span>' : '',
				$args['source'] == 'rating' && $slug < 5 ? '<span class="text">' . esc_html__( '& Up', 'glozin-addons' ) . '</span>' : '',
				$args['show_counts'] ? '<span class="products-filter__count counter">' . $option['count'] . '</span>' : '',
				! empty( $option['has_children'] ) ? '<span class="products-filter__option-toggler" aria-hidden="true"></span>' : ''
			);

			$current_level = $option['level'];
			$counter++;
		}

		echo str_repeat( '</li></ul>', $current_level + 1 );

		printf(
			'<input type="hidden" name="%s" value="%s" %s>',
			esc_attr( $args['name'] ),
			esc_attr( implode( ',', $args['current'] ) ),
			empty( $args['current'] ) ? 'disabled' : ''
		);

		if ( $args['attribute'] && $args['multiple'] && 'or' == $args['query_type'] ) {
			printf(
				'<input type="hidden" name="query_type_%s" value="or" %s>',
				esc_attr( $args['attribute'] ),
				empty( $args['current'] ) ? 'disabled' : ''
			);
		}
	}

	/**
	 * Print HTML of dropdown
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function display_dropdown( $args ) {
		$args = wp_parse_args( $args, array(
			'name'        => '',
			'current'     => array(),
			'options'     => array(),
			'all'         => esc_html__( 'Any', 'glozin-addons' ),
			'show_counts' => false,
		) );

		if ( empty( $args['options'] ) ) {
			return;
		}

		echo '<select name="' . esc_attr( $args['name'] ) . '">';

		echo '<option value="">' . $args['all'] . '</option>';
		foreach ( $args['options'] as $slug => $option ) {
			$name = $option['level'] ? str_repeat( '&nbsp;&nbsp;&nbsp;', $option['level'] ) . ' ' . $option['name'] : $option['name'];

			printf(
				'<option value="%s" %s>%s%s</option>',
				esc_attr( $slug ),
				selected( true, in_array( $slug, (array) $args['current'] ), false ),
				strip_tags( $name ),
				$args['show_counts'] ? ' (' . $option['count'] . ')' : ''
			);
		}

		echo '</select>';
	}

	/**
	 * Display attribute filter automatically
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function display_auto( $args ) {
		$args = wp_parse_args( $args, array(
			'name'        => '',
			'type'        => 'select',
			'current'     => array(),
			'options'     => array(),
			'attribute'   => '',
			'multiple'    => false,
			'show_counts' =>  false,
		) );

		if ( empty( $args['options'] ) ) {
			return;
		}

		if ( ! class_exists( '\WCBoost\VariationSwatches\Plugin' ) && ! class_exists( 'TA_WC_Variation_Swatches' ) ) {
			$args['type'] = 'select';
		}

		switch ( $args['type'] ) {
			case 'color':
				echo '<div class="products-filter__options products-filter--swatches filter-swatches swatches-color">';
				foreach ( $args['options'] as $slug => $option ) {
					$color = $this->get_attribute_swatches( $option['id'], 'color' );

					$is_dual_color = get_term_meta( $option['id'], 'wcboost_variation_swatches_is_dual_color', true );
					$secondary_color = get_term_meta( $option['id'], 'wcboost_variation_swatches_second_color', true );
					$classes = $secondary_style = '';

					if ( $is_dual_color == 'yes' ) {
						$classes = ! empty( $secondary_color ) ? 'product-variation-item__multi-color' : '';
						$secondary_style = ! empty( $secondary_color ) ? 'style=--wcboost-swatches-item-color-secondary:' . $secondary_color . ';' : '';
					} 

					printf(
						'<span class="products-filter__option swatch gz-tooltip-inside gz-tooltip-short-spacing swatch-color swatch-%s %s %s" data-value="%s" data-tooltip="%s" data-tooltip-position="top" %s><span class="wcboost-variation-swatches__name bg-color" style="background-color:%s;">%s</span>%s</span>',
						esc_attr( $slug ),
						in_array( $slug, (array) $args['current'] ) ? 'selected' : '',
						esc_attr( $classes ),
						esc_attr( $slug ),
						esc_attr( $option['name'] ),
						esc_attr( $secondary_style ),
						esc_attr( $color ),
						esc_html( $option['name'] ),
						$args['show_counts'] ? '<span class="products-filter__count counter">' . $option['count'] . '</span>' : ''
					);
				}
				echo '</div>';

				printf(
					'<input type="hidden" name="%s" value="%s" %s>',
					esc_attr( $args['name'] ),
					esc_attr( implode( ',', $args['current'] ) ),
					empty( $args['current'] ) ? 'disabled' : ''
				);

				if ( $args['attribute'] && $args['multiple'] && 'or' == $args['query_type'] ) {
					printf(
						'<input type="hidden" name="query_type_%s" value="or" %s>',
						esc_attr( $args['attribute'] ),
						empty( $args['current'] ) ? 'disabled' : ''
					);
				}
				break;

			case 'image':
				echo '<div class="products-filter__options products-filter--swatches filter-swatches swatches-image">';
				foreach ( $args['options'] as $slug => $option ) {
					$image = $this->get_attribute_swatches( $option['id'], 'image' );
					$image = $image ? wp_get_attachment_image_src( $image ) : '';
					$image = $image ? $image[0] : WC()->plugin_url() . '/assets/images/placeholder.png';

					printf(
						'<span class="products-filter__option swatch gz-tooltip-inside gz-tooltip-short-spacing swatch-image swatch-%s %s" data-value="%s" data-tooltip="%s" data-tooltip-position="top"><img src="%s" alt="%s">%s</span>',
						esc_attr( $slug ),
						in_array( $slug, (array) $args['current'] ) ? 'selected' : '',
						esc_attr( $slug ),
						esc_attr( $option['name'] ),
						esc_url( $image ),
						esc_attr( $option['name'] ),
						$args['show_counts'] && $args['display_type'] != 'auto' ? '<span class="products-filter__count counter">' . $option['count'] . '</span>' : ''
					);
				}
				echo '</div>';

				printf(
					'<input type="hidden" name="%s" value="%s" %s>',
					esc_attr( $args['name'] ),
					esc_attr( implode( ',', $args['current'] ) ),
					empty( $args['current'] ) ? 'disabled' : ''
				);

				if ( $args['attribute'] && $args['multiple'] && 'or' == $args['query_type'] ) {
					printf(
						'<input type="hidden" name="query_type_%s" value="or" %s>',
						esc_attr( $args['attribute'] ),
						empty( $args['current'] ) ? 'disabled' : ''
					);
				}
				break;

			case 'label':
				echo '<div class="products-filter__options products-filter--swatches filter-swatches swatches-label">';
				foreach ( $args['options'] as $slug => $option ) {
					$label = $this->get_attribute_swatches( $option['id'], 'label' );
					$label = $label ? $label : $option['name'];

					printf(
						'<span class="products-filter__option swatch gz-tooltip-inside gz-tooltip-short-spacing swatch-label swatch-%s %s" data-value="%s" data-tooltip="%s" data-tooltip-position="top">%s%s</span>',
						esc_attr( $slug ),
						in_array( $slug, (array) $args['current'] ) ? 'selected' : '',
						esc_attr( $slug ),
						esc_attr( $label ),
						esc_html( $label ),
						$args['show_counts'] ? '<span class="products-filter__count counter">' . $option['count'] . '</span>' : ''
					);
				}
				echo '</div>';

				printf(
					'<input type="hidden" name="%s" value="%s" %s>',
					esc_attr( $args['name'] ),
					esc_attr( implode( ',', $args['current'] ) ),
					empty( $args['current'] ) ? 'disabled' : ''
				);

				if ( $args['attribute'] && $args['multiple'] && 'or' == $args['query_type'] ) {
					printf(
						'<input type="hidden" name="query_type_%s" value="or" %s>',
						esc_attr( $args['attribute'] ),
						empty( $args['current'] ) ? 'disabled' : ''
					);
				}
				break;

			case 'button':
				echo '<div class="products-filter__options products-filter--swatches filter-swatches swatches-button">';
				foreach ( $args['options'] as $slug => $option ) {
					$label = $option['name'];

					printf(
						'<span class="products-filter__option swatch gz-tooltip-inside gz-tooltip-short-spacing swatch-button swatch-%s %s" data-value="%s" data-tooltip="%s" data-tooltip-position="top">%s%s</span>',
						esc_attr( $slug ),
						in_array( $slug, (array) $args['current'] ) ? 'selected' : '',
						esc_attr( $slug ),
						esc_attr( $label ),
						esc_html( $label ),
						$args['show_counts'] ? '<span class="products-filter__count counter">' . $option['count'] . '</span>' : ''
					);
				}
				echo '</div>';

				printf(
					'<input type="hidden" name="%s" value="%s" %s>',
					esc_attr( $args['name'] ),
					esc_attr( implode( ',', $args['current'] ) ),
					empty( $args['current'] ) ? 'disabled' : ''
				);

				if ( $args['attribute'] && $args['multiple'] && 'or' == $args['query_type'] ) {
					printf(
						'<input type="hidden" name="query_type_%s" value="or" %s>',
						esc_attr( $args['attribute'] ),
						empty( $args['current'] ) ? 'disabled' : ''
					);
				}
				break;

			default:
				$this->display_dropdown( $args );
				break;
		}
	}

	/**
	 * Get atribute swatches data
	 *
	 * @param int $term_id
	 * @param string $type
	 * @return mixed
	 */
	public function get_attribute_swatches( $term_id, $type = 'color' ) {
		if ( class_exists( '\WCBoost\VariationSwatches\Admin\Term_Meta' ) ) {
			$data = \WCBoost\VariationSwatches\Admin\Term_Meta::instance()->get_meta( $term_id, $type );
		} else {
			$data = get_term_meta( $term_id, $type, true );
		}

		return $data;
	}

	/**
	 * Count products within certain terms, taking the main WP query into consideration.
	 *
	 * This query allows counts to be generated based on the viewed products, not all products.
	 *
	 * @since 1.0.0
	 *
	 * @see WC_Widget_Layered_Nav->get_filtered_term_product_counts
	 *
	 * @param  array $term_ids Term IDs.
	 * @param  string $taxonomy Taxonomy.
	 * @param  string $query_type Query Type.
	 *
	 * @return array
	 */
	protected function get_filtered_term_product_counts( $term_ids, $taxonomy, $query_type ) {
		global $wpdb;

		$tax_query  = \WC_Query::get_main_tax_query();
		$meta_query = \Glozin\Addons\Elementor\Builder\Helper::is_catalog() ? \WC_Query::get_main_meta_query() : false;

		if ( 'or' === $query_type ) {
			foreach ( $tax_query as $key => $query ) {
				if ( is_array( $query ) && $taxonomy === $query['taxonomy'] ) {
					unset( $tax_query[ $key ] );
				}
			}
		}

		if ( 'product_brand' === $taxonomy ) {
			foreach ( $tax_query as $key => $query ) {
				if ( is_array( $query ) ) {
					if ( $query['taxonomy'] === 'product_brand' ) {
						unset( $tax_query[ $key ] );

						if ( preg_match( '/pa_/', $query['taxonomy'] ) ) {
							unset( $tax_query[ $key ] );
						}
					}
				}
			}
		}


		if ( 'product_author' === $taxonomy ) {
			foreach ( $tax_query as $key => $query ) {
				if ( is_array( $query ) ) {
					if ( $query['taxonomy'] === 'product_author' ) {
						unset( $tax_query[ $key ] );

						if ( preg_match( '/pa_/', $query['taxonomy'] ) ) {
							unset( $tax_query[ $key ] );
						}
					}
				}
			}
		}

		if ( 'product_cat' === $taxonomy ) {
			foreach ( $tax_query as $key => $query ) {
				if ( is_array( $query ) ) {
					if ( $query['taxonomy'] === 'product_cat' ) {
						unset( $tax_query[ $key ] );
					}
				}
			}
		}

		$meta_query     = new \WP_Meta_Query( $meta_query );
		$tax_query      = new \WP_Tax_Query( $tax_query );
		$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
		$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );
		$term_ids_sql   = '(' . implode( ',', array_map( 'absint', $term_ids ) ) . ')';

		// Generate query.
		$query           = array();
		$query['select'] = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) AS term_count, terms.term_id AS term_count_id";
		$query['from']   = "FROM {$wpdb->posts}";
		$query['join']   = "
			INNER JOIN {$wpdb->term_relationships} AS term_relationships ON {$wpdb->posts}.ID = term_relationships.object_id
			INNER JOIN {$wpdb->term_taxonomy} AS term_taxonomy USING( term_taxonomy_id )
			INNER JOIN {$wpdb->terms} AS terms USING( term_id )
			" . $tax_query_sql['join'] . $meta_query_sql['join'];

		$query['where'] = "
			WHERE {$wpdb->posts}.post_type IN ( 'product' )
			AND {$wpdb->posts}.post_status = 'publish'
			{$tax_query_sql['where']} {$meta_query_sql['where']}
			AND terms.term_id IN $term_ids_sql";

		if( \Glozin\Addons\Elementor\Builder\Helper::is_catalog() ) {
			$search = \WC_Query::get_main_search_query_sql();
			if ( $search ) {
				$query['where'] .= ' AND ' . $search;
			}
		}

		$query['group_by'] = 'GROUP BY terms.term_id';
		$query             = apply_filters( 'woocommerce_get_filtered_term_product_counts_query', $query );
		$query_sql         = implode( ' ', $query );

		// We have a query - let's see if cached results of this query already exist.
		$query_hash = md5( $query_sql );

		// Maybe store a transient of the count values.
		$cache = apply_filters( 'woocommerce_layered_nav_count_maybe_cache', true );
		if ( true === $cache ) {
			$cached_counts = (array) get_transient( 'wc_layered_nav_counts_' . sanitize_title( $taxonomy ) );
		} else {
			$cached_counts = array();
		}

		if ( ! isset( $cached_counts[ $query_hash ] ) ) {
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$results                      = $wpdb->get_results( $query_sql, ARRAY_A );
			$counts                       = array_map( 'absint', wp_list_pluck( $results, 'term_count', 'term_count_id' ) );
			$cached_counts[ $query_hash ] = $counts;
			if ( true === $cache ) {
				set_transient( 'wc_layered_nav_counts_' . sanitize_title( $taxonomy ), $cached_counts, DAY_IN_SECONDS );
			}
		}

		return array_map( 'absint', (array) $cached_counts[ $query_hash ] );
	}

	protected function get_filter_source_options() {
		$sources = array(
			'products_group' => esc_html__( 'Group', 'glozin-addons' ),
			'price'          => esc_html__( 'Price', 'glozin-addons' ),
			'attribute'      => esc_html__( 'Attributes', 'glozin-addons' ),
			'rating'         => esc_html__( 'Rating', 'glozin-addons' ),
			'product_status' => esc_html__( 'Product Status', 'glozin-addons' ),
		);

		// Getting other taxonomies.
		$product_taxonomies = get_object_taxonomies( 'product', 'objects' );
		foreach ( $product_taxonomies as $taxonomy_name => $taxonomy ) {
			if ( ! $taxonomy->public || ! $taxonomy->publicly_queryable ) {
				continue;
			}

			if ( 'product_shipping_class' == $taxonomy_name || taxonomy_is_product_attribute( $taxonomy_name ) ) {
				continue;
			}

			$sources[ $taxonomy_name ] = $taxonomy->label;
		}

		return $sources;

	}

	protected function get_filter_display($filter) {
		if( $filter['source'] == 'price' ) {
			$filter['display'] = $filter['display_price'];
		} elseif( $filter['source'] == 'attribute' ) {
			$filter['display'] = $filter['display_attribute'];
			$filter['multiple'] = $filter['multiple_attribute'];
		} elseif( $filter['source'] == 'rating' ) {
			$filter['display'] = $filter['display_rating'];
			$filter['multiple'] = $filter['multiple_rating'];
		} else {
			$filter['display'] = $filter['display_default'];
			$filter['multiple'] = $filter['multiple_default'];
		}

		return $filter;
	}

	protected function get_filter_attribute_options() {
		$attributes = array();

		// Getting attribute taxonomies.
		$attribute_taxonomies = wc_get_attribute_taxonomies();
		foreach ( $attribute_taxonomies as $taxonomy ) {
			$attributes[ $taxonomy->attribute_name ] = $taxonomy->attribute_label;
		}

		return $attributes;
	}

	protected function get_filter_display_options( $source = 'product_cat' ) {
		$options = array(
			'price' => array(
				'slider' => esc_html__( 'Slider', 'glozin-addons' ),
				'ranges' => esc_html__( 'Ranges', 'glozin-addons' ),
			),
			'attribute' => array(
				'auto'       => esc_html__( 'Auto', 'glozin-addons' ),
				'dropdown'   => esc_html__( 'Dropdown', 'glozin-addons' ),
				'list'       => esc_html__( 'Vertical List', 'glozin-addons' ),
				'h-list'     => esc_html__( 'Horizontal List', 'glozin-addons' ),
				'checkboxes' => esc_html__( 'Checkbox List', 'glozin-addons' ),
			),
			'rating' => array(
				'dropdown'   => esc_html__( 'Dropdown', 'glozin-addons' ),
				'checkboxes' => esc_html__( 'Checkbox List', 'glozin-addons' ),
			),
			'default' => array(
				'dropdown'   => esc_html__( 'Dropdown', 'glozin-addons' ),
				'list'       => esc_html__( 'Vertical List', 'glozin-addons' ),
				'h-list'     => esc_html__( 'Horizontal List', 'glozin-addons' ),
				'checkboxes' => esc_html__( 'Checkbox List', 'glozin-addons' ),
			),
		);

		if ( 'all' == $source ) {
			return $options;
		}

		if ( array_key_exists( $source, $options ) ) {
			return $options[ $source ];
		}

		return $options['default'];
	}
}
