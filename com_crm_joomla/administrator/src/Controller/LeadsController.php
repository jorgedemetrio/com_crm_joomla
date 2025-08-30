<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Crm\Administrator\Controller;

use Joomla\CMS\MVC\Controller\AdminController;

/**
 * Leads Controller
 */
class LeadsController extends AdminController
{
    /**
     * The default view for the display task.
     *
     * @var string
     */
    protected $defaultView = 'leads';

    /**
     * Get the prefix for the model.
     *
     * @return  string  The prefix for the model.
     */
    protected function getModelPrefix()
    {
        return 'Lead';
    }
}
