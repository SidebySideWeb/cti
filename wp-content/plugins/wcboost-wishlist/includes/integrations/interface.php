<?php
/**
 * Integration interface
 *
 * @package WCBoost\Wishlist
 */
namespace WCBoost\Wishlist\Integrations;

defined( 'ABSPATH' ) || exit;

/**
 * Interface \WCBoost\Wishlist\Integrations\Integration_Interface
 */
interface Integration_Interface {

	/**
	 * Determine if the integration should be loaded
	 *
	 * @return bool
	 */
	public function should_load();

	/**
	 * Load the integration
	 *
	 * @return void
	 */
	public function load();
}
