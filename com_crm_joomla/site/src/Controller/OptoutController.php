<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Crm\Site\Controller;

use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Optout Controller
 *
 * @since  1.0.0
 */
class OptoutController extends BaseController
{
    /**
     * Method to handle user unsubscribe.
     * URL: index.php?option=com_crm&task=optout.unsubscribe&email=[EMAIL]
     */
    public function unsubscribe()
    {
        // TODO: Implement unsubscribe logic
        $app = \Joomla\CMS\Factory::getApplication();
        $app->enqueueMessage('Unsubscribe feature not implemented yet.', 'notice');
        $app->redirect('index.php');
    }
}
