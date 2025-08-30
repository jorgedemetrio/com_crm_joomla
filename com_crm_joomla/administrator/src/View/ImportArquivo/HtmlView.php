<?php
namespace Joomla\Component\Crm\Administrator\View\ImportArquivo;

use Joomla\CMS\MVC\View\AdminView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

class HtmlView extends AdminView
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
        $user  = $this->getApplication()->getIdentity();
        $isNew = ($this->item->id == 0);

        if ($this->getLayout() === 'process') {
            ToolbarHelper::title(Text::_('COM_CRM_IMPORTARQUIVO_PROCESS_TITLE') . ': ' . $this->item->nome);
            // Add a "Start Import" button
            ToolbarHelper::custom('importarquivo.doImport', 'upload', 'upload', 'COM_CRM_IMPORTARQUIVO_START_IMPORT', false);
            ToolbarHelper::cancel('importarquivo.cancel', 'JTOOLBAR_CANCEL');
            return;
        }

        $title = $isNew ? 'COM_CRM_IMPORTARQUIVO_NEW' : 'COM_CRM_IMPORTARQUIVO_EDIT';
        ToolbarHelper::title(Text::_($title));

        if ($isNew ? $user->authorise('core.create', 'com_crm.importacao') : $user->authorise('core.edit', 'com_crm.importacao'))
        {
            ToolbarHelper::apply('importarquivo.apply');
            ToolbarHelper::save('importarquivo.save');
        }

        ToolbarHelper::cancel('importarquivo.cancel', 'JTOOLBAR_CANCEL');
    }
}
