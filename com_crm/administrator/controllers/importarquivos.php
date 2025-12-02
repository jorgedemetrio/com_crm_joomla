<?php

defined('_JEXEC') or die;

class CrmControllerImportArquivos extends JControllerAdmin
{
    protected $defaultView = 'importarquivos';

    protected function getModelPrefix()
    {
        return 'ImportArquivo';
    }
}
