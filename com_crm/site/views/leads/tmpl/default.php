<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_crm
 */

defined('_JEXEC') or die;

JHtml::_('behavior.formvalidator');

$app    = JFactory::getApplication();
$itemid = isset($this->itemid) ? (int) $this->itemid : $app->input->getInt('Itemid');
$action = JRoute::_('index.php?option=com_crm&view=leads&Itemid=' . (int) $itemid);
?>
<div class="com-crm-leads">
    <h1><?php echo JText::_('COM_CRM_LEADS_TITLE'); ?></h1>
    <p><?php echo JText::_('COM_CRM_LEADS_INTRO'); ?></p>

    <form action="<?php echo $action; ?>" method="post" class="form-validate">
        <div class="control-group">
            <label class="control-label" for="crm-message"><?php echo JText::_('COM_CRM_LEADS_MESSAGE_LABEL'); ?></label>
            <div class="controls">
                <textarea id="crm-message" name="message" class="required" rows="5"></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary validate">
                <?php echo JText::_('COM_CRM_LEADS_SUBMIT'); ?>
            </button>
        </div>

        <input type="hidden" name="Itemid" value="<?php echo (int) $itemid; ?>" />
        <?php echo JHtml::_('form.token'); ?>
    </form>
</div>
