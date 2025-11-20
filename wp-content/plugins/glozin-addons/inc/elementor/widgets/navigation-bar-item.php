<?php
namespace Glozin\Addons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Controls_Stack ;
use Elementor\Group_Control_Border ;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Icon Box widget
 */
class Navigation_Bar_Item extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-navigation-bar-item';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Glozin] Navigation Bar Item', 'glozin-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-icon-box';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return ['glozin-addons-navigation'];
	}

	/**
	 * Get widget keywords.
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @return array Widget keywords.
	 */
   	public function get_keywords() {
	   return [ 'navigation', 'navigation bar', 'item', 'glozin-addons' ];
   	}

	/**
	 * Register the widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->content_sections();
		$this->style_sections();
	}

	protected function content_sections() {
		$this->start_controls_section(
			'section_icon',
			[ 'label' => __( 'Navigation Bar', 'glozin-addons' ) ]
		);

		$this->add_control(
			'icon_type',
			[
				'label' => __( 'Icon Type', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'icon' => __( 'Icon', 'glozin-addons' ),
					'image' => __( 'Image', 'glozin-addons' ),
					'external' => __( 'External', 'glozin-addons' ),
				],
				'default' => 'icon',
			]
		);

		$this->add_control(
			'icon',
			[
				'label' => __( 'Icon', 'glozin-addons' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fa fa-star',
					'library' => 'fa-solid',
				],
				'condition' => [
					'icon_type' => 'icon',
				],
			]
		);

		$this->add_control(
			'image',
			[
				'label' => __( 'Choose Image', 'glozin-addons' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'icon_type' => 'image',
				],
			]
		);

		$this->add_control(
			'icon_url',
			[
				'label' => __( 'External Icon URL', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'icon_type' => 'external',
				],
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'This is the title', 'glozin-addons' ),
				'placeholder' => __( 'Enter your title', 'glozin-addons' ),
				'label_block' => true,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'action',
			[
				'label' => __( 'Clicked Action', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'link',
				'options' => [
					'link'  => __( 'Redirect to Custom URL', 'glozin-addons' ),
					'cart'  => __( 'Open Cart Panel (Offcanvas)', 'glozin-addons' ),
					'account' => __( 'Login Popup or Account Panel', 'glozin-addons' ),
					'filter' => __( 'Filter Panel', 'glozin-addons' ),
				],
			]
		);

		$this->add_control(
			'filter_panel_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'If you want to open the filter panel, you need to create a filter panel in the filter panel manager.', 'glozin-addons' ),
				'condition' => [
					'action' => 'filter',
				],
			]
		);

		$this->add_control(
			'custom_url',
			[
				'label'       => __( 'Custom URL', 'glozin-addons' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [
					'active' => true,
				],
				'default' => [
					'url' => '#',
				],
				'placeholder' => __( 'https://your-link.com', 'glozin-addons' ),
				'condition'   => [
					'action' => 'link',
				],
			]
		);

		$this->add_control(
			'counter',
			[
				'label'   => __( 'Counter Display', 'glozin-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'     => __( 'Do Not Show', 'glozin-addons' ),
					'cart'     => __( 'Show Cart Item Count', 'glozin-addons' ),
					'wishlist' => __( 'Show Wishlist Item Count', 'glozin-addons' ),
				],
				'condition' => [
					'action!' => 'filter',
				],
			]
		);


		$this->end_controls_section();
	}

	protected function style_sections() {
		$this->start_controls_section(
			'section_style_content',
			[
				'label'     => __( 'Content', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'width',
			[
				'label' => __( 'Width', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					]
				],
				'default' => [],
				'selectors' => [
					'{{WRAPPER}}' => 'min-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'icon_style_heading',
			[
				'label' => __( 'Icon', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-navigation-bar__icon' => 'color: {{VALUE}};transition: 0.4s;',
				],
			]
		);

		$this->add_control(
			'icon_hover_color',
			[
				'label' => __( 'Hover Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-navigation-bar-item:hover .glozin-navigation-bar__icon' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
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
					'{{WRAPPER}} .glozin-navigation-bar__icon' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .icon-type-image .glozin-navigation-bar__icon' => 'max-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'title_style_heading',
			[
				'label' => __( 'Title', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .glozin-navigation-bar__title',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-navigation-bar__title' => 'color: {{VALUE}};transition: 0.4s;',
				],
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label' => __( 'Hover Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-navigation-bar-item:hover .glozin-navigation-bar__title' => 'color: {{VALUE}}',
				],
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
					'{{WRAPPER}} .glozin-navigation-bar__title' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render icon box widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', [ 'glozin-navigation-bar-elementor', 'd-flex', 'align-items-center', 'justify-content-between' ] );
		$this->add_render_attribute( 'item', 'class', ['glozin-navigation-bar-item', 'text-center', 'icon-type-' . $settings['icon_type']] );
		$this->add_render_attribute( 'icon', 'class', [ 'glozin-navigation-bar__icon', 'fs-20', 'lh-1', 'd-inline-block', 'position-relative' ]);
		$this->add_render_attribute( 'title', 'class', ['glozin-navigation-bar__title', 'fs-13', 'fw-semibold', 'lh-1' ] );

		if ( $settings['action'] == 'account' ) {

			if ( ! is_user_logged_in() ) {
				$this->set_prop( 'modals', 'login' );
				$this->add_render_attribute( 'item', 'data-toggle', 'modal' );
				$this->add_render_attribute( 'item', 'data-target', 'login-modal' );
			} else {
				$this->set_prop( 'panels', 'account' );
				$this->add_render_attribute( 'item', 'data-toggle', 'off-canvas' );
				$this->add_render_attribute( 'item', 'data-target', 'account-panel' );

			}

			if( function_exists('wc_get_account_endpoint_url') && wc_get_account_endpoint_url( 'dashboard' ) ) { 
				$this->add_link_attributes( 'item', [
					'url'   => wc_get_account_endpoint_url( 'dashboard' ),
				] );
			}
			
		} else if ( $settings['action'] == 'cart' ) {
			$this->add_render_attribute( 'item', 'data-toggle', 'off-canvas' );
			$this->add_render_attribute( 'item', 'data-target', 'cart-panel' );
			$this->set_prop('panels', 'cart');
			if( function_exists('wc_get_page_permalink') && wc_get_page_permalink( 'cart' ) ) { 
				$this->add_link_attributes( 'item', [
					'url'   => wc_get_page_permalink( 'cart' ),
				] );
			}
			
		} else if ( $settings['action'] == 'filter' ) {
			$this->add_render_attribute( 'item', 'data-toggle', 'off-canvas' );
			$this->add_render_attribute( 'item', 'data-target', 'filter-sidebar-panel' );
			$this->set_prop('panels', 'filter');
		} else {
			if ( ! empty( $settings['custom_url']['url'] ) ) {
				$this->add_link_attributes( 'item', $settings['custom_url'] );
			}
		}

		echo '<div '. $this->get_render_attribute_string( 'wrapper' ) .'>';
			echo '<a '. $this->get_render_attribute_string( 'item' ) .'>';
				echo '<div '. $this->get_render_attribute_string( 'icon' ) .'>';
					if ( 'image' == $settings['icon_type'] ) {
						if( ! empty( $settings['image'] ) && ! empty( $settings['image']['url'] ) ) :
							$settings['image'] = $settings['image'];
							$settings['image_size'] = 'thumbnail';

							echo wp_kses_post( \Elementor\Group_Control_Image_Size::get_attachment_image_html( $settings ) );
						endif;
					} elseif ( 'external' == $settings['icon_type'] ) {
						echo $settings['icon_url'] ? sprintf( '<img alt="%s" src="%s">', esc_attr( $settings['title'] ), esc_url( $settings['icon_url'] ) ) : '';
					} else {
					if(!empty($settings['icon']['value'])) {
						echo '<span class="glozin-svg-icon">';
							Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] );
						echo '</span>';
						}
					}

					if ( $settings['counter'] == 'cart' ) {
						$counter = ! empty(WC()->cart) ? WC()->cart->get_cart_contents_count() : 0;
						echo '<span class="header-counter header-cart__counter">'. $counter .'</span>';
					}
					if ( $settings['counter'] == 'wishlist' && class_exists( '\WCBoost\Wishlist\Helper' ) ) {
						$counter = intval( \WCBoost\Wishlist\Helper::get_wishlist()->count_items() );
						$classes = $counter == 0 ? ' empty-counter' : '';
						echo '<span class="header-counter header-wishlist__counter'. $classes .'">'. $counter .'</span>';
					}
				echo '</div>';
				if( ! empty( $settings['title'] ) ) {
					echo '<div '. $this->get_render_attribute_string( 'title' ) .'>';
						echo wp_kses_post( $settings['title'] );
					echo '</div>';
				};
			echo '</a>';
		echo '</div>';
	}

	protected function set_prop( $prop, $value ) {
		if ( class_exists( '\Glozin\Theme' ) && method_exists( '\Glozin\Theme', 'set_prop' ) ) {
			return \Glozin\Theme::set_prop( $prop, $value );
		}
	}
	
}