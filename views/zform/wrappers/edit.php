<?php
defined('SYSPATH') or die('No direct script access.');
echo Form::open();
echo $form;
echo Form::button('save', __('Save'), array(
    'type' => 'submit',
    'class' => 'btn btn-primary'
));
echo Form::close();