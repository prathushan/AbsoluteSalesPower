<?php

use Duplicator\Utils\LinkManager;

defined("ABSPATH") || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */
?>
<p>
    <?php
    printf(
        wp_kses(
            __(
                'In addition to the <a href="%s" target="_blank" rel="noopener noreferrer">classic installer method</a> ' .
                'on an empty site, Duplicator Pro now supports Drag and Drop migrations and site restores! Simply drag ' .
                'the bundled site Backup to the site you wish to overwrite.',
                'duplicator'
            ),
            array(
                'a' => array(
                    'href'   => array(),
                    'rel'    => array(),
                    'target' => array(),
                )
            )
        ),
        LinkManager::getPostUrl('how-to-move-a-wordpress-website-to-a-new-host', 'import_popup', 'classic installer method')
    );
    ?>
</p>
