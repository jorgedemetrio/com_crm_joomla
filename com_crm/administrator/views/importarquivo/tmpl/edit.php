<?php

defined('_JEXEC') or die;

JHtml::_('behavior.formvalidator');
?>

<form action="<?php echo JRoute::_('index.php?option=com_crm&layout=edit&id=' . $this->escape($this->item->id)); ?>" method="post" name="adminForm" id="item-form" class="form-validate" enctype="multipart/form-data">
    <div class="form-horizontal">
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_CRM_IMPORTARQUIVO_DETAILS'); ?></legend>
            <div class="row-fluid">
                <div class="span6">
                    <?php echo $this->form->renderField('nome'); ?>
                    <?php echo $this->form->renderField('arquivo'); ?>
                    <?php echo $this->form->renderField('grupos_destino'); ?>
                </div>
            </div>
        </fieldset>
    </div>
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</form>
