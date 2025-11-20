<?php
namespace Glozin\Addons\Elementor\Builder\Widgets;

use Glozin\Addons\Elementor\Builder\Current_Query_Renderer;
use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Products_Filter_Actived extends Widget_Base {

	public function get_name() {
		return 'glozin-products-filter-actived';
	}

	public function get_title() {
		return esc_html__( '[Glozin] Products Filter Actived', 'glozin-addons' );
	}

	public function get_icon() {
		return 'eicon-filter';
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'archive', 'product', 'filter', 'actived' ];
	}

	public function get_categories() {
		return [ 'glozin-addons-archive-product' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
            'section_style',
            [
                'label' => __( 'Style', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_responsive_control(
			'margin',
			[
				'label'      => __( 'Margin', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .catalog-toolbar__active-filters' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .catalog-toolbar__active-filters' => 'margin: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
            'filters_style',
            [
                'label' => __( 'Filters Actived', 'glozin-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_responsive_control(
			'filters_gap',
			[
				'label' => esc_html__( 'Gap', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', '%', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .catalog-toolbar__filters-actived' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'filter_items_style',
			[
				'label' => esc_html__( 'Filters', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'filters_typography',
				'selector' => '{{WRAPPER}} .catalog-toolbar__filters-actived .remove-filtered:not(:last-child)',
			]
		);

		$this->add_control(
			'filters_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .catalog-toolbar__filters-actived .remove-filtered:not(:last-child)' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'filters_hover_color',
			[
				'label'     => __( 'Hover Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .catalog-toolbar__filters-actived .remove-filtered:not(:last-child):hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'filters_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .catalog-toolbar__filters-actived .remove-filtered:not(:last-child)' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'filters_hover_background_color',
			[
				'label'     => __( 'Hover Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .catalog-toolbar__filters-actived .remove-filtered:not(:last-child):hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'filters_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .catalog-toolbar__filters-actived .remove-filtered:not(:last-child)' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'filters_hover_border_color',
			[
				'label'     => __( 'Hover Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .catalog-toolbar__filters-actived .remove-filtered:not(:last-child):hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'filters_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .catalog-toolbar__filters-actived .remove-filtered:not(:last-child)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .catalog-toolbar__filters-actived .remove-filtered:not(:last-child)' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'filters_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .catalog-toolbar__filters-actived .remove-filtered:not(:last-child)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .catalog-toolbar__filters-actived .remove-filtered:not(:last-child)' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'filters_spacing',
			[
				'label' => esc_html__( 'Spacing', 'glozin-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', '%', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .catalog-toolbar__filters-actived .remove-filtered:not(:last-child)' => 'padding-inline-end: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'filters_remove_style',
			[
				'label' => esc_html__( 'Clear All', 'glozin-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'filters_remove_typography',
				'selector' => '{{WRAPPER}} .catalog-toolbar__filters-actived .remove-filtered-all',
			]
		);

		$this->add_control(
			'filters_remove_color',
			[
				'label'     => __( 'Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .catalog-toolbar__filters-actived .remove-filtered-all' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'filters_remove_hover_color',
			[
				'label'     => __( 'Hover Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .catalog-toolbar__filters-actived .remove-filtered-all:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'filters_remove_background_color',
			[
				'label'     => __( 'Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .catalog-toolbar__filters-actived .remove-filtered-all' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'filters_remove_hover_background_color',
			[
				'label'     => __( 'Hover Background Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .catalog-toolbar__filters-actived .remove-filtered-all:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'filters_remove_border_color',
			[
				'label'     => __( 'Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .catalog-toolbar__filters-actived .remove-filtered-all' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'filters_remove_hover_border_color',
			[
				'label'     => __( 'Hover Border Color', 'glozin-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .catalog-toolbar__filters-actived .remove-filtered-all:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'filters_remove_padding',
			[
				'label'      => __( 'Padding', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .catalog-toolbar__filters-actived .remove-filtered-all' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .catalog-toolbar__filters-actived .remove-filtered-all' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'filters_remove_border_radius',
			[
				'label'      => __( 'Border Radius', 'glozin-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .catalog-toolbar__filters-actived .remove-filtered-all' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}} .catalog-toolbar__filters-actived .remove-filtered-all' => 'border-radius: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		if ( \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
			?>
			<div class="catalog-toolbar__active-filters actived">
				<div class="catalog-toolbar__filters-actived d-flex flex-wrap align-items-center gap-10 active" data-clear-text="<?php esc_attr_e( 'Clear all', 'glozin' ); ?>">
					<a href="#" class="remove-filtered" data-name="filter_color" data-value="color" rel="nofollow" aria-label="Remove filter"><?php esc_html_e( 'Color', 'glozin' ); ?></a>
					<a href="#" class="remove-filtered remove-filtered-all"><?php esc_html_e( 'Clear all', 'glozin' ); ?></a>
				</div>
			</div>
			<?php
			return;
		} else {
			global $wp_query;
			$shortcode = new Current_Query_Renderer( $wp_query->query, 'current_query' );
			$total = $shortcode->get_query_results()->total;
		}

		$filter_class = ! isset( $_GET['filter'] ) ? ' hidden' : '';
		?>
		<div class="catalog-toolbar__active-filters<?php echo esc_attr( $filter_class ); ?>">
			<div class="catalog-toolbar__filters-actived d-flex flex-wrap align-items-center gap-10" data-clear-text="<?php echo esc_attr__( 'Clear all', 'glozin' ); ?>"></div>
		</div>
		<?php
	}
}
