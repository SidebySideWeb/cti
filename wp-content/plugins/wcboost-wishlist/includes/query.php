<?php
/**
 * Handle wishlist query and rewrite rules
 *
 * @version 1.0.0
 *
 * @package WCBoost\Wishlist
 */

namespace WCBoost\Wishlist;

defined( 'ABSPATH' ) || exit;

/**
 * Class \WCBoost\Wishlist\Query
 */
class Query {

	/**
	 * Query vars to add to wp.
	 *
	 * @var array
	 */
	private $query_vars = [];

	/**
	 * The wishlist instance.
	 *
	 * @var \WCBoost\Wishlist\Wishlist
	 */
	public $wishlist;

	/**
	 * Constructor for the query class. Hooks in methods.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'add_endpoints' ] );
		add_action( 'init', [ $this, 'add_wishlist_page_rewrite_rules' ] );

		if ( ! is_admin() ) {
			add_filter( 'query_vars', [ $this, 'add_query_vars' ] );
			add_action( 'parse_request', [ $this, 'parse_request' ], 0 );
		}

		$this->query_vars = [
			'wishlist_token' => 'wishlist_token',
			'edit-wishlist'  => get_option( 'wcboost_wishlist_edit_endpoint', 'edit-wishlist' ),
		];
	}

	/**
	 * Get query vars.
	 *
	 * @return array
	 */
	public function get_query_vars() {
		return apply_filters( 'wcboost_wishlist_query_vars', $this->query_vars );
	}

	/**
	 * Add rewrite rules for wishlist page.
	 */
	public function add_wishlist_page_rewrite_rules() {
		// Use get_option to ensure always get the page of the default language (if multilingual is enabled).
		$wishlist_page_id = get_option( 'wcboost_wishlist_page_id' );

		if ( empty( $wishlist_page_id ) ) {
			return;
		}

		$wishlist_page = get_post( $wishlist_page_id );
		$wishlist_slug = $wishlist_page ? urldecode( $wishlist_page->post_name ) : false;

		if ( empty( $wishlist_slug ) ) {
			return;
		}

		$this->add_rewrite_rules( $wishlist_slug );
		$this->maybe_flush_rewrite_rules( $wishlist_slug );
	}

	/**
	 * Add rewrite rules for wishlists
	 *
	 * @param  string $base Wishilist page slug.
	 * @return void
	 */
	public function add_rewrite_rules( $base ) {
		if ( empty( $base ) || ! is_string( $base ) ) {
			return;
		}

		$rewrite_rules = $this->get_rewrite_rules( $base );

		if ( empty( $rewrite_rules ) ) {
			return;
		}

		foreach ( $rewrite_rules as $regex => $query ) {
			add_rewrite_rule( $regex, $query, 'top' );
		}
	}

	/**
	 * Flush rewrite rules when wishlist rules are not updated.
	 *
	 * Use a hash-based caching mechanism that stores a hash of the rewrite rules.
	 * This prevents unnecessary comparisons of large arrays on every page load.
	 *
	 * @since 1.1.6
	 *
	 * @param string $base Wishlist page slug.
	 * @return void
	 */
	public function maybe_flush_rewrite_rules( $base ) {
		$rewrite_rules = $this->get_rewrite_rules( $base );

		// Cache the rewrite rules hash to avoid unnecessary comparisons.
		$rules_hash  = md5( serialize( $rewrite_rules ) );
		$stored_hash = get_option( 'wcboost_wishlist_rewrite_rules_hash' );

		if ( $stored_hash !== $rules_hash ) {
			$current_rules = get_option( 'rewrite_rules' );

			// Only flush if there are actual differences
			if ( ! is_array( $current_rules ) || ! empty( array_diff( array_keys( $rewrite_rules ), array_keys( $current_rules ) ) ) ) {
				flush_rewrite_rules();
			}

			// Update the cache.
			update_option( 'wcboost_wishlist_rewrite_rules_hash', $rules_hash, false );
		}
	}

	/**
	 * Get the wishlist rewrite rules
	 *
	 * @since  1.1.6
	 *
	 * @param  string $base Wishlist page slug.
	 * @return array
	 */
	private function get_rewrite_rules( $base ) {
		$rewrite_rules = [];
		$query_vars    = $this->get_query_vars();

		// Edit wishlist rewrite rule.
		if ( ! empty( $query_vars['edit-wishlist'] ) ) {
			$rewrite_rules['^' . $base . '/' . $query_vars['edit-wishlist'] . '(/(.*))?/?$'] = 'index.php?pagename=' . $base . '&wishlist_token=$matches[2]&' . $query_vars['edit-wishlist'] . '=$matches[2]';
		}

		// Paged rewrite rule.
		$rewrite_rules['^' . $base . '(/(.*))?/page/([0-9]{1,})/?$'] = 'index.php?pagename=' . $base . '&wishlist_token=$matches[2]&paged=$matches[3]';

		// View wishlist rewrite rule have to be last because it is the default.
		$rewrite_rules['^' . $base . '(/(.*))?/?$'] = 'index.php?pagename=' . $base . '&wishlist_token=$matches[2]';

		return apply_filters( 'wcboost_wishlist_rewrite_rules', $rewrite_rules, $base );
	}

	/**
	 * Add endpoints for query vars.
	 *
	 * @since 1.1.6
	 */
	public function add_endpoints() {
		foreach ( $this->get_query_vars() as $key => $var ) {
			if ( 'wishlist_token' == $key ) {
				continue;
			}

			if ( ! empty( $var ) ) {
				add_rewrite_endpoint( $var, EP_PAGES );
			}
		}
	}

	/**
	 * Add public query vars for wishlist page.
	 *
	 * @param array $vars Query vars.
	 * @return array
	 */
	public function add_query_vars( $vars ) {
		foreach ( $this->get_query_vars() as $key => $var ) {
			$vars[] = $key;
		}

		return $vars;
	}

	/**
	 * Parse the request and look for query vars
	 */
	public function parse_request() {
		global $wp;

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		foreach ( $this->get_query_vars() as $key => $var ) {
			if ( isset( $_GET[ $var ] ) ) {
				$wp->query_vars[ $key ] = sanitize_text_field( wp_unslash( $_GET[ $var ] ) );
			} elseif ( isset( $wp->query_vars[ $var ] ) ) {
				$wp->query_vars[ $key ] = $wp->query_vars[ $var ];
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Get the default wishlist of the current user
	 */
	protected function read_default_wishlist() {
		$default_wishlist_id = \WC_Data_Store::load( 'wcboost_wishlist' )->get_default_wishlist_id();

		if ( $default_wishlist_id ) {
			$this->wishlist = new Wishlist( $default_wishlist_id );
		}

		if ( empty( $this->wishlist ) ) {
			$this->wishlist = new Wishlist();
			$this->wishlist->set_is_default( true );
		}
	}

	/**
	 * Get wishlist instance.
	 * If no wishlist ID is passed, the default wishlist will be returned.
	 *
	 * @param int|string $wishlist_id Wishlist id or token.
	 * @return \WCBoost\Wishlist\Wishlist
	 */
	public function get_wishlist( $wishlist_id = 0 ) {
		// Ensure the default wishlist is always exists.
		if ( empty( $this->wishlist ) ) {
			$this->read_default_wishlist();
		}

		if ( ! $wishlist_id ) {
			return $this->wishlist;
		}

		if ( $this->wishlist->get_wishlist_id() == $wishlist_id ) {
			return $this->wishlist;
		}

		return new Wishlist( $wishlist_id );
	}

	/**
	 * Get all wishlits of current user
	 *
	 * @return array
	 */
	public function get_user_wishlists() {
		$wishlist_ids = \WC_Data_Store::load( 'wcboost_wishlist' )->get_wishlist_ids();
		$wishlists    = [];

		while ( count( $wishlist_ids ) ) {
			$id          = array_pop( $wishlist_ids );
			$wishlists[] = new Wishlist( $id );
		}

		return $wishlists;
	}

	/**
	 * Get the wishlish endpoint URL
	 *
	 * @param string $endpoint Endpoint name.
	 * @param string $value Endpoint value.
	 *
	 * @return string
	 */
	public function get_endpoint_url( $endpoint, $value = '' ) {
		$wishlist_url = wc_get_page_permalink( 'wishlist' );
		$query_vars = $this->get_query_vars();
		$endpoint   = ! empty( $query_vars[ $endpoint ] ) ? $query_vars[ $endpoint ] : $endpoint;

		if ( get_option( 'permalink_structure' ) ) {
			if ( strstr( $wishlist_url, '?' ) ) {
				$query_string = '?' . wp_parse_url( $wishlist_url, PHP_URL_QUERY );
				$wishlist_url = current( explode( '?', $wishlist_url ) );
			} else {
				$query_string = '';
			}

			$url = trailingslashit( $wishlist_url );

			if ( $value ) {
				$url .= 'wishlist-token' == $endpoint || 'wishlist_token' == $endpoint ? user_trailingslashit( $value ) : trailingslashit( $endpoint ) . user_trailingslashit( $value );
			} else {
				$url .= user_trailingslashit( $endpoint );
			}

			$url .= $query_string;
		} else {
			$url = add_query_arg( $endpoint, $value, $wishlist_url );
		}

		return apply_filters( 'wcboost_wishlist_get_endpoint_url', $url, $endpoint, $value, $this );
	}
}
