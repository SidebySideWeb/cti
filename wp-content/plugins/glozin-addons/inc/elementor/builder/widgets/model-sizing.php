<?php
namespace Glozin\Addons\Elementor\Builder\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Model_Sizing extends Widget_Base {
	use \Glozin\Addons\Elementor\Builder\Traits\Product_Id_Trait;

	public function get_name() {
		return 'glozin-model-sizing';
	}

	public function get_title() {
		return esc_html__( '[Glozin] Model Sizing', 'glozin-addons' );
	}

	public function get_icon() {
		return 'eicon-call-to-action';
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'model', 'sizing', 'product' ];
	}

	public function get_categories() {
		return [ 'glozin-addons-product' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_product_style',
			[
				'label' => esc_html__( 'Style', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'gap',
			[
				'label' => esc_html__( 'Gap', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .model-sizing' => 'gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'thumbnail_heading',
			[
				'label' => esc_html__( 'Thumbnail', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'thumbnail_width',
			[
				'label' => esc_html__( 'Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .model-sizing-thumbnail' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'thumbnail_height',
			[
				'label' => esc_html__( 'Height', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .model-sizing-thumbnail' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'thumbnail_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .model-sizing-thumbnail img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .model-sizing-thumbnail img' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'informations_heading',
			[
				'label' => esc_html__( 'Informations', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'informations_column_gap',
			[
				'label' => esc_html__( 'Column Gap', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .model-sizing-informations' => 'column-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'label_heading',
			[
				'label' => esc_html__( 'Label', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'label_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .model-sizing-information__label' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} .model-sizing-information__label',
			]
		);

		$this->add_control(
			'value_heading',
			[
				'label' => esc_html__( 'Value', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'value_color',
			[
				'label' => esc_html__( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .model-sizing-information__value' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'value_typography',
				'selector' => '{{WRAPPER}} .model-sizing-information__value',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		global $product;

		$product = $this->get_product();

		if ( ! $product ) {
			return;
		}

		if ( \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
			$this->render_html($product);
		} else {
			do_action('glozin_model_sizing_elementor');
		}
	}

    public function render_html($product) {
        ?>
            <div class="model-sizing d-flex align-items-center gap-20 border-bottom pb-20 mb-25">
				<div class="model-sizing-thumbnail gz-ratio flex-shrink-0">
					<?php echo wp_get_attachment_image( $product->get_image_id(), 'woocommerce_thumbnail' ); ?>
                </div>
				<div class="model-sizing-informations d-flex flex-wrap flex-column flex-md-row column-gap-20">
					<div class="model-sizing-information__item w-100">
						<span class="model-sizing-information__label"><?php esc_html_e('Model is Wearing:', 'glozin-addons'); ?></span>
						<span class="model-sizing-information__value text-dark fw-semibold"><?php esc_html_e('S', 'glozin-addons'); ?></span>
					</div>
					<div class="model-sizing-information__item--left">
						<div class="model-sizing-information__item">
                            <span class="model-sizing-information__label"><?php esc_html_e('Height:', 'glozin-addons'); ?></span>
                            <span class="model-sizing-information__value text-dark fw-semibold"><?php esc_html_e('175cm', 'glozin-addons'); ?></span>
                        </div>
						<div class="model-sizing-information__item">
                            <span class="model-sizing-information__label"><?php esc_html_e('Shoulder width:', 'glozin-addons'); ?></span>
                            <span class="model-sizing-information__value text-dark fw-semibold"><?php esc_html_e('43cm', 'glozin-addons'); ?></span>
                        </div>
					</div>
					<div class="model-sizing-information__item--right">
                        <div class="model-sizing-information__item">
                            <span class="model-sizing-information__label"><?php esc_html_e('Weight:', 'glozin-addons'); ?></span>
                            <span class="model-sizing-information__value text-dark fw-semibold"><?php esc_html_e('53kg', 'glozin-addons'); ?></span>
                        </div>
                        <div class="model-sizing-information__item">
                            <span class="model-sizing-information__label"><?php esc_html_e('Bust/waist/hips:', 'glozin-addons'); ?></span>
                            <span class="model-sizing-information__value text-dark fw-semibold"><?php esc_html_e('80/61/95', 'glozin-addons'); ?></span>
                        </div>
                    </div>
				</div>
			</div>
        <?php
    }
}
