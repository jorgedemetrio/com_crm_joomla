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
 * Leads Model
 */
class CrmModelLeads extends JModelList
{
    /**
     * Constructor.
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
     * Method to auto-populate the model state.
     *
     * @param   string  $ordering   An optional ordering field.
     * @param   string  $direction  An optional direction (asc or desc).
     *
     * @return  void
     */
    protected function populateState($ordering = null, $direction = null)
    {
        $published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
        $this->setState('filter.published', $published);

        $status = $this->getUserStateFromRequest($this->context . '.filter.status', 'filter_status');
        $this->setState('filter.status', $status);

        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        parent::populateState('a.razao_social', 'asc');
    }

/**
     * Method to build an SQL query to load the list data.
     */
    protected function getListQuery()
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.razao_social, a.nome_fantasia, a.email, a.telefone1, a.cidade, a.estado, a.status, a.state AS published'
            )
        );
        $query->from($db->quoteName('#__crm_leads', 'a'));

        // Filter by search in name or email
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $like = $db->quote('%' . $search . '%');
            $query->where('a.razao_social LIKE ' . $like . ' OR a.nome_fantasia LIKE ' . $like . ' OR a.email LIKE ' . $like);
        }

        // Filter by status
        $status = $this->getState('filter.status');
        if (!empty($status)) {
            $query->where('a.status = ' . $db->quote($status));
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
        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }
}
