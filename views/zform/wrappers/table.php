<?php
$controller = Request::current()->controller();
$action = Request::current()->action();
$is_ajax = Request::current()->is_ajax();
$check = isset($_REQUEST['check']) ? $_REQUEST['check'] : NULL;
$list_search = isset($_REQUEST['list-search']) ? $_REQUEST['list-search'] : NULL;
$list_column = isset($_REQUEST['list-column']) ? $_REQUEST['list-column'] : NULL;
$list_per_page = isset($_REQUEST['list-per-page']) ? $_REQUEST['list-per-page'] : 30;
$list_form_loaded = isset($_REQUEST['list-form-loaded']) ? $_REQUEST['list-form-loaded'] : NULL;
$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : NULL;
$button_action = isset($_REQUEST['button-action']) ? $_REQUEST['button-action'] : NULL;
$result = '';
if (! $is_ajax || (! $list_form_loaded && ! $page)) {
    $base_link = Redback::instance()->get_link('js', TRUE, FALSE, FALSE);
    Redback::instance()->scripts($base_link . "list.js");
    if ($addNewButton) {
        ?>
<div class="container">
	<div class="ui-accordion-header ui-helper-reset ui-state-default  ui-corner-top sixteen columns alpha omega">
		<?= Html::anchor($controller. '/edit' ,'<span class="ui-button-text"> '.__('add_new').'</span>', array('class'=>"$tabClass ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only", 'title'=>'Add New' ));?>
	</div>
	<?php
    }
    ?>
	<div class="ui-widget-content sixteen columns alpha omega">
		<?php
    echo Form::open(URL::site('/' . $controller . '/' . $action), array(
        'onsubmit' => "return list_form(this)",
        'id' => $id,
        'method' => 'get'
    ));
}
$action_list = array(
    'select' => __('Select'),
    'delete' => __('Delete'),
    'export-csv' => __('Export CSV'),
    'export-OfficeOpenXML' => 'OfficeOpenXML'
);
?>
		<div class="row">
			<div class=" alpha three columns">
				<?php
    echo Form::hidden('list-form-loaded', 1);
    echo Form::label($id . '-search', 'Search');
    echo Form::select('list-column', $select_list, $list_column);
    ?>
			</div>
			<div class="three columns">
				<?php
    echo Form::input('list-search', $list_search, array(
        'id' => $id . '-search'
    ));
    ?>
			</div>
			<div class="two columns">
				<?php
    echo Form::button('Search', 'Search', array(
        'type' => 'submit'
    ));
    ?>
			</div>
			<div class="three columns">
				<?php
    echo Form::label($id . '-action', 'Action');
    if ($button_action == 'delete') {
        echo Form::hidden('button-action', 'delete-confirm', array(
            'id' => $id . '-action'
        ));
    } else {
        echo Form::select('button-action', $action_list, $button_action, array(
            'id' => $id . '-action'
        ));
    }
    ?>
			</div>
			<div class="two columns">
				<?php
    echo Form::button('go', 'Go', array(
        'type' => 'submit'
    ));
    ?>
			</div>
			<div class="three columns omega">
				<?php
    echo Form::label($id . '-show', 'Show');
    echo Form::input('list-per-page', $list_per_page, array(
        'id' => $id . '-show'
    ));
    ?>
			</div>
		</div>
		<?php
echo $paging->render();
// format data for DataTable
$datatable = new Helper_Datatable($column_list, array(
    'paginator' => true,
    'class' => 'table  ',
    'sortable' => 'true',
    'default_sort' => 'id'
));
$datatable->values($data);
echo $datatable->render();
echo $paging->render();
if (! $is_ajax || (! $list_form_loaded && ! $page)) {
    echo Form::close();
    ?>
	</div>
</div>
<?php
}
?>