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
?>
<form action="<?php echo Route::_('index.php?option=com_crm&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" name="adminForm" id="item-form" class="form-validate">

    <div class="main-card">
        <?php echo $this->form->renderFieldset('default'); ?>
        <?php echo LayoutHelper::render('joomla.edit.title_alias', ['item' => $this->item]); ?>

        <div class="row">
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-body">
                        <div class="form-horizontal">
                            <fieldset class="form-horizontal">
                                <legend><?php echo Text::_('COM_CRM_LEADGROUP_DETAILS'); ?></legend>
                                <?php echo $this->form->renderFieldset('default'); ?>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <?php echo LayoutHelper::render('joomla.edit.global', ['item' => $this->item]); ?>
            </div>
        </div>
    </div>

    <input type="hidden" name="task" value="" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
