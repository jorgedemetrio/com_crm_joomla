<?php
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;

?>
<form action="<?php echo Route::_('index.php?option=com_crm&view=importarquivos'); ?>" method="post" name="adminForm" id="adminForm">
    <div id="j-main-container" class="j-main-container">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th width="1%"><?php echo HTMLHelper::_('grid.checkall'); ?></th>
                    <th><?php echo HTMLHelper::_('searchtools.sort', 'COM_CRM_IMPORTARQUIVO_NOME_LABEL', 'a.nome', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
                    <th><?php echo HTMLHelper::_('searchtools.sort', 'JDATE', 'a.created', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
                    <th width="1%"><?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->items as $i => $item) : ?>
                    <tr>
                        <td><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td>
                        <td>
                            <a href="<?php echo Route::_('index.php?option=com_crm&task=importarquivo.edit&id=' . $item->id); ?>">
                                <?php echo $this->escape($item->nome); ?>
                            </a>
                        </td>
                        <td><?php echo HTMLHelper::_('date', $item->created, 'Y-m-d H:i:s'); ?></td>
                        <td><?php echo $item->id; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4"><?php echo $this->pagination->getListFooter(); ?></td>
                </tr>
            </tfoot>
        </table>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo HTMLHelper::_('form.token'); ?>
    </div>
</form>
