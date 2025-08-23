<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm_joomla
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Com_Crm_Joomla\Administrator\Controller;

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Factory;

\defined('_JEXEC') or die;

/**
 * Grupos Controller
 *
 * @since  1.0.0
 */
class GruposController extends AdminController
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var  string
	 */
	protected $text_prefix = 'COM_CRM_JOOMLA_GRUPOS';

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The name of the model.
	 * @param   string  $prefix  The prefix for the class name.
	 * @param   array   $config  Configuration array for model.
	 *
	 * @return  \Joomla\CMS\MVC\Model\BaseDatabaseModel
	 *
	 * @since   1.0.0
	 */
	public function getModel($name = 'Grupos', $prefix = 'Administrator', $config = ['ignore_request' => true])
	{
		return parent::getModel($name, $prefix, $config);
	}
}
