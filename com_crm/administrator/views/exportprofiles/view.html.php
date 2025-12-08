<?php
defined('_JEXEC') or die;

class CrmViewExportprofiles extends JViewLegacy
{
    public function display($tpl = null)
    {
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        JHtmlSidebar::setAction('index.php?option=com_crm&view=exportprofiles');
        $this->sidebar = JHtmlSidebar::render();

        if (count($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            return;
        }

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        JToolbarHelper::title(JText::_('COM_CRM_EXPORT_PROFILES'));

        $user = JFactory::getUser();
        if ($user->authorise('core.create', 'com_crm')) {
            JToolbarHelper::addNew('exportprofile.add');
        }
        if ($user->authorise('core.edit', 'com_crm')) {
            JToolbarHelper::editList('exportprofile.edit');
        }
        if ($user->authorise('core.delete', 'com_crm')) {
            JToolbarHelper::deleteList('', 'exportprofiles.delete');
        }
    }
}
