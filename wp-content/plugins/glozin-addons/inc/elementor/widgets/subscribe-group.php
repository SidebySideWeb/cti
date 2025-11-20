<?php
namespace Glozin\Addons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor heading widget.
 *
 * Elementor widget that displays an eye-catching headlines.
 *
 * @since 1.0.0
 */
class Subscribe_Group extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-subscribe-group';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Glozin] Subscribe Group', 'glozin-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-form-horizontal';
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
		return [ 'subscribe box', 'subscribe group', 'form', 'currency', 'language', 'glozin-addons' ];
	}

	/**
	 * Scripts
	 *
	 * @return void
	 */
	public function get_script_depends() {
		return [
			'glozin-subscribe-form-widget',
		];
	}

	/**
	 * Styles
	 *
	 * @return void
	 */
	public function get_style_depends() {
		return [
			'glozin-elementor-css',
		];
	}
	/**
	 * Register the widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->content_sections();
		$this->style_sections();
	}

	protected function content_sections() {
		// Content
		$this->start_controls_section(
			'section_subscribe_box',
			[ 'label' => __( 'Subscribe Box', 'glozin-addons' ) ]
		);

		$this->add_control(
			'type',
			[
				'label' => esc_html__( 'Type', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'mailchimp'  => esc_html__( 'Mailchimp', 'glozin-addons' ),
					'shortcode' => esc_html__( 'Use Shortcode', 'glozin-addons' ),
				],
				'default' => 'mailchimp',
			]
		);

		$this->add_control(
			'form',
			[
				'label'   => esc_html__( 'Mailchimp Form', 'glozin-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $this->get_contact_form(),
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => '==',
							'value' => 'mailchimp'
						],
					],
				],
			]
		);

		$this->add_control(
			'form_shortcode',
			[
				'label' => __( 'Enter your shortcode', 'glozin-addons' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => '',
				'placeholder' => '[gallery id="123" size="medium"]',
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => '==',
							'value' => 'shortcode'
						],
					],
				],
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => '',
				'label_block' => true,
				'separator' => 'before',
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
				'default' => 'h4',
			]
		);

		$this->add_control(
			'description',
			[
				'label' => __( 'Description', 'glozin-addons' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'default' => '',
			]
		);

		$this->add_control(
			'after_description',
			[
				'label' => __( 'After Description', 'glozin-addons' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'default' => '',
			]
		);

		$this->end_controls_section();

	}

	protected function style_sections() {
		$this->start_controls_section(
			'style_content',
			[
				'label'     => __( 'Content', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'toggle_menu',
			[
				'label'        => __( 'Toggle Menu on Mobile', 'glozin-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_off'    => __( 'Off', 'glozin-addons' ),
				'label_on'     => __( 'On', 'glozin-addons' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'heading_icon',
			[
				'label' => __( 'Arrow Icon', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'toggle_menu' => 'yes',
				],
			]
		);

		$this->add_control(
			'style_icons',
			[
				'label' => __( 'Icon Normal', 'glozin-addons' ),
				'type' => Controls_Manager::ICONS,
				'default' => [],
				'condition' => [
					'toggle_menu' => 'yes',
				],
			]
		);

		$this->add_control(
			'style_icons_active',
			[
				'label' => __( 'Icon Active', 'glozin-addons' ),
				'type' => Controls_Manager::ICONS,
				'default' => [],
				'condition' => [
					'toggle_menu' => 'yes',
					'style_icons[value]!' => '',
				],
			]
		);

		$this->add_control(
			'style_form',
			[
				'label' => __( 'Form', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'form_type',
				[
				'label' => esc_html__( 'Type', 'glozin-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'row' => [
						'title' => esc_html__( 'Row', 'glozin-addons' ),
						'icon' => 'eicon-arrow-right',
					],
					'column' => [
						'title' => esc_html__( 'Column', 'glozin-addons' ),
						'icon' => 'eicon-arrow-down',
					],
				],
				'default' => 'row',
			]
		);

		$this->add_control(
			'style_input',
			[
				'label' => __( 'Input', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'input_bgcolor',
			[
				'label' => __( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-subscribe-box__content' => '--gz-input-bg-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_color',
			[
				'label' => __( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-subscribe-box__content' => '--gz-input-color: {{VALUE}}; --gz-input-placeholder-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_border_color',
			[
				'label' => __( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-subscribe-box__content' => '--gz-input-border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_border_color_hover',
			[
				'label' => __( 'Border Color Hover', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-subscribe-box__content,
					{{WRAPPER}} .glozin-subscribe-box__content .mc4wp-form-row.focused' => '--gz-input-border-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}}' => '--gz-input-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}}' => '--gz-input-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'input_spacing_right',
			[
				'label'     => esc_html__( 'Spacing Right', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-subscribe-box__type-row input[type="email"]' => 'padding-inline-end: {{SIZE}}{{UNIT}}',
					'.rtl {{WRAPPER}} .glozin-subscribe-box__type-row input[type="email"]' => 'padding-inline-start: {{SIZE}}{{UNIT}}; padding-inline-end: var(--gz-input-padding-x)',
				],
				'condition'   => [
					'form_type' => 'row',
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

		$this->add_control(
			'button_bg_color',
			[
				'label' => __( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-subscribe-box__content' => '--gz-button-bg-color: {{VALUE}}; --gz-button-bg-color-hover: {{VALUE}}; --gz-button-border-color: {{VALUE}}; --gz-button-border-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_color',
			[
				'label' => __( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-subscribe-box__content' => '--gz-button-color: {{VALUE}}; --gz-button-color-hover: {{VALUE}};',
				],
			]
		);

    	$this->add_control(
			'button_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}}' => '--gz-button-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}}' => '--gz-button-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'style_message',
			[
				'label' => __( 'Message', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'message_error_color',
			[
				'label' => __( 'Error Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-subscribe-box__content .mc4wp-response .mc4wp-error' => 'color: {{VALUE}};',
					'{{WRAPPER}} .glozin-subscribe-box__content .mc4wp-response .mc4wp-error a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'message_success_color',
			[
				'label' => __( 'Success Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-subscribe-box__content .mc4wp-response .mc4wp-success' => 'color: {{VALUE}};',
					'{{WRAPPER}} .glozin-subscribe-box__content .mc4wp-response .mc4wp-success a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'style_title',
			[
				'label' => __( 'Title', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-subscribe-box__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .glozin-subscribe-box__title',
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label' => __( 'Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-subscribe-box__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'style_description',
			[
				'label' => __( 'Description', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => __( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-subscribe-box__description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'description_typography',
				'selector' => '{{WRAPPER}} .glozin-subscribe-box__description',
			]
		);

		$this->add_responsive_control(
			'description_spacing',
			[
				'label' => __( 'Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-subscribe-box__description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'style_after_description',
			[
				'label' => __( 'After Description', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'after_description_color',
			[
				'label' => __( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-subscribe-box__after-description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'after_description_color_link',
			[
				'label' => __( 'Color Link', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-subscribe-box__after-description a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'after_description_typography',
				'selector' => '{{WRAPPER}} .glozin-subscribe-box__after-description',
			]
		);

		$this->add_responsive_control(
			'after_description_spacing',
			[
				'label' => __( 'Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-subscribe-box__after-description' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', [ 'glozin-subscribe-box', 'glozin-subscribe-group', 'glozin-subscribe-box__type-' . $settings['form_type'] ] );
		$this->add_render_attribute( 'content', 'class', [ 'glozin-subscribe-box__content' ] );
		$this->add_render_attribute( 'title', 'class', [ 'glozin-subscribe-box__title', 'fw-semibold', 'mt-0', 'mb-20', 'mb-md-25', 'h6' ] );
		$this->add_render_attribute( 'description', 'class', [ 'glozin-subscribe-box__description', 'mb-25' ] );
		$this->add_render_attribute( 'after_description', 'class', [ 'glozin-subscribe-box__after-description', 'mt-15' ] );

		if ( $settings['toggle_menu'] == 'yes' ) {
			$this->add_render_attribute( 'wrapper', 'class', [ 'glozin-toggle-mobile__wrapper' ] );
			$this->add_render_attribute( 'content', 'class', [ 'glozin-toggle-mobile__content' ] );
			$this->add_render_attribute( 'title', 'class', [ 'glozin-toggle-mobile__title', 'd-flex', 'align-items-center', 'justify-content-between', 'position-relative' ] );
		}

		$output = sprintf(
			'<div class="glozin-subscribe-box__content">%s</div>',
			do_shortcode( '[mc4wp_form id="' . esc_attr( $settings['form'] ) . '"]' ),
		);
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<?php if ( $settings['title'] ) : ?>
				<<?php echo $settings['title_size']; ?> <?php echo $this->get_render_attribute_string( 'title' ); ?>>
					<?php echo $settings['title']; ?>
					<?php if ( $settings['toggle_menu'] == 'yes' ) : ?>
						<?php
							if ( ! empty( $settings['style_icons']['value'] ) ) {
								$collapse_icon = '<span class="glozin-svg-icon glozin-subscribe-box__icon glozin-subscribe-box__icon-default hidden-md hidden-lg hidden-xl">';
								$collapse_icon .= $this->get_icon_html( $settings['style_icons'], [ 'aria-hidden' => 'true' ] );
								$collapse_icon .= '</span>';

								if ( ! empty( $settings['style_icons_active']['value'] ) ) {
									$collapse_icon .= '<span class="glozin-svg-icon glozin-subscribe-box__icon glozin-subscribe-box__icon-active hidden-md hidden-lg hidden-xl">';
									$collapse_icon .= $this->get_icon_html( $settings['style_icons_active'], [ 'aria-hidden' => 'true' ] );
									$collapse_icon .= '</span>';
								}
							} else {
								$collapse_icon = '<span class="gz-collapse-icon"></span>';
							}

							echo $collapse_icon;
						?>
					<?php endif; ?>
				</<?php echo $settings['title_size']; ?>>
			<?php endif; ?>
			<div <?php echo $this->get_render_attribute_string( 'content' ); ?>>
				<?php if ( $settings['description'] ) : ?>
					<div <?php echo $this->get_render_attribute_string( 'description' ); ?>><?php echo $settings['description']; ?></div>
				<?php endif; ?>
				<?php
					if( $settings['type'] == 'mailchimp' ) {
						echo sprintf(
							'<div class="glozin-subscribe-box__content">%s</div>',
							do_shortcode( '[mc4wp_form id="' . esc_attr( $settings['form'] ) . '"]' ),
						);
					} else {
						echo do_shortcode(  $settings['form_shortcode'] );
					}
				?>
				<?php if ( $settings['after_description'] ) : ?>
					<div <?php echo $this->get_render_attribute_string( 'after_description' ); ?>><?php echo $settings['after_description']; ?></div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Get Contact Form
	 */
	protected function get_contact_form() {
		$mail_forms    = get_posts( 'post_type=mc4wp-form&posts_per_page=-1' );
		$mail_form_ids = array(
			'' => esc_html__( 'Select Form', 'glozin-addons' ),
		);
		foreach ( $mail_forms as $form ) {
			$mail_form_ids[$form->ID] = $form->post_title;
		}

		return $mail_form_ids;
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