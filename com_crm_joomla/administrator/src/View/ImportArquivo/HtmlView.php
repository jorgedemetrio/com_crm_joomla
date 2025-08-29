<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Crm\Administrator\View\ImportArquivo;

use Joomla\CMS\MVC\View\AdminView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

/**
 * ImportArquivo View
 *
 * @since  1.0.0
 */
class HtmlView extends AdminView
{
    protected $form;
    protected $item;

    /**
     * Display the view
     */
    public function display($tpl = null)
    {
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            Factory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            return false;
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     */
    protected function addToolbar()
    {
        $isNew = ($this->item->id == 0);

        ToolbarHelper::title($isNew ? Text::_('COM_CRM_IMPORTARQUIVO_VIEW_NEW_TITLE') : Text::_('COM_CRM_IMPORTARQUIVO_VIEW_EDIT_TITLE'));

        ToolbarHelper::apply('importarquivo.apply');
        ToolbarHelper::save('importarquivo.save');
        ToolbarHelper::save2new('importarquivo.save2new');
        ToolbarHelper::cancel('importarquivo.cancel');
    }
}
