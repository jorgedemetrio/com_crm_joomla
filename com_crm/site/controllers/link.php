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
 * Link Controller
 *
 * @since  1.0.0
 */
class CrmControllerLink extends JControllerLegacy
{
    /**
     * Method to track a link click and redirect.
     * URL: index.php?option=com_crm&task=link.acesso&id=[LINK_ID]&lid=[LEAD_ID]
     */
    public function acesso()
    {
        // TODO: Implement click tracking logic
        $app = JFactory::getApplication();
        $app->enqueueMessage('Link access tracking not implemented yet.', 'notice');
        $app->redirect('index.php');
    }
}
