<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm_joomla
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

\defined('_JEXEC') or die;

HTMLHelper::_('behavior.formvalidator');
?>

<form action="<?php echo Route::_('index.php?option=com_crm_joomla&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">

    <?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>

    <div class="main-card">
        <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'details']); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('JDETAILS')); ?>
            <div class="row">
                <div class="col-lg-9">
                    <fieldset class="form-horizontal">
                        <?php echo $this->form->renderField('nome'); ?>
                    </fieldset>
                </div>
                <div class="col-lg-3">
                    <?php echo $this->form->renderField('state'); ?>
                    <?php echo $this->form->renderField('id'); ?>
                </div>
            </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.endTabSet'); ?>
    </div>

    <input type="hidden" name="task" value="" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
