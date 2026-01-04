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
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tabstate');
?>

<form action="<?php echo JRoute::_('index.php?option=com_crm&layout=edit&id=' . $this->item->id); ?>"
      method="post"
      name="adminForm"
      id="adminForm"
      class="form-validate">

    <?php echo JHtml::_('uitab.startTabSet', 'myTab', ['active' => 'details']); ?>

    <?php echo JHtml::_('uitab.addTab', 'myTab', 'details', JText::_('COM_CRM_LEAD_DETAILS_LABEL')); ?>
    <div class="row-fluid">
        <div class="span9">
            <?php echo $this->form->renderFieldset('details'); ?>
        </div>
    </div>
    <?php echo JHtml::_('uitab.endTab'); ?>

    <?php echo JHtml::_('uitab.addTab', 'myTab', 'address', JText::_('COM_CRM_LEAD_ADDRESS_LABEL')); ?>
    <div class="row-fluid">
        <div class="span9">
            <?php echo $this->form->renderFieldset('address'); ?>
        </div>
    </div>
    <?php echo JHtml::_('uitab.endTab'); ?>

    <?php echo JHtml::_('uitab.addTab', 'myTab', 'extra', JText::_('COM_CRM_LEAD_EXTRA_INFO_LABEL')); ?>
    <div class="row-fluid">
        <div class="span9">
            <?php echo $this->form->renderFieldset('extra'); ?>
        </div>
    </div>
    <?php echo JHtml::_('uitab.endTab'); ?>

    <?php echo JHtml::_('uitab.endTabSet'); ?>

    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</form>
