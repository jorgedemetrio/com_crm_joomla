<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Lead View
 */
class CrmViewLead extends JViewLegacy
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
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
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
        $user  = JFactory::getApplication()->getUser();
        $isNew = ($this->item->id == 0);

        // Set the title
        $title = $isNew ? JText::_('COM_CRM_LEAD_VIEW_NEW_TITLE') : JText::_('COM_CRM_LEAD_VIEW_EDIT_TITLE');
        JToolbarHelper::title($title);

        // Check if the user can edit this item.
        $canDo = $isNew ? $user->authorise('core.create', 'com_crm') : $user->authorise('core.edit', 'com_crm');

        if ($canDo) {
            JToolbarHelper::apply('lead.apply');
            JToolbarHelper::save('lead.save');

            if ($user->authorise('core.create', 'com_crm')) {
                JToolbarHelper::save2new('lead.save2new');
            }
        }

        // For new records, check the create permission.
        if ($isNew && ($user->authorise('core.create', 'com_crm'))) {
             JToolbarHelper::cancel('lead.cancel', 'JTOOLBAR_CANCEL');
        } else {
             JToolbarHelper::cancel('lead.cancel', 'JTOOLBAR_CLOSE');
        }
    }
}
