<?php
defined ( 'SYSPATH' ) or die ( 'No direct script access.' );
/**
 *
 * @var $field ZForm_Field
 */
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
<li <?php echo HTML::attributes($attributes); ?>>
	<?php
			echo $field->form_label ();
			echo $field->form_field ();
			echo $field->get_error ( TRUE );
			?>
	 <span class="help-block"> <span class="fa fa-info-circle" style="float: left; margin-right: .3em;"></span>
		<?php echo $field->info(); ?>
	 </span>
</li>
<?php
		}
}
?>