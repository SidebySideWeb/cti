<?php

namespace Glozin\Addons\Modules\Inventory;

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
		add_filter('posts_clauses', array($this, 'order_by_stock_status'), 2000, 2);
	}

	public function order_by_stock_status($posts_clauses, $query){
		if (\Glozin\Addons\Helper::is_catalog() && $query->is_main_query() && ! is_admin()) {
			$posts_clauses = $this->order_by_stock_clauses($posts_clauses);
		}

		return $posts_clauses;

	}

	public function order_by_stock_clauses($posts_clauses){
		global $wpdb;
		$posts_clauses['join'] .= " INNER JOIN $wpdb->postmeta istockstatus ON ($wpdb->posts.ID = istockstatus.post_id) ";
		$posts_clauses['orderby'] = " istockstatus.meta_value ASC, " . $posts_clauses['orderby'];
		$posts_clauses['where'] = " AND istockstatus.meta_key = '_stock_status' AND istockstatus.meta_value <> '' " . $posts_clauses['where'];

		return $posts_clauses;
	}

}