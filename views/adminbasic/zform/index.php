<?php
defined ( 'SYSPATH' ) or die ( 'No direct script access.' );
if (isset ( $errors )) {
	$form->errors = $errors;
}
if (Redback::instance ()->fullResponse) {
	?>
<div class="ui-tabs relative">
	<div class="ui-tabs-panel">
			
	<?php
	if (isset ( $list )) {
		echo $list;
	}
	?>
		</div>
</div>
<?php
} else {
	echo $list;
}
?>