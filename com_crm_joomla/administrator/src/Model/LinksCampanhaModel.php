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
 * LinksCampanha Model
 */
class LinksCampanhaModel extends ListModel
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
                'campanha_id', 'a.campanha_id',
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
                'a.id, a.nome, a.url_destino, a.clicks_total, a.state AS published, c.nome AS campanha_nome'
            )
        );
        $query->from($dbDriver->quoteName('#__crm_campanha_links', 'a'));

        // Join over for campaign name
        $query->join('LEFT', $dbDriver->quoteName('#__crm_campanhas', 'c') . ' ON (' . $dbDriver->quoteName('c.id') . ' = ' . $dbDriver->quoteName('a.campanha_id') . ')');


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
