<?php
namespace Glozin\Addons\Elementor\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;

trait Aspect_Ratio_Base {
	/**
	 * Register controls for products query
	 *
	 * @param array $controls
	 */
	protected function register_aspect_ratio_controls( $conditions = [], $default = [] ) {
		$this->add_aspect_ratio_controls( 'aspect_ratio', $conditions, $default );
	}

	protected function add_aspect_ratio_controls( $id_prefix, $conditions = [], $default = [] ) {
		$default = wp_parse_args( $default, [ 'aspect_ratio_type' => 'vertical' ] );

		$this->add_responsive_control(
			"{$id_prefix}_type",
			[
				'label'     => esc_html__( 'Aspect Ratio', 'glozin-addons' ) . ( $id_prefix === 'aspect_ratio_mobile' ? ' Mobile' : '' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					''           => esc_html__( 'Default', 'glozin-addons' ),
					'square'     => esc_html__( 'Square', 'glozin-addons' ),
					'vertical'   => esc_html__( 'Vertical rectangle', 'glozin-addons' ),
					'horizontal' => esc_html__( 'Horizontal rectangle', 'glozin-addons' ),
					'custom'     => esc_html__( 'Custom', 'glozin-addons' ),
				],
				'default'   => $default['aspect_ratio_type'],
				'condition' => $conditions,
			]
		);

		$custom_conditions = wp_parse_args( $conditions, [ "{$id_prefix}_type" => 'custom' ] );

		$this->add_responsive_control(
			$id_prefix,
			[
				'label'       => esc_html__( 'Aspect ratio (Eg: 3:4)', 'glozin-addons' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__(
					'Images will be cropped to aspect ratio',
					'glozin-addons'
				),
				'default'     => '',
				'label_block' => false,
				'condition'   => $custom_conditions,
			]
		);
	}

	/**
	 * Render aspect ratio style
	 *
	 * @return void
	 */
    protected function render_aspect_ratio_style( $style = '', $aspect_ratio = 1, $mobile = false, $iframe = false ) {
        $settings = $this->get_settings_for_display();

		if ( ! empty( $settings['aspect_ratio_type'] ) ) {
			$ratio = $this->calculate_aspect_ratio(
				$settings['aspect_ratio_type'],
				$settings['aspect_ratio'] ?? ''
			);

			if ( $ratio ) {
				$aspect_ratio = $ratio;
				$style .= '--gz-ratio-percent: ' . round( 100 / $aspect_ratio ) . '%;';
			}
		}

		if ( ! empty( $settings['aspect_ratio_type_tablet'] ) ) {
			$ratio = $this->calculate_aspect_ratio(
				$settings['aspect_ratio_type_tablet'],
				$settings['aspect_ratio_tablet'] ?? ''
			);

			if ( $ratio ) {
				$aspect_ratio = $ratio;
				$style .= '--gz-ratio-percent-tablet: ' . round( 100 / $aspect_ratio ) . '%;';
			}
		}

		if ( ! empty( $settings['aspect_ratio_type_mobile'] ) ) {
			$ratio = $this->calculate_aspect_ratio(
				$settings['aspect_ratio_type_mobile'],
				$settings['aspect_ratio_mobile'] ?? ''
			);

			if ( $ratio ) {
				$aspect_ratio = $ratio;
				$style .= '--gz-ratio-percent-mobile: ' . round( 100 / $aspect_ratio ) . '%;';
			}
		}


		if( $iframe ) {
			$width = 325;

			if ( $settings['aspect_ratio_type'] == 'custom' && ! empty( $settings['aspect_ratio'] ) ) {
				if ( ! is_numeric( $settings['aspect_ratio'] ) ) {
					$cropping_split = explode( ':', $settings['aspect_ratio'] );
					$width = max( 1, (float) current( $cropping_split ) );
				}
			}

			$style .= ' --gz-ratio-iframe-min-width: ' . $width . 'px;';
			$style .= ' --gz-item-iframe-width: ' . $width . ';';
			$style .= ' --gz-item-iframe-width-origin: ' . $width . ';';
		}

		return $style;
    }

	protected function calculate_aspect_ratio( $type, $value = '' ) {
		switch ( $type ) {
			case 'vertical':
				return 0.7488888888888889;
			case 'horizontal':
				return 1.9857142857142858;
			case 'square':
				return 1;
			case 'custom':
				if ( ! empty( $value ) ) {
					if ( ! is_numeric( $value ) ) {
						$cropping_split = explode( ':', $value );
						$width = max( 1, (float) current( $cropping_split ) );
						$height = max( 1, (float) end( $cropping_split ) );
						return floatval( $width / $height );
					}
					return (float) $value;
				}
				break;
		}
		return null;
	}
}