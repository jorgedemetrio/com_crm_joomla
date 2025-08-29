<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Crm\Administrator\Model;

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;

/**
 * CampanhaEmail Model
 *
 * @since  1.0.0
 */
class CampanhaEmailModel extends AdminModel
{
    /**
     * Method to get the record form.
     */
    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm('com_crm.campanhaemail', 'campanhaemail', ['control' => 'jform', 'load_data' => $loadData]);
        if (empty($form)) {
            return false;
        }
        return $form;
    }

    /**
     * Method to get the data that should be injected into the form.
     */
    protected function loadFormData()
    {
        $data = Factory::getApplication()->getUserState('com_crm.edit.campanhaemail.data', null);

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }
}
