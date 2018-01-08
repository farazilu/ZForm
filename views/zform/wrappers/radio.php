<?php defined('SYSPATH') or die('No direct script access.'); ?>
<fieldset <?=HTML::attributes($attributes)?>>
	<legend><?=$field->form_label() ?></legend>
		<?php echo $field->form_field(); ?>
		<?php echo $field->get_error(TRUE);  ?>
		<span class="help-block"><?php echo $field->info(); ?></span>
</fieldset>