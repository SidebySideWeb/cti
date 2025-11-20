<?php
/**
 * Hooks of QuickView.
 *
 * @package Glozin
 */

namespace Glozin\WooCommerce\Loop;

use \Glozin\Helper;
use Glozin\Icon;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of QuickView template.
 */
class Quick_View extends \Glozin\WooCommerce\Single_Product\Product_Base {
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
		add_action( 'wp_enqueue_scripts', array( $this, 'quick_view_scripts' ), 20 );
		add_filter( 'glozin_wp_script_data', array( $this, 'quickview_script_data' ), 10, 3 );

		// Quick view AJAX.
		add_action( 'wc_ajax_product_quick_view', array( $this, 'quick_view' ) );

		// Gallery
		add_action( 'glozin_woocommerce_before_product_quickview_summary', 'woocommerce_show_product_images', 10 );

		// Summary
		add_action( 'glozin_woocommerce_product_quickview_summary', array( $this, 'product_title' ), 5 );
		add_action( 'glozin_woocommerce_product_quickview_summary', 'woocommerce_template_single_rating', 7 );
		add_action( 'glozin_woocommerce_product_quickview_summary', array( $this, 'open_product_price' ), 9 );
		add_action( 'glozin_woocommerce_product_quickview_summary', 'woocommerce_template_single_price', 10 );
		add_action( 'glozin_woocommerce_product_quickview_summary', array( $this, 'close_product_price' ), 12 );
		add_action( 'glozin_woocommerce_product_quickview_summary', array( $this, 'short_description' ), 20 );
		add_action( 'glozin_woocommerce_product_quickview_summary', 'woocommerce_template_single_add_to_cart', 30 );

		// Button view full details
		add_action( 'glozin_woocommerce_product_quickview_summary', array( $this, 'view_full_details_button' ), 60 );

	}

	/**
	 * WooCommerce specific scripts & stylesheets.
	 *
	 * @return void
	 */
	public static function quick_view_scripts() {
		wp_enqueue_script( 'glozin-countdown',  get_template_directory_uri() . '/assets/js/plugins/jquery.countdown.js', array(), '1.0' );

		if ( wp_script_is( 'wc-add-to-cart-variation', 'registered' ) ) {
			wp_enqueue_script( 'wc-add-to-cart-variation' );
		}

		if ( wp_script_is( 'flexslider', 'registered' ) ) {
			wp_enqueue_script( 'flexslider' );
		}
	}

	/**
	 * Quickview script data.
	 *
	 * @since 1.0.0
	 *
	 * @param $data
	 *
	 * @return array
	 */
	public function quickview_script_data( $data ) {
		$data['product_quickview_nonce'] = wp_create_nonce( 'glozin-product-quickview' );
		$data['mobile_single_product_gallery_arrows'] = \Glozin\Helper::get_option( 'mobile_single_product_gallery_arrows' );

		return $data;
	}

	/**
	 * Product quick view template.
	 *
	 * @return string
	 */
	public static function quick_view() {
		if ( empty( $_POST['product_id'] ) ) {
			wp_send_json_error( esc_html__( 'No product.', 'glozin' ) );
			exit;
		}

		$post_object = get_post( $_POST['product_id'] );
		if ( ! $post_object || ! in_array( $post_object->post_type, array( 'product', 'product_variation', true ) ) ) {
			wp_send_json_error( esc_html__( 'Invalid product.', 'glozin' ) );
			exit;
		}

		$GLOBALS['post'] = $post_object;
		wc_setup_product_data( $post_object );
		ob_start();
		wc_get_template( 'content-product-quickview.php', array(
			'post_object'      => $post_object,
		) );
		wp_reset_postdata();
		wc_setup_product_data( $GLOBALS['post'] );
		$output = ob_get_clean();

		wp_send_json_success( $output );
		exit;
	}

	/**
	 * Product title
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_title() {
		the_title( '<h3 class="product_title entry-title">', '</h3>' );
	}

	/**
	 * View full details button
	 *
	 * @return void
	 */
	public function view_full_details_button() {
	?>
		<a class="view-full-details-button gz-button gz-button-subtle" href="<?php echo esc_url( get_permalink() ); ?>">
			<?php esc_html_e( 'View Full Details', 'glozin' ); ?>
			<?php echo Icon::get_svg( 'double-arrow' ); ?>
		</a>
	<?php
	}

	/**
	 *  Quick view icon
	 */
	protected function quick_view_button_icon($classes = 'gz-button', $product = false) {
		$classes = 'product-loop-button gz-button-icon gz-tooltip-inside ' . $classes;

		$classes = apply_filters( 'glozin_quick_view_button_icon_classes', $classes );

		self::quick_view_button_html( $classes, true, $product);
	}

	/**
	 *  Quick view icon
	 */
	public function quick_view_button_icon_light($product = false) {
		$this->quick_view_button_icon( 'gz-button-light gz-tooltip-inside', $product );
	}

	/**
	 * Get Quick view icon
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function quick_view_button_html( $classes = '', $only_icon = false, $_product = false ) {
		global $product;

		$_product = empty( $_product  ) ? $product : $_product ;

		$content = \Glozin\Icon::inline_svg( 'icon=icon-quickview' );
		if( ! $only_icon ) {
			$content = sprintf(
				'<span class="glozin-button__icon">%s</span>
				<span class="glozin-quickview-button__text">%s</span>',
				$content,
				esc_html__( 'Quick View', 'glozin' )
			);
		}
		\Glozin\Theme::set_prop( 'modals', 'quickview' );
		echo sprintf(
			'<a href="%s" class="glozin-quickview-button button %s" data-toggle="modal" data-target="quick-view-modal" data-product_id="%d" data-tooltip="%s" data-tooltip_position="%s" aria-label="%s" rel="nofollow">
				%s
			</a>',
			is_customize_preview() ? '#' : esc_url( get_permalink() ),
			esc_attr( $classes ),
			esc_attr( $_product->get_id() ),
			esc_attr__( 'Quick View', 'glozin' ),
			apply_filters( 'glozin_quickview_tooltip_position', 'left' ),
			esc_attr__( 'Quick View for', 'glozin' ) . ' ' . $_product->get_title(),
			$content
		);
	}
}
