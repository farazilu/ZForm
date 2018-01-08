<?php defined('SYSPATH') or die('No direct script access.'); ?>
<li <?php echo HTML::attributes($attributes); ?>>
	<?php echo $field->form_label(); ?>
	<?php echo $field->form_field(); ?>
	<?php echo $field->get_error(TRUE);  ?>
	 <span class="info ui-state-highlight ui-corner-all"> <span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
		<?php echo $field->info(); ?>
	 </span>
</li>
