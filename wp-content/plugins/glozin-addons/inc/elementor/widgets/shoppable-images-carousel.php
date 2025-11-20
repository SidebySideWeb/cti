<?php

namespace Glozin\Addons\Elementor\Widgets;

use Glozin\Addons\Elementor\Base\Carousel_Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Image_Size;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Shoppable Images Carousel widget
 */
class Shoppable_Images_Carousel extends Carousel_Widget_Base {
    use \Glozin\Addons\Elementor\Base\Aspect_Ratio_Base;
    use \Glozin\Addons\Elementor\Base\Video_Base;

	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-shoppable-images-carousel';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( '[Glozin] Shoppable Images Carousel', 'glozin-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-carousel';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'glozin-addons' ];
	}

    /**
	 * Scripts
	 *
	 * @return void
	 */
	public function get_script_depends() {
		return [
			'glozin-elementor-widgets',
			'glozin-shoppable-images-widget'
		];
	}

	/**
	 * Styles
	 *
	 * @return void
	 */
	public function get_style_depends() {
		return [
			'glozin-elementor-css'
		];
	}

	/**
	 * Register heading widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->content_sections();
		$this->style_sections();
	}

	protected function content_sections() {
		$this->start_controls_section(
			'section_contents',
			[
				'label' => __( 'Contents', 'glozin-addons' ),
			]
		);

        $repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'source',
			[
				'label' => esc_html__( 'Media Source', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'image' 	=> esc_html__( 'Image', 'glozin-addons' ),
					'video' 	=> esc_html__( 'Video', 'glozin-addons' ),
				],
				'default' => 'image',
			]
		);

		$repeater->add_control(
			'image',
			[
				'label'    => __( 'Image', 'glozin-addons' ),
				'type' => Controls_Manager::MEDIA,
				'media_types' => [ 'image' ],
				'condition' => [
					'source' => 'image',
				],
			]
		);

		$this->register_video_repeater_controls( $repeater, [ 'source' => 'video' ] );

		$repeater->add_control(
			'action',
			[
				'label'   => esc_html__( 'Default action when clicked', 'glozin-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'shoppable' => esc_html__( 'Show Shoppable Image Content', 'glozin-addons' ),
					'link'  => esc_html__( 'Open linked URL', 'glozin-addons' ),
				],
				'label_block' => true,
				'default' => 'shoppable',
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'link',
			[
				'label'       => __( 'Link', 'glozin-addons' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'https://your-link.com', 'glozin-addons' ),
				'default'     => [
					'url' => '#',
				],
				'condition' => [
					'action' => 'link',
				],
			]
		);

        $repeater->add_control(
            'shoppable_images',
            [
                'label'       => __( 'Shoppable Images', 'glozin-addons' ),
                'type'        => 'glozin-autocomplete',
                'multiple'    => false,
                'source'      => 'shoppable_images',
                'sortable'    => true,
                'label_block' => true,
				'condition' => [
					'action' => 'shoppable',
				],
            ]
        );

		$repeater->add_control(
			'important_note',
			[
				'type' => Controls_Manager::NOTICE,
				'notice_type' => 'info',
				'content' => sprintf( '<a target="_blank" href="%s">%s</a>', admin_url( 'edit.php?post_type=shoppable_images' ), __( 'Click here to create your shoppable image content first!', 'glozin-addons' ) ),
				'condition' => [
					'action' => 'shoppable',
				],
			]
		);

        $this->add_control(
			'images',
			[
				'label'  => __( 'Images', 'glozin-addons' ),
				'type'   => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image',
				'default' => 'full'
			]
		);

        $this->register_aspect_ratio_controls( [], [ 'aspect_ratio_type' => 'square' ] );

		$this->add_control(
			'button_heading',
			[
				'label' => esc_html__( 'Button', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'button_icon',
			[
				'label'            => __( 'Icon', 'glozin-addons' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'skin'             => 'inline',
				'label_block'      => false,
			]
		);

		$this->end_controls_section();

        $this->start_controls_section(
			'section_slider_options',
			[
				'label' => esc_html__( 'Carousel Settings', 'glozin-addons' ),
				'type'  => Controls_Manager::SECTION,
			]
		);

		$controls = [
			'slides_rows'	   => 1,
			'slides_to_show'   => 6,
			'slides_to_scroll' => 1,
			'space_between'    => 10,
			'navigation'       => 'both',
			'autoplay'         => '',
			'autoplay_speed'   => 3000,
			'pause_on_hover'   => 'yes',
			'animation_speed'  => 800,
			'infinite'         => '',
		];

		$this->register_carousel_controls($controls);

		$this->end_controls_section();
	}

	protected function style_sections() {
		$this->start_controls_section(
			'section_style',
			[
				'label'     => __( 'Style', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'icon_position',
			[
				'label'                => esc_html__( 'Icon Position', 'glozin-addons' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'options'              => [
					'top'   => [
						'title' => esc_html__( 'Top', 'glozin-addons' ),
						'icon'  => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => esc_html__( 'Middle', 'glozin-addons' ),
						'icon'  => 'eicon-v-align-middle',
					],
				],
				'default' => 'top',
			]
		);

        $this->add_control(
			'image_video_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-shoppable-images-carousel__image' => '--gz-image-rounded: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

        $this->start_controls_section(
			'section_style_carousel',
			[
				'label' => esc_html__( 'Carousel Style', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->register_carousel_style_controls();

		$this->end_controls_section();
	}

	/**
	 * Render icon box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

        if( empty( $settings['images'] ) ) {
            return;
        }

        $col = $settings['slides_to_show'];
		$col_tablet = ! empty( $settings['slides_to_show_tablet'] ) ? $settings['slides_to_show_tablet'] : $col;
		$col_mobile = ! empty( $settings['slides_to_show_mobile'] ) ? $settings['slides_to_show_mobile'] : $col;

        $this->add_render_attribute( 'container', 'class', [ 'glozin-shoppable-images-carousel', 'glozin-carousel--elementor', 'swiper' ] );
        $this->add_render_attribute( 'container', 'data-desktop', $col );
		$this->add_render_attribute( 'container', 'data-tablet', $col_tablet );
		$this->add_render_attribute( 'container', 'data-mobile', $col_mobile );
		$this->add_render_attribute( 'container', 'style', $this->render_space_between_style() );
        $this->add_render_attribute( 'container', 'style', $this->render_aspect_ratio_style() );

        $this->add_render_attribute( 'wrapper', 'class', [ 'glozin-shoppable-images-carousel__wrapper', 'swiper-wrapper' ] );
        $this->add_render_attribute( 'item', 'class', [ 'glozin-shoppable-images-carousel__item', 'swiper-slide' ] );

        $this->add_render_attribute( 'image', 'class', [ 'glozin-shoppable-images-carousel__image', 'glozin-elementor-video', 'gz-ratio', 'gz-image-rounded', 'overflow-hidden' ] );
        $this->add_render_attribute( 'button_icon', 'class', [ 'glozin-shoppable-images-carousel__icon', 'gz-button', 'gz-button-icon', 'position-absolute', 'z-1', 'rounded-100' ] );

		$button_icon = 'icon-instagram-shop';
		if ( 'middle' == $settings['icon_position'] ) {
			$this->add_render_attribute( 'container', 'class', 'glozin-shoppable-images-carousel--middle' );
			$this->add_render_attribute( 'image', 'class', [ 'gz-hover-zoom', 'gz-hover-effect' ] );
			$this->add_render_attribute( 'button_icon', 'class', [ 'gz-button-light', 'top-50', 'start-50' ] );
			$button_icon = 'icon-instagram';
		} else {
			$this->add_render_attribute( 'button_icon', 'class', [ 'top-10', 'end-10' ] );
		}

		$is_new   	= Icons_Manager::is_migration_allowed();
		$index = 0;

		\Glozin\Addons\Helper::set_prop( 'modals', 'quickview' );
		
        ?>
        <div <?php echo $this->get_render_attribute_string( 'container' );?>>
            <div <?php echo $this->get_render_attribute_string( 'wrapper' );?>>
                <?php foreach( $settings['images'] as $image ) : ?>
					<?php $index++; ?>
                    <div <?php echo $this->get_render_attribute_string( 'item' );?>>
                        <?php if ( ! empty( $image['image']['url'] ) && 'image' == $image['source'] ) : ?>
							<div <?php echo $this->get_render_attribute_string( 'image' );?>>
								<?php
									$args = [];
									$args['image'] = $image['image'];
									$args['image_size'] = $settings['image_size'];
									echo wp_kses_post( \Elementor\Group_Control_Image_Size::get_attachment_image_html( $args ) );
								?>
							</div>
						<?php endif; ?>
						<?php if ( $this->has_video( $image ) && 'video' == $image['source'] ) : ?>
							<div <?php echo $this->get_render_attribute_string( 'image' );?>>
								<?php $this->render_video( $image ); ?>
							</div>
						<?php endif; ?>
						
						<span <?php echo $this->get_render_attribute_string( 'button_icon' );?>>
							<?php if ( ! empty( $settings['button_icon']['value'] ) ) : ?>
								<span class="gz-icon-svg">
									<?php if ( $is_new ) :
										Icons_Manager::render_icon( $settings['button_icon'], [ 'aria-hidden' => 'true' ] );
									endif; ?>
								</span>
							<?php else : ?>
								<?php echo \Glozin\Addons\Helper::get_svg( $button_icon ); ?>
							<?php endif; ?>
						</span>

						<?php 
						$button_link = false;
						$button_link_index = 'button_link' . $index;
						if( $image['action'] == 'link' && ! empty( $image['link']['url'] ) ) {
							$this->add_render_attribute( $button_link_index, 'href', $image['link']['url'] );
							$this->add_render_attribute( $button_link_index, 'target', $image['link']['is_external'] ? '_blank' : '_self' );
							$this->add_render_attribute( $button_link_index, 'rel', $image['link']['nofollow'] ? 'nofollow' : '' );
							$this->add_render_attribute( $button_link_index, 'class', [ 'glozin-shoppable-images-carousel__button-link','position-absolute', 'z-1', 'top-0', 'end-0', 'bottom-0', 'start-0'  ] );
							$button_link = true;
						} else if( $image['action'] == 'shoppable' && ! empty( $image['shoppable_images'] ) ) {
							$this->add_render_attribute( $button_link_index, 'href', '#' );
							$this->add_render_attribute( $button_link_index, 'class', [ 'glozin-shoppable-images-carousel__button-shoppable', 'position-absolute', 'z-1', 'top-0', 'end-0', 'bottom-0', 'start-0' ] );
							$this->add_render_attribute( $button_link_index, 'data-toggle', 'modal' );
							$this->add_render_attribute( $button_link_index, 'data-target', 'shoppable-images-modal' );
							$this->add_render_attribute( $button_link_index, 'data-shoppable_images_id', $image['shoppable_images'] );
							$button_link = true;
						} 

						if( $button_link ) {
							$link_for = esc_html__( 'View Instagram post number', 'glozin-addons' );
							$link_for .= ' ' . $index;
							echo '<a ' . $this->get_render_attribute_string( $button_link_index ) . '><span class="screen-reader-text">' . esc_html( $link_for ) . '</span></a>';
						}
						?>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php echo $this->render_pagination(); ?>
            <?php echo '<div class="swiper-arrows">' . $this->render_arrows() . '</div>'; ?>
        </div>
        <?php
        $this->render_shoppable_images_modal();
	}

    public function render_shoppable_images_modal() {
        ?>
        <div class="shoppable-images-modal modal">
            <div class="modal__backdrop"></div>
            <div class="modal__container">
                <div class="modal__wrapper position-relative">
                    <a href="#" class="modal__button-close position-absolute z-4 gz-button gz-button-icon" aria-label="<?php esc_attr_e( 'Close shoppable image content modal', 'glozin-addons' ); ?>">
                        <?php echo \Glozin\Addons\Helper::get_svg( 'close' ); ?>
                    </a>
                    <div class="modal__content">
						<div class="modal__shoppable"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
