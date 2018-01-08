<?php
defined('SYSPATH') or die('No direct script access.');
if (isset($row)) {
    $controller = Request::current()->controller();
    ?>
<ol class="clearfix ui-corner-bottom">
	<li><?php echo HTML::anchor($controller.'/edit/'.$row['id'],'<span class="fa fa-edit"></span><span class="fa fa-text">Edit</span>' ,array('class'=> $_z_tag_link_type, 'title'=>__('generic_label_edit').' '.$row['id'] , 'data-id'=> $row['id']) ); ?></li>
	<?php //echo HTML::anchor($controller.'/view/'.$row['id'],'<span class="fa fa-search"></span><span class="fa fa-text">View</span>' ,array('title'=>__('View').' '.$row['id'],  'class'=>'sort-link-view ' ,'data-id'=> $row['id']   )); ?>
	<li><?php echo HTML::anchor($controller.'/del/'.$row['id'],'<span class="fa fa-trash-o"></span><span class="fa fa-text">Del</span>' ,array('title'=>__('Delete').' '.$row['id'],  'class'=>'sort-link-del sortLink ' ,'data-id'=> $row['id']   )); ?></li>
	<li><?php echo Form::checkbox("check[".$row['id']."]", $row['id'], in_array($row['id'], $check) , array()); ?></li>
</ol>
<?php
}
?>