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
 * GrupoLead Model
 */
class CrmModelGrupoLead extends JModelAdmin
{
    /**
     * Method to get the record form.
     */
    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm(
            'com_crm.grupolead',
            'grupolead',
            ['control' => 'jform', 'load_data' => $loadData]
        );

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected into the form.
     */
    protected function loadFormData()
    {
        $data = JFactory::getApplication()->getUserState(
            'com_crm.edit.grupolead.data',
            []
        );

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }
}
