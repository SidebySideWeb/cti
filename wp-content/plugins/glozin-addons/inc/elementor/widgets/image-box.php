<?php
namespace Glozin\Addons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor widget.
 *
 * Elementor widget that displays an eye-catching headlines.
 *
 * @since 1.0.0
 */
class Image_Box extends Widget_Base {
	use \Glozin\Addons\Elementor\Base\Aspect_Ratio_Base;
	use \Glozin\Addons\Elementor\Base\Button_Base;

	/**
	 * Get widget name.
	 *
	 * Retrieve Stores Location widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-image-box';
	}

	/**
	 * Get widget title
	 *
	 * Retrieve Stores Location widget title
	 *
	 * @return string Widget title
	 */
	public function get_title() {
		return __( '[Glozin] Image Box', 'glozin-addons' );
	}

	/**
	 * Get widget icon
	 *
	 * Retrieve TeamMemberGrid widget icon
	 *
	 * @return string Widget icon
	 */
	public function get_icon() {
		return 'eicon-image-box';
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
	 * Register heading widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
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
			[
				'label' => __( 'Content', 'glozin-addons' ),
			]
		);

        $this->add_responsive_control(
			'image',
			[
				'label'    => __( 'Image', 'glozin-addons' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => wc_placeholder_img_src(),
				],
			]
		);

		$this->add_control(
			'title', [
				'label' => esc_html__( 'Title', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Title', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'description', [
				'label' => esc_html__( 'Description', 'glozin-addons' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'Description', 'glozin-addons' ),
			]
		);

        $this->add_control(
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

		$this->add_control(
			'show_button',
			[
				'label'     => esc_html__( 'Show Button', 'glozin-addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__( 'Yes', 'glozin-addons' ),
				'label_off' => esc_html__( 'No', 'glozin-addons' ),
				'return_value' => 'yes',
				'default'   => '',
			]
		);

		$this->add_control(
			'clickable_button',
			[
				'label'     => esc_html__( 'Clickable only on button', 'glozin-addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__( 'Yes', 'glozin-addons' ),
				'label_off' => esc_html__( 'No', 'glozin-addons' ),
				'return_value' => 'yes',
				'default'   => 'yes',
				'condition' => [
					'show_button' => 'yes',
				],
			]
		);

		$this->add_control(
			'button_text', [
				'label' => esc_html__( 'Button Text', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Button Text', 'glozin-addons' ),
				'condition' => [
					'show_button' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

    // Tab Style
	protected function section_style() {
		$this->start_controls_section(
			'section_style',
			[
				'label'     => __( 'Content', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'text_align',
			[
				'label'       => esc_html__( 'Text Align', 'glozin-addons' ),
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
					'{{WRAPPER}} .glozin-image-box' => 'text-align: {{VALUE}}',
				],
			]
		);

        $this->add_control(
			'image_icon_heading',
			[
				'label' => esc_html__( 'Image & Icon', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->register_aspect_ratio_controls( [], [ 'aspect_ratio_type' => 'square' ] );

        $this->add_responsive_control(
			'image_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-image-box' => '--gz-image-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-image-box' => '--gz-image-rounded: {{RIGHT}}{{UNIT}} {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_spacing',
			[
				'label'   => esc_html__( 'Spacing', 'glozin-addons' ),
				'type'    => Controls_Manager::SLIDER,
				'range'   => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-image-box__image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
				'selector' => '{{WRAPPER}} .glozin-image-box__title',
			]
		);

        $this->add_control(
			'title_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-image-box__title' => 'color: {{VALUE}};',
				],
			]
		);

        $this->add_control(
			'title_color_hover',
			[
				'label'     => __( 'Hover Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-image-box__title:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label'   => esc_html__( 'Spacing', 'glozin-addons' ),
				'type'    => Controls_Manager::SLIDER,
				'range'   => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-image-box__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .glozin-image-box__description',
			]
		);

        $this->add_control(
			'description_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-image-box__description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_heading',
			[
				'label' => esc_html__( 'Button', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'button_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-button' => 'margin-top: {{TOP}}{{UNIT}}; margin-inline-end: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}}; margin-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->register_button_style_controls('outline');

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

        $this->add_render_attribute( 'wrapper', 'class', [ 'glozin-image-box', 'd-flex', 'flex-column', 'align-items-center' ] );
		$this->add_render_attribute( 'description', 'class', [ 'glozin-image-box__description' ] );
        $this->add_render_attribute( 'content', 'class', [ 'glozin-image-box__content', 'w-100' ] );
		$this->add_render_attribute( 'image-link', 'class', [ 'glozin-image-box__image', 'gz-hover-zoom', 'w-100', 'gz-hover-effect', 'overflow-hidden', 'gz-image-rounded', 'gz-ratio', 'position-relative', 'mb-25' ] );
		$this->add_render_attribute( 'image-link', 'style', $this->render_aspect_ratio_style() );
		$this->add_render_attribute( 'title-link', 'class', [ 'glozin-image-box__title', 'fs-18', 'lh-1', 'fw-semibold', 'mt-0', 'mb-10', 'd-inline-block' ] );

		$link_check = true;
		if ( empty( $settings['link']['url'] ) ) {
			$link_check = false;
		} elseif ( $settings['show_button'] == 'yes' && $settings['clickable_button'] == 'yes' ) {
			$link_check = false;
		}

	?>
        <div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<?php
				if ( $link_check ) {
					$this->add_link_attributes( 'image-link', $settings['link'] );
					$aria_label = ! empty( $settings['title'] ) ? esc_html__( 'Link for', 'glozin-addons' ) . ' ' . $settings['title'] : esc_html__( 'Link for image', 'glozin-addons' );
					$this->add_render_attribute( 'image-link', 'aria-label', $aria_label );
					echo '<a '. $this->get_render_attribute_string( 'image-link' ) .'>';
				} else {
					echo '<div '. $this->get_render_attribute_string( 'image-link' ) .'>';
				}
			?>
				<?php if( ! empty( $settings['image'] ) && ! empty( $settings['image']['url'] ) ) : ?>
					<?php
						$image_args = [
							'image'        => ! empty( $settings['image'] ) ? $settings['image'] : '',
							'image_tablet' => ! empty( $settings['image_tablet'] ) ? $settings['image_tablet'] : '',
							'image_mobile' => ! empty( $settings['image_mobile'] ) ? $settings['image_mobile'] : '',
						];
						echo \Glozin\Addons\Helper::get_responsive_image_elementor( $image_args );
					?>
				<?php endif; ?>
			<?php
				if ( $link_check ) {
					echo '</a>';
				} else {
					echo '</div>';
				}
			?>
			<div <?php echo $this->get_render_attribute_string( 'content' ); ?>>
				<?php if( ! empty( $settings['title'] ) ) : ?>
					<?php
						if ( $link_check ) {
							$this->add_link_attributes( 'title-link', $settings['link'] );
							$this->add_render_attribute( 'title-link', 'class', [ 'd-inline-block' ] );
							echo '<a '. $this->get_render_attribute_string( 'title-link' ) .'>';
						} else {
							echo '<h3 '. $this->get_render_attribute_string( 'title-link' ) .'>';
						}
					?>
					<?php echo wp_kses_post( $settings['title'] ); ?>
					<?php
						if ( $link_check ) {
							echo '</a>';
						} else {
							echo '</h3>';
						}
					?>
				<?php endif; ?>
				<?php if( ! empty( $settings['description'] ) ) : ?>
					<div <?php echo $this->get_render_attribute_string( 'description' ); ?>>
						<?php echo wp_kses_post( $settings['description'] ); ?>
					</div>
				<?php endif; ?>
				<?php
					if ( $settings['show_button'] == 'yes' ) {
						$settings['button_link'] = $settings['link'];
						$settings['button_classes'] = ' mt-25';
						$this->render_button($settings);
					}
				?>
			</div>
        </div>
    <?php
	}
}