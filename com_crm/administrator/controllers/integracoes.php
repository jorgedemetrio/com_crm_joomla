<?php
defined('_JEXEC') or die;

class CrmControllerIntegracoes extends JControllerAdmin
{
    public function getModel($name = 'Integracao', $prefix = 'CrmModel', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }
}
