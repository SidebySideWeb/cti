<?php
/**
 * Email Footer
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 */

defined( 'ABSPATH' ) || exit;

$email = $email ?? null;
$logo_url = get_template_directory_uri() . '/images/logo.svg';
$contact_phone = '22990 23200';
$store_name = get_bloginfo( 'name', 'display' );

?>
																		</div>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td align="center" valign="top" style="padding: 20px 0;">
									<table border="0" cellpadding="10" cellspacing="0" width="100%" id="template_footer" role="presentation">
										<tr>
											<td valign="top" style="text-align: center; padding: 20px 0;">
												<table border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation">
													<tr>
														<td style="text-align: center; padding: 10px 0;">
															<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $store_name ); ?>" style="max-width: 150px; height: auto; margin-bottom: 15px;" />
														</td>
													</tr>
													<tr>
														<td style="text-align: center; padding: 10px 0;">
															<p style="margin: 0; font-size: 14px; color: #666;">
																<strong>Tel:</strong> <a href="tel:2299023200" style="color: #666; text-decoration: none;"><?php echo esc_html( $contact_phone ); ?></a>
															</p>
														</td>
													</tr>
													<?php
													$email_footer_text = get_option( 'woocommerce_email_footer_text' );
													if ( apply_filters( 'woocommerce_is_email_preview', false ) ) {
														$text_transient = get_transient( 'woocommerce_email_footer_text' );
														$email_footer_text = false !== $text_transient ? $text_transient : $email_footer_text;
													}
													if ( $email_footer_text ) :
													?>
													<tr>
														<td style="text-align: center; padding: 10px 0; color: #999; font-size: 12px;">
															<?php echo wp_kses_post( wpautop( wptexturize( apply_filters( 'woocommerce_email_footer_text', $email_footer_text, $email ) ) ) ); ?>
														</td>
													</tr>
													<?php endif; ?>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</div>
				</td>
				<td></td>
			</tr>
		</table>
	</body>
</html>


