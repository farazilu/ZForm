<?php
defined ( 'SYSPATH' ) or die ( 'No direct script access.' );
$onclick = Redback::instance ()->get_track_event ( 'Form', Request::$current->controller () . '-' . Request::$current->method (), 'save' );
?>
<div class="row2">
	<footer>
		<div class="container-fluid">
			<div class="row">
				<div class="hidden-xs col-sm-6"></div>
				<div class="col-xs-6 col-sm-3">
					<div class="form-group">
						<a class="btn btn-default btn-block" href="/<?=Request::$initial->controller()?>"><?=__('reset_button_label')?></a>
					</div>
				</div>
				<div class="col-xs-6 col-sm-3">
					<div class="form-group">
						<button id="save" type="submit" class="btn btn-primary pull-right btn-block" name="Save" value="<?=__('save_button_label')?>" onclick="<?=$onclick?>"><?=__('save_button_label')?></button>
					</div>
				</div>
			</div>
		</div>
	</footer>
</div>