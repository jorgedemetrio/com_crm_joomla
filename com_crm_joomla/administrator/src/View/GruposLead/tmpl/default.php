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
HTMLHelper::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>

<form action="<?php echo Route::_('index.php?option=com_crm&view=gruposlead'); ?>" method="post" name="adminForm" id="adminForm">
    <?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>

    <div id="j-main-container" class="j-main-container">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th width="1%">
                        <?php echo HTMLHelper::_('grid.checkall'); ?>
                    </th>
                    <th width="1%" class="nowrap center">
                        <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                    </th>
                    <th class="left">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_CRM_GRUPOSLEAD_NOME', 'a.nome', $listDirn, $listOrder); ?>
                    </th>
                    <th width="5%" class="nowrap center">
                        <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->items as $i => $item) : ?>
                    <tr class="row<?php echo $i % 2; ?>">
                        <td class="center">
                            <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                        </td>
                        <td class="center">
                            <?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'gruposlead.', true, 'cb'); ?>
                        </td>
                        <td>
                            <a href="<?php echo Route::_('index.php?option=com_crm&task=grupolead.edit&id=' . $this->escape($item->id)); ?>">
                                <?php echo $this->escape($item->nome); ?>
                            </a>
                        </td>
                        <td class="center">
                            <?php echo $this->escape($item->id); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4">
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
