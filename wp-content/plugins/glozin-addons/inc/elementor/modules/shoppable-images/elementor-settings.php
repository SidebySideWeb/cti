<?php
namespace Glozin\Addons\Elementor\Modules\Shoppable_Images;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Core\Base\Module;
use Elementor\Controls_Manager;
use Elementor\Core\DocumentTypes\PageBase as PageBase;

class Elementor_Settings extends Module {
	/**
	 * Get module name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'display-settings';
	}

	/**
	 * Module constructor.
	 */
	public function __construct() {
		add_action( 'elementor/documents/register_controls', [ $this, 'register_display_controls' ] );
	}

	/**
	 * Register display controls.
	 *
	 * @param object $document
	 */
	public function register_display_controls( $document ) {
		if ( ! $document instanceof PageBase ) {
			return;
		}

		$post_type = get_post_type( $document->get_main_id() );

		if ( 'shoppable_images' != $post_type ) {
			return;
		}

		add_action('elementor/element/after_section_end', [ $this, 'update_controls' ]);

		$this->register_modal_style( $document );

	}

	/**
	 * Register template controls of display.
	 *
	 * @param object $document
	 */
	protected function register_modal_style( $document ) {
		$document->start_controls_section(
			'modal_style',
			[
				'label' => esc_html__( 'Popup Style', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$document->add_control(
			'modal_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'.shoppable-images-modal[data-shoppable_images_id="' . get_the_ID() . '"] .modal__container' => 'background-color: {{VALUE}};',
					'body.single-shoppable_images.postid-' . get_the_ID() .' .modal__container' => 'background-color: {{VALUE}};',
				],
			]
		);

		$document->add_control(
			'modal_width',
			[
				'label' => esc_html__( 'Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1170,
					],
					'vw' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', 'vw' ],
				'default' => [
					'size' => 1020,
					'unit' => 'px'
				],
				'selectors' => [
					'.shoppable-images-modal[data-shoppable_images_id="' . get_the_ID() . '"]' => '--gz-modal-content-width: {{SIZE}}{{UNIT}};',
					'body.single-shoppable_images.postid-' . get_the_ID() .' .modal__container' => 'max-width: {{SIZE}}{{UNIT}}; margin: 0 auto;',
				],
			]
		);

		$document->add_control(
			'modal_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top'    => '15',
					'right'  => '15',
					'bottom' => '15',
					'left'   => '15',
					'unit'   => 'px',
				],
				'selectors'  => [
					'.shoppable-images-modal[data-shoppable_images_id="' . get_the_ID() . '"]' => '--gz-modal-content-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'body.single-shoppable_images.postid-' . get_the_ID() .' .modal__container' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$document->add_control(
			'modal_close_heading',
			[
				'label' => __( 'Close', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$document->add_control(
			'modal_close_size',
			[
				'label' => esc_html__( 'Size', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'size_units' => [ 'px' ],
				'default' => [],
				'selectors' => [
					'.shoppable-images-modal[data-shoppable_images_id="' . get_the_ID() . '"]' . ' .modal__button-close' => 'font-size: {{SIZE}}{{UNIT}};',
					'body.single-shoppable_images.postid-' . get_the_ID() .' .modal__button-close' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$document->add_control(
			'modal_close_position_top',
			[
				'label' => esc_html__( 'Position Top', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 1000,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%' ],
				'default' => [],
				'selectors' => [
					'.shoppable-images-modal[data-shoppable_images_id="' . get_the_ID() . '"]' . ' .modal__button-close' => 'top: {{SIZE}}{{UNIT}};',
					'body.single-shoppable_images.postid-' . get_the_ID() .' .modal__button-close' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$document->add_control(
			'modal_close_position_right',
			[
				'label' => esc_html__( 'Position Right', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 1000,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%' ],
				'default' => [],
				'selectors' => [
					'.shoppable-images-modal[data-shoppable_images_id="' . get_the_ID() . '"] .modal__button-close' => 'right: {{SIZE}}{{UNIT}};',
					'body.single-shoppable_images.postid-' . get_the_ID() .' .modal__button-close' => 'right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$document->add_control(
			'modal_close_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'.shoppable-images-modal[data-shoppable_images_id="' . get_the_ID() . '"] .modal__button-close' => 'color: {{VALUE}};',
					'body.single-shoppable_images.postid-' . get_the_ID() .' .modal__button-close' => 'color: {{VALUE}};',
				],
			]
		);

		$document->add_control(
			'modal_close_color_hover',
			[
				'label' => esc_html__( 'Color Hover', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'.shoppable-images-modal[data-shoppable_images_id="' . get_the_ID() . '"] .modal__button-close:hover' => 'color: {{VALUE}};',
					'body.single-shoppable_images.postid-' . get_the_ID() .' .modal__button-close:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$document->end_controls_section();
	}

	/**
	 * @param $element    Controls_Stack
	 */
	public function update_controls( $document ) {
		$document->remove_control( 'hide_title' );
		$document->remove_control( 'section_page_style' );
	}
}