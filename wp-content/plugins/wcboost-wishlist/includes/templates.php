<?php
/**
 * Templates utility class
 *
 * @version 1.0.0
 * @package WCBoost\Wishlist
 */

namespace WCBoost\Wishlist;

defined( 'ABSPATH' ) || exit;

/**
 * Class \WCBoost\Wishlist\Templates
 */
class Templates {

	/**
	 * Get the appropriate wishlist template based on wishlist state
	 *
	 * @param \WCBoost\Wishlist\Wishlist $wishlist
	 * @return string
	 */
	public static function get_wishlist_template( $wishlist ) {
		if ( $wishlist->can_edit() ) {
			if ( get_query_var( 'edit-wishlist' ) ) {
				$template = 'wishlist/form-edit-wishlist.php';
			} else {
				$template = $wishlist->count_items() ? 'wishlist/wishlist.php' : 'wishlist/wishlist-empty.php';
			}
		} elseif ( $wishlist->is_shareable() ) {
			$template = $wishlist->count_items() ? 'wishlist/wishlist.php' : 'wishlist/wishlist-empty.php';
		} else {
			$template = 'wishlist/wishlist-none.php';
		}

		return apply_filters( 'wcboost_wishlist_template', $template, $wishlist );
	}

	/**
	 * Get wishlist content template
	 *
	 * @param \WCBoost\Wishlist\Wishlist $wishlist
	 * @return string
	 */
	public static function get_wishlist_content_template( $wishlist ) {
		$layout   = self::get_wishlist_content_layout();
		$template = 'wishlist/wishlist-' . $layout . '.php';

		return apply_filters( 'wcboost_wishlist_content_template', $template, $wishlist );
	}

	/**
	 * Load wishlist template with args
	 *
	 * @param string $template Template name
	 * @param array $args Template arguments
	 * @param string $template_path Optional template path
	 * @return void
	 */
	public static function load_template( $template, $args = [], $template_path = '' ) {
		if ( empty( $template_path ) ) {
			$template_path = Plugin::instance()->plugin_path() . '/templates/';
		}

		wc_get_template( $template, $args, '', $template_path );
	}

	/**
	 * Get template HTML
	 *
	 * @param string $template Template name
	 * @param array $args Template arguments
	 * @param string $template_path Optional template path
	 * @return string
	 */
	public static function get_template_html( $template, $args = [], $template_path = '' ) {
		if ( empty( $template_path ) ) {
			$template_path = Plugin::instance()->plugin_path() . '/templates/';
		}

		return wc_get_template_html( $template, $args, '', $template_path );
	}

	/**
	 * Get wishlist template args
	 *
	 * @param \WCBoost\Wishlist\Wishlist
	 *
	 * @return array
	 */
	public static function get_wishlist_template_args( $wishlist ) {
		$args = [
			'wishlist'   => $wishlist,
			'return_url' => apply_filters( 'wcboost_wishlist_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ),
		];

		if ( get_query_var( 'edit-wishlist' ) ) {
			$args['show_title_field'] = wc_string_to_bool( get_option( 'wcboost_wishlist_page_show_title', 'no' ) );
			$args['show_desc_field']  = wc_string_to_bool( get_option( 'wcboost_wishlist_page_show_desc', 'no' ) );
		}

		return apply_filters( 'wcboost_wishlist_template_args', $args, $wishlist );
	}

	/**
	 * Get button template arguments
	 *
	 * @param \WCBoost\Wishlist\Wishlist $wishlist
	 * @param \WCBoost\Wishlist\Wishlist_Item $item
	 * @return array
	 */
	public static function get_button_template_args( $wishlist, $item ) {
		$product = $item->get_product();
		$args    = [
			'product_id' => $product->get_id(),
			'class'      => [ 'wcboost-wishlist-button' ],
			'url'        => $item->get_add_url(),
			/* translators: %s product name */
			'aria-label' => sprintf( __( 'Add %s to the wishlist', 'wcboost-wishlist' ), '&ldquo;' . $product->get_title() . '&rdquo;' ),
			'label'      => Helper::get_button_text(),
			'quantity'   => 1,
			'icon'       => Helper::get_wishlist_icon(),
		];

		// Button classes.
		$button_type = wc_get_theme_support( 'wishlist::button_type' );
		$button_type = $button_type ? $button_type : get_option( 'wcboost_wishlist_button_type', 'button' );

		$args['class'][] = 'wcboost-wishlist-button--' . $button_type;

		if ( 'text' != $button_type ) {
			$args['class'][] = 'button';

			if ( function_exists( 'wp_theme_get_element_class_name' ) ) {
				$args['class'][] = \wp_theme_get_element_class_name( 'button' );
			}
		}

		if ( wc_string_to_bool( get_option( 'wcboost_wishlist_enable_ajax_add_to_wishlist', 'yes' ) ) ) {
			$args['class'][] = 'wcboost-wishlist-button--ajax';
		}

		if ( $wishlist->has_item( $item ) ) {
			$args['class'][] = 'added';
			$args['icon']    = Helper::get_wishlist_icon( true );

			switch ( get_option( 'wcboost_wishlist_exists_item_button_behaviour', 'view_wishlist' ) ) {
				case 'hide':
					$args['class'][] = 'hidden';
					break;

				case 'remove':
					$args['url']        = $item->get_remove_url();
					/* translators: %s product name */
					$args['aria-label'] = sprintf( __( 'Remove %s from the wishlist', 'wcboost-wishlist' ), '&ldquo;' . $product->get_title() . '&rdquo;' );
					$args['label']      = Helper::get_button_text( 'remove' );
					break;

				case 'view_wishlist':
					$args['url']        = wc_get_page_permalink( 'wishlist' );
					$args['aria-label'] = __( 'Open the wishlist', 'wcboost-wishlist' );
					$args['label']      = Helper::get_button_text( 'view' );
					break;
			}
		} elseif ( ! $wishlist->is_default() ) {
			$args['url'] = add_query_arg( [ 'wishlist' => $wishlist->get_id() ], $args['url'] );
		}

		if ( wc_string_to_bool( get_option( 'wcboost_wishlist_allow_adding_variations' ) ) && $product->is_type( 'variable' ) ) {
			/** @var \WC_Product_Variable $product */
			$variations = $product->get_available_variations( 'objects' );
			$data       = [];

			// Add the parent product to the top of variation data.
			$data[] = [
				'variation_id' => $product->get_id(),
				'add_url'      => $item->get_add_url(),
				'remove_url'   => $item->get_remove_url(),
				'added'        => $wishlist->has_item( $item ) ? 'yes' : 'no',
				'is_parent'    => 'yes',
			];

			foreach ( $variations as $variation ) {
				$item   = new Wishlist_Item( $variation );
				$data[] = [
					'variation_id' => $variation->get_id(),
					'add_url'      => $item->get_add_url(),
					'remove_url'   => $item->get_remove_url(),
					'added'        => $wishlist->has_item( $item ) ? 'yes' : 'no',
				];
			}

			$args['variations_data'] = $data;
		}

		// Add a new key "attributes",
		// but must keep orginal keys to ensure backwards compatibility.
		$args['attributes'] = [
			'data-quantity'   => $args['quantity'],
			'data-product_id' => $args['product_id'],
			'aria-label'      => $args['aria-label'],
			'rel'             => 'nofollow',
		];

		if ( isset( $args['variations_data'] ) ) {
			$args['attributes']['data-variations'] = wp_json_encode( $args['variations_data'] );
		}

		$args = apply_filters( 'wcboost_wishlist_button_template_args', $args, $wishlist, $product );
		$args['class'] = implode( ' ', (array) $args['class'] );

		return $args;
	}

	/**
	 * Get wishlist content template args
	 *
	 * @param \WCBoost\Wishlist\Wishlist $wishlist
	 * @return array
	 */
	public static function get_content_template_args( $wishlist ) {
		$wishlist_layout  = self::get_wishlist_content_layout();
		$allow_variations = wc_string_to_bool( get_option( 'wcboost_wishlist_allow_adding_variations', 'no' ) );
		$args             = [
			'layout'              => $wishlist_layout,
			'wishlist'            => $wishlist,
			'show_variation_data' => apply_filters( 'wcboost_wishlist_show_variation_data', $allow_variations ),
		];

		if ( 'table' == $wishlist_layout ) {
			$default_columns  = [
				'price'    => 'yes',
				'stock'    => 'yes',
				'quantity' => 'no',
				'date'     => 'no',
				'purchase' => 'yes',
			];
			$columns = get_option( 'wcboost_wishlist_table_columns', $default_columns );
			$columns = wp_parse_args( $columns, $default_columns );

			$args['columns'] = array_map( 'wc_string_to_bool', $columns );
		}

		return apply_filters( 'wcboost_wishlist_content_template_args', $args, $wishlist );
	}

	/**
	 * Get wishlist header template args
	 *
	 * @param \WCBoost\Wishlist\Wishlist $wishlist
	 * @return array
	 */
	public static function get_header_template_args( $wishlist ) {
		$show_title = wc_string_to_bool( get_option( 'wcboost_wishlist_page_show_title', 'no' ) );
		$show_desc  = wc_string_to_bool( get_option( 'wcboost_wishlist_page_show_desc', 'no' ) );
		$args       = [
			'wishlist'      => $wishlist,
			'display_title' => $show_title,
			'display_desc'  => $show_desc,
		];

		return apply_filters( 'wcboost_wishlist_header_template_args', $args, $wishlist );
	}

	/**
	 * Get wishlist footer template args
	 *
	 * @param \WCBoost\Wishlist\Wishlist $wishlist
	 * @return array
	 */
	public static function get_footer_template_args( $wishlist ) {
		$args = [
			'wishlist' => $wishlist,
		];

		return apply_filters( 'wcboost_wishlist_footer_template_args', $args, $wishlist );
	}

	/**
	 * Get wishlist content layout
	 *
	 * @return string
	 */
	private static function get_wishlist_content_layout() {
		$supported_layouts = (array) apply_filters( 'wcboost_wishlist_supported_layouts', [ 'table' ] );
		$layout = apply_filters( 'wcboost_wishlist_layout', 'table' );
		$layout = in_array( $layout, $supported_layouts ) ? $layout : 'table';

		return $layout;
	}
}
