<?php

defined('_JEXEC') or die;

class CrmViewImportArquivo extends JViewLegacy
{
    protected $form;
    protected $item;
    protected $previewData;

    public function display($tpl = null)
    {
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');

        // If processing, get preview data
        if ($this->getLayout() === 'process') {
            $this->previewData = $this->getModel()->getPreviewData();
        }

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        $user  = JFactory::getApplication()->getUser();
        $isNew = ($this->item->id == 0);

        if ($this->getLayout() === 'process') {
            JToolbarHelper::title(JText::_('COM_CRM_IMPORTARQUIVO_PROCESS_TITLE') . ': ' . $this->item->nome);
            // Add a "Start Import" button
            JToolbarHelper::custom('importarquivo.doImport', 'upload', 'upload', 'COM_CRM_IMPORTARQUIVO_START_IMPORT', false);
            JToolbarHelper::cancel('importarquivo.cancel', 'JTOOLBAR_CANCEL');
            return;
        }

        $title = $isNew ? 'COM_CRM_IMPORTARQUIVO_NEW' : 'COM_CRM_IMPORTARQUIVO_EDIT';
        JToolbarHelper::title(JText::_($title));

        if ($isNew ? $user->authorise('core.create', 'com_crm.importacao') : $user->authorise('core.edit', 'com_crm.importacao'))
        {
            JToolbarHelper::apply('importarquivo.apply');
            JToolbarHelper::save('importarquivo.save');
        }

        JToolbarHelper::cancel('importarquivo.cancel', 'JTOOLBAR_CANCEL');
    }
}
