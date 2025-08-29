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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filter\File;
use Joomla\Filesystem\Path;
use Joomla\Filesystem\Folder;

/**
 * ImportArquivo Model
 *
 * @since  1.0.0
 */
class ImportArquivoModel extends AdminModel
{
    /**
     * Method to get the record form.
     */
    public function getForm($data = [], $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm(
            'com_crm.importarquivo',
            'importarquivo',
            ['control' => 'jform', 'load_data' => $loadData]
        );

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
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState(
            'com_crm.edit.importarquivo.data',
            []
        );

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Method to save the form data.
     */
    public function save($data)
    {
        $app = Factory::getApplication();
        $files = $app->input->files->get('jform');
        $file = $files['arquivo_path'];

        // 1. File validation
        if (empty($file['name'])) {
            $this->setError(Text::_('COM_CRM_IMPORT_ERROR_NO_FILE_SELECTED'));
            return false;
        }
        if ($file['error'] || !is_uploaded_file($file['tmp_name'])) {
            $this->setError(Text::_('COM_CRM_IMPORT_ERROR_UPLOAD_FAILED'));
            return false;
        }
        $allowedExtensions = ['csv'];
        $fileExtension = strtolower(Path::getExt($file['name']));
        if (!in_array($fileExtension, $allowedExtensions)) {
            $this->setError(Text::_('COM_CRM_IMPORT_ERROR_INVALID_FILE_TYPE'));
            return false;
        }

        // 2. Prepare destination path
        // Note: JPATH_SITE is the root of the Joomla installation.
        // We define a media path for our component's uploads.
        $uploadPath = JPATH_SITE . '/media/com_crm/imports';
        if (!Folder::exists($uploadPath)) {
            Folder::create($uploadPath);
        }

        // 3. Sanitize filename and create unique name
        $filename = File::makeSafe($file['name']);
        $destPath = Path::combine($uploadPath, uniqid() . '_' . $filename);

        // 4. Move the file
        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
             $this->setError(Text::_('COM_CRM_IMPORT_ERROR_MOVE_FAILED'));
             return false;
        }

        // 5. Update data array with file path
        // We need to store a path relative to the site root for DB
        $data['arquivo_path'] = 'media/com_crm/imports/' . basename($destPath);
        $data['status'] = 'pendente'; // Set initial status

        // 6. Call parent save to store in DB
        if (parent::save($data)) {
            return true;
        }

        return false;
    }
}
