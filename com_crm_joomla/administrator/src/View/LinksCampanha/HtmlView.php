<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Crm\Administrator\View\LinksCampanha;

use Joomla\CMS\MVC\View\ListView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

/**
 * LinksCampanha View
 */
class HtmlView extends ListView
{
    /**
     * Display the view
     */
    public function display($tpl = null)
    {
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        if (count($errors = $this->get('Errors'))) {
            $this->getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            return;
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     */
    protected function addToolbar()
    {
        ToolbarHelper::title(Text::_('COM_CRM_LINKSCAMPANHA_VIEW_DEFAULT_TITLE'));

        $user = $this->getApplication()->getIdentity();

        if ($user->authorise('core.create', 'com_crm.linkcampanha')) {
            ToolbarHelper::addNew('linkcampanha.add');
        }

        if ($user->authorise('core.edit', 'com_crm.linkcampanha')) {
            ToolbarHelper::editList('linkcampanha.edit');
        }

        if ($user->authorise('core.delete', 'com_crm.linkcampanha')) {
            ToolbarHelper::deleteList(Text::_('COM_CRM_CONFIRM_DELETE'), 'linkscampanha.delete');
        }

        if ($user->authorise('core.edit.state', 'com_crm.linkcampanha')) {
            ToolbarHelper::publish('linkscampanha.publish', 'JTOOLBAR_PUBLISH', true);
            ToolbarHelper::unpublish('linkscampanha.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }
    }
}
