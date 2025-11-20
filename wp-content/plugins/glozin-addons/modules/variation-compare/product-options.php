<?php
/**
 * WooCommerce additional settings.
 *
 * @package Glozin
 */

 namespace Glozin\Addons\Modules\Variation_Compare;

use \Glozin\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Product Settings
 */
class Product_Options {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
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
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 50 );

		add_filter( 'woocommerce_product_data_tabs', [ $this, 'variation_compare_tab' ] );
		add_action( 'woocommerce_product_data_panels', array( __CLASS__, 'product_variation_compare_options' ) );
		add_action( 'woocommerce_process_product_meta', array( __CLASS__, 'save_product_data' ) );

		// Atribute
		add_action( 'wp_ajax_glozin_wc_product_variation_compare', array( $this, 'wc_product_variation_compare' ) );
		add_action( 'wp_ajax_nopriv_glozin_wc_product_variation_compare', array( $this, 'wc_product_variation_compare' ) );
	}

	/**
	 * Enqueue Scripts
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts( $hook ) {
		$screen = get_current_screen();
		if ( in_array( $hook, array( 'post.php', 'post-new.php' ) ) && $screen->post_type == 'product' ) {
			wp_enqueue_script( 'glozin_wc_variation_compare_js', GLOZIN_ADDONS_URL . 'modules/variation-compare/assets/admin/variation-compare-admin.js', array( 'jquery' ), '20220318', true );
		}
	}

	/**
	 * Add new product data tab for swatches
	 *
	 * @param array $tabs
	 *
	 * @return array
	 */
	public function variation_compare_tab( $tabs ) {
		$tabs['product_variation_compare'] = [
			'label'    => esc_html__( 'Variation Compare', 'glozin-addons' ),
			'target'   => 'product_variation_compare_data',
			'class'    => [ 'variation_compare_tab', 'show_if_variable' ],
			'priority' => 61,
		];

		return $tabs;
	}

	/**
	 * Add more options to advanced tab.
	 */
	public static function product_variation_compare_options() {
		?>
		<div id="product_variation_compare_data" class="panel woocommerce_options_panel wc-metaboxes-wrapper hidden">
			<div class="options_group product-variation-compare" id="glozin-variation-compare">
			<?php
			self::get_product_attributes(get_the_ID());
			?>
			</div>
		</div>
		<?php
	}

	/**
	 * Save product data.
	 *
	 * @param int $post_id The post ID.
	 */
	public static function save_product_data( $post_id ) {
		if ( 'product' !== get_post_type( $post_id ) ) {
			return;
		}

		if ( isset( $_POST['glozin_product_variation_attribute'] ) ) {
			$woo_data = $_POST['glozin_product_variation_attribute'];
			update_post_meta( $post_id, 'glozin_product_variation_attribute', $woo_data );
		}

	}

	/**
	 * Get Product Attributes AJAX function.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function wc_product_variation_compare() {
		$post_id = $_POST['post_id'];

		if ( empty( $post_id ) ) {
			return;
		}
		ob_start();
		$this->get_product_attributes($post_id);
		$response = ob_get_clean();
		wp_send_json_success( $response );
		die();
	}

	/**
	 * Get Product Attributes function.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function get_product_attributes ($post_id) {
		$product_object = wc_get_product( $post_id );
		if( ! $product_object ) {
			return;
		}
		$attributes = $product_object->get_attributes();

		if( ! $attributes ) {
			return;
		}
		$options         = array();
		$options['']     = esc_html__( 'Default', 'glozin-addons' );
		$options['none'] = esc_html__( 'None', 'glozin-addons' );
		foreach ( $attributes as $attribute ) {
			$options[ sanitize_title( $attribute['name'] ) ] = wc_attribute_label( $attribute['name'] );
		}
		woocommerce_wp_select(
			array(
				'id'       => 'glozin_product_variation_attribute',
				'label'    => esc_html__( 'Primary Attribute', 'glozin-addons' ),
				'desc_tip'    => true,
				'description' => esc_html__( 'Display the primary attribute for comparison on the product page', 'glozin-addons' ),
				'options'  => $options
			)
		);

	}
}
