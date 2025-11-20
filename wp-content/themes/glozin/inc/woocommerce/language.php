<?php
/**
 * Hooks of Language.
 *
 * @package Glozin
 */

namespace Glozin\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Wishlist template.
 */
class Language {
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
	 * Return boolean language switcher
	 *
	 * @return void
	 */
	public static function language_status() {
		return apply_filters( 'wpml_active_languages', array() );
	}

	/**
	 * Print HTML of language switcher
	 * It requires plugin WPML installed
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function language_switcher( $display = 'list' ) {
		$languages = self::language_status();
		$lang_list = array();
		$current   = '';

		if ( empty( $languages ) ) {
			return;
		}

		\Glozin\Theme::set_prop( 'popovers', 'language' );

		if( $display == 'list' ) {
			foreach ( (array) $languages as $code => $language ) {
				if ( ! $language['active'] ) {
					$lang_list[] = sprintf(
						'<li class="glozin-language__menu-item py-4 %s"><a class="no-underline text-base text-hover-color" href="%s">%s</a></li>',
						esc_attr( $code ),
						esc_url( $language['url'] ),
						esc_html( $language['native_name'] )
					);
				} else {
					$current = $language;
					array_unshift( $lang_list, sprintf(
						'<li class="glozin-language__menu-item py-4 %s"><a class="no-underline text-dark text-hover-color active" href="%s">%s</a></li>',
						esc_attr( $code ),
						esc_url( $language['url'] ),
						esc_html( $language['native_name'] )
					) );
				}
			}

			if( $current && isset( $current['native_name'] ) ) {
				echo sprintf(
					'<div class="current">%s %s</div>',
					$current['native_name'],
					\Glozin\Icon::get_svg('arrow-bottom')
				);
			}

			echo '<div class="currency-dropdown invisible shadow position-absolute top-0 end-0">';
			echo '<ul class="preferences-menu__item-child list-unstyled">';
				echo implode( "\n\t", $lang_list );
			echo '</ul>';
			echo '</div>';
		} else {
			?>
			<label><?php esc_html_e( 'Language', 'glozin' ); ?></label>
			<select name="language" id="glozin_language" class="language_select preferences_select">
				<?php
				foreach ( (array) $languages as $key => $language ) {
					$current_language = ! empty( $language['active'] ) ? esc_attr( $key ) : '';
					echo '<option value="' . esc_url( $language['url'] ) . '"' . selected( $current_language, esc_attr( $key ), false ) . '>' . esc_html( $language['native_name'] ) . '</option>';
				}
				?>
			</select>
			<?php
		}
	}
}
