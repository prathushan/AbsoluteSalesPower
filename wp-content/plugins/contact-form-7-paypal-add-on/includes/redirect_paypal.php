<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


function cf7pp_paypal_redirect($post_id, $payment_id) {
	// get variables

	$name = 	get_post_meta($post_id, "_cf7pp_name", true);
	$price = 	(float) get_post_meta($post_id, "_cf7pp_price", true);
	$id = 		get_post_meta($post_id, "_cf7pp_id", true);

	$options = cf7pp_free_options();

	$mode = intval( $options['mode'] );

	$currency = cf7pp_free_currency_code_to_iso( $options['currency'] );
	$language = cf7pp_free_language_code_to_locale( $options['language'] );

	$ppcp_status = cf7pp_free_ppcp_status();
	if ( !empty( $ppcp_status['client_id'] ) && empty( $ppcp_status['errors'] ) ) {
		cf7pp_free_ppcp_order_create( $ppcp_status, $name, $price, $id, $currency, $payment_id, $options['return'], $options['cancel'] );
	} else {
		// live or test mode
		if ($mode === 1) {
			$account = $options['sandboxaccount'];
			$path = "sandbox.paypal";
		} elseif ($mode === 2)  {
			$account = $options['liveaccount'];
			$path = "paypal";
		}

		$array = array(
			'business'			=> $account,
			'currency_code'		=> $currency,
			'charset'			=> get_bloginfo('charset'),
			'rm'				=> '1', 				// return method for return url, use 1 for GET
			'return'			=> $options['return'],
			'cancel_return'		=> $options['cancel'],
			'cbt'				=> get_bloginfo('name'),
			'bn'				=> 'WPPlugin_SP',
			'lc'				=> $language,
			'item_number'		=> $id,
			'item_name'			=> $name,
			'amount'			=> $price,
			'cmd'				=> '_xclick',
			'invoice'			=> $payment_id,
			'notify_url'		=> cf7pp_get_paypal_notify_url(),
		);


		// generate url with parameters
		$paypal_url = "https://www.$path.com/cgi-bin/webscr?";
		$paypal_url .= http_build_query($array);
		//$paypal_url = htmlentities($paypal_url); // fix for &curren was displayed literally
		$paypal_url = str_replace('&amp;','&',$paypal_url);

		// redirect to paypal
		wp_redirect($paypal_url);
		exit;
	}
}
	
?>