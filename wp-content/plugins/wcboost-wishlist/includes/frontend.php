<?php
/**
 * Frontend hooks and functionality
 *
 * @version 1.0.0
 *
 * @package WCBoost\Wishlist
 */

namespace WCBoost\Wishlist;

defined( 'ABSPATH' ) || exit;

use WCBoost\Packages\Utilities\SingletonTrait;
use WCBoost\Wishlist\Helper;

/**
 * Class \WCBoost\Wishlist\Frontend
 */
class Frontend {

	use SingletonTrait;

	/**
	 * Class constructor
	 */
	protected function __construct() {
		add_action( 'wp', [ $this, 'template_hooks' ] );
		add_action( 'wp', [ $this, 'add_nocache_headers' ] );
		add_filter( 'wp_robots', [ $this, 'add_noindex_robots' ], 20 );

		add_action( 'init', [ $this, 'register_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	/**
	 * Template hooks
	 */
	public function template_hooks() {
		add_filter( 'body_class', [ $this, 'body_class' ] );

		// Wishlist page.
		add_action( 'wcboost_wishlist_before_wishlist', [ $this, 'print_notices' ], 5 );
		add_action( 'wcboost_wishlist_before_wishlist', [ $this, 'wishlist_header' ], 10 );

		add_action( 'wcboost_wishlist_main_content', [ $this, 'wishlist_content' ] );

		if ( wc_string_to_bool( get_option( 'wcboost_wishlist_page_show_title', 'no' ) ) ) {
			add_action( 'wcboost_wishlist_header', [ $this, 'wishlist_title' ] );
		}

		if ( wc_string_to_bool( get_option( 'wcboost_wishlist_page_show_desc', 'no' ) ) ) {
			add_action( 'wcboost_wishlist_header', [ $this, 'wishlist_description' ], 20 );
		}

		add_action( 'wcboost_wishlist_after_wishlist', [ $this, 'wishlist_footer' ] );

		if ( wc_string_to_bool( get_option( 'wcboost_wishlist_share', 'yes' ) ) ) {
			add_action( 'wcboost_wishlist_footer', [ $this, 'share_buttons' ] );
		}

		add_action( 'wcboost_wishlist_footer', [ $this, 'link_edit_wishlist' ], 50 );

		// Display button on single product page.
		if ( 'theme' != wc_get_theme_support( 'wishlist::single_button_position' ) ) {
			add_action( 'woocommerce_before_single_product', [ $this, 'display_wishlist_button' ] );
		}

		// Display button on the loop. Default is hidden.
		if ( 'theme' != wc_get_theme_support( 'wishlist::loop_button_position' ) ) {
			switch ( get_option( 'wcboost_wishlist_loop_button_position' ) ) {
				case 'before_add_to_cart':
					add_action( 'woocommerce_after_shop_loop_item', [ $this, 'loop_add_to_wishlist_button' ], 9 );
					break;

				case 'after_add_to_cart':
					add_action( 'woocommerce_after_shop_loop_item', [ $this, 'loop_add_to_wishlist_button' ], 11 );
					break;
			}
		}

		// Display the delete link on the wishlist edit page.
		add_action( 'wcboost_wishlist_edit_form_actions', [ $this, 'link_remove_wishlist' ] );

		add_filter( 'wcboost_wishlist_description', 'wpautop' );
		add_filter( 'wcboost_wishlist_description', 'wp_kses_post' );

		// Display buttons in the wishlist widget.
		add_action( 'wcboost_wishlist_widget_buttons', [ $this, 'widget_buttons' ], 10, 2 );

		// Change the page title and heading for the wishlist endpoints.
		add_filter( 'document_title_parts', [ $this, 'wishlist_endpoints_page_title' ] );
		add_filter( 'the_title', [ $this, 'wishlist_endpoints_page_heading' ] );
	}

	/**
	 * Add nocache headers.
	 * Prevent caching on the wishlist page
	 */
	public function add_nocache_headers() {
		if ( ! headers_sent() && Helper::is_wishlist() ) {
			wc_nocache_headers();
		}
	}

	/**
	 * Tell search engines stop indexing the URL with add-to-wishlist param.
	 *
	 * @param array $robots
	 * @return array
	 */
	public function add_noindex_robots( $robots ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_GET['add-to-wishlist'] ) ) {
			return $robots;
		}

		return wp_robots_no_robots( $robots );
	}

	/**
	 * Register scripts
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function register_scripts() {
		$plugin = Plugin::instance();
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_style( 'wcboost-wishlist', $plugin->plugin_url( '/assets/css/wishlist.css' ), [], WCBOOST_WISHLIST_VERSION );
		wp_register_script( 'wcboost-wishlist', $plugin->plugin_url( '/assets/js/wishlist' . $suffix . '.js' ), [ 'jquery' ], WCBOOST_WISHLIST_VERSION, true );
		wp_register_script( 'wcboost-wishlist-fragments', $plugin->plugin_url( '/assets/js/wishlist-fragments' . $suffix . '.js' ), [ 'jquery' ], WCBOOST_WISHLIST_VERSION, true );
	}

	/**
	 * Enqueue wishlist style and scripts
	 */
	public function enqueue_scripts() {
		if ( apply_filters( 'wcboost_wishlist_enqueue_frontend_style', true ) ) {
			wp_enqueue_style( 'wcboost-wishlist' );
		}

		if ( 'custom' == get_option( 'wcboost_wishlist_button_type' ) ) {
			wp_add_inline_style( 'wcboost-wishlist', $this->get_custom_button_css() );
		}

		wp_enqueue_script( 'wcboost-wishlist' );
		wp_localize_script( 'wcboost-wishlist', 'wcboost_wishlist_params', [
			'allow_adding_variations'     => get_option( 'wcboost_wishlist_allow_adding_variations' ),
			'wishlist_redirect_after_add' => get_option( 'wcboost_wishlist_redirect_after_add' ),
			'wishlist_url'                => apply_filters( 'wcboost_wishlist_add_to_wishlist_redirect', wc_get_page_permalink( 'wishlist' ), null ),
			'exists_item_behavior'        => get_option( 'wcboost_wishlist_exists_item_button_behaviour', 'view_wishlist' ),
			'i18n_link_copied_notice'     => esc_html__( 'The wishlist link is copied to clipboard', 'wcboost-wishlist' ),
			'i18n_close_button_text'      => esc_html__( 'Close', 'wcboost-wishlist' ),
			'i18n_add_to_wishlist'        => Helper::get_button_text(),
			'i18n_view_wishlist'          => Helper::get_button_text( 'view' ),
			'i18n_remove_from_wishlist'   => Helper::get_button_text( 'remove' ),
			'icon_normal'                 => Helper::get_wishlist_icon(),
			'icon_filled'                 => Helper::get_wishlist_icon( true ),
			'icon_loading'                => Helper::get_icon( 'spinner' ),
		] );

		wp_enqueue_script( 'wcboost-wishlist-fragments' );
		wp_localize_script( 'wcboost-wishlist-fragments', 'wcboost_wishlist_fragments_params', [
			'refresh_on_load' => get_option( 'wcboost_wishlist_ajax_bypass_cache', 'no' ),
			'hash_name'       => 'wcboost_wishlist_hash_' . Helper::get_wishlist_hash_key(),
			'timeout'         => apply_filters( 'wcboost_wishlist_ajax_timeout', 5000 ),
		] );
	}

	/**
	 * Add CSS classes to the body element on wishlist page
	 *
	 * @param array $classes
	 * @return array
	 */
	public function body_class( $classes ) {
		if ( Helper::is_wishlist() ) {
			$classes[] = 'woocommerce-page';
			$classes[] = 'woocommerce-wishlist';
			$classes[] = 'wcboost-wishlist-page';
		}

		return $classes;
	}

	/**
	 * Display notices.
	 * Need the additional check to avoid errors with live editor like Elementor.
	 *
	 * @return void
	 */
	public function print_notices() {
		if ( WC()->session ) {
			wc_print_notices();
		}
	}

	/**
	 * Load the wishlist header template.
	 *
	 * @return void
	 */
	public function wishlist_header() {
		$wishlist = Helper::get_wishlist( get_query_var( 'wishlist_token' ) );

		if ( ! $wishlist ) {
			return;
		}

		$show_title = wc_string_to_bool( get_option( 'wcboost_wishlist_page_show_title', 'no' ) );
		$show_desc  = wc_string_to_bool( get_option( 'wcboost_wishlist_page_show_desc', 'no' ) );
		$visble     = $show_title || $show_desc;

		if ( ! apply_filters( 'wcboost_wishlist_display_header', $visble, $wishlist ) ) {
			return;
		}

		$args = Templates::get_header_template_args( $wishlist );

		Templates::load_template( 'wishlist/wishlist-header.php', $args );
	}

	/**
	 * Load the wishlist content template.
	 *
	 * @return void
	 */
	public function wishlist_content() {
		$wishlist = Helper::get_wishlist( get_query_var( 'wishlist_token' ) );

		if ( ! $wishlist ) {
			return;
		}

		$template = Templates::get_wishlist_content_template( $wishlist );
		$args     = Templates::get_content_template_args( $wishlist );

		Templates::load_template( $template, $args );
	}

	/**
	 * Load the wishlist footer template.
	 *
	 * @return void
	 */
	public function wishlist_footer() {
		$wishlist = Helper::get_wishlist( get_query_var( 'wishlist_token' ) );

		if ( ! $wishlist ) {
			return;
		}

		$args = Templates::get_footer_template_args( $wishlist );

		Templates::load_template( 'wishlist/wishlist-footer.php', $args );
	}

	/**
	 * Display the wishlist title html.
	 *
	 * @param \WCBoost\Wishlist\Wishlist $wishlist
	 * @return void
	 */
	public function wishlist_title( $wishlist ) {
		$title = apply_filters( 'wcboost_wishlist_title', $wishlist->get_wishlist_title(), $wishlist );

		if ( empty( $title ) ) {
			return;
		}

		echo wp_kses_post( apply_filters( 'wcboost_wishlist_title_html', '<h2 class="wcboost-wishlist-title">' . $title . '</h2>', $title, $wishlist ) );
	}

	/**
	 * Display the wishlist description
	 *
	 * @param \WCBoost\Wishlist\Wishlis $wishlist
	 * @return void
	 */
	public function wishlist_description( $wishlist ) {
		$desc = apply_filters( 'wcboost_wishlist_description', $wishlist->get_description() );

		if ( empty( $desc ) ) {
			return;
		}

		echo wp_kses_post( apply_filters( 'wcboost_wishlist_description_html', '<div class="wcboost-wishlist-description">' . $desc . '</div>', $desc, $wishlist ) );
	}

	/**
	 * Display social sharing buttons on wishlist page
	 */
	public function share_buttons( $wishlist ) {
		$wishlist = $wishlist ? $wishlist : Helper::get_wishlist( get_query_var( 'wishlist_token' ) );

		if ( ! $wishlist->is_shareable() || ! $wishlist->count_items() ) {
			return;
		}

		$socials = ['facebook', 'twitter', 'linkedin', 'tumblr', 'reddit', 'stumbleupon', 'telegram', 'whatsapp', 'pocket', 'digg', 'vk', 'email', 'link'];
		$default = array_combine( $socials, array_fill( 0, count( $socials ), 'yes' ) );
		$enabled = get_option( 'wcboost_wishlist_share_socials', [] );
		$enabled = wp_parse_args( $enabled, $default );
		$enabled = array_map( 'wc_string_to_bool', $enabled );
		$enabled = array_filter( $enabled );

		if ( empty( $enabled ) ) {
			return;
		}

		// Don't display the share buttons if the wishlist is not viewed by the owner.
		if ( 'shared' == $wishlist->get_status()  && ! $wishlist->can_edit() ) {
			return;
		}

		$args = apply_filters( 'wcboost_wishlist_share_template_args', [
			'title'    => __( 'Share', 'wcboost-wishlist' ),
			'socials'  => array_keys( $enabled ),
			'wishlist' => $wishlist,
		] );

		Templates::load_template( 'wishlist/share.php', $args );
	}

	/**
	 * Display the link to edit the wishlist.
	 *
	 * @param \WCBoost\Wishlist\Wishlist $wishlist
	 * @return void
	 */
	public function link_edit_wishlist( $wishlist ) {
		if ( ! $wishlist->can_edit() ) {
			return;
		}

		$link = sprintf(
			'<a href="%s" class="wcboost-wishlist-edit-link" rel="nofollow">%s</a>',
			esc_url( $wishlist->get_edit_url() ),
			esc_html__( 'Edit wishlist', 'wcboost-wishlist' )
		);

		$link = apply_filters( 'wcboost_wishlist_edit_link', $link, $wishlist );

		if ( $link ) {
			echo '<div class="wcboost-wishlist-edit-link-wrapper">' . wp_kses_post( $link ) . '</div>';
		}
	}

	/**
	 * Load the form for deleting a wishlist.
	 *
	 * @deprecated 1.2.2
	 * @param \WCBoost\Wishlist\Wishlist $wishlist
	 * @return void
	 */
	public function form_delete_wishlist( $wishlist ) {
		_deprecated_function( __FUNCTION__, '1.2.2' );

		if ( ! is_user_logged_in() ) {
			return;
		}

		$wishlist = $wishlist ? $wishlist : Helper::get_wishlist( get_query_var( 'wishlist_token' ) );

		if ( ! $wishlist || ! $wishlist->can_delete() ) {
			return;
		}

		$args = apply_filters( 'wcboost_wishlist_form_delete_template_args', [
			'title'    => __( 'Delete wishlist', 'wcboost-wishlist' ),
			'message'  => __( 'Delete the wishlist and all items it contains', 'wcboost-wishlist' ),
			'wishlist' => $wishlist,
		] );

		Templates::load_template( 'wishlist/form-delete-wishlist.php', $args );
	}

	/**
	 * Display the link to remove the wishlist in the edit form.
	 *
	 * @since 1.2.2
	 *
	 * @param \WCBoost\Wishlist\Wishlist $wishlist
	 * @return void
	 */
	public function link_remove_wishlist( $wishlist ) {
		if ( ! $wishlist || ! $wishlist->get_id() || ! $wishlist->can_delete() ) {
			return;
		}
		?>
		<a href="<?php echo esc_url( $wishlist->get_delete_url() ); ?>" class="wcboost-wishlist-remove-link" rel="nofollow">
			<?php esc_html_e( 'Delete', 'wcboost-wishlist' ); ?>
		</a>
		<?php
	}

	/**
	 * Template hooks to display the wishlist on the single product page.
	 *
	 * @return void
	 */
	public function display_wishlist_button() {
		global $product;

		switch ( get_option( 'wcboost_wishlist_single_button_position', wc_get_theme_support( 'wishlist::single_button_position', 'after_add_to_cart' ) ) ) {
			case 'after_title':
				add_action( 'woocommerce_single_product_summary', [ $this, 'single_add_to_wishlist_button' ], 6 );
				break;

			case 'after_excerpt':
				add_action( 'woocommerce_single_product_summary', [ $this, 'single_add_to_wishlist_button' ], 25 );
				break;

			case 'before_add_to_cart':
				if ( ! $product->is_type( 'simple' ) || ( $product->is_purchasable() && $product->is_in_stock() ) ) {
					add_action( 'woocommerce_before_add_to_cart_button', [ $this, 'single_add_to_wishlist_button' ] );
				} else {
					add_action( 'woocommerce_single_product_summary', [ $this, 'single_add_to_wishlist_button' ], 35 );
				}
				break;

			case 'after_add_to_cart':
				if ( ! $product->is_type( 'simple' ) || ( $product->is_purchasable() && $product->is_in_stock() ) ) {
					add_action( 'woocommerce_after_add_to_cart_button', [ $this, 'single_add_to_wishlist_button' ] );
				} else {
					add_action( 'woocommerce_single_product_summary', [ $this, 'single_add_to_wishlist_button' ], 35 );
				}
				break;
		}
	}

	/**
	 * Display the add to wishlist button on catalog pages.
	 */
	public function loop_add_to_wishlist_button() {
		global $product;

		if ( ! $product ) {
			return;
		}

		$wishlist = Helper::get_wishlist( get_query_var( 'wishlist_token' ) );
		$item     = new Wishlist_Item( $product );

		if ( $wishlist->has_item( $item ) && 'hide' == get_option( 'wcboost_wishlist_exists_item_button_behaviour' ) ) {
			return;
		}

		$args = Templates::get_button_template_args( $wishlist, $item );

		Templates::load_template( 'loop/add-to-wishlist.php', $args );
	}

	/**
	 * Display the add to wishlist button on the single product page.
	 */
	public function single_add_to_wishlist_button() {
		global $product;

		if ( ! $product ) {
			return;
		}

		$wishlist = Helper::get_wishlist( get_query_var( 'wishlist_token' ) );
		$item     = new Wishlist_Item( $product );

		if ( $wishlist->has_item( $item ) && 'hide' == get_option( 'wcboost_wishlist_exists_item_button_behaviour' ) ) {
			return;
		}

		$args = Templates::get_button_template_args( $wishlist, $item );
		$args['class'] .= ' wcboost-wishlist-single-button';

		Templates::load_template( 'single-product/add-to-wishlist.php', $args );
	}

	/**
	 * Get CSS for custom button style.
	 *
	 * @return string
	 */
	public function get_custom_button_css() {
		$default_style = wp_parse_args( get_option( 'wcboost_wishlist_button_style' ), [
			'background_color' => '#333333',
			'border_color' => '#333333',
			'text_color' => '#ffffff',
		] );

		$hover_style = wp_parse_args( get_option( 'wcboost_wishlist_button_hover_style' ), [
			'background_color' => '#111111',
			'border_color' => '#111111',
			'text_color' => '#ffffff',
		] );

		$css = ':root {
			--wcboost-wishlist-button-color--background:' . $default_style['background_color'] . ';
			--wcboost-wishlist-button-color--border:' . $default_style['border_color'] . ';
			--wcboost-wishlist-button-color--text:' . $default_style['text_color'] . ';
			--wcboost-wishlist-button-hover-color--background:' . $hover_style['background_color'] . ';
			--wcboost-wishlist-button-hover-color--border:' . $hover_style['border_color'] . ';
			--wcboost-wishlist-button-hover-color--text:' . $hover_style['text_color'] . ';
		}';

		return $css;
	}

	/**
	 * Display wishlist buttons in the widget.
	 *
	 * @param  \WCBoost\Wishlist\Wishlist $wishlist
	 * @param  array $args
	 * @return void
	 */
	public function widget_buttons( $wishlist, $args ) {
		if ( ! $args['show_buttons'] ) {
			return;
		}

		printf(
			'<a href="%s" class="button">%s</a>',
			esc_url( $wishlist->get_public_url() ),
			esc_html__( 'View wishlist', 'wcboost-wishlist' )
		);
	}

	/**
	 * Get the button template args.
	 * Add a new parameter "attributes" since version 1.1.0
	 *
	 * @since 1.0.0
	 *
	 * @deprecated 1.2.2
	 *
	 * @param Wishlist $wishlist
	 * @param Wishlist_Item $item
	 * @return array
	 */
	public function get_button_template_args( $wishlist, $item ) {
		_deprecated_function( __FUNCTION__, '1.2.2', 'WCBoost\Wishlist\Templates::get_button_template_args()' );

		return Templates::get_button_template_args( $wishlist, $item );
	}

	/**
	 * Will set cookies if needed and when possible.
	 *
	 * @since 1.1.1
	 * @deprecated 1.1.2 Moved to the Session class.
	 *
	 * @return void
	 */
	public function maybe_set_cookies() {
		_deprecated_function( __METHOD__, '1.1.2', '\WCBoost\Wishlist\Session\maybe_set_cookies' );
		Session::instance()->maybe_set_cookies();
	}

	/**
	 * Filter the page title for wishlist endpoints
	 *
	 * Changes the page title when viewing manage-wishlists or add-wishlist endpoints.
	 * Similar to how WooCommerce handles My Account page endpoints.
	 *
	 * @since 1.2.3
	 *
	 * @param array $title_parts The document title parts.
	 * @return array Modified title parts.
	 */
	public function wishlist_endpoints_page_title( $title_parts ) {
		if ( ! Helper::is_wishlist() ) {
			return $title_parts;
		}

		if ( get_query_var( 'edit-wishlist' ) ) {
			$title_parts['title'] = __( 'Edit Wishlist', 'wcboost-wishlist' );
		}

		return $title_parts;
	}

	/**
	 * Filter the page heading for wishlist endpoints
	 *
	 * @since 1.2.3
	 *
	 * @param string $title The page title.
	 * @return string Modified page title.
	 */
	public function wishlist_endpoints_page_heading( $title ) {
		if ( ! in_the_loop() || ! is_main_query() || ! Helper::is_wishlist() ) {
			return $title;
		}

		if ( get_query_var( 'edit-wishlist' ) ) {
			$title = esc_html__( 'Edit wishlist', 'wcboost-wishlist' );
		}

		return $title;
	}
}
