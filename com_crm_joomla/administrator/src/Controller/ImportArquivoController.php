<?php
namespace Joomla\Component\Crm\Administrator\Controller;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

class ImportArquivoController extends FormController
{
    /**
     * Method to display the processing view.
     *
     * @return  void
     */
    public function process()
    {
        $this->checkToken('get');

        // Get the ID of the import job from the request
        $cid = $this->input->get('cid', [], 'array');
        $id = (int) $cid[0];

        if (empty($id)) {
             $this->setRedirect(Route::_('index.php?option=com_crm&view=importarquivos', false));
             return;
        }

        // Set the layout and display the view
        $this->view->setLayout('process');
        $this->view->display();
    }

    /**
     * Method to perform the actual import.
     *
     * @return  void
     */
    public function doImport()
    {
        $this->checkToken();

        $id = $this->input->getInt('id');
        $map = $this->input->get('map', [], 'array');

        if (empty($id) || empty($map)) {
            $this->app->enqueueMessage('Invalid import request.', 'error');
            $this->setRedirect(Route::_('index.php?option=com_crm&view=importarquivos', false));
            return;
        }

        $model = $this->getModel();

        $result = $model->processImport($id, $map);

        if ($result) {
            $this->app->enqueueMessage(Text::sprintf('COM_CRM_IMPORT_SUCCESS_MESSAGE', $result->success, $result->fail));
        } else {
            $this->app->enqueueMessage(Text::_('COM_CRM_IMPORT_FAILED_MESSAGE') . ': ' . $model->getError(), 'error');
        }

        $this->setRedirect(Route::_('index.php?option=com_crm&view=importarquivos', false));
    }
}
