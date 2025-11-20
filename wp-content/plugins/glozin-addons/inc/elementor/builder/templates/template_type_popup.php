<?php
/**
 * The Template for displaying all template type
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div id="glozin-builder-template-modal" class="glozin-builder-template-modal">
	<div class="modal__backdrop"></div>
	<div class="modal__content">
		<span class="glozin-svg-icon glozin-svg-icon--close modal__button-close"><svg width="24" height="24" aria-hidden="true" role="img" focusable="false" viewBox="0 0 32 32"><path d="M28.336 5.936l-2.272-2.272-10.064 10.080-10.064-10.080-2.272 2.272 10.080 10.064-10.080 10.064 2.272 2.272 10.064-10.080 10.064 10.080 2.272-2.272-10.080-10.064z"></path></svg></span>
		<form class="modal-content__form" action="<?php echo esc_url( admin_url('post.php') ); ?>">
			<input type="hidden" class="_wpnonce" value="<?php echo wp_create_nonce( 'glozin_buider_new_template' ); ?>">
			<div class="modal-content-form__title"><?php echo esc_html__( 'Choose Template Type', 'glozin-addons' ); ?></div>
			<div  class="elementor-form-field">
				<label for="glozin-builder-template-modal-type" class="elementor-form-field__label"><?php echo esc_html__( 'Select the type of template you want to work on', 'glozin-addons' ); ?></label>
				<select id="glozin-builder-template-modal-type" class="elementor-form-field__select" required>
					<option value="footer"><?php echo esc_html__( 'Footer', 'glozin-addons' ); ?></option>
					<option value="navigation_bar"><?php echo esc_html__( 'Navigation Bar', 'glozin-addons' ); ?></option>
					<?php
						if( get_option( 'glozin_product_builder_enable', false ) ) {
							?><option value="product"><?php echo esc_html__( 'Single Product', 'glozin-addons' ); ?></option><?php
						}

						if( get_option( 'glozin_product_archive_builder_enable', false ) ) {
							?><option value="archive"><?php echo esc_html__( 'Product Archive ', 'glozin-addons' ); ?></option><?php
						}

						if( get_option( 'glozin_cart_page_builder_enable', false ) ) {
							?><option value="cart_page"><?php echo esc_html__( 'Cart Page', 'glozin-addons' ); ?></option><?php
						}

						if( get_option( 'glozin_checkout_page_builder_enable', false ) ) {
							?><option value="checkout_page"><?php echo esc_html__( 'Checkout Page', 'glozin-addons' ); ?></option><?php
						}

						if( get_option( 'glozin_404_page_builder_enable', false ) ) {
							?><option value="404_page"><?php echo esc_html__( '404 Page', 'glozin-addons' ); ?></option><?php
						}
					?>
				</select>
			</div>
			<div class="elementor-form-field">
				<label for="glozin-builder-template-modal__post-title" class="elementor-form-field__label">
					<?php echo esc_html__( 'Name your template', 'glozin-addons' ); ?>
				</label>
				<input type="text" placeholder="<?php echo esc_attr__( 'Enter template name (optional)', 'glozin-addons' ); ?>" required id="glozin-builder-template-modal__post-title" class="elementor-form-field__text">
			</div>
			<div class="elementor-form-field">
				<input class="elementor-form-field__checkbox" type="checkbox" name="woolentor-template-enable" id="glozin-builder-template-modal__post-enable">
				<label for="glozin-builder-template-modal__post-enable" class="elementor-form-field__label">
					<?php echo esc_html__( 'Enable Builder', 'glozin-addons' ); ?>
				</label>
			</div>
			<button id="glozin-builder-template-modal__submit" class="elementor-button e-primary"><span><?php echo esc_html__( 'Create Template', 'glozin-addons' ); ?></span></button>
			<p class="modal-content-form-message"></p>
		</form>
	</div>
</div>
