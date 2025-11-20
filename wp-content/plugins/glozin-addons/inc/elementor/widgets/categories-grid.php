<?php
namespace Glozin\Addons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Glozin\Addons\Helper;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor categories grid widget.
 *
 * Elementor widget that displays an eye-catching headlines.
 *
 * @since 1.0.0
 */
class Categories_Grid extends Widget_Base {
	use \Glozin\Addons\Elementor\Base\Aspect_Ratio_Base;
	use \Glozin\Addons\Elementor\Base\Button_Base;
	/**
	 * Get widget name.
	 *
	 * Retrieve categories grid widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-products-categories-grid';
	}

	/**
	 * Get widget title
	 *
	 * Retrieve categories grid widget title
	 *
	 * @return string Widget title
	 */
	public function get_title() {
		return __( '[Glozin] Products Categories Grid', 'glozin-addons' );
	}

	/**
	 * Get widget icon
	 *
	 * Retrieve product categories widget icon
	 *
	 * @return string Widget icon
	 */
	public function get_icon() {
		return 'eicon-product-categories';
	}

	/**
	 * Get widget categories
	 *
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @return string Widget categories
	 */
	public function get_categories() {
		return [ 'glozin-addons' ];
	}

	/**
	 * Get widget keywords.
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'collection', 'list', 'categories', 'products', 'glozin-addons' ];
	}

	public function get_script_depends(): array {
		return [ 'glozin-categories-grid-widget' ];
	}

	/**
	 * Get style dependencies.
	 *
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return [ 'glozin-categories-grid-css' ];
	}

	/**
	 * Register categories grid widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => __( 'Categories', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'source',
			[
				'label'       => esc_html__( 'Source', 'glozin-addons' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'default' => esc_html__( 'Default', 'glozin-addons' ),
					'custom'  => esc_html__( 'Custom', 'glozin-addons' ),
				],
				'default'     => 'default',
				'label_block' => true,
			]
		);

		$this->add_control(
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
				'condition'   => [
					'source' => 'custom',
				],
			]
		);

		$this->add_control(
			'number',
			[
				'label'           => esc_html__( 'Item Per Page', 'glozin-addons' ),
				'type'            => Controls_Manager::NUMBER,
				'min'             => 1,
				'max'             => 50,
				'default' 		=> '8',
				'frontend_available' => true,
				'condition'   => [
					'source' => 'default',
				],
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'          => esc_html__( 'Columns', 'glozin-addons' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 1,
				'max'            => 10,
				'step'           => 1,
				'default'        => 4,
				'tablet_default' => 3,
				'mobile_default' => 2,
				'selectors' => [
					'{{WRAPPER}} .glozin-categories-grid__items' => '--gz-categories-grid-columns: {{VALUE}}',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'     => esc_html__( 'Order By', 'glozin-addons' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					''           => esc_html__( 'Default', 'glozin-addons' ),
					'date'       => esc_html__( 'Date', 'glozin-addons' ),
					'title'      => esc_html__( 'Title', 'glozin-addons' ),
					'count'      => esc_html__( 'Count', 'glozin-addons' ),
					'menu_order' => esc_html__( 'Menu Order', 'glozin-addons' ),
				],
				'default'   => '',
				'condition'   => [
					'source' => 'default',
				],
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
					'numeric' => esc_attr__( 'Numeric', 'glozin-addons' ),
					'infinite' => esc_attr__( 'Infinite Scroll', 'glozin-addons' ),
					'loadmore' => esc_attr__( 'Load More', 'glozin-addons' ),
				],
				'default' => 'numeric',
				'condition'   => [
					'pagination' => 'yes',
				],
			]
		);

		$this->add_control(
			'pagination_text',
			[
				'label'       => __( 'Button Text', 'glozin-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'condition'   => [
					'pagination' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->section_style();

	}

	// Tab Style
	protected function section_style() {
		$this->start_controls_section(
			'section_style_categories',
			[
				'label' => esc_html__( 'Categories', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label'   => esc_html__( 'Column Gap', 'glozin-addons' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
                    'unit' => 'px',
                    'size' => 30,
                ],
				'tablet_default' => [
                    'unit' => 'px',
                    'size' => 15,
                ],
				'mobile_default' => [
                    'unit' => 'px',
                    'size' => 15,
                ],
				'range'   => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-categories-grid__items' => '--gz-categories-grid-col-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'row_gap',
			[
				'label'   => esc_html__( 'Row Gap', 'glozin-addons' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
                    'unit' => 'px',
                    'size' => 30,
                ],
				'tablet_default' => [
                    'unit' => 'px',
                    'size' => 15,
                ],
				'mobile_default' => [
                    'unit' => 'px',
                    'size' => 15,
                ],
				'range'   => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-categories-grid__items' => '--gz-categories-grid-row-gap: {{SIZE}}{{UNIT}};',
				],
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

		$this->register_aspect_ratio_controls( [], [ 'aspect_ratio_type' => 'vertical' ] );

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}}' => '--gz-image-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}}' => '--gz-image-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'gradient_image',
			[
				'label' => __( 'Gradient', 'glozin-addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'glozin-addons' ),
				'label_on'  => __( 'Show', 'glozin-addons' ),
				'default'   => '',
			]
		);

		$this->add_control(
			'gradient_image_popover_toggle',
			[
				'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'label' => esc_html__( 'Background', 'glozin-addons' ),
				'label_off' => esc_html__( 'Default', 'glozin-addons' ),
				'label_on' => esc_html__( 'Custom', 'glozin-addons' ),
				'return_value' => 'yes',
				'condition' => [
					'gradient_image' => 'yes',
				],
			]
		);

		$this->start_popover();

		$this->add_control(
			'gradient_image_heading',
			[
				'type'  => Controls_Manager::HEADING,
				'label' => esc_html__( 'Background', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'gradient_image_color_primary',
			[
				'label' => __( 'Color Primary', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-categories-grid-gradient' => '--gz-gradient-color-primary: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'gradient_image_color_secondary',
			[
				'label' => __( 'Color Secondary', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-categories-grid-gradient' => '--gz-gradient-color-secondary: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'gradient_image_angle',
			[
				'label' => esc_html__( 'Angle', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'deg', 'grad', 'rad', 'turn', 'custom' ],
				'default' => [],
				'selectors' => [
					'{{WRAPPER}} .glozin-categories-grid-gradient' => '--gz-gradient-angle: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_popover();

		$this->add_control(
			'button_heading',
			[
				'label' => esc_html__( 'Button', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'button_align',
			[
				'label'   => __( 'Alignment', 'glozin-addons' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
						'title' => __( 'Left', 'glozin-addons' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [
						'title' => __( 'Center', 'glozin-addons' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'   => [
						'title' => __( 'Right', 'glozin-addons' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default' => '',
				'selectors'            => [
					'{{WRAPPER}} .glozin-categories-grid__button' => '{{VALUE}}',
				],
				'selectors_dictionary' => [
					'left'   => 'left: var(--gz-button-position-side); right: auto; transform: none;',
					'center' => 'left: 50%; transform: translateX(-50%);',
					'right'  => 'left: auto; right: var(--gz-button-position-side); transform: none;',
				],
			]
		);

		$this->add_responsive_control(
			'button_position_side',
			[
				'label'   => esc_html__( 'Position Side', 'glozin-addons' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'range'   => [
					'px' => [
						'min' => 0,
						'max' => 300,
					],
				],
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-categories-grid__button' => '--gz-button-position-side: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_position_bottom',
			[
				'label'   => esc_html__( 'Position Bottom', 'glozin-addons' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [],
				'range'   => [
					'px' => [
						'min' => 0,
						'max' => 300,
					],
				],
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-categories-grid__button' => 'bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->register_button_style_controls( 'light', 'glozin-categories-grid__button .glozin-button' );

		$this->end_controls_section();
	}

	/**
	 * Render heading widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', [ 'glozin-categories-grid' ] );
		$this->add_render_attribute( 'wrapper', 'style', [$this->render_aspect_ratio_style() ] );

		if ( $settings['gradient_image'] == 'yes' ) {
			$this->add_render_attribute( 'wrapper', 'class', [ 'glozin-categories-grid-gradient' ] );
		}

		$button_class = ! empty( $settings['button_style'] ) ? ' gz-button-'  . $settings['button_style'] : '';
		$button_class .= in_array( $settings['button_style'], ['', 'light', 'outline-dark' , 'outline'] ) ? ' py-17 px-20' : '';
		$button_class .= in_array( $settings['button_style'], ['', 'light', 'outline-dark'] ) ? ' gz-button-hover-effect' : '';

		$categories_content = '';

		if ( $settings['product_cat'] ) {
			$categories_content = $this->get_custom_categories_content( $settings, $button_class );
		} else {
			$term_args = [
				'taxonomy' => 'product_cat',
				'orderby'  => $settings['orderby'],
			];

			$paged = get_query_var('paged') ? get_query_var('paged') : 1;
			$total_terms = count(get_terms('product_cat'));
			$total_pages = ceil( $total_terms / intval( $settings['number'] ) );

			$categories_content = $this->get_categories_content( $settings, $button_class, $term_args, $paged );
			$pagination_content = $this->get_pagination_content( $settings, $paged, $total_pages );
		}

		echo sprintf(
			'<div %s>
				<div class="glozin-categories-grid__items d-flex">%s</div>
				%s
			</div>',
			$this->get_render_attribute_string( 'wrapper' ),
			$categories_content,
			$pagination_content
		);
	}

	protected function get_custom_categories_content( $settings, $button_class ) {
		$cats = explode(',', $settings['product_cat']);
		$output = [];

		foreach ( $cats as $tab ) {
			$term = get_term_by( 'slug', $tab, 'product_cat' );

			if( is_wp_error( $term ) || empty( $term ) ) {
				continue;
			}

			$thumbnail_id = absint( get_term_meta( $term->term_id, 'thumbnail_id', true ) );
			$settings['image']['url'] = wp_get_attachment_image_src( $thumbnail_id );
			$settings['image']['id']  = $thumbnail_id;
			$image = Group_Control_Image_Size::get_attachment_image_html( $settings );
			$image = $image ? '<div class="glozin-categories-grid__image"> '.$image.'</div>' : '';

			if ( empty( $image ) ) {
				$image = '<img src="'. wc_placeholder_img_src() .'" title="'. esc_attr( $term->name ) .'" alt="'. esc_attr( $term->name ) .'" loading="lazy"/>';
			}

			$output[] = sprintf(
				'<div class="glozin-categories-grid__item">
					<a href="%s" class="gz-ratio gz-hover-zoom gz-hover-effect overflow-hidden gz-image-rounded position-relative">
						%s
						<span class="glozin-categories-grid__button glozin-button gz-button%s position-absolute bottom-15 bottom-30-md z-3">
							<span class="glozin-categories-grid__text">%s</span>
						</span>
					</a>
				</div>',
				esc_url( get_term_link( $term->term_id, 'product_cat' ) ),
				$image,
				esc_attr($button_class),
				esc_html( $term->name )
			);
		}

		return implode( '', $output );
	}

	protected function get_categories_content( $settings, $button_class, $term_args, $paged ) {
		$output = [];

		if( $settings['number'] ) {
			$term_args['number'] = intval( $settings['number'] );
			$term_args['offset'] = ( $paged - 1 ) * intval( $settings['number'] );
		}

		$terms = get_terms( $term_args );

		foreach ( $terms as $term ) {
			$thumbnail_id = absint( get_term_meta( $term->term_id, 'thumbnail_id', true ) );
			$settings['image']['url'] = wp_get_attachment_image_src( $thumbnail_id );
			$settings['image']['id']  = $thumbnail_id;
			$image = Group_Control_Image_Size::get_attachment_image_html( $settings );

			if ( empty( $image ) ) {
				$image = '<img src="'. wc_placeholder_img_src() .'" title="'. esc_attr( $term->name ) .'" alt="'. esc_attr( $term->name ) .'" loading="lazy"/>';
			}


			$output[] = sprintf(
				'<div class="glozin-categories-grid__item">
					<a href="%s" class="gz-ratio gz-hover-zoom gz-hover-effect overflow-hidden gz-image-rounded position-relative">
						%s
						<span class="glozin-categories-grid__button position-absolute bottom-15 bottom-30-md start-50 translate-middle-x z-3">
							<span class="glozin-button gz-button%s">
								<span class="glozin-categories-grid__text">%s</span>
							</span>
						</span>
					</a>
				</div>',
				esc_url( get_term_link( $term->term_id, 'product_cat' ) ),
				$image,
				esc_attr($button_class),
				esc_html( $term->name )
			);
		}

		return implode( '', $output );
	}

	protected function get_pagination_content( $settings, $paged, $total_pages ) {
		$output = [];
		if ( $settings['pagination'] == 'yes' && $paged < $total_pages ) {
			$classes_type = $settings['pagination_type'] == 'infinite' ? ' woocommerce-pagination--infinite' : '';
			$output[] = '<nav class="woocommerce-pagination'. esc_attr( $classes_type ) .' w-100">';

			if ( $settings['pagination_type'] == 'numeric' ) {
				$big = 999999999;
				$output[] = paginate_links(array(
					'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
					'format' => '?paged=%#%',
					'current' => $paged,
					'total' => $total_pages,
					'type' => 'list',
					'prev_text' => \Glozin\Addons\Helper::get_svg( 'left-mini' ),
					'next_text' => \Glozin\Addons\Helper::get_svg( 'right-mini' ),
				));
			} else {
				$output[] = sprintf( '<a href="#" class="woocommerce-pagination-button ajax-load-products glozin-button gz-button py-17 px-30 px-md-46 gz-button-hover-effect" data-number="%s" data-page="%s" data-button-type="%s">
					<span>%s</span>
				</a>',
				esc_attr( $settings['number'] ),
				esc_attr( $paged + 1 ),
				esc_attr($settings['button_style']),
				$settings['pagination_text'] ? esc_html( $settings['pagination_text'] ) : esc_html__( 'Load more', 'glozin-addons' ),
				);
			}

			$output[] = '</nav>';
		}

		return implode( '', $output );
	}
}