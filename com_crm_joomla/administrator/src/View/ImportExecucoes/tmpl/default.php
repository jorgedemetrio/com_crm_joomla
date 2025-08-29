<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('bootstrap.modal');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>

<form action="<?php echo Route::_('index.php?option=com_crm&view=importexecucoes'); ?>" method="post" name="adminForm" id="adminForm">
    <?php // echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>

    <div id="j-main-container" class="j-main-container">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th width="1%">
                        <?php echo HTMLHelper::_('grid.checkall'); ?>
                    </th>
                    <th class="left">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_CRM_IMPORTARQUIVOS_NOME', 'ia.nome', $listDirn, $listOrder); ?>
                    </th>
                    <th width="10%" class="nowrap center">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_CRM_IMPORTEXECUCOES_STATUS', 'a.status', $listDirn, $listOrder); ?>
                    </th>
                    <th width="10%" class="nowrap center">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_CRM_IMPORTEXECUCOES_LINHAS_SUCESSO', 'a.linhas_sucesso', $listDirn, $listOrder); ?>
                    </th>
                    <th width="10%" class="nowrap center">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_CRM_IMPORTEXECUCOES_LINHAS_FALHA', 'a.linhas_falha', $listDirn, $listOrder); ?>
                    </th>
                    <th width="15%" class="nowrap center">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_CRM_IMPORTEXECUCOES_FINISHED_AT', 'a.finished_at', $listDirn, $listOrder); ?>
                    </th>
                    <th width="10%" class="nowrap center">
                        <?php echo Text::_('COM_CRM_IMPORTEXECUCOES_LOG'); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->items as $i => $item) : ?>
                    <tr class="row<?php echo $i % 2; ?>">
                        <td class="center">
                            <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                        </td>
                        <td>
                            <?php echo $this->escape($item->import_nome); ?>
                        </td>
                        <td class="center">
                            <span class="badge bg-<?php echo $item->status === 'ok' ? 'success' : ($item->status === 'parcial' ? 'warning' : 'danger'); ?>">
                                <?php echo Text::_('COM_CRM_IMPORT_EXEC_STATUS_' . strtoupper($this->escape($item->status))); ?>
                            </span>
                        </td>
                        <td class="center">
                            <?php echo (int) $item->linhas_sucesso; ?>
                        </td>
                        <td class="center">
                             <?php echo (int) $item->linhas_falha; ?>
                        </td>
                        <td class="center">
                            <?php echo HTMLHelper::_('date', $item->finished_at, Text::_('DATE_FORMAT_LC4')); ?>
                        </td>
                        <td class="center">
                            <?php if (!empty($item->log)) : ?>
                                <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#logModal-<?php echo $item->id; ?>">
                                    <span class="icon-eye"></span> <?php echo Text::_('JVIEW'); ?>
                                </button>
                                <?php echo LayoutHelper::render('joomla.modal.main',
                                    [
                                        'id' => 'logModal-' . $item->id,
                                        'title' => Text::sprintf('COM_CRM_IMPORTEXECUCOES_LOG_FOR', $item->import_nome),
                                        'body' => '<pre>' . $this->escape($item->log) . '</pre>',
                                        'footer' => '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">' . Text::_('JCLOSE') . '</button>',
                                    ]);
                                ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>
        </table>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo HTMLHelper::_('form.token'); ?>
    </div>
</form>
