<?php
defined('SYSPATH') or die('No direct script access.');
echo Form::open();
echo $form;
echo Form::button('save', __('Save'), array(
    'type' => 'submit',
    'class' => 'btn btn-default'
));
echo Form::close();