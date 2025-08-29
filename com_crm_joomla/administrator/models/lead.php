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
use Joomla\Database\DatabaseDriver;

/**
 * Lead Model
 *
 * @since  1.0.0
 */
class LeadModel extends AdminModel
{
    /**
     * Method to get the record form.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  \Joomla\CMS\Form\Form|false  A Form object on success, false on failure
     * @since   1.0.0
     */
    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm('com_crm.lead', 'lead', ['control' => 'jform', 'load_data' => $loadData]);
        if (empty($form)) {
            return false;
        }
        return $form;
    }

    /**
     * Method to get the data that should be injected into the form.
     *
     * @return  mixed  The data for the form.
     * @since   1.0.0
     */
    protected function loadFormData()
    {
        $data = Factory::getApplication()->getUserState('com_crm.edit.lead.data', null);

        if (empty($data)) {
            $data = $this->getItem();

            // Load associated groups
            if (!empty($data->id)) {
                $db = Factory::getDbo();
                $query = $db->getQuery(true)
                    ->select('group_id')
                    ->from($db->quoteName('#__crm_lead_group_map'))
                    ->where($db->quoteName('lead_id') . ' = ' . $db->quote($data->id));
                $db->setQuery($query);
                $data->groups = $db->loadColumn();
            }
        }

        return $data;
    }

    /**
     * Method to save the form data.
     *
     * @param   array  $data  The form data.
     *
     * @return  boolean  True on success, false on failure.
     * @since   1.0.0
     */
    public function save($data)
    {
        $db    = $this->getDbo();
        $table = $this->getTable();
        $key   = $table->getKeyName();
        $pk    = (!empty($data[$key])) ? $data[$key] : $this->getState($this->getName() . '.id');

        try {
            if ($pk) {
                $table->load($pk);
            }

            if (!$table->bind($data)) {
                $this->setError($table->getError());
                return false;
            }

            if (!$table->check()) {
                $this->setError($table->getError());
                return false;
            }

            if (!$table->store()) {
                $this->setError($table->getError());
                return false;
            }

            $pk = $table->$key;

            $groups = $data['groups'] ?? [];

            $query = $db->getQuery(true)
                ->delete($db->quoteName('#__crm_lead_group_map'))
                ->where($db->quoteName('lead_id') . ' = ' . $db->quote($pk));
            $db->setQuery($query)->execute();

            if (!empty($groups)) {
                $query = $db->getQuery(true)
                    ->insert($db->quoteName('#__crm_lead_group_map'))
                    ->columns($db->quoteName(['lead_id', 'group_id']));

                foreach ($groups as $groupId) {
                    if (!empty($groupId)) {
                        $query->values($db->quote($pk) . ', ' . $db->quote($groupId));
                    }
                }
                $db->setQuery($query)->execute();
            }
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }

        $this->setState($this->getName() . '.id', $pk);

        return true;
    }
}
