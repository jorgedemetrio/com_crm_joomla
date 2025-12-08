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
        $app   = JFactory::getApplication();
        $input = $app->input;
        $db    = JFactory::getDbo();

        $linkId = $input->getString('id');
        $leadId = $input->getString('lid');
        $itemid = $input->getInt('Itemid', $this->getDefaultItemid());

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
                            $db->quoteName('user_agent')
                        )
                    )
                    ->values(
                        $db->quote($linkId) . ', ' .
                        $db->quote($leadId) . ', ' .
                        $db->quote($link->campanha_id) . ', ' .
                        $now . ', ' .
                        $db->quote($input->server->getString('REMOTE_ADDR')) . ', ' .
                        $db->quote($input->server->getString('HTTP_X_FORWARDED_FOR')) . ', ' .
                        $db->quote($input->server->getString('HTTP_USER_AGENT'))
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
