<?php
namespace Glozin\Addons\Elementor\Widgets;

use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Image_Size;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Stores Location widget.
 *
 * Elementor widget that displays an eye-catching headlines.
 *
 * @since 1.0.0
 */
class Store_Locations extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve Stores Location widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-store-locations';
	}

	/**
	 * Get widget title
	 *
	 * Retrieve Stores Location widget title
	 *
	 * @return string Widget title
	 */
	public function get_title() {
		return __( '[Glozin] Store Locations', 'glozin-addons' );
	}

	/**
	 * Get widget icon
	 *
	 * Retrieve TeamMemberGrid widget icon
	 *
	 * @return string Widget icon
	 */
	public function get_icon() {
		return 'eicon-map-pin';
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
	 * Get widget keywords.
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'stores', 'location', 'locations', 'map', 'glozin-addons' ];
	}

    /**
	 * Script
	 *
	 * @return void
	 */
	public function get_script_depends() {
		return [
			'mapbox',
			'mapboxgl',
			'mapbox-sdk',
			'glozin-store-locations-widget',
		];
	}

	public function get_style_depends() {
		return [
			'mapbox',
			'mapboxgl',
			'glozin-store-locations-css',
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
		$this->section_content();
		$this->section_style();
	}

	protected function section_content() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'map_type',
			[
				'label'   => esc_html__( 'Map Type', 'glozin-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'google' => esc_html__( 'Google Map', 'glozin-addons' ),
					'mapbox'  => esc_html__( 'Mapbox', 'glozin-addons' ),
				],
				'default' => 'google',
			]
		);

		$this->add_control(
			'access_token',
			[
				'label'       => esc_html__( 'Access Token', 'glozin-addons' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Enter your access token', 'glozin-addons' ),
				'label_block' => true,
				'description' => sprintf(__('Please go to <a href="%s" target="_blank">Maps Box APIs</a> to get a key', 'glozin-addons'), esc_url('https://www.mapbox.com')),
				'condition' => [
					'map_type' => 'mapbox',
				],
			]
		);

		$this->add_control(
			'marker_heading',
			[
				'label' => esc_html__( 'Marker', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'map_type' => 'mapbox',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'marker_image',
				// Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
				'default'   => 'full',
				'separator' => 'none',
				'condition' => [
					'map_type' => 'mapbox',
				],
			]
		);

		$this->add_control(
			'marker_image',
			[
				'label'   => esc_html__( 'Choose Image', 'glozin-addons' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => wc_placeholder_img_src(),
				],
				'condition' => [
					'map_type' => 'mapbox',
				],
			]
		);

		$repeater = new Repeater();

        $repeater->add_control(
			'title', [
				'label' => esc_html__( 'Store title', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
			]
		);

		$repeater->add_control(
			'address',
			[
				'label' => esc_html__( 'Location', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
					],
				],
				'ai' => [
					'active' => false,
				],
				'placeholder' => esc_html__( 'London Eye, London, United Kingdom', 'glozin-addons' ),
				'default' => esc_html__( 'London Eye, London, United Kingdom', 'glozin-addons' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'phone', [
				'label' => esc_html__( 'Phone', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'locations',
			[
				'label' => esc_html__( 'Locations', 'glozin-addons' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default' => [
					[
                        'title' => esc_html__( 'Store Title', 'glozin-addons' ),
                        'location' => esc_html__( 'London Eye, London, United Kingdom', 'glozin-addons' ),
                        'phone' => esc_html__( '(+00) 123 4567', 'glozin-addons' ),
					],
					[
                        'title' => esc_html__( 'Store Title', 'glozin-addons' ),
                        'location' => esc_html__( 'London Eye, London, United Kingdom', 'glozin-addons' ),
                        'phone' => esc_html__( '(+00) 123 4567', 'glozin-addons' ),
					],
					[
                        'title' => esc_html__( 'Store Title', 'glozin-addons' ),
                        'location' => esc_html__( 'London Eye, London, United Kingdom', 'glozin-addons' ),
                        'phone' => esc_html__( '(+00) 123 4567', 'glozin-addons' ),
					],
				],
			]
		);

        $this->add_control(
			'zoom',
			[
				'label' => esc_html__( 'Zoom', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 12,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'label' => esc_html__( 'Height', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 40,
						'max' => 1440,
					],
				],
				'size_units' => [ 'px', 'em', 'rem', 'vh', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .glozin-store-locations' => '--gz-store-locations-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'mode',
			[
				'label'       => esc_html__( 'Mode', 'glozin-addons' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'streets-v11' 	=> esc_html__( 'Streets', 'glozin-addons' ),
					'light-v10' 	=> esc_html__( 'Light', 'glozin-addons' ),
					'dark-v10'  	=> esc_html__( 'Dark', 'glozin-addons' ),
					'outdoors-v11'  => esc_html__( 'Outdoors', 'glozin-addons' ),
				],
				'default'     => 'light-v10',
				'condition' => [
					'map_type' => 'mapbox',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function section_style() {
		// Style
		$this->start_controls_section(
			'section_style',
			[
				'label'     => __( 'Content', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label'      => __( 'Map Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .glozin-store-locations' => '--gz-rounded-iframe: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .glozin-store-locations' => '--gz-rounded-iframe: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'mapbox_heading',
			[
				'label' => esc_html__( 'Mapbox', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'map_type' => 'mapbox',
				],
			]
		);

		$this->add_control(
			'color_1',
			[
				'label'     => esc_html__( 'Color water', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'map_type' => 'mapbox',
				],
			]
		);

		$this->add_control(
			'color_2',
			[
				'label'     => esc_html__( 'Color building', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'map_type' => 'mapbox',
				],
			]
		);

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

		$this->add_render_attribute( 'wrapper', 'class', [ 'glozin-store-locations', 'd-flex', 'flex-wrap', 'flex-nowrap-lg', 'glozin-store-locations-type--' . $settings['map_type'] ] );

		$html = '';

		$this->add_render_attribute( 'tabs', 'class', [ 'glozin-store-locations__tabs', 'w-100', 'position-relative' ] );
		$this->add_render_attribute( 'tabs_scroll', 'class', [ 'glozin-store-locations__scroll', 'd-flex', 'flex-column' ] );
		$this->add_render_attribute( 'tab', 'class', 'glozin-store-locations__tab' );
		$this->add_render_attribute( 'title', 'class', [ 'glozin-store-locations__title', 'fs-16', 'fw-semibold', 'text-dark', 'd-flex', 'align-items-center', 'mb-5' ] );
		$this->add_render_attribute( 'info', 'class', 'glozin-store-locations__info' );
		$this->add_render_attribute( 'span', 'class', 'fw-semibold' );

		$tabs = [];
		$tab  = [];

		$i = 0;
		foreach ( $settings['locations'] as $location ) :
			if ( empty( $location['address'] ) ) {
				return;
			}

			if( $i == 0 ) {
				$this->add_render_attribute( 'content', 'class', 'active' );
			} else {
				$this->remove_render_attribute( 'content' );
			}

			$this->add_render_attribute( 'content', 'class', [ 'glozin-store-locations__content', 'py-20', 'px-25' ]);

			$this->add_render_attribute( 'content', 'data-tab', 'tab-' . $i );

			if ( 'mapbox' === $settings['map_type'] ) {
				$this->add_render_attribute('info','data-latitude', '' );
				$this->add_render_attribute('info','data-longitude', '' );
			}

			$title   = ! empty( $location['title'] ) ? '<div '. $this->get_render_attribute_string( 'title' ) .'>' . wp_kses_post( $location['title'] ) .'</div>' : '';
			$address = ! empty( $location['address'] ) ? '<div '. $this->get_render_attribute_string( 'info' ) .'>'. wp_kses_post( $location['address'] ) .'</div>' : '';
			$phone   = ! empty( $location['phone'] ) ? '<div '. $this->get_render_attribute_string( 'info' ) .'>'. wp_kses_post( $location['phone'] ) .'</div>' : '';

			$tabs[] = sprintf(
							'<div %s>
								%s
								%s
								%s
							</div>',
							$this->get_render_attribute_string( 'content' ),
							$title,
							$address,
							$phone,
						);

			if ( 'mapbox' === $settings['map_type'] ) {
				$id     = uniqid( 'glozin-map-' );

				$image = $settings[ 'marker_image' ];
				$src = Group_Control_Image_Size::get_attachment_image_src( $image['id'], 'marker_image', $settings );

				// JS
				$color_1                     = $settings['color_1'] ? $settings['color_1'] : '#c8d7d4';
				$color_2                     = $settings['color_2'] ? $settings['color_2'] : '#f0f0ec';

				if( $i == 0 ) {
					$this->add_render_attribute( 'map_box', 'class', [ 'glozin-store-locations__mapbox','active' ] );
				} else {
					$this->remove_render_attribute( 'map_box' );
				}

				$output_map = array(
					'marker'  => $src,
					'token'   => $settings['access_token'],
					'zom'     => intval( $settings['zoom'] ),
					'color_1' => $color_1,
					'color_2' => $color_2,
					'local'   => $location['address'],
					'mode'    => $settings['mode'],
				);

				$this->add_render_attribute('map_box','data-map',wp_json_encode($output_map) );
				$this->add_render_attribute( 'map_box', 'data-tab', 'tab-' . $i );

				$tab[] = sprintf(
					'<div %s>
						<div class="glozin-map__content" id="%s"></div>
					</div>',
					$this->get_render_attribute_string( 'map_box' ),
					$id
				);
			} else {
				if ( 0 === absint( $settings['zoom']['size'] ) ) {
					$settings['zoom']['size'] = 10;
				}

				$params = [
					rawurlencode( $location['address'] ),
					absint( $settings['zoom']['size'] ),
				];

				$url = 'https://maps.google.com/maps?q=%1$s&amp;t=m&amp;z=%2$d&amp;output=embed&amp;iwloc=near';

				if( $i == 0 ) {
					$this->add_render_attribute( 'embed', 'class', 'active' );
				} else {
					$this->remove_render_attribute( 'embed' );
				}

				$this->add_render_attribute( 'embed', 'class', 'glozin-store-locations__embed' );
				$this->add_render_attribute( 'embed', 'data-tab', 'tab-' . $i );

				$tab[] = sprintf(
								'<div %s>
									<iframe loading="lazy"
											src="%s"
											title="%s"
											aria-label="%s"
											width="950"
											height="985"
									></iframe>
								</div>',
								$this->get_render_attribute_string( 'embed' ),
								esc_url( vsprintf( $url, $params ) ),
								esc_attr( $location['address'] ),
								esc_attr( $location['address'] )
							);
			}


			$i++;
		endforeach;

		$html = sprintf(
			'<div %s>
				<div %s>%s</div>
			</div>
			<div %s>
				%s
			</div>',
			$this->get_render_attribute_string( 'tabs' ),
			$this->get_render_attribute_string( 'tabs_scroll' ),
			implode( '', $tabs ),
			$this->get_render_attribute_string( 'tab' ),
			implode( '', $tab )
		);


        ?>
        <div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
            <?php echo $html; ?>
        </div>
    <?php
	}
}