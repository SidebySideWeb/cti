<?php
/**
 * Single Product hooks.
 *
 * @package Glozin
 */

namespace Glozin\Addons\Modules\Advanced_Search;

use Glozin\Addons\Modules\Advanced_Search\Posts as Search_Posts;
use Glozin\Addons\Modules\Advanced_Search\Taxonomies as Search_Taxonomies;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Single Product
 */
class AJAX_Search {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * posts
	 *
	 * @var $instance
	 */
	protected static $posts = null;

	/**
	 * taxonomies
	 *
	 * @var $instance
	 */
	protected static $taxonomies = null;

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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'glozin_search_modal_before_form', array( $this, 'search_modal_results' ));
		add_action( 'wc_ajax_glozin_instance_search_form', array( $this, 'instance_search_form' ) );
	}

	public function enqueue_scripts() {
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script( 'glozin-ajax-search', GLOZIN_ADDONS_URL . 'modules/advanced-search/assets/ajax-search-frontend' . $debug . '.js',  array( 'jquery'), GLOZIN_ADDONS_VER, array('strategy' => 'defer') );

		$glozin_data = array(
			'ajax_url'             	=> class_exists( 'WC_AJAX' ) ? \WC_AJAX::get_endpoint( '%%endpoint%%' ) : '',
			'nonce'                	=> wp_create_nonce( '_glozin_nonce' ),
			'header_ajax_search' 	=> get_option( 'glozin_ajax_search', 'yes'),
			'header_search_number' 	=> get_option( 'glozin_ajax_search_number', 4),
		);

		wp_localize_script(
			'glozin-ajax-search', 'glozinAjaxSearch', $glozin_data
		);
	}

	public function search_modal_results() {
		?>
		<div class="modal__content-results mt-25"></div>
		<div class="modal__content-loading d-flex flex-column gap-15 mt-15">
			<div class="gz-trending d-flex flex-column gap-15">
				<div class="gz-trending-title bg-content-loading"></div>
				<div class="gz-trending-items d-flex gap-10">
					<div class="gz-trending-item bg-content-loading"></div>
					<div class="gz-trending-item bg-content-loading"></div>
					<div class="gz-trending-item bg-content-loading"></div>
				</div>
			</div>
			<div class="gz-products d-flex flex-nowrap overflow-hidden gz-row">
				<div class="gz-product-card d-flex flex-column gap-10 gz-col gz-col-12 gz-col-md-4 gz-col-xl-1-5">
					<div class="gz-product-card_img bg-content-loading"></div>
					<div class="gz-product-card__info d-flex flex-column align-items-center justify-content-center gap-10">
						<div class="gz-product-card_txt bg-content-loading"></div>
						<div class="gz-product-card_txt bg-content-loading"></div>
						<div class="gz-product-card_txt bg-content-loading"></div>
					</div>
				</div>
				<div class="gz-product-card d-flex flex-column gap-10 gz-col gz-col-12 gz-col-md-4 gz-col-xl-1-5">
					<div class="gz-product-card_img bg-content-loading"></div>
					<div class="gz-product-card__info d-flex flex-column align-items-center justify-content-center gap-10">
						<div class="gz-product-card_txt bg-content-loading"></div>
						<div class="gz-product-card_txt bg-content-loading"></div>
						<div class="gz-product-card_txt bg-content-loading"></div>
					</div>
				</div>
				<div class="gz-product-card d-flex flex-column gap-10 gz-col gz-col-12 gz-col-md-4 gz-col-xl-1-5">
					<div class="gz-product-card_img bg-content-loading"></div>
					<div class="gz-product-card__info d-flex flex-column align-items-center justify-content-center gap-10">
						<div class="gz-product-card_txt bg-content-loading"></div>
						<div class="gz-product-card_txt bg-content-loading"></div>
						<div class="gz-product-card_txt bg-content-loading"></div>
					</div>
				</div>
				<div class="gz-product-card d-flex flex-column gap-10 gz-col gz-col-12 gz-col-md-4 gz-col-xl-1-5">
					<div class="gz-product-card_img bg-content-loading"></div>
					<div class="gz-product-card__info d-flex flex-column align-items-center justify-content-center gap-10">
						<div class="gz-product-card_txt bg-content-loading"></div>
						<div class="gz-product-card_txt bg-content-loading"></div>
						<div class="gz-product-card_txt bg-content-loading"></div>
					</div>
				</div>
				<div class="gz-product-card d-flex flex-column gap-10 gz-col gz-col-12 gz-col-md-4 gz-col-xl-1-5">
					<div class="gz-product-card_img bg-content-loading"></div>
					<div class="gz-product-card__info d-flex flex-column align-items-center justify-content-center gap-10">
						<div class="gz-product-card_txt bg-content-loading"></div>
						<div class="gz-product-card_txt bg-content-loading"></div>
						<div class="gz-product-card_txt bg-content-loading"></div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Search form
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function instance_search_form() {
		$output_products	 = Helper::get('posts')->get_products_base();
		$output_taxonomies 	 = Helper::get('taxonomies')->get_cats();
		$output_pages		 = Helper::get('posts')->get_pages();
		$output_posts		 = Helper::get('posts')->get_posts();

		$response = '';
		$_response = '';

		if( ! empty( $output_taxonomies ) ) {
			$_response .= $output_taxonomies;
		}

		if( ! empty( $output_pages ) ) {
			$_response .= $output_pages;
		}

		if( ! empty( $output_posts ) ) {
			$_response .= $output_posts;
		}

		$swiper_options = array(
			'slidesPerView' => array(
				'desktop' => ! empty( $_response ) ? 4 : 5,
				'tablet' => 3,
				'mobile' => 1,
			),
			'spaceBetween' => array(
				'desktop' => 20,
				'tablet' => 20,
				'mobile' => 15,
			),
		);

		if ( empty( $output_products['products'] ) ) {
			$empty_icon = \Glozin\Addons\Helper::get_svg( 'search-not-found' );
			$response .= sprintf( '<div class="list-item list-item-empty text-center">
										<div class="list-item-empty__icon fs-60 lh-1">%s</div>
										<p class="list-item-empty__text">%s</p>
									</div>',
									$empty_icon,
									esc_html__( "Sorry, we couldn’t find any matching results for this search. These popular product might interest you.", 'glozin-addons' )
								);
		} else {
			$response .= sprintf( '<div class="results-content-title fs-18 mb-20 heading-letter-spacing lh-normal">%s <span class="text-dark fw-medium">"%s"</span></div>', esc_html__( 'Search for ', 'glozin-addons' ), esc_attr( $_POST['term'] ) );

			if( ! empty( Helper::get_suggestions_text() ) ) {
				$response .= Helper::get_suggestions_text();
			}

			$response .= '<div class="results-content-products glozin-swiper swiper glozin-product-carousel gz-arrows-middle navigation-class--tabletdots navigation-class--mobiledots" data-swiper=' . esc_attr( json_encode( $swiper_options ) ) . ' data-desktop="' . esc_attr( $swiper_options['slidesPerView']['desktop'] ) . '" data-tablet="' . esc_attr( $swiper_options['slidesPerView']['tablet'] ) . '" data-mobile="' . esc_attr( $swiper_options['slidesPerView']['mobile'] ) . '">'
			. $output_products['products']
			. \Glozin\Icon::inline_svg( [ 'icon' => 'icon-next', 'class' => 'swiper-button swiper-button-next' ] )
			. \Glozin\Icon::inline_svg( [ 'icon' => 'icon-back', 'class' => 'swiper-button swiper-button-prev' ] )
			. '<div class="swiper-pagination"></div>'
			. '</div>';

			$response .= $output_products['view_all'];

			if( ! empty( $_response ) ) {
				$_response = '<div class="results-content__left d-flex flex-column border-top border-md-none pt-30 pt-md-0 gap-30">' . $_response . '</div>';
				$response = '<div class="results-content d-flex flex-column flex-md-row gap-30">' . $_response . '<div class="results-content__right flex-grow-1">' . $response . '</div></div>';
			}
		}

		wp_send_json_success( $response );
		die();
	}
}