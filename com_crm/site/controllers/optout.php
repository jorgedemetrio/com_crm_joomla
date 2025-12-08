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
        $app    = JFactory::getApplication();
        $input  = $app->input;
        $db     = JFactory::getDbo();
        $method = $input->getMethod();

        if ($method === 'POST' && !JSession::checkToken()) {
            jexit(JText::_('JINVALID_TOKEN'));
        }

        $email      = trim($input->getString('email'));
        $scope      = $input->getWord('scope', 'global');
        $campanhaId = $input->getString('campanha_id');
        $reason     = $input->getString('reason');
        $itemid     = $input->getInt('Itemid', $this->getDefaultItemid());

        if (empty($email)) {
            $this->redirectWithMessage(JText::_('COM_CRM_OPTOUT_EMAIL_REQUIRED'), 'error', $itemid);
            return;
        }

        if ($scope !== 'campanha') {
            $scope = 'global';
            $campanhaId = null;
        }

        if ($scope === 'campanha' && empty($campanhaId)) {
            $scope = 'global';
        }

        $emailHash = hash('sha256', strtolower($email));
        $now       = $db->quote(JFactory::getDate()->toSql());

        try {
            $query = $db->getQuery(true)
                ->insert($db->quoteName('#__crm_email_optout'))
                ->columns(
                    array(
                        $db->quoteName('email_hash'),
                        $db->quoteName('scope'),
                        $db->quoteName('campanha_id'),
                        $db->quoteName('reason'),
                        $db->quoteName('created')
                    )
                )
                ->values(
                    $db->quote($emailHash) . ', ' .
                    $db->quote($scope) . ', ' .
                    $db->quote($campanhaId) . ', ' .
                    $db->quote($reason) . ', ' .
                    $now
                );

            $query .= ' ON DUPLICATE KEY UPDATE ' .
                $db->quoteName('reason') . ' = ' . $db->quote($reason) . ', ' .
                $db->quoteName('created') . ' = ' . $now;

            $db->setQuery($query)->execute();

            $this->redirectWithMessage(JText::_('COM_CRM_OPTOUT_SUCCESS'), 'message', $itemid);
        } catch (Exception $e) {
            $this->redirectWithMessage(JText::_('COM_CRM_OPTOUT_ERROR'), 'error', $itemid);
        }
    }

    /**
     * Get the current menu item id.
     *
     * @return  integer
     */
    protected function getDefaultItemid()
    {
        $menu = JFactory::getApplication()->getMenu()->getActive();

        return $menu ? (int) $menu->id : 0;
    }

    /**
     * Redirect with routed Itemid.
     *
     * @param   string   $message  Message to enqueue.
     * @param   string   $type     Message type.
     * @param   integer  $itemid   Menu item id to preserve.
     */
    protected function redirectWithMessage($message, $type, $itemid)
    {
        $app = JFactory::getApplication();
        $app->enqueueMessage($message, $type);
        $app->redirect(JRoute::_('index.php?Itemid=' . (int) $itemid, false));
    }
}
