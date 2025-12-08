<?php
defined('_JEXEC') or die;

class CrmViewIntegracao extends JViewLegacy
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
        JToolbarHelper::title(JText::_('COM_CRM_INTEGRACOES_EDIT'));

        $isNew = ($this->item->id == 0 || empty($this->item->id));
        JToolbarHelper::apply('integracao.apply');
        JToolbarHelper::save('integracao.save');
        JToolbarHelper::cancel('integracao.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
    }
}
