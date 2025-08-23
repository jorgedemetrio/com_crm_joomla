<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm_joomla
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Text;

\defined('_JEXEC') or die;

HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));
$user       = $this->state->get('user');
?>

<form action="<?php echo Route::_('index.php?option=com_crm_joomla&view=grupos'); ?>" method="post" name="adminForm" id="adminForm">
    <?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th width="1%" class="text-center">
                    <?php echo HTMLHelper::_('grid.checkall'); ?>
                </th>
                <th width="1%" style="min-width:55px" class="nowrap text-center">
                    <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                </th>
                <th>
                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_CRM_JOOMLA_GRUPOS_NOME', 'a.nome', $listDirn, $listOrder); ?>
                </th>
                <th width="10%" class="nowrap text-center">
                    <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->items as $i => $item) :
                $canEdit = $user->authorise('core.edit', 'com_crm_joomla.grupo.' . $item->id);
                $canChange = $user->authorise('core.edit.state', 'com_crm_joomla.grupo.' . $item->id);
            ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td class="text-center">
                        <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                    </td>
                    <td class="text-center">
                        <?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'grupos.', $canChange, 'cb'); ?>
                    </td>
                    <td>
                        <?php if ($canEdit) : ?>
                            <a href="<?php echo Route::_('index.php?option=com_crm_joomla&task=grupo.edit&id=' . $item->id); ?>">
                                <?php echo $this->escape($item->nome); ?>
                            </a>
                        <?php else : ?>
                            <?php echo $this->escape($item->nome); ?>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
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
</form>
