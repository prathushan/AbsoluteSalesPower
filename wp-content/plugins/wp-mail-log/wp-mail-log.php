<?php

/**
 * Plugin Name: WP Mail Log
 * Description: WP Mail Log helps you to Log and view all emails from WordPress.
 * Plugin URI: https://wpvibes.com/
 * Author: WPVibes
 * Version: 1.1.3
 * Author URI: https://wpvibes.com/
 * License:      GNU General Public License v2 or later
 * License URI:  http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wml-wts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


define( 'WML_URL', plugins_url( '/', __FILE__ ) );
define( 'WML_PATH', plugin_dir_path( __FILE__ ) );
define( 'WML_BASE', plugin_basename( __FILE__ ) );
define( 'WML_FILE', __FILE__ );
define( 'WML_VERSION', '1.1.3' );


require WML_PATH . 'includes/bootstrap.php';
