<?php

/*
Plugin Name: Contact Form 7 - PayPal & Stripe Add-on
Plugin URI: https://wpplugin.org/downloads/contact-form-7-paypal-add-on/
Description: Integrates PayPal & Stripe with Contact Form 7
Author: Scott Paterson
Author URI: https://wpplugin.org/downloads/contact-form-7-paypal-add-on/
License: GPL2
Version: 2.3.2
Requires Plugins: contact-form-7
*/

/*  Copyright 2014-2024 WPPlugin LLC / Scott Paterson

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/



// plugin variable: cf7pp


// empty function used by pro version to check if free version is installed
function cf7pp_free() {
}

// check if pro version is attempting to be activated - if so, then deactive this plugin
if (function_exists('cf7pp_pro')) {

	deactivate_plugins('contact-form-7-paypal-add-on-pro/paypal.php');

} else {
	define('CF7PP_VERSION_NUM', 	'2.3.2');

	define( 'CF7PP_STRIPE_CONNECT_ENDPOINT', 'https://wpplugin.org/stripe/connect.php' );
	define( 'CF7PP_FREE_PPCP_API', 'https://wpplugin.org/ppcp-cf7pp/' );

	define( 'CF7PP_FREE_URL', plugin_dir_url( __FILE__ ) );

	/**
	 * Get plugin options
	 */
	function cf7pp_free_options() {
		$default = [
			'currency' => '25',
			'language' => '3',
			'mode' => '2',
			'cancel' => '',
			'return' => '',
			'redirect' => '1',
			'request_method' => '1',
			'session' => '1',
			'success' => 'Payment Successful',
			'failed' => 'Payment Failed',
			'stripe_return' => '',
			'mode_stripe' => '2',
			'acct_id_test' => '',
			'acct_id_live' => '',
			'ppcp_onboarding' => [
				'live' => [],
				'sandbox' => []
			],
			'ppcp_notice_dismissed' => 0,
			'stripe_connect_notice_dismissed' => 0
		];
		$options = (array) get_option( 'cf7pp_options' );

		return array_merge( $default, $options );
	}

	/**
	 * Update plugin options
	 */
	function cf7pp_free_options_update( $options ) {
		update_option( 'cf7pp_options', $options );
	}

	//  plugin functions
	register_activation_hook( 	__FILE__, "cf7pp_activate" );
	register_deactivation_hook( __FILE__, "cf7pp_deactivate" );
	register_uninstall_hook( 	__FILE__, "cf7pp_uninstall" );

	function cf7pp_activate() {
		// default options
        $options = cf7pp_free_options();

		$options['ppcp_notice_dismissed'] = 0;
		$options['stripe_connect_notice_dismissed'] = 0;

		cf7pp_free_options_update( $options );
	}

	function cf7pp_deactivate() {
		delete_option("cf7pp_my_plugin_notice_shown");
	}

	function cf7pp_uninstall() {
	}
	
	function cf7pp_enable_cf7_ajax() {
		if ( function_exists( 'wpcf7' ) ) {  // Check if Contact Form 7 is active
			add_filter( 'wpcf7_load_js', '__return_true' ); // This is required to be enabled for redirection to work
		}
	}
	add_action( 'init', 'cf7pp_enable_cf7_ajax' );


	// check to make sure contact form 7 is installed and active
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {		
		
		// public includes
		include_once('includes/functions.php');
		include_once('includes/redirect_methods.php');
		include_once('includes/redirect_paypal.php');
		include_once('includes/redirect_stripe.php');
		include_once('includes/enqueue.php');

		if (!class_exists('Stripe\Stripe')) {
			include_once('includes/stripe_library/init.php');
		}

		// admin includes
		if (is_admin()) {
			include_once('includes/admin/tabs_page.php');
			include_once('includes/admin/settings_page.php');
			include_once('includes/admin/menu_links.php');
			include_once('includes/admin/extensions.php');
			include_once('includes/admin/notices.php');
		}

		// include payments functionality
		include_once('includes/payments/payments.inc.php');

		// include stripe-connect
		include_once('includes/stripe-connect.php');

		// Include PayPal Commerce Platform
		include_once( 'includes/ppcp.php' );

		// Include PayPal Commerce Platform - Frontend
		include_once( 'includes/ppcp_frontend.php' );

		// start session if not already started and session support is enabled
		$options = cf7pp_free_options();
		
		if (empty($options['session'])) {
			$session = '1';
		} else {
			$session = $options['session'];
		}
		
		if ($session == '2') {
			function cf7pp_session() {
				if(!session_id()) {
					session_start();
					session_write_close();
				}
			}
			add_action('init', 'cf7pp_session', 1);
			
		}
		
		
		
		
	} else {
		
		// give warning if contact form 7 is not active
		function cf7pp_my_admin_notice() {
			?>
			<div class="error">
				<p><?php _e( '<b>Contact Form 7 - PayPal & Stripe Add-on:</b> Contact Form 7 is not installed and / or active! Please install <a target="_blank" href="https://wordpress.org/plugins/contact-form-7/">Contact Form 7</a>.', 'cf7pp' ); ?></p>
			</div>
			<?php
		}
		add_action( 'admin_notices', 'cf7pp_my_admin_notice' );
		
	}
}


?>