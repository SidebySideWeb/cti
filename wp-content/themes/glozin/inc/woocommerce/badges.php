<?php
/**
 * Badges hooks.
 *
 * @package Glozin
 */

namespace Glozin\WooCommerce;

use Glozin\Helper, Glozin\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Badges
 */
class Badges {
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
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash' );
		add_action( 'glozin_product_loop_thumbnail', array( $this, 'badges' ), 2 );

		// Single product
		add_action( 'woocommerce_single_product_summary', array( $this, 'single_badges' ), 1 );
		add_action( 'glozin_woocommerce_product_quickview_summary', array( $this, 'single_badges' ), 1 );
		add_action( 'glozin_woocommerce_product_summary', array( $this, 'single_badges' ), 1 );

		// Add badges for data product
		add_filter( 'woocommerce_available_variation', array( $this, 'data_variation_badges' ), 10, 3 );
	}

	/**
	 * Product badges.
	 */
	public static function badges( $product = null, $classes = 'position-absolute top-10 top-15-xl start-10 start-15-xl z-2 pe-none', $args = array() ) {
		if( empty( $product ) ) {
			global $product;
		}

		$badges = array();
		$badges[] = self::get_badges( $product, $args );

		$_product_id = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();

		$custom_badges_icon_image_html = get_post_meta( $_product_id, 'custom_badges_icon_image_html', true );
		$custom_badges_text = get_post_meta( $_product_id, 'custom_badges_text', true );
		if ( $custom_badges_icon_image_html || $custom_badges_text ) {
			$style    = '';
			$custom_badges_bg    = get_post_meta( $_product_id, 'custom_badges_bg', true );
			$custom_badges_color = get_post_meta( $_product_id, 'custom_badges_color', true );
			$bg_color = ! empty( $custom_badges_bg ) ? '--id--badge-custom-bg:' . $custom_badges_bg . ';' : '';
			$color    = ! empty( $custom_badges_color ) ? '--id--badge-custom-color:' . $custom_badges_color . ';' : '';

			if ( $bg_color || $color ) {
				$style = 'style="' . $color . $bg_color . '"';
			}

			if ( $custom_badges_icon_image_html ) {
				$custom_badges_icon_image_html = '<span class="custom-icon-image glozin-svg-icon me-3">' . $custom_badges_icon_image_html . '</span>';
			}

			$badges_custom['html'] = '<span class="custom woocommerce-badge" ' . $style . '>' . $custom_badges_icon_image_html . esc_html( $custom_badges_text ) . '</span>';
			$badges[] = $badges_custom;
		}

		$badge_html = '';
		$badge_countdown = '';
		foreach ( $badges as $badge ) {
			if ( ! empty( $badge ) && ! empty( $badge['html']  ) ) {
				if( is_array( $badge['html'] ) ) {
					$badge_html .= implode( '', $badge['html'] );
				} else {
					$badge_html .= $badge['html'];
				}
			}

			if ( ! empty( $badge ) && ! empty( $badge['countdown']  ) ) {
				$badge_countdown = $badge['countdown'];
			}
		}

		if( ! empty( $badge_html ) ) {
			printf( '<div class="woocommerce-badges %s">%s</div>', esc_attr( apply_filters( 'glozin_woocommerce_badges_class', $classes ) ), $badge_html );
		}

		echo ! empty( $badge_countdown ) ? $badge_countdown : '';

	}

	/**
	 * Single product badges.
	 */
	public static function single_badges( $product, $args = array(), $classes = 'woocommerce-badges--single' ) {
		if( empty( $product ) ) {
			global $product;
		}
		$args = wp_parse_args(
			$args,
			array(
				'badges_sale'           => Helper::get_option( 'product_badges_sale' ),
				'badges_sale_type'      => Helper::get_option( 'product_badges_sale_type' ),
				'badges_new'            => Helper::get_option( 'product_badges_new' ),
				'badges_featured'       => Helper::get_option( 'product_badges_featured' ),
				'badges_in_stock'       => Helper::get_option( 'product_badges_stock' ),
				'badges_soldout'        => Helper::get_option( 'product_badges_stock' ),
				'badges_preorder'       => Helper::get_option( 'product_badges_preorder' ),
				'is_single'	            => true,
				'sale_display_type' 	=> false,
			)
		);

		self::badges( $product, $classes, $args );
	}

	/**
	 * Get product badges.
	 *
	 * @return array
	 */
	public static function get_badges( $product = array(), $args = array() ) {
		if( empty( $product ) ) {
			global $product;
		}

		$args = wp_parse_args(
			$args,
			array(
				'badges_soldout'        => Helper::get_option( 'badges_soldout' ),
				'badges_soldout_text'   => esc_html__( 'Out of stock', 'glozin' ),
				'badges_sale'           => Helper::get_option( 'badges_sale' ),
				'badges_sale_type'      => Helper::get_option( 'badges_sale_type' ),
				'badges_sale_text'      => esc_html__( 'Sale', 'glozin' ),
				'badges_new'            => Helper::get_option( 'badges_new' ),
				'badges_new_text'       => esc_html__( 'New', 'glozin' ),
				'badges_featured'       => Helper::get_option( 'badges_featured' ),
				'badges_featured_text'  => esc_html__( 'Hot', 'glozin' ),
				'badges_preorder'       => Helper::get_option( 'badges_preorder' ),
			)
		);

		$badges = array();
		$badges['countdown'] = '';

		if ( $args['badges_soldout'] && $product->get_stock_status() == 'outofstock' ) {
			if ( class_exists( '\Glozin\Addons\Modules\Pre_Order\Helper' ) && \Glozin\Addons\Modules\Pre_Order\Helper::is_pre_order_active( $product ) ) {
				if( $args['badges_preorder'] ) {
					$text = esc_html__( 'Pre-Order', 'glozin' );
					$badges['html'] = '<div class="stock-badge"><p class="pre-order woocommerce-badge">' . esc_html( $text ) . '</p></div>';
				}
			} else {
				$text = ! empty( $args['badges_soldout_text'] ) ? $args['badges_soldout_text'] : esc_html__( 'Out Of Stock', 'glozin' );
				$badges['html'] = '<div class="stock-badge"><p class="stock sold-out woocommerce-badge">' . esc_html( $text ) . '</p></div>';
			}
		} else {
			if ( $product->is_on_sale() && $args['badges_sale'] ) {
				$badges['html'][] = self::sale_flash( $product, $args );

				if( ! isset( $args['sale_display_type'] ) && Helper::get_option( 'sale_display_type' ) == 'countdown' ) {
					$badges['countdown'] = \Glozin\WooCommerce\Helper::get_product_countdown( '', '', 'glozin-badges-sale__countdown gz-button gz-button-light text-primary position-absolute start-50 translate-middle-x bottom-15 ms-auto me-auto my-0 fw-semibold rounded-30 z-2 pe-none', $product );
				}

				if( ! isset( $args['sale_display_type'] ) && Helper::get_option( 'sale_display_type' ) == 'marquee' ) {
					$badges['html'][] = self::sale_flash_marquee( $product );
				}
			}

			else if ( $args['badges_new'] && in_array( $product->get_id(), WooCommerce\General::glozin_woocommerce_get_new_product_ids() ) ) {
				$text          = $args['badges_new_text'];
				$text          = empty( $text ) ? esc_html__( 'New', 'glozin' ) : $text;
				$badges['html'][] = '<span class="new woocommerce-badge">' . esc_html( $text ) . '</span>';
			}

			else if ( $product->is_featured() && $args['badges_featured'] ) {
				$text               = $args['badges_featured_text'];
				$text               = empty( $text ) ? esc_html__( 'Hot', 'glozin' ) : $text;
				$badges['html'][] = '<span class="featured woocommerce-badge">' . esc_html( $text ) . '</span>';
			}

			if ( class_exists( '\Glozin\Addons\Modules\Pre_Order\Helper' ) && \Glozin\Addons\Modules\Pre_Order\Helper::is_pre_order_active( $product ) ) {
				if( $args['badges_preorder'] ) {
					$text = esc_html__( 'Pre-Order', 'glozin' );
					$badges['html'][] = '<div class="stock-badge"><p class="pre-order woocommerce-badge">' . esc_html( $text ) . '</p></div>';
				}
			} else if ( $product->is_in_stock() && ! empty( $args['badges_in_stock'] ) && ! $product->is_on_backorder() && $product->is_purchasable() ) {
				$badges['html'][] = '<div class="stock-badge"><p class="stock in-stock woocommerce-badge">' . wc_format_stock_for_display( $product ) . '</p></div>';
				$product_availability = $product ? $product->get_availability() : '';
				$text = $product_availability && !empty( $product_availability['availability'] ) ? $product_availability['availability'] : esc_html__( 'In Stock', 'glozin' );
				$badges['html'][] = '<div class="stock-badge"><p class="stock in-stock woocommerce-badge">' . $text . '</p></div>';
				
			}
		}

		$badges = apply_filters( 'glozin_product_badges', $badges, $product );

		return $badges;
	}

	/**
	 * Sale badge.
	 *
	 * @param string $output  The sale flash HTML.
	 * @param object $post    The post object.
	 * @param object $product The product object.
	 *
	 * @return string
	 */
	public static function sale_flash( $product, $args ) {
		if ( 'grouped' == $product->get_type() ) {
			return '';
		}
		$output = '';
		$type       = $args[ 'badges_sale_type' ];
		$text       =  ! empty( $args['badges_sale_text'] ) ? $args['badges_sale_text'] : esc_html__( 'Sale', 'glozin' );
		$percentage = 0;

		if ( 'percent' == $type || false !== strpos( $text, '{%}' ) || false !== strpos( $text, '{$}' ) ) {
			$percentage = self::get_product_discount_percent( $product );
		}

		if ( 'percent' == $type ) {
			if( $percentage >= 1 ) {
				$output = '<span class="onsale woocommerce-badge">-' . $percentage . '%</span>';
			}
		} else {
			$output = '<span class="onsale woocommerce-badge">' . wp_kses_post( $text ) . '</span>';
		}

		return $output;
	}

	public static function sale_flash_marquee( $product ) {
		if( is_singular('product') ) {
			return;
		}

		$percent = self::get_product_discount_percent( $product );
		?>

		<div class="glozin-sale-flash-marquee glozin-marquee glozin-elementor--marquee bg-dark text-light py-5 position-absolute z-2 bottom-0 start-0 end-0" data-speed="<?php echo esc_attr( Helper::get_option( 'sale_display_marquee_speed' ) ); ?>">
			<div class="glozin-marquee__inner glozin-marquee--inner">
				<div class="glozin-marquee__items glozin-marquee--items glozin-marquee--original" data-id="<?php echo esc_attr( $product->get_id() ); ?>">
					<div class="d-flex align-items-center gap-20">
						<?php echo \Glozin\Icon::get_svg( 'sale-flash', 'ui', 'class=fs-12' ); ?>
						<div class="fs-13 fw-semibold text-uppercase text-nowrap">
							<?php echo sprintf( esc_html__( 'Hot Sale %d%% Off', 'glozin' ), $percent ); ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php
	}

	public static function get_product_discount_percent( $product ) {
		$saved      = 0;
		$percentage = 0;

		if ( $product->get_type() == 'variable' ) {
			$available_variations = $product->get_available_variations();
			$max_percentage       = 0;
			$max_saved            = 0;
			$total_variations     = count( $available_variations );

			for ( $i = 0; $i < $total_variations; $i++ ) {
				$variation_id        = $available_variations[ $i ]['variation_id'];
				$variable_product    = new \WC_Product_Variation( $variation_id );
				$regular_price       = $variable_product->get_regular_price();
				$sales_price         = $variable_product->get_price();
				$variable_saved      = $regular_price && $sales_price ? ( $regular_price - $sales_price ) : 0;
				$variable_percentage = $regular_price && $sales_price ? round( ( ( ( $regular_price - $sales_price ) / $regular_price ) * 100 ) ) : 0;

				if ( $variable_saved > $max_saved ) {
					$max_saved = $variable_saved;
				}

				if ( $variable_percentage > $max_percentage ) {
					$max_percentage = $variable_percentage;
				}
			}

			$saved      = $max_saved ? $max_saved : $saved;
			$percentage = $max_percentage ? $max_percentage : $percentage;
		} elseif ( $product->get_regular_price() != 0 ) {
			$regular_price = $product->get_regular_price();
			$sales_price = $product->get_price();

			$percentage = round( ( ( $regular_price - $sales_price ) / $regular_price ) * 100 );
		}

		return $percentage;
	}

	public function data_variation_badges( $data, $parent, $variation ) {
		ob_start();
		$this->single_badges( $variation );
		$badges_html = ob_get_clean();
		$data['badges_html'] = esc_html($badges_html);
		return $data;
	}
}
