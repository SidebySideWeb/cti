<?php
namespace Glozin\Addons\Elementor\Builder\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Archive_Page_Header extends Widget_Base {

	public function get_name() {
		return 'glozin-archive-page-header';
	}

	public function get_title() {
		return esc_html__( '[Glozin] Archive Page Header', 'glozin-addons' );
	}

	public function get_icon() {
		return 'eicon-header';
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'archive', 'product', 'page', 'header' ];
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
            'page_header_content',
            [
                'label' => __( 'Page Header', 'glozin-addons' ),
            ]
        );

		$this->add_control(
			'elements',
			[
				'label' => esc_html__( 'Show Elements', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'options' => [
					'title'       => esc_html__('Title', 'glozin-addons'),
					'breadcrumb'  => esc_html__('BreadCrumb', 'glozin-addons'),
					'description' => esc_html__('Description', 'glozin-addons'),
				],
				'default' => [ 'title', 'description', 'breadcrumb' ],
			]
		);

		$this->add_control(
			'page_header_description_lines',
			[
				'label' => esc_html__( 'Number Lines Of Description', 'glozin-addons' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 100,
				'step' => 1,
				'default' => 5,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
            'page_header_style',
            [
                'label' => __( 'Style', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_responsive_control(
			'horizontal_position',
			[
				'label'                => esc_html__( 'Horizontal Position', 'glozin-addons' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'options'              => [
					'left'   => [
						'title' => esc_html__( 'Left', 'glozin-addons' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'glozin-addons' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'glozin-addons' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'     => '',
				'selectors'            => [
					'{{WRAPPER}} .page-header__content' => 'align-items: {{VALUE}}',
				],
				'selectors_dictionary' => [
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				],
			]
		);

		$this->add_responsive_control(
			'vertical_position',
			[
				'label'                => esc_html__( 'Vertical Position', 'glozin-addons' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'options'              => [
					'top'   => [
						'title' => esc_html__( 'Top', 'glozin-addons' ),
						'icon'  => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => esc_html__( 'Middle', 'glozin-addons' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom'  => [
						'title' => esc_html__( 'Bottom', 'glozin-addons' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'     => '',
				'selectors'            => [
					'{{WRAPPER}} .page-header__content' => 'justify-content: {{VALUE}}',
				],
				'selectors_dictionary' => [
					'top'   => 'flex-start',
					'middle' => 'center',
					'bottom'  => 'flex-end',
				],
			]
		);

		$this->add_responsive_control(
			'alignment',
			[
				'label'       => esc_html__( 'Alignment', 'glozin-addons' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'left'   => [
						'title' => esc_html__( 'Left', 'glozin-addons' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'glozin-addons' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'glozin-addons' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'     => '',
				'selectors'   => [
					'{{WRAPPER}} .page-header__content' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'background_image',
			[
				'label'   => esc_html__( 'Background Image', 'glozin-addons' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
				],
				'selectors' => [
					'{{WRAPPER}} .page-header.page-header--shop' => 'background-image: url("{{URL}}");',
				],
			]
		);

		$this->add_control(
			'background_color_overlay',
			[
				'label' => esc_html__( 'Background Color Overlay', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .page-header.page-header--shop::before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .page-header.page-header--shop' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .page-header.page-header--shop' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'breadcrumb_heading',
			[
				'label' => esc_html__( 'Breadcrumb', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'breadcrumb_spacing',
			[
				'label' => esc_html__( 'Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'selectors' => [
					'{{WRAPPER}} .site-breadcrumb' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'breadcrumb_text_heading',
			[
				'label' => esc_html__( 'Text', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'breadcrumb_text_typography',
				'selector' => '{{WRAPPER}} .site-breadcrumb',
			]
		);

		$this->add_control(
			'breadcrumb_text_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .site-breadcrumb' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'breadcrumb_link_heading',
			[
				'label' => esc_html__( 'Link', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'breadcrumb_link_typography',
				'selector' => '{{WRAPPER}} .site-breadcrumb a',
			]
		);

		$this->add_control(
			'breadcrumb_link_color',
			[
				'label' => esc_html__( 'Link Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .site-breadcrumb a, {{WRAPPER}} .site-breadcrumb span' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'breadcrumb_icon_heading',
			[
				'label' => esc_html__( 'Icon', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'breadcrumb_icon_size',
			[
				'label' => __( 'Icon Size', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					]
				],
				'default' => [],
				'selectors' => [
					'{{WRAPPER}} .site-breadcrumb .dot-between::after' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'breadcrumb_icon_color',
			[
				'label' => __( 'Icon Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .site-breadcrumb .dot-between::after' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'breadcrumb_icon_spacing',
			[
				'label' => __( 'Icon Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					]
				],
				'default' => [],
				'selectors' => [
					'{{WRAPPER}} .site-breadcrumb .dot-between::after' => 'margin-inline-start: {{SIZE}}{{UNIT}}; margin-inline-end: {{SIZE}}{{UNIT}}',
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

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .page-header__title',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .page-header__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label' => esc_html__( 'Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'selectors' => [
					'{{WRAPPER}} .page-header__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'description_heading',
			[
				'label' => esc_html__( 'Description', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'description_typography',
				'selector' => '{{WRAPPER}} .page-header__description',
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .page-header__description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if( empty( $settings['elements'] ) ) {
			return;
		}
		?>
		<div id="page-header" class="page-header page-header--shop">
			<div class="page-header__content position-relative d-flex flex-column <?php echo apply_filters('glozin_page_header_content_class', 'justify-content-center align-items-center text-center'); ?>">
				<?php
					if( in_array( 'breadcrumb', $settings['elements'] ) ) {
						$this->breadcrumb();
					}
				?>
				<?php
					if( in_array( 'title', $settings['elements'] ) ) {
						$this->title();
					}
				?>
				<?php
					if( in_array( 'description', $settings['elements'] ) ) {
						$this->description( $settings );
					}
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Show title
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function title() {
		$title = '<h1 class="page-header__title heading-letter-spacing h2 mt-0 mb-0">' . get_the_archive_title() . '</h1>';
		echo apply_filters('glozin_page_header_title', $title);
	}

	/**
	 * Show breadcrumb
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function breadcrumb() {
		if( class_exists( '\Glozin\Breadcrumb' ) ) {
			\Glozin\Breadcrumb::instance()->breadcrumb();
		}
	}

	/**
	 * Get description
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function description( $settings, $description = '' ) {
		ob_start();
		if( function_exists('is_shop') && is_shop() ) {
			woocommerce_product_archive_description();
		}

		$description = ob_get_clean();

		$description = apply_filters('glozin_page_header_description_html', '');
		if ( ! empty( $description ) ) {
			return $description;
		}

		$description = get_post_meta( \Glozin\Addons\Helper::get_post_ID(), '_page_header_description', true );

		if ( is_tax() ) {
			$term = get_queried_object();
			if ( $term ) {
				$description = $term->description;
			}
		}

		$description = apply_filters('glozin_page_header_description', $description);
		$number_lines = $settings['page_header_description_lines'];
		$number_lines = apply_filters('glozin_page_header_description_lines', $number_lines);
		$style = $number_lines ? '--gz-page-header-description-lines: '. esc_attr( $number_lines ) : '';

		$option = json_encode([
			'more' => esc_html__('Show More', 'glozin'),
			'less' => esc_html__('Show Less', 'glozin')
		]);

		if( empty( $description ) ) {
			if( \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
				$description = esc_html__( 'This is a description of the archive product.', 'glozin-addons' );
			}
		}

		if( $description ) {
			$this->add_render_attribute( 'description', 'class', [ 'page-header__description' ] );
			$this->add_render_attribute( 'description', 'style', $style );
			?>
			<div <?php echo $this->get_render_attribute_string( 'description' ); ?>>
				<div class="shop-header__description-inner"><?php echo wpautop(do_shortcode( $description )); ?></div>
				<div class="shop-header__more-wrapper hidden">
					<button class="shop-header__more gz-button-subtle mt-20" data-settings="<?php echo htmlspecialchars($option); ?>"><?php echo esc_html__('Show More', 'glozin-addons'); ?></button>
				</div>
			</div>
			<?php
		}
	}
}
