<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_crm
 *
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

// Get the ID of the import job from the input
$id = $this->get('Item')->id;

// The URL for the actual processing task
$processUrl = Route::_('index.php?option=com_crm&task=importarquivo.process&id=' . (int) $id . '&' . \Joomla\CMS\Session\Session::getFormToken() . '=1', false);
?>

<div class="card">
    <div class="card-header">
        <h1><?php echo Text::_('COM_CRM_IMPORT_PROCESS_HEADING'); ?></h1>
    </div>
    <div class="card-body">
        <p><?php echo Text::_('COM_CRM_IMPORT_PROCESS_SUBHEADING'); ?></p>
        <div class="progress">
            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%">
                <?php echo Text::_('COM_CRM_IMPORT_PROCESS_PLEASE_WAIT'); ?>
            </div>
        </div>
        <p class="text-muted">
            <?php echo Text::sprintf('COM_CRM_IMPORT_PROCESS_FILENAME', $this->get('Item')->nome); ?>
        </p>
    </div>
</div>

<script>
    // Redirect to the processing URL to start the import
    window.location.href = '<?php echo $processUrl; ?>';
</script>
