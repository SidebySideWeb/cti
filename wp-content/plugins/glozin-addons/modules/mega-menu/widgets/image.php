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
class Image extends Widget_Base {

	/**
	 * Set the widget name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'image';
	}

	/**
	 * Set the widget label
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Image Box', 'glozin-addons' );
	}

	/**
	 * Default widget options
	 *
	 * @return array
	 */
	public function get_defaults() {
		return array(
			'image'  => array( 'id' => '', 'url' => '' ),
			'link'   => array( 'url' => '', 'target' => '' ),
			'button' => '',
			'style'  => 'dark',
		);
	}

	/**
	 * Render widget content
	 */
	public function render() {
		$data = $this->get_data();
		$ratio = '';
		if ( ! empty( $data['ratio'] ) ) {
			$ratio = $this->render_aspect_ratio_style();
		}

		echo '<div class="menu-widget-image gz-hover-zoom position-relative" '. $ratio .'>';
		if ( empty( $data['link']['url'] ) ) {
			echo '<span class="gz-ratio gz-image-rounded-md gz-hover-effect overflow-hidden">';
		} else {
			echo '<a href="'. $data['link']['url'] .'" class="gz-ratio gz-image-rounded-md gz-hover-effect overflow-hidden">';
		}
		$this->render_image( $data['image'], 'full', array(
			'alt' => esc_html__( 'Mega Menu Image', 'glozin-addons' ) . $data['image']['id'],
			'class' => 'menu-widget-image__image'
		) );
		if ( empty( $data['link']['url'] ) ) {
			echo '</span>';
		} else {
			echo '</a>';
		}
		echo '</div>';

		echo '<div class="menu-widget-image__content d-flex flex-column justify-content-end position-absolute start-0 end-0 bottom-0 pb-25">';

		$class_btn = 'menu-widget-image__button gz-button gz-button-hover-effect fw-semibold ms-auto me-auto z-3';
		if ( ! empty( $data['style'] ) && $data['style'] == 'light' ) {
			$class_btn .= ' gz-button-light';
		}
		$data['link']['class'] = $class_btn;
		$data['link']['target'] = $data['link']['target'] ? $data['link']['target'] : '';
		$this->render_link_open( $data['link'] );

		if ( empty( $data['link']['url'] ) ) {
			echo '<span class="'. $class_btn .'">' . wp_kses_post( $data['button'] ) . '</span>';
		} else {
			echo wp_kses_post( $data['button'] );
		}

		$this->render_link_close( $data['link'] );

		echo '</div>';

	}

	/**
	 * Render aspect ratio style
	 *
	 * @return void
	 */
    protected function render_aspect_ratio_style() {
		$data = $this->get_data();
		$aspect_ratio = 1;

        if( $data['ratio'] == 'vertical' ) {
            $aspect_ratio = 0.79;
        }

        if( $data['ratio'] == 'horizontal' ) {
            $aspect_ratio = 1.3678977272727273;
        }

        if( $data['ratio'] == 'custom' && ! empty( $data['aspect_ratio'] ) ) {
            if( ! is_numeric( $data['aspect_ratio'] ) ) {
                $cropping_split = explode( ':', $data['aspect_ratio'] );
                $width          = max( 1, (float) current( $cropping_split ) );
                $height         = max( 1, (float) end( $cropping_split ) );
                $aspect_ratio   = floatval( $width / $height );
            } else {
                $aspect_ratio = $data['aspect_ratio'];
            }
        }

        return 'style="--gz-ratio-percent: '. round( 100 / $aspect_ratio ) . '%;"';
    }

	/**
	 * Widget setting fields.
	 */
	public function add_controls() {
		$this->add_control( array(
			'type' => 'image',
			'label' => __( 'Image', 'glozin-addons' ),
			'name' => 'image',
		) );

		$this->add_control( array(
			'type' => 'select',
			'name' => 'ratio',
			'label' => __( 'Image Ratio', 'glozin-addons' ),
			'options' => array(
				'square'     => __( 'Square', 'glozin-addons' ),
				'vertical'   => __( 'Vertical rectangle', 'glozin-addons' ),
				'horizontal' => __( 'Horizontal rectangle', 'glozin-addons' ),
				'custom'     => __( 'Custom', 'glozin-addons' ),
			),
		) );

		$this->add_control( array(
			'type' => 'text',
			'label'       => __( 'Aspect ratio (Eg: 3:4)', 'glozin-addons' ),
			'description' => __( 'When you choose the "Custom" ratio, the image will be cropped to fit the specified aspect ratio.', 'glozin-addons' ),
			'name' => 'aspect_ratio',
		) );

		$this->add_control( array(
			'type' => 'link',
			'name' => 'link',
		) );

		$this->add_control( array(
			'type' => 'text',
			'name' => 'button',
			'label' => __( 'Button Text', 'glozin-addons' ),
		) );

		$this->add_control( array(
			'type' => 'select',
			'name' => 'style',
			'label' => __( 'Button Style', 'glozin-addons' ),
			'options' => array(
				'dark'     => __( 'Dark', 'glozin-addons' ),
				'light'   => __( 'Light', 'glozin-addons' ),
			),
		) );
	}
}