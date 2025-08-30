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
use Joomla\CMS\Factory;

/**
 * LinksCampanha Model
 */
class LinksCampanhaModel extends ListModel
{
    /**
     * Constructor.
     */
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'a.id',
                'nome', 'a.nome',
                'campanha_id', 'a.campanha_id',
                'published', 'a.state',
            ];
        }

        parent::__construct($config);
    }

    /**
     * Method to build an SQL query to load the list data.
     */
    protected function getListQuery()
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true);

        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.nome, a.url_destino, a.clicks_total, a.state AS published, c.nome AS campanha_nome'
            )
        );
        $query->from($db->quoteName('#__crm_campanha_links', 'a'));

        // Join over for campaign name
        $query->join('LEFT', $db->quoteName('#__crm_campanhas', 'c') . ' ON (' . $db->quoteName('c.id') . ' = ' . $db->quoteName('a.campanha_id') . ')');


        // Filter by search in name
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $like = $db->quote('%' . $search . '%');
            $query->where('a.nome LIKE ' . $like);
        }

        // Add the list ordering clause.
        $orderCol  = $this->state->get('list.ordering', 'a.nome');
        $orderDirn = $this->state->get('list.direction', 'asc');
        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }
}
