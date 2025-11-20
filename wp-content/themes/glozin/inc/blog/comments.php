<?php
/**
 * Comments functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Glozin
 */

namespace Glozin\Blog;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Comments
 *
 */
class Comments {
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
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
        add_filter( 'comment_form_defaults', array( $this, 'comment_form_defaults' ) );
        add_filter( 'comment_form_default_fields', array( $this, 'comment_form_fields' ) );
	}

    public function comment_form_defaults($args) {
        $required_text = ' ' . wp_required_field_message();

        $args['class_container'] = $args['class_container'] . ' border-top mt-60 pt-55';
        $args['title_reply'] = esc_html__( 'Leave a Comment', 'glozin' );
        $args['title_reply_before'] = '<h3 id="reply-title" class="comment-reply-title h2 mt-0 heading-letter-spacing text-center">';
		$args['cancel_reply_before'] = ' <small class="ms-10 fs-16 gz-button gz-button-subtle">';
        $args['comment_notes_before'] = sprintf(
                            '<p class="comment-notes text-center mb-40 mt-0">%s%s</p>',
                            sprintf(
                                '<span id="email-notes">%s</span>',
                                esc_html__( 'Your email address will not be published.', 'glozin' )
                            ),
                            $required_text
                        );
        $args['submit_field'] = '<p class="form-submit text-center mt-30 mb-0">%1$s %2$s</p>';

		$args['class_submit'] = 'submit gz-button gz-button-hover-effect';

        return $args;
    }

    /**
	 * Custom fields comment form
	 *
	 * @since  1.0
	 *
	 * @return  array  $fields
	 */
	public function comment_form_fields($fields) {
		global $commenter, $aria_req;

		$comment_author = isset($commenter['comment_author']) ? $commenter['comment_author'] : '';
		$comment_author_email = isset($commenter['comment_author_email']) ? $commenter['comment_author_email'] : '';
		$comment_author_url = isset($commenter['comment_author_url']) ? $commenter['comment_author_url'] : '';

		$fields_new = array(
			'author' => '<p class="comment-form-author">' .
			            '<input id ="author" placeholder="' . esc_attr__( 'Your Name*', 'glozin' ) . ' " name="author" type="text" required value="' . esc_attr( $comment_author ) .
			            '" size="30" maxlength="245" autocomplete="name"' . $aria_req . ' /></p>',

			'email' => '<p class="comment-form-email">' .
			           '<input id ="email" placeholder="' . esc_attr__( 'Your Email*', 'glozin' ) . '" name="email" type="email" required value="' . esc_attr( $comment_author_email ) .
			           '" size="30" maxlength="100" aria-describedby="email-notes" autocomplete="email"' . $aria_req . ' /></p>',

			'url' => '<p class="comment-form-url">' .
			         '<input id ="url" placeholder="' . esc_attr__( 'Your Website (Optional)', 'glozin' ) . '" name="url" type="text" value="' . esc_attr( $comment_author_url ) .
			         '" maxlength="200" autocomplete="url" /></p>'
		);

        unset($fields['author']);
        unset($fields['email']);
        unset($fields['url']);

        $fields_new = array_merge($fields_new, $fields);

		return $fields_new;
	}

	/**
	 * Comment callback function
	 *
	 * @since 1.0.0
	 *
	 * @param object $comment
	 * @param array $args
	 * @param int $depth
	 *
	 * @return string
	 */
	public static function glozin_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		extract( $args, EXTR_SKIP );

		$avatar = '';
		if ( $args['avatar_size'] != 0 ) {
			$avatar = get_avatar( $comment, $args['avatar_size'] );
		}

		$classes = get_comment_class( empty( $args['has_children'] ) ? '' : 'parent' );
		$classes = $classes ? implode( ' ', $classes ) : $classes;

		$comments = array(
			'comment_parent'      => 0,
			'comment_ID'          => get_comment_ID(),
			'comment_class'       => $classes,
			'comment_avatar'      => $avatar,
			'comment_author_link' => get_comment_author_link(),
			'comment_link'        => get_comment_link( get_comment_ID() ),
			'comment_date'        => get_comment_date(),
			'comment_time'        => get_comment_time(),
			'comment_approved'    => $comment->comment_approved,
			'comment_text'        => get_comment_text(),
			'comment_reply'       => get_comment_reply_link( array_merge( $args, array(
				'add_below' => 'comment',
				'depth'     => $depth,
				'max_depth' => $args['max_depth']
			) ) )

		);

		$comment = self::comment_template( $comments );

		echo ! empty( $comment ) ? $comment : '';
	}

	/**
	 * Comment Template function
	 *
	 * @since 1.0.0
	 *
	 * @param object $comment
	 *
	 * @return string
	 */
	public static function comment_template( $comments ) {
		$output    = array();
		$output[]  = sprintf( '<li id="comment-%s" class="%s">', esc_attr( $comments['comment_ID'] ), esc_attr( $comments['comment_class'] ) );
		$output[]  = sprintf( '<article id="div-comment-%s" class="comment-body d-flex gap-15">', $comments['comment_ID'] );
		$output [] = ! empty( $comments['comment_avatar'] ) ? sprintf(
			'<header class="comment-meta">' .
			'<div class="comment-author vcard">%s</div>' .
			'</header>',
			$comments['comment_avatar'] ) : '';
		$output[]  = '<div class="comment-content flex-1 border-bottom-dashed mb-30 pb-30"><div class="comment-metadata d-flex gap-10 align-items-center lh-1 flex-wrap">';
		$output[]  = sprintf( '<cite class="fn fs-16 text-dark fw-semibold fst-normal heading-letter-spacing">%s </cite>', $comments['comment_author_link'] );
		$date      = sprintf( esc_html__( '%1$s at %2$s', 'glozin' ), $comments['comment_date'], $comments['comment_time'] );
		$output[]  = sprintf( '<a href="%s" class="date fs-14 text-base">%s</a>', esc_url( $comments['comment_link'] ), $date );
		$output[]  = '</div>';
		if ( $comments['comment_approved'] == '0' ) {
			$output[] = sprintf( '<p class="comment-text mb-15"><em class="comment-awaiting-moderation fs-14">%s</em></p>', esc_html__( 'Your comment is awaiting moderation.', 'glozin' ) );
		} else {
			$output[] = sprintf( '<p class="comment-text fs-14 mb-15">%s</p>', $comments['comment_text'] );
		}

		$output[] = '<div class="reply d-flex gap-10 align-items-center fs-13 lh-1">';
		$output[] = $comments['comment_reply'];

		if ( current_user_can( 'edit_comment', $comments['comment_ID'] ) ) {
			$output[] = sprintf( '<a class="comment-edit-link" href="%s">%s</a>', esc_url( admin_url( 'comment.php?action=editcomment&amp;c=' ) . $comments['comment_ID'] ), esc_html__( 'Edit', 'glozin' ) );
		}

		$output[] = '</div></div></article>';

		return implode( ' ', $output );
	}
}
