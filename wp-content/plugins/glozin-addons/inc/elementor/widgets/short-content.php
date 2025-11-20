<?php

namespace Glozin\Addons\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Timeline widget
 */
class Short_Content extends Widget_Base {
    use \Glozin\Addons\Elementor\Base\Button_Base;
    
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-short-content';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( '[Glozin] Short Content', 'glozin-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-shortcode';
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
     * Retrieve the list of scripts the widget depended on.
     *
     * Used to set scripts dependencies required to run the widget.
     *
     * @return array Widget scripts dependencies.
     */
    public function get_script_depends() {
        return [ 'glozin-elementor-widgets' ];
    }

    /**
     * Retrieve the list of styles the widget depended on.
     *
     * Used to set styles dependencies required to run the widget.
     *
     * @return array Widget styles dependencies.
     */
	public function get_style_depends() {
		return [ 'glozin-elementor-css' ];
	}

	/**
	 * Register heading widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->content_sections();
		$this->style_sections();
	}

	protected function content_sections() {
		$this->start_controls_section(
			'section_contents',
			[
				'label' => __( 'Contents', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'content',
			[
				'label' => esc_html__( 'Content', 'glozin-addons' ),
				'type' => Controls_Manager::WYSIWYG,
				'placeholder' => esc_html__( 'Enter your content', 'glozin-addons' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

        $this->add_control(
			'show',
			[
				'label' => __( 'Show Text', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Show Text', 'glozin-addons' ),
				'label_block' => true,
			]
		);

        $this->add_control(
			'hide',
			[
				'label' => __( 'Hide Text', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Hide Text', 'glozin-addons' ),
				'label_block' => true,
			]
		);

		$this->end_controls_section();
	}

	protected function style_sections() {
		$this->start_controls_section(
			'section_style',
			[
				'label'     => __( 'Style', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
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
				'selectors'            => [
					'{{WRAPPER}} .glozin-short-content' => 'justify-content: {{VALUE}}',
				],
				'selectors_dictionary' => [
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				],
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
				'selectors'   => [
					'{{WRAPPER}} .glozin-short-content' => 'text-align: {{VALUE}}',
				],
			]
		);

        $this->add_responsive_control(
			'max_width',
			[
				'label'     => esc_html__( 'Max Width', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 2000,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-short-content__wrapper' => 'max-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

        $this->add_control(
			'content_heading',
			[
				'label' => esc_html__( 'Content', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_responsive_control(
			'max_height',
			[
				'label'     => esc_html__( 'Max Height', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 97,
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-short-content__content:not(.show)' => 'max-height: {{SIZE}}{{UNIT}}',
				],
				'frontend_available' => true,
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'content_typography',
				'selector' => '{{WRAPPER}} .glozin-short-content__content',
			]
		);

        $this->add_responsive_control(
			'content_color',
			[
				'label'      => esc_html__( 'Content Color', 'glozin-addons' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .glozin-short-content__content' => 'color: {{VALUE}}',
				],
			]
		);

        $this->end_controls_section();

        $this->start_controls_section(
			'section_button_style',
			[
				'label'     => __( 'Button Style', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_responsive_control(
			'button_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'glozin-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 150,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-short-content__button' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

        $this->register_button_style_controls( 'subtle' );

		$this->end_controls_section();
	}

	/**
	 * Render icon box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

        if( empty( $settings['content'] ) ) {
            return;
        }

        $this->add_render_attribute( 'element', 'class', [ 'glozin-short-content', 'd-flex', 'justify-content-center', 'text-center' ] );
        $this->add_render_attribute( 'wrapper', 'class', [ 'glozin-short-content__wrapper' ] );
        $this->add_render_attribute( 'content', 'class', [ 'glozin-short-content__content', 'overflow-hidden', 'position-relative' ] );
        $this->add_render_attribute( 'button', 'class', [ 'glozin-short-content__button', 'glozin-button', 'gz-button', 'gz-button-' . $settings['button_style'], 'mt-22' ] );
        $this->add_render_attribute( 'button', 'data-show', esc_attr( $settings['show'] ) );
        $this->add_render_attribute( 'button', 'data-hide', esc_attr( $settings['hide'] ) );
        
        ?>
        <div <?php echo $this->get_render_attribute_string( 'element' ); ?>>
            <div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
                <div <?php echo $this->get_render_attribute_string( 'content' ); ?>>
                    <?php echo wp_kses_post( wpautop( $settings['content'] ) ); ?>
                </div>
                <div <?php echo $this->get_render_attribute_string( 'button' ); ?>>
                    <?php echo wp_kses_post( $settings['show'] ); ?>
                </div>
            </div>
        </div>
        <?php
	}
}
