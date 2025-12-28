<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once dirname(__FILE__) . '/crm.php';

/**
 * ImportArquivo Table class.
 */
class CrmTableImportArquivo extends CrmTable
{
    /**
     * Constructor
     *
     * @param   DatabaseDriver  &$db  A database connector object
     */
    public function __construct(&$db)
    {
        parent::__construct('#__crm_import_arquivo', 'id', $db);
    }



    /**
     * Overloaded store method to handle UUIDs.
     *
     * @param   boolean  $updateNulls  True to update fields even if they are null.
     *
     * @return  boolean  True on success.
     */
    public function store($updateNulls = false)
    {
        $k = $this->_tbl_key;

        if (empty($this->$k)) {
            $this->$k = $this->generateUuid();
            // Force insert for new UUID
            $ret = $this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);
        } else {
            // Check if record exists
            $query = $this->_db->getQuery(true)
                ->select('COUNT(*)')
                ->from($this->_tbl)
                ->where($k . ' = ' . $this->_db->quote($this->$k));
            $this->_db->setQuery($query);
            if ($this->_db->loadResult()) {
                $ret = $this->_db->updateObject($this->_tbl, $this, $this->_tbl_key, $updateNulls);
            } else {
                $ret = $this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);
            }
        }

        if (!$ret) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        return true;
    }

}
