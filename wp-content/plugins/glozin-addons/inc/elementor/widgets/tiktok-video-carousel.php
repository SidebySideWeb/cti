<?php
namespace Glozin\Addons\Elementor\Widgets;

use Glozin\Addons\Elementor\Base\Carousel_Widget_Base;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Pricing Table widget
 */
class Tiktok_Video_Carousel extends Carousel_Widget_Base {
	use \Glozin\Addons\Elementor\Base\Aspect_Ratio_Base;

	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-tiktok-video-carousel';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Glozin] Tiktok Video Carousel', 'glozin-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-video-camera';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return ['glozin-addons'];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'tiktok', 'video', 'carousel', 'glozin' ];
	}

	public function get_script_depends() {
		return [
			'glozin-elementor-widgets'
		];
	}

	/**
	 * Register the widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
	   	$this->start_controls_section(
			'section_instagram',
			[ 'label' => __( 'Tiktok Videos', 'glozin-addons' ) ]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'video_id',
			[
				'label' => __( 'Video ID', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'videos',
			[
				'label' => __( 'Videos', 'glozin-addons' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [],
			]
		);

		$this->register_aspect_ratio_controls( [], [ 'aspect_ratio_type' => 'vertical' ] );

		$this->end_controls_section();

		// Carousel Settings
		$this->start_controls_section(
			'section_products_carousel',
			[
				'label' => __( 'Carousel Settings', 'glozin-addons' ),
			]
		);

		$controls = [
			'slides_to_show'    				=> 4,
			'slides_to_scroll'     				=> 1,
			'space_between'  					=> 35,
			'navigation'    					=> 'none',
			'navigation_classes'    			=> 'none',
			'autoplay' 							=> '',
			'autoplay_speed'      				=> 3000,
			'pause_on_hover'    				=> 'yes',
			'animation_speed'  					=> 800,
			'infinite'  						=> '',
		];

		$this->register_carousel_controls($controls);

		$this->end_controls_section();

		// Content style
		$this->start_controls_section(
			'section_style_content',
			[
				'label' => __( 'Content', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);



		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label'     => __( 'Carousel Options', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->register_carousel_style_controls();

		$this->end_controls_section();
	}


	/**
	 * Render widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$col = $settings['slides_to_show'];
		$col_tablet = ! empty( $settings['slides_to_show_tablet'] ) ? $settings['slides_to_show_tablet'] : $col;
		$col_mobile = ! empty( $settings['slides_to_show_mobile'] ) ? $settings['slides_to_show_mobile'] : $col;

		$this->add_render_attribute( 'video', 'class', [ 'glozin-tiktok-video-carousel', 'glozin-carousel--elementor', 'swiper' ] );
		$this->add_render_attribute( 'video', 'data-desktop', $col );
		$this->add_render_attribute( 'video', 'data-tablet', $col_tablet );
		$this->add_render_attribute( 'video', 'data-mobile', $col_mobile );

		$this->add_render_attribute( 'ratio', 'style', $this->render_aspect_ratio_style( '', 1, false, true ) );
		$this->add_render_attribute( 'ratio', 'class', ['gz-ratio--iframe'] );

		$this->add_render_attribute( 'wrapper', 'class', [ 'glozin-tiktok-video-carousel__wrapper', 'swiper-wrapper' ] );

		if ( empty( $settings['videos'] ) ) {
			return;
		}

		echo '<div ' . $this->get_render_attribute_string( 'video' ) . '>';
		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>';

		foreach ($settings['videos'] as $index => $video) {
			$wrapper_key 		= $this->get_repeater_setting_key( 'wrapper', 'banner', $index );
			$this->add_render_attribute( $wrapper_key, 'class', ['glozin-tiktok-video-carousel__item', 'swiper-slide'] );

			echo '<div ' . $this->get_render_attribute_string( $wrapper_key ) . '>';
				echo '<div ' . $this->get_render_attribute_string( 'ratio' ) . '>';
					echo '<blockquote class="glozin-tiktok-lazy-wrapper" data-src="https://www.tiktok.com/embed/v2/' . esc_attr( $video['video_id'] ) . '">';
						echo '<div class="tiktok-placeholder" style="width:100%;height:739px;background:#000;"></div>';
					echo '</blockquote>';
				echo '</div>';
			echo '</div>';
		}

		echo '</div>';
		echo '<div class="swiper-arrows">'. $this->render_arrows() .'</div>';
	   	echo $this->render_pagination();
		echo '</div>';
	}

}