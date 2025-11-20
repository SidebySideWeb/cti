<?php
namespace Glozin\Addons\Elementor\Builder\Widgets;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_Images extends Widget_Base {

	use \Glozin\Addons\Elementor\Builder\Traits\Product_Id_Trait;

	public function get_name() {
		return 'glozin-product-images';
	}

	public function get_title() {
		return esc_html__( '[Glozin] Product Images', 'glozin-addons' );
	}

	public function get_icon() {
		return 'eicon-product-images';
	}

	public function get_categories() {
		return [ 'glozin-addons-product' ];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'image', 'product', 'gallery', 'lightbox' ];
	}

	public function get_script_depends() {
		return [
			'imagesLoaded',
			'glozin-product-elementor-widgets'
		];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_product_images',
			[ 'label' => __( 'Product Images', 'glozin-addons' ) ]
		);

		$this->add_control(
			'product_image_zoom',
			[
				'label'      => esc_html__( 'Image Zoom', 'glozin-addons' ),
				'type'       => Controls_Manager::SELECT,
				'options'    => [
					'none'  	=> esc_html__( 'None', 'glozin' ),
					'bounding'  => esc_html__( 'External zoom', 'glozin' ),
					'inner'     => esc_html__( 'Inner zoom square', 'glozin' ),
					'magnifier' => esc_html__( 'Inner zoom circle', 'glozin' ),
				],
				'default'    => 'none',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'thumbnails_layout',
			[
				'label'      => esc_html__( 'Thumbnails Layout', 'glozin-addons' ),
				'type'       => Controls_Manager::SELECT,
				'options'    => [
					''                  => esc_html__( 'Left thumbnails', 'glozin' ),
					'bottom-thumbnails' => esc_html__( 'Bottom thumbnails', 'glozin' ),
					'grid-1'            => esc_html__( 'Grid 1', 'glozin' ),
					'grid-2'            => esc_html__( 'Grid 2', 'glozin' ),
					'stacked'           => esc_html__( 'Stacked', 'glozin' ),
					'hidden-thumbnails' => esc_html__( 'Hidden thumbnails', 'glozin' ),
				],
				'default'    => '',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'product_image_lightbox',
			[
				'label'       => esc_html__( 'Product Image LightBox', 'glozin-addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'product_image_lightbox_icon',
			[
				'label'            => __( 'Custom LightBox Icon', 'glozin-addons' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'skin'             => 'inline',
				'label_block'      => false,
				'condition'   => [
					'product_image_lightbox' => 'yes',
				],
			]
		);

		$this->add_control(
			'mobile_single_product_gallery_arrows',
			[
				'label'       => esc_html__( 'Mobile Single Product Gallery Arrows', 'glozin-addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'default' => '',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_product_carousel',
			[ 'label' => __( 'Carousel', 'glozin-addons' ) ]
		);

		$this->add_control(
			'arrows_prev_icon',
			[
				'label'            => __( 'Custom Previous Icon', 'glozin-addons' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'skin'             => 'inline',
				'label_block'      => false,
			]
		);

		$this->add_control(
			'arrows_next_icon',
			[
				'label'            => __( 'Custom Next Icon', 'glozin-addons' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'skin'             => 'inline',
				'label_block'      => false,
			]
		);

		$this->end_controls_section();

		$this->section_style();
	}

	protected function section_style() {
		$this->start_controls_section(
			'section_style',
			[
				'label'     => __( 'Styles', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'gallery_heading',
			[
				'label' => esc_html__( 'Gallery', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'gallery_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-product-gallery .woocommerce-product-gallery__image' => '--gz-image-rounded-product-gallery: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .woocommerce-product-gallery .woocommerce-product-gallery__image' => '--gz-image-rounded-product-gallery: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'thumbnails_heading',
			[
				'label' => esc_html__( 'Thumbnails', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'thumbnails_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-product-gallery-thumbnails' => '--gz-image-rounded-product-thumbnail: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-product-gallery-thumbnails' => '--gz-image-rounded-product-thumbnail: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'thumbnails_border_color',
			[
				'label' => esc_html__( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-product-gallery-thumbnails .swiper-slide-thumb-active::after' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'lightbox_heading',
			[
				'label' => esc_html__( 'LightBox Button', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'lightbox_width',
			[
				'label'     => esc_html__( 'Width', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-product-gallery .glozin-button--product-lightbox' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'lightbox_height',
			[
				'label'     => esc_html__( 'Height', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-product-gallery .glozin-button--product-lightbox' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'lightbox_icon',
			[
				'label'     => esc_html__( 'Icon Size', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-product-gallery .glozin-button--product-lightbox' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'lightbox_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-product-gallery .glozin-button--product-lightbox' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'lightbox_background_color',
			[
				'label' => esc_html__( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-product-gallery .glozin-button--product-lightbox' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'lightbox_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-product-gallery .glozin-button--product-lightbox:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'lightbox_hover_background_color',
			[
				'label' => esc_html__( 'Hover Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-product-gallery .glozin-button--product-lightbox:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'lightbox_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-product-gallery .glozin-button--product-lightbox' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .woocommerce-product-gallery .glozin-button--product-lightbox' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_carousel_style',
			[
				'label'     => __( 'Carousel', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'arrows_horizontal_spacing',
			[
				'label'      => esc_html__( 'Horizontal Spacing', 'glozin-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => -100,
						'max' => 1170,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-product-gallery .elementor-swiper-button-prev' => 'left: {{SIZE}}{{UNIT}};',
					'.rtl {{WRAPPER}} .woocommerce-product-gallery .elementor-swiper-button-prev' => 'right: {{SIZE}}{{UNIT}}; left: auto;',
					'{{WRAPPER}} .woocommerce-product-gallery .elementor-swiper-button-next' => 'right: {{SIZE}}{{UNIT}};',
					'.rtl {{WRAPPER}} .woocommerce-product-gallery .elementor-swiper-button-next' => 'left: {{SIZE}}{{UNIT}}; right: auto;',
				],
			]
		);

		$this->add_responsive_control(
			'arrows_vertical_spacing',
			[
				'label'      => esc_html__( 'Vertical Spacing', 'glozin-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 1170,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .swiper-button' => '--gz-arrow-top: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'arrows_size',
			[
				'label'     => __( 'Icon Size', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 100,
						'min' => 0,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-button' => '--gz-arrow-font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrows_width',
			[
				'label'     => __( 'Width', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 100,
						'min' => 0,
					],
				],
				'default' => [],
				'selectors' => [
					'{{WRAPPER}} .swiper-button' => '--gz-arrow-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrows_height',
			[
				'label'     => __( 'Height', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 100,
						'min' => 0,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-button' => '--gz-arrow-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'arrows_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .swiper-button' => '--gz-arrow-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .swiper-button' => '--gz-arrow-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs(
			'arrows_tabs'
		);
		$this->start_controls_tab(
			'arrows_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'glozin-addons' ),
			]
		);
		$this->add_control(
			'arrows_bgcolor',
			[
				'label'     => esc_html__( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-button' => '--gz-arrow-bg-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrows_color',
			[
				'label'     => esc_html__( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-button' => '--gz-arrow-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrows_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-button' => '--gz-arrow-border-color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'arrows_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'arrows_hover_bgcolor',
			[
				'label'     => esc_html__( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-button' => '--gz-arrow-bg-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrows_hover_color',
			[
				'label'     => esc_html__( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-button' => '--gz-arrow-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrows_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-button' => '--gz-arrow-border-color-hover: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	public function render() {
		$settings = $this->get_settings_for_display();

		global $product;

		$product = $this->get_product();

		if ( ! $product ) {
			return;
		}

		$classes = [
			'product-gallery-summary',
			'glozin-product-gallery'
		];

		$this->add_render_attribute( 'thumbnails', 'class', $classes );

		if ( ! empty( $settings['product_image_lightbox_icon']['value'] ) ) {
			$this->add_render_attribute( 'thumbnails', 'data-lightbox_icon', esc_attr( $this->get_icon_html( $settings['product_image_lightbox_icon'], [ 'aria-hidden' => 'true' ] ) ) );
		}

		if ( ! empty( $settings['arrows_prev_icon']['value'] ) ) {
			$this->add_render_attribute( 'thumbnails', 'data-prev_icon', esc_attr( $this->get_icon_html( $settings['arrows_prev_icon'], [ 'aria-hidden' => 'true' ] ) ) );
		}

		if ( ! empty( $settings['arrows_next_icon']['value'] ) ) {
			$this->add_render_attribute( 'thumbnails', 'data-next_icon', esc_attr( $this->get_icon_html( $settings['arrows_next_icon'], [ 'aria-hidden' => 'true' ] ) ) );
		}

		add_filter( 'woocommerce_single_product_image_gallery_classes', array( $this, 'single_product_image_gallery_classes' ), 20, 1 );
		add_action( 'woocommerce_product_thumbnails', array( $this, 'product_gallery_thumbnails' ), 20 );
		?>

		<div <?php echo $this->get_render_attribute_string( 'thumbnails' ); ?>>
			<?php wc_get_template( 'single-product/product-image.php' ); ?>
			<?php
				if ( \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {

					?>
					<script>
						jQuery( '.woocommerce-product-gallery' ).each( function() {
							jQuery( this ).css( 'opacity', '1' );
						} );
					</script>
					<?php
				}

				if( class_exists('\WCBoost\Wishlist\Frontend') || class_exists('\WCBoost\ProductsCompare\Frontend') ) {
					echo '<div class="product-featured-icons product-featured-icons--mobile d-flex d-none-md flex-column gap-10 position-absolute top-15 end-15 z-2">';
						if( class_exists('\WCBoost\Wishlist\Frontend') ) {
							\WCBoost\Wishlist\Frontend::instance()->single_add_to_wishlist_button();
						}
		
						if( class_exists('\WCBoost\ProductsCompare\Frontend') ) {
							\WCBoost\ProductsCompare\Frontend::instance()->single_add_to_compare_button();
						}
					echo '</div>';
				}
			?>
		</div>
		<?php
	}

	/**
	 * Single product image gallery classes
	 *
	 * @param array $args
	 * @return array
	 */
	public function single_product_image_gallery_classes( $classes ) {
		$settings = $this->get_settings_for_display();
		global $product;

		$gallery_layout = $settings['thumbnails_layout'];

		if( empty( $gallery_layout ) ) {
            $classes[] = 'woocommerce-product-gallery--vertical';
            $classes[] = 'd-flex-md';
            $classes[] = 'flex-md-row-reverse';
			$classes[] = 'gap-10';
		} elseif( in_array( $gallery_layout, array( 'grid-1', 'grid-2', 'stacked' ) ) ) {
            $classes[] = 'woocommerce-product-gallery--grid';
            $classes[] = 'woocommerce-product-gallery--' . esc_attr( $gallery_layout );
        } else {
			$classes[] = 'woocommerce-product-gallery--horizontal';
		}

		$key = array_search( 'images', $classes );
		if ( $key !== false ) {
			unset( $classes[ $key ] );
		}

		$attachment_ids = $product->get_gallery_image_ids();

		if ( $attachment_ids && $product->get_image_id() ) {
			$classes[] = 'woocommerce-product-gallery--has-thumbnails';
		}

		if( $settings['product_image_zoom'] !== 'none' ) {
			$classes[] = 'woocommerce-product-gallery--has-zoom';
		}

		if( ! empty( $settings['mobile_single_product_gallery_arrows'] ) ) {
			$classes[] = 'woocommerce-product-gallery--has-arrows-mobile';
		}

		return $classes;
	}

	/**
	 * Product gallery thumbnails
	 *
	 * @return void
	 */
	public function product_gallery_thumbnails() {
		$settings = $this->get_settings_for_display();
		global $product;

		$attachment_ids = apply_filters( 'glozin_single_product_gallery_image_ids', $product->get_gallery_image_ids() );

		if ( $attachment_ids && $product->get_image_id() ) {
			add_filter( 'woocommerce_single_product_flexslider_enabled', '__return_false' );
			
			if( ! in_array( $settings['thumbnails_layout'], array( 'grid-1', 'grid-2', 'stacked', 'hidden-thumbnails' ) ) ) {
				echo '<div class="glozin-product-gallery-thumbnails d-none d-block-md">';
					echo apply_filters( 'glozin_product_get_gallery_image', wc_get_gallery_image_html( $product->get_image_id() ), 1 );
					$index = 2;
					foreach ( $attachment_ids as $attachment_id ) {
						echo apply_filters( 'glozin_product_get_gallery_thumbnail', wc_get_gallery_image_html( $attachment_id ), $index );
						$index++;
					}
				echo '</div>';
			}
			$rm_filter = 'remove_filter';
			$rm_filter( 'woocommerce_single_product_flexslider_enabled', '__return_false' );
		}
	}

	/**
	 * @param array $icon
	 * @param array $attributes
	 * @param $tag
	 * @return bool|mixed|string
	 */
	function get_icon_html( array $icon, array $attributes, $tag = 'i' ) {
		/**
		 * When the library value is svg it means that it's a SVG media attachment uploaded by the user.
		 * Otherwise, it's the name of the font family that the icon belongs to.
		 */
		if ( 'svg' === $icon['library'] ) {
			$output = Icons_Manager::render_uploaded_svg_icon( $icon['value'] );
		} else {
			$output = Icons_Manager::render_font_icon( $icon, $attributes, $tag );
		}
		return $output;
	}

}