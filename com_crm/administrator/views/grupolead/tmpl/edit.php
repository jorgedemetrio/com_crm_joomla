<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.formvalidator');
?>

<form action="<?php echo JRoute::_('index.php?option=com_crm&layout=edit&id=' . (int) $this->item->id); ?>"
      method="post"
      name="adminForm"
      id="adminForm"
      class="form-validate">

    <div class="form-horizontal">
        <fieldset class="adminform">
            <legend><?php echo JText::_('JDETAILS'); ?></legend>
            <div class="row-fluid">
                <div class="span6">
                    <?php echo $this->form->renderField('nome'); ?>
                    <?php echo $this->form->renderField('state'); ?>
                </div>
            </div>
        </fieldset>
    </div>

    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</form>
