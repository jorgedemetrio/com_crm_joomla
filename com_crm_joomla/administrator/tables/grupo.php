<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm_joomla
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Com_Crm_Joomla\Administrator\Table;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

\defined('_JEXEC') or die;

/**
 * Grupo Table class.
 *
 * @since  1.0.0
 */
class GrupoTable extends Table
{
    /**
     * Constructor
     *
     * @param   DatabaseDriver  $db  A database connector object
     */
    public function __construct(DatabaseDriver &$db)
    {
        parent::__construct('#__crm_lead_groups', 'id', $db);
        $this->setColumnAlias('published', 'state');
    }

    /**
	 * Overloaded check function to ensure data integrity.
	 *
	 * @return bool True if the data is valid, false otherwise.
	 */
	public function check()
	{
		// Check for a valid name
		if (trim($this->nome) === '')
		{
			$this->setError(Text::_('COM_CRM_JOOMLA_GRUPO_ERROR_NOME_REQUIRED'));
			return false;
		}

		// Check for unique name
		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->select($this->getKeyName())
			->from($this->_tbl)
			->where($db->quoteName('nome') . ' = ' . $db->quote($this->nome));

		// If it's an existing record, exclude it from the check
		if ($this->id)
		{
			$query->where($db->quoteName('id') . ' != ' . $db->quote($this->id));
		}

		$db->setQuery($query);

		if ($db->loadResult())
		{
			$this->setError(Text::_('COM_CRM_JOOMLA_GRUPO_ERROR_NOME_UNIQUE'));
			return false;
		}

		return parent::check();
	}

    /**
	 * Method to bind an array to the table.
	 *
	 * @param   array   $array   The array to bind to the table
	 * @param   string  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  boolean  True on success.
	 */
	public function bind($array, $ignore = '')
	{
        // Generate a new UUID if the id is empty
        if (empty($array['id']))
        {
            $array['id'] = \Joomla\CMS\Uuid\UuidFactory::getFactory()->create();
        }

		return parent::bind($array, $ignore);
	}
}
