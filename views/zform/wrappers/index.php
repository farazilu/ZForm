<?php
/**
 *
 * @author Faraz Ahmad
 * @package default
 */
$directory = Request::current()->directory();
$controller = Request::current()->controller();
?>
<div class="block">
	<div class="submenu">
		<ul>
			<li><?php echo HTML::anchor("/{$directory}/{$controller}/edit/" , __('Add new feature'))?></li>
		</ul>
		<br style="clear: both;">
	</div>
	<div class="content">
<?php
echo $paging->render();
// format data for DataTable
$data = array();
$merge = null;

foreach ($result as $entry) {
    $row = $entry->as_array();
    // reformat dates
    $row['created'] = Helper_Format::friendly_datetime($row['created']);
    $row['modified'] = Helper_Format::friendly_datetime($row['modified']);
    $row['last_login'] = Helper_Format::relative_time($row['last_login']);
    // $row['last_failed_login'] = Helper_Format::relative_time(strtotime($row['last_failed_login']));
    // add actions
    $directory = Request::current()->directory();
    $controller = Request::current()->controller();
    // $action = Request::current()->action();
    
    $row['actions'] = HTML::anchor("/{$directory}/{$controller}/edit/" . $row['id'], __('Edit')) . ' | ' . HTML::anchor("/{$directory}/{$controller}/delete/" . $row['id'], __('Delete'));
    // set roles
    
    $data[] = $row;
}

$column_list['actions'] = array(
    'label' => __('Actions'),
    'sortable' => false
);
$datatable = new Helper_Datatable($column_list, array(
    'paginator' => true,
    'class' => 'table',
    'sortable' => 'true',
    'default_sort' => 'username'
));
$datatable->values($data);
echo $datatable->render();
echo $paging->render();
?>
</div>
</div>