<?php
/**
 * Glozin Addons Modules functions and definitions.
 *
 * @package Glozin
 */

namespace Glozin\Addons\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Addons Modules
 */
class Settings {

	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private static $instance;

	/**
	 * Registered modules.
	 *
	 * Holds the list of all the registered modules.
	 *
	 * @var array
	 */
	private $modules = [];

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
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ), 15 );
		add_action( 'admin_init', array( $this, 'save_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function enqueue_scripts() {
		if( is_admin() && isset($_GET['page']) && $_GET['page'] == 'theme_features' ) {
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'wc-enhanced-select' );
			wp_enqueue_script( 'woocommerce_admin' );
			
			$params = array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'strings' => array(
					'import_products' => __( 'Import products', 'woocommerce' ),
					'export_products' => __( 'Export products', 'woocommerce' ),
				),
				'urls' => array(
					'import_products' => current_user_can( 'import' ) ? esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_importer' ) ) : null,
					'export_products' => current_user_can( 'export' ) ? esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_exporter' ) ) : null,
				),
			);
			wp_localize_script( 'woocommerce_admin', 'woocommerce_admin', $params );
			
			wp_enqueue_script( 'glozin-addons-settings', GLOZIN_ADDONS_URL . 'assets/js/admin/features-settings.js', array( 'jquery' ), GLOZIN_ADDONS_VER, true );

			wp_enqueue_style( 'woocommerce_admin_styles' );
			wp_enqueue_style( 'jquery-ui-style' );
			wp_enqueue_style( 'glozin-addons-settings', GLOZIN_ADDONS_URL . 'assets/css/admin/features-settings.css', array(), GLOZIN_ADDONS_VER );
		}
	}

	/**
	 * Register admin menu.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_admin_menu() {
		add_submenu_page(
			'glozin_dashboard',
			esc_html__( 'Theme Features', 'glozin-addons' ),
			esc_html__( 'Theme Features', 'glozin-addons' ),
			'manage_options',
			'theme_features',
			array($this, 'theme_features_page')
		);
	}

	/**
	 * Register settings.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function save_settings() {
		$current_page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';
		if ( $current_page != 'theme_features' ) {
			return;
		}

		if ( ! isset( $_POST['action'] ) || $_POST['action'] != 'glozin_save_settings' ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have permission to access this page.', 'glozin-addons' ) );
			return;
		}
	
		if ( isset( $_POST['_wpnonce'] ) && ! wp_verify_nonce( $_POST['_wpnonce'], 'glozin_save_settings' ) ) {
			wp_die( __( 'You do not have permission to access this page.', 'glozin-addons' ) );
			return;
		}
	
		$current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : '';
		if ( empty( $current_tab ) ) {
			return;
		}

		$fields = apply_filters( 'glozin_get_settings_theme_features', array(), $current_tab );
		if( function_exists( 'woocommerce_update_options' ) ) {
			woocommerce_update_options( $fields );
		}

	}

	/**
	 * Theme features page.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function theme_features_page() {
		$current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : '';
		$sections = apply_filters( 'glozin_get_sections_theme_features', array() );
		?>
		<div class="wrap woocommerce">
			<h1><?php esc_html_e( 'Theme Features Settings', 'glozin-addons' ); ?></h1>
			<div class="gz-features-tabs">
				<div class="gz-features-tabs-header">
					<div class="gz-features-tabs-search-wrapper">
						<input type="text" id="gz-features-tabs-search" class="gz-features-tabs-search" placeholder="<?php esc_attr_e( 'Type to find a feature...', 'glozin-addons' ); ?>">
					</div>
					<div class="nav-tab-wrapper">
						<?php foreach ( $sections as $section_id => $section_title ) : ?>
							<a href="?page=theme_features&tab=<?php echo esc_attr( $section_id ); ?>"
							class="gz-features-tab-item nav-tab <?php echo $current_tab == $section_id ? 'nav-tab-active' : ''; ?>">
								<?php echo esc_html( $section_title ); ?>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="tab-content">
					<?php
					$settings = apply_filters( 'glozin_get_settings_theme_features', array(), $current_tab );

					if ( ! empty( $settings ) ) {
						echo '<form method="post" action="">';
						wp_nonce_field( 'glozin_save_settings' );
						echo '<input type="hidden" name="action" value="glozin_save_settings">';
						if( function_exists( 'woocommerce_admin_fields' ) ) {
							woocommerce_admin_fields( $settings );
						} 
						submit_button();
						echo '</form>';
					} else {
						echo '<div class="gz-features-tabs-description">';
						echo '<p>' . esc_html__( 'Welcome to our comprehensive feature showcase, designed to elevate your WooCommerce store and enhance the shopping experience for your customers.', 'glozin-addons' ) . '</p>';
						echo '<p>' . esc_html__( 'On the left, you\'ll find a list of our key features, each crafted to provide you with powerful tools for customization and optimization.', 'glozin-addons' ) . '</p>';
						echo '</div>';
					}
					?>
				</div>
			</div>
		</div>
	<?php
	}
}
