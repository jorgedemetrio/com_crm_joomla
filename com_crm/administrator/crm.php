<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024 Sobieski Produções. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Access check
if (!JFactory::getUser()->authorise('core.manage', 'com_crm'))
{
    throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
}

$controller = JControllerLegacy::getInstance('Crm');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
