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

    /**
     * Check rate limit for a specific action/table.
     *
     * @param   string   $table           Table name (e.g. #__crm_email_opens).
     * @param   string   $dateColumn      Column name for timestamp (e.g. opened_at).
     * @param   string   $ip              IP address to check.
     * @param   integer  $limit           Max allowed requests.
     * @param   integer  $intervalMinutes Time interval in minutes.
     *
     * @return  boolean  True if allowed, False if limit exceeded.
     */
    public static function checkRateLimit($table, $dateColumn, $ip, $limit = 60, $intervalMinutes = 1)
    {
        $db = JFactory::getDbo();
        $date = JFactory::getDate();
        $date->modify("-{$intervalMinutes} minutes");
        $dbDate = $db->quote($date->toSql());

        $query = $db->getQuery(true)
            ->select('COUNT(id)')
            ->from($db->quoteName($table))
            ->where($db->quoteName('ip') . ' = ' . $db->quote($ip))
            ->where($db->quoteName($dateColumn) . ' > ' . $dbDate);

        $db->setQuery($query);
        $count = (int) $db->loadResult();

        return $count < $limit;
    }
}
