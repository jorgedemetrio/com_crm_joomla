<?php
defined('_JEXEC') or die;

class CrmViewExportprofile extends JViewLegacy
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
        JToolbarHelper::title(JText::_('COM_CRM_EXPORT_PROFILES_EDIT'));

        $isNew = ($this->item->id == 0 || empty($this->item->id));
        JToolbarHelper::apply('exportprofile.apply');
        JToolbarHelper::save('exportprofile.save');
        JToolbarHelper::cancel('exportprofile.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
    }
}
