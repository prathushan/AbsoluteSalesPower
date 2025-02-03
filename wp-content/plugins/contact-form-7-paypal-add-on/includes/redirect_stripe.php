<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


function cf7pp_stripe_redirect($post_id,$fid,$return_url,$payment_id) {
	
	$options = cf7pp_free_options();
	
	
	// get variables
	$name = 	sanitize_text_field(get_post_meta($post_id, "_cf7pp_name", true));
	$price = 	sanitize_text_field(get_post_meta($post_id, "_cf7pp_price", true));
	$id = 		sanitize_text_field(get_post_meta($post_id, "_cf7pp_id", true));
	
	if ($options['mode_stripe'] == "1") {
		$account_id = isset($options['acct_id_test']) ? $options['acct_id_test'] : '';
		$token = isset($options['stripe_connect_token_test']) ? $options['stripe_connect_token_test'] : '';
	} else {
		$account_id = isset($options['acct_id_live']) ? $options['acct_id_live'] : '';
		$token = isset($options['stripe_connect_token_live']) ? $options['stripe_connect_token_live'] : '';
	}

	if (empty($account_id)) {
		if ($options['mode_stripe'] == "1") {
			$stripe_key = isset($options['pub_key_test']) ? $options['pub_key_test'] : '';
			$stripe_sec = isset($options['sec_key_test']) ? $options['sec_key_test'] : '';
		} else {
			$stripe_key = isset($options['pub_key_live']) ? $options['pub_key_live'] : '';
			$stripe_sec = isset($options['sec_key_live']) ? $options['sec_key_live'] : '';
		}
	}

	if (empty($options['session'])) {
		$session = '1';
	} else {
		$session = sanitize_text_field($options['session']);
	}

	if ($session == '1') {
		$stripe_return	= isset($_COOKIE['cf7pp_stripe_return']) ? sanitize_text_field($_COOKIE['cf7pp_stripe_return']) : NULL;
		$stripe_email	= isset($_COOKIE['cf7pp_stripe_email']) ? sanitize_text_field($_COOKIE['cf7pp_stripe_email']) : NULL;
	} else {		
		$stripe_return 	= isset($_SESSION['cf7pp_stripe_return']) ? sanitize_text_field($_SESSION['cf7pp_stripe_return']) : NULL;
		$stripe_email 	= isset($_SESSION['cf7pp_stripe_email']) ? sanitize_text_field($_SESSION['cf7pp_stripe_email']) : NULL;
	}	
	
	// email	
	if (empty($stripe_email)) {
		$email = null;
	} else {
		$email = $stripe_email;
	}
	
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$email = null;
	}
	
	// currency
	if ($options['currency'] == "1") { $currency = "AUD"; }
	if ($options['currency'] == "2") { $currency = "BRL"; }
	if ($options['currency'] == "3") { $currency = "CAD"; }
	if ($options['currency'] == "4") { $currency = "CZK"; }
	if ($options['currency'] == "5") { $currency = "DKK"; }
	if ($options['currency'] == "6") { $currency = "EUR"; }
	if ($options['currency'] == "7") { $currency = "HKD"; }
	if ($options['currency'] == "8") { $currency = "HUF"; }
	if ($options['currency'] == "9") { $currency = "ILS"; }
	if ($options['currency'] == "10") { $currency = "JPY"; }
	if ($options['currency'] == "11") { $currency = "MYR"; }
	if ($options['currency'] == "12") { $currency = "MXN"; }
	if ($options['currency'] == "13") { $currency = "NOK"; }
	if ($options['currency'] == "14") { $currency = "NZD"; }
	if ($options['currency'] == "15") { $currency = "PHP"; }
	if ($options['currency'] == "16") { $currency = "PLN"; }
	if ($options['currency'] == "17") { $currency = "GBP"; }
	if ($options['currency'] == "18") { $currency = "RUB"; }
	if ($options['currency'] == "19") { $currency = "SGD"; }
	if ($options['currency'] == "20") { $currency = "SEK"; }
	if ($options['currency'] == "21") { $currency = "CHF"; }
	if ($options['currency'] == "22") { $currency = "TWD"; }
	if ($options['currency'] == "23") { $currency = "THB"; }
	if ($options['currency'] == "24") { $currency = "TRY"; }
	if ($options['currency'] == "25") { $currency = "USD"; }
	if ($options['currency'] == "26") { $currency = "RON"; }
	
	
	$cancel_url = $return_url;
	
	// return url
	if (!empty($stripe_return)) {
		$success_url = $stripe_return;
	} else {
		$success_url = $return_url;
	}
	
	if (filter_var($success_url, FILTER_VALIDATE_URL) === FALSE) {
		echo "Website admin: Success or Return URL is not valid.";
		exit;
	}
	
	if (filter_var($cancel_url, FILTER_VALIDATE_URL) === FALSE) {
		echo "Website admin: Success or Return URL is not valid.";
		exit;
	}
	
	if (empty($name)) 		{ $name =  "(No item name)"; }
	
	
	if (empty($account_id) && (empty($stripe_key) || empty($stripe_sec))) {
		echo "Website Admin: Please connect your Stripe account on the settings page (Contact -> PayPal & Stripe Settings -> Stripe)";
		exit;
	}
	
	
	if (!empty($price)) {
		
		if ($currency != 'JPY') {
			// convert amount to cents
			$amount = $price * 100;
		} else {
			$amount = $price;
			$amount = (int)$amount;
		}
		
		
		if (!empty($id)) {
			$description = $id;
		} else {
			$description = ' ';
		}
		
		$line_items[] = [
			'price_data' => [
				'currency' 		=> $currency,
				'unit_amount' 	=> $amount,
				'product_data' 	=> [
					'name' 			=> $name,
					'description' 	=> $description,
				],
			],
			'quantity' => 1,
		];
		
	}

	// Stripe does not allow totals of 0.00, so show error if this happens
	if ($amount == 0) {
		echo 'Website Admin: Price cannot be set to 0.00.';
		exit;
	}



	if (!empty($stripe_sec)) {
		\Stripe\Stripe::setApiKey($stripe_sec);

		$checkout_session = \Stripe\Checkout\Session::create([
			'submit_type' 				=> 'pay',
			'payment_method_types' 		=> ['card'],
			'customer_email' 			=> $email,
			'line_items' 				=> $line_items,
			'mode' 						=> 'payment',
			'success_url' 				=> add_query_arg(array(
				'cf7pp_stripe_success'	=> true,
				'cf7pp_fid'				=> $fid,
				'id'					=> '{CHECKOUT_SESSION_ID}'
			), $success_url),
			'cancel_url' 					=> $cancel_url,
			'client_reference_id'			=> $payment_id
		]);

		if (empty($checkout_session->id)) {
			echo "An unexpected error occurred. Please try again.";
			exit;
		}
	} else {
		$success_url = add_query_arg(
			array(
				'cf7pp_stripe_success'	=> true,
				'cf7pp_fid'				=> $fid,
				'id'					=> '{CHECKOUT_SESSION_ID}'
			),
			$success_url
		);

		$stripe_connect_url = CF7PP_STRIPE_CONNECT_ENDPOINT . '?' . http_build_query(
			array(
				'action'				=> 'checkoutSession',
				'mode'					=> $options['mode_stripe'] == 1 ? 'sandbox' : 'live',
				'customer_email'		=> $email,
				'line_items'			=> $line_items,
				'success_url'			=> $success_url,
				'cancel_url'			=> $cancel_url,
				'notice_url'			=> cf7pp_get_stripe_connect_webhook_url(),
				'client_reference_id'	=> $payment_id,
		  		'account_id'			=> $account_id,
		  		'token'					=> $token
			)
		);

		$checkout_session = wp_remote_get($stripe_connect_url);
		$checkout_session = json_decode($checkout_session['body']);

		if (empty($checkout_session->session_id)) {
			echo "An unexpected error occurred. Please try again.";
			exit;
		}
	}	
	
	?>
	<!DOCTYPE html>
	<html>
		<head>
			<script src="https://js.stripe.com/v3/"></script>
		</head>
		<body>
			<script type="text/javascript">
				<?php if (!empty($stripe_key)) { ?>
				var stripe = Stripe('<?php echo $stripe_key; ?>');			
				window.onload = function() {
					stripe.redirectToCheckout({sessionId: '<?php echo $checkout_session->id ?>'});
				};
				<?php } else { ?>
				var stripe = Stripe('<?php echo $checkout_session->stripe_key; ?>', {stripeAccount: '<?php echo $account_id; ?>'});			
				window.onload = function() {
					stripe.redirectToCheckout({sessionId: '<?php echo $checkout_session->session_id ?>'});
				};
			<?php } ?>
			</script>
		</body>
	</html>

<?php } ?>