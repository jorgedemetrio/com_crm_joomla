<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm_joomla
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Com_Crm_Joomla\Administrator\Model;

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;

\defined('_JEXEC') or die;

/**
 * Grupo Model
 *
 * @since  1.0.0
 */
class GrupoModel extends AdminModel
{
    /**
     * Method to get the record form.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  \Joomla\CMS\Form\Form|boolean  A Form object on success, false on failure
     *
     * @since   1.0.0
     */
    public function getForm($data = [], $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm(
            'com_crm_joomla.grupo',
            'grupo',
            ['control' => 'jform', 'load_data' => $loadData]
        );

        if (empty($form))
        {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected into the form.
     *
     * @return  mixed  The data for the form.
     *
     * @since   1.0.0
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState(
            'com_crm_joomla.edit.grupo.data',
            null
        );

        if (empty($data))
        {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  \Joomla\CMS\Table\Table  A #__table object
	 */
	public function getTable($name = 'Grupo', $prefix = 'Administrator\\Table', $options = [])
	{
		return parent::getTable($name, $prefix, $options);
	}
}
