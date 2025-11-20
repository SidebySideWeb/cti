<?php
namespace Glozin\Addons\Elementor\Builder\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_Navigation extends Widget_Base {
	use \Glozin\Addons\Elementor\Builder\Traits\Product_Id_Trait;

	public function get_name() {
		return 'glozin-product-navigation';
	}

	public function get_title() {
		return esc_html__( '[Glozin] Product Navigation', 'glozin-addons' );
	}

	public function get_icon() {
		return 'eicon-post-navigation';
	}

	public function get_categories() {
		return ['glozin-addons-product'];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'navigation', 'product' ];
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
			'section_navigation',
			[
				'label' => esc_html__( 'Product Navigation', 'glozin-addons' ),
			]
		);

        $this->add_control(
			'icon_previous',
			[
				'label' => __( 'Icon Previous', 'glozin-addons' ),
				'type' => Controls_Manager::ICONS,
			]
		);

        $this->add_control(
			'icon_next',
			[
				'label' => __( 'Icon Next', 'glozin-addons' ),
				'type' => Controls_Manager::ICONS,
			]
		);

        $this->add_control(
			'icon_category',
			[
				'label' => __( 'Icon Category', 'glozin-addons' ),
				'type' => Controls_Manager::ICONS,
			]
		);

		$this->end_controls_section();

        $this->start_controls_section(
			'section_navigation_style',
			[
				'label' => esc_html__( 'Style', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_control(
			'gap',
			[
				'label' => __( 'Gap', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					]
				],
				'default' => [],
				'selectors' => [
					'{{WRAPPER}} .product-navigation' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
			'icon_heading',
			[
				'label' => esc_html__( 'Icon', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'icon_size',
			[
				'label' => __( 'Size', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					]
				],
				'default' => [],
				'selectors' => [
					'{{WRAPPER}} .product-navigation__button .glozin-svg-icon' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-navigation__button .glozin-svg-icon' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'icon_hover_color',
			[
				'label' => __( 'Hover Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-navigation__button:hover .glozin-svg-icon' => 'color: {{VALUE}}',
				],
			]
		);

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

		global $product;

        $product = $this->get_product();

		if ( ! $product ) {
			return;
		}

        $term        = $product->get_category_ids()[0];
		$taxonomy    = 'product_cat';
		$prevProduct = get_previous_post( true, '', $taxonomy );
		$nextProduct = get_next_post( true, '', $taxonomy );

        $this->add_render_attribute( 'navigation', 'class', [ 'product-navigation', 'position-relative', 'd-none d-flex-md', 'align-items-center', 'gap-10' ] );

		if ( \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
			$prevProduct = true;
			$nextProduct = true;
			$prevProductTitle = esc_html__( 'Previous Product Title', 'glozin-addons' );
			$nextProductTitle = esc_html__( 'Next Product Title', 'glozin-addons' );
		} else {
			if( ! empty( $prevProduct ) ) {
				$prevProductTitle = $prevProduct->post_title;
			}
			
			if( ! empty( $nextProduct ) ) {
                $nextProductTitle = $nextProduct->post_title;
            }
		}

        ?>
            <div <?php echo $this->get_render_attribute_string( 'navigation' ); ?>>
				<?php if( is_rtl() ) : ?>
					<?php if( ! empty( $nextProduct ) ) : ?>
						<a class="product-navigation__button product-navigation__button-next d-inline-flex align-items-center justify-content-center text-dark-grey fs-11 py-10 pe-10" href="<?php echo esc_url( get_permalink( $nextProduct ) ); ?>">
                            <?php if( $settings['icon_next'] && $settings['icon_next']['value'] ) {
                                    echo '<span class="glozin-svg-icon glozin-svg-icon--next">' . \Elementor\Icons_Manager::try_get_icon_html( $settings['icon_next'], [ 'aria-hidden' => 'true' ] ) . '</span>';
                                } else {
							        echo \Glozin\Addons\Helper::inline_svg(['icon' => 'icon-next', 'class' => 'has-vertical-align']) ;
                                } ?>
						</a>
					<?php endif; ?>
				<?php else : ?>
					<?php if( ! empty( $prevProduct ) ) : ?>
						<a class="product-navigation__button product-navigation__button-prev d-inline-flex align-items-center justify-content-center text-dark-grey fs-11 py-10 pe-10" href="<?php echo esc_url( get_permalink( $prevProduct ) ); ?>">
                            <?php if( $settings['icon_previous'] && $settings['icon_previous']['value'] ) {
                                    echo '<span class="glozin-svg-icon glozin-svg-icon--previous">' . \Elementor\Icons_Manager::try_get_icon_html( $settings['icon_previous'], [ 'aria-hidden' => 'true' ] ) . '</span>';
                                } else {
							        echo \Glozin\Addons\Helper::inline_svg(['icon' => 'icon-back', 'class' => 'has-vertical-align']); 
                                } ?>
						</a>
					<?php endif; ?>
				<?php endif; ?>

				<a class="product-navigation__button d-inline-flex text-dark gz-tooltip fs-16" href="<?php echo get_term_link( $term, $taxonomy ); ?>" data-tooltip="<?php echo esc_attr__( 'Back to products', 'glozin' ); ?>">
                    <?php if( $settings['icon_category'] && $settings['icon_category']['value'] ) {
                            echo '<span class="glozin-svg-icon glozin-svg-icon--category">' . \Elementor\Icons_Manager::try_get_icon_html( $settings['icon_category'], [ 'aria-hidden' => 'true' ] ) . '</span>';
                        } else {
                            echo \Glozin\Addons\Helper::get_svg( 'object-column' ); 
                        } ?>
				</a>

				<?php if( is_rtl() ) : ?>
					<?php if( ! empty( $prevProduct ) ) : ?>
						<a class="product-navigation__button product-navigation__button-prev d-inline-flex align-items-center justify-content-center text-dark-grey fs-11 py-10 ps-10" href="<?php echo esc_url( get_permalink( $prevProduct ) ); ?>">
                            <?php if( $settings['icon_previous'] && $settings['icon_previous']['value'] ) {
                                    echo '<span class="glozin-svg-icon glozin-svg-icon--previous">' . \Elementor\Icons_Manager::try_get_icon_html( $settings['icon_previous'], [ 'aria-hidden' => 'true' ] ) . '</span>';
                                } else {
							        echo \Glozin\Addons\Helper::inline_svg(['icon' => 'icon-back', 'class' => 'has-vertical-align']); 
                                } ?>
						</a>
					<?php endif; ?>
				<?php else : ?>
					<?php if( ! empty( $nextProduct ) ) : ?>
						<a class="product-navigation__button product-navigation__button-next d-inline-flex align-items-center justify-content-center text-dark-grey fs-11 py-10 ps-10" href="<?php echo esc_url( get_permalink( $nextProduct ) ); ?>">
                            <?php if( $settings['icon_next'] && $settings['icon_next']['value'] ) {
                                    echo '<span class="glozin-svg-icon glozin-svg-icon--next">' . \Elementor\Icons_Manager::try_get_icon_html( $settings['icon_next'], [ 'aria-hidden' => 'true' ] ) . '</span>';
                                } else {
							        echo \Glozin\Addons\Helper::inline_svg(['icon' => 'icon-next', 'class' => 'has-vertical-align']); 
                                } ?>
						</a>
					<?php endif; ?>
				<?php endif; ?>
				<?php if( ! empty( $nextProduct ) ) : ?>
					<div class="product-navigation__tooltip product-navigation__tooltip-next position-absolute end-0 d-none d-flex-md gap-10 px-10 py-10 bg-light z-2 rounded-5 invisible pe-none">
						<div class="product-navigation__tooltip-image">
							<a href="<?php echo esc_url( get_permalink( $nextProduct ) ); ?>">
								<?php 
								$product = ! empty( $nextProduct->ID ) ? wc_get_product($nextProduct->ID) : $product;
								echo ! empty( $product ) ? $product->get_image('woocommerce_gallery_thumbnail') : '';
								?>
							</a>
						</div>
						<div class="product-navigation__tooltip-summary">
							<div class="product-navigation__tooltip-title fs-14 fw-medium lh-normal">
								<a href="<?php echo esc_url( get_permalink( $nextProduct ) ); ?>">
									<?php echo esc_html( $nextProductTitle ); ?>
								</a>
							</div>
							<div class="product-navigation__tooltip-price fs-13 mt-8 lh-normal"><p class="price"><?php echo wc_get_product( !empty( $nextProduct->ID ) ? $nextProduct->ID : $product->get_id() )->get_price_html(); ?></p></div>
						</div>
					</div>
				<?php endif; ?>
				<?php if( ! empty( $prevProduct ) ) : ?>
					<div class="product-navigation__tooltip product-navigation__tooltip-prev position-absolute end-0 d-none d-flex-md gap-10 px-10 py-10 bg-light z-2 rounded-5 invisible pe-none">
						<div class="product-navigation__tooltip-image">
							<a href="<?php echo esc_url( get_permalink( $prevProduct ) ); ?>">
								<?php 
								$product = ! empty( $prevProduct->ID ) ? wc_get_product($prevProduct->ID) : $product;
								echo ! empty( $product ) ? $product->get_image('woocommerce_gallery_thumbnail') : '';
								?>
							</a>
						</div>
						<div class="product-navigation__tooltip-summary">
							<div class="product-navigation__tooltip-title fs-14 fw-medium lh-normal">
								<a href="<?php echo esc_url( get_permalink( $prevProduct ) ); ?>">
									<?php echo esc_html( $prevProductTitle ); ?>
								</a>
							</div>
							<div class="product-navigation__tooltip-price fs-13 mt-8 lh-normal"><p class="price"><?php echo wc_get_product( ! empty( $prevProduct->ID ) ? $prevProduct->ID : $product->get_id() )->get_price_html(); ?></p></div>
						</div>
					</div>
				<?php endif; ?>
			</div>
        <?php
	}
}