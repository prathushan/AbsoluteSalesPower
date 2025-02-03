<?php

/**
 * @package Duplicator
 */

namespace Duplicator\Utils;

use Duplicator\Installer\Utils\InstallerLinkManager;

/**
 * Link manager class
 */
class LinkManager extends InstallerLinkManager
{
    /**
     * @param string|string[] $paths   The path to the article
     * @param string          $medium  The utm medium
     * @param string          $content The utm content
     *
     * @return string The url with path and utm params
     */
    protected static function buildUrl($paths, $medium, $content)
    {
        return apply_filters('duplicator_upsell_url_filter', parent::buildUrl($paths, $medium, $content));
    }
}
