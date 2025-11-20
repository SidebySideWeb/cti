<?php
namespace Glozin\Addons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Currency widget
 */
class Currencies extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'glozin-currencies';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Glozin] Currencies', 'glozin-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-price-list';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return ['glozin-addons'];
	}

	/**
	 * Get widget keywords.
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'currency', 'currencies', 'glozin-addons' ];
	}

	/**
	 * Register the widget styles.
	 *
	 * @access public
	 */
	public function get_style_depends() {
		return [ 'glozin-elementor-css' ];
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->section_style();
	}

	// Tab Style
	protected function section_style() {
		$this->start_controls_section(
			'section_style',
			[
				'label'     => __( 'Style', 'glozin-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'current_typography',
				'selector' => '{{WRAPPER}} .current',
			]
		);

		$this->add_control(
			'current_color',
			[
				'label' => __( 'Color', 'glozin-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .current' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Icon Size', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default' => [
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .current .glozin-svg-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render icon box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$this->add_render_attribute( 'wrapper', 'class', [ 'glozin-currency-elementor', 'glozin-currency-language-elementor', 'glozin-currency-language' ] );
		$this->add_render_attribute( 'wrapper', 'data-toggle', 'popover' );
		$this->add_render_attribute( 'wrapper', 'data-target', 'currency-popover' );
		$this->add_render_attribute( 'wrapper', 'data-device', 'mobile' );

		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<?php echo \Glozin\WooCommerce\Currency::currency_switcher(); ?>
		</div>
		<?php
	}
}