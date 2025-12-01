<?php

class CrmControllerImportArquivo extends JControllerForm
{
    /**
     * Method to display the processing view.
     *
     * @return  void
     */

defined('_JEXEC') or die;
    public function process()
    {
        // Check for request forgeries
        $this->checkToken('get');

        // Get the ID of the import job from the request
        $cid = $this->input->get('cid', [], 'array');

        if (empty($cid[0])) {
             $this->setRedirect(JRoute::_('index.php?option=com_crm&view=importarquivos', false));
             return;
        }

        // We are editing that record
        $this->input->set('id', (int) $cid[0]);

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
            $this->setRedirect(JRoute::_('index.php?option=com_crm&view=importarquivos', false));
            return;
        }

        $model = $this->getModel();

        $result = $model->processImport($id, $map);

        if ($result) {
            $this->app->enqueueMessage(JText::sprintf('COM_CRM_IMPORT_SUCCESS_MESSAGE', $result->success, $result->fail));
        } else {
            $this->app->enqueueMessage(JText::_('COM_CRM_IMPORT_FAILED_MESSAGE') . ': ' . $model->getError(), 'error');
        }

        $this->setRedirect(JRoute::_('index.php?option=com_crm&view=importarquivos', false));
    }
}
