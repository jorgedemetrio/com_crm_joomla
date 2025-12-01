<?php

defined('_JEXEC') or die;

class CrmViewImportArquivos extends JViewLegacy
{
    public function display($tpl = null)
    {
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');

        $this->addToolbar();


        CrmHelper::addSubmenu('importarquivos');
        $this->sidebar = JHtmlSidebar::render();
parent::display($tpl);
    }

    protected function addToolbar()
    {
        JToolbarHelper::title(JText::_('COM_CRM_IMPORTARQUIVOS_TITLE'));

        $user = JFactory::getApplication()->getUser();

        if ($user->authorise('core.create', 'com_crm.importacao')) {
            JToolbarHelper::addNew('importarquivo.add');
        }

        if ($user->authorise('core.edit', 'com_crm.importacao')) {
            JToolbarHelper::custom('importarquivo.process', 'cogs', 'cogs', 'COM_CRM_IMPORTARQUIVO_PROCESS_BUTTON', true);
        }

        if ($user->authorise('core.delete', 'com_crm.importacao')) {
            JToolbarHelper::deleteList(JText::_('COM_CRM_CONFIRM_DELETE'), 'importarquivos.delete');
        }
    }
}
