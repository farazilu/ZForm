<?php
defined('SYSPATH') or die('No direct script access.');
if (isset($errors)) {
    $form->errors = $errors;
}
if (isset($content)) {
    $content->get_form();
}

