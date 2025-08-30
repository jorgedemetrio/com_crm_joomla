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
 * Leads Model
 */
class LeadsModel extends ListModel
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
                'razao_social', 'a.razao_social',
                'nome_fantasia', 'a.nome_fantasia',
                'email', 'a.email',
                'telefone1', 'a.telefone1',
                'cidade', 'a.cidade',
                'estado', 'a.estado',
                'status', 'a.status',
                'origem', 'a.origem',
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
                'a.id, a.razao_social, a.nome_fantasia, a.email, a.telefone1, a.cidade, a.estado, a.status, a.state AS published'
            )
        );
        $query->from($dbDriver->quoteName('#__crm_leads', 'a'));

        // Filter by search in name or email
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $like = $dbDriver->quote('%' . $search . '%');
            $query->where('a.razao_social LIKE ' . $like . ' OR a.nome_fantasia LIKE ' . $like . ' OR a.email LIKE ' . $like);
        }

        // Filter by status
        $status = $this->getState('filter.status');
        if (!empty($status)) {
            $query->where('a.status = ' . $dbDriver->quote($status));
        }

        // Filter by state (published)
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $query->where('a.state = ' . (int) $published);
        } elseif ($published === '') {
            $query->where('(a.state IN (0, 1))');
        }

        // Add the list ordering clause.
        $orderCol  = $this->state->get('list.ordering', 'a.razao_social');
        $orderDirn = $this->state->get('list.direction', 'asc');
        $query->order($dbDriver->escape($orderCol) . ' ' . $dbDriver->escape($orderDirn));

        return $query;
    }
}
