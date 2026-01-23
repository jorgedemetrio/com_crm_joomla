<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_crm
 */

defined('_JEXEC') or die;

function CrmBuildRoute(&$query)
{
    $segments = array();

    if (isset($query['view'])) {
        $segments[] = $query['view'];
        unset($query['view']);
    }

    if (isset($query['id'])) {
        $segments[] = $query['id'];
        unset($query['id']);
    }

    if (isset($query['Itemid'])) {
        $query['Itemid'] = (int) $query['Itemid'];
    }

    return $segments;
}

function CrmParseRoute($segments)
{
    $vars = array();

    if (!empty($segments)) {
        $vars['view'] = array_shift($segments);

        if (!empty($segments)) {
            $id = array_shift($segments);
            // Sentinel: Allow Integers or UUIDs only
            if (is_numeric($id) || preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $id)) {
                $vars['id'] = $id;
            }
        }
    }

    return $vars;
}
