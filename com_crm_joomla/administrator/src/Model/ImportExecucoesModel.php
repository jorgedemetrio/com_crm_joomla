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
 * ImportExecucoes Model
 *
 * @since  1.0.0
 */
class ImportExecucoesModel extends ListModel
{
    /**
     * Constructor.
     */
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'a.id',
                'status', 'a.status',
                'tipo', 'a.tipo',
                'referencia_id', 'a.referencia_id',
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
                'a.id, a.tipo, a.referencia_id, a.status, a.linhas_total, a.linhas_sucesso, a.linhas_falha, a.finished_at, a.log'
            )
        );
        $query->from($db->quoteName('#__crm_import_execucoes', 'a'));

        // Filter by the parent import job ID
        $referenciaId = $this->getState('filter.referencia_id');
        if ($referenciaId) {
            $query->where('a.referencia_id = ' . $db->quote($referenciaId));
        }

        // Join with import_arquivo to get the name
        $query->select('ia.nome AS import_nome')
              ->join('LEFT', $db->quoteName('#__crm_import_arquivo', 'ia') . ' ON ' . $db->quoteName('ia.id') . ' = ' . $db->quoteName('a.referencia_id'));


        // Add the list ordering clause.
        $orderCol  = $this->state->get('list.ordering', 'a.finished_at');
        $orderDirn = $this->state->get('list.direction', 'desc');
        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }
}
