<?php
/**
 * Template part for displaying the logo
 *
 * @package Glozin
 */

$show_title = isset( $args['title'] ) ? $args['title'] : true;
$logo = apply_filters( 'glozin_header_logo', '', $args['type'] );
$logo_light = apply_filters( 'glozin_header_logo_light', '', $args['type'] );

if( empty( $logo ) ) {
	if ( 'text' == $args['type'] ) {
		$logo = ! empty( $args['logo'] ) ? $args['logo'] : \Glozin\Helper::get_option( 'logo_text' );
	} elseif ( 'svg' == $args['type'] ) {
		$logo = ! empty( $args['logo'] ) ? $args['logo'] : \Glozin\Helper::get_option( 'logo_svg' );

	} else {
		$logo = ! empty( $args['logo'] ) ? $args['logo'] : \Glozin\Helper::get_option( 'logo' );

		if ( empty( $logo ) ) {
			$logo = get_template_directory_uri() . '/images/logo.svg';
		}
	}
}

if( empty( $logo_light ) ) {
	if ( 'image' == $args['type'] ) {
		$logo_light = ( isset( $args['logo_light'] ) && ! empty( $args['logo_light'] ) ) ? $args['logo_light'] : \Glozin\Helper::get_option( 'logo_light' );
	}
}

?>
<div class="header-logo position-relative z-3 <?php echo esc_attr( $args['classes'] ); ?>">
	<a class="d-block position-relative fw-semibold lh-1" href="<?php echo esc_url( home_url() ) ?>">
		<?php if ( $logo ) : ?>
			<?php if ( 'text' == $args['type'] ) : ?>
				<span class="header-logo__text fs-32"><?php echo esc_html( $logo ) ?></span>
			<?php elseif ( 'svg' == $args['type'] ) : ?>
				<span class="header-logo__svg fs-32"><?php echo \Glozin\Icon::sanitize_svg( $logo ); ?></span>
			<?php elseif ( 'svg_upload' == $args['type'] ) : ?>
				<span class="header-logo__svg fs-32"><?php echo ! empty($logo) ? $logo : ''; ?></span>
			<?php else : ?>
				<?php if( ! empty( $logo_light ) ) : ?>
					<img src="<?php echo esc_url( $logo_light ); ?>" alt="<?php echo get_bloginfo( 'name' ); ?>" class="logo-light">
				<?php endif; ?>
				<img src="<?php echo esc_url( $logo ); ?>" class="logo-dark d-inline-block" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<?php endif; ?>
		<?php endif; ?>
	</a>
	<?php if ($show_title) : ?>
	<?php \Glozin\Header\Main::site_branding_title(); ?>
	<?php \Glozin\Header\Main::site_branding_description(); ?>
	<?php endif ?>
</div>
