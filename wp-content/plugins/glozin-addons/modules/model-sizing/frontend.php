<?php
/**
 * Single Product hooks.
 *
 * @package Glozin
 */

namespace Glozin\Addons\Modules\Model_Sizing;

use Glozin\Helper;
use Glozin\Icon;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Single Product
 */
class Frontend {
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
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'woocommerce_single_product_summary', array( $this, 'model_sizing_html' ), 36 );
		add_action( 'glozin_model_sizing_elementor', array( $this, 'model_sizing_html' ), 36 );
	}

	public function scripts() {
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_style( 'glozin-model-sizing', GLOZIN_ADDONS_URL . 'modules/model-sizing/assets/model-sizing' . $debug . '.css' );
	}

	/**
	 * Model Sizing HTML
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function model_sizing_html() {
		$model_sizing_id = get_post_meta( get_the_ID(), 'model_sizing_thumbnail_id', true );
		$wearing = get_post_meta( get_the_ID(), 'model_sizing_wearing', true );
		$height = get_post_meta( get_the_ID(), 'model_sizing_height', true );
		$weight = get_post_meta( get_the_ID(), 'model_sizing_weight', true );
		$shoulder_width = get_post_meta( get_the_ID(), 'model_sizing_shoulder_width', true );
		$bust_waist_hip = get_post_meta( get_the_ID(), 'model_sizing_bust_waist_hip', true );
		$informations_custom = get_post_meta( get_the_ID(), 'model_sizing_informations_custom', true );
		$html_left = [];
		$html_right = [];

		if( ! empty( $model_sizing_id ) && ( ! empty( $wearing ) || ! empty( $height ) || ! empty( $weight ) || ! empty( $shoulder_width ) || ! empty( $bust_waist_hip ) || ! empty( $informations_custom ) ) ) {
			if( ! empty( $informations_custom ) ) :
				foreach( $informations_custom as $key => $information ) :
					if( ! empty( $information['label'] ) && ! empty( $information['value'] ) ) :
						if( $key % 2 == 0 ) {
							$html_left[] = '<div class="model-sizing-information__item model-sizing-information__item--custom">
								<span class="model-sizing-information__label">' . esc_html( $information['label'] ) . '</span>
								<span class="model-sizing-information__value text-dark fw-semibold">' . esc_html( $information['value'] ) . '</span>
							</div>';
						} else {
							$html_right[] = '<div class="model-sizing-information__item model-sizing-information__item--custom">
								<span class="model-sizing-information__label">' . esc_html( $information['label'] ) . '</span>
								<span class="model-sizing-information__value text-dark fw-semibold">' . esc_html( $information['value'] ) . '</span>
							</div>';
						}
					endif;
				endforeach;
			endif; ?>
			<div class="model-sizing d-flex align-items-center gap-20 border-bottom pb-20 mb-25">
				<?php if( ! empty( $model_sizing_id ) ) : ?>
					<div class="model-sizing-thumbnail gz-ratio flex-shrink-0">
						<?php echo wp_get_attachment_image( $model_sizing_id, 'thumbnail' ); ?>
					</div>
				<?php endif; ?>

				<div class="model-sizing-informations d-flex flex-wrap flex-column flex-md-row column-gap-20">
					<?php if( ! empty( $wearing ) ) : ?>
						<div class="model-sizing-information__item w-100">
							<span class="model-sizing-information__label"><?php esc_html_e( 'Model is Wearing:', 'glozin-addons' ); ?></span>
							<span class="model-sizing-information__value text-dark fw-semibold"><?php echo esc_html( $wearing ); ?></span>
						</div>
					<?php endif; ?>
					<div class="model-sizing-information__item--left">
						<?php if( ! empty( $height ) ) : ?>
							<div class="model-sizing-information__item">
								<span class="model-sizing-information__label"><?php esc_html_e( 'Height:', 'glozin-addons' ); ?></span>
								<span class="model-sizing-information__value text-dark fw-semibold"><?php echo esc_html( $height ); ?></span>
							</div>
						<?php endif; ?>
						<?php if( ! empty( $shoulder_width ) ) : ?>
							<div class="model-sizing-information__item">
								<span class="model-sizing-information__label"><?php esc_html_e( 'Shoulder width:', 'glozin-addons' ); ?></span>
								<span class="model-sizing-information__value text-dark fw-semibold"><?php echo esc_html( $shoulder_width ); ?></span>
							</div>
						<?php endif; ?>
						<?php echo ! empty( $html_left ) ? implode( '', $html_left ) : ''; ?>
					</div>
					<div class="model-sizing-information__item--right">
						<?php if( ! empty( $weight ) ) : ?>
							<div class="model-sizing-information__item">
								<span class="model-sizing-information__label"><?php esc_html_e( 'Weight:', 'glozin-addons' ); ?></span>
								<span class="model-sizing-information__value text-dark fw-semibold"><?php echo esc_html( $weight ); ?></span>
							</div>
						<?php endif; ?>
						<?php if( ! empty( $bust_waist_hip ) ) : ?>
						<div class="model-sizing-information__item">
								<span class="model-sizing-information__label"><?php esc_html_e( 'Bust/waist/hips:', 'glozin-addons' ); ?></span>
								<span class="model-sizing-information__value text-dark fw-semibold"><?php echo esc_html( $bust_waist_hip ); ?></span>
							</div>
						<?php endif; ?>
						<?php echo ! empty( $html_right ) ? implode( '', $html_right ) : ''; ?>
					</div>
				</div>
			</div>
			<?php
		}
	}

}
