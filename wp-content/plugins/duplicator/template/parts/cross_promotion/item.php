<?php

use Duplicator\Utils\ExtraPlugins\ExtraItem;

/**
 * Variables
 *
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 * @var ExtraItem $plugin
 */

$plugin = $tplData['plugin'];
?>
<div class="plugin-item">
    <img src="<?php echo esc_url($plugin->icon); ?>" alt="<?php echo esc_attr($plugin->name); ?> logo"/>
    <div class="details">
        <h5 class="addon-name">
            <?php echo esc_html($plugin->name); ?>
        </h5>
        <p class="addon-desc"><?php echo esc_html($plugin->desc); ?></p>
        <button class="button button-primary dup-extra-plugin-item" data-plugin="<?php echo esc_attr($plugin->getSlug());?>">
            <?php echo esc_html_e('Install', 'duplicator'); ?>
        </button>
    </div>
</div>
