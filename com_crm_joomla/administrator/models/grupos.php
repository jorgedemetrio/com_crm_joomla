<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm_joomla
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Com_Crm_Joomla\Administrator\Model;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;

\defined('_JEXEC') or die;

/**
 * Grupos Model
 *
 * @since  1.0.0
 */
class GruposModel extends ListModel
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @since   1.0.0
     */
    public function __construct($config = [])
    {
        if (empty($config['filter_fields']))
        {
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
     * @return      string  An SQL query
     *
     * @since       1.0.0
     */
    protected function getListQuery()
    {
        // Create a new query object.
        $db    = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the fields
        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.nome, a.state, a.ordering, a.created, a.created_by'
            )
        );

        // From the lead_groups table
        $query->from($db->quoteName('#__crm_lead_groups', 'a'));

        // Filter by published state
        $state = $this->getState('filter.state');
        if (is_numeric($state))
        {
            $query->where('a.state = ' . (int) $state);
        }
        elseif ($state === '')
        {
            $query->where('(a.state IN (0, 1))');
        }

        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            if (stripos($search, 'id:') === 0)
            {
                $query->where('a.id = ' . $db->quote(substr($search, 3)));
            }
            else
            {
                $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true)) . '%');
                $query->where('a.nome LIKE ' . $search);
            }
        }

        // Add the list ordering clause.
        $orderCol  = $this->state->get('list.ordering', 'a.nome');
        $orderDirn = $this->state->get('list.direction', 'ASC');
        $query->order($db->escape($orderCol . ' ' . $orderDirn));

        return $query;
    }
}
