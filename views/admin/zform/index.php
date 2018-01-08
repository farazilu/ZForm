<?php
defined ( 'SYSPATH' ) or die ( 'No direct script access.' );
if (isset ( $errors )) {
	$form->errors = $errors;
}
if (Redback::instance ()->fullResponse) {
	?>
<div id="tabs">
	<ul class="nav nav-tabs" id="nav-tabs">
		<li><a href="#tabs-1"><?= __($title) ?> </a></li>
	</ul>
	<div class="tab-content">
		<div id="tabs-1">
		<?php 	echo $list;  ?>
	</div>
	</div>
</div>
<?php
} else {
	echo $list;
}
?>