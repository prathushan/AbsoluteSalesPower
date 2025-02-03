<?php

/**
 * Template for First Package section
 *
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

use Duplicator\Utils\LinkManager;

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */
?>
<div class="dup-admin-about-section dup-admin-about-section-first-form" style="display:flex;">

    <div class="dup-admin-about-section-first-form-text">

        <h2><?php _e('Creating Your First Backup', 'duplicator') ?></h2>

        <p>
            <?php _e('Want to get started creating your first Backup with Duplicator? By following the step by step ' .
                'instructions in this walkthrough, you can easily create a backup or migration.', 'duplicator') ?>
        </p>
        <p>
            <?php _e('To begin, you’ll need to be logged into the WordPress admin area. Once there, click on Duplicator ' .
                'in the admin sidebar to go the Backups page.', 'duplicator') ?>
        </p>
        <p>
            <?php _e('In the Backups page, the Backups list will be empty because there are no Backups yet. To create ' .
                'a new Backup, click on the Create New button, and this will launch the Backup Creation Wizard.', 'duplicator') ?>
        </p>

        <ul class="list-plain">
            <li>
                <a 
                    href="<?php echo esc_url(LinkManager::getCategoryUrl(LinkManager::QUICK_START_CAT, 'about-getting_started', 'Quick Start Guide')); ?>" 
                    target="_blank" 
                    rel="noopener noreferrer"
                >
                    <?php _e('Quick Start Guide', 'duplicator'); ?>
                </a>
            </li>
            <li>
                <a 
                    href="<?php echo esc_url(LinkManager::getDocUrl('backup-site', 'about-getting_started', 'Create Backup')); ?>" 
                    target="_blank" 
                    rel="noopener noreferrer"
                >
                    <?php _e('How to Create a Backup', 'duplicator'); ?>
                </a>
            </li>
            <li>
                <a 
                    href="<?php echo esc_url(LinkManager::getDocUrl('classic-install', 'about-getting_started', 'Migrate')); ?>" 
                    target="_blank" 
                    rel="noopener noreferrer"
                >
                    <?php _e('How to Migrate to a New Site', 'duplicator'); ?>
                </a>
            </li>
        </ul>

    </div>

</div>
