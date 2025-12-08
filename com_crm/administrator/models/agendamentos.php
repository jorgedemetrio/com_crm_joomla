<?php
defined('_JEXEC') or die;

class CrmModelAgendamentos extends JModelList
{
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'a.id',
                'published', 'a.state',
                'campanha_id', 'a.campanha_id',
                'tipo', 'a.tipo',
                'inicio_em', 'a.inicio_em',
                'status_job', 'a.status_job'
            ];
        }
        parent::__construct($config);
    }

    protected function populateState($ordering = null, $direction = null)
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
        $this->setState('filter.published', $published);

        parent::populateState('a.campanha_id', 'asc');
    }

    protected function getListQuery()
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select(
            $this->getState(
                'list.select',
                'a.*, a.state AS published'
            )
        );
        $query->from($db->quoteName('#__crm_agendamentos', 'a'));

        // Search
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . $db->quote(substr($search, 3)));
            } else {
                $search = $db->quote('%' . $search . '%');
                $query->where('a.campanha_id LIKE ' . $search);
            }
        }

        // Published
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $query->where('a.state = ' . (int) $published);
        } elseif ($published === '') {
            $query->where('(a.state IN (0, 1))');
        }

        // Ordering
        $orderCol  = $this->state->get('list.ordering', 'a.campanha_id');
        $orderDirn = $this->state->get('list.direction', 'asc');
        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }
}
