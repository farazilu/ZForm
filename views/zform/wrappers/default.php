<?php
defined ( 'SYSPATH' ) or die ( 'No direct script access.' );
switch ($form_horizontal) {
	case 'mini' :
		{
			echo $field->form_field ();
			echo $field->get_error ( TRUE );
			break;
		}
	case 'horizontal' :
		{
			?>
<div <?=HTML::attributes($attributes)?>>
	<?=$field->form_label ()?>
	       <div class="col-sm-10">
	   <?php
			echo $field->form_field ();
			echo $field->get_error ( TRUE );
			?>
	 <span class="help-block sr-only"><?php echo $field->info(); ?></span>
	</div>
	   <?php
			
			?>
</div>
<?php
			break;
		}
	default :
		{
			?>
<div <?=HTML::attributes($attributes)?>>
	<?=$field->form_label ()?>
    <?=$field->form_field ()?>
	<?=$field->get_error ( TRUE )?>
	<span class="help-block sr-only"><?php echo $field->info(); ?></span>
</div>
<?php
		}
}
?>