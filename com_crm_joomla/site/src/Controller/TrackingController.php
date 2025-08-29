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
 * Tracking Controller
 *
 * @since  1.0.0
 */
class TrackingController extends BaseController
{
    /**
     * Method to track an email open.
     * URL: index.php?option=com_crm&task=tracking.open&eid=[ENVIO_ID]
     */
    public function open()
    {
        // TODO: Implement open tracking logic
        // 1. Get Envio ID from input
        // 2. Log the open event in #__crm_email_opens

        // 3. Output a 1x1 transparent GIF
        $app = \Joomla\CMS\Factory::getApplication();
        $app->setHeader('Content-Type', 'image/gif');
        // A 1x1 transparent GIF
        echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        $app->close();
    }
}
