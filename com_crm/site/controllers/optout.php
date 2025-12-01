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
 * Optout Controller
 *
 * @since  1.0.0
 */
class CrmControllerOptout extends JControllerLegacy
{
    /**
     * Method to handle user unsubscribe.
     * URL: index.php?option=com_crm&task=optout.unsubscribe&email=[EMAIL]
     */
    public function unsubscribe()
    {
        // TODO: Implement unsubscribe logic
        $app = JFactory::getApplication();
        $app->enqueueMessage('Unsubscribe feature not implemented yet.', 'notice');
        $app->redirect('index.php');
    }
}
