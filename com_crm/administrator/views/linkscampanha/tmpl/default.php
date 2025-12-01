<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>

<form action="<?php echo JRoute::_('index.php?option=com_crm&view=linkscampanha'); ?>" method="post" name="adminForm" id="adminForm">
    <?php echo JLayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>

    <?php if (!empty($this->sidebar)) : ?>
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
<?php else : ?>
    <div id="j-main-container">
<?php endif; ?>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th width="1%">
                        <?php echo JHtml::_('grid.checkall'); ?>
                    </th>
                    <th width="1%" class="nowrap center">
                        <?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                    </th>
                    <th class="left">
                        <?php echo JHtml::_('searchtools.sort', 'COM_CRM_LINKCAMPANHA_NOME_LABEL', 'a.nome', $listDirn, $listOrder); ?>
                    </th>
                    <th class="left">
                        <?php echo JHtml::_('searchtools.sort', 'COM_CRM_LINKCAMPANHA_CAMPANHA_ID_LABEL', 'c.nome', $listDirn, $listOrder); ?>
                    </th>
                    <th class="left">
                        <?php echo JHtml::_('searchtools.sort', 'COM_CRM_LINKCAMPANHA_URL_DESTINO_LABEL', 'a.url_destino', $listDirn, $listOrder); ?>
                    </th>
                    <th width="10%" class="nowrap center">
                        <?php echo JHtml::_('searchtools.sort', 'COM_CRM_LINKCAMPANHA_CLICKS_LABEL', 'a.clicks_total', $listDirn, $listOrder); ?>
                    </th>
                    <th width="5%" class="nowrap center">
                        <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->items as $i => $item) : ?>
                    <tr class="row<?php echo $i % 2; ?>">
                        <td class="center">
                            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                        </td>
                        <td class="center">
                            <?php echo JHtml::_('jgrid.published', $item->published, $i, 'linkscampanha.', true, 'cb'); ?>
                        </td>
                        <td>
                            <a href="<?php echo JRoute::_('index.php?option=com_crm&task=linkcampanha.edit&id=' . $this->escape($item->id)); ?>">
                                <?php echo $this->escape($item->nome); ?>
                            </a>
                        </td>
                        <td><?php echo $this->escape($item->campanha_nome); ?></td>
                        <td><?php echo $this->escape($item->url_destino); ?></td>
                        <td class="center"><?php echo (int) $item->clicks_total; ?></td>
                        <td class="center">
                            <?php echo $this->escape($item->id); ?>
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
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
