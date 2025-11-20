<?php
/**
 * Hooks of Shoppable Video.
 *
 * @package Glozin
 */

namespace Glozin\WooCommerce;

use \Glozin\Helper;
use Glozin\Icon;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Shoppable Video template.
 */
class Shoppable_Video_Elementor extends \Glozin\WooCommerce\Single_Product\Product_Base {
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
		// Gallery
		add_action( 'glozin_woocommerce_product_shoppable_video_summary', 'woocommerce_show_product_images', 1 );

		// Summary
		add_action( 'glozin_woocommerce_product_shoppable_video_summary', array( $this, 'product_title' ), 5 );
		add_action( 'glozin_woocommerce_product_shoppable_video_summary', 'woocommerce_template_single_rating', 7 );
		add_action( 'glozin_woocommerce_product_shoppable_video_summary', array( $this, 'open_product_price' ), 9 );
		add_action( 'glozin_woocommerce_product_shoppable_video_summary', 'woocommerce_template_single_price', 10 );
		add_action( 'glozin_woocommerce_product_shoppable_video_summary', array( $this, 'close_product_price' ), 12 );
		add_action( 'glozin_woocommerce_product_shoppable_video_summary', array( $this, 'short_description' ), 20 );
		add_action( 'glozin_woocommerce_product_shoppable_video_summary', 'woocommerce_template_single_add_to_cart', 30 );
		
		// Button view full details
		add_action( 'glozin_woocommerce_product_shoppable_video_summary', array( $this, 'view_full_details_button' ), 25 );
		add_action( 'glozin_woocommerce_product_shoppable_video_summary', array( $this, 'view_full_details_button' ), 60 );
	}

	/**
	 * Product title
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_title() {
		the_title( '<h4 class="product-title fs-24 mt-0 mb-15"><a href="'.get_permalink().'">', '</a></h4>' );
	}

	/**
	 * View full details button
	 *
	 * @return void
	 */
	public function view_full_details_button() {
	?>
		<a class="view-full-details-button gz-button gz-button-text" href="<?php echo esc_url( get_permalink() ); ?>">
			<?php esc_html_e( 'View details', 'glozin' ); ?>
			<?php echo Icon::get_svg( 'double-arrow' ); ?>
		</a>
	<?php
	}
}
