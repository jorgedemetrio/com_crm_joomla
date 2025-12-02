<?php
defined('_JEXEC') or die;

class CrmViewImportweb extends JViewLegacy
{
    public function display($tpl = null)
    {
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');

        if (count($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            return;
        }

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        JToolbarHelper::title(JText::_('COM_CRM_IMPORT_WEB_EDIT'));

        $isNew = ($this->item->id == 0 || empty($this->item->id));
        JToolbarHelper::apply('importweb.apply');
        JToolbarHelper::save('importweb.save');
        JToolbarHelper::cancel('importweb.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
    }
}
