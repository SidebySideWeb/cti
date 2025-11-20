<?php
namespace Glozin\Addons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;

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
class Marquee extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve Stores Location widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-marquee';
	}

	/**
	 * Get widget title
	 *
	 * Retrieve Stores Location widget title
	 *
	 * @return string Widget title
	 */
	public function get_title() {
		return __( '[Glozin] Marquee', 'glozin-addons' );
	}

	/**
	 * Get widget icon
	 *
	 * Retrieve TeamMemberGrid widget icon
	 *
	 * @return string Widget icon
	 */
	public function get_icon() {
		return 'eicon-animation';
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
	 * Scripts
	 *
	 * @return void
	 */
	public function get_script_depends() {
		return [
			'imagesLoaded',
			'glozin-marquee-widget'
		];
	}

	/**
	 * Get style depends
	 *
	 * @return array
	 */
	public function get_style_depends() {
		return [
			'glozin-marquee-css'
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
		$this->section_style();
	}

	protected function section_content() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'glozin-addons' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'type',
			[
				'label'   => esc_html__( 'Type', 'glozin-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'image',
				'options' => [
					'icon'  => esc_html__( 'Icon', 'glozin-addons' ),
					'image' => esc_html__( 'Image', 'glozin-addons' ),
				],
			]
		);

        $repeater->add_control(
			'icon',
			[
				'label' => __( 'Icon', 'glozin-addons' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'fa fa-star',
					'library' => 'fa-solid',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'type',
							'value' => 'icon',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'image',
			[
				'label'    => __( 'Image', 'glozin-addons' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => wc_placeholder_img_src(),
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'type',
							'value' => 'image',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'text', [
				'label' => esc_html__( 'Text', 'glozin-addons' ),
				'type' => Controls_Manager::TEXTAREA,
			]
		);

		$this->add_control(
			'items',
			[
				'label' => esc_html__( 'Items', 'glozin-addons' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
                        'text' => esc_html__( 'Express Your True Self', 'glozin-addons' ),
					],
					[
                        'text' => esc_html__( 'Exclusive Seasonal Picks', 'glozin-addons' ),
					],
					[
                        'text' => esc_html__( 'Exclusive Seasonal Picks', 'glozin-addons' ),
					],
				],
			]
		);

		$this->add_control(
			'speed',
			[
				'label'   => __( 'Speed', 'glozin-addons' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => '0.3',
				'min'     => 0.1,
				'max'	  => 0.9,
				'step'    => 0.1,
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();
	}

	protected function section_style() {
		// Style
		$this->start_controls_section(
			'section_style',
			[
				'label'     => __( 'Content', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'hover_pause',
			[
				'label'     => esc_html__( 'Hover stop', 'glozin-addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__( 'Yes', 'glozin-addons' ),
				'label_off' => esc_html__( 'No', 'glozin-addons' ),
				'default'   => 'yes',
			]
		);

		$this->add_responsive_control(
			'padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-marquee' => 'padding-top: {{TOP}}{{UNIT}}; padding-inline-end: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'gap',
			[
				'label'      => esc_html__( 'Gap', 'glozin-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-marquee__items' => 'gap: {{SIZE}}{{UNIT}}; margin-inline-end: {{SIZE}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-marquee__items' => 'gap: {{SIZE}}{{UNIT}}; margin-inline-start: {{SIZE}}{{UNIT}}; margin-inline-end: 0;',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label'      => esc_html__( 'Background Color', 'glozin-addons' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .glozin-marquee' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'selector' => '{{WRAPPER}} .glozin-marquee',
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

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				'default'   => 'full',
			]
		);

		$this->add_responsive_control(
			'image_width',
			[
				'label'      => esc_html__( 'Width', 'glozin-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-marquee__image' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-marquee__image' => '--gz-image-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-marquee__image' => '--gz-image-rounded: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icon_heading',
			[
				'label' => esc_html__( 'Icon', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'      => esc_html__( 'Size', 'glozin-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-marquee__icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'      => esc_html__( 'Color', 'glozin-addons' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .glozin-marquee__icon' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'spacing',
			[
				'label'      => esc_html__( 'Spacing', 'glozin-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-marquee__item' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'text_heading',
			[
				'label' => esc_html__( 'Text', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .glozin-marquee__text',
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'      => esc_html__( 'Color', 'glozin-addons' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .glozin-marquee__text' => 'color: {{VALUE}}',
				],
			]
		);

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

		$classes = [
			'glozin-marquee',
			'glozin-elementor--marquee',
			'py-25',
			'border-top',
			'border-bottom',
			$settings['hover_pause'] == 'yes' ? 'hover-stop' : '',
		];

        $this->add_render_attribute( 'wrapper', 'class', $classes );
        $this->add_render_attribute( 'inner', 'class', [ 'glozin-marquee__inner', 'glozin-marquee--inner', 'position-relative', 'align-items-center' ] );
        $this->add_render_attribute( 'items', 'class', [ 'glozin-marquee__items', 'glozin-marquee--items', 'glozin-marquee--original', 'align-items-center' ] );
        $this->add_render_attribute( 'image', 'class', [ 'glozin-marquee__image' ] );
        $this->add_render_attribute( 'icon', 'class', [ 'glozin-marquee__icon', 'glozin-svg-icon' ] );
        $this->add_render_attribute( 'text', 'class', [ 'glozin-marquee__text' ] );
    ?>
        <div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
            <div <?php echo $this->get_render_attribute_string( 'inner' ); ?>>
				<div <?php echo $this->get_render_attribute_string( 'items' ); ?>>
					<?php foreach( $settings['items'] as $index => $item ) : ?>
						<?php
							$item_key = $this->get_repeater_setting_key( 'item', 'elementor-marquee', $index );
							$this->add_render_attribute( $item_key, 'class', [ 'glozin-marquee__item', 'd-flex', 'align-items-center', 'elementor-repeater-item-' . esc_attr( $item['_id'] ) ] );
						?>
						<div <?php echo $this->get_render_attribute_string( $item_key ); ?>>
							<?php if( $item['type'] == 'image' ) : ?>
								<?php if( ! empty( $item['image']['id'] ) ) : ?>
									<div <?php echo $this->get_render_attribute_string( 'image' ); ?>>
										<?php echo wp_get_attachment_image( $item['image']['id'], 'thumbnail' ); ?>
									</div>
								<?php endif; ?>
							<?php else : ?>
								<?php if( ! empty( $item['icon'] ) && ! empty( $item['icon']['value'] ) ) : ?>
									<span <?php echo $this->get_render_attribute_string( 'icon' ); ?>>
										<?php Icons_Manager::render_icon( $item['icon'], [ 'aria-hidden' => 'true' ] ); ?>
									</span>
								<?php endif; ?>
							<?php endif; ?>
							<div <?php echo $this->get_render_attribute_string( 'text' ); ?>>
								<?php echo wp_kses_post( $item['text'] ); ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
            </div>
        </div>
    <?php
	}
}