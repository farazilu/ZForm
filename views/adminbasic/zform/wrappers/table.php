<?php
$controller = Request::current ()->controller ();
$action = Request::current ()->action ();
$is_ajax = Request::current ()->is_ajax ();
$list_form_loaded = isset ( $_REQUEST ['list-form-loaded'] ) ? $_REQUEST ['list-form-loaded'] : NULL;
$page = isset ( $_REQUEST ['page'] ) ? $_REQUEST ['page'] : NULL;
$result = '';
$list_per_page_array = array (
		10 => 10,
		20 => 20,
		30 => 30,
		50 => 50,
		100 => 100,
		200 => 200,
		500 => 500 
);
?>
<!-- 	<div class="form-wrapper"> -->
<!-- start of rendering  -->
<?php
echo Form::open ( URL::site ( '/' . $controller . '/' . $action ), array (
		'onsubmit' => "return list_form(this)",
		'id' => $id,
		'method' => 'get' 
) );
// }
$action_list = array (
		'select' => __ ( 'list_page_action_list_search' ),
		'delete' => __ ( 'list_page_action_list_delete' ),
		'export-csv' => __ ( 'list_page_action_list_export_csv' ),
		'export-OfficeOpenXML' => __ ( 'list_page_action_list_export_xml' ) 
);
?>


	<?php
	echo Form::hidden ( 'list-form-loaded', 1 );
	if ($button_action == 'delete') {
		echo Form::hidden ( 'button-action', 'delete-confirm', array (
				'id' => $id . '-action' 
		) );
		?>
<div class="col-sm-12 col-md-12">
	<div class="panel panel-danger">
		<div class="panel-body">
			<?= __('list_page_delete_confirm_message')?>
				<div class="btn-group pull-right">
   <?= HTML::anchor('/' . $controller  , __('reset_button_label'), array('class' => 'btn btn-default'))?>
    <?= Form::button('go', __('button_label_confirm'), array('type' => 'submit', 'class'=>'btn btn-danger'))?>
        </div>
		</div>
	</div>
</div>
<?php
	} else {
		?>
<div class="nav nav-tabs hidden-xs" id="nav-tabs">
	<div class="col-sm-6">
		<div class="input-group input-group-multi">
			<span class="input-group-addon input-group-addon-blank"><?=  Form::label($id . '-search', __('search_label'))?></span>
          	<?=Form::select('list-column', $select_list, $list_column, array('class' => 'form-control radius-left'))?>
       	
	           <?=Form::input('list-search', $list_search, array('id' => $id . '-search','class' => 'form-control'))?>
                <span class="input-group-btn"><?=Form::button('Search', __('search_button_label'), array('type' => 'submit','class' => 'btn btn-default'))?></span>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="input-group">
	<?=Form::select('button-action', $action_list, $button_action, array('id' => $id . '-action','class' => 'form-control'))?>
        <span class="input-group-btn"><?=Form::button('go', __('form_button_label_go'), array('type' => 'submit','class' => 'btn btn-default'))?></span>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="input-group">
			<span class="input-group-addon input-group-addon-blank"><?= Form::label($id . '-show', __('form_button_label_show')) ?>    </span>
    <?=Form::select('list-per-page', $list_per_page_array, $list_per_page, array('class' => 'form-control'))?>
        <span class="input-group-btn">
    <?=Form::button('go', __('form_button_label_go'), array('type' => 'submit','class' => 'btn btn-default'))?>
            </span>
		</div>
	</div>
</div>
<!-- 

<div class="nav nav-tabs hidden-xs" id="nav-tabs">
	<div class="col-sm-3">
		<div class="input-group">
			<span class="input-group-addon "><?=  Form::label($id . '-search', __('search_label'))?></span>
          	<?=Form::select('list-column', $select_list, $list_column, array('class' => 'form-control'))?>
       	</div>
	</div>
	<div class="col-sm-3">
		<div class="input-group">
	           <?=Form::input('list-search', $list_search, array('id' => $id . '-search','class' => 'form-control'))?>
                <span class="input-group-btn"><?=Form::button('Search', __('search_button_label'), array('type' => 'submit','class' => 'btn btn-default'))?></span>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="input-group">
	<?=Form::select('button-action', $action_list, $button_action, array('id' => $id . '-action','class' => 'form-control'))?>
        <span class="input-group-btn"><?=Form::button('go', __('form_button_label_go'), array('type' => 'submit','class' => 'btn btn-default'))?></span>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="input-group">
			<span class="input-group-addon"><?= Form::label($id . '-show', __('form_button_label_show')) ?>    </span>
    <?=Form::select('list-per-page', $list_per_page_array, $list_per_page, array('class' => 'form-control'))?>
        <span class="input-group-btn">
    <?=Form::button('go', __('form_button_label_go'), array('type' => 'submit','class' => 'btn btn-default'))?>
            </span>
		</div>
	</div>
</div>

 -->
<?php
	}
	?>
<div class="panel panel-default">
	<div class="panel-body panel-body-table">



<?php
// render pagination list
// echo $paging->render();
// format data for DataTable
$datatable = new Helper_Datatable ( $column_list, array (
		'paginator' => true,
		'class' => 'table table-striped table-hover table-product',
		'sortable' => 'true',
		'default_sort' => 'id' 
) );
if (count ( $data )) {
	$datatable->values ( $data );
	echo $datatable->render ( null, $primary_key, $primary_val );
} else {
	?>
	<p class="bg-info"><?=__ ( 'sorry-no-result-to-display' )?></p>
	<?php
}
// render pagination list
echo $paging->render ();
if (! empty ( $used_limit_message )) {
	?>    
<hr>
		<ul class="list-unstyled clearfix" id="limits">
			<li><?=$used_limit_message ?></li>
		</ul>
<?php
}
?>
	</div>
</div>
<?php
// if (!$is_ajax || (!$list_form_loaded && !$page)){
echo Form::close ();
?>
<!-- 	</div>  -->
<!-- end of rendering -->
<?php
// }
?>