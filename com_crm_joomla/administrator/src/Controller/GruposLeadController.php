<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Crm\Administrator\Controller;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;

/**
 * GruposLead Controller
 */
class GruposLeadController extends BaseController
{
    /**
     * The default view for the display task.
     *
     * @var string
     */
    protected $defaultView = 'gruposlead';

    /**
     * Method to delete a list of records.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function delete()
    {
        // Check for request forgeries
        $this->checkToken();

        $user = $this->app->getIdentity();

        if (!$user->authorise('core.delete', 'com_crm.grupolead')) {
            $this->app->enqueueMessage(Text::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'), 'error');
            $this->setRedirect('index.php?option=com_crm&view=gruposlead');

            return;
        }

        // Get items to remove from the request.
        $cid = $this->input->get('cid', [], 'array');

        if (!is_array($cid) || count($cid) < 1) {
            $this->app->enqueueMessage(Text::_('JGLOBAL_SELECT_AN_ITEM_TO_DELETE'), 'error');
            $this->setRedirect('index.php?option=com_crm&view=gruposlead');

            return;
        }

        // Get the model.
        $model = $this->getModel();

        // Remove the items.
        if ($model->delete($cid)) {
            $this->app->enqueueMessage(Text::plural('COM_CRM_N_GRUPOSLEAD_DELETED', count($cid)));
        } else {
            $this->app->enqueueMessage($model->getError(), 'error');
        }

        $this->setRedirect('index.php?option=com_crm&view=gruposlead');
    }

    /**
     * Method to publish a list of records.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function publish()
    {
        // Check for request forgeries
        $this->checkToken();

        $user = $this->app->getIdentity();

        if (!$user->authorise('core.edit.state', 'com_crm.grupolead')) {
            $this->app->enqueueMessage(Text::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), 'error');
            $this->setRedirect('index.php?option=com_crm&view=gruposlead');

            return;
        }

        // Get items to publish from the request.
        $cid = $this->input->get('cid', [], 'array');
        $data = ['publish' => 1, 'unpublish' => 0, 'archive' => 2, 'trash' => -2, 'report' => -3];
        $task = $this->getTask();
        $value = ArrayHelper::getValue($data, $task, 0, 'int');


        if (empty($cid)) {
            $this->app->enqueueMessage(Text::_('JGLOBAL_SELECT_AN_ITEM_TO_PUBLISH'), 'error');
            $this->setRedirect('index.php?option=com_crm&view=gruposlead');
            return;
        }

        // Get the model.
        $model = $this->getModel();

        // Publish the items.
        if (!$model->publish($cid, $value)) {
            $this->app->enqueueMessage($model->getError(), 'error');
        }

        $this->setRedirect('index.php?option=com_crm&view=gruposlead');
    }
}
