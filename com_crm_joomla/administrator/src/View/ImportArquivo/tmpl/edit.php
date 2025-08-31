<?php
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('behavior.formvalidator');
?>

<form action="<?php echo Route::_('index.php?option=com_crm&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate" enctype="multipart/form-data">
    <div class="form-horizontal">
        <fieldset class="adminform">
            <legend><?php echo Text::_('COM_CRM_IMPORTARQUIVO_DETAILS'); ?></legend>
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
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
