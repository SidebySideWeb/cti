<?php
namespace Glozin\Addons\Elementor\Builder\Widgets;

use Glozin\Addons\Elementor\Base\Carousel_Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Archive_Top_Categories extends Carousel_Widget_Base {
	use \Glozin\Addons\Elementor\Base\Aspect_Ratio_Base;

	public function get_name() {
		return 'glozin-archive-top-categories';
	}

	public function get_title() {
		return esc_html__( '[Glozin] Archive Top Categories', 'glozin-addons' );
	}

	public function get_icon() {
		return 'eicon-products';
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'archive', 'categories', 'top' ];
	}

	public function get_categories() {
		return [ 'glozin-addons-archive-product' ];
	}

	public function get_script_depends() {
		return [
			'glozin-product-elementor-widgets'
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
			'limit',
			[
				'label'     	  => esc_html__( 'Limit', 'glozin' ),
				'description'     => esc_html__( 'Enter 0 to get all categories. Enter a number to get limit number of top categories.', 'glozin' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'default' => 0,
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'       => esc_html__( 'Order By', 'glozin' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'order' => esc_html__( 'Category Order', 'glozin' ),
					'name'  => esc_html__( 'Category Name', 'glozin' ),
					'id'    => esc_html__( 'Category ID', 'glozin' ),
					'count' => esc_html__( 'Product Counts', 'glozin' ),
				],
				'default'     => 'order',
			]
		);

		$this->add_control(
			'image_heading',
			[
				'label' => esc_html__( 'Image', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->register_aspect_ratio_controls( [], [ 'aspect_ratio_type' => '' ] );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_slider_options',
			[
				'label' => esc_html__( 'Carousel Settings', 'glozin-addons' ),
				'type'  => Controls_Manager::SECTION,
			]
		);

		$controls = [
			'slides_to_show'   => 6,
			'slides_to_scroll' => 1,
			'space_between'    => 30,
			'navigation'       => 'both',
			'autoplay'         => '',
			'autoplay_speed'   => 3000,
			'pause_on_hover'   => 'yes',
			'animation_speed'  => 800,
			'infinite'         => '',
		];

		$this->register_carousel_controls($controls);

        $this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Style', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .catalog-top-categories__item' => '--gz-image-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .catalog-top-categories__item' => '--gz-image-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'title_heading',
			[
				'label' => esc_html__( 'Title', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label' => esc_html__( 'Spacing Top', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .catalog-top-categories__title' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_carousel',
			[
				'label' => esc_html__( 'Carousel Style', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->register_carousel_style_controls();

        $this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$col = $settings['slides_to_show'];
		$col_tablet = ! empty( $settings['slides_to_show_tablet'] ) ? $settings['slides_to_show_tablet'] : $col;
		$col_mobile = ! empty( $settings['slides_to_show_mobile'] ) ? $settings['slides_to_show_mobile'] : $col_tablet;

		if ( \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
			$args = array(
				'taxonomy' => 'product_cat',
				'parent'   => 0,
			);
		} else {
			if( is_search() ) {
				return;
			}

			$queried        = get_queried_object();
			if( empty( $queried ) ) {
				return;
			}
			$current_term   = ! empty ( $queried->term_id ) ? $queried->term_id : '';
			$ouput          = array();

			if( $this->is_shop() ) {
				$args = array(
					'taxonomy' => 'product_cat',
					'parent'   => 0,
				);
			} else {
				$termchildren  = get_term_children( $queried->term_id, $queried->taxonomy );

				$args = array(
					'taxonomy' => $queried->taxonomy,
				);

				if( ! empty( $termchildren ) ) {
					$args['parent'] = $queried->term_id;

					if( count( $termchildren ) == 1 ) {
						$term = get_term_by( 'id', $termchildren[0], $queried->taxonomy );

						if( $term->count == 0 ) {
							$args['parent'] = $queried->parent;
						}
					}

				} else {
					$args['parent'] = $queried->parent;
				}
			}
		}

		if ( ! empty( $settings['orderby'] ) ) {
			$args['orderby'] = $settings['orderby'];

			if ( $settings['orderby'] == 'order' ) {
				$args['menu_order'] = 'asc';
			} else {
				if ( $settings['orderby'] == 'count' ) {
					$args['order'] = 'desc';
				}
			}
		}

		if( ! empty ( $settings['limit'] ) && $settings['limit'] !== '0' ) {
			$args['number'] =  $settings['limit'];
		}

		$terms = get_terms( $args );

		if ( is_wp_error( $terms ) || ! $terms ) {
			return;
		}

		$thumbnail_size = 'full';

		$button_icon = ! empty( $settings['button_icon']['value'] ) ? '<span class="glozin-svg-icon glozin-svg-icon--arrow-top">' . \Elementor\Icons_Manager::try_get_icon_html( $settings['button_icon'], [ 'aria-hidden' => 'true' ] ) . '</span>' : '';

		foreach( $terms as $term ) {
			$thumb_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
			$images = ! empty( wp_get_attachment_image_src( $thumb_id, $thumbnail_size ) ) ? wp_get_attachment_image_src( $thumb_id, $thumbnail_size )[0] : wc_placeholder_img_src( $thumbnail_size );

			$thumb_url = !empty( $thumb_id ) ? $images : wc_placeholder_img_src( $thumbnail_size );
			$term_img = !empty( $thumb_url ) ? '<img class="catalog-top-categories__image" src="' . esc_url( $thumb_url ) . '" alt="' . esc_attr( $term->name ) . '" />' : '<span class="catalog-top-categories__image">' . esc_attr( $term->name ) . '</span>';

			$ouput[] = sprintf(
						'<div class="catalog-top-categories__item swiper-slide %s">
							<a class="catalog-top-categories__inner gz-ratio gz-hover-zoom gz-hover-effect gz-image-rounded overflow-hidden position-relative" href="%s">
								%s
							</a>
							<a class="catalog-top-categories__title fs-14 fw-semibold text-dark d-block mt-15" href="%s">
								<span class="catalog-top-categories__text d-block">%s</span>
							</a>
						</div>',
						( !empty( $current_term ) && $current_term == $term->term_id ) ? 'active' : '',
						esc_url( get_term_link( $term->term_id ) ),
						$term_img,
						esc_url( get_term_link( $term->term_id ) ),
						esc_html( $term->name ),
					);
		}

		$this->add_render_attribute( 'wrapper', 'class', [ 'catalog-top-categories--elementor', 'gz-ratio--portrait', 'swiper', 'glozin-carousel--elementor', 'catalog-top-categories', 'position-relative', 'w-100' ] );
		$this->add_render_attribute( 'wrapper', 'data-desktop', $col );
		$this->add_render_attribute( 'wrapper', 'data-tablet', $col_tablet );
		$this->add_render_attribute( 'wrapper', 'data-mobile', $col_mobile );
		$this->add_render_attribute( 'wrapper', 'style', [ $this->render_aspect_ratio_style() ] );

		echo sprintf(
				'<div %s>
					<div class="catalog-top-categories__wrapper swiper-wrapper">%s</div>
					%s
					%s
				</div>',
				$this->get_render_attribute_string( 'wrapper' ),
				implode( '', $ouput ),
				$this->render_pagination(),
				$this->render_arrows(),
			);
	}

	/**
	 * Check is shop
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function is_shop() {
		if( function_exists('is_product_category') && is_product_category() ) {
			return false;
		} elseif( function_exists('is_shop') && is_shop() ) {
			if ( ! empty( $_GET ) && ( isset($_GET['product_cat']) )) {
				return false;
			}

			return true;
		}

		return true;
	}
}
