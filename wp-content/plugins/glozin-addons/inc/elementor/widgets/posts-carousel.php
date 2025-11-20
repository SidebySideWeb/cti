<?php
namespace Glozin\Addons\Elementor\Widgets;

use Glozin\Addons\Elementor\Base\Carousel_Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Glozin\Addons\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Icon Box widget
 */
class Posts_Carousel extends Carousel_Widget_Base {
	use \Glozin\Addons\Elementor\Base\Aspect_Ratio_Base;
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-posts-carousel';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Glozin] Posts Carousel', 'glozin-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-carousel';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return ['glozin-addons'];
	}

	/**
	 * Get widget keywords.
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'post carousel', 'post', 'carousel', 'glozin' ];
	}

	/**
	 * Scripts
	 *
	 * @return void
	 */
	public function get_script_depends() {
		return [
			'glozin-elementor-widgets'
		];
	}

	/**
	 * Register heading widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->section_content();
		$this->section_style_carousel();
	}

	// Tab Content
	protected function section_content() {
		$this->start_controls_section(
			'section_posts_carousel',
			[ 'label' => __( 'Posts Carousel', 'glozin-addons' ) ]
		);

		$this->add_control(
			'limit',
			[
				'label'     => __( 'Total', 'glozin-addons' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => -1,
				'max'       => 100,
				'step'      => 1,
				'default'   => 6,
			]
		);

		$this->add_control(
			'category',
			[
				'label'    => __( 'Category', 'glozin-addons' ),
				'type'     => Controls_Manager::SELECT2,
				'options'  => self::get_terms_options(),
				'default'  => '',
				'multiple' => true,
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'      => esc_html__( 'Order By', 'glozin-addons' ),
				'type'       => Controls_Manager::SELECT,
				'options'    => [
					'date'       => esc_html__( 'Date', 'glozin-addons' ),
					'name'       => esc_html__( 'Name', 'glozin-addons' ),
					'id'         => esc_html__( 'Ids', 'glozin-addons' ),
					'rand' 		=> esc_html__( 'Random', 'glozin-addons' ),
				],
				'default'    => 'date',
			]
		);

		$this->add_control(
			'order',
			[
				'label'      => esc_html__( 'Order', 'glozin-addons' ),
				'type'       => Controls_Manager::SELECT,
				'options'    => [
					''     => esc_html__( 'Default', 'glozin-addons' ),
					'ASC'  => esc_html__( 'Ascending', 'glozin-addons' ),
					'DESC' => esc_html__( 'Descending', 'glozin-addons' ),
				],
				'default'    => '',
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

		$this->register_aspect_ratio_controls( [], [ 'aspect_ratio_type' => 'square' ] );

		$this->end_controls_section();

		$this->section_content_carousel();
	}

	protected function section_content_carousel() {
		$this->start_controls_section(
			'section_carousel_settings',
			[
				'label' => __( 'Carousel Settings', 'glozin-addons' ),
			]
		);

		$controls = [
			'slides_to_show'   => 3,
			'slides_to_scroll' => 1,
			'space_between'    => 30,
			'navigation'       => 'arrows',
			'autoplay'         => '',
			'autoplay_speed'   => 3000,
			'pause_on_hover'   => 'yes',
			'animation_speed'  => 800,
			'infinite'         => '',
		];

		$this->register_carousel_controls($controls);

		$this->end_controls_section();
	}

	protected function section_style_carousel() {
		$this->start_controls_section(
			'section_post_style',
			[
				'label'     => __( 'Posts', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'post_image_heading',
			[
				'label' => esc_html__( 'Image', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'post_image_border_radius',
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

		$this->add_responsive_control(
			'post_image_spacing',
			[
				'label' => __( 'Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
					]
				],
				'default' => [],
				'selectors' => [
					'{{WRAPPER}} article' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'post_summary_heading',
			[
				'label' => esc_html__( 'Summary', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'post_summary_alignment',
			[
				'label'       => esc_html__( 'Alignment', 'glozin-addons' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'start'   => [
						'title' => esc_html__( 'Left', 'glozin-addons' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'glozin-addons' ),
						'icon'  => 'eicon-text-align-center',
					],
					'end'  => [
						'title' => esc_html__( 'Right', 'glozin-addons' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'     => '',
				'selectors'   => [
					'{{WRAPPER}} article .entry-summary' => 'align-items: {{VALUE}}; text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'post_category_heading',
			[
				'label' => esc_html__( 'Category', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'post_carousel_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .entry-category a' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'post_carousel_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .entry-category a' => 'border-start-start-radius: {{TOP}}{{UNIT}}; border-start-end-radius: {{RIGHT}}{{UNIT}}; border-end-end-radius: {{BOTTOM}}{{UNIT}}; border-end-start-radius: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'post_carousel_typography',
				'selector' => '{{WRAPPER}} .entry-category a',
			]
		);

		$this->add_control(
			'post_carousel_background_color',
			[
				'label' => __( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .entry-category a' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'post_carousel_color',
			[
				'label' => __( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .entry-category a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'post_carousel_border_color',
			[
				'label' => __( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .entry-category a' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'post_category_spacing',
			[
				'label' => __( 'Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
					]
				],
				'default' => [],
				'selectors' => [
					'{{WRAPPER}} .entry-category' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'post_title_heading',
			[
				'label' => esc_html__( 'Title', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'post_title_typography',
				'selector' => '{{WRAPPER}} .entry-title',
			]
		);

		$this->add_control(
			'post_title_color',
			[
				'label' => __( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .entry-title a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'post_title_spacing',
			[
				'label' => __( 'Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
					]
				],
				'default' => [],
				'selectors' => [
					'{{WRAPPER}} .entry-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'post_title_line',
			[
				'label'      => esc_html__( 'Post Title in', 'glozin-addons' ),
				'type'       => Controls_Manager::SELECT,
				'options'    => [
					'none' => esc_html__( 'Default', 'glozin-addons' ),
					'2'    => esc_html__( '2 lines', 'glozin-addons' ),
					'3'    => esc_html__( '3 lines', 'glozin-addons' ),
					'4'    => esc_html__( '4 lines', 'glozin-addons' ),
				],
				'default'    => 'none',
				'selectors' => [
					'{{WRAPPER}} .entry-title' => '--gz-line-clamp-count: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'post_meta_heading',
			[
				'label' => esc_html__( 'Meta', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'post_meta_typography',
				'selector' => '{{WRAPPER}} .entry-meta',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_slider',
			[
				'label' => esc_html__( 'Carousel Settings', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->register_carousel_style_controls();

		$this->end_controls_section();

	}

	/**
	 * Render widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$col = $settings['slides_to_show'];
		$col_tablet = ! empty( $settings['slides_to_show_tablet'] ) ? $settings['slides_to_show_tablet'] : $col;
		$col_mobile = ! empty( $settings['slides_to_show_mobile'] ) ? $settings['slides_to_show_mobile'] : $col;

		$this->add_render_attribute( 'wrapper', 'class', [ 'glozin-posts-carousel', 'hfeed', 'glozin-carousel--elementor', 'swiper' ] );
		$this->add_render_attribute( 'wrapper', 'data-desktop', $col );
		$this->add_render_attribute( 'wrapper', 'data-tablet', $col_tablet );
		$this->add_render_attribute( 'wrapper', 'data-mobile', $col_mobile );
        $this->add_render_attribute( 'wrapper', 'style', [ $this->render_space_between_style(), $this->render_aspect_ratio_style() ] );
		$this->add_render_attribute( 'inner', 'class', [ 'glozin-posts-carousel__inner', 'gz-post-grid', 'd-flex', 'swiper-wrapper' ] );
		$this->add_render_attribute( 'image', 'class', [ 'glozin-posts-carousel__image', 'post-thumbnail', 'gz-ratio', 'gz-eff-img-zoom' ] );

		$args = array(
			'post_type'              => 'post',
			'posts_per_page'         => $settings['limit'],
			'orderby'     			 => $settings['orderby'],
			'ignore_sticky_posts'    => 1,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'suppress_filters'       => false,
		);

		if ($settings['order'] != ''){
			$args['order'] = $settings['order'];
		}
		$category_name = is_array($settings['category']) ? implode(',', $settings['category']) : $settings['category'];
		if ( $category_name ) {
			$args['category_name'] = $category_name;
		}

		$posts = new \WP_Query( $args );

		if ( ! $posts->have_posts() ) {
			return '';
		}

		add_filter( 'post_class', [ $this, 'add_custom_post_class' ] );

		echo '<div '. $this->get_render_attribute_string( 'wrapper' ) .'>';
		echo '<div '. $this->get_render_attribute_string( 'inner' ) .'>';

		while ( $posts->have_posts() ) : $posts->the_post();
			$content_template = apply_filters( 'glozin_content_template_part', get_post_type() );
			get_template_part( 'template-parts/content/content', $content_template );

		endwhile;
		wp_reset_postdata();

		echo '</div>';
		echo '<div class="swiper-arrows">'. $this->render_arrows() .'</div>';
	   	echo $this->render_pagination();
		echo '</div>';

		remove_filter( 'post_class', [ $this, 'add_custom_post_class' ] );
	}

	/**
	 * Get terms array for select control
	 *
	 * @param string $taxonomy
	 * @return array
	 */
	public static function get_terms_options( $taxonomy = 'category' ) {
		$terms = Helper::get_terms_hierarchy( $taxonomy, '&#8212;' );

		if ( empty( $terms ) ) {
			return [];
		}

		$options = wp_list_pluck( $terms, 'name', 'slug' );

		return $options;
	}

	public function add_custom_post_class( $classes ) {
		$classes[] = 'd-flex flex-column gap-30 swiper-slide';
		return $classes;
	}
}