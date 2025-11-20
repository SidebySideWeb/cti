<?php
/**
 * Helper hooks.
 *
 * @package Glozin
 */

namespace Glozin\Addons\Modules\Advanced_Search;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Helper
 */
class Helper {
	public static function get_result_item($result) {
		return sprintf(
			'<li class="result-item">' .
			'<a class="result-title" href="%s">' .
			'%s' .
			'</a>' .
			'</li>',
			esc_url( $result['permalink'] ),
			$result['name'],
		);

	}

	public static function get_result_list($result) {
		return sprintf(
			'<div class="%s">'.
			'<h5 class="heading-letter-spacing my-0">%s</h5>'.
			'<ul class="results-list list-unstyled mt-15">'.
			'%s'.
			'</ul>'.
			'</div>',
			esc_attr($result['classes']),
			$result['name'],
			$result['response']
		);

	}

	public static function get( $class ) {
		if( $class == 'posts' ) {
			return \Glozin\Addons\Modules\Advanced_Search\Ajax_Search\Posts::instance();
		} elseif( $class == 'taxonomies' ) {
			return \Glozin\Addons\Modules\Advanced_Search\Ajax_Search\Taxonomies::instance();
		}
	}

	/**
	 * Loop over products
	 *
	 * @since 1.0.0
	 *
	 * @param string
	 */
	public static function get_template_loop( $products_ids, $template = 'product' ) {
		if( empty( $products_ids ) ) {
			return;
		}
		update_meta_cache( 'post', $products_ids );
		update_object_term_cache( $products_ids, 'product' );

		$original_post = $GLOBALS['post'];

		woocommerce_product_loop_start();

		foreach ( $products_ids as $product_id ) {
			$GLOBALS['post'] = get_post( $product_id ); // WPCS: override ok.
			setup_postdata( $GLOBALS['post'] );
			wc_get_template_part( 'content', $template );
		}

		$GLOBALS['post'] = $original_post; // WPCS: override ok.

		woocommerce_product_loop_end();

		wp_reset_postdata();
		wc_reset_loop();
	}

	/**
	 * Get suggestions text
	 *
	 */
	public static function get_suggestions_text( $search_key = '' ) {
		if( ! empty( $_POST['term'] ) ) {
			$search_key = trim( $_POST['term'] );
		}
		$suggestions = get_option( 'glozin_ajax_search_suggestions', 'yes' );
		$suggestions_type = get_option( 'glozin_ajax_search_suggestions_type', [ 'best_selling' ] );
		$suggestions_number = get_option( 'glozin_ajax_search_suggestions_number', 5 );
		if( $suggestions !== 'yes' ) {
			return;
		}

		if( empty( $suggestions_type ) ) {
			return;
		}

		$suggestions_text = [];
		$product_ids = [];

		$args = array(
			'post_type' => 'product',
			'post_status' => 'publish',
			'posts_per_page' => -1,
		);

		if( in_array( 'recent', $suggestions_type ) ) {
			$product_ids = array_merge( $product_ids, ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] ) : array() );
		}

		if( in_array( 'featured', $suggestions_type ) ) {
			$product_ids = array_merge( $product_ids, wc_get_featured_product_ids() );
		}

		if( in_array( 'best_selling', $suggestions_type ) ) {
			$args_best_selling = array_merge( array(
				'meta_key' => 'total_sales',
				'order'    => 'DESC',
				'orderby'  => 'meta_value_num',
			), $args );

			$query = new \WP_Query( $args_best_selling );

			while ( $query->have_posts() ) { $query->the_post();
				$product_ids[] = get_the_ID();
			}

			wp_reset_query();
		}

		if( in_array( 'top_rated', $suggestions_type ) ) {
			$args_top_rated = array_merge( array(
				'meta_key' => '_wc_average_rating',
				'order'    => 'DESC',
				'orderby'  => 'meta_value_num',
			), $args );

			$query = new \WP_Query( $args_top_rated );

			while ( $query->have_posts() ) { $query->the_post();
				$product_ids[] = get_the_ID();
			}

			wp_reset_query();
		}

		if( in_array( 'sale', $suggestions_type ) ) {
			$product_ids = array_merge( $product_ids, wc_get_product_ids_on_sale() );
		}

		$product_ids = array_map( 'intval', $product_ids );
		$product_ids = array_unique( $product_ids );

		$matching_titles = [];
		if( ! empty( $search_key ) ) {
			foreach ($product_ids as $product_id) {
				$product = wc_get_product($product_id);

				if ( empty( $product ) ) {
					return;
				}

				if (stripos($product->get_title(), $search_key) !== false) {
					$matching_titles = array_merge($matching_titles, self::get_matching_substrings($product->get_title(), $search_key));
				}
			}
		}

		if( ! empty( $matching_titles ) ) {
			$matching_titles = array_unique($matching_titles);
			usort($matching_titles, function($a, $b) {
				return strlen($a) - strlen($b);
			});
			for($i = 0; $i < $suggestions_number; $i++) {
				if( ! empty( $matching_titles[$i] ) ) {
					$suggestions_text[] = sprintf( '<a class="result-title" href="%s">%s</a>', esc_url( home_url( '/' ) . '?s=' . urlencode(strip_tags($matching_titles[$i])). '&post_type=product' ), wp_kses_post( $matching_titles[$i] ) );
				}
			}
		}

		if( ! empty( $suggestions_text ) && is_array( $suggestions_text ) && count($suggestions_text) > 1 ) {
			return sprintf( '<div class="results-content-title h5 mt-0 mb-10 heading-letter-spacing lh-normal">%s</div>', esc_html__( 'Suggestions', 'glozin-addons' ) ) . '<div class="results-content-suggestions d-flex flex-wrap gap-20 mb-22">' . implode( '', $suggestions_text ) . '</div>';
		}

		return;
	}

	/**
	 * Get matching substrings
	 *
	 * @param string $product_name
	 * @param string $search_keyword
	 * @return array
	 */
	public static function get_matching_substrings($product_name, $search_key) {
		$args = [];
		$product_name = strtolower($product_name);
		$search_key = strtolower($search_key);

		if( $product_name === $search_key ) {
			return $args;
		}
		
		if (stripos($product_name, $search_key) == false) {
			return $args;
		}
		
		// If search key contains spaces, find and highlight the exact phrase
		if (strpos($search_key, ' ') !== false) {
			// Find all occurrences of the search key in the product name
			$pattern = '/(' . preg_quote($search_key, '/') . ')/i';
				
			// Get the context around the match (words before and after if available)
			$words = explode(' ', $product_name);
			$search_words = explode(' ', $search_key);
			$search_word_count = count($search_words);
			
			for ($i = 0; $i < count($words) - $search_word_count + 1; $i++) {
				$potential_match = '';
				for ($j = 0; $j < $search_word_count; $j++) {
					$potential_match .= ($j > 0 ? ' ' : '') . $words[$i + $j];
				}
				
				if (strtolower($potential_match) === $search_key) {
					// Add context before if available
					if ($i > 0) {
						$context_before = $words[$i - 1] . ' ' . $potential_match;
						$args[] = preg_replace($pattern, '<mark>$1</mark>', $context_before);

						if( ! empty( $words[$i - 2] ) ) {
							$context_before = $words[$i - 2] . ' ' . $words[$i - 1] . ' ' . $potential_match;
							$args[] = preg_replace($pattern, '<mark>$1</mark>', $context_before);
						}
					}
					
					// Add context after if available
					if ($i + $search_word_count < count($words)) {
						$context_after = $potential_match . ' ' . $words[$i + $search_word_count];
						$args[] = preg_replace($pattern, '<mark>$1</mark>', $context_after);

						if( ! empty( $words[$i + $search_word_count + 1] ) ) {
							$context_after = $potential_match . ' ' . $words[$i + $search_word_count] . ' ' . $words[$i + $search_word_count + 1];
							$args[] = preg_replace($pattern, '<mark>$1</mark>', $context_after);
						}
					}
					
					// Add the exact match
					//$args[] = preg_replace($pattern, '<mark>$1</mark>', $potential_match);
				}
			}
		} else {
			// Original logic for single word search keys
			$words = explode(' ', $product_name);
			foreach ($words as $key => $word) {
				if (strpos($word, $search_key) !== false) {
					if (count($words) > 1) {
						if (end($words) === $word) {
							$args[] = preg_replace('/(' . preg_quote($search_key, '/') . ')/i', '<mark>$1</mark>', $words[intval($key) - 1] . ' ' . $word);

							if( ! empty( $words[intval($key) - 2] ) ) {
								$args[] = preg_replace('/(' . preg_quote($search_key, '/') . ')/i', '<mark>$1</mark>', $words[intval($key) - 2] . ' ' . $words[intval($key) - 1] . ' ' . $word);
							}

							if( ! empty( $words[intval($key) - 3] ) ) {
								$args[] = preg_replace('/(' . preg_quote($search_key, '/') . ')/i', '<mark>$1</mark>', $words[intval($key) - 3] . ' ' . $words[intval($key) - 2] . ' ' . $words[intval($key) - 1] . ' ' . $word);
							}
						} else {
							$args[] = preg_replace('/(' . preg_quote($search_key, '/') . ')/i', '<mark>$1</mark>', $word . ' ' . $words[intval($key) + 1]);

							if( ! empty( $words[intval($key) + 2] ) ) {
								$args[] = preg_replace('/(' . preg_quote($search_key, '/') . ')/i', '<mark>$1</mark>', $word . ' ' . $words[intval($key) + 1] . ' ' . $words[intval($key) + 2]);
							}

							if( ! empty( $words[intval($key) + 3] ) ) {
								$args[] = preg_replace('/(' . preg_quote($search_key, '/') . ')/i', '<mark>$1</mark>', $word . ' ' . $words[intval($key) + 1] . ' ' . $words[intval($key) + 2] . ' ' . $words[intval($key) + 3]);
							}
						}
					}

					//$args[] = preg_replace('/(' . preg_quote($search_key, '/') . ')/i', '<mark>$1</mark>', $word);
				}
			}
		}

		return $args;
	}
}