<?php
namespace Glozin\Addons\Elementor\Widgets;

use Elementor\Conditions;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Navigation Menu widget
 */
class Navigation_Menu extends Widget_Base {
/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-navigation-menu';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Glozin] Navigation Menu', 'glozin-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-nav-menu';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return ['glozin-addons-footer'];
	}

	/**
	 * Get widget keywords.
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @return array Widget keywords.
	 */
   	public function get_keywords() {
	   return [ 'navigation-menu', 'menu', 'glozin-addons' ];
   	}

	/**
	 * Scripts
	 *
	 * @return void
	 */
	public function get_script_depends() {
		return [
			'glozin-elementor-widgets',
		];
	}

	/**
	 * Styles
	 *
	 * @return void
	 */
	public function get_style_depends() {
		return [ 'glozin-elementor-css' ];
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
			'section_navigation_menu',
			[ 'label' => __( 'Navigation Menu', 'glozin-addons' ) ]
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
				'default' => __( 'Add Your Text Here', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'title_size',
			[
				'label' => __( 'Title HTML Tag', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h4',
			]
		);

		$this->add_control(
			'menu_type',
			[
				'label' => __( 'Type', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'wordpress',
				'options' => [
					'wordpress'  => esc_html__( 'WordPress Menu', 'glozin-addons' ),
					'custom' => esc_html__( 'Custom', 'glozin-addons' ),
				],
			]
		);

		$this->add_control(
			'list_menu',
			[
				'label' => __( 'Select Menu', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => \Glozin\Addons\Helper::get_navigation_bar_get_menus(),
				'condition' => [
					'menu_type' => 'wordpress',
				],
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'title_menu',
			[
				'label'   => esc_html__( 'Text', 'glozin-addons' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Menu item', 'glozin-addons' ),
			]
		);

		$repeater->add_control(
			'link_menu', [
				'label'         => esc_html__( 'Link', 'glozin-addons' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => esc_html__( 'https://your-link.com', 'glozin-addons' ),
				'show_external' => true,
				'default'       => [
					'url'         => '#',
					'is_external' => false,
					'nofollow'    => false,
				],
			]
		);

		$this->add_control(
			'menu_items',
			[
				'label'         => esc_html__( 'Menu Items', 'glozin-addons' ),
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $repeater->get_controls(),
				'default'       => [
					[
						'title_menu' => esc_html__( 'Menu item 1', 'glozin-addons' ),
						'link_menu' => '#',
					],
					[
						'title_menu' => esc_html__( 'Menu item 2', 'glozin-addons' ),
						'link_menu' => '#',
					],
					[
						'title_menu' => esc_html__( 'Menu item 3', 'glozin-addons' ),
						'link_menu' => '#',
					],
				],
				'prevent_empty' => false,
				'condition' => [
					'menu_type' => 'custom',
				],
				'title_field'   => '{{{ title_menu }}}',
			]
		);

		$this->end_controls_section();
	}

	protected function style_sections(){
		$this->start_controls_section(
			'style_navigation_menu',
			[
				'label'     => __( 'Navigation Menu', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'toggle_menu',
			[
				'label'        => __( 'Toggle Menu on Mobile', 'glozin-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_off'    => __( 'Off', 'glozin-addons' ),
				'label_on'     => __( 'On', 'glozin-addons' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'heading_icon',
			[
				'label' => __( 'Arrow Icon', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'toggle_menu' => 'yes',
				],
			]
		);

		$this->add_control(
			'style_icons',
			[
				'label' => __( 'Icon Normal', 'glozin-addons' ),
				'type' => Controls_Manager::ICONS,
				'default' => [],
				'condition' => [
					'toggle_menu' => 'yes',
				],
			]
		);

		$this->add_control(
			'style_icons_active',
			[
				'label' => __( 'Icon Active', 'glozin-addons' ),
				'type' => Controls_Manager::ICONS,
				'default' => [],
				'condition' => [
					'toggle_menu' => 'yes',
					'style_icons[value]!' => '',
				],
			]
		);

		$this->add_control(
			'style_title',
			[
				'label' => __( 'Title', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-navigation-menu__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'selector' => '{{WRAPPER}} .glozin-navigation-menu__title',
			]
		);

		$this->add_responsive_control(
			'spacing',
			[
				'label' => __( 'Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-navigation-menu__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'style_menu',
			[
				'label' => __( 'Menu', 'glozin-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'spacing_menu',
			[
				'label' => __( 'Gap', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .glozin-navigation-menu__menu' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography_menu',
				'selector' => '{{WRAPPER}} .glozin-navigation-menu__menu li a',
			]
		);

		$this->add_control(
			'menu_color',
			[
				'label' => __( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-navigation-menu__menu a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'menu_hover_color',
			[
				'label' => __( 'Hover Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-navigation-menu__menu a:hover' => 'color: {{VALUE}};',
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

		$this->add_render_attribute( 'wrapper', 'class', [ 'glozin-navigation-menu-element' ] );
		$this->add_render_attribute( 'title', 'class', [ 'glozin-navigation-menu__title', 'fw-semibold', 'mt-0', 'mb-20', 'mb-md-25', 'h6' ] );
		$this->add_inline_editing_attributes( 'title' );
		$toggle_classes = 'glozin-navigation-menu';
		$collapse_icon = '';

		if ( $settings['toggle_menu'] == 'yes' ) {
			$this->add_render_attribute( 'wrapper', 'class', [ 'glozin-toggle-mobile__wrapper' ] );
			$this->add_render_attribute( 'title', 'class', [ 'glozin-toggle-mobile__title', 'd-flex', 'align-items-center', 'justify-content-between', 'position-relative' ] );
			$toggle_classes .= ' glozin-toggle-mobile__content';

			if ( ! empty( $settings['style_icons']['value'] ) ) {
				$collapse_icon = '<span class="glozin-svg-icon glozin-navigation-menu__icon glozin-navigation-menu__icon-default d-none-md">';
				$collapse_icon .= $this->get_icon_html( $settings['style_icons'], [ 'aria-hidden' => 'true' ] );
				$collapse_icon .= '</span>';

				if ( ! empty( $settings['style_icons_active']['value'] ) ) {
					$collapse_icon .= '<span class="glozin-svg-icon glozin-navigation-menu__icon glozin-navigation-menu__icon-active d-none-md">';
					$collapse_icon .= $this->get_icon_html( $settings['style_icons_active'], [ 'aria-hidden' => 'true' ] );
					$collapse_icon .= '</span>';
				}
			} else {
				$collapse_icon = '<span class="gz-collapse-icon d-inline-flex align-items-center justify-content-center position-relative"></span>';
			}
		}

		$menu_items = $settings['menu_items'];
		?>
			<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
				<?php
				if ( ! empty( $settings['title'] ) ) {
					printf( '<%1$s %2$s>%3$s%4$s</%1$s>', $settings['title_size'], $this->get_render_attribute_string( 'title' ), $settings['title'], $collapse_icon );
				}
				?>

				<?php
					if ( $settings['menu_type'] == "wordpress" ) {
						if ( empty( $settings['list_menu'] ) ) {
							return;
						}

						wp_nav_menu( array(
							'theme_location' 	=> '__no_such_location',
							'menu'           	=> $settings['list_menu'],
							'container'      	=> 'nav',
							'container_class'   => $toggle_classes,
							'menu_class'     	=> 'nav-menu glozin-navigation-menu__menu d-flex flex-wrap flex-column gap-9 list-unstyled',
							'depth'          	=> 1,
						) );
					} else {
						$menu = '<nav class="'. $toggle_classes .'">';
						$menu .= '<ul class="nav-menu glozin-navigation-menu__menu d-flex flex-wrap flex-column gap-9 list-unstyled">';
						if ( ! empty ( $menu_items ) ) {
							foreach ( $menu_items as $index => $item ) {
								$link_key 	  = $this->get_repeater_setting_key( 'link', 'slide', $index );
								$this->add_link_attributes( $link_key, $item['link_menu'] );

								if ( !empty( $item['title_menu'] ) ) {
									$menu .= '<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-home menu-item-' . $item['_id'] . '">';
										$menu .= '<a '. $this->get_render_attribute_string( $link_key ) .'>';
										$menu .= $item['title_menu'];
										$menu .= '</a>';
									$menu .= '</li>';
								}
							}
						}
							$menu .= '</ul>';
						$menu .= '</nav>';
						echo $menu;
					}
				?>
			</div>
		<?php

	}

	/**
	 * @param array $icon
	 * @param array $attributes
	 * @param $tag
	 * @return bool|mixed|string
	 */
	function get_icon_html( array $icon, array $attributes, $tag = 'i' ) {
		/**
		 * When the library value is svg it means that it's a SVG media attachment uploaded by the user.
		 * Otherwise, it's the name of the font family that the icon belongs to.
		 */
		if ( 'svg' === $icon['library'] ) {
			$output = Icons_Manager::render_uploaded_svg_icon( $icon['value'] );
		} else {
			$output = Icons_Manager::render_font_icon( $icon, $attributes, $tag );
		}
		return $output;
	}
}