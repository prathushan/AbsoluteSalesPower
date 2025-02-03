<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Save payment as a custom post type
 * @since 1.8
 * @return payment_id in wp_posts table
 */
function cf7pp_insert_payment($gateway, $mode, $amount, $form_id, $status='cf7pp-pending') {
	$payment_id = wp_insert_post( array(
		'post_title'    => 'Order made on ' . date('H:i:s M d, Y'),
		'post_status'   => $status,
		'post_type'     => 'cf7pp_payments',
		'meta_input'    => array(
			'gateway'			=> strtolower($gateway),
			'mode'				=> $mode,
			'transaction_id'	=> '',
			'amount'			=> $amount,
			'cf7pp_form_id'     => $form_id
		)
	) );

	return $payment_id;
}

/**
 * Complete cf7pp payment
 * @since 1.8
 * @return bool
 */
function cf7pp_complete_payment($payment_id, $status, $transaction_id = '', $payer_email = '') {
	$payment_id = (int) $payment_id;
	if ( empty($payment_id) ) return false;

	$transaction_id = sanitize_text_field($transaction_id);
	if ( !empty( $transaction_id ) ) {
		update_post_meta( $payment_id, 'transaction_id', $transaction_id );
	}

	if ( !empty( $payer_email ) ) {
		update_post_meta( $payment_id, 'payer_email', sanitize_email( $payer_email ) );
	}

	$status = 'cf7pp-' . $status;
	$statuses = cf7pp_get_payment_statuses();
	if ( array_key_exists($status, $statuses) ) {
		wp_update_post(array(
			'ID'			=> $payment_id,
			'post_status'	=> $status
		));
	}

	return true;
}

/**
 * Get amount of earnings
 * @since 1.8
 * @return float
 */
function cf7pp_get_earnings_amount($mode = 'live') {
	global $wpdb;

	$mode = in_array($mode, ['sandbox', 'live']) ? $mode : 'live';

	$earnings = $wpdb->get_results(
		"SELECT SUM(pm2.meta_value) AS amount
		 FROM {$wpdb->posts} AS p
		 JOIN {$wpdb->postmeta} AS pm1 ON (p.ID = pm1.post_id)
		 JOIN {$wpdb->postmeta} AS pm2 ON (p.ID = pm2.post_id)
		 WHERE p.post_type ='cf7pp_payments'
		   AND p.post_status ='cf7pp-completed'
		   AND pm1.meta_key = 'mode'
		   AND pm1.meta_value = '{$mode}'
		   AND pm2.meta_key = 'amount'
		"
	);

	return (float) $earnings[0]->amount;
}




/**
 * Check if a URL is considered a local one
 *
 * @since  1.8
 *
 * @param  string $url The URL Provided
 *
 * @return boolean      If we're considering the URL local or not
 */
function cf7pp_is_local_url( $url = '' ) {
		$is_local_url = false;
		
		// Trim it up
		$url = strtolower( trim( $url ) );
		
		// Need to get the host...so let's add the scheme so we can use parse_url
		if ( false === strpos( $url, 'http://' ) && false === strpos( $url, 'https://' ) ) {
			$url = 'http://' . $url;
		}
		
		$url_parts = parse_url( $url );
		$host      = ! empty( $url_parts['host'] ) ? $url_parts['host'] : false;
		
		if ( ! empty( $url ) && ! empty( $host ) ) {
			
			if ( false !== ip2long( $host ) ) {
				if ( ! filter_var( $host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
					$is_local_url = true;
				}
			} else if ( 'localhost' === $host ) {
				$is_local_url = true;
			}
			
			$host_pieces = explode('.', $host);
			
			if (isset($host_pieces[1])) {
				if (empty($host_pieces[1])) {
					$is_local_url = true;
				}
			}
			
			$check_tlds = apply_filters( 'cf7pp_validate_tlds', true );
			if ( $check_tlds ) {
				$tlds_to_check = apply_filters( 'cf7pp_url_tlds', array(
					'.dev', '.local', '.test',
				) );
				
				foreach ( $tlds_to_check as $tld ) {
					if ( false !== strpos( $host, $tld ) ) {
						$is_local_url = true;
						continue;
					}
				}
			}
			
			if ( substr_count( $host, '.' ) > 1 ) {
				$subdomains_to_check = apply_filters( 'cf7pp_url_subdomains', array(
					'dev.', '*.staging.', '*.test.', 'staging-*.',
				) );
				
				foreach ( $subdomains_to_check as $subdomain ) {
					
					$subdomain = str_replace( '.', '(.)', $subdomain );
					$subdomain = str_replace( array( '*', '(.)' ), '(.*)', $subdomain );
					
					if ( preg_match( '/^(' . $subdomain . ')/', $host ) ) {
						$is_local_url = true;
						continue;
					}
				}
			}
		}
		
		return $is_local_url;
}



/**
 * Display admin notice on PayPal & Stripe Payments admin page if user is on local environment
 * @since 1.8
 */
add_action( 'admin_head', 'cf7pp_show_cf7pp_payments_localhost_notice' );
function cf7pp_show_cf7pp_payments_localhost_notice () {
    global $current_screen;
	
	if ($current_screen->post_type == 'cf7pp_payments') {
		
		$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
		
		$local = cf7pp_is_local_url($actual_link);
		
		if ($local == true) {
			
			function cf7pp_show_cf7pp_payments_localhost_admin_notice() {
				?>
				<div class="notice notice-info">
				<p><?php _e( 'Your website appears to be a testing website / a localhost environment - Please note that PayPal & Stripe "payment status" will not change to "completed" unless your site is public on the internet. ', 'sample-text-domain' ); ?></p>
				</div>
				<?php
			}
			
			
		add_action( 'admin_notices', 'cf7pp_show_cf7pp_payments_localhost_admin_notice' );
		
		}
	}
}