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

/**
 * ImportExecucao Table class.
 *
 * @since  1.0.0
 */
class ImportExecucaoTable extends Table
{
    /**
     * Constructor
     */
    public function __construct(DatabaseDriver &$db)
    {
        parent::__construct('#__crm_import_execucoes', 'id', $db);
    }
}
