<?php

namespace Duplicator\Utils\Support;

use DUP_Log;
use DUP_Package;
use DUP_Server;
use DUP_Settings;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Utils\ZipArchiveExtended;
use Exception;

class SupportToolkit
{
    const SUPPORT_TOOLKIT_BACKUP_NUMBER = 10;
    const SUPPORT_TOOLKIT_PREFIX        = 'duplicator_support_toolkit_';

    /**
     * Returns true if the diagnostic data can be downloaded
     *
     * @return bool true if diagnostic info can be downloaded
     */
    public static function isAvailable()
    {
        return ZipArchiveExtended::isPhpZipAvailable();
    }

    /**
     * Returns the diagnostic data download URL if available,
     * empty string otherwise.
     *
     * @return string
     */
    public static function getSupportToolkitDownloadUrl()
    {
        if (!self::isAvailable()) {
            return '';
        }

        return admin_url('admin-ajax.php') . '?' . http_build_query([
            'action' => 'duplicator_download_support_toolkit',
            'nonce'  => wp_create_nonce('duplicator_download_support_toolkit'),
        ]);
    }

    /**
     * Generates a support toolkit zip file
     *
     * @return string The path to the generated zip file
     */
    public static function getToolkit()
    {
        $tempZipFilePath = DUP_Settings::getSsdirTmpPath() . '/' .
            self::SUPPORT_TOOLKIT_PREFIX . date('YmdHis') . '_' .
            SnapUtil::generatePassword(16, false, false) . '.zip';
        $zip             = new ZipArchiveExtended($tempZipFilePath);

        if ($zip->open() === false) {
            throw new Exception(__('Failed to create zip file', 'duplicator'));
        }

        // Trace log
        if (DUP_Settings::Get('trace_log_enabled')) {
            $zip->addFile(DUP_Log::getTraceFilepath());
        }

        // Debug log (if it exists)
        if (WP_DEBUG_LOG !== false) {
            if (is_bool(WP_DEBUG_LOG) && WP_DEBUG_LOG === true) {
                $zip->addFile(
                    trailingslashit(wp_normalize_path(realpath(WP_CONTENT_DIR))) . 'debug.log',
                    '',
                    10 * MB_IN_BYTES
                );
            } elseif (is_string(WP_DEBUG_LOG) && strlen(WP_DEBUG_LOG) > 0) {
                //The path can be relative too so resolve via safepath
                $zip->addFile(
                    SnapIO::safePath(WP_DEBUG_LOG, true),
                    '',
                    10 * MB_IN_BYTES
                );
            }
        }

        //phpinfo (as html)
        $zip->addFileFromString('phpinfo.html', self::getPhpInfo());

        //custom server settings info (as html)
        $zip->addFileFromString('serverinfo.txt', self::getPlainServerSettings());

        //Last 10 backup build logs
        DUP_Package::by_status_callback(
            function (DUP_Package $package) use ($zip) {
                $file_path = DUP_Settings::getSsdirPath() . "/" . $package->getLogFilename();
                $zip->addFile($file_path);
            },
            [],
            self::SUPPORT_TOOLKIT_BACKUP_NUMBER,
            0,
            '`id` DESC'
        );

        return $tempZipFilePath;
    }

    /**
     * Returns the contents of the "Server Settings" section in "Tools" > "General" in plain text format
     *
     * @return string
     */
    private static function getPlainServerSettings()
    {
        $result = '';

        foreach (DUP_Server::getServerSettingsData() as $section) {
            $result .= $section['title'] . "\n";
            $result .= str_repeat('=', 50) . "\n";
            foreach ($section['settings'] as $data) {
                $result .= str_pad($data['logLabel'], 20, ' ', STR_PAD_RIGHT) . ' ' . $data['value'] . "\n";
            }
            $result .= "\n\n";
        }

        return $result;
    }

    /**
     * Returns the output of phpinfo as a string
     *
     * @return string
     */
    private static function getPhpInfo()
    {
        ob_start();
        SnapUtil::phpinfo();
        $phpInfo = ob_get_clean();

        return $phpInfo === false ? '' : $phpInfo;
    }
}
