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
 * CampanhaLinks Model for the list view.
 *
 * @since  1.0.0
 */
class CampanhaLinksModel extends ListModel
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
                'nome', 'a.nome',
                'url_destino', 'a.url_destino',
                'state', 'a.state',
                'campanha_nome', 'c.nome',
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
                'a.id, a.nome, a.url_destino, a.state, a.campanha_id'
            )
        );
        $query->from($db->quoteName('#__crm_campanha_links', 'a'));

        // Join over the campaigns for the campaign name.
        $query->select($db->quoteName('c.nome', 'campanha_nome'))
            ->join('LEFT', $db->quoteName('#__crm_campanhas', 'c') . ' ON ' . $db->quoteName('a.campanha_id') . ' = ' . $db->quoteName('c.id'));

        // Filter by published state
        $state = $this->getState('filter.state');
        if (is_numeric($state)) {
            $query->where($db->quoteName('a.state') . ' = ' . (int) $state);
        }

        // Filter by search in name
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $search = $db->quote('%' . $db->escape($search, true) . '%');
            $query->where('(' . $db->quoteName('a.nome') . ' LIKE ' . $search . ' OR ' . $db->quoteName('a.url_destino') . ' LIKE ' . $search . ')');
        }

        // Filter by Campaign
        $campaignId = $this->getState('filter.campanha_id');
        if (is_numeric($campaignId) && $campaignId > 0) {
            $query->where($db->quoteName('a.campanha_id') . ' = ' . (int) $campaignId);
        }

        // Add the list ordering clause.
        $orderCol  = $this->state->get('list.ordering', 'a.nome');
        $orderDirn = $this->state->get('list.direction', 'ASC');
        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }
}
