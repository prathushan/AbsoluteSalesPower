<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly


/**
 * Allowed HTML tags for admin notices.
 * Adjust this array if you need to allow more tags or attributes.
 */
function cf7pp_allowed_html() {
    return array(
        'a' => array(
            'href' => array(),
            'target' => array(),
            'class' => array(),
            'style' => array(),
        ),
        'b' => array(),
        'br' => array(),
        'strong' => array(),
        'u' => array(),
        'span' => array(
            'class' => array(),
            'style' => array(),
        ),
        'img' => array(
            'src' => array(),
            'alt' => array(),
            'class' => array(),
            'style' => array(),
        ),
        // Add more tags and attributes as needed
    );
}


/**
 * Show admin notice.
 * @since 1.8
 * @return void
 */
add_action('admin_notices', 'cf7pp_admin_earnings_notice');
function cf7pp_admin_earnings_notice(){

    $cf7pp_show_earnings_notice = get_option('cf7pp_show_earnings_notice');
    if ( $cf7pp_show_earnings_notice == '-1' || (time() < $cf7pp_show_earnings_notice + DAY_IN_SECONDS * 14) ) return;

    $msg = __('We noticed you\'ve been using Contact Form 7 - PayPal & Stripe for a while!', 'contact-form-7');

    if (empty($msg)) return;

    // Desired redirect URL
    $redirect_url = 'https://wordpress.org/support/plugin/contact-form-7-paypal-add-on/reviews/?filter=5#new-post';

    // Current page URL to append query arguments
    $current_url = remove_query_arg( array( 'cf7pp_show_earnings_notice', 'page' ) );

    // URLs for each button action
    $dismiss_and_redirect_url = esc_url( add_query_arg( 'cf7pp_show_earnings_notice', 'dismiss_and_redirect', $current_url ) );
    $later_url = esc_url( add_query_arg( 'cf7pp_show_earnings_notice', 'later', $current_url ) );
    $never_url = esc_url( add_query_arg( 'cf7pp_show_earnings_notice', 'never', $current_url ) );

    printf(
        '<div class="notice notice-success">
            <p><strong>%s</strong> %s</p>
            <p>%s</p>
            <p>%s</p>
            <p>
                <a class="button button-primary" href="#" onclick="window.open(\'%s\', \'_blank\'); window.location.href=\'%s\'; return false;">%s</a>
                <a class="button button-secondary" href="%s">%s</a>
                <a class="button button-secondary" href="%s">%s</a>
            </p>
        </div>',
        esc_html__('Hello -', 'contact-form-7'),
        esc_html($msg),
        wp_kses(
            sprintf(
                /* translators: %s: URL to support page */
                __('If you like this plugin, please consider leaving a positive review to spread the word. Or if you have any issues or questions, please leave a support message <a href="%s" target="_blank">here</a>.', 'contact-form-7'),
                esc_url('https://wordpress.org/support/plugin/contact-form-7-paypal-add-on/')
            ),
            array(
                'a' => array(
                    'href'   => array(),
                    'target' => array(),
                ),
            )
        ),
        esc_html__('- Thank you so much!', 'contact-form-7'),
        esc_url($redirect_url),
        esc_url($dismiss_and_redirect_url),
        esc_html__('Okay, you deserve it', 'contact-form-7'),
        esc_url($later_url),
        esc_html__('Maybe later', 'contact-form-7'),
        esc_url($never_url),
        esc_html__('Never', 'contact-form-7')
    );
}


/**
 * Handle "Maybe later", "Never", and "dismiss_and_redirect" actions.
 * @since 1.8
 */
add_action('init', 'cf7pp_show_earnings_notice');
function cf7pp_show_earnings_notice() {
    if ( !isset($_GET['cf7pp_show_earnings_notice']) ) return;

    $notice = sanitize_text_field($_GET['cf7pp_show_earnings_notice']);

    switch ($notice) {
        case 'later':
            update_option('cf7pp_show_earnings_notice', time());
            break;

        case 'never':
            update_option('cf7pp_show_earnings_notice', '-1');
            break;

        case 'dismiss_and_redirect':
            // Dismiss the notice
            update_option('cf7pp_show_earnings_notice', '-1');
            // Redirect to the desired URL
            wp_safe_redirect('https://wordpress.org/support/plugin/contact-form-7-paypal-add-on/reviews/?filter=5#new-post');
            exit();
    }

    wp_safe_redirect(remove_query_arg('cf7pp_show_earnings_notice'));
    exit();
}


/**
 * Show admin notice for Stripe Connect.
 * @since 1.8
 */
add_action('admin_notices', 'cf7pp_admin_stripe_connect_notice');
function cf7pp_admin_stripe_connect_notice() {
    $options = cf7pp_free_options();
    
    if (isset($options['mode_stripe'])) {
        $mode = $options['mode_stripe'] == "2" ? 'live' : 'sandbox';
    } else {
        $mode = 'sandbox';
    }
    
    $acct_id_key = $mode == 'live' ? 'acct_id_live' : 'acct_id_test';

    if (!empty($options[$acct_id_key]) || !empty($options['stripe_connect_notice_dismissed']) ||
        ( isset( $_GET['page'] ) && sanitize_text_field($_GET['page']) == 'cf7pp_admin_table' && isset( $_GET['tab'] ) && sanitize_text_field($_GET['tab']) == 5 ) ) return;

    $dismiss_url = esc_url(add_query_arg('cf7pp_admin_stripe_connect_notice_dismiss', 1, admin_url()));

    printf(
        '<div class="notice notice-error is-dismissible cf7pp-stripe-connect-notice" data-dismiss-url="%s">
            <p>%s</p>
            <p><a href="%s" class="stripe-connect-btn"><span>Connect with Stripe</span></a></p>
            <br />%s
        </div>',
        $dismiss_url,
        wp_kses(
            __('<b>Important</b> - \'Contact Form 7 - PayPal & Stripe Add-on\' now uses Stripe Connect.
            Stripe Connect improves security and allows for easier setup. <br /><br />If you use Stripe, please use Stripe Connect.
            Have questions: see the <a target="_blank" href="https://wpplugin.org/documentation/stripe-connect/">documentation</a>.', 'contact-form-7'),
            cf7pp_allowed_html()
        ),
        esc_url(cf7pp_stripe_connect_url()),
        esc_html__('WPPlugin LLC is an official Stripe Partner. Pay as you go pricing: 2% per-transaction fee + Stripe fees.', 'contact-form-7')
    );
}

/**
 * Dismiss admin notice for Stripe Connect.
 * @since 1.8
 */
add_action('admin_init', 'cf7pp_admin_stripe_connect_notice_dismiss');
function cf7pp_admin_stripe_connect_notice_dismiss() {
    if ( isset($_GET['cf7pp_admin_stripe_connect_notice_dismiss']) ) {
        $dismiss_option = intval($_GET['cf7pp_admin_stripe_connect_notice_dismiss']);
        if ($dismiss_option === 1) {
            $options = cf7pp_free_options();
            $options['stripe_connect_notice_dismissed'] = 1;
            cf7pp_free_options_update( $options );
            wp_safe_redirect(remove_query_arg('cf7pp_admin_stripe_connect_notice_dismiss'));
            exit;
        }
    }
}

function cf7pp_paypal_commerce_onboarding_url() {
    $options = cf7pp_free_options();
    $mode = intval( $options['mode'] );
    $query_args = [
        'action' => 'cf7pp-ppcp-onboarding-start',
        'nonce' => wp_create_nonce( 'cf7pp-ppcp-onboarding-start' )
    ];
    if ( $mode === 1 ) {
        $query_args['sandbox'] = 1;
    }
    
    return '
    <a
        id="cf7pp-ppcp-onboarding-start-btn"
        class="cf7pp-ppcp-button cf7pp-ppcp-onboarding-start"
        style="background-color: #fff; border: 1px solid #162c70; color:#162c70;"
        data-paypal-button="true"
        href="'. esc_url(add_query_arg( $query_args, admin_url( 'admin-ajax.php' )) ) .'"
        target="PPFrame"
    > <img class="cf7pp-ppcp-paypal-logo" style="max-height:25px" src="'. esc_url(CF7PP_FREE_URL.'imgs/paypal-logo.png') .'" alt="paypal-logo" /><br />'. esc_html__('Get Started', 'contact-form-7') .'</a>';
}

/**
 * Stripe Connect error notice.
 * @since 1.8
 */
add_action('admin_notices', 'cf7pp_admin_stripe_connect_error_notice');
function cf7pp_admin_stripe_connect_error_notice() {
    if (empty($_GET['cf7pp_error']) || sanitize_text_field($_GET['cf7pp_error']) != 'stripe-connect-handler') return;

    printf(
        '<div class="notice notice-error is-dismissible">
            <p>%s</p>
        </div>',
        esc_html__('An error occurred while interacting with our Stripe Connect interface.', 'contact-form-7')
    );
}

/**
 * Show admin notice for PayPal Commerce Platform.
 * @since 2.0
 */
add_action( 'admin_notices', 'cf7pp_ppcp_admin_notice' );
function cf7pp_ppcp_admin_notice() {
    $options = cf7pp_free_options();
    $env = intval( $options['mode'] ) === 2 ? 'live' : 'sandbox';
    $connected = !empty( $options['ppcp_onboarding'][$env] ) && !empty( $options['ppcp_onboarding'][$env]['seller_id'] );
    if ( $connected || !empty( $options['ppcp_notice_dismissed'] ) ||
        ( isset( $_GET['page'] ) && sanitize_text_field($_GET['page']) == 'cf7pp_admin_table' && isset( $_GET['tab'] ) && sanitize_text_field($_GET['tab']) == 4 ) ) return;

    printf(
        '<div class="notice notice-error is-dismissible cf7pp-ppcp-connect-notice" data-dismiss-url="%s">
            <p>%s</p>
            %s
            <br />%s
        </div>',
        esc_url(add_query_arg( 'cf7pp_admin_ppcp_notice_dismiss', 1, admin_url() )),
        wp_kses(
            __('<b>Important</b> - \'Contact Form 7 - PayPal & Stripe Add-on\' now uses the PayPal Commerce Platform.
            <u><b>PayPal Standard is now a Legacy product.</b></u> <br /><br /> <b><u>If you use PayPal, please update to PayPal Commerce Platform.</u></b>', 'contact-form-7'),
            cf7pp_allowed_html()
        ),
        cf7pp_paypal_commerce_onboarding_url(),
        esc_html__('WPPlugin LLC is an official PayPal Partner. Pay as you go pricing: 2% per-transaction fee + PayPal fees.', 'contact-form-7')
    );
}

/**
 * Dismiss admin notice for PayPal Commerce Platform.
 * @since 2.0
 */
add_action( 'admin_init', 'cf7pp_free_ppcp_admin_notice_dismiss' );
function cf7pp_free_ppcp_admin_notice_dismiss() {
    if ( isset( $_GET['cf7pp_admin_ppcp_notice_dismiss'] ) ) {
        $options = cf7pp_free_options();
        $options['ppcp_notice_dismissed'] = 1;
        cf7pp_free_options_update( $options );
        wp_safe_redirect(remove_query_arg('cf7pp_admin_ppcp_notice_dismiss'));
        exit();
    }
}
