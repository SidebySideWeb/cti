<?php
/**
 * Sales botter popup
 *
 */

defined( 'ABSPATH' ) || exit;

$product = $args['product'];

if( empty( $product ) ) {
	return;
}

$progress_bar_duration = ! empty( $args['progress_bar_duration'] ) ? $args['progress_bar_duration'] : 6000;
?>
<div class="live-sales-notification d-flex justify-content-between bg-light position-fixed start-10 end-10 bottom-10 start-30-md bottom-30-md shadow rounded-10 overflow-hidden px-10 py-10">
	<div class="live-sales-notification__content d-flex align-items-center w-100">
		<a class="live-sales-notification__thumbnail" href="<?php echo esc_url( $product['product_link'] ); ?>">
			<?php echo $product['product_thumb']; ?>
		</a>
		<div class="d-flex flex-column justify-content-between live-sales-notification__summary px-10 py-10">
			<div class="live-sales-notification__name">
				<span class="d-block fs-12 text-dark"><?php echo $product['first_name']; ?> <?php echo ! empty( $product['address'] ) ? '(' . $product['address'] . ')' : ''; ?> <span><?php esc_html_e( 'purchased', 'glozin-addons' ); ?></span></span>
				<a class="d-block position-relative fs-14 fw-semibold text-dark heading-letter-spacing" href="<?php echo esc_url( $product['product_link'] ); ?>"><?php echo $product['product_name']; ?></a>
			</div>
			<div class="live-sales-notification__bottom d-flex align-items-center gap-15">
				<span class="live-sales-notification__time-passed fs-12 text-gray"><?php echo $product['time_passed']; ?> <?php echo $product['time_passed_type']; ?> <?php esc_html_e( 'ago', 'glozin-addons' ); ?></span>
				<span class="live-sales-notification__verified fs-12 text-gray d-inline-flex align-items-center gap-5"><?php echo \Glozin\Addons\Helper::get_svg( 'verified', 'ui', [ 'class' => 'text-dark' ] ); ?><?php esc_html_e( 'Verified', 'glozin-addons' ); ?></span>
			</div>
		</div>
	</div>
	<span class="live-sales-notification__close live-sales-notification__icon d-inline-block lh-1 fs-10 ps-10">
		<?php echo \Glozin\Addons\Helper::get_svg( 'close' ); ?>
	</span>
	<span class="live-sales-notification__progress-bar position-absolute bottom-0 start-0 w-100" style="--live-sales-notification-progress-bar-duration: <?php echo esc_attr( $progress_bar_duration ); ?>ms">
		<span class="live-sales-notification__progress-bar-inner position-absolute top-0 start-0 w-100 h-100 bg-dark"></span>
	</span>
</div>