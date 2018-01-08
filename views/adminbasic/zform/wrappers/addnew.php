<?php
defined ( 'SYSPATH' ) or die ( 'No direct script access.' );
?>
<a class="btn btn-preview" href="/<?= Request::initial()->controller() ?>/edit" title="<?= __('button_add_new_title') ?>"><?= __('add_new') ?></a>