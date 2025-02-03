<?php

namespace Duplicator\Utils\ExtraPlugins;

use DUP_LITE_Plugin_Upgrade;
use Duplicator\Controllers\AboutUsController;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Notifications\Notice;
use Duplicator\Core\Views\TplMng;

class CrossPromotion
{
    const PLUGINS_LIMIT = 3;

    /** @var string */
    const NOTICE_SLUG = 'duplicator_cross_promotion';

    /**
     * Init notice
     *
     * @return void
     */
    public static function init()
    {
        if (!current_user_can('install_plugins')) {
            return;
        }

        $installInfo = DUP_LITE_Plugin_Upgrade::getInstallInfo();
        if ($installInfo['updateTime'] + (2 * WEEK_IN_SECONDS) > time()) {
            return;
        }

        if (!ControllersManager::isCurrentPage(ControllersManager::MAIN_MENU_SLUG)) {
            return;
        }

        $plugins = self::getExtraPlugins();
        if (count($plugins) === 0) {
            return;
        }

        AboutUsController::enqueueScripts();

        Notice::add(
            TplMng::getInstance()->render(
                'parts/cross_promotion/list',
                [
                    'plugins' => $plugins,
                    'limit' => self::PLUGINS_LIMIT,
                ],
                false
            ),
            self::NOTICE_SLUG,
            '',
            [
                'autop' => false,
                'dismiss' => Notice::DISMISS_USER,
            ]
        );
    }

    /**
     * Get the extra plugins to be promoted
     *
     * @return ExtraItem[]
     */
    public static function getExtraPlugins()
    {
        $slugs           = self::getSlugs();
        $plugins         = [];
        $extraPluginsMng = ExtraPluginsMng::getInstance();

        foreach ($slugs as $slug) {
            if (count($plugins) >= self::PLUGINS_LIMIT) {
                break;
            }

            if (($plugin = $extraPluginsMng->getBySlug($slug)) === false) {
                continue;
            }

            if ($plugin->isInstalled() || !$plugin->checkRequirments()) {
                continue;
            }

            $plugins[] = $plugin;
        }

        foreach ($extraPluginsMng->getAll() as $plugin) {
            if (count($plugins) >= self::PLUGINS_LIMIT) {
                break;
            }

            if (in_array($plugin->getSlug(), $slugs)) {
                continue;
            }

            if ($plugin->isInstalled() || !$plugin->checkRequirments()) {
                continue;
            }

            $plugins[] = $plugin;
        }

        return $plugins;
    }

    /**
     * Get the slugs of the extra plugins to be promoted with priority
     *
     * @return string[]
     */
    protected static function getSlugs()
    {
        return [
            'search-replace-wpcode/wsrw.php',
            'wp-mail-smtp/wp_mail_smtp.php',
            'insert-headers-and-footers/ihaf.php',
            'all-in-one-seo-pack/all_in_one_seo_pack.php',
            'wpforms-lite/wpforms.php',
            'uncanny-automator/uncanny-automator.php',
        ];
    }
}
