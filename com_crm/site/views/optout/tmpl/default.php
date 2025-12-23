<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$data = $this->data;
$itemid = (int) $this->itemid;
$email = htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8');
?>
<div class="com-crm-optout">
    <!-- Help Button -->
    <div class="clearfix mb-3">
        <button type="button" class="btn btn-info float-end" data-bs-toggle="modal" data-bs-target="#optoutHelpModal">
            <i class="bi bi-question-circle"></i> <?php echo JText::_('COM_CRM_OPTOUT_HELP_TITLE'); ?>
        </button>
    </div>

    <h1><?php echo JText::_('COM_CRM_OPTOUT_CONFIRM_TITLE'); ?></h1>

    <div class="alert alert-warning">
        <p class="lead">
            <?php echo JText::sprintf('COM_CRM_OPTOUT_CONFIRM_MESSAGE', $email); ?>
        </p>
    </div>

    <form action="<?php echo JRoute::_('index.php?option=com_crm&task=optout.unsubscribe&Itemid=' . $itemid); ?>" method="post" class="form-validate">

        <!-- Hidden fields to preserve context -->
        <input type="hidden" name="email" value="<?php echo $email; ?>" />
        <input type="hidden" name="scope" value="<?php echo htmlspecialchars($data['scope'], ENT_QUOTES, 'UTF-8'); ?>" />
        <input type="hidden" name="campanha_id" value="<?php echo htmlspecialchars($data['campanha_id'], ENT_QUOTES, 'UTF-8'); ?>" />
        <input type="hidden" name="reason" value="<?php echo htmlspecialchars($data['reason'], ENT_QUOTES, 'UTF-8'); ?>" />
        <input type="hidden" name="tracking" value="<?php echo htmlspecialchars($data['tracking'], ENT_QUOTES, 'UTF-8'); ?>" />
        <input type="hidden" name="Itemid" value="<?php echo $itemid; ?>" />

        <div class="form-actions mt-4">
            <button type="submit" class="btn btn-danger btn-lg">
                <i class="bi bi-x-circle"></i> <?php echo JText::_('COM_CRM_OPTOUT_CONFIRM_BUTTON'); ?>
            </button>
            <a href="<?php echo JRoute::_('index.php?Itemid=' . $itemid); ?>" class="btn btn-secondary btn-lg">
                <i class="bi bi-arrow-left"></i> <?php echo JText::_('JCANCEL'); ?>
            </a>
        </div>

        <?php echo JHtml::_('form.token'); ?>
    </form>
</div>

<!-- Help Modal -->
<div class="modal fade" id="optoutHelpModal" tabindex="-1" aria-labelledby="optoutHelpModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="optoutHelpModalLabel"><?php echo JText::_('COM_CRM_OPTOUT_HELP_TITLE'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?php echo JText::_('COM_CRM_OPTOUT_HELP_BODY'); ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo JText::_('JCLOSE'); ?></button>
            </div>
        </div>
    </div>
</div>
