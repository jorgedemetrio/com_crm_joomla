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
 * ImportArquivo Table class.
 *
 * @since  1.0.0
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
}
