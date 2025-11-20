<?php
/**
 * Widget Image
 */

namespace Glozin\Addons\Modules\Mega_Menu\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Banner widget class
 */
class Banner extends Widget_Base {

	/**
	 * Set the widget name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'banner';
	}

	/**
	 * Set the widget label
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Banner', 'glozin-addons' );
	}

	/**
	 * Default widget options
	 *
	 * @return array
	 */
	public function get_defaults() {
		return array(
			'image'  			=> array( 'id' => '', 'url' => '' ),
			'link'   			=> array( 'url' => '', 'target' => '' ),
			'before_title'  	=> '',
			'title'  			=> '',
			'description'   	=> '',
			'sub_description' 	=> array( 'id' => '', 'url' => '' ),
			'button_text' 		=> '',
			'ratio'  			=> 'vertical',
			'aspect_ratio' 		=> '',
			'style' 			=> 'dark',
			'position' 			=> 'bottom',
		);
	}

	/**
	 * Render widget content
	 */
	public function render() {
		$data = $this->get_data();


		$classes = $data['classes'] ? ' ' . $data['classes'] : '';
		$ratio = '';

		if ( ! empty( $data['ratio'] ) ) {
			$ratio = $this->render_aspect_ratio_style();
		}

		$banner_classes = ' menu-widget-banner__style-'. $data['style'];
		if ( $data['position'] == 'center' ) {
			$banner_classes .= ' align-items-center';
		} else {
			$banner_classes .= ' align-items-end';
		}

		echo '<div class="menu-widget-banner'. esc_attr( $classes ) .'" '. $ratio .'>';

			echo '<div class="menu-widget-banner__image gz-ratio gz-image-rounded-md">';
			if ( ! empty( $data['image']['url'] ) ) {
				$this->render_image( $data['image'], 'full', array( 'alt' => esc_attr__( 'Mega Menu Banner', 'glozin-addons' ) . $data['image']['id'] ) );
			} else {
				echo '<img src="'. wc_placeholder_img_src() .'" title="'. esc_attr__( 'Mega Menu Banner', 'glozin-addons' ) .'" alt="'. esc_attr__( 'Mega Menu Banner', 'glozin-addons' ) .'"/>';
			}
			echo '</div>';

		echo '<div class="menu-widget-banner__content d-flex justify-content-center position-absolute top-0 start-0 end-0 bottom-0 w-100 h-100 py-20 px-20 py-md-50 px-md-50'. esc_attr( $banner_classes ) .'">';
		echo '<div class="menu-widget-banner__inner text-center d-flex flex-column z-3">';

		if ( ! empty( $data['before_title'] ) ) {
			echo '<div class="menu-widget-banner__before-title fw-semibold">'. esc_html( $data['before_title'] ) .'</div>';
		}

		if ( ! empty( $data['title'] ) ) {
			echo '<div class="menu-widget-banner__title fs-32 fw-semibold">'. esc_html( $data['title'] ) .'</div>';
		}

		if ( ! empty( $data['main_description'] ) ) {
			echo '<div class="menu-widget-banner__description mb-25">'. wp_kses_post( $data['main_description'] ) .'</div>';
		}

		if ( ! empty( $data['sub_description']['url'] ) ) {
			$this->render_image( $data['sub_description'], 'full', array(
				'alt' => esc_attr__( 'Sub Description Image', 'glozin-addons' ) . $data['sub_description']['id'],
				'class' => 'menu-widget-banner__sub-description ms-auto me-auto mb-25'
			) );
		}

		if ( ! empty( $data['button_text'] ) ) {
			$class_btn = 'menu-widget-banner__button gz-button gz-button-hover-effect fw-semibold ms-auto me-auto';
			if ( ! empty( $data['style'] ) && $data['style'] == 'light' ) {
				$class_btn .= ' gz-button-light';
			}

			$data['link']['class'] = $class_btn;

			$this->render_link_open( $data['link'] );

			if ( empty( $data['link']['url'] ) ) {
				echo '<span class="'. $class_btn .'">' . wp_kses_post( $data['button_text'] ) . '</span>';
			} else {
				echo wp_kses_post( $data['button_text'] );
			}

			$this->render_link_close( $data['link'] );
		}



		echo '</div>';

		if ( $data['link']['url'] ) {
			echo '<a href="'. $data['link']['url'] .'" aria-label="'. esc_attr__( 'Open menu banner link', 'glozin-addons' ) .'" class="menu-widget-banner__link position-absolute top-0 left-0 w-100 h-100 z-2"><span class="screen-reader-text">'. wp_kses_post( $data['button_text'] ) .'</span></a>';
		}

		echo '</div>';
		echo '</div>';
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
			'type' => 'text',
			'label' => __( 'Before Title', 'glozin-addons' ),
			'name' => 'before_title',
		) );

		$this->add_control( array(
			'type' => 'text',
			'label' => __( 'Title', 'glozin-addons' ),
			'name' => 'title',
		) );

		$this->add_control( array(
			'type' => 'textarea',
			'label' => __( 'Description', 'glozin-addons' ),
			'name' => 'main_description',
		) );

		$this->add_control( array(
			'type' => 'image',
			'label' => __( 'Sub Description Image', 'glozin-addons' ),
			'name' => 'sub_description',
		) );

		$this->add_control( array(
			'type' => 'text',
			'label' => __( 'Button Text', 'glozin-addons' ),
			'name' => 'button_text',
		) );

		$this->add_control( array(
			'type' => 'link',
			'name' => 'link',
		) );

		$this->add_control( array(
			'type' => 'select',
			'name' => 'style',
			'label' => __( 'Banner Style', 'glozin-addons' ),
			'options' => array(
				'dark'     => __( 'Dark', 'glozin-addons' ),
				'light'   => __( 'Light', 'glozin-addons' ),
			),
		) );

		$this->add_control( array(
			'type' => 'select',
			'name' => 'position',
			'label' => __( 'Position', 'glozin-addons' ),
			'options' => array(
				'bottom'   => __( 'Bottom', 'glozin-addons' ),
				'center'   => __( 'Center', 'glozin-addons' ),
			),
		) );
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
}