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

// Register helpers if any (optional, standard autoloading might catch it if named correctly)
// But explicit register is safer for J3
JLoader::register('CrmHelper', JPATH_COMPONENT . '/helpers/crm.php');

$controller = JControllerLegacy::getInstance('Crm');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
