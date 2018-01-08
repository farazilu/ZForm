<?php defined('SYSPATH') or die('No direct script access.'); ?>
<li <?php echo HTML::attributes($attributes); ?>>
	<ul>
		<li class="table-ul">
		<?php echo $field->form_label(); ?>
		 <?php echo $field->form_field(); ?>
		  <?php echo $field->get_error(TRUE);  ?>
		  <span class="text-info"> <span class="fa fa-info-circle"></span><?php echo $field->info(); ?></span>
		</li>
	</ul>
</li>
