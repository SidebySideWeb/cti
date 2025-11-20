<?php
namespace Glozin\Addons\Elementor\Widgets;

use Glozin\Addons\Elementor\Base\Products_Widget_Base;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_Recently_Viewed extends Products_Widget_Base {
	use \Glozin\Addons\Elementor\Base\Button_Base;

	public function get_name() {
		return 'glozin-product-recently-viewed';
	}

	public function get_title() {
		return esc_html__( '[Glozin] Product Recently Viewed Grid', 'glozin-addons' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return ['glozin-addons'];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'product', 'recently', 'viewed' ];
	}

	public function get_script_depends() {
		return [
			'glozin-product-recently-viewed-widget',
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
			'columns',
			[
				'label' => esc_html__( 'Columns', 'glozin-addons' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 4,
				'range' => [
					'px' => [
						'max' => 6,
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
			'clear_enable',
			[
				'label'     => esc_html__( 'Clear All Button', 'glozin-addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__( 'Yes', 'glozin-addons' ),
				'label_off' => esc_html__( 'No', 'glozin-addons' ),
				'return_value' => 'yes',
				'default'   => '',
			]
		);

		$this->add_control(
			'pagination',
			[
				'label' => __( 'Pagination', 'glozin-addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'glozin-addons' ),
				'label_on'  => __( 'Show', 'glozin-addons' ),
				'default'   => '',
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
			'section_content_style',
			[
				'label' => __( 'Content', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_title',
			[
				'label'     => esc_html__( 'Title', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .recently-viewed-products__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .recently-viewed-products__title',
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label' => __( 'Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					]
				],
				'default' => [],
				'selectors' => [
					'{{WRAPPER}} .recently-viewed-products__title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'heading_description',
			[
				'label'     => esc_html__( 'Description', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator'  => 'before',
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => __( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .recently-viewed-products__description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'description_typography',
				'selector' => '{{WRAPPER}} .recently-viewed-products__description',
			]
		);

		$this->add_responsive_control(
			'description_spacing',
			[
				'label' => __( 'Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					]
				],
				'default' => [],
				'selectors' => [
					'{{WRAPPER}} .recently-viewed-products__description' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Button', 'glozin-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->register_button_style_controls();

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
		$columns = ! empty( $settings['columns'] ) ? $settings['columns'] : 4;
		$pagination = ! empty( $settings['pagination'] ) ? $settings['pagination'] : '';
		$ajax_enable = ! empty( $settings['ajax_enable'] ) ? $settings['ajax_enable'] : '';

		$data_settings = array(
			'limit' => $limit,
			'columns' => $columns,
			'pagination' => $pagination,
			'ajax_enable' => $ajax_enable,
		);

		$this->add_render_attribute( 'wrapper', 'class', [
			'recently-viewed-products__elementor',
			'woocommerce'
		] );
		$product_recently_viewed_ids = self::get_product_recently_viewed_ids();
		$no_product_class = $settings['hide_no_products'] == 'yes' ? ' d-none' : '';
		$empty_class = ! empty( $product_recently_viewed_ids ) ? 'd-none' : '';

		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ) ?>>
			<?php
			if( ! empty( $product_recently_viewed_ids ) ) :
				?>
				<?php $this->render_heading( $settings ); ?>
				<section class="recently-viewed-products recently-viewed-products--elementor products <?php echo $settings['ajax_enable'] == 'yes' ? 'has-ajax ajax-loading' : ''; ?>" data-settings="<?php echo esc_attr( json_encode( $data_settings ) ) ?>">
					<?php
						if( $settings['ajax_enable'] !== 'yes' ) {
							self::get_recently_viewed_products( $settings );
						} else {
							\Glozin\Addons\Helper::set_prop( 'modals', 'quickview' );
						}
					?>
				</section>
				<?php if ( $settings['clear_enable'] == 'yes' ) : ?>
					<div class="recently-viewed-products__clear text-center">
						<?php
							$settings['button_text'] = esc_html__( 'Clear All', 'glozin-addons' );
							$settings['button_link']['url'] = '#';
							$settings['button_classes'] = ' recently-viewed-products__clear-all mt-50';
							$this->render_button( $settings, 'button_clear' );
						?>
					</div>
				<?php endif; ?>
			<?php endif; ?>
			<div class="recently-viewed-products__empty <?php echo esc_attr( $empty_class ) ?>">
				<?php $this->render_heading( $settings, $no_product_class ); ?>
				<div class="recently-viewed-products__no-products text-center<?php echo esc_attr( $no_product_class ) ?>">
					<p class="mb-0"><?php echo esc_html__( 'No products in recent viewing history.', 'glozin' ) ?></p>
					<?php
						$settings['button_text'] = esc_html__( 'Back to Shopping', 'glozin-addons' );
						$settings['button_link']['url'] = esc_url( wc_get_page_permalink( 'shop' ) );
						$settings['button_classes'] = ' mt-20 px-30 min-w-200';
						$this->render_button( $settings, 'button_no_products' );
					?>
				</div>
			</div>
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
