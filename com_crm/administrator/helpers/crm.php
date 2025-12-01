<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024 Sobieski Produções. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Crm Component Helper.
 */
class CrmHelper
{
    /**
     * Configure the Linkbar.
     *
     * @param   string  $vName  The name of the active view.
     *
     * @return  void
     */
    public static function addSubmenu($vName)
    {
        JHtmlSidebar::addEntry(
            JText::_('COM_CRM_LEADS'),
            'index.php?option=com_crm&view=leads',
            $vName == 'leads'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_CRM_LEAD_GROUPS'),
            'index.php?option=com_crm&view=gruposlead',
            $vName == 'gruposlead'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_CRM_CAMPAIGNS'),
            'index.php?option=com_crm&view=campanhas',
            $vName == 'campanhas'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_CRM_LINKSCAMPANHA'),
            'index.php?option=com_crm&view=linkscampanha',
            $vName == 'linkscampanha'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_CRM_IMPORTARQUIVOS'),
            'index.php?option=com_crm&view=importarquivos',
            $vName == 'importarquivos'
        );
    }

    /**
     * Get the actions
     *
     * @return  JObject
     */
    public static function getActions()
    {
        $user   = JFactory::getUser();
        $result = new JObject;
        $assetName = 'com_crm';

        $actions = JAccess::getActions('com_crm', 'component');

        foreach ($actions as $action) {
            $result->set($action->name, $user->authorise($action->name, $assetName));
        }

        return $result;
    }
}
