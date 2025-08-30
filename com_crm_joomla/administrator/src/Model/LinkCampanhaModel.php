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
use Joomla\CMS\Language\Text;

/**
 * LinkCampanha Model
 */
class LinkCampanhaModel extends AdminModel
{
    /**
     * Method to get the record form.
     *
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  \Joomla\CMS\Form\Form|false  A Form object on success, false on failure.
     *
     * @since   1.0.0
     */
    public function getForm($loadData = true)
    {
        // Get the form.
        $form = $this->loadForm(
            'com_crm.linkcampanha',
            'linkcampanha',
            ['control' => 'jform', 'load_data' => $loadData]
        );

        if (empty($form)) {
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
        $data = $this->getApplication()->getUserState(
            'com_crm.edit.linkcampanha.data',
            []
        );

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Method to save the form data.
     *
     * @param   array  $data  The form data.
     *
     * @return  boolean  True on success.
     *
     * @since   1.0.0
     */
    public function save($data)
    {
        $user = $this->getApplication()->getIdentity();
        $isNew = empty($data['id']);

        if ($isNew) {
            // Check for create permission
            if (!$user->authorise('core.create', 'com_crm.linkcampanha')) {
                $this->setError(Text::_('JLIB_APPLICATION_ERROR_CREATE_NOT_PERMITTED'));
                return false;
            }
        } else {
            // Check for edit permission
            if (!$user->authorise('core.edit', 'com_crm.linkcampanha.' . $data['id'])) {
                $this->setError(Text::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
                return false;
            }
        }

        return parent::save($data);
    }
}
