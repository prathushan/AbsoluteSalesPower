<?php

use Duplicator\Utils\ExtraPlugins\ExtraItem;

/**
 * Variables
 *
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

defined('ABSPATH') || die();

if (!current_user_can('install_plugins')) {
    return;
}

/** @var ExtraItem[] $plugins */
$plugins = $tplData['plugins'];
/** @var int $limit */
$limit = $tplData['limit'];
?>
<div id="dup-cross-promotion">
    <p>
        <em><?php echo __('Enoying Duplicator? Check out a couple of our other free plugins...', 'duplicator'); ?></em>
    </p>
    <div class="list">
        <?php
        foreach ($plugins as $i => $plugin) {
            if ($i > $limit - 1) {
                break;
            }

            $tplMng->render(
                'parts/cross_promotion/item',
                array('plugin' => $plugin)
            );
        }
        ?>
    </div>
</div>
