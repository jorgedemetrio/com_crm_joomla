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
 * Leads Model for the list view.
 *
 * @since  1.0.0
 */
class LeadsModel extends ListModel
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     */
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'a.id',
                'razao_social', 'a.razao_social',
                'nome_fantasia', 'a.nome_fantasia',
                'email', 'a.email',
                'cidade', 'a.cidade',
                'estado', 'a.estado',
                'status', 'a.status',
                'origem', 'a.origem',
                'state', 'a.state',
            ];
        }

        parent::__construct($config);
    }

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return  \Joomla\Database\Query
     */
    protected function getListQuery()
    {
        $db    = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.razao_social, a.nome_fantasia, a.email, a.telefone1, a.cidade, a.estado, a.status, a.origem, a.state'
            )
        );
        $query->from($db->quoteName('#__crm_leads', 'a'));

        // Filter by published state
        $state = $this->getState('filter.state');
        if (is_numeric($state)) {
            $query->where($db->quoteName('a.state') . ' = ' . (int) $state);
        } elseif ($state === '') {
            $query->where($db->quoteName('a.state') . ' IN (0, 1)');
        }

        // Filter by search in name or email
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $search = $db->quote('%' . $db->escape($search, true) . '%');
            $query->where(
                '(' . $db->quoteName('a.razao_social') . ' LIKE ' . $search . ' OR '
                . $db->quoteName('a.nome_fantasia') . ' LIKE ' . $search . ' OR '
                . $db->quoteName('a.email') . ' LIKE ' . $search . ')'
            );
        }

        // Filter by Origin
        $origin = $this->getState('filter.origem');
        if (!empty($origin)) {
            $query->where($db->quoteName('a.origem') . ' = ' . $db->quote($origin));
        }

        // Filter by Status
        $status = $this->getState('filter.status');
        if (!empty($status)) {
            $query->where($db->quoteName('a.status') . ' = ' . $db->quote($status));
        }

        // Add the list ordering clause.
        $orderCol  = $this->state->get('list.ordering', 'a.razao_social');
        $orderDirn = $this->state->get('list.direction', 'ASC');
        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }
}
