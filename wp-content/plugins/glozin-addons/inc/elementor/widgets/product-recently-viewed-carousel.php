<?php
namespace Glozin\Addons\Elementor\Widgets;

use Glozin\Addons\Elementor\Base\Products_Widget_Base;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_Recently_Viewed_Carousel extends Products_Widget_Base {
	use \Glozin\Addons\Elementor\Base\Button_Base;

	public function get_name() {
		return 'glozin-product-recently-viewed-carousel';
	}

	public function get_title() {
		return esc_html__( '[Glozin] Product Recently Viewed Carousel', 'glozin-addons' );
	}

	public function get_icon() {
		return 'eicon-posts-carousel';
	}

	public function get_categories() {
		return ['glozin-addons'];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'product', 'recently', 'viewed', 'carousel' ];
	}

	public function get_script_depends() {
		return [
			'glozin-product-recently-viewed-widget',
			'glozin-products-carousel-widget'
		];
	}

	/**
	 * Register heading widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_recently_viewed_products_content',
			[
				'label' => esc_html__( 'Recently Viewed Products', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'recently_viewed_heading',
			[
				'label'     => esc_html__( 'Heading', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'glozin-addons' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your title', 'glozin-addons' ),
			]
		);
		$this->add_control(
			'description',
			[
				'label' => __( 'Description', 'glozin-addons' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'recently_viewed_content',
			[
				'label'     => esc_html__( 'Content', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'limit',
			[
				'label' => esc_html__( 'Limit', 'glozin-addons' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 5,
				'range' => [
					'px' => [
						'max' => 20,
					],
				],
			]
		);

		$this->add_control(
			'ajax_enable',
			[
				'label'       => esc_html__( 'Load With Ajax', 'glozin-addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'frontend_available' => true
			]
		);

		$this->add_control(
			'hide_no_products',
			[
				'label'       => esc_html__( 'Hide When No Products', 'glozin-addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'frontend_available' => true
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_products_carousel',
			[
				'label' => __( 'Carousel Settings', 'glozin-addons' ),
			]
		);

		$controls = [
			'slides_to_show'   => 4,
			'slides_to_scroll' => 1,
			'space_between'    => 30,
			'navigation'       => '',
			'autoplay'         => '',
			'autoplay_speed'   => 3000,
			'pause_on_hover'   => 'yes',
			'animation_speed'  => 800,
			'infinite'         => '',
		];

		$this->register_carousel_controls( $controls );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label'     => __( 'Carousel Settings', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .recently-viewed-products__elementor' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .recently-viewed-products__elementor' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .recently-viewed-products__elementor' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .recently-viewed-products__elementor' => 'margin: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'content_border',
				'label' => esc_html__( 'Border', 'glozin-addons' ),
				'selector' => '{{WRAPPER}} .recently-viewed-products__elementor',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_carousel_style',
			[
				'label'     => __( 'Carousel Settings', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->register_carousel_style_controls();

		$this->end_controls_section();

	}

	/**
	 * Render heading widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$limit = ! empty( $settings['limit'] ) ? $settings['limit'] : 5;
		$columns = $settings['slides_to_show'];
		$columns_tablet = ! empty( $settings['slides_to_show_tablet'] ) ? $settings['slides_to_show_tablet'] : 3;
		$columns_mobile = ! empty( $settings['slides_to_show_mobile'] ) ? $settings['slides_to_show_mobile'] : 2;
		$data_settings = array(
			'limit' => $limit,
			'columns' => $columns,
		);

		$this->add_render_attribute( 'wrapper', 'class', [
			'recently-viewed-products__elementor',
			'woocommerce'
		] );

		$product_class = $settings['ajax_enable'] == 'yes' ? 'has-ajax ajax-loading' : '';
		$no_product_class = $settings['hide_no_products'] == 'yes' ? ' d-none' : '';

		$this->add_render_attribute( 'swiper', 'class', [ 'swiper', 'product-swiper--elementor recently-viewed-products recently-viewed-products--elementor', $product_class ] );
		$this->add_render_attribute( 'swiper', 'data-desktop', esc_attr( $columns ) );
		$this->add_render_attribute( 'swiper', 'data-tablet', esc_attr( $columns_tablet ) );
		$this->add_render_attribute( 'swiper', 'data-mobile', esc_attr( $columns_mobile ) );
		$this->add_render_attribute( 'swiper', 'data-settings', json_encode( $data_settings ) );

		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ) ?>>
			<?php
			$product_recently_viewed_ids = self::get_product_recently_viewed_ids();
			if( ! empty( $product_recently_viewed_ids ) ) :
				?>
				<?php $this->render_heading( $settings ); ?>
				<section <?php echo $this->get_render_attribute_string( 'swiper' ) ?>>
					<?php
						if( $settings['ajax_enable'] !== 'yes' ) {
							self::get_recently_viewed_products( $settings );
						} else {
							\Glozin\Addons\Helper::set_prop( 'modals', 'quickview' );
						}
						echo $this->render_arrows();
						echo $this->render_pagination();
					?>
				</section>
			<?php else : ?>
				<?php $this->render_heading( $settings, $no_product_class ); ?>
				<div class="recently-viewed-products__no-products text-center <?php echo $no_product_class ?>">
					<p><?php echo esc_html__( 'No products in recent viewing history.', 'glozin' ) ?></p>
					<?php
					$settings['button_text'] = esc_html__( 'Back to Shopping', 'glozin-addons' );
					$settings['button_link']['url'] = esc_url( wc_get_page_permalink( 'shop' ) );
					$settings['button_classes'] = ' mt-20';
					$this->render_button( $settings, 'button_no_products' );
				?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	public function render_heading( $settings, $classes = '' ) {
		if ( empty( $settings['title'] ) && empty( $settings['description'] ) ) {
			return;
		}

		echo '<div class="recently-viewed-products__heading text-center' . esc_attr( $classes ) . '">';
		if ( ! empty( $settings['title'] ) ) {
			echo '<h2 class="recently-viewed-products__title mt-0 mb-10">' . esc_html( $settings['title'] ) . '</h2>';
		}
		if ( ! empty( $settings['description'] ) ) {
			echo '<div class="recently-viewed-products__description mb-35">' . esc_html( $settings['description'] ) . '</div>';
		}
		echo '</div>';
	}
}
