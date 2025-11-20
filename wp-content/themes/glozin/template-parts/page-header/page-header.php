<?php
/**
 * Template part for displaying the blog header
 *
 * @package Glozin
 */

?>

<div id="page-header" class="<?php \Glozin\Page_Header::classes('page-header'); ?>">
	<div class="container clearfix">
		<?php do_action('glozin_before_page_header_content'); ?>
		<div class="page-header__content position-relative d-flex flex-column <?php echo apply_filters('glozin_page_header_content_class', 'justify-content-center align-items-center text-center'); ?>">
			<?php \Glozin\Page_Header::breadcrumb(); ?>
			<?php echo \Glozin\Page_Header::title(); ?>
			<?php echo \Glozin\Page_Header::description(); ?>
		</div>
		<?php do_action('glozin_after_page_header_content'); ?>
	</div>
</div>