<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Crm\Administrator\Model;

use Joomla\CMS\MVC\Model\AdminModel;

/**
 * Lead Model
 */
class LeadModel extends AdminModel
{
    /**
     * Method to get the record form.
     *
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  \Joomla\CMS\Form\Form|false  A Form object on success, false on failure.
     *
     * @since   1.0.0
     */
    public function getForm($loadData = true)
    {
        // Get the form.
        $form = $this->loadForm(
            'com_crm.lead',
            'lead',
            ['control' => 'jform', 'load_data' => $loadData]
        );

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected into the form.
     *
     * @return  mixed  The data for the form.
     *
     * @since   1.0.0
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = $this->getApplication()->getUserState(
            'com_crm.edit.lead.data',
            []
        );

        if (empty($data)) {
            $data = $this->getItem();
        }

        // Load the associated groups for an existing lead.
        if (!empty($data->id)) {
            $dbDriver = $this->getDbo();
            $query = $dbDriver->getQuery(true)
                ->select('group_id')
                ->from($dbDriver->quoteName('#__crm_lead_group_map'))
                ->where($dbDriver->quoteName('lead_id') . ' = ' . $dbDriver->quote($data->id));
            $dbDriver->setQuery($query);
            $data->groups = $dbDriver->loadColumn();
        }

        return $data;
    }

    /**
     * Override the save method to handle the many-to-many relationship.
     *
     * @param   array  $data  An array of form data.
     *
     * @return  boolean  True on success, false on failure.
     *
     * @since   1.0.0
     */
    public function save($data)
    {
        $dbDriver = $this->getDbo();
        $groups = $data['groups'] ?? [];

        // Unset groups from data array so it doesn't interfere with parent::save
        unset($data['groups']);

        // First, save the lead itself
        if (!parent::save($data)) {
            return false;
        }

        // Get the ID of the saved lead
        $leadId = $this->getState($this->context . '.id');

        // Now, handle the groups
        // 1. Delete existing relationships for this lead
        $query = $dbDriver->getQuery(true)
            ->delete($dbDriver->quoteName('#__crm_lead_group_map'))
            ->where($dbDriver->quoteName('lead_id') . ' = ' . $dbDriver->quote($leadId));
        $dbDriver->setQuery($query);

        try {
            $dbDriver->execute();
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }

        // 2. Insert new relationships if any were selected
        if (!empty($groups)) {
            $query = $dbDriver->getQuery(true)
                ->insert($dbDriver->quoteName('#__crm_lead_group_map'))
                ->columns([$dbDriver->quoteName('lead_id'), $dbDriver->quoteName('group_id')]);

            foreach ($groups as $groupId) {
                if (!empty($groupId)) {
                    $query->values($dbDriver->quote($leadId) . ', ' . $dbDriver->quote($groupId));
                }
            }

            $dbDriver->setQuery($query);

            try {
                $dbDriver->execute();
            } catch (\Exception $e) {
                $this->setError($e->getMessage());
                return false;
            }
        }

        return true;
    }
}
