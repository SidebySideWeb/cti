<?php
/**
 * Translate Press compatibility functions
 *
 * @package Glozin
 */

namespace Glozin\Languages;

use \Glozin\Helper;

class TRP {
	/**
	 * The single instance of the class
	 *
	 * @var Translate Press
	 */
	protected static $instance = null;

	protected static $languages = null;

	protected static $trp_languages = null;

	/**
	 * Main instance
	 *
	 * @return Translate Press
	 */
	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'wpml_active_languages', array( $this, 'languages' ) );
	}

	public function languages() {
		if( isset( self::$languages )  ) {
			return self::$languages;
		}
		$trp = \TRP_Translate_Press::get_trp_instance();

		if( ! $trp ) {
			return;
		}

        $trp_languages = $trp ? $trp->get_component( 'languages' ) : '';

		if( ! $trp_languages ) {
			return;
		}

		$default = get_locale();
        if ( empty( $default ) ){
            $default = 'en_US';
        }
		$settings_option = get_option( 'trp_settings', 'not_set' );
		$languages_to_display = array( $default );
		if( $settings_option ){
			if ( current_user_can(apply_filters( 'trp_translating_capability', 'manage_options' )) ){
				$languages_to_display = $settings_option['translation-languages'];
			}else{
				$languages_to_display = $settings_option['publish-languages'];

			}
		}
        $published_languages = $trp_languages->get_language_names( $languages_to_display );

		$current_language = array();
		$other_languages = array();
		global $TRP_LANGUAGE;
		foreach( $published_languages as $code => $name ) {
			if( $code == $TRP_LANGUAGE ) {
				$current_language['code'] = $code;
				$current_language['name'] = $name;
			} else {
				$other_languages[$code] = $name;
			}
		}

		$languages = array();
		$url_converter = $trp->get_component('url_converter');

		if( $current_language ) {
			$languages[$current_language['code']] = array(
				'active' => '1',
				'country_flag_url' => $this->add_flag($current_language['code'], $current_language['name']),
				'url'              => $url_converter  ? esc_url( $url_converter->get_url_for_language($current_language['code'], false) ) : '',
				'native_name'      => $current_language['name'],
				'language_code'    => strtok($current_language['code'], '_')
			);
		}

		if( $other_languages ) {
			foreach( $other_languages as $code => $name ) {
				$languages[$code] = array(
					'active' => '0',
					'country_flag_url' => $this->add_flag($code, $name),
					'url'              => $url_converter  ? esc_url( $url_converter->get_url_for_language($code, false, '') ) : '',
					'native_name'      => $name,
					'language_code'    => strtok($code, '_')
				);
			}
		}

		self::$languages = $languages;

		return self::$languages;
	}

	public function add_flag( $language_code, $language_name, $location = NULL ) {
        // Path to folder with flags images
        $flags_path = TRP_PLUGIN_URL .'assets/images/flags/';
        $flags_path = apply_filters( 'trp_flags_path', $flags_path, $language_code );

        // File name for specific flag
        $flag_file_name = $language_code .'.png';
        $flag_file_name = apply_filters( 'trp_flag_file_name', $flag_file_name, $language_code );
        $flag_html = $flags_path . $flag_file_name;

		return $flag_html;
    }
}