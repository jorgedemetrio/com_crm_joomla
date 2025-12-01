<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * LinksCampanha Controller
 */
class CrmControllerLinksCampanha extends JControllerAdmin
{
    /**
     * The default view for the display task.
     *
     * @var string
     */
    protected $defaultView = 'linkscampanha';

    /**
     * Get the prefix for the model.
     *
     * @return  string  The prefix for the model.
     */
    protected function getModelPrefix()
    {
        return 'LinkCampanha';
    }
}
