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
        $tracking   = $input->getString('tracking', $session->get('com_crm.tracking'));
        $sessionId  = $session->getId();
        $ip         = $input->server->getString('REMOTE_ADDR');
        $ipProxy    = $input->server->getString('HTTP_X_FORWARDED_FOR');
        $userAgent  = $input->server->getString('HTTP_USER_AGENT');

        try {
            if ($envioId && empty($campanhaId)) {
                $query = $db->getQuery(true)
                    ->select($db->quoteName('campanha_id'))
                    ->from($db->quoteName('#__crm_email_envios'))
                    ->where($db->quoteName('id') . ' = ' . (int) $envioId)
                    ->where($db->quoteName('state') . ' = 1');

                $db->setQuery($query);
                $campanhaId = $db->loadResult();
            }

            if ($envioId && $campanhaId) {
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
