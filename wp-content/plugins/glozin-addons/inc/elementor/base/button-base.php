<?php
namespace Glozin\Addons\Elementor\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;

trait Button_Base {
	/**
	 * Register controls
	 *
	 * @param array $controls
	 */
	protected function register_button_repeater_controls( $repeater) {
		$repeater->add_control(
			'button_text',
			[
				'label'       => __( 'Text', 'glozin-addons' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => __( 'Click here', 'glozin-addons' ),
				'placeholder' => __( 'Click here', 'glozin-addons' ),
			]
		);

		$repeater->add_control(
			'button_link',
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
	}

	/**
	 * Register controls
	 *
	 * @param array $controls
	 */
	protected function register_button_controls( $hide_link = false, $button_text_label = '', $button_link_label = '', $button_default_text = '' ) {
		$button_text_label = empty( $button_text_label ) ? __( 'Text', 'glozin-addons' ) : $button_text_label;
		$button_link_label = empty( $button_link_label ) ? __( 'Link', 'glozin-addons' ) : $button_link_label;
		$button_default_text = empty( $button_default_text ) ? esc_html__( 'Click here', 'glozin-addons' ) : $button_default_text;

		$this->add_control(
			'button_text',
			[
				'label'       => $button_text_label,
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => $button_default_text,
				'placeholder' => $button_default_text,
			]
		);

		if ( ! $hide_link ) {
			$this->add_control(
				'button_link',
				[
					'label'       => $button_link_label,
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
		}
	}

	/**
	 * Register controls style
	 *
	 * @param array $controls
	 */
	protected function register_button_style_controls( $default_style = '', $default_classes = '', $prefix = '' ) {
		$default_classes = $default_classes != '' ? $default_classes : 'glozin-button';
		$prefix = $prefix !== '' ? $prefix . '_' : $prefix;

		$this->add_control(
			$prefix . 'button_style',
			[
				'label'   => __( 'Style', 'glozin-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => $default_style,
				'options' => [
					''             => __( 'Solid Dark', 'glozin-addons' ),
					'light'        => __( 'Solid Light', 'glozin-addons' ),
					'outline-dark' => __( 'Outline Dark', 'glozin-addons' ),
					'outline'      => __( 'Outline Light', 'glozin-addons' ),
					'subtle'       => __( 'Underline', 'glozin-addons' ),
					'text'         => __( 'Text', 'glozin-addons' ),
				],
			]
		);

		$this->add_responsive_control(
			$prefix . 'button_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .' . $default_classes => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			$prefix . 'button_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .' . $default_classes => '--gz-button-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .' . $default_classes => '--gz-button-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => $prefix . 'button_typography',
				'selector' => '{{WRAPPER}} .' . $default_classes,
			]
		);

		$this->add_responsive_control(
			$prefix . 'button_min_width',
			[
				'label' => esc_html__( 'Min Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'size_units' => [ 'px', 'em', 'rem', '%', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .' . $default_classes => 'min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			$prefix . 'button_min_height',
			[
				'label' => esc_html__( 'Min Height', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'size_units' => [ 'px', 'em', 'rem' ],
				'selectors' => [
					'{{WRAPPER}} .' . $default_classes => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			$prefix . 'button_border_width',
			[
				'label' => esc_html__( 'Border Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'size_units' => [ 'px', 'em', 'rem' ],
				'selectors' => [
					'{{WRAPPER}} .' . $default_classes => '--gz-button-border-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					$prefix . 'button_style' => [ 'outline-dark', 'outline', 'subtle' ],
				],
			]
		);

		$this->start_controls_tabs( $prefix . 'tabs_button_style' );

		$this->start_controls_tab(
			$prefix . 'tab_button_normal',
			[
				'label' => __( 'Normal', 'glozin-addons' ),
			]
		);

		$this->add_control(
			$prefix . 'button_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .' . $default_classes => '--gz-button-bg-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$prefix . 'button_text_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .' . $default_classes => '--gz-button-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$prefix . 'button_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .' . $default_classes => '--gz-button-border-color: {{VALUE}};',
				],
				'condition' => [
					$prefix . 'button_style' => [ 'outline-dark', 'outline', 'subtle' ],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			$prefix . 'tab_button_hover',
			[
				'label' => __( 'Hover', 'glozin-addons' ),
			]
		);

		$this->add_control(
			$prefix . 'button_background_hover_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .' . $default_classes => '--gz-button-bg-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$prefix . 'hover_color',
			[
				'label'     => __( 'Text Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .' . $default_classes => '--gz-button-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$prefix . 'button_hover_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .' . $default_classes => '--gz-button-border-color-hover: {{VALUE}};',
				],
				'condition' => [
					$prefix . 'button_style' => [ 'outline-dark', 'outline', 'subtle' ],
				],
			]
		);

		if( ! get_theme_mod('button_eff_hover_bg_disable', false) ) {
			$this->add_control(
				$prefix . 'button_background_effect_hover_color',
				[
					'label'     => __( 'Background Effect Color', 'glozin-addons' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .' . $default_classes => '--gz-button-eff-bg-color-hover: {{VALUE}};',
					],
					'condition' => [
						$prefix . 'button_style' => ['', 'light', 'outline-dark'],
					]
				]
			);
		}

		$this->end_controls_tab();

		$this->end_controls_tabs();
	}

	/**
	 * Render button for shortcode.
	 *
	 */
	protected function render_button( $repeater = '', $index = '', $button_link = '', $args = [] ) {
		$settings 	= $this->get_settings_for_display();

		$repeater 	= ! empty( $repeater ) ? $repeater : $this->get_settings_for_display();
		if ( ! empty( $index ) ) {
			$button_key = $this->get_repeater_setting_key( 'button', 'button_index', $index );
			$text_key   = $this->get_repeater_setting_key( 'text', 'button_index', $index );
		} else {
			$button_key = 'button';
			$text_key   = 'text';
		}

		if ( empty( $args['no_text'] ) && empty( $repeater['button_text'] ) ) {
			return;
		}

		$is_new   	= Icons_Manager::is_migration_allowed();

		$button_link = ! empty( $button_link ) ? $button_link : $repeater['button_link'];

		if ( ! empty( $button_link['url'] ) ) {
			$this->add_link_attributes( $button_key, $button_link );
			$this->add_render_attribute( $button_key, 'class', 'glozin-button-link' );
		} elseif ( ! empty( $button_link ) ) {
			$this->add_render_attribute( $button_key, 'href', $button_link);
		}

		$this->add_render_attribute( $button_key, 'class', 'glozin-button' );

		if ( ! empty( $settings['button_css_id'] ) ) {
			$this->add_render_attribute( $button_key, 'id', $settings['button_css_id'] );
		}

		if ( isset( $repeater['button_text_classes'] ) ) {
			$this->add_render_attribute( $text_key, 'class', $repeater['button_text_classes'] );
		}

		$classes = 'gz-button';
		$classes .= ! empty( $repeater['button_classes'] ) ? $repeater['button_classes'] : '';
		$classes .= ! empty( $settings['button_style'] ) ? ' gz-button-'  . $settings['button_style'] : '';
		$classes .= empty( $args['no_text'] ) && ! empty( $settings['button_style'] ) && in_array( $settings['button_style'], ['', 'light', 'outline-dark'] ) ? ' px-30 gz-button-hover-effect' : '';

		if( ! empty( $args['classes'] ) ) {
			$classes .= ' ' . $args['classes'];
		}

		if( ! empty( $args['no_text'] ) ) {
			$classes .= ' gz-button-icon';
		}

		$this->add_render_attribute( $button_key, 'class', $classes );

		$this->add_render_attribute( [
			'content-wrapper' => [
				'class' => 'glozin-button-content-wrapper',
			],
			'icon-align'      => [
				'class' => [
					'glozin-svg-icon',
				],
			],
			$text_key            => [
				'class' => 'glozin-button-text',
			],
		] );

		$icon_default = ! empty( $args['icon_default'] ) ? $args['icon_default'] : '';

		$this->add_inline_editing_attributes( $text_key, 'none' );
		$button_text =  empty( $args['no_text'] ) ? sprintf('<span %s>%s</span>', $this->get_render_attribute_string( $text_key ), $repeater['button_text']) : '';
		$aria_label = ! empty( $args['aria_label'] ) ? $args['aria_label'] : '';
		if( empty( $aria_label ) ) {
			$aria_label = ! empty( $repeater['button_text'] ) ? esc_html__( 'Link for', 'glozin-addons' ) . ' ' . $repeater['button_text'] : esc_html__( 'View more', 'glozin-addons' );
		}
		
		?>
		<a <?php echo $this->get_render_attribute_string( $button_key ); ?> aria-label="<?php echo esc_attr( $aria_label ); ?>">
			<?php if ( ! empty( $settings['button_icon']['value'] ) ) : ?>
				<span <?php echo $this->get_render_attribute_string( 'icon-align' ); ?>>
					<?php if ( $is_new ) :
						Icons_Manager::render_icon( $settings['button_icon'], [ 'aria-hidden' => 'true' ] );
					endif; ?>
				</span>
			<?php else : ?>
				<?php echo $icon_default; ?>
			<?php endif; ?>

			<?php echo $button_text; ?>
		</a>
		<?php
	}

}