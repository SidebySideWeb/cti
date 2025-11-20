<?php
namespace Glozin\Addons\Elementor\Controls;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Core\Base\Module;
use Elementor\Core\Breakpoints\Manager as Breakpoints_Manager;
use Elementor\Controls_Manager;

class Settings_Layout extends Module {

	/**
	 * Get module name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'settings-layout';
	}


	/**
	 * Module constructor.
	 */
	public function __construct() {
		add_action( 'elementor/element/kit/section_settings-layout/before_section_end', [ $this, 'update_settings_controls' ], 10, 2 );
		add_action( 'elementor/element/kit/section_settings-layout/after_section_start', [ $this, 'add_settings_controls' ], 10, 2 );
	}

	/**
	 * @param $element    Controls_Stack
	 */
	public function update_settings_controls( $element ) {
		$breakpoints_default_config = Breakpoints_Manager::get_default_config();
		$breakpoint_key_mobile = Breakpoints_Manager::BREAKPOINT_KEY_MOBILE;
		$breakpoint_key_tablet = Breakpoints_Manager::BREAKPOINT_KEY_TABLET;
		$element->update_responsive_control(
			'container_width',
			[
				'label' => esc_html__( 'Content Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'default' => [
					'size' => 1410,
				],
				'tablet_default' => [
					'size' => $breakpoints_default_config[ $breakpoint_key_tablet ]['default_value'],
				],
				'mobile_default' => [
					'size' => $breakpoints_default_config[ $breakpoint_key_mobile ]['default_value'],
				],
				'range' => [
					'px' => [
						'min' => 300,
						'max' => 1500,
						'step' => 10,
					],
				],
				'description' => esc_html__( 'Sets the default width of the content area (Default: 1200px)', 'glozin-addons' ),
				'selectors' => [
					'.elementor-section.elementor-section-boxed > .elementor-container' => 'max-width: {{SIZE}}{{UNIT}}',
					'.e-con' => '--container-max-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

	}

	/**
	 * @param $element    Controls_Stack
	 */
	public function add_settings_controls( $element ) {
		$element->add_responsive_control(
			'glozin_container_spacing',
			[
				'label' => esc_html__( 'Content Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
				],
				'description' => esc_html__( 'Sets the default spacing left and right of the content area. Default is 50px', 'glozin-addons' ),
				'selectors' => [
					'.e-con-inner' => '--gz-container-spacing: {{SIZE}}{{UNIT}}',
				],
			]
		);

	}
}
