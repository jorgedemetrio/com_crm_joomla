<?php

defined('_JEXEC') or die;

$previewData = $this->previewData;
?>

<form action="<?php echo JRoute::_('index.php?option=com_crm&task=importarquivo.doImport&id=' . $this->escape($this->item->id)); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
    <h3><?php echo JText::_('COM_CRM_IMPORTARQUIVO_MAPPING_TITLE'); ?></h3>
    <p><?php echo JText::_('COM_CRM_IMPORTARQUIVO_MAPPING_DESC'); ?></p>

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <?php foreach ($previewData->headers as $header) : ?>
                    <th><?php echo $this->escape($header); ?></th>
                <?php endforeach; ?>
            </tr>
            <tr>
                <?php foreach ($previewData->headers as $index => $header) : ?>
                    <td>
                        <select name="map[<?php echo $index; ?>]" class="form-select">
                            <option value=""><?php echo JText::_('JSELECT'); ?></option>
                            <?php foreach ($previewData->mappableFields as $field) : ?>
                                <option value="<?php echo $field; ?>"><?php echo $field; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($previewData->preview as $row) : ?>
                <tr>
                    <?php foreach ($row as $cell) : ?>
                        <td><?php echo $this->escape($cell); ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</form>
