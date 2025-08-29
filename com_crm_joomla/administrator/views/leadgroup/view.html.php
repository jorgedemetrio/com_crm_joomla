<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Crm\Administrator\View\Leadgroup;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

/**
 * View to edit a Leadgroup.
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
    protected $form;
    protected $item;

    /**
     * Display the view
     *
     * @param   string  $tpl  The name of the template file to parse.
     *
     * @return  void
     */
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


    /**
     * Add the page title and toolbar.
     *
     * @return  void
     * @since   1.0.0
     */
    protected function addToolbar()
    {
        Factory::getApplication()->input->set('hidemainmenu', true);

        $isNew = ($this->item->id == 0);

        ToolbarHelper::title($isNew ? Text::_('COM_CRM_LEADGROUP_NEW') : Text::_('COM_CRM_LEADGROUP_EDIT'));

        ToolbarHelper::apply('leadgroup.apply');
        ToolbarHelper::save('leadgroup.save');
        ToolbarHelper::save2new('leadgroup.save2new');
        ToolbarHelper::cancel('leadgroup.cancel');
    }
}
