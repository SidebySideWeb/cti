<?php
namespace Glozin\Addons\Elementor\Builder\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_Tag extends Widget_Base {
	use \Glozin\Addons\Elementor\Builder\Traits\Product_Id_Trait;

	public function get_name() {
		return 'glozin-product-tag';
	}

	public function get_title() {
		return esc_html__( '[Glozin] Product Tag', 'glozin-addons' );
	}

	public function get_icon() {
		return 'eicon-product-meta';
	}

	public function get_categories() {
		return ['glozin-addons-product'];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'tag', 'product' ];
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
			'section_content',
			[ 'label' => __( 'Content', 'glozin-addons' ) ]
		);

		$this->add_control(
			'tag_text',
			[
				'label' => __( 'Tag Text', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Tag:', 'glozin-addons' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'tags_text',
			[
				'label' => __( 'Tags Text', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Tags:', 'glozin-addons' ),
				'label_block' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Style', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'selector' => '{{WRAPPER}} .tagged_as',
			]
		);

		$this->add_control(
			'color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tagged_as' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tags_heading',
			[
				'label' => esc_html__( 'Tags', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'tags_typography',
				'selector' => '{{WRAPPER}} .tagged_as a',
			]
		);

		$this->add_control(
			'tags_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tagged_as a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tags_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tagged_as a:hover' => 'color: {{VALUE}};',
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

		if ( \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
			$this->tags_html( $settings );
			return;
		}

		if( ! count( $product->get_tag_ids() ) ) {
			return;
		}

		$text = ! empty( $settings['tag_text'] ) ? $settings['tag_text'] : esc_html__( 'Tag:', 'glozin' );
		$texts = ! empty( $settings['tags_text'] ) ? $settings['tags_text'] : esc_html__( 'Tags:', 'glozin' );

		echo '<div class="product_meta">';
			echo wc_get_product_tag_list( $product->get_id(), ', ', '<span class="tagged_as">' . _n( $text, $texts, count( $product->get_tag_ids() ), 'glozin' ) . ' ', '</span>' );
		echo '</div>';
	}

	public function tags_html( $settings ) {
		$texts = ! empty( $settings['tags_text'] ) ? $settings['tags_text'] : esc_html__( 'Tags:', 'glozin' );

		echo '<div class="product_meta">';
			echo '<span class="tagged_as">'. $texts . ' <a>'. esc_html__( 'Tag 1', 'glozin-addons' ) .'</a>, <a>'. esc_html__( 'Tag 2', 'glozin-addons' ) .'</a></span>';
		echo '</div>';
	}
}
