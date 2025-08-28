<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.tabstate');
?>
<form action="<?php echo Route::_('index.php?option=com_crm&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" name="adminForm" id="item-form" class="form-validate">

    <?php echo LayoutHelper::render('joomla.edit.title_alias', ['item' => $this->item]); ?>

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="details-tab" data-bs-toggle="tab" href="#details" role="tab" aria-controls="details" aria-selected="true"><?php echo Text::_('COM_CRM_LEAD_DETAILS_LABEL'); ?></a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="address-tab" data-bs-toggle="tab" href="#address" role="tab" aria-controls="address" aria-selected="false"><?php echo Text::_('COM_CRM_LEAD_ADDRESS_LABEL'); ?></a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="extra-details-tab" data-bs-toggle="tab" href="#extra-details" role="tab" aria-controls="extra-details" aria-selected="false"><?php echo Text::_('COM_CRM_LEAD_EXTRA_DETAILS_LABEL'); ?></a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="partners-tab" data-bs-toggle="tab" href="#partners" role="tab" aria-controls="partners" aria-selected="false"><?php echo Text::_('COM_CRM_LEAD_PARTNERS_LABEL'); ?></a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="origin-tab" data-bs-toggle="tab" href="#origin" role="tab" aria-controls="origin" aria-selected="false"><?php echo Text::_('COM_CRM_LEAD_ORIGIN_LABEL'); ?></a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="groups-tab" data-bs-toggle="tab" href="#groups" role="tab" aria-controls="groups" aria-selected="false"><?php echo Text::_('COM_CRM_LEAD_GROUPS_LABEL'); ?></a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="publishing-tab" data-bs-toggle="tab" href="#publishing" role="tab" aria-controls="publishing" aria-selected="false"><?php echo Text::_('JGLOBAL_FIELDSET_PUBLISHING'); ?></a>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
            <?php echo $this->form->renderFieldset('default'); ?>
        </div>
        <div class="tab-pane fade" id="address" role="tabpanel" aria-labelledby="address-tab">
            <?php echo $this->form->renderFieldset('address'); ?>
        </div>
        <div class="tab-pane fade" id="extra-details" role="tabpanel" aria-labelledby="extra-details-tab">
            <?php echo $this->form->renderFieldset('details'); ?>
        </div>
        <div class="tab-pane fade" id="partners" role="tabpanel" aria-labelledby="partners-tab">
            <?php echo $this->form->renderFieldset('partners'); ?>
        </div>
        <div class="tab-pane fade" id="origin" role="tabpanel" aria-labelledby="origin-tab">
            <?php echo $this->form->renderFieldset('origin'); ?>
        </div>
        <div class="tab-pane fade" id="groups" role="tabpanel" aria-labelledby="groups-tab">
            <?php echo $this->form->renderFieldset('groups'); ?>
        </div>
        <div class="tab-pane fade" id="publishing" role="tabpanel" aria-labelledby="publishing-tab">
            <?php echo $this->form->renderFieldset('publishing'); ?>
        </div>
    </div>

    <input type="hidden" name="task" value="" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
