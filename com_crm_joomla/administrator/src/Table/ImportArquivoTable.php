<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Crm\Administrator\Table;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Uid\Uid;

/**
 * ImportArquivo Table class.
 */
class ImportArquivoTable extends Table
{
    /**
     * Constructor
     *
     * @param   DatabaseDriver  &$db  A database connector object
     */
    public function __construct(DatabaseDriver &$db)
    {
        parent::__construct('#__crm_import_arquivo', 'id', $db);
    }

    /**
     * Overloaded save method to generate UUID for new records.
     *
     * @param   array   $src             An associative array or object to bind to the table.
     * @param   string  $orderingFilter  An optional ordering filter.
     * @param   mixed   $ignore          An optional array or comma-separated list of properties to ignore while binding.
     *
     * @return  boolean  True on success.
     */
    public function save($src, $orderingFilter = '', $ignore = '')
    {
        if (empty($this->id)) {
            $this->id = Uid::create();
        }

        return parent::save($src, $orderingFilter, $ignore);
    }
}
