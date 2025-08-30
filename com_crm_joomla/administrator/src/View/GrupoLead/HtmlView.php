<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Crm\Administrator\View\GrupoLead;

use Joomla\CMS\MVC\View\AdminView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

/**
 * GrupoLead View
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
        $title = $isNew ? Text::_('COM_CRM_GRUPOLEAD_VIEW_NEW_TITLE') : Text::_('COM_CRM_GRUPOLEAD_VIEW_EDIT_TITLE');
        ToolbarHelper::title($title);
        ToolbarHelper::apply('grupolead.apply');
        ToolbarHelper::save('grupolead.save');
        ToolbarHelper::save2new('grupolead.save2new');
        ToolbarHelper::cancel('grupolead.cancel');
    }
}
