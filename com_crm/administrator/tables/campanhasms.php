<?php
defined('_JEXEC') or die;

class CrmTableCampanhasms extends JTable
{
    public function __construct(&$db)
    {
        parent::__construct('#__crm_campanha_sms', 'id', $db);
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
