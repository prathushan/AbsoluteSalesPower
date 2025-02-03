<?php

namespace Duplicator\Utils\ExtraPlugins;

use DUP_Log;
use Duplicator\Libs\Snap\SnapString;
use Duplicator\Utils\ExpireOptions;

class ExtraItem
{
    const STATUS_NOT_INSTALLED = 0;
    const STATUS_INSTALLED     = 1;
    const STATUS_ACTIVE        = 2;
    const URL_TYPE_GENERIC     = 0;
    const URL_TYPE_ZIP         = 1;
    const PLUGIN_API_FIELDS    = [
        'active_installs'          => false,
        'added'                    => false,
        'author'                   => false,
        'author_block_count'       => false,
        'author_block_rating'      => false,
        'author_profile'           => false,
        'banners'                  => false,
        'compatibility'            => false,
        'contributors'             => false,
        'description'              => false,
        'donate_link'              => false,
        'download_link'            => false,
        'downloaded'               => false,
        'group'                    => false,
        'homepage'                 => false,
        'icons'                    => false,
        'last_updated'             => false,
        'name'                     => false,
        'num_ratings'              => false,
        'rating'                   => false,
        'ratings'                  => false,
        'requires'                 => true,
        'requires_php'             => true,
        'reviews'                  => false,
        'screenshots'              => false,
        'sections'                 => false,
        'short_description'        => false,
        'slug'                     => false,
        'support_threads'          => false,
        'support_threads_resolved' => false,
        'tags'                     => false,
        'tested'                   => false,
        'version'                  => false,
        'versions'                 => false,
    ];

    /**
     * plugin name
     *
     * @var string
     */
    public $name = '';

    /**
     * plugin slug
     *
     * @var string
     */
    protected $slug = '';

    /**
     * url to plugin icon
     *
     * @var string
     */
    public $icon = '';

    /**
     * plugin description
     *
     * @var string
     */
    public $desc = '';

    /**
     * plugin url either to zip file or pro version
     *
     * @var string
     */
    public $url = '';

    /**
     * plugin url on wordpress.org if available
     *
     * @var bool|string
     */
    public $wpOrgURL = '';

    /**
     * PRO version of plugin if available
     *
     * @var ExtraItem|null
     */
    protected $pro = null;

    /**
     * Class constructor
     *
     * @param string      $name     plugin name
     * @param string      $slug     plugin slug
     * @param string      $icon     url to plugin icon
     * @param string      $desc     plugin description
     * @param string      $url      plugin url
     * @param string|bool $wpOrgURL plugin url on wordpress.org
     */
    public function __construct($name, $slug, $icon, $desc, $url, $wpOrgURL = false)
    {
        $this->name     = $name;
        $this->slug     = $slug;
        $this->icon     = $icon;
        $this->desc     = $desc;
        $this->url      = $url;
        $this->wpOrgURL = $wpOrgURL;
    }

    /**
     * Returns plugin slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Is plugin active
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->isInstalled() && is_plugin_active($this->slug);
    }

    /**
     * Is plugin installed
     *
     * @return bool
     */
    public function isInstalled()
    {
        static $installedSlugs = null;
        if ($installedSlugs === null) {
            if (!function_exists('get_plugins')) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }
            $installedSlugs = array_keys(get_plugins());
        }
        return in_array($this->slug, $installedSlugs);
    }

    /**
     * Checks of the WP and PHP version requirments pass
     *
     * @return bool True if checks pass, false otherwise
     */
    public function checkRequirments()
    {
        global $wp_version;

        if (($reqs = $this->getRequirments()) === false) {
            return false;
        }

        return version_compare($wp_version, $reqs['wp_version'], '>=') &&
            version_compare(PHP_VERSION, $reqs['php_version'], '>=');
    }

    /**
     * Gets the requirments either from the cache (wp_options) or from the remote
     * API and updates the cache.
     *
     * @return array{last_updated:int,wp_version:string,php_version:string}|false The data or false of failure
     */
    private function getRequirments()
    {
        if (($data = ExpireOptions::get($this->getApiSlug())) === false) {
            if (($data = $this->getRemoteRequirments()) !== false) {
                ExpireOptions::set($this->getApiSlug(), $data, 2 * WEEK_IN_SECONDS);
            } else {
                return false;
            }
        }

        return $data;
    }

    /**
     * Returns the slug that should be used with the API calls
     *
     * @return string
     */
    private function getApiSlug()
    {
        return dirname($this->getSlug());
    }

    /**
     * Retrieves the PHP and WP version requirments of a plugin from the WP API.
     *
     * @return array{wp_version:string,php_version:string}|false The data or false on failure
     */
    private function getRemoteRequirments()
    {
        if (!function_exists('plugins_api')) {
            require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        }

        $response = plugins_api('plugin_information', [
            'slug'   => $this->getApiSlug(),
            'fields' => self::PLUGIN_API_FIELDS
        ]);

        if (is_wp_error($response)) {
            return false;
        }

        return [
            'wp_version'  => $response->requires,
            'php_version' => $response->requires_php
        ];
    }

    /**
     * Returns pro version of plugin if available
     *
     * @return ExtraItem|null
     */
    public function getPro()
    {
        return $this->pro;
    }

    /**
     * Set pro plugin
     *
     * @param string      $name     plugin name
     * @param string      $slug     plugin slug
     * @param string      $icon     url to plugin icon
     * @param string      $desc     plugin description
     * @param string      $url      plugin url
     * @param string|bool $wpOrgURL plugin url on wordpress.org
     *
     * @return void
     */
    public function setPro($name, $slug, $icon, $desc, $url, $wpOrgURL = false)
    {
        $this->pro = new self($name, $slug, $icon, $desc, $url, $wpOrgURL);
    }

    /**
     * Whether to skip lite version of plugin because it is installed and pro version is available
     *
     * @return bool
     */
    public function skipLite()
    {
        return $this->pro !== null && $this->isActive();
    }

    /**
     * Enum of status constants (STATUS_ACTIVE, STATUS_INSTALLED, STATUS_UNINSALED)
     *
     * @return int return status constant
     */
    public function getStatus()
    {
        if ($this->isActive()) {
            return self::STATUS_ACTIVE;
        } elseif ($this->isInstalled()) {
            return self::STATUS_INSTALLED;
        } else {
            return self::STATUS_NOT_INSTALLED;
        }
    }

    /**
     * Status text
     *
     * @return string
     */
    public function getStatusText()
    {
        switch ($this->getStatus()) {
            case self::STATUS_ACTIVE:
                return __('Active', 'duplicator');
            case self::STATUS_INSTALLED:
                return __('Inactive', 'duplicator');
            case self::STATUS_NOT_INSTALLED:
                return __('Not Installed', 'duplicator');
        }
    }

    /**
     * Enum of URL constants (URL_TYPE_GENERIC, URL_TYPE_ZIP)
     *
     * @return int
     */
    public function getURLType()
    {
        if (SnapString::endsWith($this->url, '.zip')) {
            return self::URL_TYPE_ZIP;
        } else {
            return self::URL_TYPE_GENERIC;
        }
    }

    /**
     * Install this plugin
     *
     * @return bool true on success
     */
    public function install()
    {
        if ($this->isInstalled()) {
            return true;
        }

        if (!SnapString::endsWith($this->url, '.zip')) {
            throw new \Exception('Invalid plugin url for installation');
        }

        if (!current_user_can('install_plugins')) {
            throw new \Exception('User does not have permission to install plugins');
        }

        if (!class_exists('Plugin_Upgrader')) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        }
        wp_cache_flush();

        $upgrader = new \Plugin_Upgrader(new \Automatic_Upgrader_Skin());
        if (!$upgrader->install($this->url)) {
            throw new \Exception('Failed to install plugin');
        }

        return true;
    }

    /**
     * Activate this plugin
     *
     * @return bool true on success
     */
    public function activate()
    {
        if ($this->isActive()) {
            return true;
        }

        if (!is_null(activate_plugin($this->slug))) {
            throw new \Exception('Failed to activate plugin');
        }

        return true;
    }
}
