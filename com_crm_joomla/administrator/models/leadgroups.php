<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Crm\Administrator\Model;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\DatabaseDriver;

/**
 * Leadgroups Model for the list view.
 *
 * @since  1.0.0
 */
class LeadgroupsModel extends ListModel
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @see     \Joomla\CMS\MVC\Model\BaseDatabaseModel
     * @since   1.0.0
     */
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'a.id',
                'nome', 'a.nome',
                'state', 'a.state',
                'ordering', 'a.ordering',
                'created', 'a.created',
                'created_by', 'a.created_by',
            ];
        }

        parent::__construct($config);
    }

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return  \Joomla\Database\Query
     * @since   1.0.0
     */
    protected function getListQuery()
    {
        // Get a db connection.
        $db = $this->getDbo();

        // Create a new query object.
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.nome, a.state, a.ordering, a.created, a.created_by'
            )
        );
        $query->from($db->quoteName('#__crm_lead_groups', 'a'));

        // Filter by published state
        $state = $this->getState('filter.state');
        if (is_numeric($state)) {
            $query->where($db->quoteName('a.state') . ' = ' . (int) $state);
        } elseif ($state === '') {
            $query->where($db->quoteName('a.state') . ' IN (0, 1)');
        }

        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $search = $db->quote('%' . $db->escape($search, true) . '%');
            $query->where($db->quoteName('a.nome') . ' LIKE ' . $search);
        }

        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering', 'a.nome');
        $orderDirn = $this->state->get('list.direction', 'ASC');
        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }
}
