<?php
defined('_JEXEC') or die;

require_once dirname(__FILE__) . '/crm.php';

class CrmTableImportweb extends CrmTable
{
    public function __construct(&$db)
    {
        parent::__construct('#__crm_import_web', 'id', $db);
    }

    public function store($updateNulls = false)
    {
        $k = $this->_tbl_key;

        if (empty($this->$k)) {
            $this->$k = $this->generateUuid();
            $ret = $this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);
        } else {
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
