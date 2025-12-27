<?php
namespace S1WC;

if ( ! defined( 'ABSPATH' ) ) exit;

class Customer_Admin {

	public static function init() {
		add_action( 'current_screen', [ __CLASS__, 'maybe_init' ] );
	}

	public static function maybe_init() {
		$screen = get_current_screen();
		if ( ! $screen ) {
			return;
		}

		$is_user_screen = in_array( $screen->id, [ 'users', 'user', 'profile' ], true );
		if ( ! $is_user_screen ) {
			return;
		}

		add_filter( 'manage_users_columns', [ __CLASS__, 'add_user_columns' ], 20 );
		add_filter( 'manage_users_custom_column', [ __CLASS__, 'render_user_column' ], 10, 3 );
		add_filter( 'manage_users_sortable_columns', [ __CLASS__, 'make_columns_sortable' ] );
		add_action( 'pre_get_users', [ __CLASS__, 'handle_sorting' ] );
		add_action( 'show_user_profile', [ __CLASS__, 'render_user_profile_section' ] );
		add_action( 'edit_user_profile', [ __CLASS__, 'render_user_profile_section' ] );
	}

	private static function is_customer( $user_id ) {
		$user = get_userdata( $user_id );
		if ( ! $user ) {
			return false;
		}
		
		if ( in_array( 'customer', $user->roles, true ) ) {
			return true;
		}

		if ( ! function_exists( 'wc_get_orders' ) ) {
			return false;
		}
		
		$trdr = get_user_meta( $user_id, 's1_customer_trdr', true );
		if ( ! empty( $trdr ) ) {
			return true;
		}
		
		$orders = wc_get_orders( [
			'customer_id' => $user_id,
			'limit' => 1,
			'return' => 'ids',
		] );
		
		return ! empty( $orders );
	}

	public static function add_user_columns( $columns ) {
		$new_columns = [];
		foreach ( $columns as $key => $label ) {
			$new_columns[ $key ] = $label;
			if ( $key === 'email' ) {
				$new_columns['s1wc_trdr'] = __( 'TRDR', 'softone-woocommerce-sync' );
				$new_columns['s1wc_trdbranch'] = __( 'TRDBRANCH', 'softone-woocommerce-sync' );
			}
		}
		return $new_columns;
	}

	public static function render_user_column( $value, $column_name, $user_id ) {
		if ( ! in_array( $column_name, [ 's1wc_trdr', 's1wc_trdbranch' ], true ) ) {
			return $value;
		}

		if ( ! self::is_customer( $user_id ) ) {
			return '—';
		}

		if ( $column_name === 's1wc_trdr' ) {
			$trdr = get_user_meta( $user_id, 's1_customer_trdr', true );
			if ( ! empty( $trdr ) ) {
				return '<strong>' . esc_html( $trdr ) . '</strong>';
			}
			return '<span style="color: #999;">—</span>';
		}

		if ( $column_name === 's1wc_trdbranch' ) {
			$trdbranch = get_user_meta( $user_id, 's1_customer_trdbranch', true );
			if ( ! empty( $trdbranch ) ) {
				return '<strong>' . esc_html( $trdbranch ) . '</strong>';
			}
			return '<span style="color: #999;">—</span>';
		}

		return $value;
	}

	public static function make_columns_sortable( $columns ) {
		$columns['s1wc_trdr'] = 's1wc_trdr';
		$columns['s1wc_trdbranch'] = 's1wc_trdbranch';
		return $columns;
	}

	public static function handle_sorting( $query ) {
		if ( ! is_admin() ) {
			return;
		}

		$orderby = $query->get( 'orderby' );
		if ( ! in_array( $orderby, [ 's1wc_trdr', 's1wc_trdbranch' ], true ) ) {
			return;
		}

		$meta_key = $orderby === 's1wc_trdr' ? 's1_customer_trdr' : 's1_customer_trdbranch';
		
		$meta_query = $query->get( 'meta_query' );
		if ( ! is_array( $meta_query ) ) {
			$meta_query = [];
		}
		
		$meta_query[] = [
			'key' => $meta_key,
			'compare' => 'EXISTS',
		];
		
		$query->set( 'meta_query', $meta_query );
		$query->set( 'meta_key', $meta_key );
		$query->set( 'orderby', 'meta_value' );
	}

	public static function render_user_profile_section( $user ) {
		if ( ! self::is_customer( $user->ID ) ) {
			return;
		}

		$trdr = get_user_meta( $user->ID, 's1_customer_trdr', true );
		$trdbranch = get_user_meta( $user->ID, 's1_customer_trdbranch', true );
		$customer_code = get_user_meta( $user->ID, 's1_customer_code', true );
		$customer_afm = get_user_meta( $user->ID, 's1_customer_afm', true );

		if ( empty( $trdr ) && empty( $trdbranch ) && empty( $customer_code ) && empty( $customer_afm ) ) {
			return;
		}

		?>
		<h2><?php esc_html_e( 'SoftOne ERP Information', 'softone-woocommerce-sync' ); ?></h2>
		<table class="form-table" role="presentation">
			<tbody>
				<?php if ( ! empty( $customer_code ) ) : ?>
				<tr>
					<th><label><?php esc_html_e( 'Customer Code', 'softone-woocommerce-sync' ); ?></label></th>
					<td>
						<code style="background: #f5f5f5; padding: 4px 8px; border-radius: 3px; font-size: 13px;"><?php echo esc_html( $customer_code ); ?></code>
					</td>
				</tr>
				<?php endif; ?>

				<?php if ( ! empty( $customer_afm ) ) : ?>
				<tr>
					<th><label><?php esc_html_e( 'AFM (Tax ID)', 'softone-woocommerce-sync' ); ?></label></th>
					<td>
						<code style="background: #f5f5f5; padding: 4px 8px; border-radius: 3px; font-size: 13px;"><?php echo esc_html( $customer_afm ); ?></code>
					</td>
				</tr>
				<?php endif; ?>

				<tr>
					<th><label><?php esc_html_e( 'TRDR', 'softone-woocommerce-sync' ); ?></label></th>
					<td>
						<?php if ( ! empty( $trdr ) ) : ?>
							<code style="background: #f5f5f5; padding: 4px 8px; border-radius: 3px; font-size: 13px; font-weight: 600;"><?php echo esc_html( $trdr ); ?></code>
						<?php else : ?>
							<span style="color: #999;">—</span>
						<?php endif; ?>
					</td>
				</tr>

				<tr>
					<th><label><?php esc_html_e( 'TRDBRANCH', 'softone-woocommerce-sync' ); ?></label></th>
					<td>
						<?php if ( ! empty( $trdbranch ) ) : ?>
							<code style="background: #f5f5f5; padding: 4px 8px; border-radius: 3px; font-size: 13px; font-weight: 600;"><?php echo esc_html( $trdbranch ); ?></code>
						<?php else : ?>
							<span style="color: #999;">—</span>
						<?php endif; ?>
					</td>
				</tr>

				<?php
				$other_fields = [
					's1_customer_name' => __( 'Customer Name', 'softone-woocommerce-sync' ),
					's1_customer_address' => __( 'Address', 'softone-woocommerce-sync' ),
					's1_customer_city' => __( 'City', 'softone-woocommerce-sync' ),
					's1_customer_zip' => __( 'ZIP Code', 'softone-woocommerce-sync' ),
					's1_customer_phone' => __( 'Phone', 'softone-woocommerce-sync' ),
				];

				foreach ( $other_fields as $meta_key => $label ) {
					$value = get_user_meta( $user->ID, $meta_key, true );
					if ( ! empty( $value ) ) :
						?>
						<tr>
							<th><label><?php echo esc_html( $label ); ?></label></th>
							<td><?php echo esc_html( $value ); ?></td>
						</tr>
						<?php
					endif;
				}
				?>
			</tbody>
		</table>
		<?php
	}
}

