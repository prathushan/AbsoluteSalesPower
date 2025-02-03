<?php

/**
 *
 * @package   Duplicator
 * @copyright (c) 2023, Snap Creek LLC
 */

namespace Duplicator\Controllers;

use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Views\TplMng;

/**
 * Welcome screen controller
 */
class WelcomeController
{
    /**
     * Hidden welcome page slug.
     *
     * @var string
     */
    const SLUG = 'duplicator-getting-started';

    /**
     * Option determining redirect
     *
     * @var string
     */
    const REDIRECT_OPT_KEY = 'duplicator_redirect_to_welcome';

    /**
     * Init.
     *
     * @return void
     */
    public static function init()
    {
        // If user is in admin ajax or doing cron, return.
        if (function_exists('wp_doing_ajax') &&  wp_doing_ajax()) {
            return;
        }
        if (function_exists('wp_doing_cron') &&  wp_doing_cron()) {
            return;
        }

        add_action('admin_menu', array(__CLASS__, 'register'));
        add_action('admin_head', array(__CLASS__, 'hideMenu'));
        add_action('admin_init', array(__CLASS__, 'redirect'), 9999);
    }

    /**
     * Register the pages to be used for the Welcome screen (and tabs).
     *
     * These pages will be removed from the Dashboard menu, so they will
     * not actually show. Sneaky, sneaky.
     *
     * @return void
     */
    public static function register()
    {
        // Getting started - shows after installation.
        $hook_suffix = add_dashboard_page(
            esc_html__('Welcome to Duplicator', 'duplicator'),
            esc_html__('Welcome to Duplicator', 'duplicator'),
            'export',
            self::SLUG,
            array(__CLASS__, 'render')
        );
        add_action('admin_print_styles-' . $hook_suffix, array(__CLASS__, 'enqueues'));
    }

    /**
     * Removed the dashboard pages from the admin menu.
     *
     * This means the pages are still available to us, but hidden.
     *
     * @return void
     */
    public static function hideMenu()
    {
        remove_submenu_page('index.php', self::SLUG);
    }

    /**
     * Welcome screen redirect.
     *
     * This function checks if a new install or update has just occurred. If so,
     * then we redirect the user to the appropriate page.
     *
     * @return void
     */
    public static function redirect()
    {
        if (!get_option(self::REDIRECT_OPT_KEY, false)) {
            return;
        }

        /**
         * Filter to disable the onboarding redirect.
         *
         * @since 1.5.11.1
         *
         * @param bool $disable True to disable the onboarding redirect.
         */
        if (apply_filters('duplicator_disable_onboarding_redirect', false)) {
            delete_option(self::REDIRECT_OPT_KEY);
            return;
        }

        delete_option(self::REDIRECT_OPT_KEY);

        wp_safe_redirect(admin_url('index.php?page=' . WelcomeController::SLUG));
        exit;
    }

    /**
     * Enqueue assets.
     *
     * @return void
     */
    public static function enqueues()
    {
        wp_enqueue_style(
            'dup-welcome',
            DUPLICATOR_PLUGIN_URL . "assets/css/welcome.css",
            array(),
            DUPLICATOR_VERSION
        );
        wp_enqueue_script(
            'duplicator-onboarding',
            DUPLICATOR_PLUGIN_URL . "assets/js/onboarding.js",
            array('jquery'),
            DUPLICATOR_VERSION,
            true
        );
        wp_localize_script(
            'duplicator-onboarding',
            'duplicator_onboarding',
            array(
                'ajax_url'     => admin_url('admin-ajax.php'),
                'nonce'        => wp_create_nonce("duplicator_enable_usage_stats"),
                'email'        => wp_get_current_user()->user_email,
                'redirect_url' => ControllersManager::getMenuLink(ControllersManager::PACKAGES_SUBMENU_SLUG)
            )
        );
        wp_enqueue_style('dup-font-awesome');
        wp_enqueue_script('dup-jquery-qtip');
        wp_enqueue_style('dup-plugin-style');
        wp_enqueue_style('dup-jquery-qtip');
    }

    /**
     * Render welcome screen
     *
     * @return void
     */
    public static function render()
    {
        TplMng::getInstance()->render('admin_pages/welcome/welcome', array(), true);
    }
}
