<?php
namespace Glozin\Addons\Elementor\Builder\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Ask_Question extends Widget_Base {
	use \Glozin\Addons\Elementor\Builder\Traits\Product_Id_Trait;

	public function get_name() {
		return 'glozin-ask-question';
	}

	public function get_title() {
		return esc_html__( '[Glozin] Ask Question', 'glozin-addons' );
	}

	public function get_icon() {
		return 'eicon-help-o';
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'question', 'ask', 'form', 'product' ];
	}

	public function get_categories() {
		return [ 'glozin-addons-product' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[ 'label' => __( 'Content', 'glozin-addons' ) ]
		);

		$this->add_control(
			'icon',
			[
				'label' => __( 'Icon', 'glozin-addons' ),
				'type' => Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'text',
			[
				'label' => __( 'Text', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Ask a question', 'glozin-addons' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'heading_form',
			[
				'label'     => esc_html__( 'Form', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'form_shortcode',
			[
				'label' => __( 'Enter your shortcode', 'glozin-addons' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => '',
				'placeholder' => '[contact-form-7 id="11" title="Contact form 1"]',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_product_style',
			[
				'label' => esc_html__( 'Style', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-extra-link-item .glozin-svg-icon' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-extra-link-item .glozin-svg-icon' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'icon_spacing',
			[
				'label' => esc_html__( 'Icon Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-extra-link-item' => 'gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'link_heading',
			[
				'label' => esc_html__( 'Link', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography',
				'selector' => '{{WRAPPER}} .glozin-extra-link-item',
			]
		);

		$this->add_control(
			'link_color',
			[
				'label' => esc_html__( 'Link Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-extra-link-item' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'link_hover_color',
			[
				'label' => esc_html__( 'Hover Link Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-extra-link-item:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		global $product;

		$product = $this->get_product();

		if ( ! $product ) {
			return;
		}

		if ( \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
			$this->get_ask_question_buttons( $settings );
		} else {
			if( ! empty( $settings['form_shortcode'] ) ) {
				$this->get_ask_question_buttons( $settings );
			}
		}

		if( ! empty( $settings['form_shortcode'] ) ) {
			add_action( 'wp_footer', [ $this, 'ask_question_content' ], 50 );
		}
	}

	public function get_ask_question_buttons( $settings ) {
		echo '<div class="glozin-product-extra-link">';
			echo '<a href="#" class="glozin-extra-link-item glozin-extra-link-item--ask-question d-inline-flex align-items-center gap-10 lh-normal text-base text-hover-color" data-toggle="modal" data-target="product-ask-question-modal-'. esc_attr( $this->get_id() ) .'">';
				if( ! empty( $settings['icon']['value'] ) ) {
					echo '<span class="glozin-svg-icon glozin-svg-icon--question">' . \Elementor\Icons_Manager::try_get_icon_html( $settings['icon'], [ 'aria-hidden' => 'true' ] ) . '</span>';
				} else {
					echo \Glozin\Addons\Helper::get_svg( 'question' );
				}

				if( ! empty( $settings['text'] ) ) {
					echo esc_html( $settings['text'] );
				} else {
					echo esc_html__( 'Ask a question', 'glozin' );
				}
			echo '</a>';
		echo '</div>';
	}

	/**
	 * Product ask question content panel
	 */
	public function ask_question_content() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="product-ask-question-modal modal product-extra-link-modal glozin-contact-form" data-id="product-ask-question-modal-<?php echo esc_attr( $this->get_id() ); ?>">
			<div class="modal__backdrop"></div>
			<div class="modal__container">
				<div class="modal__wrapper">
					<div class="modal__header">
						<h3 class="modal__title h5"><?php esc_html_e( 'Ask a question', 'glozin' ); ?></h3>
						<a href="#" class="modal__button-close">
							<?php echo \Glozin\Addons\Helper::get_svg( 'close', 'ui' ); ?>
						</a>
					</div>
					<div class="modal__content">
						<div class="ask-question-content"><?php echo do_shortcode( wp_kses_post( $settings['form_shortcode'] ) ); ?></div>
					</div>
				</div>
			</div>
			<span class="modal__loader"><span class="glozinSpinner"></span></span>
		</div>
		<?php
	}
}
