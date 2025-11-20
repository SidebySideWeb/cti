<?php
namespace Glozin\Addons\Elementor\Builder\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Variation_Compare extends Widget_Base {
	use \Glozin\Addons\Elementor\Builder\Traits\Product_Id_Trait;

	public function get_name() {
		return 'glozin-variation-compare';
	}

	public function get_title() {
		return esc_html__( '[Glozin] Variation Compare', 'glozin-addons' );
	}

	public function get_icon() {
		return 'eicon-exchange';
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'variation', 'compare', 'product' ];
	}

	public function get_categories() {
		return [ 'glozin-addons-product' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'icon',
			[
				'label' => __( 'Icon', 'glozin-addons' ),
				'type' => Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'text',
			[
				'label' => __( 'Text', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Compare', 'glozin-addons' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_product_style',
			[
				'label' => esc_html__( 'Style', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-extra-link-item .glozin-svg-icon' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'glozin-addons' ),
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
					'{{WRAPPER}} .glozin-extra-link-item .glozin-svg-icon' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'icon_spacing',
			[
				'label' => esc_html__( 'Icon Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-extra-link-item' => 'gap: {{SIZE}}{{UNIT}}',
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
				'selector' => '{{WRAPPER}} .glozin-extra-link-item',
			]
		);

		$this->add_control(
			'link_color',
			[
				'label' => esc_html__( 'Link Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-extra-link-item' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'link_hover_color',
			[
				'label' => esc_html__( 'Hover Link Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-extra-link-item:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		global $product;

		$product = $this->get_product();

		if ( ! $product ) {
			return;
		}

		$is_compare = true;

		if( $product->get_type() != 'variable' ) {
			$is_compare = false;
		} else {
			$attributes = $product->get_variation_attributes();
			$attribute_name = get_post_meta( $product->get_id(), 'glozin_product_variation_attribute', true );
			$attribute_name = (0 === strpos( $attribute_name, 'pa_' )) ? str_replace( 'pa_', '', $attribute_name ) : $attribute_name;
			$attribute_name = $attribute_name ? $attribute_name : get_option( 'glozin_variation_compare_primary' );
			if( $attribute_name ==  'none' ) {
				$is_compare = false;
			} else {
				if( ! empty($attributes['pa_' . $attribute_name]) ) {
					$is_compare = $attribute_name;
				} else {
					$is_compare = false;
				}
			}
		}

		if ( \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
			echo '<div class="glozin-product-extra-link">';
				echo '<a href="#" class="glozin-extra-link-item glozin-extra-link-item--variation-compare d-inline-flex align-items-center gap-10 lh-normal text-base text-hover-color" data-toggle="modal" data-target="product-variation-compare-modal">';
				if( ! empty( $settings['icon']['value'] ) ) {
					echo '<span class="glozin-svg-icon glozin-svg-icon--compare-color">' . \Elementor\Icons_Manager::try_get_icon_html( $settings['icon'], [ 'aria-hidden' => 'true' ] ) . '</span>';
				} else {
					echo \Glozin\Addons\Helper::get_svg( 'compare-color' );
				}

				if( ! empty( $settings['text'] ) ) {
					echo esc_html( $settings['text'] );
				} else {
					echo esc_html__( 'Compare', 'glozin-addons' );
				}

				echo '</a>';
			echo '</div>';
		} else {
			if( $is_compare ) {
				add_filter( 'glozin_product_variation_compare_icon', [ $this, 'product_variation_compare_icon' ] );
				add_filter( 'glozin_product_variation_compare_text', [ $this, 'product_variation_compare_text' ] );
				echo '<div class="glozin-product-extra-link">';
					do_action('glozin_variation_compare_elementor');
				echo '</div>';
				remove_filter( 'glozin_product_variation_compare_icon', [ $this, 'product_variation_compare_icon' ] );
				remove_filter( 'glozin_product_variation_compare_text', [ $this, 'product_variation_compare_text' ] );
			}
		}

	}

	public function product_variation_compare_icon( $icon ) {
		$settings = $this->get_settings_for_display();

		if( ! empty( $settings['icon']['value'] ) ) {
        	return '<span class="glozin-svg-icon glozin-svg-icon--compare-color">' . \Elementor\Icons_Manager::try_get_icon_html( $settings['icon'], [ 'aria-hidden' => 'true' ] ) . '</span>';
		}

		return $icon;
    }

	public function product_variation_compare_text( $text ) {
		$settings = $this->get_settings_for_display();

		if( ! empty( $settings['text'] ) ) {
        	return esc_html( $settings['text'] );
		}

		return $text;
    }
}
