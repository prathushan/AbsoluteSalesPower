<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

function cf7pp_stripe_connection_status() {
	global $stripeConnectionStatus;

	if ( !isset($stripeConnectionStatus) ) {
		$stripeConnectionStatus = false;

		$options = cf7pp_free_options();

		if (!isset($options['mode_stripe']) || $options['mode_stripe'] == "2") {
			$account_id_key = 'acct_id_live';
			$stripe_connect_token_key = 'stripe_connect_token_live';
			$mode = 'live';
		} else {
			$account_id_key = 'acct_id_test';
			$stripe_connect_token_key = 'stripe_connect_token_test';
			$mode = 'sandbox';
		}

		$account_id = isset($options[$account_id_key]) ? $options[$account_id_key] : '';
		$token = isset($options[$stripe_connect_token_key]) ? $options[$stripe_connect_token_key] : '';

		if (!empty($account_id)) {
			$url = CF7PP_STRIPE_CONNECT_ENDPOINT . '?' . http_build_query(
				array(
					'action'		=> 'checkStatus',
					'mode'			=> $mode,
					'account_id'	=> $account_id,
					'token'			=> $token
				)
			);

			$account = wp_remote_get($url);

			$account = !empty( $account['body'] ) ? json_decode($account['body'], true) : [];

			if (!empty($account['payouts_enabled']) && intval($account['payouts_enabled']) === 1) {
				$stripeConnectionStatus['email'] = $account['email'];
				$stripeConnectionStatus['display_name'] = $account['display_name'];
				$stripeConnectionStatus['mode'] = $mode;
				$stripeConnectionStatus['account_id'] = $account_id;
				$stripeConnectionStatus['token'] = $token;
			}
		}
	}

	return $stripeConnectionStatus;
}

function cf7pp_stripe_connection_status_html() {
	$connected = cf7pp_stripe_connection_status();

	if ($connected) {
		$reconnect_mode = $connected['mode'] == 'live' ? 'sandbox' : 'live';
		printf(
			'<div class="notice inline notice-success cf7pp-stripe-connect">
				<p><strong>%s</strong><br>%s — Administrator (Owner)</p>
				<p>Pay as you go pricing: 2%% per-transaction fee + Stripe fees.</p>
			</div>
			<div>
				Your Stripe account is connected in <strong>%s</strong> mode. <a href="%s">Connect in <strong>%s</strong> mode</a>, or <a href="%s">disconnect this account</a>.
			</div>',
			$connected['display_name'],
			$connected['email'],
			$connected['mode'],
			cf7pp_stripe_connect_url($reconnect_mode),
			$reconnect_mode,
			cf7pp_stripe_disconnect_url($connected['account_id'], $connected['token'])
		);
	} else {
		printf(
			'<a href="%s"" class="stripe-connect-btn"><span>Connect with Stripe</span></a><br /><br />Connect with Stripe for pay as you go pricing: 2&#37; per-transaction fee + Stripe fees. Have questions about connecting with Stripe? Please see the <a target="_blank" href="https://wpplugin.org/documentation/stripe-connect/">documentation</a>. ',
			cf7pp_stripe_connect_url()
		);
	}
}

function cf7pp_stripe_connect_url($mode = false) {
	if ( $mode === false ) {
		$options = cf7pp_free_options();
		
		if (isset($options['mode_stripe'])) {
			$mode = $options['mode_stripe'] == "2" ? 'live' : 'sandbox';
		} else {
			$mode = 'sandbox';
		}
		
	}

	$stripe_connect_url = CF7PP_STRIPE_CONNECT_ENDPOINT . '?' . http_build_query(
		array(
			'action'		=> 'connect',
			'mode'			=> $mode,
			'return_url'	=> cf7pp_stripe_connect_tab_url()
		)
	);

	return $stripe_connect_url;
}

function cf7pp_stripe_disconnect_url($account_id, $token) {
	$options = cf7pp_free_options();

	$stripe_connect_url = CF7PP_STRIPE_CONNECT_ENDPOINT . '?' . http_build_query(
		array(
			'action'		=> 'disconnect',
			'mode'			=> $options['mode_stripe'] == 1 ? 'sandbox' : 'live',
			'return_url'	=> cf7pp_stripe_connect_tab_url(),
			'account_id'	=> $account_id,
			'token'			=> $token
		)
	);

	return $stripe_connect_url;
}

function cf7pp_stripe_connect_tab_url() {
	return add_query_arg(
		array(
			'page'	=> 'cf7pp_admin_table',
			'tab'	=> '5',
			'cf7pp_free_nonce' 	=> wp_create_nonce('cf7pp_free_stripe')
		),
		admin_url('admin.php')
	);
}

add_action('plugins_loaded', 'cf7pp_stripe_connect_completion');
function cf7pp_stripe_connect_completion() {
	if (empty($_GET['cf7pp_stripe_connect_completion']) || intval($_GET['cf7pp_stripe_connect_completion']) !== 1 || empty($_GET['mode']) || empty($_GET['account_id']) || empty($_GET['token'])) return;
	
	// nonce check
	if (!isset($_GET['cf7pp_free_nonce']) || !wp_verify_nonce($_GET['cf7pp_free_nonce'], 'cf7pp_free_stripe')) {
		wp_die('Security check failed');
	}

	if (!current_user_can('manage_options')) {
		return;
	}

	$options = cf7pp_free_options();

	if ($_GET['mode'] == 'live') {
		$account_id_key = 'acct_id_live';
		$stripe_connect_token_key = 'stripe_connect_token_live';
		$mode_stripe = 2;
	} else {
		$account_id_key = 'acct_id_test';
		$stripe_connect_token_key = 'stripe_connect_token_test';
		$mode_stripe = 1;
	}
	
	$options[$account_id_key] = sanitize_text_field($_GET['account_id']);
	$options[$stripe_connect_token_key] = sanitize_text_field($_GET['token']);
	$options['mode_stripe'] = $mode_stripe;

	if (isset($options['pub_key_live'])) { unset($options['pub_key_live']); }
	if (isset($options['sec_key_live'])) { unset($options['sec_key_live']); }
	if (isset($options['pub_key_test'])) { unset($options['pub_key_test']); }
	if (isset($options['sec_key_test'])) { unset($options['sec_key_test']); }
	if (isset($options['webhook_data_live'])) { unset($options['webhook_data_live']); }
	if (isset($options['webhook_data_test'])) { unset($options['webhook_data_test']); }
	$options['stripe_connect_notice_dismissed'] = 0;

	cf7pp_free_options_update( $options );

	$return_url = cf7pp_stripe_connect_tab_url();

	/**
	 * Filters the URL users are returned to after Stripe connect completed
	 *
	 * @since 1.8.3
	 *
	 * @param $return_url URL to return to.
	 */
	$return_url = apply_filters('cf7pp_stripe_connect_return_url', $return_url);

	wp_redirect($return_url);
}

add_action('plugins_loaded', 'cf7pp_stripe_disconnected');
function cf7pp_stripe_disconnected() {
	if (empty($_GET['cf7pp_stripe_disconnected']) || intval($_GET['cf7pp_stripe_disconnected']) !== 1 || empty($_GET['mode']) || empty($_GET['account_id'])) return;

	// nonce check
	if (!isset($_GET['cf7pp_free_nonce']) || !wp_verify_nonce($_GET['cf7pp_free_nonce'], 'cf7pp_free_stripe')) {
		wp_die('Security check failed');
	}
	
	if (!current_user_can('manage_options')) {
		return;
	}

	$options = cf7pp_free_options();
	$acct_id_key = $_GET['mode'] == 'live' ? 'acct_id_live' : 'acct_id_test';
	if ($options[$acct_id_key] == $_GET['account_id']) {
		$options[$acct_id_key] = '';
		cf7pp_free_options_update( $options );
	}

	$return_url = cf7pp_stripe_connect_tab_url();

	/**
	 * Filters the URL users are returned to after Stripe disconnect completed
	 *
	 * @since 1.8.3
	 *
	 * @param $return_url URL to return to.
	 */
	$return_url = apply_filters('cf7pp_stripe_disconnect_return_url', $return_url);

	wp_redirect($return_url);
}

