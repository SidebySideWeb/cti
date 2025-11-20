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
class Heading extends Widget_Base {

	/**
	 * Set the widget name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'heading';
	}

	/**
	 * Set the widget label
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Heading', 'glozin-addons' );
	}

	/**
	 * Default widget options
	 *
	 * @return array
	 */
	public function get_defaults() {
		return array(
			'title'  	=> '',
			'link'   	=> array( 'url' => '', 'target' => '' ),
			'type' 		=> ''
		);
	}

	/**
	 * Render widget content
	 */
	public function render() {
		$data = $this->get_data();

		$classes = $data['classes'] ? ' ' . $data['classes'] : '';
		$classes = 'menu-item--widget-heading-title text-dark fs-16 fw-semibold pb-10 lh-1 d-inline-block' . $classes;

		$data['link']['class'] = $classes;
		$data['link']['target'] = $data['link']['target'] ? $data['link']['target'] : '';

		$this->render_link_open( $data['link'] );

		if ( empty( $data['link']['url'] ) ) {
			echo '<span class="'. $classes .'">' . wp_kses_post( $data['title'] ) . '</span>';
		} else {
			echo wp_kses_post( $data['title'] );
		}

		$this->render_link_close( $data['link'] );
	}

	/**
	 * Widget setting fields.
	 */
	public function add_controls() {
		$this->add_control( array(
			'type' => 'text',
			'name' => 'title',
			'label' => esc_html__( 'Navigation Label', 'glozin-addons' ),
		) );

		$this->add_control( array(
			'type' => 'link',
			'name' => 'link',
		) );

		$this->add_control( array(
			'type' => 'select',
			'name' => 'type',
			'options' => array(
				'0' 		=> esc_html__( 'Default', 'glozin-addons' ),
				'hidden' => esc_html__( 'Hidden', 'glozin-addons' ),
				'empty' => esc_html__( 'Empty (keep spacing)', 'glozin-addons' ),
				'divider' => esc_html__( 'Divider', 'glozin-addons' )
			),
		) );
	}
}