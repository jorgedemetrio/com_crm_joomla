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
use Joomla\CMS\Factory;

/**
 * Lead Model
 */
class LeadModel extends AdminModel
{
    /**
     * Method to get the record form.
     */
    public function getForm($data = [], $loadData = true)
    {
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
     */
    protected function loadFormData()
    {
        $data = Factory::getApplication()->getUserState(
            'com_crm.edit.lead.data',
            []
        );

        if (empty($data)) {
            $data = $this->getItem();
        }

        // Load the associated groups for an existing lead.
        if (!empty($data->id)) {
            $db = Factory::getDbo();
            $query = $db->getQuery(true)
                ->select('group_id')
                ->from('#__crm_lead_group_map')
                ->where('lead_id = ' . $db->quote($data->id));
            $db->setQuery($query);
            $data->groups = $db->loadColumn();
        }

        return $data;
    }

    /**
     * Override the save method to handle the many-to-many relationship
     */
    public function save($data)
    {
        $db = Factory::getDbo();
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
        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__crm_lead_group_map'))
            ->where($db->quoteName('lead_id') . ' = ' . $db->quote($leadId));
        $db->setQuery($query);
        $db->execute();

        // 2. Insert new relationships if any were selected
        if (!empty($groups)) {
            $query = $db->getQuery(true)
                ->insert($db->quoteName('#__crm_lead_group_map'))
                ->columns([$db->quoteName('lead_id'), $db->quoteName('group_id')]);

            foreach ($groups as $groupId) {
                if(!empty($groupId)) {
                    $query->values($db->quote($leadId) . ', ' . $db->quote($groupId));
                }
            }

            $db->setQuery($query);

            try {
                $db->execute();
            } catch (\Exception $e) {
                $this->setError($e->getMessage());
                return false;
            }
        }

        return true;
    }
}
