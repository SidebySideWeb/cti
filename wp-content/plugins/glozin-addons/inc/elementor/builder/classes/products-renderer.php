<?php
namespace Glozin\Addons\Elementor\Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Products_Renderer extends Base_Products_Renderer {

	private $settings = [];

	public function __construct( $settings = [], $type = 'products' ) {
		$columns_default = get_option('woocommerce_catalog_columns', 4);
		$columns = isset( $settings['show_default_view'] ) && $settings['show_default_view'] == 'list' ? 1 : get_option('woocommerce_catalog_columns', 4);

		if ( ! \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
			if( isset( $_COOKIE['catalog_view'] ) ) {
				$columns = $_COOKIE['catalog_view'] == 'list' ? 1 : $columns;
			}
		}
		
		if( isset( $_GET['view'] ) ) {
			$columns = $_GET['view'] == 'list' ? 1 : $columns;
		}
		
		$rows = get_option('woocommerce_catalog_rows', 4);
		$this->settings = $settings;
		$this->type = $type;
		$this->attributes = $this->parse_attributes( [
			'columns'        => $columns,
			'rows'           => $rows,
			'limit' 		 => intval( $columns ) == 1 ? ( intval( $columns_default ) * intval( $rows ) ) : ( intval( $columns ) * intval( $rows ) ),
			'paginate'       => $settings['paginate'],
			'cache'          => false,
		] );
		$this->query_args = $this->parse_query_args();
	}

	public function parse_query_args() {
		$query_args = [
			'post_type' => 'product',
			'post_status' => 'publish',
			'ignore_sticky_posts' => true,
			'no_found_rows' => false === wc_string_to_bool( $this->attributes['paginate'] ),
			'orderby' => '',
			'order' => '',
		];

		$query_args['meta_query'] = WC()->query->get_meta_query();
		$query_args['tax_query'] = [];

		$ordering_args = WC()->query->get_catalog_ordering_args();

		$query_args['orderby'] = $ordering_args['orderby'];
		$query_args['order'] = $ordering_args['order'];
		if ( $ordering_args['meta_key'] ) {
			$query_args['meta_key'] = $ordering_args['meta_key'];
		}

		// fallback to the widget's default settings in case settings was left empty:
		$rows = $this->attributes['rows'];
		$columns = $this->attributes['columns'];
		$query_args['posts_per_page'] = isset( $this->attributes['limit'] ) ? intval( $this->attributes['limit'] ) : intval( $columns * $rows );

		$this->set_pagination_args( $query_args );

		$this->set_visibility_query_args( $query_args );

		$query_args = apply_filters( 'woocommerce_shortcode_products_query', $query_args, $this->attributes, $this->type );

		// Always query only IDs.
		$query_args['fields'] = 'ids';

		return $query_args;
	}


	protected function set_pagination_args( &$query_args ) {
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
		if ( 'yes' !== $this->settings['paginate'] ) {
			return;
		}

		$this->set_paged_args( $query_args );

	}

	protected function set_paged_args( &$query_args ) {
		$page = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );

		if ( 1 === $page ) {
			return;
		}

		$query_args['paged'] = $page;
	}

	/**
	 * Run the query and return an array of data, including queried ids and pagination information.
	 *
	 * @since  3.3.0
	 * @return object Object with the following props; ids, per_page, found_posts, max_num_pages, current_page
	 */
	public function get_query_results() {
		$transient_name    = $this->get_transient_name();
		$transient_version = \WC_Cache_Helper::get_transient_version( 'product_query' );
		$cache             = wc_string_to_bool( $this->attributes['cache'] ) === true;
		$transient_value   = $cache ? get_transient( $transient_name ) : false;

		if ( isset( $transient_value['value'], $transient_value['version'] ) && $transient_value['version'] === $transient_version ) {
			$results = $transient_value['value'];
		} else {
			$query = new \WP_Query( $this->query_args );

			$paginated = ! $query->get( 'no_found_rows' );

			$results = (object) array(
				'ids'          => wp_parse_id_list( $query->posts ),
				'total'        => $paginated ? (int) $query->found_posts : count( $query->posts ),
				'total_pages'  => $paginated ? (int) $query->max_num_pages : 1,
				'per_page'     => (int) $query->get( 'posts_per_page' ),
				'current_page' => $paginated ? (int) max( 1, $query->get( 'paged', 1 ) ) : 1,
			);

			if ( $cache ) {
				$transient_value = array(
					'version' => $transient_version,
					'value'   => $results,
				);
				set_transient( $transient_name, $transient_value, DAY_IN_SECONDS * 30 );
			}
		}

		// Remove ordering query arguments which may have been added by get_catalog_ordering_args.
		WC()->query->remove_ordering_args();

		/**
		 * Filter shortcode products query results.
		 *
		 * @since 4.0.0
		 * @param stdClass $results Query results.
		 * @param WC_Shortcode_Products $this WC_Shortcode_Products instance.
		 */
		return apply_filters( 'woocommerce_shortcode_products_query_results', $results, $this );
	}
}
