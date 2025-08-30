<?php
namespace Joomla\Component\Crm\Administrator\Model;

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\Path;

class ImportArquivoModel extends AdminModel
{
    public function getForm($loadData = true)
    {
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

    protected function loadFormData()
    {
        $data = $this->getApplication()->getUserState('com_crm.edit.importarquivo.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    public function save($data)
    {
        // ACL Check
        $user = $this->getApplication()->getIdentity();
        $isNew = empty($data['id']);
        if ($isNew) {
            if (!$user->authorise('core.create', 'com_crm.importacao')) {
                $this->setError(Text::_('JLIB_APPLICATION_ERROR_CREATE_NOT_PERMITTED'));
                return false;
            }
        } else {
            // For editing, we don't re-upload the file, just save metadata
            if (!$user->authorise('core.edit', 'com_crm.importacao.' . $data['id'])) {
                $this->setError(Text::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
                return false;
            }
            return parent::save($data);
        }

        // Handle file upload only for new records
        $files = $this->getApplication()->getInput()->files->get('jform', [], 'array');
        $file = $files['arquivo'];

        // Basic validation
        if (empty($file['name'])) {
            $this->setError(Text::_('COM_CRM_IMPORTARQUIVO_ERROR_NO_FILE'));
            return false;
        }

        if ($file['error'] || !is_uploaded_file($file['tmp_name'])) {
            $this->setError(Text::_('COM_CRM_IMPORTARQUIVO_ERROR_UPLOAD_FAILED'));
            return false;
        }

        // Check file type
        $fileExtension = strtolower(Path::getExt($file['name']));
        if ($fileExtension !== 'csv') {
            $this->setError(Text::_('COM_CRM_IMPORTARQUIVO_ERROR_INVALID_FILE_TYPE'));
            return false;
        }

        // Prepare destination
        $destFolder = JPATH_ROOT . '/media/com_crm_joomla/imports';
        Folder::create($destFolder);

        $safeFileName = time() . '_' . Path::makeSafe($file['name']);
        $destPath = $destFolder . '/' . $safeFileName;

        // Move the file
        if (!File::upload($file['tmp_name'], $destPath)) {
            $this->setError(Text::_('COM_CRM_IMPORTARQUIVO_ERROR_MOVE_FAILED'));
            return false;
        }

        // Add the path to the data to be saved
        $data['arquivo_path'] = 'media/com_crm_joomla/imports/' . $safeFileName;

        return parent::save($data);
    }

    /**
     * Method to get the CSV preview and mapping data.
     *
     * @return  object|false  An object with preview data or false on failure.
     */
    public function getPreviewData()
    {
        $item = $this->getItem();
        if (empty($item->arquivo_path)) {
            $this->setError(Text::_('COM_CRM_IMPORTARQUIVO_ERROR_NO_FILE_PATH'));
            return false;
        }

        $filePath = JPATH_ROOT . '/' . $item->arquivo_path;
        if (!file_exists($filePath)) {
            $this->setError(Text::_('COM_CRM_IMPORTARQUIVO_ERROR_FILE_NOT_FOUND'));
            return false;
        }

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            $this->setError(Text::_('COM_CRM_IMPORTARQUIVO_ERROR_CANNOT_OPEN_FILE'));
            return false;
        }

        // Get headers
        $headers = fgetcsv($handle);

        // Get preview rows
        $preview = [];
        $i = 0;
        while (($row = fgetcsv($handle)) !== false && $i < 5) {
            $preview[] = $row;
            $i++;
        }

        fclose($handle);

        $data = new \stdClass();
        $data->headers = $headers;
        $data->preview = $preview;
        $data->mappableFields = $this->getMappableFields();

        return $data;
    }

    /**
     * Get the list of fields that can be mapped.
     *
     * @return  array
     */
    protected function getMappableFields()
    {
        $db = $this->getDbo();
        $fields = $db->getTableColumns('#__crm_leads');

        // Remove fields that shouldn't be mapped directly
        $unwanted = ['id', 'state', 'ordering', 'checked_out', 'checked_out_time', 'created', 'created_by', 'modified', 'modified_by'];

        return array_diff(array_keys($fields), $unwanted);
    }

    /**
     * Method to process the import.
     *
     * @param   int    $id   The ID of the import job.
     * @param   array  $map  The column mapping array.
     *
     * @return  object|false  An object with success/fail counts or false on failure.
     */
    public function processImport($id, $map)
    {
        // Get the import job item
        $item = $this->getItem($id);
        if (empty($item->arquivo_path)) {
            $this->setError(Text::_('COM_CRM_IMPORTARQUIVO_ERROR_NO_FILE_PATH'));
            return false;
        }

        $filePath = JPATH_ROOT . '/' . $item->arquivo_path;
        if (!file_exists($filePath)) {
            $this->setError(Text::_('COM_CRM_IMPORTARQUIVO_ERROR_FILE_NOT_FOUND'));
            return false;
        }

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            $this->setError(Text::_('COM_CRM_IMPORTARQUIVO_ERROR_CANNOT_OPEN_FILE'));
            return false;
        }

        // Skip header row
        fgetcsv($handle);

        // Get the Lead model to handle saving, which includes the many-to-many relationship
        $leadModel = $this->getInstance('Lead', 'Joomla\\Component\\Crm\\Administrator\\Model');

        $successCount = 0;
        $failCount = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $leadData = [];
            foreach ($map as $index => $fieldName) {
                if (!empty($fieldName) && isset($row[$index])) {
                    $leadData[$fieldName] = $row[$index];
                }
            }

            // Basic validation: must have email or phone
            if (empty($leadData['email']) && empty($leadData['telefone1'])) {
                $failCount++;
                continue;
            }

            // Assign to the destination groups from the import job
            $leadData['groups'] = $item->grupos_destino;

            // Use the LeadModel to save, which will handle the groups correctly
            if ($leadModel->save($leadData)) {
                $successCount++;
            } else {
                $failCount++;
            }
        }

        fclose($handle);

        $result = new \stdClass();
        $result->success = $successCount;
        $result->fail = $failCount;

        return $result;
    }
}
