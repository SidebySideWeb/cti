<?php

namespace Glozin\Addons\Modules\Recent_Sales_Count;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main class of plugin for admin
 */
class Frontend {

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
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'woocommerce_single_product_summary', array( $this, 'open_recent_sales_count' ), 6 );
		add_action( 'woocommerce_single_product_summary', array( $this, 'recent_sales_count' ), 8 );
		add_action( 'woocommerce_single_product_summary', array( $this, 'close_recent_sales_count' ), 8 );

		add_action( 'glozin_recent_sales_count_elementor', array( $this, 'recent_sales_count' ), 13 );

		add_action( 'template_redirect', array( $this, 'update_recent_sales_count' ) );
	}

	/**
	 * Open recent sales count
	 *
	 * @return void
	 */
	public function open_recent_sales_count() {
		echo '<div class="glozin-recent-sales-count d-flex gap-20">';
	}

	/**
	 * Close recent sales count
	 *
	 * @return void
	 */
	public function close_recent_sales_count() {
		echo '</div>';
	}


	/**
	 * Get people view fake
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function recent_sales_count() {
		global $product;

		$categories = get_option( 'glozin_recent_sales_count_categories' );
		$products   = get_option( 'glozin_recent_sales_count_products' );
		$number     = get_post_meta( $product->get_ID(), 'glozin_recent_sales_count_expiration', true );
		$hours      = apply_filters( 'glozin_recent_sales_hours', get_option( 'glozin_recent_sales_count_hours', 7 ) );
		$check 		= false;

		if( empty( $products ) && empty( $categories ) ) {
			$check = true;
		}

		if( ! empty( $categories ) ) {

			$terms = get_the_terms( $product->get_ID(), 'product_cat' );
			if( ! is_wp_error( $terms ) && $terms ) {
				$term_slugs = array();
				foreach( $terms as $term ) {
					$term_slugs[] = $term->slug;
					if( in_array( $term->slug, $categories ) ) {
						$check = true;
						break;
					}
				}
			}

		}

		if( ! empty( $products ) ) {
			if( in_array( $product->get_ID(), $products ) ) {
				$check = true;
			}
		}

		if( ! $check ) {
			return;
		}

		if( get_option( 'glozin_recent_sales_count_out_of_stock', 'yes' ) == 'no' && ! $product->is_in_stock() ) {
			return;
		}

		$html_number = '<span class="glozin-recent-sales-count__numbers">' . $number['number'] . '</span>';
		?>
			<div class="glozin-recent-sales-count d-flex gap-5 lh-normal mb-24">
				<?php echo apply_filters( 'glozin_recent_sales_count_icon', \Glozin\Addons\Helper::get_svg( 'fire', 'ui', 'class=glozin-recent-sales__icon fs-16' ) ); ?>
				<span class="glozin-recent-sales-count__text text-primary">
					<?php
					echo apply_filters( 'glozin_recent_sales_count_text', sprintf(
						__( '%s sold in last %s hours', 'glozin-addons' ),
						$html_number,
						$hours
					), $html_number, $hours );
					?>
				</span>
			</div>
		<?php
	}

	/**
	 * Update cart tracking
	 *
	 * @return void
	 */
	public function update_recent_sales_count() {
		if( ! is_singular( 'product' ) ) {
			return;
		}

		$number = get_post_meta( get_the_ID(), 'glozin_recent_sales_count_expiration', true );
		$time   = ( intval( apply_filters( 'glozin_recent_sales_hours', get_option( 'glozin_recent_sales_count_hours', 7 ) ) ) * 60 * 60 );
		$from   = get_option( 'glozin_recent_sales_count_random_numbers_form', 1 );
		$to     = get_option( 'glozin_recent_sales_count_random_numbers_to', 100 );

		$args = array(
			'number' => rand( $from, $to ),
			'time'   => time()
		);

		if( ! $number ) {
			update_post_meta( get_the_ID(), 'glozin_recent_sales_count_expiration', $args );
		} else {
			if( ( $number['time'] + $time ) <= time() ) {
				update_post_meta( get_the_ID(), 'glozin_recent_sales_count_expiration', $args );
			}
		}
	}
}