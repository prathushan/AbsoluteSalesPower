<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly


/**
 * Used for testing to make sure the IPN can listen to URL calls.
 * @since 1.8
 * @return string
 */
add_action('template_redirect','cf7pp_ipn_test');
function cf7pp_ipn_test() {

	if (isset($_REQUEST['cf7pp_test'])) {
		echo "Contact Form 7 - PayPal Add-on - Test Successful";
		exit;
	}
}


/**
 * PayPal notify url.
 * @since 1.8
 * @return string or array
 */
function cf7pp_get_paypal_notify_url($return = 'str') {
	$options = cf7pp_free_options();
	$mode_paypal = $options['mode'] == '1' ? 'sandbox' : 'production';

	$namespace = 'paypalipn/v1';
	$route = '/cf7pp_' . $mode_paypal;

	if ($return == 'str') {
		$result = add_query_arg('rest_route', '/' . $namespace . $route, get_site_url());
	} else {
		$result = array(
			'namespace'	=> $namespace,
			'route'		=> $route
		);
	}

	return $result;
}


/**
 * Register PayPal IPN listener.
 * @since 1.8
 */
add_action('rest_api_init', 'cf7pp_paypal_ipn_listener');
function cf7pp_paypal_ipn_listener() {
	$notify_url = cf7pp_get_paypal_notify_url('arr');
    register_rest_route($notify_url['namespace'], $notify_url['route'], array(
        'methods' 				=> 'POST',
        'callback' 				=> 'cf7pp_paypal_ipn_handler',
        'permission_callback'	=> 'cf7pp_paypal_ipn_auth'
    ));
}


/**
 * PayPal IPN permission callback.
 * @since 1.8
 * @return bool
 */
function cf7pp_paypal_ipn_auth() {
	return true; // security done in the handler
}


/**
 * PayPal IPN handler.
 * @since 1.8
 */
function cf7pp_paypal_ipn_handler() {
	$payload = file_get_contents('php://input');
	parse_str($payload, $data);

	if (strtolower($data['payment_status']) == 'completed') {
		$options = cf7pp_free_options();
		$paypal_post_url = 'https://www.' . ($options['mode'] == '1' ? 'sandbox.' : '') . 'paypal.com/cgi-bin/webscr';

		$data['cmd'] = '_notify-validate';
		$args = array(
			'method'           => 'POST',
			'timeout'          => 45,
			'redirection'      => 5,
			'httpversion'      => '1.1',
			'blocking'         => true,
			'headers'          => array(
				'host'         => 'www.paypal.com',
				'connection'   => 'close',
				'content-type' => 'application/x-www-form-urlencoded',
				'post'         => '/cgi-bin/webscr HTTP/1.1',
				
			),
			'sslverify'        => false,
			'body'             => $data
		);
			
		// Get response
		$response = wp_remote_post($paypal_post_url, $args);
		
		$status = is_wp_error($response) || strtolower($response['body']) != 'verified' ? 'failed' : 'completed';
		
		
		cf7pp_complete_payment($data['invoice'], $status, $data['txn_id']);
		
		http_response_code(200);
		
	} else {
		$status = 'failed';
	}
	
}