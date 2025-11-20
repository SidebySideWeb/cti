<?php
/**
 * Glozin Addons Elementor Builder Helper init
 *
 *
 * @package Glozin
 */

namespace Glozin\Addons\Elementor\Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Helper
 */
class Helper {

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

	public static function glozin_get_terms() {
		$terms = get_the_terms( get_the_ID(), 'glozin_builder_type' );
        $terms = ! is_wp_error( $terms ) &&  $terms ? wp_list_pluck($terms, 'slug') : [];

		return $terms;
	}

	public static function is_preview_mode(){
		if( self::is_elementor_editor_mode() || get_post_type() === 'glozin_builder' ) {
			return true;
		} else {
			return false;
		}
	}

	public static function is_elementor_editor_mode(){
		if( class_exists('\Elementor\Plugin') && \Elementor\Plugin::instance()->editor->is_edit_mode() ){
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get template elementor settings
	 *
	 * @return void
	 */
	public static function get_template_settings( $template_id ) {
		$page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );
		$page_settings_model = $page_settings_manager->get_model( $template_id );

		return $page_settings_model->get_settings();
	}

	public static function is_catalog() {
		if ( class_exists( '\Glozin\Helper' ) && method_exists( '\Glozin\Helper', 'is_catalog' ) ) {
			return \Glozin\Helper::is_catalog();
		}

		return false;
	}

	public static function get_redirect_template($template, $template_part, $template_id) {
		if( ! empty( $template_id ) ) {
			$document        = \Elementor\Plugin::$instance->documents->get_doc_for_frontend($template_id);
			$template_module = \Elementor\Plugin::$instance->modules_manager->get_modules('page-templates');

			if( $document && $document::get_property('support_wp_page_templates') ) {
				$page_template = $document->get_meta('_wp_page_template');
				$page_template = ( in_array( $page_template, ['elementor_header_footer', 'elementor_canvas'] ) ? $page_template : 'elementor_header_footer');

				$template_path = $template_module->get_template_path( $page_template );

				if( 'elementor_theme' !== $page_template && !$template_path && $document->is_built_with_elementor() ) {
					$kit_default_template = \Elementor\Plugin::$instance->kits_manager->get_current_settings('default_page_template');
					$template_path        = $template_module->get_template_path( $kit_default_template );
				}

				if( $template_path ) {
					$template = $template_path;
				}
			}

			$template_module->set_print_callback(function () use ( $template_id, $template_part ){
				include_once ( self::get_template_part( $template_part, $template_id ) );
			});
		} else {
			$template = self::get_template_part( $template_part, $template_id );
		}
		return $template;
	}

	public static function get_template_part( $slug, $template_id ){
		$template = '';
      	if( $slug === 'product' ) {
			if( empty( $template_id ) ) {
				$template = GLOZIN_ADDONS_DIR . 'inc/elementor/builder/templates/single-template-builder-empty.php';
			} else {
				$template = GLOZIN_ADDONS_DIR . 'inc/elementor/builder/templates/single-template-builder.php';
			}
        }

		if( $slug === 'archive' ) {
			$template = GLOZIN_ADDONS_DIR . 'inc/elementor/builder/templates/archive-template-builder.php';
        }

		if( $slug === 'cart_page' ) {
			$template = GLOZIN_ADDONS_DIR . 'inc/elementor/builder/templates/archive-template-builder.php';
        }

		if( $slug === 'checkout_page' ) {
			$template = GLOZIN_ADDONS_DIR . 'inc/elementor/builder/templates/archive-template-builder.php';
        }

		if( $slug === '404_page' ) {
			$template = GLOZIN_ADDONS_DIR . 'inc/elementor/builder/templates/archive-template-builder.php';
        }

		return $template;
    }

	public static function check_elementor_css_print_method() {
		if( get_option( 'elementor_css_print_method' ) == 'internal' ){
			return true;
		}

		return false;
	}
}