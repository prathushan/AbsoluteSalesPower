<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


// returns the form id of the forms that have paypal enabled - used for redirect method 1 and method 2
function cf7pp_forms_enabled() {

	// array that will contain which forms paypal is enabled on
	$enabled = array();
	
	$args = array(
		'posts_per_page'   => 999,
		'post_type'        => 'wpcf7_contact_form',
		'post_status'      => 'publish',
	);
	$posts_array = get_posts($args);
	
	
	// loop through them and find out which ones have paypal enabled
	foreach($posts_array as $post) {
		
		$post_id = $post->ID;
		
		// paypal
		$enable = get_post_meta( $post_id, "_cf7pp_enable", true);
		
		if ($enable == "1") {
			$enabled[] = $post_id.'|paypal';
		}
		
		// stripe
		$enable_stripe = get_post_meta( $post_id, "_cf7pp_enable_stripe", true);
		
		if ($enable_stripe == "1") {
			$enabled[] = $post_id.'|stripe';
		}
		
	}

	return json_encode($enabled);

}


// hook into contact form 7 - after send
add_action('template_redirect','cf7pp_redirect_method');
function cf7pp_redirect_method() {

	// for paypal 
	if (isset($_GET['cf7pp_paypal_redirect'])) {
		
		// get the id from the cf7pp_before_send_mail function theme redirect
		$post_id 	= sanitize_text_field($_GET['cf7pp_paypal_redirect']);
		
		if (isset($_GET['cf7pp_p'])) {
			$payment_id 	= sanitize_text_field($_GET['cf7pp_p']);
		} else {
			$payment_id = '';
		}
		
		cf7pp_paypal_redirect($post_id,$payment_id);
		exit;
		
	}
	
	// for stripe
	if (isset($_GET['cf7pp_stripe_redirect'])) {
		
		// get the id from the cf7pp_before_send_mail function theme redirect
		$post_id 	= sanitize_text_field($_GET['cf7pp_stripe_redirect']);
		
		if (isset($_GET['cf7pp_return'])) {
			$return_url 	= sanitize_text_field($_GET['cf7pp_return']);
		} else {
			$return_url = '';
		}
		
		// long contact form 7 form id
		if (isset($_GET['cf7pp_fid'])) {
			$fid 	= sanitize_text_field($_GET['cf7pp_fid']);
		} else {
			$fid = '';
		}
		
		if (isset($_GET['cf7pp_p'])) {
			$payment_id 	= sanitize_text_field($_GET['cf7pp_p']);
		} else {
			$payment_id = '';
		}
		
		cf7pp_stripe_redirect($post_id,$fid,$return_url,$payment_id);
		exit;
		
	}
}




$options = cf7pp_free_options();
$request_method = isset($options['request_method']) ? $options['request_method'] : 1; // Default to 1 if not set

if ($request_method == 1) {
    // Register AJAX actions
    add_action('wp_ajax_cf7pp_get_form_stripe_success', 'cf7pp_get_form_stripe_success_callback');
    add_action('wp_ajax_nopriv_cf7pp_get_form_stripe_success', 'cf7pp_get_form_stripe_success_callback');
}

function cf7pp_get_form_stripe_success_callback() {
    global $options;
	
	$request_method = isset($options['request_method']) ? $options['request_method'] : 1; // Default to 1 if not set
    
    $html_success = "
        ".$options['success']."
        <br />
    ";
    
    $response = array(
        'html' => $html_success,
    );

    // Check if request method is REST API (2) or AJAX (1)
    if ($request_method == 2) {
        return rest_ensure_response($response); // REST API response
    } else {
        echo json_encode($response); // AJAX response
        wp_die();
    }
}





// hook into contact form 7 - before send
add_action('wpcf7_before_send_mail', 'cf7pp_before_send_mail');
function cf7pp_before_send_mail() {

	$wpcf7 = WPCF7_ContactForm::get_current();

	// need to save submission for later and the variables get lost in the cf7 javascript redirect
	$submission_orig = WPCF7_Submission::get_instance();

	if ($submission_orig) {
		// get form post id
		$posted_data = $submission_orig->get_posted_data();
		
		$options = 			cf7pp_free_options();
		
		
		$post_id = 			$wpcf7->id();
		
		
		$gateway = 			strtolower(get_post_meta($post_id, "_cf7pp_gateway", true));
		$amount_total = 	get_post_meta($post_id, "_cf7pp_price", true);
		
		$enable = 			get_post_meta( $post_id, "_cf7pp_enable", true);
		$enable_stripe = 	get_post_meta( $post_id, "_cf7pp_enable_stripe", true);
		
		$stripe_email = 	strtolower(get_post_meta($post_id, "_cf7pp_stripe_email", true));
		
		if (!empty($stripe_email)) {
			$stripe_email = 	$posted_data[$stripe_email];
		} else {
			$stripe_email = '';
		}
		
		
		$gateway_orig = $gateway;
		
		if ($enable == '1') {
			$gateway = 'paypal';
		}
		
		if ($enable_stripe == '1') {
			$gateway = 'stripe';			
		}
		
		if ($enable == '1' && $enable_stripe == '1') {
			$gateway = $posted_data[$gateway_orig][0];
		}		
		
		
		
		if (isset($options['mode_stripe'])) {
			if ($options['mode_stripe'] == "1") {
				$tags['stripe_state'] = "test";
			} else {
				$tags['stripe_state'] = "live";
			}
		} else {
			$tags['stripe_state'] = "live";
		}

		// save payment
		$mode = ((strtolower($gateway) == 'paypal' && $options['mode'] == 1) || (strtolower($gateway) == 'stripe' && $options['mode_stripe'] == 1)) ? 'sandbox' : 'live';
		$payment_id = cf7pp_insert_payment($gateway, $mode, $amount_total, $post_id);
		
		
		if (empty($options['session'])) {
				$session = '1';
			} else {
				$session = $options['session'];
			}
			
			if ($session == '1') {
				
			setcookie('cf7pp_gateway', 				$gateway, time()+3600, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
			setcookie('cf7pp_amount_total', 		$amount_total, time()+3600, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
			setcookie('cf7pp_stripe_state', 		$tags['stripe_state'], time()+3600, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
			setcookie('cf7pp_stripe_email', 		$stripe_email, time()+3600, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
			setcookie('cf7pp_stripe_return', 		$options['stripe_return'], time()+3600, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
			setcookie('cf7pp_payment_id', 			$payment_id, time()+3600, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
			
		} else {
			session_start();
			$_SESSION['cf7pp_gateway'] = 		$gateway;
			$_SESSION['cf7pp_amount_total'] = 	$amount_total;
			$_SESSION['cf7pp_stripe_state'] = 	$tags['stripe_state'];
			$_SESSION['cf7pp_stripe_email'] = 	$stripe_email;
			$_SESSION['cf7pp_stripe_return'] = 	$options['stripe_return'];
			$_SESSION['cf7pp_payment_id'] = 	$payment_id;
			session_write_close();
		}

	}
}


// after submit post for js - used for method 1 and 2 for paypal and stripe
add_action('wp_ajax_cf7pp_get_form_post', 'cf7pp_get_form_post_callback');
add_action('wp_ajax_nopriv_cf7pp_get_form_post', 'cf7pp_get_form_post_callback');
function cf7pp_get_form_post_callback() {

	$options = cf7pp_free_options();


	if (empty($options['session'])) {
		$session = '1';
	} else {
		$session = $options['session'];
	}

	if ($session == '1') {
		
		if(isset($_COOKIE['cf7pp_gateway'])) {
			$gateway = $_COOKIE['cf7pp_gateway'];
		}
		
		if(isset($_COOKIE['cf7pp_amount_total'])) {
			$amount_total = $_COOKIE['cf7pp_amount_total'];
		}
		
		if(isset($_COOKIE['cf7pp_stripe_email'])) {
			$stripe_email = $_COOKIE['cf7pp_stripe_email'];
		}
		
		if(isset($_COOKIE['cf7pp_stripe_return'])) {
			$stripe_return = $_COOKIE['cf7pp_stripe_return'];
		}
		
		if(isset($_COOKIE['cf7pp_payment_id'])) {
			$cf7pp_payment_id = $_COOKIE['cf7pp_payment_id'];
		}
		
	} else {
		
		if(isset($_SESSION['cf7pp_gateway'])) {
			$gateway = $_SESSION['cf7pp_gateway'];
		}
		
		if(isset($_SESSION['cf7pp_amount_total'])) {
			$amount_total = $_SESSION['cf7pp_amount_total'];
		}
		
		if(isset($_SESSION['cf7pp_stripe_email'])) {
			$stripe_email = $_SESSION['cf7pp_stripe_email'];
		}
		
		if(isset($_SESSION['cf7pp_stripe_return'])) {
			$stripe_return = $_SESSION['cf7pp_stripe_return'];
		}
		
		if(isset($_SESSION['cf7pp_payment_id'])) {
			$cf7pp_payment_id = $_SESSION['cf7pp_payment_id'];
		}
	}
	
	if (empty($stripe_return) ) {
		$stripe_return = '';
	}
	
	
	$request_method = isset($options['request_method']) ? $options['request_method'] : 1; // Default to 1 if not set

	// Register REST API endpoints only if the request method is set to 2
	if ($request_method == 2) {
			
			return array(
			'gateway'          => $gateway,
			'amount_total'     => $amount_total,
			'email'            => !empty($stripe_email) ? $stripe_email : null,
			'stripe_return'    => $stripe_return,
			'payment_id'       => $cf7pp_payment_id,
		);
		
	} else {
	
		$response = array(
			'gateway'         		=> $gateway,
			'amount_total'         	=> $amount_total,
			'email'       	 		=> !empty($stripe_email) ? $stripe_email : null,
			'stripe_return'       	=> $stripe_return,
			'payment_id'       		=> $cf7pp_payment_id,
			
		);
		
		echo json_encode($response);
		
		wp_die();
		
	}
}