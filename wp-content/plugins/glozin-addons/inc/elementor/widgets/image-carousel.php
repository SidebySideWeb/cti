<?php
namespace Glozin\Addons\Elementor\Widgets;

use Glozin\Addons\Elementor\Base\Carousel_Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Repeater;
use Elementor\Group_Control_Image_Size;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor widget.
 *
 * Elementor widget that displays an eye-catching headlines.
 *
 * @since 1.0.0
 */
class Image_Carousel extends Carousel_Widget_Base {
	use \Glozin\Addons\Elementor\Base\Aspect_Ratio_Base;
	/**
	 * Get widget name.
	 *
	 * Retrieve Stores Location widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-image-carousel';
	}

	/**
	 * Get widget title
	 *
	 * Retrieve Stores Location widget title
	 *
	 * @return string Widget title
	 */
	public function get_title() {
		return __( '[Glozin] Images Carousel', 'glozin-addons' );
	}

	/**
	 * Get widget icon
	 *
	 * Retrieve TeamMemberGrid widget icon
	 *
	 * @return string Widget icon
	 */
	public function get_icon() {
		return 'eicon-carousel';
	}

	/**
	 * Get widget categories
	 *
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @return string Widget categories
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
			'glozin-elementor-widgets'
		];
	}

	/**
	 * Get style dependencies.
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return [ 'glozin-elementor-css' ];
	}

	/**
	 * Register heading widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->section_content();
		$this->section_style();
	}

    // Tab Content
	protected function section_content() {
		$this->section_content_slides();
		$this->section_slider_options();
	}

	protected function section_content_slides() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'glozin-addons' ),
			]
		);

		$repeater = new Repeater();

        $repeater->add_responsive_control(
			'image',
			[
				'label'    => __( 'Image', 'glozin-addons' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => wc_placeholder_img_src(),
				],
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
			]
		);

		$this->add_control(
			'items',
			[
				'label' => esc_html__( 'Items', 'glozin-addons' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'image' => [
							'url' => wc_placeholder_img_src(),
						],
						'link' => [
							'url' => '#',
						],
					],
					[
						'image' => [
							'url' => wc_placeholder_img_src(),
						],
						'link' => [
							'url' => '#',
						],
					],
					[
						'image' => [
							'url' => wc_placeholder_img_src(),
						],
						'link' => [
							'url' => '#',
						],
					],
					[
						'image' => [
							'url' => wc_placeholder_img_src(),
						],
						'link' => [
							'url' => '#',
						],
					],
					[
						'image' => [
							'url' => wc_placeholder_img_src(),
						],
						'link' => [
							'url' => '#',
						],
					],
					[
						'image' => [
							'url' => wc_placeholder_img_src(),
						],
						'link' => [
							'url' => '#',
						],
					],
					[
						'image' => [
							'url' => wc_placeholder_img_src(),
						],
						'link' => [
							'url' => '#',
						],
					],
				],
			]
		);

		$this->register_aspect_ratio_controls( [], [ 'aspect_ratio_type' => 'horizontal' ] );

		$this->end_controls_section();
	}

    protected function section_slider_options() {
		$this->start_controls_section(
			'section_slider_options',
			[
				'label' => esc_html__( 'Carousel Settings', 'glozin-addons' ),
				'type'  => Controls_Manager::SECTION,
			]
		);

		$controls = [
			'slides_to_show'   => 6,
			'slides_to_scroll' => 1,
			'space_between'    => 0,
			'navigation'       => 'dots',
			'autoplay'         => '',
			'autoplay_speed'   => 3000,
			'pause_on_hover'   => 'yes',
			'animation_speed'  => 800,
			'infinite'         => '',
		];

		$this->register_carousel_controls($controls);

		$this->end_controls_section();
	}

    // Tab Style
	protected function section_style() {
		$this->section_style_content();
		$this->section_style_carousel();
	}

	protected function section_style_content() {
		// Style
		$this->start_controls_section(
			'section_style',
			[
				'label'     => __( 'Content', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-image-carousel' => '--gz-border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-image-carousel__item' => 'border-start-start-radius: {{TOP}}{{UNIT}}; border-start-end-radius: {{RIGHT}}{{UNIT}}; border-end-end-radius: {{BOTTOM}}{{UNIT}}; border-end-start-radius: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

    protected function section_style_carousel() {
		$this->start_controls_section(
			'section_style_carousel',
			[
				'label' => esc_html__( 'Carousel Settings', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->register_carousel_style_controls();

		$this->end_controls_section();
	}

	/**
	 * Render heading widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$col = $settings['slides_to_show'];
		$col_tablet = ! empty( $settings['slides_to_show_tablet'] ) ? $settings['slides_to_show_tablet'] : $col;
		$col_mobile = ! empty( $settings['slides_to_show_mobile'] ) ? $settings['slides_to_show_mobile'] : $col;

        $this->add_render_attribute( 'wrapper', 'class', [ 'glozin-image-carousel', 'glozin-carousel--elementor', 'swiper' ] );
		$this->add_render_attribute( 'wrapper', 'data-desktop', $col );
		$this->add_render_attribute( 'wrapper', 'data-tablet', $col_tablet );
		$this->add_render_attribute( 'wrapper', 'data-mobile', $col_mobile );
		$this->add_render_attribute( 'wrapper', 'style', $this->render_space_between_style() );
		$this->add_render_attribute( 'wrapper', 'style', $this->render_aspect_ratio_style() );

        $this->add_render_attribute( 'inner', 'class', [ 'glozin-image-carousel__inner', 'd-flex', 'swiper-wrapper'] );
        $this->add_render_attribute( 'item', 'class', [ 'glozin-image-carousel__item', 'd-flex', 'position-relative', 'swiper-slide', 'border', 'overflow-hidden' ] );
        $this->add_render_attribute( 'image', 'class', [ 'glozin-image-carousel__image' ] );
    ?>
        <div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<div <?php echo $this->get_render_attribute_string( 'inner' ); ?>>
				<?php foreach( $settings['items'] as $index => $item ) : ?>
					<?php
						$link_key = $this->get_repeater_setting_key( 'link', 'image_carousel', $index );
						$this->add_render_attribute( $link_key, 'class', [ 'gz-ratio' ] );
						$this->add_link_attributes( $link_key, $item['link'] );
						$aria_label = esc_html__( 'Link for image', 'glozin-addons' );
						$aria_label .= ' ' . $index + 1;
						$this->add_render_attribute( $link_key, 'aria-label', $aria_label );
					?>
					<div <?php echo $this->get_render_attribute_string( 'item' ); ?>>
					<?php if( ! empty( $item['image'] ) && ! empty( $item['image']['url'] ) ) : ?>
						<?php if( $item['link']['url'] ) : ?>
							<a <?php echo $this->get_render_attribute_string( $link_key ); ?>>
						<?php endif; ?>
							<?php
								$image_args = [
									'image'        => ! empty( $item['image'] ) ? $item['image'] : '',
									'image_tablet' => ! empty( $item['image_tablet'] ) ? $item['image_tablet'] : '',
									'image_mobile' => ! empty( $item['image_mobile'] ) ? $item['image_mobile'] : '',
								];
							?>
							<?php echo \Glozin\Addons\Helper::get_responsive_image_elementor( $image_args ); ?>
						<?php if( $item['link']['url'] ) : ?>
							</a>
						<?php endif; ?>
					<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
			<?php echo '<div class="swiper-arrows">'. $this->render_arrows() .'</div>'; ?>
            <?php echo $this->render_pagination(); ?>
        </div>
    <?php
	}
}