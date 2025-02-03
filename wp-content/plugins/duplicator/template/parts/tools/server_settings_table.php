<?php

/**
 * Duplicator messages sections
 *
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */
$serverSettings = $tplData['serverSettings'];
?>
<table class="widefat" cellspacing="0">
<?php foreach ($serverSettings as $section) : ?>
    <tr>
        <td class="dup-settings-diag-header" colspan="2"><?php echo esc_html($section['title']); ?></td>
    </tr>
    <?php foreach ($section['settings'] as $setting) : ?>
        <tr>
            <td>
                <?php if (!empty($setting['labelLink'])) : ?>
                    <a href="<?php echo esc_url($setting['labelLink']); ?>" target="_blank">
                        <?php echo esc_html($setting['label']); ?>
                    </a>
                <?php else : ?>
                    <?php echo esc_html($setting['label']); ?>
                <?php endif; ?>
            </td>
            <td>
                <?php echo esc_html($setting['value']); ?>
                <?php if (!empty($setting['valueNote'])) : ?>
                <small>
                    <i>
                        <?php echo wp_kses(
                            $setting['valueNote'],
                            [
                                'a' => [
                                    'href'   => [],
                                    'target' => [],
                                ],
                            ]
                        ); ?>
                    </i>
                </small>
                <?php endif; ?>
                <?php if (!empty($setting['valueNoteBottom'])) : ?>
                <p>
                    <small>
                        <i>
                            <?php echo wp_kses(
                                $setting['valueNoteBottom'],
                                [
                                    'a' => [
                                        'href'   => [],
                                        'target' => [],
                                    ],
                                ]
                            ); ?>
                        </i>
                    </small>
                </p>
                <?php endif; ?>
                <?php if (!empty($setting['valueTooltip'])) : ?>
                    <i 
                      class="fa fa-question-circle data-size-help"
                      data-tooltip-title="<?php echo __('Info', 'duplicator'); ?>"
                      data-tooltip="<?php echo esc_attr($setting['valueTooltip']); ?>">
                    </i>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endforeach; ?>
</table>
