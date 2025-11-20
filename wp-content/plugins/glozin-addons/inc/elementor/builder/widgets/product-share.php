<?php
namespace Glozin\Addons\Elementor\Builder\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_Share extends Widget_Base {
	use \Glozin\Addons\Elementor\Builder\Traits\Product_Id_Trait;

	public function get_name() {
		return 'glozin-product-share';
	}

	public function get_title() {
		return esc_html__( '[Glozin] Product Share', 'glozin-addons' );
	}

	public function get_icon() {
		return 'eicon-share';
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'social', 'share', 'product' ];
	}

	public function get_categories() {
		return [ 'glozin-addons-product' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_settings',
			[
				'label' => __( 'Content', 'glozin-addons' ),
			]
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
				'placeholder' => __( 'Share', 'glozin-addons' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'socials',
			[
				'label' => esc_html__( 'Select socials', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'options' => [
					'facebook'    => esc_html__( 'Facebook', 'glozin-addons' ),
					'twitter'     => esc_html__( 'Twitter', 'glozin-addons' ),
					'googleplus'  => esc_html__( 'Google Plus', 'glozin-addons' ),
					'pinterest'   => esc_html__( 'Pinterest', 'glozin-addons' ),
					'tumblr'      => esc_html__( 'Tumblr', 'glozin-addons' ),
					'reddit'      => esc_html__( 'Reddit', 'glozin-addons' ),
					'linkedin'    => esc_html__( 'Linkedin', 'glozin-addons' ),
					'stumbleupon' => esc_html__( 'StumbleUpon', 'glozin-addons' ),
					'digg'        => esc_html__( 'Digg', 'glozin-addons' ),
					'telegram'    => esc_html__( 'Telegram', 'glozin-addons' ),
					'whatsapp'    => esc_html__( 'WhatsApp', 'glozin-addons' ),
					'vk'          => esc_html__( 'VK', 'glozin-addons' ),
					'email'       => esc_html__( 'Email', 'glozin-addons' ),
				],
				'default' => [ 'facebook', 'twitter', 'tumblr', 'whatsapp', 'email' ],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'whatsapp_number',
			[
				'label' => esc_html__( 'WhatsApp Phone Number', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'conditions' => [
					'terms' => [
						[
							'name' => 'socials',
							'operator' => 'contains',
							'value' => 'whatsapp',
						],
					],
				],
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
			$this->get_product_share_button( $settings );
		} else {
			if( ! empty( $settings['socials'] ) ) {
				$this->get_product_share_button( $settings );
			}
		}

		if( ! empty( $settings['socials'] ) ) {
			add_action( 'wp_footer', [ $this, 'product_share_content' ], 40 );
		}
	}

	public function get_product_share_button( $settings ) {
		echo '<div class="glozin-product-extra-link">';
			echo '<a href="#" class="glozin-extra-link-item glozin-extra-link-item--share d-inline-flex align-items-center gap-10 lh-normal text-base text-hover-color" data-toggle="modal" data-target="product-share-modal-'. esc_attr( $this->get_id() ) .'">';
				if( ! empty( $settings['icon']['value'] ) ) {
					echo '<span class="glozin-svg-icon glozin-svg-icon--share">' . \Elementor\Icons_Manager::try_get_icon_html( $settings['icon'], [ 'aria-hidden' => 'true' ] ) . '</span>';
				} else {
					echo \Glozin\Addons\Helper::get_svg( 'share' );
				}

				if( ! empty( $settings['text'] ) ) {
					echo esc_html( $settings['text'] );
				} else {
					echo esc_html__( 'Share', 'glozin' );
				}
			echo '</a>';
		echo '</div>';
	}

	/**
	 * Product Share content
	 */
	public function product_share_content() {
		$settings = $this->get_settings_for_display();
		if( empty( $settings['socials'] ) ) {
			return;
		}

		?>
		<div class="product-share-modal modal product-extra-link-modal" data-id="product-share-modal-<?php echo esc_attr( $this->get_id() ); ?>">
			<div class="modal__backdrop"></div>
			<div class="modal__container">
				<div class="modal__wrapper">
					<div class="modal__header">
						<h3 class="modal__title h5"><?php esc_html_e( 'Copy link', 'glozin' ); ?></h3>
						<a href="#" class="modal__button-close">
							<?php echo \Glozin\Addons\Helper::get_svg( 'close', 'ui' ); ?>
						</a>
					</div>
					<div class="modal__content">
						<div class="product-share__copylink">
							<form class="gz-responsive d-flex align-items-center gap-10 mb-20">
								<input class="product-share__copylink--link glozin-copylink__link flex-1" type="text" value="<?php echo esc_url( get_permalink( get_the_ID() ) ); ?>" readonly="readonly" />
								<button class="product-share__copylink--button glozin-copylink__button gz-button gz-button-icon" data-icon="<?php echo esc_attr( \Glozin\Addons\Helper::get_svg( 'copy' ) ); ?>" data-icon_copied="<?php echo esc_attr( \Glozin\Addons\Helper::inline_svg( ['icon' => 'check', 'class' => 'has-vertical-align'] ) ); ?>"><?php echo \Glozin\Addons\Helper::get_svg( 'copy' ); ?></button>
							</form>
						</div>
						<div class="product-share__share">
							<div class="product-share__copylink-heading h6 mb-15 mt-0"><?php echo esc_html__( 'Share', 'glozin' ); ?></div>
							<?php echo ! empty( $this->share_socials( $settings['socials'], $settings['whatsapp_number'] ) ) ? $this->share_socials( $settings['socials'], $settings['whatsapp_number'] ) : '' ; ?>
						</div>
					</div>
				</div>
			</div>
			<span class="modal__loader"><span class="glozinSpinner"></span></span>
		</div>
		<?php
	}

	/**
	 * Button Share
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function share_socials( $socials, $whatsapp_number ) {
		if ( ! class_exists( '\Glozin\Addons\Helper' ) && ! method_exists( '\Glozin\Addons\Helper','share_link' )) {
			return;
		}

		$args = array();
		if ( ( ! empty( $socials ) ) ) {
			$output = array();

			foreach ( $socials as $social => $value ) {
				if( $value == 'whatsapp' ) {
					$args['whatsapp_number'] = $whatsapp_number;
				}

				if( $value == 'facebook' ) {
					$args[$value]['icon'] = 'facebook-f';
				}

				$output[] = \Glozin\Addons\Helper::share_link( $value, $args, false );
			}

			return sprintf( '<ul class="post__socials-share d-flex align-items-center flex-wrap gap-10 my-0 py-0 list-unstyled">%s</ul>', implode( '', $output )	);
		};
	}
}
