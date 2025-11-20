<?php
namespace Glozin\Addons\Elementor\Base;

use Glozin\Addons\Modules\Base\Variation_Select as BaseVariation_Select;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Variation_Select extends BaseVariation_Select {

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
	 * Constructor
	 *
	 * @param WC_Product_Variable $product
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Render variation dropdown
	 *
	 * @return void
	 */
	public function render( $product = null ) {
		if( ! empty( $product ) ) {
			$this->product = $product;
		}

		if ( ! $this->product ) {
			return;
		}

		$options = $this->get_options();
		$selected = false;

		if ( empty( $options ) ) {
			return;
		}
		?>
		<select name="variation_id">
			<option><?php esc_html_e( 'Select an option', 'glozin-addons' ); ?></option>
			<?php foreach ( $options as $option ) : ?>
				<?php $selected = $option['selected'] ? $option : $selected; ?>
				<?php
					$variation_product = new \WC_Product_Variation( $option['variation_id'] );
				?>
				<option
					value="<?php echo esc_attr( $option['variation_id'] ) ?>"
					data-attributes="<?php echo esc_attr( $this->json_encode_attribute( $option['attributes'] ) ); ?>"
					data-price="<?php echo esc_attr( $option['price'] ); ?>"
					data-price_html="<?php echo esc_attr( $variation_product->get_price_html() ); ?>"
					data-image="<?php echo esc_attr( $option['thumbnail_src'] ); ?>"
					<?php selected( true, $option['selected'] ); ?>
				>
					<?php echo esc_html( $option['label'] ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
		if ( $selected ) {
			$attributes = $selected['attributes'];
		} else {
			$attributes = array_fill_keys( array_keys( $options[0]['attributes'] ), '' );
		}
		foreach ( $attributes as $attr_name => $attr_value ) {
			printf(
				'<input type="hidden" name="%s" value="%s">',
				esc_attr( $attr_name ),
				esc_attr( $attr_value )
			);
		}
	}
}
