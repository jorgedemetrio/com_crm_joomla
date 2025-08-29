<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Crm\Administrator\Controller;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;

/**
 * ImportArquivo controller class.
 *
 * @since  1.0.0
 */
class ImportArquivoController extends FormController
{
    /**
     * Overriding the save method to redirect to the processing view.
     * In a later step, we will implement the process view. For now,
     * this is a placeholder to show where the logic will go.
     */
    public function save($key = null, $urlVar = null)
    {
        // Check for request forgeries.
        $this->checkToken();

        $model = $this->getModel('ImportArquivo');
        $data  = $this->input->post->get('jform', [], 'array');

        // The model's save method handles the file upload and DB record.
        if ($model->save($data)) {
            $id = $model->getState($this->context . '.id');

            // Redirect to the processing view
            $this->setRedirect(
                Route::_('index.php?option=com_crm&view=importarquivo&layout=process&id=' . $id, false)
            );
            return true;
        }

        // Redirect back to the form on failure.
        $this->setRedirect(
            Route::_('index.php?option=com_crm&view=importarquivo&layout=edit', false),
            $model->getError(),
            'error'
        );

        return false;
    }

    /**
     * Method to process the import.
     */
    public function process()
    {
        $this->checkToken('get');
        $app = \Joomla\CMS\Factory::getApplication();
        $id  = $this->input->getInt('id');

        // Set a default redirect just in case
        $this->setRedirect(
            Route::_('index.php?option=com_crm&view=importarquivos', false)
        );

        $model = $this->getModel('ImportArquivo');
        $item = $model->getItem($id);

        if (empty($item) || empty($item->arquivo_path)) {
            $app->enqueueMessage(\Joomla\CMS\Language\Text::_('COM_CRM_IMPORT_ERROR_NO_FILE'), 'error');
            return;
        }

        $filePath = JPATH_SITE . '/' . $item->arquivo_path;

        if (!\Joomla\Filesystem\File::exists($filePath)) {
            $app->enqueueMessage(\Joomla\CMS\Language\Text::sprintf('COM_CRM_IMPORT_ERROR_FILE_NOT_FOUND', $item->arquivo_path), 'error');
            return;
        }

        // Open the CSV
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            $app->enqueueMessage(\Joomla\CMS\Language\Text::_('COM_CRM_IMPORT_ERROR_CANNOT_OPEN_FILE'), 'error');
            return;
        }

        $header = fgetcsv($handle);
        if ($header === false || count($header) < 1) {
            $app->enqueueMessage(\Joomla\CMS\Language\Text::_('COM_CRM_IMPORT_ERROR_EMPTY_OR_INVALID_CSV'), 'error');
            fclose($handle);
            return;
        }

        $db = \Joomla\CMS\Factory::getDbo();
        // Assuming these table classes exist or will be created.
        $leadTable = new \Joomla\Component\Crm\Administrator\Table\LeadTable($db);
        $execucaoTable = new \Joomla\Component\Crm\Administrator\Table\ImportExecucaoTable($db);

        $successCount = 0;
        $failureCount = 0;
        $logMessages = [];
        $line = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $line++;

            if (count($header) !== count($row)) {
                $failureCount++;
                $logMessages[] = "Linha $line: " . \Joomla\CMS\Language\Text::_('COM_CRM_IMPORT_LOG_COLUMN_MISMATCH');
                continue;
            }

            $data = array_combine($header, $row);

            // --- Validation ---
            if (empty($data['email']) && empty($data['telefone1'])) {
                $failureCount++;
                $logMessages[] = "Linha $line: " . \Joomla\CMS\Language\Text::_('COM_CRM_IMPORT_LOG_SKIPPED_NO_EMAIL_PHONE');
                continue;
            }

            // --- Deduplication ---
            $query = $db->getQuery(true)
                ->select($db->quoteName('id'))
                ->from($db->quoteName('#__crm_leads'));
            $where = [];
            if (!empty($data['email'])) {
                $where[] = $db->quoteName('email') . ' = ' . $db->quote($data['email']);
            }
            if (!empty($data['telefone1'])) {
                $where[] = $db->quoteName('telefone1') . ' = ' . $db->quote($data['telefone1']);
            }
            $query->where('(' . implode(' OR ', $where) . ')');
            $db->setQuery($query);
            if ($db->loadResult()) {
                $failureCount++;
                $logMessages[] = "Linha $line: " . \Joomla\CMS\Language\Text::sprintf('COM_CRM_IMPORT_LOG_DUPLICATE', ($data['email'] ?? $data['telefone1']));
                continue;
            }

            // --- Save Lead ---
            // Note: `id` should be a UUID. The table class should handle this.
            $leadData = [
                'nome' => $data['nome'] ?? 'Sem nome',
                'email' => $data['email'] ?? null,
                'telefone1' => $data['telefone1'] ?? null,
                'cidade' => $data['cidade'] ?? null,
                'estado' => $data['estado'] ?? null,
                'pais' => $data['pais'] ?? 'Brasil',
                'state' => 1,
                'status' => 'NOVO',
                'origem' => 'CSV'
            ];

            try {
                // The save method in a Joomla Table class should ideally handle setting the ID for new records.
                if (!$leadTable->save($leadData)) {
                    throw new \Exception($leadTable->getError());
                }
                $successCount++;
            } catch (\Exception $e) {
                $failureCount++;
                $logMessages[] = "Linha $line: " . \Joomla\CMS\Language\Text::sprintf('COM_CRM_IMPORT_LOG_SAVE_FAILED', ($leadData['email'] ?? $leadData['telefone1']), $e->getMessage());
            }
        }
        fclose($handle);

        // --- Log Execution ---
        $logData = [
            'id' => null,
            'tipo' => 'arquivo',
            'referencia_id' => $id,
            'status' => ($failureCount > 0) ? ($successCount > 0 ? 'parcial' : 'falha') : 'ok',
            'linhas_total' => $successCount + $failureCount,
            'linhas_sucesso' => $successCount,
            'linhas_falha' => $failureCount,
            'log' => implode("\n", $logMessages),
            'finished_at' => (new \Joomla\CMS\Date\Date('now'))->toSql()
        ];
        $execucaoTable->save($logData);

        // --- Update Import Job Status ---
        $item->status = 'processado';
        $model->save($item);

        // --- Final Redirect ---
        $this->setMessage(\Joomla\CMS\Language\Text::sprintf('COM_CRM_IMPORT_SUCCESS_MESSAGE', $successCount, $failureCount));
        $this->setRedirect(Route::_('index.php?option=com_crm&view=importexecucoes&filter_referencia_id=' . $id, false));
    }
}
