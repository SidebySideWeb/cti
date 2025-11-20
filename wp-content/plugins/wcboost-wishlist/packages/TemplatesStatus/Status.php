<?php
/**
 * Template status class
 *
 * @version 1.1.0
 *
 * @package WCBoost\Packages\TemplatesStatus
 */
namespace WCBoost\Packages\TemplatesStatus;

use WCBoost\Packages\TemplatesStatus\TemplatesTrait;
use WCBoost\Packages\Utilities\SingletonTrait;

/**
 * Class \WCBoost\Packages\TemplatesStatus\Status
 */
class Status {

	use SingletonTrait;
	use TemplatesTrait;

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_filter( 'pre_set_transient_wc_system_status_theme_info', [ $this, 'theme_templates_info' ] );

		add_action( 'switch_theme', [ $this, 'delete_templates_status_cache' ] );
	}

	/**
	 * Get the updated theme info with custom WooCommerce templates provided by WCBoost Wishlist
	 *
	 * @since  1.0.0
	 * @param  array $info
	 *
	 * @return array
	 */
	public function theme_templates_info( $info ) {
		$templates_info = $this->check_override_templates();

		if ( null === $templates_info ) {
			return $info;
		}

		// Update the 'has_outdated_templates' status only if
		// the theme contains plugins' templates.
		if ( $templates_info['outdated'] ) {
			$info['has_outdated_templates'] = true;
		}

		// Merge the override templates array.
		if ( ! empty( $templates_info['files'] ) ) {
			$info['overrides'] = array_merge( $info['overrides'], $templates_info['files'] );
		}

		return $info;
	}
}
