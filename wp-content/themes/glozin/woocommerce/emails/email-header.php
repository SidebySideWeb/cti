<?php
/**
 * Email Header
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 */

use Automattic\WooCommerce\Utilities\FeaturesUtil;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$email_improvements_enabled = FeaturesUtil::feature_is_enabled( 'email_improvements' );
$store_name = $store_name ?? get_bloginfo( 'name', 'display' );
$logo_url = get_template_directory_uri() . '/images/logo.svg';
$contact_phone = '22990 23200';

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />
		<meta content="width=device-width, initial-scale=1.0" name="viewport">
		<title><?php echo esc_html( $store_name ); ?></title>
		<?php do_action( 'woocommerce_email_styles' ); ?>
	</head>
	<body <?php echo is_rtl() ? 'rightmargin' : 'leftmargin'; ?>="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
		<table width="100%" id="outer_wrapper" role="presentation" style="background-color: #f5f5f5;">
			<tr>
				<td></td>
				<td width="600">
					<div id="wrapper" dir="<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>">
						<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="inner_wrapper" role="presentation">
							<tr>
								<td align="center" valign="top" style="padding: 20px 0;">
									<table border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation">
										<tr>
											<td id="template_header_image" style="text-align: center; padding: 20px 0;">
												<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $store_name ); ?>" style="max-width: 200px; height: auto;" />
											</td>
										</tr>
										<tr>
											<td style="text-align: center; padding: 10px 0;">
												<p style="margin: 0; font-size: 14px; color: #666;">
													<strong>Tel:</strong> <a href="tel:2299023200" style="color: #666; text-decoration: none;"><?php echo esc_html( $contact_phone ); ?></a>
												</p>
											</td>
										</tr>
									</table>
									<table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_container" role="presentation" style="background-color: #ffffff; border-radius: 4px;">
										<tr>
											<td align="center" valign="top">
												<table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_header" role="presentation">
													<tr>
														<td id="header_wrapper" style="padding: 30px 40px 20px;">
															<h1 style="margin: 0; font-size: 24px; font-weight: bold; color: #333;"><?php echo esc_html( $email_heading ); ?></h1>
														</td>
													</tr>
												</table>
											</td>
										</tr>
										<tr>
											<td align="center" valign="top">
												<table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_body" role="presentation">
													<tr>
														<td valign="top" id="body_content" style="background-color: #ffffff;">
															<table border="0" cellpadding="20" cellspacing="0" width="100%" role="presentation">
																<tr>
																	<td valign="top" id="body_content_inner_cell">
																		<div id="body_content_inner" style="color: #333; font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6;">


