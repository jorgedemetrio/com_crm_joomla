<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm_joomla
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Com_Crm_Joomla\Administrator\View\Grupos;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\Component\Content\Administrator\Helper\ContentHelper;

\defined('_JEXEC') or die;

/**
 * View for the Grupos list
 *
 * @since  1.0.0
 */
class GruposView extends BaseHtmlView
{
    protected $items;
    protected $pagination;
    protected $state;

    /**
     * Display the view
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function display($tpl = null)
    {
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state      = $this->get('State');
        $this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

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
     *
     * @since   1.0.0
     */
    protected function addToolbar()
    {
        $canDo = Factory::getApplication()->getIdentity();

        ToolbarHelper::title(Text::_('COM_CRM_JOOMLA_GRUPOS_TITLE'), 'users-group');

        if ($canDo->authorise('core.create', 'com_crm_joomla'))
        {
            ToolbarHelper::addNew('grupo.add');
        }

        if ($canDo->authorise('core.edit.state', 'com_crm_joomla'))
        {
            ToolbarHelper::publish('grupos.publish', 'JTOOLBAR_PUBLISH', true);
            ToolbarHelper::unpublish('grupos.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }

        if ($canDo->authorise('core.delete', 'com_crm_joomla'))
        {
            ToolbarHelper::deleteList(Text::_('COM_CRM_JOOMLA_GRUPOS_CONFIRM_DELETE'), 'grupos.delete', 'JTOOLBAR_DELETE');
        }

        if ($canDo->authorise('core.admin', 'com_crm_joomla')) {
            ToolbarHelper::preferences('com_crm_joomla');
        }
    }
}
