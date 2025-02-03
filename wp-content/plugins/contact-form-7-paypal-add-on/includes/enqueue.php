<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



// admin enqueue
function cf7pp_admin_enqueue() {

	// admin css
	wp_register_style('cf7pp-admin-css',plugins_url('/assets/css/admin.css',__DIR__),array(),CF7PP_VERSION_NUM);
	wp_enqueue_style('cf7pp-admin-css');

	// admin js
	wp_enqueue_script('cf7pp-admin',plugins_url('/assets/js/admin.js',__DIR__),array('jquery'),CF7PP_VERSION_NUM);
	wp_localize_script( 'cf7pp-admin', 'cf7pp', [
		'ajaxUrl' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce( 'cf7pp-free-request' )
	] );
}
add_action('admin_enqueue_scripts','cf7pp_admin_enqueue');












$options = cf7pp_free_options();
$request_method = isset($options['request_method']) ? $options['request_method'] : 1; // Default to 1 if not set

// Register REST API endpoints only if the request method is set to 2
if ($request_method == 2) {
    add_action('rest_api_init', function () {
        // Register endpoint for form post
        register_rest_route('cf7pp/v1', '/cf7pp_get_form_post', array(
            'methods' => 'GET',
            'callback' => 'cf7pp_get_form_post_callback',
            'permission_callback' => '__return_true', // Adjust as needed
        ));

        // Register endpoint for stripe success message
        register_rest_route('cf7pp/v1', '/cf7pp_get_form_stripe_success', array(
            'methods' => 'GET',
            'callback' => 'cf7pp_get_form_stripe_success_callback',
            'permission_callback' => '__return_true', // Adjust as needed
        ));
    });
}

// Public enqueue
function cf7pp_public_enqueue() {
    $site_url = get_home_url();
    $path_paypal = $site_url . '/?cf7pp_paypal_redirect=';
    $path_stripe = $site_url . '/?cf7pp_stripe_redirect=';

    $options = cf7pp_free_options();
    $request_method = isset($options['request_method']) ? $options['request_method'] : 1; // Default to 1 if not set

    // Set AJAX URL based on request method
    $ajax_url = ($request_method == 2) ? site_url('/wp-json/cf7pp/v1/') : admin_url('admin-ajax.php');

    // Determine which JS file to enqueue
    $js_file = ($request_method == 2) ? '/assets/js/redirect_method_rest_api.js' : '/assets/js/redirect_method.js';

    // Enqueue the appropriate JavaScript file
    wp_enqueue_script('cf7pp-redirect_method', plugins_url($js_file, __DIR__), array('jquery'), CF7PP_VERSION_NUM);
    wp_localize_script('cf7pp-redirect_method', 'ajax_object_cf7pp', array(
        'ajax_url' => $ajax_url,
        'forms' => cf7pp_forms_enabled(),
        'path_paypal' => $path_paypal,
        'path_stripe' => $path_stripe,
        'method' => $options['redirect'],
    ));
}
add_action('wp_enqueue_scripts', 'cf7pp_public_enqueue', 10);
