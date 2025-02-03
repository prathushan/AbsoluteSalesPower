<?php

namespace WML\Classes;

/**
 * Admin Notices
 *
 * This class is to manage plugin admin notices.
 *
 */

class Admin_Notice {

	/**
	 * The instance
	 *
	 * @var $instance null|object
	 */
	public static $instance;

	public $module_manager;

	/**
	 * To get the instance @var $instance
	 *
	 * @since 0.3
	 * @return object $instance of class
	 * @access public
	 *
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * The constructor of class. Automatically call when class object create
	 *
	 * @since 0.3
	 * @return void
	 * @access public
	 *
	 */
	public function __construct() {
		add_filter( 'wpv-mail/admin_notices', [ $this, 'review_notice' ] );
	}

	/**
	 * This function is called by Add Filter `wpv-mail/admin_notices`
	 *
	 * This function add review notice if 10 log entries entered to database
	 *
	 * @since 0.3
	 * @param array $notice array of notice with add filter
	 * @return array updated array of notice
	 * @access public
	 *
	 */
	public function review_notice( $notice ) {

		$download_later = get_transient( 'wml_remind_later' );

		$wml_review = get_option( 'wml_review' );

		if ( $wml_review === 'done' || $download_later ) {
			return $notice;
		}

		global $wpdb;
		$rowcount = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}wml_entries" );

		if ( $rowcount > 10 ) {
			$notice[] = 'wpv_mail_review';
		}
		return $notice;
	}
}
