<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Crm\Administrator\View\Leadgroups;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

/**
 * View for the Leadgroups list.
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{

    protected $items;
    protected $pagination;
    protected $state;
    public $filterForm;
    public $activeFilters;

    public function display($tpl = null)
    {
        $this->items         = $this->get('Items');
        $this->pagination    = $this->get('Pagination');
        $this->state         = $this->get('State');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');


        if (count($errors = $this->get('Errors'))) {
            Factory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            return;
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar()
    {
        ToolbarHelper::title(Text::_('COM_CRM_LEADGROUPS_TITLE'));


        ToolbarHelper::addNew('leadgroup.add');
        ToolbarHelper::editList('leadgroup.edit');
        ToolbarHelper::publish('leadgroups.publish', 'JTOOLBAR_PUBLISH', true);
        ToolbarHelper::unpublish('leadgroups.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        ToolbarHelper::archive('leadgroups.archive', 'JTOOLBAR_ARCHIVE', true);
        ToolbarHelper::deleteList(Text::_('COM_CRM_LEADGROUPS_CONFIRM_DELETE'), 'leadgroups.delete', 'JTOOLBAR_DELETE');

        if ($this->get('State')->get('filter.state') == -2) {
            ToolbarHelper::deleteList(Text::_('COM_CRM_LEADGROUPS_CONFIRM_DELETE'), 'leadgroups.delete', 'JTOOLBAR_EMPTY_TRASH');
        }

        ToolbarHelper::preferences('com_crm');
    }
}
