<?php
namespace Joomla\Component\Crm\Administrator\Model;

use Joomla\CMS\MVC\Model\ListModel;

class ImportArquivosModel extends ListModel
{
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'a.id',
                'nome', 'a.nome',
                'created', 'a.created',
                'created_by', 'a.created_by',
            ];
        }

        parent::__construct($config);
    }

    protected function getListQuery()
    {
        $dbDriver = $this->getDbo();
        $query    = $dbDriver->getQuery(true);

        $query->select('a.*')
            ->from($dbDriver->quoteName('#__crm_import_arquivo', 'a'));

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
