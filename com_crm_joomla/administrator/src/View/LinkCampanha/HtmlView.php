<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Crm\Administrator\View\LinkCampanha;

use Joomla\CMS\MVC\View\AdminView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

/**
 * LinkCampanha View
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
            $this->getApplication()->enqueueMessage(implode("\n", $errors), 'error');
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
        $user  = $this->getApplication()->getIdentity();
        $isNew = ($this->item->id == 0);

        // Set the title
        $title = $isNew ? Text::_('COM_CRM_LINKCAMPANHA_VIEW_NEW_TITLE') : Text::_('COM_CRM_LINKCAMPANHA_VIEW_EDIT_TITLE');
        ToolbarHelper::title($title);

        // Check if the user can edit this item.
        $canDo = $isNew ? $user->authorise('core.create', 'com_crm.linkcampanha') : $user->authorise('core.edit', 'com_crm.linkcampanha.' . $this->item->id);

        if ($canDo) {
            ToolbarHelper::apply('linkcampanha.apply');
            ToolbarHelper::save('linkcampanha.save');

            if ($user->authorise('core.create', 'com_crm.linkcampanha')) {
                ToolbarHelper::save2new('linkcampanha.save2new');
            }
        }

        // For new records, check the create permission.
        if ($isNew && ($user->authorise('core.create', 'com_crm.linkcampanha'))) {
             ToolbarHelper::cancel('linkcampanha.cancel', 'JTOOLBAR_CANCEL');
        } else {
             ToolbarHelper::cancel('linkcampanha.cancel', 'JTOOLBAR_CLOSE');
        }
    }
}
