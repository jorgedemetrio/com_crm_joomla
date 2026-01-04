<?php
defined('_JEXEC') or die;

JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');
?>
<form action="<?php echo JRoute::_('index.php?option=com_crm&layout=edit&id=' . $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
    <div class="form-horizontal">
        <fieldset class="adminform">
            <legend><?php echo JText::_('JDETAILS'); ?></legend>
            <?php echo $this->form->renderField('id'); ?>
            <?php echo $this->form->renderField('state'); ?>
            <?php echo $this->form->renderField("nome"); ?>\n<?php echo $this->form->renderField("origem"); ?>\n<?php echo $this->form->renderField("palavras_chave"); ?>\n
        </fieldset>
    </div>
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</form>
