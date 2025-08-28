<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
?>

<form action="<?php echo Route::_('index.php?option=com_crm&view=leads'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>

                <?php if (empty($this->items)) : ?>
                    <div class="alert alert-info">
                        <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
                        <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                    </div>
                <?php else : ?>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="1%" class="text-center"><?php echo HTMLHelper::_('grid.checkall'); ?></th>
                                <th width="1%" class="text-center"><?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?></th>
                                <th><?php echo HTMLHelper::_('searchtools.sort', 'COM_CRM_LEAD_FIELD_RAZAO_SOCIAL_LABEL', 'a.razao_social', $listDirn, $listOrder); ?></th>
                                <th><?php echo HTMLHelper::_('searchtools.sort', 'COM_CRM_LEAD_FIELD_EMAIL_LABEL', 'a.email', $listDirn, $listOrder); ?></th>
                                <th><?php echo HTMLHelper::_('searchtools.sort', 'COM_CRM_LEAD_FIELD_CIDADE_LABEL', 'a.cidade', $listDirn, $listOrder); ?></th>
                                <th><?php echo HTMLHelper::_('searchtools.sort', 'COM_CRM_LEAD_FIELD_ESTADO_LABEL', 'a.estado', $listDirn, $listOrder); ?></th>
                                <th><?php echo HTMLHelper::_('searchtools.sort', 'COM_CRM_LEAD_FIELD_STATUS_LABEL', 'a.status', $listDirn, $listOrder); ?></th>
                                <th><?php echo HTMLHelper::_('searchtools.sort', 'COM_CRM_LEAD_FIELD_ORIGEM_LABEL', 'a.origem', $listDirn, $listOrder); ?></th>
                                <th width="10%" class="text-center"><?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($this->items as $i => $item) : ?>
                                <tr class="row<?php echo $i % 2; ?>">
                                    <td class="text-center"><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td>
                                    <td class="text-center"><?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'leads.', true, 'cb'); ?></td>
                                    <td>
                                        <a href="<?php echo Route::_('index.php?option=com_crm&task=lead.edit&id=' . $item->id); ?>">
                                            <?php echo $this->escape($item->razao_social ?: $item->nome_fantasia); ?>
                                        </a>
                                    </td>
                                    <td><?php echo $this->escape($item->email); ?></td>
                                    <td><?php echo $this->escape($item->cidade); ?></td>
                                    <td><?php echo $this->escape($item->estado); ?></td>
                                    <td><?php echo $this->escape($item->status); ?></td>
                                    <td><?php echo $this->escape($item->origem); ?></td>
                                    <td class="text-center"><?php echo $item->id; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                <?php endif; ?>

                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="0" />
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>
