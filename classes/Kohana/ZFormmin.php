<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * ZForm: a form generation library for Kohana 3 ORM
 * 
 * @package ZForm
 * @author Azuka Okuleye
 * @copyright (c) 2011 Azuka Okuleye
 * @license http://zahymaka.com/license.html
 * @property array $zfields
 */
class Kohana_ZFormmin extends ZForm
{

    protected $_belongs_to = array();

    protected $_table_columns = array();

    protected $_loaded = TRUE;

    protected $_ajax_class = 'ajaxForm';

    /**
     * Overloaded __get() for zfields
     * 
     * @param string $column            
     * @return mixed
     */
    public function __get($column)
    {
        if ($column == 'zfields') {
            $this->setup_form();
            return $this->_z_fields;
        } elseif (array_key_exists($column, $this->_object)) {
            return (in_array($column, $this->_serialize_columns)) ? $this->_unserialize_value($this->_object[$column]) : $this->_object[$column];
        } else {
            return parent::__get($column);
        }
    }

    public function __set($column, $value)
    {
        $this->_object[$column] = $value;
    }

    /**
     * Returns the value of the primary key
     * 
     * @return mixed Primary key
     */
    public function pk()
    {
        return $this->_primary_key_value;
    }

    /**
     * Path in form array
     * 
     * @param string $column            
     * @return string
     */
    public function form_id()
    {
        $id = $this->loaded() ? $this->_z_orm_name . '.' . $this->pk() . '.form' : $this->_z_orm_name . '.form';
        return str_replace('.', '_', $id);
    }

    /**
     * I think this is specifically for the days dropdown
     * 
     * @param array $array            
     * @param int $length            
     * @return array
     */
    public static function zerofill($array, $length = 2)
    {
        foreach ($array as $key => $value) {
            $array[$key] = str_pad($value, $length, '0', STR_PAD_LEFT);
        }
        return $array;
    }

    /**
     * Validates the current model's data
     * 
     * @param Validation $extra_validation
     *            Validation object
     * @return ORM
     */
    public function check(Validation $extra_validation = NULL)
    {
        // Determine if any external validation failed
        $extra_errors = ($extra_validation and ! $extra_validation->check());
        // Always build a new validation object
        $this->_validation();
        $array = $this->_validation;
        if (($this->_valid = $array->check()) === FALSE or $extra_errors) {
            $exception = new ORM_Validation_Exception($this->errors_filename(), $array);
            if ($extra_errors) {
                // Merge any possible errors from the external object
                $exception->add_object('_external', $extra_validation);
            }
            throw $exception;
        }
        return $this;
    }

    /**
     * Initializes validation rules, and labels
     * 
     * @return void
     */
    protected function _validation()
    {
        // Build the validation object with its rules
        $this->_validation = Validation::factory($this->_object)->bind(':model', $this)
            ->bind(':original_values', $this->_original_values)
            ->bind(':changed', $this->_changed);
        foreach ($this->rules() as $field => $rules) {
            $this->_validation->rules($field, $rules);
        }
        // Use column names by default for labels
        $columns = array_keys($this->_table_columns);
        // Merge user-defined labels
        $labels = array_merge(array_combine($columns, $columns), $this->labels());
        foreach ($labels as $field => $label) {
            $this->_validation->label($field, $label);
        }
    }

    public function loaded()
    {
        return $this->_loaded;
    }

    /**
     * Rule definitions for validation
     * 
     * @return array
     */
    public function rules()
    {
        return array();
    }

    /**
     * Updates or Creates the record depending on loaded()
     * @chainable
     * 
     * @param Validation $validation
     *            Validation object
     * @return ORM
     */
    public function save(Validation $validation = NULL)
    {
        return $this->loaded() ? $this->update($validation) : $this->create($validation);
    }
}