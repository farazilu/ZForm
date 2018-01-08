<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * ZForm: a select/multiselect implementation.
 * Displays a select, group of radios or group of checkboxes
 *
 * @package ZForm
 * @category Field
 * @author Azuka Okuleye
 * @copyright (c) 2011 Azuka Okuleye
 * @license http://zahymaka.com/license.html
 */
class Kohana_ZForm_Field_Enum extends ZForm_Field
{

    protected $_config = array(
        'multiple' => false,
        'multichoice' => false,
        'options' => array(),
        'format' => 'select',
        'separator' => '',
        'bubble-info' => false,
        'bubble-required' => false
    );

    protected function _set_value($value)
    {
        if (! is_array($value) && $this->_config['multiple']) {
            $value = explode($this->_config['separator'], $value);
        }
        parent::_set_value($value);
    }

    public function db_value()
    {
        if (is_array($this->_value))
            return join($this->_config['separator'], $this->_value);
        return parent::db_value();
    }

    public function get_options()
    {
        return (isset($this->_config['options'])) ? $this->_config['options'] : [];
    }

    public function render()
    {
        if (isset($this->_extra['is_nullable']) && $this->_extra['is_nullable'] === false) {
            $this->_attributes['required'] = 'required';
        }
        $render = '';
        if (! is_array($this->_config['options'])) {
            $this->_config['options'] = array_combine($this->_extra[$this->_config['options']], $this->_extra[$this->_config['options']]);
        }
        switch ($this->_config['format']) {
            case 'select':
                
                // Multiple
                if ($this->_config['multiple']) {
                    if ($this->_readonly) {
                        $keys = array_keys($this->_config['options']);
                        if (is_array($this->_value)) {
                            foreach ($this->_value as $val) {
                                if (! empty($val)) {
                                    $render .= $this->_config['options'][$val] . ', ';
                                }
                            }
                        } else {
                            $render = $this->_config['options'][$this->_value];
                        }
                        if (is_array($this->_value)) {
                            $id = $this->_id . '_' . array_search($val, $keys);
                            foreach ($this->_value as $val) {
                                $render .= Form::hidden($this->_name . "[]", $val, $this->_attributes + array(
                                    'id' => $id
                                ));
                            }
                        } else {
                            $render .= Form::hidden($this->_name, $this->_value, $this->_attributes + array(
                                'id' => $this->_id
                            ));
                        }
                        break;
                    }
                    $render = Form::select($this->_name . '[]', $this->_config['options'], (array) $this->_value, $this->_attributes + array(
                        'id' => $this->_id,
                        'class' => 'form-control chosen-select',
                        'data-placeholder' => __($this->_label)
                    ));
                    break;
                }
                // Multichoice
                if ($this->_config['multichoice']) {
                    $render = ZForm::multichoice($this->_name, $this->_config['options'], $this->_value, $this->_attributes + array(
                        'id' => $this->_id,
                        'class' => 'form-control chosen-select',
                        'data-placeholder' => __($this->_label)
                    ));
                    break;
                }
                // single
                if ($this->_readonly) {
                    if (isset($value)) {
                        $render = $this->_config['options'][$value];
                    } else {
                        $render = '';
                    }
                    $render .= Form::hidden($this->_name, $this->_value, $this->_attributes + array(
                        'id' => $this->_id
                    ));
                    break;
                }
                // Single
                // echo Debug::vars($this);
                $render = Form::select($this->_name, $this->_config['options'], $this->_value, $this->_attributes + array(
                    'id' => $this->_id,
                    'class' => 'form-control',
                    'data-placeholder' => __($this->_label)
                ));
                break;
            case 'select_edit':
                
                // Multiple
                if ($this->_config['multiple']) {
                    if ($this->_readonly) {
                        $keys = array_keys($this->_config['options']);
                        if (is_array($this->_value)) {
                            foreach ($this->_value as $val) {
                                if (! empty($val)) {
                                    $render .= $this->_config['options'][$val] . ', ';
                                }
                            }
                        } else {
                            $render = $this->_config['options'][$this->_value];
                        }
                        if (is_array($this->_value)) {
                            $id = $this->_id . '_' . array_search($val, $keys);
                            foreach ($this->_value as $val) {
                                $render .= Form::hidden($this->_name . "[]", $val, $this->_attributes + array(
                                    'id' => $id
                                ));
                            }
                        } else {
                            $render .= Form::hidden($this->_name, $this->_value, $this->_attributes + array(
                                'id' => $this->_id
                            ));
                        }
                        break;
                    }
                    $render = Form::select($this->_name . '[]', $this->_config['options'], (array) $this->_value, $this->_attributes + array(
                        'id' => $this->_id,
                        'class' => 'form-control'
                    ));
                    break;
                }
                // Multichoice
                if ($this->_config['multichoice']) {
                    $render = ZForm::multichoice($this->_name, $this->_config['options'], $this->_value, $this->_attributes + array(
                        'id' => $this->_id,
                        'class' => 'form-control'
                    ));
                    break;
                }
                // single
                if ($this->_readonly) {
                    if (isset($value)) {
                        $render = $this->_config['options'][$value];
                    } else {
                        $render = '';
                    }
                    $render .= Form::hidden($this->_name, $this->_value, $this->_attributes + array(
                        'id' => $this->_id
                    ));
                    break;
                }
                // Single
                if ($this->_value) {
                    $render = '<div class="input-group add-margin-bottom">';
                }
                $render .= Form::select($this->_name, $this->_config['options'], $this->_value, $this->_attributes + array(
                    'id' => $this->_id,
                    'class' => 'form-control'
                ));
                if ($this->_value) {
                    // echo Debug::vars($this->_value);
                    $render .= '<span class="input-group-btn">';
                    $render .= HTML::anchor("/Admin_bar/layout/{$this->_value}", __('form_layout_edit_segment_label'), array(
                        'class' => 'btn btn-default'
                    ));
                    $render .= '</span>';
                }
                if ($this->_value) {
                    $render .= '</div>';
                }
                break;
            default:
                $keys = array_keys($this->_config['options']);
                if ($this->_readonly) {
                    if (is_array($this->_value)) {
                        foreach ($this->_value as $val) {
                            if (! empty($val)) {
                                $render .= $this->_config['options'][$val] . ', ';
                            }
                        }
                    } else {
                        $render = $this->_config['options'][$this->_value];
                    }
                    if (is_array($this->_value)) {
                        foreach ($this->_value as $val) {
                            $id = $this->_id . '_' . array_search($val, $keys);
                            $render .= Form::hidden($this->_name . "[]", $val, $this->_attributes + array(
                                'id' => $id
                            ));
                        }
                    } else {
                        $render .= Form::hidden($this->_name, $this->_value, $this->_attributes + array(
                            'id' => $this->_id
                        ));
                    }
                    break;
                } else {
                    $first = true;
                    $i = 0;
                    foreach ($this->_config['options'] as $option => $label) {
                        $render .= '<span class="enum-item">';
                        $id = $this->_id . '_' . array_search($option, $keys);
                        // Multiple (checkboxes)
                        if ($this->_config['multiple']) {
                            $render .= Form::checkbox($this->_name . '[' . $option . ']', $option, in_array($option, $this->_value), $this->_attributes + array(
                                'id' => $id
                            ));
                            // Single value (radio)
                        } else {
                            $render .= Form::radio($this->_name, $option, 
                                // Always choose the first if nothing is set
                                $this->_value == $option or ($first and ! $this->_value), $this->_attributes + array(
                                    'id' => $id
                                ));
                        }
                        $render .= Form::label($id, $label);
                        $render .= '</span>';
                        $first = false;
                    }
                    $render = '<div class="enum">' . $render . '</div>';
                }
        }
        return $render;
    }
}