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
 * Accordion widget.
 */
class Accordion extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-accordion';
	}

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Glozin] Accordion', 'glozin-addons' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-accordion';
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
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'accordion', 'tabs', 'toggle', 'glozin-addons' ];
	}

	/**
	 * Script
	 *
	 * @return void
	 */
	public function get_script_depends() {
		return [
			'glozin-accordion-widget'
		];
	}

	public function get_style_depends(): array {
		return [ 'glozin-accordion-css' ];
	}

	/**
	 * Register accordion widget controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => __( 'Accordion', 'glozin-addons' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'tab_title',
			[
				'label' => __( 'Title', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Accordion Title', 'glozin-addons' ),
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'tab_content',
			[
				'label' => __( 'Description', 'glozin-addons' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => __( 'Accordion Content', 'glozin-addons' ),
				'show_label' => false,
			]
		);

		$this->add_control(
			'tabs',
			[
				'label' => __( 'Accordion Items', 'glozin-addons' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'tab_title' => __( 'Accordion #1', 'glozin-addons' ),
						'tab_content' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'glozin-addons' ),
					],
					[
						'tab_title' => __( 'Accordion #2', 'glozin-addons' ),
						'tab_content' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'glozin-addons' ),
					],
				],
				'title_field' => '{{{ tab_title }}}',
			]
		);

		$this->add_control(
			'title_html_tag',
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
				],
				'default' => 'div',
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_accordion',
			[
				'label' => __( 'Accordion', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'items_first_item',
			[
				'label'     => esc_html__( 'Active First Item', 'glozin-addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__( 'Yes', 'glozin-addons' ),
				'label_off' => esc_html__( 'No', 'glozin-addons' ),
				'return_value' => 'yes',
				'default'   => '',
			]
		);

		$this->add_control(
			'heading_items',
			[
				'label' => __( 'Items', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'items_padding',
			[
				'label'      => esc_html__( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-accordion__item' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'items_margin',
			[
				'label' => __( 'Spacing Bottom', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					]
				],
				'default' => [],
				'selectors' => [
					'{{WRAPPER}} .glozin-accordion__item' => 'margin-bottom: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .glozin-accordion__item:last-child' => 'margin-bottom: 0',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'content_item_border',
				'label' => esc_html__( 'Border', 'glozin-addons' ),
				'selector' => '{{WRAPPER}} .glozin-accordion__item',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'content_box_shadow',
				'label' => __( 'Box Shadow Active', 'glozin-addons' ),
				'selector' => '{{WRAPPER}} .glozin-accordion__item.glozin-tab--active',
			]
		);

		$this->add_control(
			'heading_title',
			[
				'label' => __( 'Title', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'title_padding',
			[
				'label'      => esc_html__( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-accordion__title a' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-accordion__title, {{WRAPPER}} .glozin-accordion__title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .glozin-accordion__title',
			]
		);

		$this->add_control(
			'heading_content',
			[
				'label' => __( 'Description', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => esc_html__( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-accordion__content' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'content_color',
			[
				'label' => __( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-accordion__content' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'selector' => '{{WRAPPER}} .glozin-accordion__content',
			]
			);

		$this->add_control(
			'heading_icon',
			[
				'label' => __( 'Icon', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'style_icons_position',
			[
				'label'                => esc_html__( 'Icon Position', 'glozin-addons' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'options'              => [
					'left'   => [
						'title' => esc_html__( 'Left', 'glozin-addons' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'glozin-addons' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'prefix_class' => 'glozin-accordion__icon-position--',
			]
		);

		$this->add_control(
			'style_icons',
			[
				'label' => __( 'Icon', 'glozin-addons' ),
				'type' => Controls_Manager::ICONS,
				'default' => [],
			]
		);

		$this->add_control(
			'style_icons_active',
			[
				'label' => __( 'Icon Active', 'glozin-addons' ),
				'type' => Controls_Manager::ICONS,
				'default' => [],
				'condition' => [
					'style_icons[value]!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'style_icons_padding',
			[
				'label'      => esc_html__( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-accordion__icon' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Size', 'glozin-addons' ),
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
					'{{WRAPPER}} .glozin-accordion__icon' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .glozin-accordion__icon--default' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'style_border_icon',
				'label' => esc_html__( 'Border', 'glozin-addons' ),
				'selector' => '{{WRAPPER}} .glozin-accordion__icon',
			]
		);

		$this->start_controls_tabs(
			'style_tabs_icon'
		);

		$this->start_controls_tab(
			'style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-accordion__icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_bgcolor',
			[
				'label' => __( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-accordion__icon' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-accordion__icon' => 'margin-top: {{TOP}}{{UNIT}}; margin-inline-end: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}}; margin-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_hover_tab',
			[
				'label' => esc_html__( 'Active', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'icon_active_color',
			[
				'label' => __( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-tab--active .glozin-accordion__icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_active_bgcolor',
			[
				'label' => __( 'Background Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-tab--active .glozin-accordion__icon' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_active_border_color',
			[
				'label' => __( 'Border Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-tab--active .glozin-accordion__icon' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_active_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-tab--active .glozin-accordion__icon' => 'margin-top: {{TOP}}{{UNIT}}; margin-inline-end: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}}; margin-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render accordion widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$id_int = substr( $this->get_id_int(), 0, 3 );
		?>
		<div class="glozin-accordion">
			<?php
			foreach ( $settings['tabs'] as $index => $item ) :
				$tab_count = $index + 1;

				$tab_title_setting_key = $this->get_repeater_setting_key( 'tab_title', 'tabs', $index );
				$tab_content_setting_key = $this->get_repeater_setting_key( 'tab_content', 'tabs', $index );

				$classes_title = ! empty( $settings['style_icons']['value'] ) ? 'glozin-accordion__title-icon' : 'glozin-accordion__title-default';
				$classes_title .= $settings['items_first_item'] == 'yes' && $tab_count == 1 ? ' glozin-tab--first-active' : '';

				$this->add_render_attribute( $tab_title_setting_key, [
					'id' => 'glozin-tab-title-' . $id_int . $tab_count,
					'class' => [ 'glozin-accordion__title', 'fs-16', 'fw-semibold', 'color-dark', 'm-0', 'position-relative', $classes_title ],
					'data-tab' => $tab_count,
					'aria-controls' => 'glozin-tab-content-' . $id_int . $tab_count,
				] );

				$this->add_render_attribute( $tab_content_setting_key, [
					'id' => 'glozin-tab-content-' . $id_int . $tab_count,
					'class' => [ 'glozin-accordion__content', 'clearfix', 'pt-5', 'pb-20', 'd-none' ],
					'data-tab' => $tab_count,
					'aria-labelledby' => 'glozin-tab-title-' . $id_int . $tab_count,
				] );

				$this->add_inline_editing_attributes( $tab_content_setting_key, 'advanced' );
				?>
				<div class="glozin-accordion__item">
					<<?php echo $settings['title_html_tag']; ?> <?php echo $this->get_render_attribute_string( $tab_title_setting_key ); ?>>
						<span class="glozin-svg-icon glozin-accordion__icon fs-12 position-absolute top-50 end-0 translate-middle-y rounded-100 d-inline-flex glozin-accordion__icon-default glozin-accordion__<?php echo $settings['style_icons']['value'] ? 'svg-icon' : 'plus'; ?>">
							<?php
								if ( ! empty( $settings['style_icons']['value'] ) ) {
									Icons_Manager::render_icon( $settings['style_icons'], [ 'aria-hidden' => 'true' ] );
								} else {
									echo '<span class="glozin-accordion__icon-plus d-inline-flex align-items-center justify-content-center position-relative me-7"></span>';
								}
							?>
						</span>
						<?php if ( ! empty( $settings['style_icons']['value'] ) || ! empty( $settings['style_icons_active']['value'] ) ) : ?>
							<span class="glozin-svg-icon glozin-accordion__icon fs-12 position-absolute top-50 end-0 translate-middle-y rounded-100 d-inline-flex glozin-accordion__icon-active glozin-accordion__svg-icon">
								<?php
									$icon_active = ! empty( $settings['style_icons_active']['value'] ) ? $settings['style_icons_active'] : $settings['style_icons'];
									Icons_Manager::render_icon( $icon_active, [ 'aria-hidden' => 'true' ] );
								?>
							</span>
						<?php endif; ?>
						<a class="glozin-accordion__title-text lh-normal" href="#glozin-tab-content-<?php echo $id_int . $tab_count; ?>"><?php echo $item['tab_title']; ?></a>
					</<?php echo $settings['title_html_tag']; ?>>
					<div <?php echo $this->get_render_attribute_string( $tab_content_setting_key ); ?>><?php echo $this->parse_text_editor( $item['tab_content'] ); ?></div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
