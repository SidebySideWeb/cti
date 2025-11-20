<?php
/**
 * Hooks of Currency.
 *
 * @package Glozin
 */

namespace Glozin\WooCommerce;
use Glozin\Icon;

use \Glozin\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Wishlist template.
 */
class Currency {
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


	public static function currency_status() {
		if( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		$args = array();
		if( apply_filters( 'glozin_woocs_active', class_exists( 'WOOCS' ) ) ) {
			global $WOOCS;

			$currencies 			= $WOOCS ? $WOOCS->get_currencies() : array();
			$currencies 			= apply_filters( 'woocs_active_currencies', $currencies );
			$current_currency 		= $WOOCS ? $WOOCS->current_currency : '';
			$current_currency 		= apply_filters( 'woocs_current_currencies', $current_currency );
			$symbol_currency    	= get_woocommerce_currency_symbol( $current_currency );
			$description_currency 	= $current_currency && $currencies[$current_currency]['description'] ? $currencies[$current_currency]['description'] : '';
			$flag_currency 			= $current_currency && $currencies[$current_currency]['flag'] ? $currencies[$current_currency]['flag'] : '';

			if( ! empty($currencies) ) {
				$args = array(
					'currencies'       		=> $currencies,
					'current_currency' 		=> $current_currency,
					'symbol_currency'  		=> $symbol_currency,
					'description_currency' 	=> $description_currency,
					'flag_currency' 		=> $flag_currency,
				);
			}
		} elseif( class_exists('\Aelia\WC\CurrencySwitcher\WC_Aelia_CurrencySwitcher') ) {
			$settings_controller = \Aelia\WC\CurrencySwitcher\WC_Aelia_CurrencySwitcher::settings();

			$enabled_currencies = $settings_controller->get_enabled_currencies();
			$exchange_rates = $settings_controller->get_exchange_rates();

			$woocommerce_currencies = get_woocommerce_currencies();
			$currencies = array();
			foreach($exchange_rates as $currency => $fx_rate) {
				// Display only Currencies supported by WooCommerce
				$currency_name = !empty($woocommerce_currencies[$currency]) ? $woocommerce_currencies[$currency] : false;
				if(!empty($currency_name)) {
					// Skip currencies that are not enabled
					if(!in_array($currency, $enabled_currencies)) {
						continue;
					}

					// Display only currencies with a valid Exchange Rate
					if($fx_rate > 0) {
						$currencies[$currency] = $currency_name;
					}
				}
			}

			if( ! empty($currencies) ) {
				$args = array(
					'currencies'       => $currencies,
					'current_currency' => \Aelia\WC\CurrencySwitcher\WC_Aelia_CurrencySwitcher::instance()->get_selected_currency(),
					'symbol_currency'  => '',
				);
			}
		}

		return $args;
	}

	public static function currency_switcher() {
		self::woocs_currency_switcher();
	}

	public static function woocs_currency_switcher() {
		$args = self::currency_status();
		$currency_list = array();

		if( empty( $args ) ) {
			return;
		}

		\Glozin\Theme::set_prop( 'popovers', 'currency' );

		$currencies = $args['currencies'];
		$current_currency = $args['current_currency'];
		$symbol_currency = $args['symbol_currency'];
		$description_currency = $args['description_currency'];
		$flag_currency = $args['flag_currency'] ? '<span class="woocs-flag"><img src="'. $args['flag_currency'] .'" alt="'. $description_currency .'"/></span>' : '';

		foreach ( $currencies as $key => $currency ) {
			if ( $current_currency == $key ) {
				array_unshift( $currency_list, sprintf(
					'<li class="glozin-currency__menu-item py-4 active">
						<a href="#" class="woocs_flag_view_item woocs_flag_view_item_current no-underline text-dark text-hover-color active" data-currency="%s">
							<span class="woocs-flag"><img src="%s" alt="%s"/></span>
							%s (%s %s)
						</a>
					</li>',
					esc_attr( $currency['name'] ),
					esc_url( $currency['flag'] ),
					esc_attr( $currency['name'] ),
					esc_html( $currency['description'] ),
					esc_html( $currency['name'] ),
					esc_html( $currency['symbol'] )
				) );
			} else {
				$currency_list[] = sprintf(
					'<li class="glozin-currency__menu-item py-4">
						<a href="#" class="woocs_flag_view_item no-underline text-base text-hover-color" data-currency="%s">
							<span class="woocs-flag"><img src="%s" alt="%s"/></span>
							%s (%s %s)
						</a>
					</li>',
					esc_attr( $currency['name'] ),
					esc_url( $currency['flag'] ),
					esc_attr( $currency['name'] ),
					esc_html( $currency['description'] ),
					esc_html( $currency['name'] ),
					esc_html( $currency['symbol'] )
				);
			}
		}

		$currency_list = apply_filters('glozin_currencies_list', $currency_list, $currencies, $current_currency);

		echo sprintf(
			'<div class="current">%s %s (%s %s) %s</div>',
			$flag_currency,
			$description_currency,
			$current_currency,
			$symbol_currency,
			\Glozin\Icon::get_svg('arrow-bottom')
		);

		echo '<div class="currency-dropdown invisible shadow position-absolute top-0 end-0">';
		echo '<ul class="preferences-menu__item-child list-unstyled">';
			echo implode( "\n\t", $currency_list );
		echo '</ul>';
		echo '</div>';
	}

}
