<?php

namespace Glozin\Addons\Modules\Live_Sales_Notification\Navigation;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main class of plugin for admin
 */
class Orders_Fake {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private static $instance;
	private $first_name;
    private $address;
    private $country;
    private $state;
    private $city;
    private $time;

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
		$this->set_first_name();
		$this->set_location();
		$this->set_time();
	}

    public function set_first_name() {
        $first_names = $this->get_custom_first_names();
        $this->first_name = $first_names;
    }

    /**
     * This provide first_name from custom names given by user
     */
    public function get_custom_first_names() {
        $names_string = get_option( 'glozin_live_sales_notification_name' );

        if( empty( $names_string ) ) {
            $names_string = esc_html__( 'Someone', 'glozin-addons' );
        }

        $names_array    = explode( PHP_EOL, $names_string );
        $selected_index = trim( array_rand( $names_array ) );
        $name           = $names_array[$selected_index];

        return $name;
    }

    /**
     * This provide location from location stored by user
     */
    public function get_custom_location() {
        $location_string = get_option( 'glozin_live_sales_notification_location' );
        $location = '';

        if( ! empty($location_string) ) {
            $locations_array = explode( PHP_EOL, $location_string );

            foreach( $locations_array as $location_array) {
                $locations[] = explode( ',', $location_array );
            }

            $location_index = array_rand( $locations );
            $location       = $locations[$location_index];
        }

        return $location;
    }

    public function set_location() {
        $location = $this->get_custom_location();

        if( ! empty( $location ) ) {
            $this->city    = isset($location[0]) ? trim($location[0]): "";
            $this->state   = isset($location[1]) ? trim($location[1]): "";
            $this->country = isset($location[2]) ? trim($location[2]): "";

            $this->address = array(
                $this->city,
                $this->state,
                $this->country
            );
        }
    }

    public function set_time() {
        $time_type  = get_option( 'glozin_live_sales_notification_time_type', 'day' );
        $time       = get_option( 'glozin_live_sales_notification_time', 1 );
        $order_time = '-' . $time .' '. $time_type;
        $present 	= time();

        $till = strtotime( $order_time );
        $random_time = rand( $till, $present );

        $this->time = date( 'Y/m/d H:i:s',  $random_time );
    }

    /**
     *  This function name are same as used by WooCommerce order object functions
    */
    public function get_billing_first_name() {
        return $this->first_name;
    }

    public function get_billing_city() {
        return $this->city;
    }

    public function get_billing_state() {
        return $this->state;
    }

    public function get_billing_country() {
        return $this->country;
    }

    public function get_date_created() {
        $obj = new \WC_DateTime( $this->time );
        return $obj;
    }

}