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

<form action="<?php echo Route::_('index.php?option=com_crm&view=campanhalinks'); ?>" method="post" name="adminForm" id="adminForm">
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
                                <th><?php echo HTMLHelper::_('searchtools.sort', 'COM_CRM_CAMPANHALINK_FIELD_NOME_LABEL', 'a.nome', $listDirn, $listOrder); ?></th>
                                <th><?php echo HTMLHelper::_('searchtools.sort', 'COM_CRM_CAMPANHALINK_FIELD_URL_DESTINO_LABEL', 'a.url_destino', $listDirn, $listOrder); ?></th>
                                <th><?php echo HTMLHelper::_('searchtools.sort', 'COM_CRM_CAMPANHALINK_FIELD_CAMPANHA_ID_LABEL', 'campanha_nome', $listDirn, $listOrder); ?></th>
                                <th width="10%" class="text-center"><?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($this->items as $i => $item) : ?>
                                <tr class="row<?php echo $i % 2; ?>">
                                    <td class="text-center"><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td>
                                    <td class="text-center"><?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'campanhalinks.', true, 'cb'); ?></td>
                                    <td>
                                        <a href="<?php echo Route::_('index.php?option=com_crm&task=campanhalink.edit&id=' . $item->id); ?>">
                                            <?php echo $this->escape($item->nome); ?>
                                        </a>
                                    </td>
                                    <td><?php echo $this->escape($item->url_destino); ?></td>
                                    <td><?php echo $this->escape($item->campanha_nome); ?></td>
                                    <td class="text-center"><?php echo $item->id; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6"><?php echo $this->pagination->getListFooter(); ?></td>
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
