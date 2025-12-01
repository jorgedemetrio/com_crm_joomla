<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * LinksCampanha View
 */
class CrmViewLinksCampanha extends JViewLegacy
{
    /**
     * Display the view
     */
    public function display($tpl = null)
    {
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        if (count($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            return;
        }

        $this->addToolbar();


        CrmHelper::addSubmenu('linkscampanha');
        $this->sidebar = JHtmlSidebar::render();
parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     */
    protected function addToolbar()
    {
        JToolbarHelper::title(JText::_('COM_CRM_LINKSCAMPANHA_VIEW_DEFAULT_TITLE'));

        $user = JFactory::getApplication()->getUser();

        if ($user->authorise('core.create', 'com_crm')) {
            JToolbarHelper::addNew('linkcampanha.add');
        }

        if ($user->authorise('core.edit', 'com_crm')) {
            JToolbarHelper::editList('linkcampanha.edit');
        }

        if ($user->authorise('core.delete', 'com_crm')) {
            JToolbarHelper::deleteList(JText::_('COM_CRM_CONFIRM_DELETE'), 'linkscampanha.delete');
        }

        if ($user->authorise('core.edit.state', 'com_crm')) {
            JToolbarHelper::publish('linkscampanha.publish', 'JTOOLBAR_PUBLISH', true);
            JToolbarHelper::unpublish('linkscampanha.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }
    }
}
