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
        // 1. Get Link ID and Lead ID from input
        // 2. Load the link record to get the destination URL
        // 3. Log the click in #__crm_campanha_link_clicks
        // 4. Update the aggregate count in #__crm_campanha_link_lead
        // 5. Redirect to the destination URL

        $app = \Joomla\CMS\Factory::getApplication();
        $app->enqueueMessage('Link access tracking not implemented yet.', 'notice');
        $app->redirect('index.php');
    }
}
