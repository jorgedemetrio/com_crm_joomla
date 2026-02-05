<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024 Sobieski Produções. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Crm Security Helper.
 * Centralizes security-related logic for the site component.
 *
 * @since  1.0.0
 */
class CrmSecurityHelper
{
    /**
     * Parse and validate HTTP_X_FORWARDED_FOR header.
     *
     * @param   string  $header  The HTTP header value.
     *
     * @return  string  The first valid IP address found, or empty string.
     */
    public static function getValidProxyIp($header)
    {
        $ipProxy = '';

        if (!empty($header)) {
            $parts = explode(',', $header);

            foreach ($parts as $part) {
                $part = trim($part);

                if (filter_var($part, FILTER_VALIDATE_IP)) {
                    $ipProxy = $part;
                    break;
                }
            }
        }

        return substr($ipProxy, 0, 45);
    }
}
