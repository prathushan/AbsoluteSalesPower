<?php

/**
 *
 * @package   Duplicator
 * @copyright (c) 2023, Snap Creek LLC
 */

use Duplicator\Core\Controllers\ControllersManager;

defined("ABSPATH") || exit;

/**
* Variables
*
* @var \Duplicator\Core\Views\TplMng  $tplMng
* @var array<string, mixed> $tplData
*/
?>
<div class="intro">
    <div class="sullie">
        <img src="<?php echo DUPLICATOR_PLUGIN_URL; ?>assets/img/welcome/willie.svg"
             alt="<?php esc_attr_e('Willie the Duplicator mascot', 'duplicator'); ?>">
    </div>
    <div class="block">
        <h1><?php esc_html_e('Never miss an important update', 'duplicator'); ?></h1>
    </div>
    <div class="block">
        <h6>
            <?php esc_html_e(
                'Opt in to get email notifications for security & feature updates, educational content, ' .
                'and occasional offers, and to share some basic WordPress environment info. This will ' .
                'help us make the plugin more compatible with your site and better at doing what you need it to.',
                'duplicator'
            ); ?>
        </h6>
        <div class="button-wrap">
            <div>
                <button id="enable-usage-stats-btn" class="dup-btn dup-btn-lg dup-btn-orange dup-btn-block">
                    <?php esc_html_e('Allow & Continue', 'duplicator'); ?>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
            <div>
                <a href="<?php echo ControllersManager::getMenuLink(ControllersManager::PACKAGES_SUBMENU_SLUG); ?>"
                   class="dup-btn dup-btn-lg dup-btn-grey dup-btn-block"
                   rel="noopener noreferrer">
                    <?php esc_html_e('Skip', 'duplicator'); ?>
                </a>
            </div>
        </div>
    </div>
    <div class="block terms-container">
        <div class="terms-list-toggle">
            <?php esc_html_e('This will allow Duplicator to', 'duplicator'); ?>
            <i class="fas fa-chevron-right fa-sm"></i>
        </div>
        <ul class="terms-list" style="display: none;">
            <li>
                <i class="fas fa-user"></i>
                <div>
                    <b>
                        <?php esc_html_e('View Basic Profile Info', 'duplicator'); ?>
                        <i 
                            class="fas fa-question-circle"
                            data-tooltip-title="<?php esc_attr_e("Basic Profile Info", 'duplicator'); ?>"
                            data-tooltip="<?php
                                esc_attr_e(
                                    'Never miss important updates, get security warnings before they ' .
                                    'become public knowledge, and receive notifications about special offers and awesome new features.',
                                    'duplicator'
                                ); ?>"
                            aria-expanded="false"
                        ></i>
                    </b>
                    <p>
                        <?php esc_html_e(
                            'Your WordPress user\'s: first & last name, and email address',
                            'duplicator'
                        ); ?>
                    </p>
                </div>
            </li>
            <li>
                <i class="fas fa-globe"></i>
                <div>
                    <b>
                        <?php esc_html_e('View Basic Website Info', 'duplicator'); ?>
                        <i 
                            class="fas fa-question-circle"
                            data-tooltip-title="<?php esc_attr_e("Basic Website Info", 'duplicator'); ?>"
                            data-tooltip="<?php
                                esc_attr_e(
                                    'To provide additional functionality that\'s relevant to your website, avoid WordPress ' .
                                    'or PHP version incompatibilities that can break your website, and recognize which ' .
                                    'languages & regions the plugin should be translated and tailored to.',
                                    'duplicator'
                                ); ?>"
                            aria-expanded="false"
                        ></i>
                    </b>
                    <p>
                        <?php esc_html_e(
                            'Homepage URL & title, WP & PHP versions, and site language',
                            'duplicator'
                        ); ?>
                    </p>
                </div>
            </li>
            <li>
                <i class="fas fa-plug"></i>
                <div>
                    <b><?php esc_html_e('View Basic Plugin Info', 'duplicator'); ?></b>
                    <p>
                        <?php esc_html_e(
                            'Current plugin & SDK versions, and if active or uninstalled',
                            'duplicator'
                        ); ?>
                    </p>
                </div>
            </li>
            <li>
                <i class="fas fa-palette"></i>
                <div>
                    <b>
                        <?php esc_html_e('View Plugins & Themes List', 'duplicator'); ?>
                        <i 
                            class="fas fa-question-circle"
                            data-tooltip-title="<?php esc_attr_e("Plugins & Themes List", 'duplicator'); ?>"
                            data-tooltip="<?php
                                esc_attr_e(
                                    'To ensure compatibility and avoid conflicts with your installed plugins and themes.',
                                    'duplicator'
                                ); ?>"
                            aria-expanded="false"
                        ></i>
                    </b>
                    <p>
                        <?php esc_html_e(
                            'Names, slugs, versions, and if active or not',
                            'duplicator'
                        ); ?>
                    </p>
                </div>
            </li>
        </ul>
    </div>
</div>
