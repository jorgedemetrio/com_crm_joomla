<?php
defined('_JEXEC') or die;

class CrmControllerCampanhasmslist extends JControllerAdmin
{
    public function getModel($name = 'Campanhasms', $prefix = 'CrmModel', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }
}
