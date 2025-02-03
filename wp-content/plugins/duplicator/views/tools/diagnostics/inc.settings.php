<?php

use Duplicator\Core\Views\TplMng;

defined('ABSPATH') || defined('DUPXABSPATH') || exit;
?>

<!-- ==============================
SERVER SETTINGS -->
<div class="dup-box">
<div class="dup-box-title">
    <i class="fas fa-tachometer-alt"></i>
    <?php esc_html_e("Server Settings", 'duplicator') ?>
    <div class="dup-box-arrow"></div>
</div>
<div class="dup-box-panel" id="dup-settings-diag-srv-panel" style="<?php echo esc_html($ui_css_srv_panel); ?>">
    <?php TplMng::getInstance()->render(
        'parts/tools/server_settings_table',
        [
            'serverSettings' => DUP_Server::getServerSettingsData(),
        ]
    ); ?>
</div> <!-- end .dup-box-panel -->
</div> <!-- end .dup-box -->
<br/>
