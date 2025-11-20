<?php
namespace Glozin\Addons\Elementor\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Glozin\Addons\Elementor\Utils;
use Glozin\Addons\WooCommerce\Products_Base;

abstract class Products_Widget_Base extends Carousel_Widget_Base {
	use Products_Base;
	/**
	 * Register controls for products query
	 *
	 * @param array $controls
	 */
	protected function register_products_controls( $controls = [], $frontend_available = false ) {
		$supported_controls = [
			'limit'    				=> 10,
			'type'     				=> 'recent_products',
			'ids'    				=> '',
			'category' 				=> '',
			'tag'      				=> '',
			'brand'    				=> '',
			'attributes'    		=> '',
			'orderby'  				=> '',
			'order'    				=> 'DESC',
			'title_tag'			=> 'default',
			'product_outofstock'    => 'no',
			'product_outofstock_last'=> 'no',
		];

		$controls = 'all' == $controls ? $supported_controls : $controls;

		foreach ( $controls as $option => $default ) {
			switch ( $option ) {
				case 'limit':
					$this->add_control(
						'limit',
						[
							'label'     => __( 'Number of Products', 'glozin-addons' ),
							'type'      => Controls_Manager::NUMBER,
							'min'       => -1,
							'max'       => 100,
							'step'      => 1,
							'default'   => $default,
							'frontend_available' => $frontend_available
						]
					);
					break;

				case 'type':
					$this->add_control(
						'type',
						[
							'label' => __( 'Type', 'glozin-addons' ),
							'type' => Controls_Manager::SELECT,
							'options' => $this->get_options_product_type(),
							'default' => $default,
							'frontend_available' => $frontend_available
						]
					);
					break;

				case 'product_outofstock':
					$this->add_control(
						'hide_product_outofstock',
						[
							'label'        => esc_html__( 'Hide Out Of Stock Products', 'glozin-addons' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'Yes', 'glozin-addons' ),
							'label_off'    => esc_html__( 'No', 'glozin-addons' ),
							'return_value' => 'yes',
							'default'      => $default,
							'frontend_available' => $frontend_available
						]
					);
					break;

				case 'product_outofstock_last':
					$this->add_control(
						'product_outofstock_last',
						[
							'label'        => esc_html__( 'Show out of stock products at the end', 'glozin-addons' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'Yes', 'glozin-addons' ),
							'label_off'    => esc_html__( 'No', 'glozin-addons' ),
							'return_value' => 'yes',
							'default'      => $default,
							'frontend_available' => $frontend_available
						]
					);
					break;

				case 'product_outofstock_last':
					$this->add_control(
						'product_outofstock_last',
						[
							'label'        => esc_html__( 'Show out of stock products at the end', 'glozin-addons' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'Show', 'glozin-addons' ),
							'label_off'    => esc_html__( 'Hide', 'glozin-addons' ),
							'return_value' => 'yes',
							'default'      => $default,
							'frontend_available' => $frontend_available
						]
					);
					break;

				case 'category':
					$this->add_control(
						'category',
						[
							'label' => __( 'Product Category', 'glozin-addons' ),
							'type' => 'glozin-autocomplete',
							'default' => $default,
							'multiple'    => true,
							'source'      => 'product_cat',
							'sortable'    => true,
							'label_block' => true,
							'frontend_available' => $frontend_available
						]
					);
					break;

				case 'tag':
					$this->add_control(
						'tag',
						[
							'label' => __( 'Product Tag', 'glozin-addons' ),
							'type' => Controls_Manager::SELECT2,
							'type' => 'glozin-autocomplete',
							'default' => $default,
							'multiple'    => true,
							'source'      => 'product_tag',
							'sortable'    => true,
							'label_block' => true,
							'frontend_available' => $frontend_available
						]
					);
					break;

				case 'brand':
					$this->add_control(
						'brand',
						[
							'label' => __( 'Product Brand', 'glozin-addons' ),
							'type' => Controls_Manager::SELECT2,
							'type' => 'glozin-autocomplete',
							'default' => $default,
							'multiple'    => true,
							'source'      => 'product_brand',
							'sortable'    => true,
							'label_block' => true,
							'frontend_available' => $frontend_available
						]
					);
					break;

				case 'attributes':
					$this->add_control(
						'attributes',
						[
							'label' => __( 'Attributes', 'glozin-addons' ),
							'type' => 'glozin-autocomplete',
							'default' => $default,
							'multiple'    => true,
							'source'      => 'attribute',
							'sortable'    => true,
							'label_block' => true,
							'frontend_available' => $frontend_available
						]
					);
					break;

				case 'ids':
					$this->add_control(
						'ids',
						[
							'label' => __( 'Products', 'glozin-addons' ),
							'type' => 'glozin-autocomplete',
							'default' => $default,
							'multiple'    => true,
							'source'      => 'product',
							'sortable'    => true,
							'label_block' => true,
							'condition' => [
								'type' => ['custom_products']
							],
							'frontend_available' => $frontend_available
						]
					);
					break;

				case 'orderby':
					$this->add_control(
						'orderby',
						[
							'label' => __( 'Order By', 'glozin-addons' ),
							'type' => Controls_Manager::SELECT,
							'options' => $this->get_options_product_orderby(),
							'default' => $default,
							'condition' => [
								'type' => ['featured_products', 'sale_products', 'custom_products']
							],
							'frontend_available' => $frontend_available
						]
					);
					break;

				case 'order':
					$this->add_control(
						'order',
						[
							'label' => __( 'Order', 'glozin-addons' ),
							'type' => Controls_Manager::SELECT,
							'options' => [
								'ASC'  => __( 'Ascending', 'glozin-addons' ),
								'DESC' => __( 'Descending', 'glozin-addons' ),
							],
							'default' => $default,
							'condition' => [
								'type' => ['featured_products', 'sale_products', 'custom_products'],
								'orderby!' => ['rand'],
							],
							'frontend_available' => $frontend_available
						]
					);
					break;
				case 'title_tag':
					$this->add_control(
						'title_tag',
						[
							'label' => __( 'Title HTML Tag', 'glozin-addons' ),
							'type' => Controls_Manager::SELECT,
							'options' => [
								'default' => __( 'Default', 'glozin-addons' ),
								'h1'  => __( 'H1', 'glozin-addons' ),
								'h2'  => __( 'H2', 'glozin-addons' ),
								'h3'  => __( 'H3', 'glozin-addons' ),
								'h4'  => __( 'H4', 'glozin-addons' ),
								'h5'  => __( 'H5', 'glozin-addons' ),
								'h6'  => __( 'H6', 'glozin-addons' ),
								'div' => __( 'div', 'glozin-addons' ),
								'span' => __( 'span', 'glozin-addons' ),
								'p' => __( 'p', 'glozin-addons' ),
							],
							'default' => $default
						]
					);
					break;
			}
		}
	}
	
	/**
	 * Render products loop content for shortcode.
	 *
	 * @param array $settings Shortcode attributes
	 */
	protected function render_products( $settings = null ) {
		$settings = ! empty( $settings ) ? $settings : $this->get_settings_for_display();
		add_filter( 'glozin_product_card_title_heading_tag', [ $this, 'product_card_title_heading_tag' ], 5 );
		return $this->get_products_loop_content( $settings );
		remove_filter( 'glozin_product_card_title_heading_tag', [ $this, 'product_card_title_heading_tag' ], 5 );
	}

	public function product_card_title_heading_tag( $tag ) {
		$settings = $this->get_settings_for_display();
		if( ! empty( $settings['title_tag'] ) && $settings['title_tag'] != 'default' ) {
			return $settings['title_tag'];
		}
		return $tag;
	}


	protected function register_aspect_ratio_controls( $conditions = [], $default = [] ) {
		$default = wp_parse_args( $default, [ 'aspect_ratio_type' => '' ] );

        $this->add_control(
			'aspect_ratio_type',
			[
				'label'   => esc_html__( 'Aspect Ratio', 'glozin-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					''       => esc_html__( 'Default', 'glozin-addons' ),
					'square' => esc_html__( 'Square', 'glozin-addons' ),
					'custom' => esc_html__( 'Custom', 'glozin-addons' ),
				],
				'default' => $default['aspect_ratio_type'],
				'condition' => $conditions,
			]
		);

		$conditions = wp_parse_args( $conditions, [ 'aspect_ratio_type' => 'custom' ] );
        $this->add_control(
			'aspect_ratio',
			[
				'label'       => esc_html__( 'Aspect ratio (Eg: 3:4)', 'glozin-addons' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'Images will be cropped to aspect ratio', 'glozin-addons' ),
				'default'     => '',
				'label_block' => false,
                'condition' => $conditions,
			]
		);
	}

	/**
	 * Render aspect ratio style
	 *
	 * @return void
	 */
    protected function render_aspect_ratio_style( $style = '', $aspect_ratio = 1 ) {
        $settings = $this->get_settings_for_display();

		if( empty( $settings['aspect_ratio_type'] ) ) {
			return;
		}

        if( $settings['aspect_ratio_type'] == 'square' ) {
            $aspect_ratio = 1;
        }

        if( $settings['aspect_ratio_type'] == 'custom' && ! empty( $settings['aspect_ratio'] ) ) {
            if( ! is_numeric( $settings['aspect_ratio'] ) ) {
                $cropping_split = explode( ':', $settings['aspect_ratio'] );
                $width          = max( 1, (float) current( $cropping_split ) );
                $height         = max( 1, (float) end( $cropping_split ) );
                $aspect_ratio   = floatval( $width / $height );
            } else {
                $aspect_ratio = $settings['aspect_ratio'];
            }
        }

		$style = '--product-image-ratio-percent: '. round( 100 / $aspect_ratio ) . '%;';

        return $style;
    }
}