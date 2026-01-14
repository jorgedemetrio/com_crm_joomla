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
        $app   = JFactory::getApplication();
        $input = $app->input;
        $db    = JFactory::getDbo();
        $session = JFactory::getSession();

        $envioId    = $input->getInt('eid');
        $campanhaId = $input->getString('cid');
        $now        = $db->quote(JFactory::getDate()->toSql());

        // Security: Validate Campanha ID format (UUID) to prevent database pollution
        if (!empty($campanhaId) && !preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $campanhaId)) {
            $campanhaId = null;
        }

        // Security: Truncate to database limits and validate UUID to prevent errors/DoS
        $tracking   = $input->getString('tracking', $session->get('com_crm.tracking'));
        if (!empty($tracking) && !preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $tracking)) {
            $tracking = '';
        }
        $tracking = substr($tracking, 0, 36);

        $sessionId  = $session->getId();
        $ip         = substr($input->server->getString('REMOTE_ADDR'), 0, 45);
        $ipProxy    = substr($input->server->getString('HTTP_X_FORWARDED_FOR'), 0, 45);
        $userAgent  = substr($input->server->getString('HTTP_USER_AGENT'), 0, 255);

        try {
            // Security: Prevent IDOR by requiring validation that envio_id belongs to campanha_id.
            // If campanha_id is not provided, we DO NOT look it up automatically, to force the attacker to know the UUID.
            // If campanha_id IS provided, we verify the match.

            // Note: This change effectively requires 'cid' for tracking to work.
            // Legacy emails without 'cid' will no longer track opens. This is a necessary security trade-off
            // to prevent mass enumeration of tracking events.

            $isValid = false;

            if ($envioId && $campanhaId) {
                // Verify that this envio_id actually belongs to the provided campanha_id AND the email is in valid state
                $query = $db->getQuery(true)
                    ->select($db->quoteName('id'))
                    ->from($db->quoteName('#__crm_email_envios'))
                    ->where($db->quoteName('id') . ' = ' . (int) $envioId)
                    ->where($db->quoteName('campanha_id') . ' = ' . $db->quote($campanhaId))
                    // Ensure we only track valid/sent emails (state=1)
                    ->where($db->quoteName('state') . ' = 1');

                $db->setQuery($query);
                if ($db->loadResult()) {
                    $isValid = true;
                }
            }

            if ($isValid) {
                $db->setQuery(
                    $db->getQuery(true)
                        ->insert($db->quoteName('#__crm_email_opens'))
                        ->columns(
                            array(
                                $db->quoteName('campanha_id'),
                                $db->quoteName('envio_id'),
                                $db->quoteName('opened_at'),
                                $db->quoteName('ip'),
                                $db->quoteName('ip_proxy'),
                                $db->quoteName('user_agent'),
                                $db->quoteName('tracking_id'),
                                $db->quoteName('session_id')
                            )
                        )
                        ->values(
                            $db->quote($campanhaId) . ', ' .
                            (int) $envioId . ', ' .
                            $now . ', ' .
                            $db->quote($ip) . ', ' .
                            $db->quote($ipProxy) . ', ' .
                            $db->quote($userAgent) . ', ' .
                            $db->quote($tracking) . ', ' .
                            $db->quote($sessionId)
                        )
                )->execute();

                // Mark the send as opened if it isn't already
                $db->setQuery(
                    $db->getQuery(true)
                        ->update($db->quoteName('#__crm_email_envios'))
                        ->set($db->quoteName('status') . ' = ' . $db->quote('lido'))
                        ->where($db->quoteName('id') . ' = ' . (int) $envioId)
                        ->where($db->quoteName('status') . ' != ' . $db->quote('lido'))
                )->execute();
            }
        } catch (Exception $e) {
            // Intentionally swallow exceptions to guarantee pixel rendering
        }

        // Output a 1x1 transparent GIF
        $app->setHeader('Content-Type', 'image/gif');
        echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        $app->close();
    }
}
