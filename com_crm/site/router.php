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
        $segments[] = (int) $query['id'];
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
            $vars['id'] = (int) array_shift($segments);
        }
    }

    return $vars;
}
