<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024 Sobieski Produções. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('CrmHelper', __DIR__ . '/helpers/crm.php');

class CrmController extends JControllerLegacy
{
    /**
     * The default view for the display task.
     *
     * @var string
     */
    protected $default_view = 'leads';

    /**
     * Display the view
     *
     * @param   boolean  $cachable   If true, the view output will be cached
     * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return  CrmController  This object to support chaining.
     */
    public function display($cachable = false, $urlparams = false)
    {
        $view   = $this->input->get('view', 'leads');
        $layout = $this->input->get('layout', 'default');
        $id     = $this->input->getInt('id');

        CrmHelper::addSubmenu($view);

        // Check for edit form.
        if ($view == 'lead' && $layout == 'edit' && !$this->checkEditId('com_crm.edit.lead', $id))
        {
            // Somehow the person just went to the form - we don't allow that.
            $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
            $this->setMessage($this->getError(), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_crm&view=leads', false));

            return false;
        }

        return parent::display($cachable, $urlparams);
    }
}
