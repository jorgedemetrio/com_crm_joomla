<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Crm\Administrator\View\CampanhaEmail;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

/**
 * View to edit a CampanhaEmail.
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
    protected $form;
    protected $item;

    public function display($tpl = null)
    {
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');

        if (count($errors = $this->get('Errors'))) {
            Factory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            return;
        }

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        Factory::getApplication()->input->set('hidemainmenu', true);
        $isNew = ($this->item->id == 0);
        ToolbarHelper::title($isNew ? Text::_('COM_CRM_CAMPANHAEMAIL_NEW') : Text::_('COM_CRM_CAMPANHAEMAIL_EDIT'));
        ToolbarHelper::apply('campanhaemail.apply');
        ToolbarHelper::save('campanhaemail.save');
        ToolbarHelper::save2new('campanhaemail.save2new');
        ToolbarHelper::cancel('campanhaemail.cancel');
    }
}
