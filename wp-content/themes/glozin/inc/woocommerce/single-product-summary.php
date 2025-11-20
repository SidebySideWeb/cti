<?php
/**
 * Hooks of Product Summary.
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
 * Class of Product Summary template.
 */
class Single_Product_Summary extends \Glozin\WooCommerce\Single_Product\Product_Base {
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
		add_action( 'glozin_woocommerce_before_product_summary', array( $this, 'add_actions' ) );
		add_action( 'glozin_woocommerce_after_product_summary',  array( $this, 'remove_actions' ) );
		
		// Gallery
		add_action( 'glozin_woocommerce_before_product_summary', 'woocommerce_show_product_images', 10 );

		// Summary
		add_action( 'glozin_woocommerce_product_summary', array( $this, 'product_title' ), 5 );
		add_action( 'glozin_woocommerce_product_summary', 'woocommerce_template_single_rating', 7 );
		add_action( 'glozin_woocommerce_product_summary', array( $this, 'open_product_price' ), 9 );
		add_action( 'glozin_woocommerce_product_summary', 'woocommerce_template_single_price', 10 );
		add_action( 'glozin_woocommerce_product_summary', array( $this, 'close_product_price' ), 12 );
		add_action( 'glozin_woocommerce_product_summary', array( $this, 'short_description' ), 20 );
		add_action( 'glozin_woocommerce_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
		
		// Button view full details
		add_action( 'glozin_woocommerce_product_summary', array( $this, 'view_full_details_button' ), 60 );

	}

	public function add_actions() {
		if( ! is_singular('product') ) {
			add_filter( 'woocommerce_single_product_image_gallery_classes', array( $this, 'single_product_image_gallery_classes' ), 20, 1 );
			add_action( 'woocommerce_product_thumbnails', array( $this, 'product_gallery_thumbnails' ), 20 );
		}
	}

	public function remove_actions() {
		if( ! is_singular('product') ) {
			$rm_filter = 'remove_filter';
			$rm_filter( 'woocommerce_single_product_image_gallery_classes', array( $this, 'single_product_image_gallery_classes' ), 20, 1 );
			remove_action( 'woocommerce_product_thumbnails', array( $this, 'product_gallery_thumbnails' ), 20 );
		}
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
}