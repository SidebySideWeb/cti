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
class HTML extends Widget_Base {

	/**
	 * Set the widget name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'html';
	}

	/**
	 * Set the widget label
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Custom HTML', 'glozin-addons' );
	}

	/**
	 * Default widget options
	 *
	 * @return array
	 */
	public function get_defaults() {
		return array(
			'text' => '',
		);
	}

	/**
	 * Render widget content
	 */
	public function render() {
		echo do_shortcode( $this->get_data( 'text' ) );
	}

	/**
	 * Widget setting fields.
	 */
	public function add_controls() {
		$this->add_control( array(
			'type' => 'textarea',
			'name' => 'text',
			'label' => __( 'Content', 'glozin-addons' ),
		) );
	}
}