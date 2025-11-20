<?php
/**
 * Template part for displaying the account icon
 *
 * @package Glozin
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}

$classes = isset($args['account_classes']) ? $args['account_classes'] : '';
$account_display = isset($args['account_display']) ? $args['account_display'] : '';
$text_classes = isset($args['account_text_class']) ? $args['account_text_class'] : '';
$data_toggle = isset($args['data_toggle']) ? $args['data_toggle'] : 'off-canvas';
$data_target = isset($args['data_target']) ? $args['data_target'] : 'account-panel';
$account_text = isset($args['account_text']) ? $args['account_text'] : esc_html__( 'Account', 'glozin' );
?>

<div class="header-account d-flex align-items-center gap-3">
	<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ); ?>" class="header-account gz-button gz-button-text<?php echo esc_attr( $classes); ?>" data-toggle="<?php echo esc_attr( $data_toggle ) ; ?>" data-target="<?php echo esc_attr( $data_target ); ?>">
		<?php echo \Glozin\Icon::get_svg( 'account' ); ?>
		<span class="screen-reader-text"><?php echo esc_html( $account_text ) ?></span>
	</a>
	<?php if ( $account_display == 'icon-text' ) : ?>
		<div class="header-account-wrapper d-flex flex-column align-items-start gap-5">
			<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ); ?>" class="gz-button gz-button-text<?php echo esc_attr( $classes); ?>" data-toggle="<?php echo esc_attr( $data_toggle ) ; ?>" data-target="<?php echo esc_attr( $data_target ); ?>">
				<span class="<?php echo esc_attr( $text_classes ) ?>"><?php echo esc_html( $account_text ) ?></span>
			</a>
			<?php if ( ! is_user_logged_in() && get_option('woocommerce_enable_myaccount_registration') === 'yes' ) : ?>
				<a id="header-account-register" class="gz-button gz-button-text fw-normal" href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ); ?>?mode=register" data-toggle="modal" data-target="login-modal">
					<?php echo esc_html__( 'Register', 'glozin' );?>
				</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>
