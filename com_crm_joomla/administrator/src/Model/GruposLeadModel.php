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

/**
 * GruposLead Model
 */
class GruposLeadModel extends ListModel
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
                'published', 'a.state',
            ];
        }

        parent::__construct($config);
    }

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return  \Joomla\Database\Query
     *
     * @since   1.0.0
     */
    protected function getListQuery()
    {
        $dbDriver = $this->getDbo();
        $query    = $dbDriver->getQuery(true);

        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.nome, a.state AS published, a.created_by'
            )
        );
        $query->from($dbDriver->quoteName('#__crm_lead_groups', 'a'));

        // Filter by search in name
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $like = $dbDriver->quote('%' . $search . '%');
            $query->where('a.nome LIKE ' . $like);
        }

        // Add the list ordering clause.
        $orderCol  = $this->state->get('list.ordering', 'a.nome');
        $orderDirn = $this->state->get('list.direction', 'asc');
        $query->order($dbDriver->escape($orderCol) . ' ' . $dbDriver->escape($orderDirn));

        return $query;
    }
}
