<?php
namespace Joomla\Component\Crm\Administrator\View\ImportArquivos;

use Joomla\CMS\MVC\View\ListView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

class HtmlView extends ListView
{
    public function display($tpl = null)
    {
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');

        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar()
    {
        ToolbarHelper::title(Text::_('COM_CRM_IMPORTARQUIVOS_TITLE'));

        $user = $this->getApplication()->getIdentity();

        if ($user->authorise('core.create', 'com_crm.importacao')) {
            ToolbarHelper::addNew('importarquivo.add');
        }

        if ($user->authorise('core.delete', 'com_crm.importacao')) {
            ToolbarHelper::deleteList(Text::_('COM_CRM_CONFIRM_DELETE'), 'importarquivos.delete');
        }
    }
}
