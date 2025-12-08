<?php
defined('_JEXEC') or die;

class CrmControllerExportprofiles extends JControllerAdmin
{
    public function getModel($name = 'Exportprofile', $prefix = 'CrmModel', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }
}
