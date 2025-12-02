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
 * Campanhas View
 */
class CrmViewCampanhas extends JViewLegacy
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


parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     */
    protected function addToolbar()
    {
        JToolbarHelper::title(JText::_('COM_CRM_CAMPANHAS_VIEW_DEFAULT_TITLE'));

        $user = JFactory::getApplication()->getUser();

        if ($user->authorise('core.create', 'com_crm')) {
            JToolbarHelper::addNew('campanha.add');
        }

        if ($user->authorise('core.edit', 'com_crm')) {
            JToolbarHelper::editList('campanha.edit');
        }

        if ($user->authorise('core.delete', 'com_crm')) {
            JToolbarHelper::deleteList(JText::_('COM_CRM_CONFIRM_DELETE'), 'campanhas.delete');
        }

        if ($user->authorise('core.edit.state', 'com_crm')) {
            JToolbarHelper::publish('campanhas.publish', 'JTOOLBAR_PUBLISH', true);
            JToolbarHelper::unpublish('campanhas.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }
    }
}
