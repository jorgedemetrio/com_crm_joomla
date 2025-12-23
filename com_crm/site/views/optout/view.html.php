<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class CrmViewOptout extends JViewLegacy
{
    public $data;
    public $itemid;

    public function display($tpl = null)
    {
        $app = JFactory::getApplication();
        $input = $app->input;

        // If data is not assigned by controller, populate from input
        if (empty($this->data)) {
            $this->data = array(
                'email'       => $input->getString('email'),
                'scope'       => $input->getWord('scope', 'global'),
                'campanha_id' => $input->getString('campanha_id'),
                'reason'      => $input->getString('reason'),
                'tracking'    => $input->getString('tracking')
            );
        }

        $this->itemid = $input->getInt('Itemid');

        $this->setDocument();

        return parent::display($tpl);
    }

    protected function setDocument()
    {
        $doc = JFactory::getDocument();
        $doc->setTitle(JText::_('COM_CRM_OPTOUT_CONFIRM_TITLE'));
    }
}
