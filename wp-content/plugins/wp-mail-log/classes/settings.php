<?php

namespace WML\Classes;

/**
 * Settings
 *
 * This class is to manage plugin settings.
 *
 */

class Settings {

	/**
	 * The instance
	 *
	 * @var $instance null|object
	 */
	private static $instance = null;

	/**
	 * Return the instance of class
	 *
	 * @since 0.3
	 * @return object $instance of class
	 * @access public
	 *
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
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
		add_action( 'wp_ajax_fv_save_config', [ $this, 'fv_save_config' ] );
	}
	/**
	 * Return saved settings in database
	 *
	 * @since 0.3
	 * @return array $settings
	 * @access public
	 *
	 */
	public function get() {
		// set default array
		$defaults = [
			'deleteLogs' => false,
			'deleteDays' => 7,
		];

		// fetch from db
		$settings = get_option( 'wpv_wml_settings' );

		// merge

		$settings = wp_parse_args( $settings, $defaults );

		// return
		return $settings;
	}
	/**
	 * Save settings to database
	 *
	 * @param array $data data to save in databse
	 * @return \WP_REST_Response Response object on success
	 * @since 0.3
	 * @access public
	 *
	 */
	public function wml_save_config( $data ) {
		// TODO :: return method response update
		$wpv_wml_settings               = [];
		$wpv_wml_settings['deleteDays'] = $data['deleteDays'];
		$wpv_wml_settings['deleteLogs'] = $data['deleteLogs'];

		$message = [];
		if ( current_user_can( 'manage_options' ) ) {
			$wpv_wml_settings   = update_option( 'wpv_wml_settings', $wpv_wml_settings, false );
			$message['status']  = 'success';
			$message['message'] = 'Settings Saved';
			return $message;
		}
	}
}
