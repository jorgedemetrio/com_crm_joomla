<?php
defined('_JEXEC') or die;

class CrmModelIntegracoes extends JModelList
{
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'a.id',
                'published', 'a.state',
                'provider', 'a.provider',
                'ativo', 'a.ativo',
                'params_json', 'a.params_json'
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

        parent::populateState('a.provider', 'asc');
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
        $query->from($db->quoteName('#__crm_integracoes', 'a'));

        // Search
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . $db->quote(substr($search, 3)));
            } else {
                $search = $db->quote('%' . $search . '%');
                $query->where('a.provider LIKE ' . $search);
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
        $orderCol  = $this->state->get('list.ordering', 'a.provider');
        $orderDirn = $this->state->get('list.direction', 'asc');
        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }
}
