<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


// admin table
function cf7pp_admin_table() {

	// get options
	$options = cf7pp_free_options();

	if ( !current_user_can( "manage_options" ) )  {
	wp_die( __( "You do not have sufficient permissions to access this page." ) );
	}



	// save and update options
	if (isset($_POST['update'])) {

		if ( empty( $_POST['cf7pp_nonce_field'] ) || !wp_verify_nonce( $_POST['cf7pp_nonce_field'], 'cf7pp_save_settings') ) {
			wp_die( __( "You do not have sufficient permissions to access this page." ) );
		}

		$options['currency'] = 					sanitize_text_field($_POST['currency']);
		if (empty($options['currency'])) { 		$options['currency'] = ''; }

		$options['language'] = 					sanitize_text_field($_POST['language']);
		if (empty($options['language'])) { 		$options['language'] = ''; }

		$options['mode'] = 						sanitize_text_field($_POST['mode']);
		if (empty($options['mode'])) { 			$options['mode'] = '2'; }

		$options['mode_stripe'] = 				sanitize_text_field($_POST['mode_stripe']);
		if (empty($options['mode_stripe'])) { 	$options['mode_stripe'] = '2'; }

		$options['cancel'] = 					sanitize_text_field($_POST['cancel']);
		if (empty($options['cancel'])) { 		$options['cancel'] = ''; }

		$options['return'] = 					sanitize_text_field($_POST['return']);
		if (empty($options['return'])) { 		$options['return'] = ''; }

		$options['redirect'] = 					sanitize_text_field($_POST['redirect']);
		if (empty($options['redirect'])) { 		$options['redirect'] = '1'; }
		
		$options['request_method'] = 					sanitize_text_field($_POST['request_method']);
		if (empty($options['request_method'])) { 		$options['request_method'] = '1'; }
		
		$options['session'] = 					sanitize_text_field($_POST['session']);
		if (empty($options['session'])) { 		$options['session'] = '1'; }

		$options['stripe_return'] = 			sanitize_text_field($_POST['stripe_return']);
		if (empty($options['stripe_return'])) { $options['stripe_return'] = ''; }
		
		$options['success'] = 					sanitize_text_field($_POST['success']);
		if (empty($options['success'])) { 		$options['success'] = 'Payment Successful'; }
		
		$options['failed'] = 					sanitize_text_field($_POST['failed']);
		if (empty($options['failed'])) { 		$options['failed'] = 'Payment Failed'; }

		cf7pp_free_options_update( $options );

		echo "<br /><div class='updated'><p><strong>"; _e("Settings Updated."); echo "</strong></p></div>";

	}



	if (isset($_POST['hidden_tab_value'])) {
		$active_tab =  (int) $_POST['hidden_tab_value'];
	} else {
		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : '1';
		$active_tab = (int) $active_tab;
	}
	
	if ($active_tab == 0) {
		$active_tab = 1;
	}

	?>


<form method='post'>

	<table width='70%'><tr><td>
	<div class='wrap'><h2>Contact Form 7 - PayPal & Stripe Settings</h2></div><br /></td><td><br />
	<input type='submit' name='btn2' class='button-primary' style='font-size: 17px;line-height: 28px;height: 32px;float: right;' value='Save Settings'>
	</td></tr></table>

	<table width='100%'><tr><td width='70%' valign='top'>




		<h2 class="nav-tab-wrapper">
			<a onclick='closetabs("1,3,4,5,6,7");newtab("1");' href="#" id="id1" class="nav-tab <?php echo $active_tab == '1' ? 'nav-tab-active' : ''; ?>">Getting Started</a>
			<a onclick='closetabs("1,3,4,5,6,7");newtab("3");' href="#" id="id3" class="nav-tab <?php echo $active_tab == '3' ? 'nav-tab-active' : ''; ?>">Language & Currency</a>
			<a onclick='closetabs("1,3,4,5,6,7");newtab("4");' href="#" id="id4" class="nav-tab <?php echo $active_tab == '4' ? 'nav-tab-active' : ''; ?>">PayPal</a>
			<a onclick='closetabs("1,3,4,5,6,7");newtab("5");' href="#" id="id5" class="nav-tab <?php echo $active_tab == '5' ? 'nav-tab-active' : ''; ?>">Stripe</a>
			<a onclick='closetabs("1,3,4,5,6,7");newtab("6");' href="#" id="id6" class="nav-tab <?php echo $active_tab == '6' ? 'nav-tab-active' : ''; ?>">Other</a>
			<a onclick='closetabs("1,3,4,5,6,7");newtab("7");' href="#" id="id7" class="nav-tab <?php echo $active_tab == '7' ? 'nav-tab-active' : ''; ?>">Extensions</a>
		</h2>
		<br />




	</td><td colspan='3'></td></tr><tr><td valign='top'>









	<div id="1" style="display:none;border: 1px solid #CCCCCC;<?php echo $active_tab == '1' ? 'display:block;' : ''; ?>">
		<div style="background-color:#E4E4E4;padding:8px;color:#000;font-size:15px;color:#464646;font-weight: 700;border-bottom: 1px solid #CCCCCC;">
			&nbsp; Getting Started
		</div>
		<div style="background-color:#fff;padding:8px;">
		
			<h3>Important - If your form has trouble redirecting - try <a href='admin.php?page=cf7pp_admin_table&tab=6'>changing these settings</a>. This will fix most issues easily.</h3>
			
			<hr>
			
			This plugin allows you to accept payments through your Contact Form 7 forms.
			
			<br /><br />
			
			On this page, you can setup your general PayPal & Stripe settings which will be used for all of your <a href='admin.php?page=wpcf7'>Contact Form 7 forms</a>.
			
			<br /><br />
			
			When you go to your list of contact forms, make a new form or edit an existing form, you will see a new tab called 'PayPal & Stripe'. Here you can
			set individual settings for that specific contact form.
			
			<br /><br />
			
			Once you have PayPal or Stripe enabled on a form, you will receive an email as soon as the customer submits the form. You can view the payment status on the <a href='edit.php?post_type=cf7pp_payments'>PayPal & Stripe Payments page</a>.
			
			<br /><br />
			
			You can view documentation for this plugin <a target='_blank' href='https://wpplugin.org/knowledgebase_category/contact-form-7-paypal-stripe-add-on-free/'>here</a>.
			
			<br /><br />
			
			If you need support, please post your question <a target='_blank' href='https://wordpress.org/support/plugin/contact-form-7-paypal-add-on/'>here</a>.
			
			<br /><br />
			
			A lot of work went into building this plugin. If you enjoy it, please leave a 5 star review <a target='_blank' href='https://wordpress.org/support/plugin/contact-form-7-paypal-add-on/reviews/?filter=5#new-post'>here</a>.
			
			<br />
			
		</div>
	</div>



	<div id="3" style="display:none;border: 1px solid #CCCCCC;<?php echo $active_tab == '3' ? 'display:block;' : ''; ?>">
		<div style="background-color:#E4E4E4;padding:8px;color:#000;font-size:15px;color:#464646;font-weight: 700;border-bottom: 1px solid #CCCCCC;">
			&nbsp; Language & Currency
		</div>
		<div style="background-color:#fff;padding:8px;">

			<table>

				<tr><td class='cf7pp_width'>
					<b>Language:</b>
				</td><td>
					<select name="language">
					<option <?php if ($options['language'] == "1") { echo "SELECTED"; } ?> value="1">Danish</option>
					<option <?php if ($options['language'] == "2") { echo "SELECTED"; } ?> value="2">Dutch</option>
					<option <?php if ($options['language'] == "3") { echo "SELECTED"; } ?> value="3">English</option>
					<option <?php if ($options['language'] == "20") { echo "SELECTED"; } ?> value="20">English - UK</option>
					<option <?php if ($options['language'] == "4") { echo "SELECTED"; } ?> value="4">French</option>
					<option <?php if ($options['language'] == "5") { echo "SELECTED"; } ?> value="5">German</option>
					<option <?php if ($options['language'] == "6") { echo "SELECTED"; } ?> value="6">Hebrew</option>
					<option <?php if ($options['language'] == "7") { echo "SELECTED"; } ?> value="7">Italian</option>
					<option <?php if ($options['language'] == "8") { echo "SELECTED"; } ?> value="8">Japanese</option>
					<option <?php if ($options['language'] == "9") { echo "SELECTED"; } ?> value="9">Norwgian</option>
					<option <?php if ($options['language'] == "10") { echo "SELECTED"; } ?> value="10">Polish</option>
					<option <?php if ($options['language'] == "11") { echo "SELECTED"; } ?> value="11">Portuguese</option>
					<option <?php if ($options['language'] == "12") { echo "SELECTED"; } ?> value="12">Russian</option>
					<option <?php if ($options['language'] == "13") { echo "SELECTED"; } ?> value="13">Spanish</option>
					<option <?php if ($options['language'] == "14") { echo "SELECTED"; } ?> value="14">Swedish</option>
					<option <?php if ($options['language'] == "15") { echo "SELECTED"; } ?> value="15">Simplified Chinese -China only</option>
					<option <?php if ($options['language'] == "16") { echo "SELECTED"; } ?> value="16">Traditional Chinese - Hong Kong only</option>
					<option <?php if ($options['language'] == "17") { echo "SELECTED"; } ?> value="17">Traditional Chinese - Taiwan only</option>
					<option <?php if ($options['language'] == "18") { echo "SELECTED"; } ?> value="18">Turkish</option>
					<option <?php if ($options['language'] == "19") { echo "SELECTED"; } ?> value="19">Thai</option>
					</select>
			</td></tr>

				<tr><td>
				</td></tr>

				<tr><td class='cf7pp_width'>
				<b>Currency:</b></td><td>
				<select name="currency">
				<option <?php if ($options['currency'] == "1") { echo "SELECTED"; } ?> value="1">Australian Dollar - AUD</option>
				<option <?php if ($options['currency'] == "2") { echo "SELECTED"; } ?> value="2">Brazilian Real - BRL</option>
				<option <?php if ($options['currency'] == "3") { echo "SELECTED"; } ?> value="3">Canadian Dollar - CAD</option>
				<option <?php if ($options['currency'] == "4") { echo "SELECTED"; } ?> value="4">Czech Koruna - CZK</option>
				<option <?php if ($options['currency'] == "5") { echo "SELECTED"; } ?> value="5">Danish Krone - DKK</option>
				<option <?php if ($options['currency'] == "6") { echo "SELECTED"; } ?> value="6">Euro - EUR</option>
				<option <?php if ($options['currency'] == "7") { echo "SELECTED"; } ?> value="7">Hong Kong Dollar - HKD</option>
				<option <?php if ($options['currency'] == "8") { echo "SELECTED"; } ?> value="8">Hungarian Forint - HUF</option>
				<option <?php if ($options['currency'] == "9") { echo "SELECTED"; } ?> value="9">Israeli New Sheqel - ILS</option>
				<option <?php if ($options['currency'] == "10") { echo "SELECTED"; } ?> value="10">Japanese Yen - JPY</option>
				<option <?php if ($options['currency'] == "11") { echo "SELECTED"; } ?> value="11">Malaysian Ringgit - MYR</option>
				<option <?php if ($options['currency'] == "12") { echo "SELECTED"; } ?> value="12">Mexican Peso - MXN</option>
				<option <?php if ($options['currency'] == "13") { echo "SELECTED"; } ?> value="13">Norwegian Krone - NOK</option>
				<option <?php if ($options['currency'] == "14") { echo "SELECTED"; } ?> value="14">New Zealand Dollar - NZD</option>
				<option <?php if ($options['currency'] == "15") { echo "SELECTED"; } ?> value="15">Philippine Peso - PHP</option>
				<option <?php if ($options['currency'] == "16") { echo "SELECTED"; } ?> value="16">Polish Zloty - PLN</option>
				<option <?php if ($options['currency'] == "17") { echo "SELECTED"; } ?> value="17">Pound Sterling - GBP</option>
				<option <?php if ($options['currency'] == "26") { echo "SELECTED"; } ?> value="26">Romanian Leu - RON (Stripe Only)</option>
				<option <?php if ($options['currency'] == "18") { echo "SELECTED"; } ?> value="18">Russian Ruble - RUB</option>
				<option <?php if ($options['currency'] == "19") { echo "SELECTED"; } ?> value="19">Singapore Dollar - SGD</option>
				<option <?php if ($options['currency'] == "20") { echo "SELECTED"; } ?> value="20">Swedish Krona - SEK</option>
				<option <?php if ($options['currency'] == "21") { echo "SELECTED"; } ?> value="21">Swiss Franc - CHF</option>
				<option <?php if ($options['currency'] == "22") { echo "SELECTED"; } ?> value="22">Taiwan New Dollar - TWD</option>
				<option <?php if ($options['currency'] == "23") { echo "SELECTED"; } ?> value="23">Thai Baht - THB</option>
				<option <?php if ($options['currency'] == "24") { echo "SELECTED"; } ?> value="24">Turkish Lira - TRY</option>
				<option <?php if ($options['currency'] == "25") { echo "SELECTED"; } ?> value="25">U.S. Dollar - USD</option>
				</select></td></tr>

			</table>

		</div>
	</div>




	<div id="4" style="display:none;border: 1px solid #CCCCCC;<?php echo $active_tab == '4' ? 'display:block;' : ''; ?>">
		<div style="background-color:#E4E4E4;padding:8px;color:#000;font-size:15px;color:#464646;font-weight: 700;border-bottom: 1px solid #CCCCCC;">
		&nbsp; PayPal Account
		</div>
		<div style="background-color:#fff;padding:8px;">

            <?php echo cf7pp_free_ppcp_status_markup(); ?>

			<table width='100%'>
                <tr><td colspan='2'><br /></td></tr>

                <?php if ( !empty( $options['liveaccount'] ) ) { ?>
				<tr><td class='cf7pp_width'>
				<b>Live Account: </b></td><td><input type='text' size=40 name='liveaccount' value='<?php echo $options['liveaccount']; ?>' readonly />
				</td></tr>

				<tr><td class='cf7pp_width'></td><td>
				<br />Enter a valid Merchant account ID (strongly recommend) or PayPal account email address. All payments will go to this account.
				<br /><br />You can find your Merchant account ID in your PayPal account under Profile -> My business info -> Merchant account ID

				<br /><br />If you don't have a PayPal account, you can sign up for free at <a target='_blank' href='https://paypal.com'>PayPal</a>. <br /><br />
				</td></tr>
                <?php } ?>

	            <?php if ( !empty( $options['sandboxaccount'] ) ) { ?>
				<tr><td class='cf7pp_width'>
				<b>Sandbox Account: </b></td><td><input type='text' size=40 name='sandboxaccount' value='<?php echo $options['sandboxaccount']; ?>' readonly />
				</td></tr>

				<tr><td class='cf7pp_width'></td><td>
				Enter a valid sandbox PayPal account email address. A Sandbox account is a PayPal accont with fake money used for testing. This is useful to make sure your PayPal account and settings are working properly being going live.
				<br /><br />To create a Sandbox account, you first need a Developer Account. You can sign up for free at the <a target='_blank' href='https://www.paypal.com/webapps/merchantboarding/webflow/unifiedflow?execution=e1s2'>PayPal Developer</a> site. <br /><br />

				Once you have made an account, create a Sandbox Business and Personal Account <a target='_blank' href='https://developer.paypal.com/webapps/developer/applications/accounts'>here</a>. Enter the Business acount email on this page and use the Personal account username and password to buy something on your site as a customer.
				<br /><br />
				</td></tr>
	            <?php } ?>

				<tr><td class='cf7pp_width'>
				<b>Sandbox Mode:</b></td><td>
				<input <?php if ($options['mode'] == "1") { echo "checked='checked'"; } ?> type='radio' name='mode' value='1'>On (Sandbox mode)
				<input <?php if ($options['mode'] == "2") { echo "checked='checked'"; } ?> type='radio' name='mode' value='2'>Off (Live mode)
				</td></tr>

			</table>

		</div>
	</div>




	<div id="5" style="display:none;border: 1px solid #CCCCCC;<?php echo $active_tab == '5' ? 'display:block;' : ''; ?>">
		<div style="background-color:#E4E4E4;padding:8px;color:#000;font-size:15px;color:#464646;font-weight: 700;border-bottom: 1px solid #CCCCCC;">
		&nbsp; Stripe Account
		</div>
		<div style="background-color:#fff;padding:8px;">

			<table width='100%'>
				<tr><td class='cf7pp_width'><b>Connection status: </b></td><td><?php cf7pp_stripe_connection_status_html(); ?></td></tr>

				<tr><td colspan="2"><br /></td></tr>

				<?php if ( !empty($options['pub_key_live']) && !empty($options['sec_key_live']) ) { ?>
				<tr><td class='cf7pp_width'><b>Live Publishable Key: </b></td><td><input type='text' size=40 name='pub_key_live' value='<?php echo $options['pub_key_live']; ?>' disabled="disabled"></td></tr>
				<tr><td class='cf7pp_width'><b>Live Secret Key: </b></td><td><input type='text' size=40 name='sec_key_live' value='<?php echo $options['sec_key_live']; ?>' disabled="disabled"></td></tr>
				<tr><td colspan="2"><br /></td></tr>
				<?php } ?>

				<?php if ( !empty($options['pub_key_test']) && !empty($options['sec_key_test']) ) { ?>
				<tr><td class='cf7pp_width'><b>Test Publishable Key: </b></td><td><input type='text' size=40 name='pub_key_test' value='<?php echo $options['pub_key_test']; ?>' disabled="disabled"></td></tr>
				<tr><td class='cf7pp_width'><b>Test Secret Key: </b></td><td><input type='text' size=40 name='sec_key_test' value='<?php echo $options['sec_key_test']; ?>' disabled="disabled"></td></tr>
				<tr><td colspan="2"><br /></td></tr>
				<?php } ?>

				<tr><td class='cf7pp_width'><b>Sandbox Mode:</b></td><td>

				<input <?php if ($options['mode_stripe'] == "1") { echo "checked='checked'"; } ?> type='radio' name='mode_stripe' value='1'>On (Sandbox mode)
				<input <?php if ($options['mode_stripe'] == "2") { echo "checked='checked'"; } ?> type='radio' name='mode_stripe' value='2'>Off (Live mode)</td></tr>


				<tr><td>
				<br />
				</td></tr>

				<tr><td class='cf7pp_width'><b>Default Text: </b></td><td></td></tr>
				<tr><td class='cf7pp_width'><b>Payment Successful: </b></td><td><input type='text' size='40' name='success' value='<?php echo $options['success']; ?>'></td></tr>
				<tr><td class='cf7pp_width'><b>Payment Failed: </b></td><td><input type='text' size='40' name='failed' value='<?php echo $options['failed']; ?>'></td></tr>
				
			</table>

		</div>
	</div>


	<div id="6" style="display:none;border: 1px solid #CCCCCC;<?php echo $active_tab == '6' ? 'display:block;' : ''; ?>">
		<div style="background-color:#E4E4E4;padding:8px;font-size:15px;color:#464646;font-weight: 700;border-bottom: 1px solid #CCCCCC;">
			&nbsp; Other Settings
		</div>
		<div style="background-color:#fff;padding:8px;">

			<table style="width: 100%;">

				<tr><td class='cf7pp_width'><b>PayPal Cancel URL: </b></td><td><input type='text' name='cancel' value='<?php echo $options['cancel']; ?>'> Optional <br /></td></tr>
				<tr><td class='cf7pp_width'></td><td>If the customer goes to PayPal and clicks the cancel button, where do they go. Example: http://example.com/cancel. Max length: 1,024. </td></tr>

				<tr><td>
				<br />
				</td></tr>

				<tr><td class='cf7pp_width'><b>PayPal Return URL: </b></td><td><input type='text' name='return' value='<?php echo $options['return']; ?>'> Optional <br /></td></tr>
				<tr><td class='cf7pp_width'></td><td>If the customer goes to PayPal and successfully pays, where are they redirected to after. Example: http://example.com/thankyou. Max length: 1,024. </td></tr>
				
				<tr><td>
				<br />
				</td></tr>
				
				<tr><td class='cf7pp_width'><b>Stripe Return URL: </b></td><td><input type='text' name='stripe_return' value='<?php echo $options['stripe_return']; ?>'> Optional <br /></td></tr>
				<tr><td class='cf7pp_width'></td><td>If the customer successfully pays with Stripe, where are they redirected to after. Example: http://example.com/thankyou. </td></tr>
				
				<tr><td>
				<br />
				</td></tr>
				
				<tr><td class='cf7pp_width'>
				<b>Redirect Method:</b></td><td>
				<input <?php if ($options['redirect'] == "1") { echo "checked='checked'"; } ?> type='radio' name='redirect' value='1'>1 (DOM wpcf7mailsent event listener)
				<input <?php if ($options['redirect'] == "2") { echo "checked='checked'"; } ?> type='radio' name='redirect' value='2'>2 (Form sent class listener)
				</td></tr>
				<tr><td class='cf7pp_width'></td><td>Method 1 recommend unless the form has problems redirecting.</td></tr>
				
				
				<tr><td>
				<br />
				</td></tr>
				
				<tr><td class='cf7pp_width'>
				<b>Request Method:</b></td><td>
				<input <?php if ($options['request_method'] == "1") { echo "checked='checked'"; } ?> type='radio' name='request_method' value='1'>1 (Admin Ajax)
				<input <?php if ($options['request_method'] == "2") { echo "checked='checked'"; } ?> type='radio' name='request_method' value='2'>2 (Rest API)
				</td></tr>
				<tr><td class='cf7pp_width'></td><td>Method 1 recommend unless the form has problems redirecting.</td></tr>
				
				
				<tr><td>
				<br />
				</td></tr>
				
				<tr><td class='cf7pp_width'>
				<b>Temporary Storage Method:</b></td><td>
				<input <?php if ($options['session'] == "1") { echo "checked='checked'"; } ?> type='radio' name='session' value='1'>Cookies
				<input <?php if ($options['session'] == "2") { echo "checked='checked'"; } ?> type='radio' name='session' value='2'>Sessions
				</td></tr>
				<tr><td class='cf7pp_width'></td><td>Cookies are recommend unless the form has problems.</td></tr>

			</table>

		</div>
	</div>
	
	
	<div id="7" style="display:none;border: 1px solid #CCCCCC;<?php echo $active_tab == '7' ? 'display:block;' : ''; ?>">
		<div style="background-color:#E4E4E4;padding:8px;font-size:15px;color:#464646;font-weight: 700;border-bottom: 1px solid #CCCCCC;">
			&nbsp; Extensions
		</div>
		<div style="background-color:#fff;padding:8px;">
			
			<table style="width: 100%;">
				
				<?php
				cf7pp_extensions_page();
				?>
				
			</table>
			
		</div>
	</div>




	<input type='hidden' name='update' value='1'>
	<input type='hidden' name='hidden_tab_value' id="hidden_tab_value" value="<?php echo $active_tab; ?>">
    <?php wp_nonce_field( 'cf7pp_save_settings','cf7pp_nonce_field' ); ?>

</form>













	</td><td width="3%" valign="top">

	</td><td width="24%" valign="top">

	<div style="border: 1px solid #CCCCCC;width:400px;">	
		<div style="background-color:#E4E4E4;padding:8px;font-size:15px;color:#464646;font-weight: 700;border-bottom: 1px solid #CCCCCC;">
		&nbsp; Pro Version Features
		</div>
		
		<div style="background-color:#fff;padding:8px;">
		
		<br />
		We offer a Pro version of our plugins for those who want more features.
		<br />
		
		<br />
		<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> Only send email if PayPal / Stripe payment is Successful <br />
		<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> No 2% PayPal per transaction application fee <br />
		<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> No 2% Stripe per transaction application fee <br />
		<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> Link any form item to price, quantity, or description <br />
		<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> Sell up to 5 items per form  <br />
		<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> Charge tax and shipping <br />
		<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> Separate PayPal & Stripe account per form <br />
		<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> Skip redirecting based upon form elements<br />
		<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> Accept recurring payments with our <a target='_blank' href='https://wpplugin.org/downloads/contact-form-7-recurring-payments-pro/'>Recurring Add-on</a><br />
		<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> Amazing plugin support agents from USA<br />
		<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> No risk, 30 day return policy <br />
		<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> Many more features! <br />
		
		<br />
		<b><center>Over 4,200 happy Pro version customers</center></b>
		
		<br />
		<center><a target='_blank' href="https://wpplugin.org/downloads/contact-form-7-paypal-add-on/" class='button-primary' style='font-size: 17px;line-height: 28px;height: 32px;'>Get the Pro Version</a></center>
		<br />
		</div>
	</div>
	
	
	</td><td width="2%" valign="top">



	</td></tr></table>

	<?php

}

function cf7pp_free_ppcp_status_markup() {
	ob_start();

	$options = cf7pp_free_options();
	$status = cf7pp_free_ppcp_status();
	if ( !empty( $status ) ) {
		if ( empty( $status['errors'] ) ) {
			$notice_type = 'success';
			$show_links = false;
		} else {
			$notice_type = 'error';
			$show_links = true;
		}
		?>
        <div id="cf7pp-ppcp-status-table">
            <table>
                <tr>
                    <td class="cf7pp-cell-left">
                        <b>Connection status: </b>
                    </td>
                    <td>
                        <div class="notice inline cf7pp-ppcp-connect notice-<?php echo $notice_type; ?>">
                            <p>
								<?php if ( !empty( $status['legal_name'] ) ) { ?>
                                    <strong><?php echo $status['legal_name']; ?></strong>
                                    <br>
								<?php } ?>
								<?php echo !empty( $status['primary_email'] ) ? $status['primary_email'] . ' — ' : ''; ?>Administrator (Owner)</p>
								<p>Pay as you go pricing: 2% per-transaction fee + PayPal fees.</p>
                        </div>
                        <div>
							<?php $reconnect_mode = $status['env'] === 'live' ? 'sandbox' : 'live'; ?>
                            Your PayPal account is connected in <strong><?php echo $status['env']; ?></strong> mode.
							<?php
							$query_args = [
								'action' => 'cf7pp-ppcp-onboarding-start',
								'nonce' => wp_create_nonce( 'cf7pp-ppcp-onboarding-start' )
							];
							if ( $reconnect_mode === 'sandbox' ) {
								$query_args['sandbox'] = 1;
							}
							?>
                            <a
                                class="cf7pp-ppcp-onboarding-start"
                                data-paypal-button="true"
                                href="<?php echo add_query_arg( $query_args, admin_url( 'admin-ajax.php' ) ); ?>"
                                target="PPFrame"
                            >Connect in <strong><?php echo $reconnect_mode; ?></strong> mode</a> or <a href="#" id="cf7pp-ppcp-disconnect">disconnect this account</a>.
                        </div>

						<?php if ( !empty( $status['errors'] ) ) { ?>
                            <p>
                                <strong>There were errors connecting your PayPal account. Resolve them in your account settings, by contacting support or by reconnecting your PayPal account.</strong>
                            </p>
                            <p>
                                <strong>See below for more details.</strong>
                            </p>
                            <ul class="cf7pp-ppcp-list cf7pp-ppcp-list-error">
								<?php foreach ( $status['errors'] as $error ) { ?>
                                    <li><?php echo $error; ?></li>
								<?php } ?>
                            </ul>
						<?php } ?>

						<?php if ( $show_links ) { ?>
                            <ul class="cf7pp-ppcp-list">
                                <li><a href="https://www.paypal.com/myaccount/settings/">PayPal account settings</a></li>
                                <li><a href="https://www.paypal.com/us/smarthelp/contact-us">PayPal support</a></li>
                            </ul>
						<?php } ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <br />
                    </td>
                </tr>
            </table>
        </div>
		<?php
	} else { ?>
        <table id="cf7pp-ppcp-status-table" class="cf7pp-ppcp-initial-view-table">
            <tr>
                <td>
                    <img class="cf7pp-ppcp-paypal-logo" src="<?php echo CF7PP_FREE_URL; ?>imgs/paypal-logo.png" alt="paypal-logo" />
                </td>
                <td class="cf7pp-ppcp-align-right cf7pp-ppcp-icons">
                    <img class="cf7pp-ppcp-paypal-methods" src="<?php echo CF7PP_FREE_URL; ?>imgs/paypal-express.png" alt="paypal-expresss" />
                    <img class="cf7pp-ppcp-paypal-methods" src="<?php echo CF7PP_FREE_URL; ?>imgs/paypal-advanced.png" alt="paypal-advanced" />
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <h3 class="cf7pp-ppcp-title">PayPal: The all-in-one checkout solution</h3>
                    <ul class="cf7pp-ppcp-list">
                        <li>Help drive conversion by offering customers a seamless checkout experience</li>
                        <li>Securely accepts all major credit/debit cards and local payment methods with the strength of the PayPal network</li>
                        <li>You only pay the standard PayPal fees + 2%.</li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td>
					<?php
					$mode = intval( $options['mode'] );
					$query_args = [
						'action' => 'cf7pp-ppcp-onboarding-start',
						'nonce' => wp_create_nonce( 'cf7pp-ppcp-onboarding-start' )
					];
					if ( $mode === 1 ) {
						$query_args['sandbox'] = 1;
					}
					?>
                    <a
                        id="cf7pp-ppcp-onboarding-start-btn"
                        class="cf7pp-ppcp-button cf7pp-ppcp-onboarding-start"
                        data-paypal-button="true"
                        href="<?php echo add_query_arg( $query_args, admin_url( 'admin-ajax.php' ) ); ?>"
                        target="PPFrame"
                    >Get started</a>
                </td>
                <td class="cf7pp-ppcp-align-right">
                    <a href="https://www.paypal.com/us/webapps/mpp/merchant-fees#statement-2" class="cf7pp-ppcp-link" target="_blank">View our simple and transparent pricing</a>
                </td>
            </tr>
			<?php if ( !empty( $_GET['error'] ) && in_array( $_GET['error'], ['security', 'api'] ) ) { ?>
                <tr>
                    <td colspan="2">
                        <ul class="cf7pp-ppcp-list cf7pp-ppcp-list-error">
                            <li>
								<?php
								if ( $_GET['error'] === 'security' ) {
									_e( 'The request has not been authenticated. Please reload the page and try again.' );
								} else {
									_e( 'The request ended with an error. Please reload the page and try again.' );
								}
								?>
                            </li>
                        </ul>
                    </td>
                </tr>
			<?php } ?>
        </table>
		<?php
	}

	if ( !wp_doing_ajax() ) { ?>
        <script>
            (function(d, s, id){
                var js, ref = d.getElementsByTagName(s)[0]; if (!d.getElementById(id)){
                    js = d.createElement(s); js.id = id; js.async = true;
                    js.src =
                        "https://www.paypal.com/webapps/merchantboarding/js/lib/lightbox/partner.js";
                    ref.parentNode.insertBefore(js, ref); }
            }(document, "script", "paypal-js"));
        </script>
	<?php }

	return ob_get_clean();
}