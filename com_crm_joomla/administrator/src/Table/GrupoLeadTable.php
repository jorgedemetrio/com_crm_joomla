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
 * GrupoLead Table class.
 */
class GrupoLeadTable extends Table
{
    /**
     * Constructor
     */
    public function __construct(DatabaseDriver &$db)
    {
        parent::__construct('#__crm_lead_groups', 'id', $db);
    }

    /**
     * Overloaded save method to generate UUID for new records.
     */
    public function save($src, $orderingFilter = '', $ignore = '')
    {
        if (empty($this->id)) {
            $this->id = \Joomla\CMS\Uid\Uid::create();
        }

        return parent::save($src, $orderingFilter, $ignore);
    }
}
