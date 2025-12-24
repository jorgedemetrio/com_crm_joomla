<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

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
        $app   = JFactory::getApplication();
        $input = $app->input;
        $db    = JFactory::getDbo();
        $session = JFactory::getSession();

        $linkId = $input->getString('id');
        $leadId = $input->getString('lid');

        // Security: Validate Lead ID format (UUID) to prevent database pollution
        if (!empty($leadId) && !preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $leadId)) {
            $leadId = null;
        }

        $itemid = $input->getInt('Itemid', $this->getDefaultItemid());
        $tracking = $input->getString('tracking', $session->get('com_crm.tracking'));
        $sessionId = $session->getId();
        $ip = $input->server->getString('REMOTE_ADDR');
        $ipProxy = $input->server->getString('HTTP_X_FORWARDED_FOR');
        $userAgent = $input->server->getString('HTTP_USER_AGENT');

        if (empty($linkId)) {
            $this->redirectWithMessage(JText::_('COM_CRM_LINK_MISSING'), 'error', $itemid);
            return;
        }

        try {
            $query = $db->getQuery(true)
                ->select(
                    array(
                        $db->quoteName('id'),
                        $db->quoteName('campanha_id'),
                        $db->quoteName('url_destino')
                    )
                )
                ->from($db->quoteName('#__crm_campanha_links'))
                ->where($db->quoteName('id') . ' = ' . $db->quote($linkId))
                ->where($db->quoteName('state') . ' = 1');

            $db->setQuery($query);
            $link = $db->loadObject();

            if (!$link || empty($link->url_destino)) {
                $this->redirectWithMessage(JText::_('COM_CRM_LINK_NOT_FOUND'), 'error', $itemid);
                return;
            }

            // Security: Prevent Open Redirect / XSS by ensuring protocol is http or https
            if (!preg_match('#^https?://#i', $link->url_destino)) {
                $this->redirectWithMessage(JText::_('COM_CRM_LINK_INVALID_PROTOCOL'), 'error', $itemid);
                return;
            }

            $now = $db->quote(JFactory::getDate()->toSql());

            // Update aggregate link counters
            $db->setQuery(
                $db->getQuery(true)
                    ->update($db->quoteName('#__crm_campanha_links'))
                    ->set($db->quoteName('clicks_total') . ' = ' . $db->quoteName('clicks_total') . ' + 1')
                    ->set($db->quoteName('last_click') . ' = ' . $now)
                    ->where($db->quoteName('id') . ' = ' . $db->quote($linkId))
            )->execute();

            // Update aggregated link/lead counters if lead was provided
            if (!empty($leadId)) {
                $linkLeadQuery = $db->getQuery(true)
                    ->insert($db->quoteName('#__crm_campanha_link_lead'))
                    ->columns(
                        array(
                            $db->quoteName('link_id'),
                            $db->quoteName('lead_id'),
                            $db->quoteName('acessos'),
                            $db->quoteName('ultimo_acesso')
                        )
                    )
                    ->values(
                        $db->quote($linkId) . ', ' .
                        $db->quote($leadId) . ', 1, ' .
                        $now
                    );

                $linkLeadQuery .= ' ON DUPLICATE KEY UPDATE ' .
                    $db->quoteName('acessos') . ' = ' . $db->quoteName('acessos') . ' + 1, ' .
                    $db->quoteName('ultimo_acesso') . ' = ' . $now;

                $db->setQuery($linkLeadQuery)->execute();
            }

            // Log click details for auditing
            $db->setQuery(
                $db->getQuery(true)
                    ->insert($db->quoteName('#__crm_campanha_link_clicks'))
                    ->columns(
                        array(
                            $db->quoteName('link_id'),
                            $db->quoteName('lead_id'),
                            $db->quoteName('campanha_id'),
                            $db->quoteName('clicked_at'),
                            $db->quoteName('ip'),
                            $db->quoteName('ip_proxy'),
                            $db->quoteName('user_agent'),
                            $db->quoteName('tracking_id'),
                            $db->quoteName('session_id')
                        )
                    )
                    ->values(
                        $db->quote($linkId) . ', ' .
                        $db->quote($leadId) . ', ' .
                        $db->quote($link->campanha_id) . ', ' .
                        $now . ', ' .
                        $db->quote($ip) . ', ' .
                        $db->quote($ipProxy) . ', ' .
                        $db->quote($userAgent) . ', ' .
                        $db->quote($tracking) . ', ' .
                        $db->quote($sessionId)
                    )
            )->execute();

            $app->redirect($link->url_destino);
        } catch (Exception $e) {
            $this->redirectWithMessage(JText::_('COM_CRM_LINK_NOT_FOUND'), 'error', $itemid);
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
     * Redirect with a routed URL preserving Itemid.
     *
     * @param   string   $message  The message to enqueue.
     * @param   string   $type     Message type.
     * @param   integer  $itemid   The menu item id.
     */
    protected function redirectWithMessage($message, $type, $itemid)
    {
        $app = JFactory::getApplication();
        $app->enqueueMessage($message, $type);
        $app->redirect(JRoute::_('index.php?Itemid=' . (int) $itemid, false));
    }
}
