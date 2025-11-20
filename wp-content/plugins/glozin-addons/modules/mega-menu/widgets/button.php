<?php
/**
 * Widget Image
 */

namespace Glozin\Addons\Modules\Mega_Menu\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Image widget class
 */
class Button extends Widget_Base {

	/**
	 * Set the widget name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'button';
	}

	/**
	 * Set the widget label
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Button', 'glozin-addons' );
	}

	/**
	 * Default widget options
	 *
	 * @return array
	 */
	public function get_defaults() {
		return array(
			'text'  	=> '',
			'link'   	=> array( 'url' => '', 'target' => '' ),
			'icon' 		=> array( 'value' => '' ),
			'alignment' => 'left',
			'spacing' => '',
		);
	}

	/**
	 * Render widget content
	 */
	public function render() {
		$data = $this->get_data();

		$data['link']['class'] = $data['classes'] ? $data['classes'] : '';
		$data['link']['class'] .= 'gz-button button-alignment--' . $data['alignment'];
		$data['link']['target'] = $data['link']['target'] ? $data['link']['target'] : '';

		$this->render_link_open( $data['link']);

		echo '<span class="mega-menu__button-text">' . $data['text'] . '</span>';

		if ( ! empty( $data['icon'] ) ) {
			echo \Glozin\Addons\Helper::get_svg( 'arrow-right', 'ui', 'class=mega-menu__button-icon' );
		}

		$this->render_link_close( $data['link'] );
	}

	/**
	 * Widget setting fields.
	 */
	public function add_controls() {
		$this->add_control( array(
			'type' => 'text',
			'name' => 'text',
			'label' => esc_html__( 'Button', 'glozin-addons' ),
		) );

		$this->add_control( array(
			'type' => 'link',
			'name' => 'link',
		) );

		$this->add_control( array(
			'type' => 'checkbox',
			'name' => 'icon',
			'label' => esc_html__( 'Enable button icon', 'glozin-addons' ),
			'options' => [
				'value' => ''
			]
		) );

		$this->add_control( array(
			'type' => 'select',
			'name' => 'alignment',
			'label' => __( 'Alignment', 'glozin-addons' ),
			'options' => array(
				'left' 	=> esc_html__( 'Left', 'glozin-addons' ),
				'right' => esc_html__( 'Right', 'glozin-addons' ),
				'center' => esc_html__( 'Center', 'glozin-addons' ),
			),
		) );

		$this->add_control( array(
			'type' => 'number',
			'name' => 'spacing',
			'label' => esc_html__( 'Spacing', 'glozin-addons' ),
			'description' => esc_html__( 'Enter a number to add spacing between the button and other menu items. Use units (px).', 'glozin-addons' ),
		) );
	}
}