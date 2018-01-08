<?php
defined ( 'SYSPATH' ) or die ( 'No direct script access.' );
if (! isset ( $title_info )) {
	$title_info = __ ( 'form_complete_fields_message' );
}
$form = new AppForm ();
if (isset ( $errors )) {
	$form->errors = $errors;
}
if (Redback::instance ()->fullResponse) {
	?>
<div <?php  if (!empty($has_image) || !empty($extra_links) || !empty($sub_link) ){?> id="tabs" <?php  }?> class="ui-tabs">
	<div class="ui-tabs-panel">

			<?php  if (!empty($has_image) || !empty($extra_links) || !empty($sub_link)){?>
			<ul class="nav nav-tabs" id="nav-tabs">
			<li><a href="#tabs-1"><?=__($title)?> </a></li>
				<?php
		if (! empty ( $has_image )) {
			?>
				<li><a href="/<?=Request::current()->controller()?>/img/<?=$content->pk()?>"><?=__('image_tab_title')?> </a></li>
				<?php
		}
		if (! empty ( $sub_link )) {
			?>
				<li><?=$sub_link?></li>
				<?php
		}
		?>
				<?=$extra_links?>
			</ul>
			<?php
	}
	?>
	<div class="tab-content">
	<?php 
	if (! empty ( $has_image ) || ! empty ( $extra_links ) || ! empty ( $sub_link )) {
		?>
         
			<div id="tabs-1">
				<?php
	}
}

if (isset ( $view_fileds )) {
	echo $content->generate_form ( $view_fileds );
} else {
	echo $content->generate_form ();
}
if (Redback::instance ()->fullResponse) {
	if (! empty ( $has_image ) || ! empty ( $extra_links ) || ! empty ( $sub_link )) {
		?>
			</div>
			<?php
	}
	?>
		</div>
	</div>
</div>
<?php
}
?>