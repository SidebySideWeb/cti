<?php
/**
 * Display single product reviews (comments)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product-reviews.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.7.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! comments_open() ) {
	return;
}

?>
<div id="reviews" class="woocommerce-Reviews">
	<h2 class="woocommerce-Reviews-title mt-0 fs-24 text-center">
		<?php
		$count = $product->get_review_count();
		if ( $count && wc_review_ratings_enabled() ) {
			/* translators: 1: reviews count 2: product name */
			$reviews_title = sprintf( esc_html( _n( '%1$s review for %2$s', '%1$s reviews for %2$s', $count, 'woocommerce' ) ), esc_html( $count ), '<span>' . get_the_title() . '</span>' );
			echo apply_filters( 'woocommerce_reviews_title', $reviews_title, $count, $product ); // WPCS: XSS ok.
		} else {
			esc_html_e( 'Customer Reviews', 'glozin' );
		}
		?>
	</h2>
	<?php
	if ( have_comments() ) :
		$rating_arr     = $product->get_rating_counts();
		$rating_count   = $product->get_rating_count();
		$review_count   = $product->get_review_count();
		$average        = $product->get_average_rating();
		$average_rating = round( $average, 2 );
	?>
		<div class="glozin-product-rating d-flex flex-column flex-lg-row justify-content-center align-items-center align-items-lg-stretch gap-24 gap-lg-0">
			<div class="glozin-product-rating__count d-inline-flex flex-column justify-content-center align-items-center align-items-lg-start gap-15 lh-1">
				<div class="glozin-product-rating__count-rating d-inline-flex align-items-center gap-10">
					<?php echo wc_get_rating_html( $average, $rating_count ); ?>
					<div class="glozin-product-rating__count-rating-text">
						<?php echo sprintf( '%s out of 5', $average_rating ); ?>
					</div>
				</div>
				<div class="glozin-product-rating__count"><?php echo sprintf( _n( 'Based on %s review', 'Based on %s reviews', $review_count, 'glozin' ), esc_html( $review_count ) ); ?></div>
			</div>
			<div class="glozin-product-rating__bar d-inline-flex flex-column justify-content-center lh-1 border-none border-lg-start">
				<?php
					for ( $i = 5; $i > 0; $i -- ) {
						$count = 0;
						if ( isset( $rating_arr[$i] ) ) {
							$count = $rating_arr[$i];
						}
				?>
						<div class="star-item <?php echo esc_attr($i) ?>-stars">
							<div class="slabel">
								<?php echo wc_get_rating_html( $i, 0 ); ?>
							</div>
							<div class="sbar">
								<div class="bar-content">
									<span class="bar-star bar-star--<?php echo esc_attr($i) ?> <?php echo esc_attr( $count > 0 ? 'active' : '' ); ?>"></span>
								</div>
							</div>
							<div class="svalue flex-1 text-center"><?php echo esc_html( $count ); ?></div>
						</div>
				<?php
					}
				?>
			</div>
			<div class="glozin-product-rating__button d-inline-flex align-items-center border-none border-lg-start">
				<button class="glozin-form-review gz-button-outline-dark" type="button" data-text="<?php echo esc_attr__( 'Write a review', 'glozin' ); ?>" data-text-cancel="<?php echo esc_attr__( 'Cancel review', 'glozin' ); ?>"><?php echo esc_html__( 'Write a review', 'glozin' ); ?></button>
			</div>
		</div>
	<?php else : ?>
		<div class="glozin-product-rating">
			<div class="text-center w-100">
				<button class="glozin-form-review gz-button-outline" type="button" data-text="<?php echo esc_attr__( 'Write a review', 'glozin' ); ?>" data-text-cancel="<?php echo esc_attr__( 'Cancel review', 'glozin' ); ?>"><?php echo esc_html__( 'Write a review', 'glozin' ); ?></button>
			</div>
			<p class="woocommerce-noreviews"><?php esc_html_e( 'No reviews yet.', 'glozin' ); ?></p>
		</div>
	<?php endif; ?>
	<div class="glozin-review-form d-none border-top py-24 mb-20">
		<?php if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'no' || wc_customer_bought_product( '', get_current_user_id(), $product->get_id() ) ) : ?>
			<div id="review_form">
				<?php
				$commenter    = wp_get_current_commenter();
				$comment_form = array(
					/* translators: %s is product title */
					'title_reply'         => have_comments() ? esc_html__( 'Write a product review', 'glozin' ) : sprintf( esc_html__( 'Be the first to review &ldquo;%s&rdquo;', 'woocommerce' ), get_the_title() ),
					/* translators: %s is product title */
					'title_reply_to'      => esc_html__( 'Leave a Reply to %s', 'woocommerce' ),
					'title_reply_before'  => '<span id="reply-title" class="comment-reply-title">',
					'title_reply_after'   => '</span>',
					'comment_notes_after' => '',
					'label_submit'        => esc_html__( 'Submit Review', 'glozin' ),
					'class_submit'        => esc_attr( 'submit glozin-button' ),
					'submit_field'        => '<p class="form-submit d-flex justify-content-center align-items-center gap-15">%1$s %2$s</p>',
					'submit_button'       => '<button class="glozin-form-review gz-button-outline" type="button">'. esc_html__( 'Cancel review', 'glozin' ) .'</button><input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />',
					'logged_in_as'        => '',
					'comment_field'       => '',
				);

				$name_email_required = (bool) get_option( 'require_name_email', 1 );
				$fields              = array(
					'author' => array(
						'label'    => __( 'Name', 'woocommerce' ),
						'type'     => 'text',
						'value'    => $commenter['comment_author'],
						'required' => $name_email_required,
						'autocomplete' => 'name',
					),
					'email'  => array(
						'label'    => __( 'Email', 'woocommerce' ),
						'type'     => 'email',
						'value'    => $commenter['comment_author_email'],
						'required' => $name_email_required,
						'autocomplete' => 'email',
					),
				);

				$comment_form['fields'] = array();

				foreach ( $fields as $key => $field ) {
					$field_html  = '<p class="comment-form-' . esc_attr( $key ) . '">';
					$field_html .= '<label for="' . esc_attr( $key ) . '">' . esc_html( $field['label'] );

					if ( $field['required'] ) {
						$field_html .= '&nbsp;<span class="required">*</span>';
					}

					$field_html .= '</label><input id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" type="' . esc_attr( $field['type'] ) . '" autocomplete="' . esc_attr( $field['autocomplete'] ) . '" value="' . esc_attr( $field['value'] ) . '" size="30" ' . ( $field['required'] ? 'required' : '' ) . ' /></p>';

					$comment_form['fields'][ $key ] = $field_html;
				}

				$account_page_url = wc_get_page_permalink( 'myaccount' );
				if ( $account_page_url ) {
					/* translators: %s opening and closing link tags respectively */
					$comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf( esc_html__( 'You must be %slogged in%s to post a review.', 'glozin' ), '<a href="' . esc_url( $account_page_url ) . '">', '</a>' ) . '</p>';
				}

				if ( wc_review_ratings_enabled() ) {
					$comment_form['comment_field'] = '<div class="comment-form-rating"><label for="rating">' . esc_html__( 'Your rating', 'woocommerce' ) . ( wc_review_ratings_required() ? '&nbsp;<span class="required">*</span>' : '' ) . '</label><select name="rating" id="rating" required>
						<option value="">' . esc_html__( 'Rate&hellip;', 'woocommerce' ) . '</option>
						<option value="5">' . esc_html__( 'Perfect', 'woocommerce' ) . '</option>
						<option value="4">' . esc_html__( 'Good', 'woocommerce' ) . '</option>
						<option value="3">' . esc_html__( 'Average', 'woocommerce' ) . '</option>
						<option value="2">' . esc_html__( 'Not that bad', 'woocommerce' ) . '</option>
						<option value="1">' . esc_html__( 'Very poor', 'woocommerce' ) . '</option>
					</select></div>';
				}

				$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Your review', 'woocommerce' ) . '&nbsp;<span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" required></textarea></p>';
				$comment_form['format'] = 'xhtml';
				comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
				?>
			</div>
		<?php else : ?>
			<p class="woocommerce-verification-required"><?php esc_html_e( 'Only logged in customers who have purchased this product may leave a review.', 'woocommerce' ); ?></p>
		<?php endif; ?>
	</div>
	<div id="comments">
		<?php if ( have_comments() ) : ?>
			<ol class="commentlist">
				<?php wp_list_comments( apply_filters( 'woocommerce_product_review_list_args', array( 'callback' => 'woocommerce_comments' ) ) ); ?>
			</ol>

			<?php
			if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
				echo '<nav class="woocommerce-pagination">';
				paginate_comments_links(
					apply_filters(
						'woocommerce_comment_pagination_args',
						array(
							'prev_text' => is_rtl() ? '&rarr;' : '&larr;',
							'next_text' => is_rtl() ? '&larr;' : '&rarr;',
							'type'      => 'list',
						)
					)
				);
				echo '</nav>';
			endif;
			?>
		<?php endif; ?>
	</div>
	<div class="clear"></div>
</div>
