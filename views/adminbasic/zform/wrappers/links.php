<?php
defined ( 'SYSPATH' ) or die ( 'No direct script access.' );
if ($row ['id']) {
	// only show delete option if allowed on object (e.g. order and users are not allowed to delete)
	if (empty ( $url )) {
		$controller = Request::current ()->controller ();
		$url = "{$controller}/del/{$row['id']}";
	}
	
	$title = __ ( 'generic_label_delete' );
	echo HTML::anchor ( $url, "<span class=\"fa fa-trash-o\" aria-hidden=\"true\"></span><span class=\"sr-only\">{$title}</span>", array (
			'title' => __ ( 'generic_label_delete' ) . ' ' . $row ['id'],
			'class' => 'sort-link-del sortLink ',
			'data-id' => $row ['id'] 
	) );
}

/*
 * if (isset ( $row )) {
 * $controller = Request::current ()->controller ();
 * ?>
 * <div class="relative">
 * <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"><span class="hidden-xs"><?=__('genertic_table_title_actions')?></span><b class="icon icon-icon07 hidden-sm hidden-md hidden-lg"></b><b class="caret"></b></a>
 * <ul class="dropdown-menu pull-right">
 * <?php
 * if (empty ( $check )) {
 * ?>
 * <li><?=HTML::anchor($controller.'/edit/'.$row['id'],__('generic_label_edit') ,array( 'title'=>__('generic_label_edit').' '.$row['id'] , 'data-id'=> $row['id']) ); ?></li>
 * <?php //echo HTML::anchor($controller.'/view/'.$row['id'],'<span class="fa fa-search"></span><span class="fa fa-text">View</span>' ,array('title'=>__('View').' '.$row['id'], 'class'=>'sort-link-view ' ,'data-id'=> $row['id'] )); ?>
 * <?php
 * if ($_z_config_delete) {
 * if (in_array ( $row ['id'], $check )) {
 * ?>
 * <li><?=HTML::anchor($controller.'/del/'.$row['id'].'?confirm=yes',__('generic_label_delete') ,array('title'=>__('generic_label_delete').' '.$row['id'], 'class'=>'sort-link-del sortLink ' ,'data-id'=> $row['id'] )); ?></li>
 * <?php
 * } else {
 * ?>
 * <li><?=HTML::anchor($controller.'/del/'.$row['id'],__('generic_label_delete') ,array('title'=>__('generic_label_delete').' '.$row['id'], 'class'=>'sort-link-del sortLink ' ,'data-id'=> $row['id'] )); ?></li>
 * <?php
 * }
 * }
 * }
 * /*
 * if ($_z_config_delete) {
 * ?>
 * <li><?=Form::checkbox("check[".$row['id']."]", $row['id'], in_array($row['id'], $check) , array()); ?></li>
 * <?php
 * }
 * * /
 * ?>
 *
 * </ul>
 *
 * </div>
 * <?php
 * }
 */
?>