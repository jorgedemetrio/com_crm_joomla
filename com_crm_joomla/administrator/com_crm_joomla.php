<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm_joomla
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

// Access check.
if (!Factory::getApplication()->getIdentity()->authorise('core.manage', 'com_crm_joomla'))
{
	return Factory::getApplication()->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error')->redirect(
		CMSApplication::getInstance('administrator')->getMenu()->getActive()->home ?
		'index.php' : 'index.php?option=com_cpanel'
	);
}

// Get an instance of the controller prefixed by Crm_Joomla
$controller = BaseController::getInstance('Crm_Joomla', ['default_task' => 'display']);

// Perform the Request task
$controller->execute(Factory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
