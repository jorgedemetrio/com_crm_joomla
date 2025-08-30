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
 * Link Controller
 *
 * @since  1.0.0
 */
class LinkController extends BaseController
{
    /**
     * Method to track a link click and redirect.
     * URL: index.php?option=com_crm&task=link.acesso&id=[LINK_ID]&lid=[LEAD_ID]
     */
    public function acesso()
    {
        // TODO: Implement click tracking logic
        $app = \Joomla\CMS\Factory::getApplication();
        $app->enqueueMessage('Link access tracking not implemented yet.', 'notice');
        $app->redirect('index.php');
    }
}
