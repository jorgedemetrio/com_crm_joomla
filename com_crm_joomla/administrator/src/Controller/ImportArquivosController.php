<?php
namespace Joomla\Component\Crm\Administrator\Controller;

use Joomla\CMS\MVC\Controller\AdminController;

class ImportArquivosController extends AdminController
{
    protected $defaultView = 'importarquivos';

    protected function getModelPrefix()
    {
        return 'ImportArquivo';
    }
}
