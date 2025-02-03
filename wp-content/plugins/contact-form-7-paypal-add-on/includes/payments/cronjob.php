<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Add cronjob.
 * @since 1.8
 */
add_action('wp', 'cf7pp_payment_check_status_cronjob');
function cf7pp_payment_check_status_cronjob() {
    if ( !wp_next_scheduled('cf7pp_payment_check_status') ) {
        wp_schedule_event(time(), 'hourly', 'cf7pp_payment_check_status');
    }
}

/**
 * Add cronjob action.
 * @since 1.8
 */
add_action( 'cf7pp_payment_check_status', 'cf7pp_payment_check_status_func' );
function cf7pp_payment_check_status_func() {
	global $wpdb;

	$wpdb->query( 
		"UPDATE {$wpdb->posts}
		 SET post_status = 'cf7pp-abandoned'
		 WHERE post_type = 'cf7pp_payments'
		   AND post_status = 'cf7pp-pending'
		   AND post_date < DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 DAY)"
	);
}