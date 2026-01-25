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
            $idSegment = array_shift($segments);

            // Security: Validate ID format (Integer OR UUID) to prevent injection/errors
            if (preg_match('/^\d+$/', (string) $idSegment) || preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', (string) $idSegment)) {
                $vars['id'] = $idSegment;
            }
        }
    }

    return $vars;
}
