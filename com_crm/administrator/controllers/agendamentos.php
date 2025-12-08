<?php
defined('_JEXEC') or die;

class CrmControllerAgendamentos extends JControllerAdmin
{
    public function getModel($name = 'Agendamento', $prefix = 'CrmModel', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }
}
