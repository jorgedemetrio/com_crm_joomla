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
            JText::_('COM_CRM_CAMPANHA_EMAILS'),
            'index.php?option=com_crm&view=campanhaemails',
            $vName == 'campanhaemails'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_CRM_SMS_TEMPLATES'),
            'index.php?option=com_crm&view=campanhasmslist',
            $vName == 'campanhasmslist'
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
        JHtmlSidebar::addEntry(
            JText::_('COM_CRM_IMPORT_WEB'),
            'index.php?option=com_crm&view=importwebs',
            $vName == 'importwebs'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_CRM_INTEGRACOES'),
            'index.php?option=com_crm&view=integracoes',
            $vName == 'integracoes'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_CRM_EXPORT_PROFILES'),
            'index.php?option=com_crm&view=exportprofiles',
            $vName == 'exportprofiles'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_CRM_AGENDAMENTOS'),
            'index.php?option=com_crm&view=agendamentos',
            $vName == 'agendamentos'
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
