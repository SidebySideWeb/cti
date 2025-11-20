<?php
namespace Glozin\Addons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Content Preview Tabs widget.
 */
class Content_Preview_Tabs extends Widget_Base {
	use \Glozin\Addons\Elementor\Base\Aspect_Ratio_Base;

	/**
	 * Get widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-content-preview-tabs';
	}

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Glozin] Content Preview Tabs', 'glozin-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-table-of-contents';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'glozin-addons' ];
	}

	/**
	 * Scripts
	 *
	 * @return void
	 */
	public function get_script_depends() {
		return [
			'glozin-elementor-widget'
		];
	}

	/**
	 * Style
	 *
	 * @return void
	 */
	public function get_style_depends() {
		return [ 'glozin-elementor-css' ];
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->section_content();
		$this->section_style();
	}

	// Tab Content
	protected function section_content() {
		$this->start_controls_section(
			'section_content',
			[ 'label' => esc_html__( 'Content Tabs', 'glozin-addons' ) ]
		);

		$this->add_control(
			'heading',
			[
				'label'       => esc_html__( 'Heading', 'glozin-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'This is heading', 'glozin-addons' ),
				'label_block' => true,
			]
		);

        $repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'glozin-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'This is title', 'glozin-addons' ),
				'label_block' => true,
			]
		);
		
		$repeater->add_control(
			'link',
			[
				'label'       => __( 'Link', 'glozin-addons' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'https://your-link.com', 'glozin-addons' ),
				'default'     => [
					'url' => '#',
				],
			]
		);

		$repeater->add_control(
			'image',
			[
				'label'   => esc_html__( 'Image', 'glozin-addons' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => wc_placeholder_img_src(),
				],
			]
		);

		$this->add_control(
			'title_size',
			[
				'label' => __( 'Title HTML Tag', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h2',
			]
		);

        $this->add_control(
			'content_tabs',
			[
				'label'         => '',
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $repeater->get_controls(),
				'default'       => [
					[
						'title' => esc_html__( 'This is title', 'glozin-addons' ),
					],
					[
						'title' => esc_html__( 'This is title', 'glozin-addons' ),
					],
					[
						'title' => esc_html__( 'This is title', 'glozin-addons' ),
					]
				],
				'title_field'   => '{{{ title }}}',
				'prevent_empty' => false,
			]
		);

		$this->register_aspect_ratio_controls( [], [ 'aspect_ratio_type' => 'horizontal' ] );

		$this->end_controls_section();
	}

	// Tab Style
	protected function section_style() {
		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Content', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
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
				'selectors' => [
					'{{WRAPPER}} .glozin-content-preview-tabs__contents' => 'justify-content: {{VALUE}}',
				],
				'selectors_dictionary' => [
					'top'   => 'flex-start',
					'middle' => 'center',
					'bottom'  => 'flex-end',
				],
			]
		);

		$this->add_responsive_control(
			'width',
			[
				'label'     => __( 'Width', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-content-preview-tabs' => '--col-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'gap',
			[
				'label'     => __( 'Gap', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-content-preview-tabs' => '--col-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-content-preview-tabs__contents' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'heading_heading',
			[
				'label' => esc_html__( 'Heading', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'heading_typography',
				'selector' => '{{WRAPPER}} .glozin-content-preview-tabs__heading',
			]
		);

        $this->add_control(
			'heading_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-content-preview-tabs__heading' => 'color: {{VALUE}};',
				],
			]
		);

        $this->add_responsive_control(
			'heading_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-content-preview-tabs__heading' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'selector' => '{{WRAPPER}} .glozin-content-preview-tabs__title',
			]
		);

        $this->add_control(
			'title_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-content-preview-tabs__title' => '--gz-link-color: {{VALUE}};',
				],
			]
		);

        $this->add_control(
			'title_color_hover',
			[
				'label'     => __( 'Hover Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-content-preview-tabs__title' => '--gz-link-color-hover: {{VALUE}};',
				],
			]
		);

        $this->add_control(
			'title_color_active',
			[
				'label'     => __( 'Active Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .glozin-content-preview-tabs__title[data-active="true"]' => '--gz-link-color: {{VALUE}};',
				],
			]
		);

        $this->add_responsive_control(
			'title_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-content-preview-tabs__title ' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'title_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-content-preview-tabs__title ' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render icon box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$contents_html = [];
		$images_html = [];

        $this->add_render_attribute( 'wrapper', 'class', [ 'glozin-content-preview-tabs', 'd-flex', 'flex-column', 'flex-md-row' ] );
		$this->add_render_attribute( 'wrapper', 'style', $this->render_aspect_ratio_style() );

		$this->add_render_attribute( 'contents', 'class', [ 'glozin-content-preview-tabs__contents', 'd-flex', 'flex-column', 'aligm-items-center', 'justify-content-center', 'column-md-custom' ] );
		$this->add_render_attribute( 'images', 'class', [ 'glozin-content-preview-tabs__images', 'position-relative', 'gz-ratio', 'column-md-custom-remaining' ] );

		$this->add_render_attribute( 'heading', 'class', [ 'glozin-content-preview-tabs__heading', 'heading', 'mb-30' ] );
		$this->add_render_attribute( 'title', 'class', [ 'glozin-content-preview-tabs__title', 'heading-letter-spacing', 'mt-0', 'mb-0', 'py-10' ] );

		$this->add_render_attribute( 'image', 'class', [ 'glozin-content-preview-tabs__image', 'position-absolute', 'start-0', 'top-0', 'end-0', 'bottom-0', 'w-100', 'h-100' ] );

		if( ! empty( $settings['content_tabs'] ) ) :
			foreach( $settings['content_tabs'] as $key => $contents ) :
				$blank = ! empty( $contents['link']['is_external'] ) ? 'target="_blank"' : '';
				$nofollow = ! empty( $contents['link']['nofollow'] ) ? 'rel="nofollow"' : '';
				if( ! empty( $contents['title'] ) ) :
					$contents_html[] = sprintf( '<%s %s  data-key="%s" data-active="%s">
													<%s>
														%s
													</%s>
												</%s>',
												esc_attr( $settings['title_size'] ),
												$this->get_render_attribute_string( 'title' ),
												esc_attr( $key ),
												$key > 0 ? 'false' : 'true',
												! empty( $contents['link']['url'] ) ? 'a href="' . esc_url( $contents['link']['url'] ) . '" ' . $blank . ' ' . $nofollow : 'span',
												wp_kses_post( $contents['title'] ),
												! empty( $contents['link']['url'] ) ? 'a' : 'span',
												esc_attr( $settings['title_size'] ),
											);
				endif;

				if( ! empty( $contents['image'] ) ) :
					$img_args = [ 'image' => '', 'image_size' => 'full' ];
					$img_args['image'] = $contents['image'];
					$images_html[] = sprintf( '<div %s  data-key="%s" data-active="%s">
												<%s>
													%s
												</%s>
											</div>',
											$this->get_render_attribute_string( 'image' ),
											esc_attr( $key ),
											$key > 0 ? 'false' : 'true',
											! empty( $contents['link']['url'] ) ? 'a href="' . esc_url( $contents['link']['url'] ) . '" ' . $blank . ' ' . $nofollow : 'span',
											\Elementor\Group_Control_Image_Size::get_attachment_image_html( $img_args ),
											! empty( $contents['link']['url'] ) ? 'a' : 'span'
										);
				endif;
			endforeach;
		endif;

		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<div <?php echo $this->get_render_attribute_string( 'contents' ); ?>>
				<?php if( ! empty( $settings['heading'] ) ) : ?>
					<div <?php echo $this->get_render_attribute_string( 'heading' ); ?>><?php echo wp_kses_post( $settings['heading'] ); ?></div>
				<?php endif; ?>
				<?php if( ! empty( $contents_html ) ) : ?>
					<?php echo implode( '', $contents_html ); ?>
				<?php endif; ?>
			</div>
			<div <?php echo $this->get_render_attribute_string( 'images' ); ?>>
			<?php if( ! empty( $images_html ) ) : ?>
					<?php echo implode( '', $images_html ); ?>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}