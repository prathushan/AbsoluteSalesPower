<?php

/**
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace Duplicator\Ajax;

use Duplicator\Ajax\AjaxWrapper;
use Duplicator\Libs\Snap\SnapURL;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Utils\Support\SupportToolkit;

class ServicesTools extends AbstractAjaxService
{
    /**
     * Init ajax calls
     *
     * @return void
     */
    public function init()
    {
        $this->addAjaxCall('wp_ajax_duplicator_download_support_toolkit', 'downloadSupportToolkit');
    }

    /**
     * Function to download diagnostic data
     *
     * @return never
     */
    public function downloadSupportToolkit()
    {
        AjaxWrapper::fileDownload(
            [
                __CLASS__,
                'downloadSupportToolkitCallback',
            ],
            'duplicator_download_support_toolkit',
            SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'nonce')
        );
    }

    /**
     * Function to create diagnostic data
     *
     * @return false|array{path:string,name:string}
     */
    public static function downloadSupportToolkitCallback()
    {
        $domain = SnapURL::wwwRemove(SnapURL::parseUrl(network_home_url(), PHP_URL_HOST));
        $result = [
            'path' => SupportToolkit::getToolkit(),
            'name' => SupportToolkit::SUPPORT_TOOLKIT_PREFIX .
                substr(sanitize_file_name($domain), 0, 12) . '_' .
                date('YmdHis') . '.zip',
        ];

        return $result;
    }
}
