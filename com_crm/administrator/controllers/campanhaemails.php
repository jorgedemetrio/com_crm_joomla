<?php
defined('_JEXEC') or die;

class CrmControllerCampanhaemails extends JControllerAdmin
{
    public function getModel($name = 'Campanhaemail', $prefix = 'CrmModel', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }
}
