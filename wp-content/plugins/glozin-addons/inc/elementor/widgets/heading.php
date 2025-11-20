<?php
namespace Glozin\Addons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Glozin\Addons\Helper;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor heading widget.
 *
 * Elementor widget that displays an eye-catching headlines.
 *
 * @since 1.0.0
 */
class Heading extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * Retrieve heading widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-heading';
	}

	/**
	 * Get widget title
	 *
	 * Retrieve heading widget title
	 *
	 * @return string Widget title
	 */
	public function get_title() {
		return __( '[Glozin] Heading', 'glozin-addons' );
	}

	/**
	 * Get widget icon
	 *
	 * Retrieve heading widget icon
	 *
	 * @return string Widget icon
	 */
	public function get_icon() {
		return 'eicon-t-letter';
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
		return [ 'heading', 'title', 'text', 'glozin-addons' ];
	}

	/**
	 * Register heading widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => __( 'Title', 'glozin-addons' ),
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
				'default' => __( 'Add Your Heading Text Here', 'glozin-addons' ),
			]
		);

		$this->add_control(
			'size',
			[
				'label' => __( 'Size', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __( 'Default', 'glozin-addons' ),
					'normal'  => __( 'Normal', 'glozin-addons' ),
					'medium'  => __( 'Medium', 'glozin-addons' ),
					'large'   => __( 'Large', 'glozin-addons' ),
				],
			]
		);

		$this->add_control(
			'title_size',
			[
				'label' => __( 'HTML Tag', 'glozin-addons' ),
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
				'default' => 'h2',
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'                => esc_html__( 'Horizontal Position', 'glozin-addons' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'options'              => [
					'flex-start'   => [
						'title' => esc_html__( 'Left', 'glozin-addons' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'glozin-addons' ),
						'icon'  => 'eicon-h-align-center',
					],
					'flex-end'  => [
						'title' => esc_html__( 'Right', 'glozin-addons' ),
						'icon'  => 'eicon-h-align-right',
					],
					'space-between' => [
						'title' => __( 'Between', 'glozin-addons' ),
						'icon' => 'eicon-justify-space-between-h',
					],
				],
				'default'     => '',
				'selectors'            => [
					'{{WRAPPER}} .glozin-heading' => 'justify-content: {{VALUE}}; text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// Style before Title
		$this->start_controls_section(
			'section_style_content',
			[
				'label'     => __( 'Content', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_control(
			'heading_title',
			[
				'label'     => esc_html__( 'Title', 'glozin-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator'  => 'before',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .glozin-heading' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'selector' => '{{WRAPPER}} .glozin-heading',
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

		if ( empty( $settings['title'] ) ) {
			return;
		}

		$this->add_render_attribute( 'wrapper', 'class', [ 'glozin-heading-elementor' ] );

		$this->add_render_attribute( 'title', 'class', [ 'glozin-heading', 'd-flex', 'align-items-center', 'my-0', 'glozin-heading--' . $settings['size'] ] );
		$this->add_render_attribute( 'icon', 'class', 'glozin-heading__icon' );

		$this->add_inline_editing_attributes( 'title' );

		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
				<<?php echo esc_attr( $settings['title_size'] ); ?> <?php echo $this->get_render_attribute_string( 'title' ); ?>>
					<?php echo wp_kses_post( do_shortcode($settings['title']) ); ?>
				</<?php echo esc_attr( $settings['title_size'] ); ?>>
			</div>
		<?php
	}
}