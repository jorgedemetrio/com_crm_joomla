<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * LinkCampanha Table class.
 */
class CrmTableLinkCampanha extends JTable
{
    /**
     * Constructor
     */
    public function __construct(&$db)
    {
        parent::__construct('#__crm_campanha_links', 'id', $db);
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

    /**
     * Generate a UUID v4.
     *
     * @return  string
     */
    protected function generateUuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
