<?php
defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_crm&view=importwebs'); ?>" method="post" name="adminForm" id="adminForm">
    <?php echo JLayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>
    <div id="j-main-container">
        <table class="table table-striped" id="importwebsList">
            <thead>
                <tr>
                    <th width="1%" class="center">
                        <?php echo JHtml::_('grid.checkall'); ?>
                    </th>
                    <th width="1%" class="nowrap center">
                        <?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                    </th>
                    <th class="left"><?php echo JHtml::_("searchtools.sort", "COM_CRM_NAME", "a.nome", $listDirn, $listOrder); ?></th>\n<th class="left"><?php echo JHtml::_("searchtools.sort", "COM_CRM_SOURCE", "a.origem", $listDirn, $listOrder); ?></th>\n<th class="left"><?php echo JHtml::_("searchtools.sort", "COM_CRM_KEYWORDS", "a.palavras_chave", $listDirn, $listOrder); ?></th>\n
                    <th width="1%" class="nowrap center">
                        <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($this->items as $i => $item) :
                $canEdit = JFactory::getUser()->authorise('core.edit', 'com_crm');
                $link = JRoute::_('index.php?option=com_crm&task=importweb.edit&id=' . $item->id);
            ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td class="center">
                        <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                    </td>
                    <td class="center">
                        <?php echo JHtml::_('jgrid.published', $item->published, $i, 'importwebs.', true, 'cb'); ?>
                    </td>
                    <td>
                        <?php if ($canEdit) : ?>
                            <a href="<?php echo $link; ?>"><?php echo $this->escape($item->nome); ?></a>
                        <?php else : ?>
                            <?php echo $this->escape($item->nome); ?>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $this->escape($item->origem); ?></td>\n<td><?php echo $this->escape($item->palavras_chave); ?></td>
                    <td class="center">
                        <?php echo $item->id; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6">
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
