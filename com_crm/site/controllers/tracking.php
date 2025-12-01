<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Tracking Controller
 *
 * @since  1.0.0
 */
class CrmControllerTracking extends JControllerLegacy
{
    /**
     * Method to track an email open.
     * URL: index.php?option=com_crm&task=tracking.open&eid=[ENVIO_ID]
     */
    public function open()
    {
        // TODO: Implement open tracking logic

        // Output a 1x1 transparent GIF
        $app = JFactory::getApplication();
        $app->setHeader('Content-Type', 'image/gif');
        echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        $app->close();
    }
}
