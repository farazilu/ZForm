<?php defined('SYSPATH') or die('No direct script access.'); ?>

<div class="form-group <?php echo $error ? 'error ' : ''; ?>field-<?php echo $field->id; ?>" id="field_<?php echo $field->id; ?>">
    <?php echo $field->form_label(array('class' => 'col-sm-3 control-label')); ?>
    <div class="col-sm-9">
		<?php echo $field->form_field(); ?>
		<?php echo sprintf('<p class="help-block">%s</p>', $error ? $error : $help_text); ?>
    </div>
</div>