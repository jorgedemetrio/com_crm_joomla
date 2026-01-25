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
            $segment = array_shift($segments);

            // Security: Validate ID segment (Integer OR UUID)
            if (is_numeric($segment) || preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $segment)) {
                $vars['id'] = $segment;
            }
        }
    }

    return $vars;
}
