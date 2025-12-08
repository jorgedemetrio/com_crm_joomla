<?php
defined('_JEXEC') or die;

class CrmControllerImportwebs extends JControllerAdmin
{
    public function getModel($name = 'Importweb', $prefix = 'CrmModel', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }
}
