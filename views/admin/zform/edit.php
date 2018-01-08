<?php
defined('SYSPATH') or die('No direct script access.');
$form = new AppForm();
if (isset($errors)) {
    $form->errors = $errors;
}
?>
<div class="itabs">
	<ul>
		<li><a href="#tabs-1"><?= __($title)?> </a></li>
		<?php
if ($has_image) {
    ?>
		<li><a href="/<?= Request::current()->controller() ?>/img/<?= $id ?>"><?= __('img') ?> </a></li>
		<?php
}
if ($extra_links) {
    echo $extra_links;
}
if ($sub_link) {
    ?>
		<li><?= $sub_link; ?></li>
		<?php
}
?>
	</ul>
	<div id="tabs-1">
		<p>
			<?php
echo $content->generate_form();
?>
		</p>
	</div>
</div>
