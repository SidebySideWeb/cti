<?php
/**
 * Register footer builder
 */

namespace Glozin\Addons;

class Theme_Settings {

	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private static $instance;


	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
	}

	/**
	 * Register admin menu.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_admin_menu() {
		$glozin_icon = "data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMzAiIHZpZXdCb3g9IjAgMCAyMCAzMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTguNzAwMSAzLjM5OTlDNi4zMDAxIDMuMzk5OSA0LjMwMDEgNC4xOTk5IDIuNjAwMSA1Ljg5OTlDMC45MDAwOTggNy41OTk5IDAuMTAwMDk4IDkuNTk5OSAwLjEwMDA5OCAxMS44OTk5QzAuMTAwMDk4IDE0LjE5OTkgMC45MDAwOTggMTYuMjk5OSAyLjYwMDEgMTcuODk5OUM0LjMwMDEgMTkuNDk5OSA2LjMwMDEgMjAuMzk5OSA4LjcwMDEgMjAuMzk5OUMxMS4xMDAxIDIwLjM5OTkgMTMuMDAwMSAxOS41OTk5IDE0LjcwMDEgMTcuODk5OUMxNi40MDAxIDE2LjE5OTkgMTcuMjAwMSAxNC4xOTk5IDE3LjIwMDEgMTEuODk5OUMxNy4yMDAxIDkuNTk5OSAxNi40MDAxIDcuNTk5OSAxNC43MDAxIDUuODk5OUMxMy4xMDAxIDQuMTk5OSAxMS4xMDAxIDMuMzk5OSA4LjcwMDEgMy4zOTk5Wk0xMi4xMDAxIDE1LjM5OTlDMTEuMjAwMSAxNi4yOTk5IDEwLjEwMDEgMTYuNzk5OSA4LjcwMDEgMTYuNzk5OUM3LjMwMDEgMTYuNzk5OSA2LjIwMDEgMTYuMjk5OSA1LjMwMDEgMTUuMzk5OUM0LjQwMDEgMTQuNDk5OSAzLjkwMDEgMTMuMjk5OSAzLjkwMDEgMTEuODk5OUMzLjkwMDEgMTAuNDk5OSA0LjQwMDEgOS4yOTk5IDUuMzAwMSA4LjM5OTlDNi4yMDAxIDcuNDk5OSA3LjMwMDEgNi45OTk5IDguNzAwMSA2Ljk5OTlDMTAuMTAwMSA2Ljk5OTkgMTEuMjAwMSA3LjQ5OTkgMTIuMTAwMSA4LjM5OTlDMTMuMDAwMSA5LjI5OTkgMTMuNTAwMSAxMC40OTk5IDEzLjUwMDEgMTEuODk5OUMxMy41MDAxIDEzLjI5OTkgMTMuMTAwMSAxNC40OTk5IDEyLjEwMDEgMTUuMzk5OVoiIGZpbGw9IiNBN0FBQUQiLz4KPHBhdGggZD0iTTEyLjIgMjQuOTk5OUMxMS4zIDI1Ljg5OTkgMTAuMSAyNi4zOTk5IDguNyAyNi4zOTk5QzcuMyAyNi4zOTk5IDYuMiAyNS44OTk5IDUuMiAyNC45OTk5QzQuMyAyNC4wOTk5IDMuOCAyMi44OTk5IDMuOCAyMS4zOTk5SDBDMCAyMy43OTk5IDAuOCAyNS43OTk5IDIuNSAyNy40OTk5QzQuMyAyOS4xOTk5IDYuMyAyOS45OTk5IDguNyAyOS45OTk5QzExLjEgMjkuOTk5OSAxMy4xIDI5LjE5OTkgMTQuOCAyNy40OTk5QzE2LjUgMjUuNzk5OSAxNy4zIDIzLjc5OTkgMTcuMyAyMS4zOTk5SDEzLjVDMTMuNiAyMi44OTk5IDEzLjEgMjMuOTk5OSAxMi4yIDI0Ljk5OTlaIiBmaWxsPSIjQTdBQUFEIi8+CjxwYXRoIGQ9Ik0xNy40IDUuMjk5OTVDMTguNyA1LjI5OTk1IDE5LjggNC4yOTk5NSAxOS44IDIuOTk5OTVDMTkuOCAxLjY5OTk1IDE4LjcgMC42OTk5NTEgMTcuNCAwLjY5OTk1MUMxNi4xIDAuNjk5OTUxIDE1IDEuNTk5OTUgMTUgMi44OTk5NUMxNSA0LjE5OTk1IDE2LjEgNS4yOTk5NSAxNy40IDUuMjk5OTVaIiBmaWxsPSIjQTdBQUFEIi8+Cjwvc3ZnPgo=";
		add_menu_page(
			esc_html__( 'Glozin', 'glozin-addons' ),
			esc_html__( 'Glozin', 'glozin-addons' ),
			'manage_options',
			'glozin_dashboard',
			array($this, 'glozin_dashboard_page_content'),
			$glozin_icon,
			59
		);

		do_action('glozin_register_theme_settings_submenu');
	}

	/**
	 * Glozin dashboard page content.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function glozin_dashboard_page_content() {
		?>
		<h1><?php esc_html_e('Glozin - Multipurpose WooCommerce WordPress Theme', 'glozin-addons') ?></h1>
		<p>
			<strong><?php esc_html_e( 'Welcome to Glozin! We\'re thrilled you chose our theme. For quick answers, browse our comprehensive Documentation. Need extra help? Our dedicated support team is ready to assist - just open a ticket and we\'ll get you sorted in no time.', 'glozin-addons' ); ?></strong>
		</p>
		<p>
			<strong>
				<a href="https://wpglozin.com/doc/#/" target="_blank"><?php esc_html_e('Documentation', 'glozin-addons'); ?></a> |
				<a href="https://uix.ticksy.com/" target="_blank"><?php esc_html_e('Support Ticket', 'glozin-addons'); ?></a>
			</strong>
		</p>
		<?php
	}
}