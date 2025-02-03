<?php
// phpcs:disable WordPress.DateTime.RestrictedFunctions.date_date
// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log
// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_print_r
namespace WML;

use WML\Classes\Admin_Notice;
use WML\Classes\Settings;

/**
 * Main Functionality File
 *
 * This class is to add functionality of plugin.
 *
*/
class WP_MAIL_LOG {

	/**
	 * Plugin Title
	 *
	 * @var $wm_title string
	*/
	private $wml_title = 'WP Mail Log';
	/**
	 * The instance
	 *
	 * @var $instance null|object
	*/
	public static $instance;

	public $helper;
	/**
	 * Current Tab
	 *
	 * @var $current_tab string
	*/
	private $current_tab = '';
	/**
	 * Return the instance of class
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
		$this->register_autoloader();

		// register_activation_hook( WML_BASE, [ $this, 'wml_activate' ] );
		// register_deactivation_hook( WML_BASE, [ $this, 'wml_deactivate' ] );
		// add_action( 'wpv/wml/delete_logs', [ $this, 'wml_delete_entry' ] );
		// Register Rest API
		add_action( 'rest_api_init', [ $this, 'init_rest_api' ] );

		// Add Adimn Menu Setting Page
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );

		// Add Setting Page Header
		add_action( 'in_admin_header', [ $this, 'in_admin_header' ] );

		// Create Table
		add_action( 'plugins_loaded', [ 'WML\Classes\DbTable', 'wml_plugin_activated' ] );

		// Caputure Mail Register Filter
		add_filter( 'wp_mail', [ 'WML\Classes\Capture_Mail', 'log_email' ] );

		// for admin scripts & styles
		add_action( 'admin_enqueue_scripts', [ $this, 'wml_admin_enqueue_scripts' ] );

		add_action( 'admin_notices', [ $this, 'add_review_notice' ] );

		add_filter( 'admin_footer_text', [ $this, 'admin_footer_text' ] );

		add_action( 'admin_init', [ $this, 'wpv_mail_review' ], 10 );

		// Add Script type module
		add_filter('script_loader_tag', [ $this, 'add_type_attribute' ] , 10, 3);
	}

	

	/**
	 * Add admin menu
	 *
	 * @since 0.3
	 * @return void
	 * @access public
	 *
	*/
	public function admin_menu() {
		add_menu_page(
			__( 'WP Mail Log', 'wpv-wml' ),
			__( 'WP Mail Log', 'wpv-wml' ),
			'manage_options',
			'wp-mail-log',
			[ $this, 'create_wp_mail_page' ],
			// 'dashicons-chart-line',
			'dashicons-email-alt',
			25
		);
		add_submenu_page( 'wp-mail-log', __( 'WP Mail Logs', 'wpv-wml' ), __( 'Emails Logs', 'wpv-wml' ), 'manage_options', 'wp-mail-log', [ $this, 'create_wp_mail_page' ], 1 );
		// add_submenu_page( 'wp-mail-log', 'WP Mail Log Settings', 'Settings', 'manage_options', 'wpv-wpml-settings', [ $this, 'wpml_settings' ], 5 );
	}
	/**
	 * Create Setting page
	 *
	 * @since 0.3
	 * @return void
	 * @access public
	 *
	*/
	public function wpml_settings() {
		if ( isset( $_GET['wml_nonce'] ) && ! wp_verify_nonce( $_GET['wml_nonce'], 'wp_rest' ) ) {
			die( 'Sorry, your nonce did not verify!' );
		}

		if ( isset( $_GET['tab'] ) ) {
			$this->current_tab = sanitize_text_field( wp_unslash( $_GET['tab'] ) );
		}

		$setting_pages = [
			'general' => __( 'General', 'wpv-wml' ),
		];
		?>

		<div class="wml-settings-wrapper">

			<div class="wml-data-wrapper">
				<div class="wml-settings-content-wrapper">
					<nav class="wml-nav-tab-wrapper">
						<?php
						foreach ( $setting_pages as $key => $label ) {
							?>
							<a class="wml-nav-tab <?php echo ( ( '' === $this->current_tab && 'general' === $key ) || $key === $this->current_tab ) ? 'wml-tab-active' : ''; ?>" href="admin.php?page=wpv-wpml-settings&tab=<?php echo esc_html( $key . add_query_arg( 'wml_nonce', wp_create_nonce( 'wp_rest' ) ) ); ?>"><?php echo esc_html( $label ); ?></a>
							<?php
						}
						?>
					</nav>

					<div class="wml-settings-tab-content-wrapper">

						<?php
						if ( '' === $this->current_tab || 'general' === $this->current_tab ) {
							?>
							<div id="wml-settings"></div>
							<?php
						}
						?>

					</div>

				</div>
			</div>

		</div>
		<?php
	}

	public function add_review_notice() {
		if ( is_admin() ) {
			$remind_later = get_transient( 'wml_remind_later' );

			$wml_review = get_option( 'wml_review' );

			if ( $wml_review === 'done' || $remind_later ) {
				return;
			}

			global $wpdb;
			$rowcount = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}wml_entries" );

			if ( $rowcount > 10 ) {
				$this->wml_review_box();
			}
		}
	}

	public function sidebar() {
		echo '<div>Sidebar Content</div>';
	}

	/**
	 * Create Header
	 *
	 * Create header for our plugin pages
	 *
	 * @since 0.3
	 * @return void
	 * @access public
	 *
	*/
	public function in_admin_header() {
		$nav_links      = $this->get_nav_links();
		$current_screen = get_current_screen();
		if ( ! isset( $nav_links[ $current_screen->id ] ) ) {
			return;
		}
		?>
		<div class="wml-topbar">
			<div class="wml-branding">
				<div class="wml-logo">
					<svg viewBox="0 0 831 775" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
						<!-- Generator: Sketch 57.1 (83088) - https://sketch.com -->
						<title>WP Mail Log</title>
						<desc>Email Log for WordPress</desc>
						<g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
							<g id="El-logo-blue" fill-rule="nonzero">
								<path d="M227.7,160.1 C227.7,155.8 229,151.8 231.3,148.5 C232.4,146.8 233.8,145.3 235.3,144.1 C238.9,141.1 243.5,139.3 248.5,139.3 L582.7,139.3 C587,139.3 591,140.6 594.3,142.9 C596.5,144.4 598.4,146.3 599.9,148.5 C602.1,151.8 603.5,155.8 603.5,160.1 L603.5,394.9 L830.8,257.3 L446.5,10.2 C427.4,-2.1 402.9,-2.1 383.8,10.2 L0.3,257.2 L227.6,394.8 L227.6,160.1 L227.7,160.1 Z" id="Path" fill="#154DAE"></path>
								<path d="M599.9,148.5 C598.4,146.3 596.5,144.4 594.3,142.9 C591,140.7 587,139.3 582.7,139.3 L248.5,139.3 C243.5,139.3 238.9,141.1 235.3,144.1 C233.8,145.4 232.4,146.9 231.3,148.5 C229.1,151.8 227.7,155.8 227.7,160.1 L227.7,394.8 L283.2,428.4 L329.1,456.2 L382.3,488.4 C402.8,500.8 428.4,500.8 448.8,488.4 L503.7,455.7 L549.5,427.5 L603.4,394.9 L603.4,160.1 C603.5,155.8 602.2,151.8 599.9,148.5 Z M368.5,182.9 L462.7,182.9 C469.5,182.9 475,188.4 475,195.2 C475,202 469.5,207.5 462.7,207.5 L368.5,207.5 C361.7,207.5 356.2,202 356.2,195.2 C356.2,188.4 361.7,182.9 368.5,182.9 Z M495.6,384.8 L335.6,384.8 C328.8,384.8 323.3,379.3 323.3,372.5 C323.3,365.7 328.8,360.2 335.6,360.2 L495.6,360.2 C502.4,360.2 507.9,365.7 507.9,372.5 C507.9,379.3 502.4,384.8 495.6,384.8 Z M342.1,313.4 C342.1,306.6 347.6,301.1 354.4,301.1 L476.6,301.1 C483.4,301.1 488.9,306.6 488.9,313.4 C488.9,320.2 483.4,325.7 476.6,325.7 L354.5,325.7 C347.7,325.7 342.1,320.2 342.1,313.4 Z M515.5,266.6 L315.7,266.6 C308.9,266.6 303.4,261.1 303.4,254.3 C303.4,247.5 308.9,242 315.7,242 L515.5,242 C522.3,242 527.8,247.5 527.8,254.3 C527.8,261.1 522.3,266.6 515.5,266.6 Z" id="Shape" fill="#F2F2F2"></path>
								<polygon id="Path" fill="#154DAE" points="101.1 694.3 329.1 456.2 283.2 428.4"></polygon>
								<polygon id="Path" fill="#154DAE" points="732.3 694.3 549.6 427.5 503.8 455.7"></polygon>
								<path d="M830.8,736.5 L830.8,257.2 L830.8,257.2 L603.5,394.8 L549.6,427.4 L732.4,694.2 L503.8,455.7 L448.9,488.4 C428.4,500.8 402.8,500.8 382.4,488.4 L329.2,456.2 L101.1,694.3 L283.2,428.4 L227.7,394.8 L0.4,257.2 L0.4,707.7 L0.4,736.5 C0.4,757.8 17.7,775 38.9,775 L792.3,775 C813.6,775 830.8,757.8 830.8,736.5 Z" id="Path" fill="#1B5ED7"></path>
								<path d="M368.5,207.6 L462.7,207.6 C469.5,207.6 475,202.1 475,195.3 C475,188.5 469.5,183 462.7,183 L368.5,183 C361.7,183 356.2,188.5 356.2,195.3 C356.2,202.1 361.7,207.6 368.5,207.6 Z" id="Path" fill="#154DAE"></path>
								<path d="M515.5,242 L315.7,242 C308.9,242 303.4,247.5 303.4,254.3 C303.4,261.1 308.9,266.6 315.7,266.6 L515.5,266.6 C522.3,266.6 527.8,261.1 527.8,254.3 C527.8,247.5 522.3,242 515.5,242 Z" id="Path" fill="#154DAE"></path>
								<path d="M476.7,325.7 C483.5,325.7 489,320.2 489,313.4 C489,306.6 483.5,301.1 476.7,301.1 L354.5,301.1 C347.7,301.1 342.2,306.6 342.2,313.4 C342.2,320.2 347.7,325.7 354.5,325.7 L476.7,325.7 Z" id="Path" fill="#154DAE"></path>
								<path d="M495.6,360.1 L335.6,360.1 C328.8,360.1 323.3,365.6 323.3,372.4 C323.3,379.2 328.8,384.7 335.6,384.7 L495.6,384.7 C502.4,384.7 507.9,379.2 507.9,372.4 C507.9,365.6 502.4,360.1 495.6,360.1 Z" id="Path" fill="#154DAE"></path>
							</g>
						</g>
					</svg>
				</div>

				<h2>
				<?php
				echo esc_html( $this->wml_title );
				?>
				</h2>
				<span class="wml-version"><?php echo 'v ' . esc_html( WML_VERSION ); ?></span>
			</div>
		</div>

		<?php
	}
	/**
	 * Navigation link of Our Plugin
	 *
	 * @since 0.3
	 * @return bool
	 * @access public
	 *
	*/
	public function get_nav_links() {
		$nav = [
			'toplevel_page_wp-mail-log'          => [
				'label'   => __( 'Email Logs', 'wpv-wml' ),
				'link'    => admin_url( 'admin.php?page=wp-mail-log' ),
				'top_nav' => true,
			],
			'wp-mail-log_page_wpv-wpml-settings' => [
				'label'   => __( 'Settings', 'wpv-wml' ),
				'link'    => admin_url( 'admin.php?page=wpv-wml-settings' ),
				'top_nav' => true,
			],
		];

		$nav = apply_filters( 'wml/nav_links', $nav );

		return $nav;
	}

	/**
	 * Create Mail Log Page
	 *
	 * Create page to view logs, render content using react js.
	 *
	 * @since 0.3
	 * @return void
	 * @access public
	 *
	 */

	public function create_wp_mail_page() {
		?>
		<div class="wml-content-section">
			<div id="wml-content"></div>
		</div>
		<?php
	}

	/**
	 * Add Admin Scripts
	 *
	 * Function fire on `admin_enqueue_scripts` hook
	 *
	 * @since 0.3
	 * @return void
	 * @access public
	 *
	 */

	public function wml_admin_enqueue_scripts() {
		$settings = Settings::instance();
		$settings = $settings->get();
		$screen   = get_current_screen();
		wp_enqueue_style( 'wml-admin-style', WML_URL . 'assets/css/admin.css', [], WML_VERSION );
		if ( $screen->id !== 'toplevel_page_wp-mail-log' ) {
			return;
		}
		wp_enqueue_style( 'log-style', WML_URL . 'build/css/index.css', [], WML_VERSION );
		wp_enqueue_script( 'log-js', WML_URL . 'build/js/index.js', [], WML_VERSION, true );
		
		wp_localize_script(
			'log-js',
			'WmlGlobalVar',
			[
				'site_url'     => site_url(),
				'ajax_url'     => admin_url( 'admin-ajax.php' ),
				'admin_url'    => admin_url(),
				'rest_url'     => get_rest_url(),
				'nonce'        => wp_create_nonce( 'wp_rest' ),
				'ajax_nonce'   => wp_create_nonce( 'wml_ajax_nonce' ),
				'locale'       => get_locale(),
				'date-formate' => get_option( 'date_format' ),
				'temp_date'    => date( get_option( 'date_format' ) ),
				'settings'     => $settings,
				'upload_dir'	=> wp_upload_dir()['baseurl'],
			]
		);
	}

	function add_type_attribute($tag, $handle, $src){
		// if not your script, do nothing and return original $tag
		if ( 'log-js' !== $handle ) {
			return $tag;
		}
		// change the script tag by adding type="module" and return it.
		$tag = '<script type="module" src="' . esc_url( $src ) . '"></script>';
		return $tag;
	}

	

	/**
	 * Add Api endopints
	 *
	 * Function fire on `rest_api_init` hook
	 *
	 * @since 0.3
	 * @return void
	 * @access public
	 *
	 */
	public function init_rest_api() {
		$controllers = [
			new \WML\Classes\Api(),
		];
		foreach ( $controllers as $controller ) {
			$controller->register_routes();
		}
	}
	/**
	 * Register Autoloader
	 *
	 * Register function as `__autoload()` implementation.
	 *
	 * @since 0.3
	 * @return void
	 * @access public
	 *
	 */
	private function register_autoloader() {
		spl_autoload_register( [ __CLASS__, 'autoload' ] );
	}
	/**
	 * Run Autoloader
	 *
	 * Autoloader will add classes
	 *
	 * @param string $class Class name.
	 * @since 0.3
	 * @return void
	 * @access public
	 *
	 */
	public function autoload( $class ) {

		if ( 0 !== strpos( $class, __NAMESPACE__ ) ) {
			return;
		}

		if ( ! class_exists( $class ) ) {

			$filename = strtolower(
				preg_replace(
					[ '/^' . __NAMESPACE__ . '\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/' ],
					[ '', '$1-$2', '-', DIRECTORY_SEPARATOR ],
					$class
				)
			);

			$filename = WML_PATH . $filename . '.php';

			if ( is_readable( $filename ) ) {
				include $filename;
			}
		}
	}
	/**
	 * Add Mail Review Notice
	 *
	 * @since 0.3
	 * @return void
	 * @access public
	 *
	 */
	public function wpv_mail_review() {
		if ( isset( $_GET['wml_remind_later'] ) || isset( $_GET['wml_review_done'] ) ) {
			if ( ! empty( $_GET['wml_nonce'] ) && wp_verify_nonce( $_GET['wml_nonce'], 'wml_rest' ) ) {

				$user  = \wp_get_current_user();
				$roles = (array) $user->roles;
				if ( $roles[0] !== 'administrator' ) {
					return;
				}

				if ( isset( $_GET['wml_remind_later'] ) ) {
					$this->wml_remind_later();
				} elseif (
					isset( $_GET['wml_review_done'] ) ) {
					$this->wml_review_done();
				}
			}
		}
	}
	/**
	 * Set Notice to Remind Later
	 *
	 * @since 0.3
	 * @return void
	 * @access public
	 *
	 */
	public function wml_remind_later() {
		set_transient( 'wml_remind_later', 'show again', WEEK_IN_SECONDS );
	}
	/**
	 * Set Review Notice to Done
	 *
	 * @since 0.3
	 * @return void
	 * @access public
	 *
	 */
	public function wml_review_done() {
		update_option( 'wml_review', 'done', false );
	}

	/**
	 * Review Notice HTML
	 *
	 * @since 0.3
	 * @return void
	 * @access public
	 *
	 */

	public function wml_review_box() {
		?>
		<div class="wml-review notice notice-success is-dismissible">
			<div class="wml-logo">
			<svg viewBox="0 0 831 775" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
				<g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
					<g id="El-logo-blue" fill-rule="nonzero">
						<path d="M227.7,160.1 C227.7,155.8 229,151.8 231.3,148.5 C232.4,146.8 233.8,145.3 235.3,144.1 C238.9,141.1 243.5,139.3 248.5,139.3 L582.7,139.3 C587,139.3 591,140.6 594.3,142.9 C596.5,144.4 598.4,146.3 599.9,148.5 C602.1,151.8 603.5,155.8 603.5,160.1 L603.5,394.9 L830.8,257.3 L446.5,10.2 C427.4,-2.1 402.9,-2.1 383.8,10.2 L0.3,257.2 L227.6,394.8 L227.6,160.1 L227.7,160.1 Z" id="Path" fill="#154DAE"></path>
						<path d="M599.9,148.5 C598.4,146.3 596.5,144.4 594.3,142.9 C591,140.7 587,139.3 582.7,139.3 L248.5,139.3 C243.5,139.3 238.9,141.1 235.3,144.1 C233.8,145.4 232.4,146.9 231.3,148.5 C229.1,151.8 227.7,155.8 227.7,160.1 L227.7,394.8 L283.2,428.4 L329.1,456.2 L382.3,488.4 C402.8,500.8 428.4,500.8 448.8,488.4 L503.7,455.7 L549.5,427.5 L603.4,394.9 L603.4,160.1 C603.5,155.8 602.2,151.8 599.9,148.5 Z M368.5,182.9 L462.7,182.9 C469.5,182.9 475,188.4 475,195.2 C475,202 469.5,207.5 462.7,207.5 L368.5,207.5 C361.7,207.5 356.2,202 356.2,195.2 C356.2,188.4 361.7,182.9 368.5,182.9 Z M495.6,384.8 L335.6,384.8 C328.8,384.8 323.3,379.3 323.3,372.5 C323.3,365.7 328.8,360.2 335.6,360.2 L495.6,360.2 C502.4,360.2 507.9,365.7 507.9,372.5 C507.9,379.3 502.4,384.8 495.6,384.8 Z M342.1,313.4 C342.1,306.6 347.6,301.1 354.4,301.1 L476.6,301.1 C483.4,301.1 488.9,306.6 488.9,313.4 C488.9,320.2 483.4,325.7 476.6,325.7 L354.5,325.7 C347.7,325.7 342.1,320.2 342.1,313.4 Z M515.5,266.6 L315.7,266.6 C308.9,266.6 303.4,261.1 303.4,254.3 C303.4,247.5 308.9,242 315.7,242 L515.5,242 C522.3,242 527.8,247.5 527.8,254.3 C527.8,261.1 522.3,266.6 515.5,266.6 Z" id="Shape" fill="#F2F2F2"></path>
						<polygon id="Path" fill="#154DAE" points="101.1 694.3 329.1 456.2 283.2 428.4"></polygon>
						<polygon id="Path" fill="#154DAE" points="732.3 694.3 549.6 427.5 503.8 455.7"></polygon>
						<path d="M830.8,736.5 L830.8,257.2 L830.8,257.2 L603.5,394.8 L549.6,427.4 L732.4,694.2 L503.8,455.7 L448.9,488.4 C428.4,500.8 402.8,500.8 382.4,488.4 L329.2,456.2 L101.1,694.3 L283.2,428.4 L227.7,394.8 L0.4,257.2 L0.4,707.7 L0.4,736.5 C0.4,757.8 17.7,775 38.9,775 L792.3,775 C813.6,775 830.8,757.8 830.8,736.5 Z" id="Path" fill="#1B5ED7"></path>
						<path d="M368.5,207.6 L462.7,207.6 C469.5,207.6 475,202.1 475,195.3 C475,188.5 469.5,183 462.7,183 L368.5,183 C361.7,183 356.2,188.5 356.2,195.3 C356.2,202.1 361.7,207.6 368.5,207.6 Z" id="Path" fill="#154DAE"></path>
						<path d="M515.5,242 L315.7,242 C308.9,242 303.4,247.5 303.4,254.3 C303.4,261.1 308.9,266.6 315.7,266.6 L515.5,266.6 C522.3,266.6 527.8,261.1 527.8,254.3 C527.8,247.5 522.3,242 515.5,242 Z" id="Path" fill="#154DAE"></path>
						<path d="M476.7,325.7 C483.5,325.7 489,320.2 489,313.4 C489,306.6 483.5,301.1 476.7,301.1 L354.5,301.1 C347.7,301.1 342.2,306.6 342.2,313.4 C342.2,320.2 347.7,325.7 354.5,325.7 L476.7,325.7 Z" id="Path" fill="#154DAE"></path>
						<path d="M495.6,360.1 L335.6,360.1 C328.8,360.1 323.3,365.6 323.3,372.4 C323.3,379.2 328.8,384.7 335.6,384.7 L495.6,384.7 C502.4,384.7 507.9,379.2 507.9,372.4 C507.9,365.6 502.4,360.1 495.6,360.1 Z" id="Path" fill="#154DAE"></path>
					</g>
				</g>
			</svg>
			</div>
			<div class="wml-review-content">
				<p class="wml-review-desc"><?php echo 'WP Mail Log has already captured 10+ mail log. That’s awesome! Could you please do a BIG favor and give it a 5-star rating on WordPress? <br/> Just to help us spread the word and boost our motivation. <br/><b>~ Anand Upadhyay</b>'; ?></p>
				<span class="wml-notic-link-wrapper">
					<a class="wml-notice-link" target="_blank" href="https://wordpress.org/support/plugin/wp-mail-log/reviews/#new-post" class="button button-primary"><span class="dashicons dashicons-heart"></span><?php esc_html_e( 'Ok, you deserve it!', 'wpv-wml' ); ?></a>
					<a class="wml-notice-link" href="
					<?php
					echo esc_html(
						add_query_arg(
							[
								'wml_remind_later' => 'later',
								'wml_nonce'        => wp_create_nonce( 'wml_rest' ),
							]
						)
					)
					?>
						"><span class="dashicons dashicons-schedule"></span><?php esc_html_e( 'May Be Later', 'wpv-wml' ); ?></a>
					<a class="wml-notice-link" href="
					<?php
					echo esc_html(
						add_query_arg(
							[
								'wml_review_done' => 'done',
								'wml_nonce'       => wp_create_nonce( 'wml_rest' ),
							]
						)
					);
					?>
														"><span class="dashicons dashicons-smiley"></span><?php esc_html_e( 'Already Done', 'wpv-wml' ); ?></a>
				</span>
			</div>
		</div>
		<?php
	}
	/**
	 * Fire on Plugin Activation Hook
	 *
	 * Write the function which will fire on plugin Activate.
	 *
	 * @since 0.3
	 * @return void
	 * @access public
	 *
	 */
	public function wml_activate() {
		// wp_schedule_event( time(), 'daily', 'wpv/wml/delete_logs' );
	}
	/**
	 * Delete Logs Automatically
	 *
	 * This function fire on wp_schedule_event.
	 *
	 * @since 0.3
	 * @return void
	 * @access public
	 *
	 */
	public function wml_delete_entry() {
		global $wpdb;

		$settings = get_option( 'wpv_wml_settings' );
		$isDelete = $settings['deleteLogs'];
		$days     = $settings['deleteDays'];

		if ( ! $isDelete ) {
			return;
		}

		$date = date( 'Y-m-d', strtotime( '-' . $days . ' days' ) );

		$table_name = $wpdb->prefix . 'wml_entries';

		$query = $wpdb->prepare( "DELETE FROM {$table_name} WHERE DATE_FORMAT(captured_gmt,GET_FORMAT(DATE,'JIS')) <= %s", $date );

		$dl1 = $wpdb->query( $query );

		if ( $dl1 === 0 ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
				error_log( print_r( 'No rows deleted...!!!', true ) );
			}
		} else {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
				error_log( print_r( 'Row Deleted...!!!', true ) );
			}
		}
	}

	/**
	 * Fire on Plugin Deactivation hook
	 *
	 * Write the function which will fire on plugin deactivate.
	 *
	 * @since 0.3
	 * @return void
	 * @access public
	 *
	 */

	public function wml_deactivate() {
		wp_clear_scheduled_hook( 'wpv/wml/delete_logs' );
	}


	/**
	 * Update Admin Footer Text
	 *
	 * @since 0.3
	 * @param string $footer_text Footer Text
	 * @return string updated $footer_text
	 * @access public
	 *
	 */

	public function admin_footer_text( $footer_text ) {
		$screen = get_current_screen();
		// Todo:: Show on plugin screens
		$wml_screens = [
			'toplevel_page_wp-mail-log',
			'wp-mail-log_page_wpv-wpml-settings',
		];

		if ( in_array( $screen->id, $wml_screens, true ) ) {
			$footer_text = sprintf(
				/* translators: 1: WP Mail Log, 2: Link to plugin review */
				__( 'Enjoyed %1$s? Please leave us a %2$s rating. We really appreciate your support!', 'wpv-wml' ),
				'<strong>' . __( 'WP Mail Log', 'wpv-wml' ) . '</strong>',
				'<a href="https://wordpress.org/support/plugin/wp-mail-log/reviews/#new-post" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
			);
		}

		return $footer_text;
	}

}

WP_MAIL_LOG::get_instance();
