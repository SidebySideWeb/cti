<?php

namespace Glozin\Addons\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;

/**
 * Elementor button widget.
 *
 * Elementor widget that displays a button with the ability to control every
 * aspect of the button design.
 *
 * @since 1.0.0
 */
class Code_Discount extends Widget_Base {
	use \Glozin\Addons\Elementor\Base\Button_Base;
	/**
	 * Get widget name.
	 *
	 * Retrieve button widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-code-discount';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve button widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Glozin] Code Discount', 'glozin-addons' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve button widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-copy';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the button widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'glozin-addons-popup' ];
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

	public function get_style_depends() {
		return [ 'glozin-elementor-css' ];
	}

	/**
	 * Register button widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'glozin-addons' ),
			]
		);

        $this->add_control(
			'code',
			[
				'label' => __( 'Code', 'glozin-addons' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your code', 'glozin-addons' ),
				'default' => __( 'CODE6789', 'glozin-addons' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Content', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->register_button_style_controls();

		$this->end_controls_section();
	}

	/**
	 * Render button widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
        $settings = $this->get_settings_for_display();

		if ( empty( $settings['code'] ) ) {
			return;
		}

		$this->add_render_attribute( 'wrapper', 'class', [ 'glozin-code-discount', 'glozin-button' ] );

		$classes = 'gz-button';
		$classes .= ! empty( $settings['button_classes'] ) ? $settings['button_classes'] : '';
		$classes .= ! empty( $settings['button_style'] ) ? ' gz-button-'  . $settings['button_style'] : '';
		$classes .= in_array( $settings['button_style'], ['', 'light', 'outline-dark' , 'outline'] ) ? ' ' : '';

		$this->add_render_attribute( 'wrapper', 'class', $classes );
        ?>
		<button <?php echo $this->get_render_attribute_string( 'wrapper' );?>>
			<?php echo \Glozin\Addons\Helper::get_svg( 'copy', 'ui', 'class=position-relative top-2' ); ?>
			<span class="glozin-button-text position-relative">
				<span class="glozin-button-text-code"><?php echo esc_html( $settings['code'] );?></span>
				<span class="glozin-button-text-copied position-absolute top-50 start-50 translate-middle invisible"><?php echo esc_html__( 'Copied', 'glozin-addons' );?></span>
			</span>
		</button>
        <?php
	}
}
