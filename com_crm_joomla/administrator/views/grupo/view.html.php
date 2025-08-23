<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm_joomla
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Com_Crm_Joomla\Administrator\View\Grupo;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

\defined('_JEXEC') or die;

/**
 * View for the Grupo edit form
 *
 * @since  1.0.0
 */
class GrupoView extends BaseHtmlView
{
    protected $form;
    protected $item;
    protected $state;

    /**
     * Display the view
     *
     * @param   string  $tpl  The name of the template file to parse.
     *
     * @return  void
     */
    public function display($tpl = null)
    {
        $this->form  = $this->get('Form');
        $this->item  = $this->get('Item');
        $this->state = $this->get('State');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            throw new \Exception(implode("\n", $errors), 500);
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     */
    protected function addToolbar()
    {
        $isNew = ($this->item->id == 0);
        $title = $isNew ? Text::_('COM_CRM_JOOMLA_GRUPO_NEW_TITLE') : Text::_('COM_CRM_JOOMLA_GRUPO_EDIT_TITLE');

        ToolbarHelper::title($title, 'users-group');

        ToolbarHelper::apply('grupo.apply');
        ToolbarHelper::save('grupo.save');
        ToolbarHelper::cancel('grupo.cancel', 'JTOOLBAR_CANCEL');
    }
}
