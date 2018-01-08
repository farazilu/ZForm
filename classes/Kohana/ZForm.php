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
 *
 */
class Kohana_ZForm extends ORM
{

    // <editor-fold desc="Protected members">
    protected $_primary_val = NULL;

    /**
     * Individual field configurations
     *
     * @var array
     */
    protected $_z_field_config = array();

    /**
     * Form labels
     *
     * @var array
     */
    protected $_z_labels = array();

    /**
     * Form fields Info
     *
     * @var array
     */
    protected $_z_info = array();

    /**
     * Fields to exclude from the form
     *
     * @var array
     */
    protected $_z_exclude = array();

    /**
     * Fields to exclude from the table
     *
     * @var array
     */
    protected $_z_exclude_table = array();

    /**
     * Filter to show from table reverse to z_exclude_table if this is set will be used
     *
     * @var array
     */
    protected $_z_show_table = array();

    /**
     * Additional field configuration.
     *
     * @var array
     */
    protected $_z_fields = array();

    /**
     * Extra attributes for form fields
     *
     * @var array
     */
    protected $_z_attributes = array();

    /**
     * Name of object (post key).
     * So for a user model you'll get user[username]
     *
     * @var string
     */
    protected $_z_orm_name = NULL;

    /**
     * Check if any Many to Many field changed
     *
     * @var array
     */
    protected $_z_changed = array();

    /**
     * Init called.
     *
     * @var boolean
     */
    protected $_z_inited = false;

    // </editor-fold>
    /**
     * Store erroes
     *
     * @var array
     */
    protected $_z_errors = array();

    /**
     * check what kind of pags should open multi tabs / singla tab/ or no tabs view.
     *
     * @var String
     * @example tabLinkSingle | tabLink | ''
     */
    protected $_z_add_new_button = TRUE;

    /**
     * if a table has tocked ticked then system will maked these fields read only.
     *
     * @var array
     */
    protected $_z_locked_fields = array();

    /**
     * set if user will be displayed delete link and check box on list page
     *
     * @var boolean
     */
    protected $_z_config_delete = true;

    public $_feature_id = 0;

    protected $_used_limit = FALSE;

    protected $_used_limit_message = NULL;

    /**
     * values that are not html sanitised using xss clean
     *
     * @var array
     */
    protected $_z_not_secure = array();

    /**
     * decide if the form should be horizontal or not
     * full| horizontal| mini
     *
     * @var string
     */
    protected $form_laytout = 'full';

    /**
     * set true to use localise translations
     *
     * @var bool
     */
    protected $_localise = true;

    protected $_img_folder_name;

    /**
     * Setup.
     * Initializes all non-hidden fields
     *
     * @return $this
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        $this->initialize();
    }

    public function primary_val()
    {
        if (! empty($this->_primary_val)) {
            return $this->_primary_val;
        }
        return $this->_primary_key;
    }

    public function primary_val_value()
    {
        if (! empty($this->_primary_val)) {
            $name = $this->_primary_val;
            return $this->$name;
        }
        return $this->_primary_key_value;
    }

    public function setup_form()
    {
        if ($this->_z_inited) {
            return $this;
        }
        $this->_z_initialize();
        $this->_z_create_fields();
        foreach ($this->_z_fields as $column => $field) {
            /* @var $field ZForm_Field */
            // Loaded or changed, set field value
            if (isset($this->_changed[$column]) || $this->loaded()) {
                $field->value = $this->zget($column);
                // print_r($field->value);
            } else {
                // Use default value
                $field->set_default();
            }
        }
        $this->finalize();
        $this->_z_finalize();
        $this->_z_inited = true;
        
        return $this;
    }

    /**
     * Set form key
     *
     * @param string $orm_name
     * @return Kohana_ZForm
     */
    public function set_name($orm_name)
    {
        $this->_z_orm_name = $orm_name;
        return $this;
    }

    /**
     * Exclude column
     *
     * @param string $column
     * @return Kohana_ZForm
     */
    public function exclude($column)
    {
        $this->_z_exclude[] = $column;
        return $this;
    }

    /**
     *
     * @param mixed $columns
     * @param boolean $form
     * @param full|horizontal|mini $form_laytout
     *
     * @return string
     */
    public function generate_form($columns = NULL, $form = TRUE)
    {
        try {
            $render = '';
            
            $this->setup_form();
            if ($this->_z_inited) {
                if (Request::$current->method() === Request::POST) {
                    $this->_z_errors = $this->errors();
                }
                if (! $columns) {
                    $all_columns = Arr::merge($this->_table_columns, $this->_has_many);
                    // All columns except excluded
                    $columns = array_keys(Arr::extract($all_columns, array_filter(array_keys($all_columns), array(
                        $this,
                        '_z_filter'
                    ))));
                } elseif (! is_array($columns)) {
                    $columns = func_get_args();
                }
                // print_r($columns);
                // print_r($this);
                // die;
                /*
                 * generate form
                 */
                
                if ($form && Redback::instance()->fullResponse) {
                    // $render .= '<div class="form-wrapper">';
                }
                // check if we need to wrap worm element
                if ($form) {
                    
                    $render .= $this->form_open();
                }
                if (! empty($this->_used_limit_message) && $form) {
                    $render .= '<div class="well">';
                    $render .= $this->_used_limit_message;
                    $render .= '</div>';
                }
                foreach ($columns as $column) {
                    if (! isset($this->_z_fields[$column]))
                        continue;
                    if ($this->_z_fields[$column] instanceof ZForm_Field_Image)
                        continue;
                    $error = isset($this->_z_errors[$column]) ? $this->_z_errors[$column] : '';
                    $this->_z_fields[$column]->set_error($error);
                    $render .= $this->_z_fields[$column]->single_field(array(
                        'class' => 'form-group ',
                        'id' => 'field_' . $this->field_id($column)
                    ));
                }
                
                // $render .=Form::hidden('X-Return-Type','xml');
                if ($form) {
                    $render .= $this->form_close();
                }
                if ($form && Redback::instance()->fullResponse) {
                    // $render .= '</div>';
                }
            }
        } catch (Kohana_Exception $e) {
            $render .= '<div class="alert alert-danger" role="alert">';
            $render .= $e->getMessage();
            $render .= '</div>';
        }
        return $render;
    }

    /**
     *
     * @param full|horizontal|mini $form_laytout
     */
    public function form_set_layout($form_laytout = 'full')
    {
        $this->form_laytout = $form_laytout;
    }

    /**
     * open HTML form for current ORM model
     *
     * @param array $attributes
     * @return string
     */
    public function form_open($attributes = [])
    {
        $this->setup_form();
        if ($this->_z_inited) {
            $request = Request::current();
            $url = '/' . $request->controller() . '/' . $request->action() . '/' . $this->pk();
            $attributes['data-response'] = 'html';
            $attributes['id'] = $this->form_id();
            if ($this->pk()) {
                $attributes['data-id'] = $this->pk();
            }
            return Form::open($url, $attributes);
        }
    }

    public function form_close($footer = true)
    {
        $render = '';
        if ($this->_z_inited) {
            if ($footer) {
                // $render .= '<footer><div class="container-fluid"><div class="col-md-12">';
                $render .= View::factory('adminbasic/zform/save');
                // $render .= '</div></div></footer>';
            }
            $render .= Form::close();
        }
        return $render;
    }

    public function generate_image_form($columns = NULL, $form = TRUE)
    {
        $this->setup_form();
        $this->_z_errors = $this->errors();
        if (! $columns) {
            // All columns except excluded
            $columns = array_keys($this->_table_columns);
        } elseif (! is_array($columns)) {
            $columns = func_get_args();
        }
        $render = '';
        if ($form && Redback::instance()->fullResponse) {
            // $render .= '<div class="form-wrapper">';
        }
        // check if we need to wrap worm element
        if ($form) {
            $request = Request::current();
            $url = '/' . $request->controller() . '/' . $request->action() . '/' . $this->pk();
            $attributes = array();
            $attributes['data-response'] = 'html';
            $attributes['id'] = $this->form_id();
            $attributes['enctype'] = 'multipart/form-data';
            if ($this->pk()) {
                $attributes['data-id'] = $this->pk();
            }
            $render .= Form::open($url, $attributes);
        }
        $render .= '<ul class="list-unstyled">';
        if (! empty($this->_used_limit_message)) {
            $render .= '<li>';
            $render .= $this->_used_limit_message;
            $render .= '</li>';
        }
        foreach ($columns as $column) {
            if (! isset($this->_z_fields[$column]))
                continue;
            if ($this->_z_fields[$column] instanceof ZForm_Field_Image) {
                $error = isset($this->_z_errors[$column]) ? $this->_z_errors[$column] : '';
                $this->_z_fields[$column]->set_error($error);
                $render .= $this->_z_fields[$column]->single_field(array(
                    'class' => 'form-group ',
                    'id' => 'field_' . $this->field_id($column)
                ));
            }
        }
        $render .= '<li>';
        // $render .=Form::hidden('X-Return-Type','xml');
        $render .= '<ul class="list-unstyled task-actions clearfix" ><li>';
        $render .= Form::input('Reset', __('Reset'), array(
            'type' => 'reset',
            'class' => 'btn btn-default'
        ));
        $render .= '</li><li class="pull-right">';
        $render .= Form::submit('Save', __('save_button_label'), array(
            'class' => 'btn btn-secondary'
        ));
        $render .= '</li></ul>';
        $render .= '</li>';
        $render .= '</ul>';
        if ($form) {
            $render .= Form::close();
        }
        if ($form && Redback::instance()->fullResponse) {
            // $render .= '</div>';
        }
        return $render;
    }

    /*
     *
     */
    public function generate_JSON($columns = NULL)
    {
        $this->setup_form();
        $this->_z_errors = $this->errors();
        if (! $columns) {
            $all_columns = Arr::merge($this->_table_columns, $this->_has_many);
            // All columns except excluded
            $columns = array_keys(Arr::extract($all_columns, array_filter(array_keys($all_columns), array(
                $this,
                '_z_filter'
            ))));
        } elseif (! is_array($columns)) {
            $columns = func_get_args();
        }
        $render = array();
        $render['form'] = $this->form_id();
        $render['id'] = $this->pk();
        $render['values'] = array();
        foreach ($columns as $column) {
            if (! isset($this->_z_fields[$column]))
                continue;
            $error = isset($this->_z_errors[$column]) ? $this->_z_errors[$column] : '';
            $this->_z_fields[$column]->set_error($error);
            $return = $this->_z_fields[$column]->single_field_json(array());
            $render['values'] = Arr::merge($render['values'], $return);
        }
        return array(
            'form' => $render
        );
    }

    /**
     *
     * @param mixed $columns,...
     * @return string
     */
    /*
     * public function generate_XML($columns = NULL){ $this->setup_form(); $this->_z_errors = $this->errors(); if (!$columns){ $all_columns = Arr::merge($this->_table_columns, $this->_has_many); // All columns except excluded $columns = array_keys(Arr::extract($all_columns, array_filter(array_keys($all_columns), array($this, '_z_filter')))); } elseif (!is_array($columns)){ $columns = func_get_args(); } $render = ''; $render .= '<form id="'.$this->form_id().'">'; $render .= '<id>'.$this->pk().'</id>'; foreach ($columns as $column){ if (!isset($this->_z_fields[$column])) continue; $error = isset($this->_z_errors[$column]) ? $this->_z_errors[$column] : ''; $this->_z_fields[$column]->set_error($error); $render .= $this->_z_fields[$column]->single_field_XML(array('class' => 'form-group '.$column, 'id' => 'field_'.$this->field_id($column)) ); } $render .= '</form>'; return $render; }
     */
    /**
     * Get the form label for a specific field
     *
     * @param string $field
     * @throws ErrorException
     * @return string
     */
    public function form_label($field)
    {
        return $this->zfields[$field]->form_label();
    }

    /**
     * Get the form field for a specific field
     *
     * @param string $field
     * @throws ErrorException
     * @return string
     */
    public function form_field($field)
    {
        return $this->zfields[$field]->form_field();
    }

    /**
     *
     * @param type $array
     *            post array default NULL
     * @param type $columns
     * @return Kohana_ZForm
     */
    public function get_form($array = NULL, $columns = NULL)
    {
        $this->setup_form();
        $_has_many = array_filter(array_keys($this->_has_many), array(
            $this,
            '_z_filter'
        ));
        
        if (! $array)
            $array = Request::current()->post();
        if (! $columns) {
            // All columns except excluded
            $_table_columns = array_keys(Arr::extract($this->_table_columns, array_filter(array_keys($this->_table_columns), array(
                $this,
                '_z_filter'
            ))));
            
            $columns = array_merge($_table_columns, $_has_many);
        } elseif (! is_array($columns)) {
            $columns = array_shift(func_get_args());
        }
        foreach ($columns as $column) {
            if (! isset($this->_z_fields[$column]))
                continue;
            $value = Arr::path($array, $this->field_path($column));
            // echo Debug::vars ( $this->field_path ( $column ), $value, $array, $value );
            // die ();
            // echo Debug::vars($value);
            // check if the value in not submitted in form.
            if (is_null($value))
                continue;
            if (! in_array($column, $this->_z_not_secure)) {
                // clean the string for security
                $value = Security::xss_clean($value);
            }
            if ($this->_z_fields[$column] instanceof ZForm_Field_Password) {
                if (! empty($value)) {
                    $this->_z_fields[$column]->value = $value;
                    $this->$column = $this->_z_fields[$column]->db_value();
                }
            } else {
                if (in_array($column, $_has_many)) {
                    if ($this->_z_fields[$column]->value != $value) {
                        $this->_z_fields[$column]->value = $value;
                        $this->_z_changed[$column] = $column;
                    }
                } else {
                    $this->_z_fields[$column]->value = $value;
                    $this->$column = $this->_z_fields[$column]->db_value();
                }
            }
        }
        // // read many to many relation
        // $_has_many = array_filter(array_keys($this->_has_many), array(
        // $this,
        // '_z_filter'
        // ));
        // foreach ($columns as $column) {
        
        // if (! isset($this->_z_fields[$column]))
        // continue;
        // $value = Arr::path($array, $this->field_path($column));
        // // check if changes
        // // echo Debug::vars($this->_z_fields[$column]->value, $value);
        // if ($this->_z_fields[$column]->value != $value) {
        // $this->_z_fields[$column]->value = $value;
        // $this->_z_changed[$column] = $column;
        // }
        // }
        
        // echo Debug::vars($this->_z_fields, $array, $columns, $this->_z_changed);
        // die();
        return $this;
    }

    public function z_save(Validation $validation = NULL)
    {
        /**
         * check if user is stil in allowed limit or if not feature is not live.
         */
        $_pk = $this->pk();
        if (! $this->_used_limit || ($this->_used_limit && isset($this->live) && (! isset($this->_changed['live']) || empty($this->live))) || ($this->_used_limit && ! isset($this->live) && $this->pk())) {
            try {
                // echo Debug::vars($this);
                // echo Debug::vars($this->_z_changed);
                // die();
                
                $this->save($validation);
                if ($this->pk() && $this->_z_changed) {
                    foreach ($this->_z_changed as $column) {
                        if (isset($this->_z_fields[$column])) {
                            // don't use db_value as it flaten the array for Enum
                            $value = $this->_z_fields[$column]->value;
                            // if ($this->_has_many[$column]['through']) {
                            $this->remove($column);
                            $this->add($column, $value);
                            $this->_saved = true;
                            // } else {}
                        }
                    }
                    $this->_z_changed = array();
                }
                
                if ($this->saved()) {
                    Message::add('success', __('generic-form-saved-message'));
                }
                $_pk2 = $this->pk();
                if (empty($_pk) && ! empty($_pk2)) {
                    $path = Route::url('kommand', array(
                        'controller' => Request::current()->controller(),
                        'action' => Request::current()->action(),
                        'id' => $this->pk()
                    ));
                    Redirect::redirect($path);
                }
                return TRUE;
            } catch (ORM_Validation_Exception $e) {
                $message = '';
                $errors = $this->errors();
                foreach ($errors as $name => $error) {
                    $message .= "$error ";
                }
                Message::add(Message::error, __('generic_error_message', array(
                    ':error_message' => $message
                )));
                return FALSE;
            } catch (Database_Exception $e) {
                switch ($e->getCode()) {
                    case 1062:
                        Message::add(Message::error, __('generic_unique_value_error'));
                        break;
                    case 1452:
                        // echo Debug::vars($e);
                        Message::add(Message::error, __('generic_unique_fk_missing_error'));
                        break;
                    default:
                        Message::add(Message::error, __('generic_error_message', array(
                            ':error_message' => $e->getMessage()
                        )));
                }
            }
        } else {
            Message::add(Message::error, __('allowed_limit_reached_for_feature'));
            return FALSE;
        }
    }

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
        } else {
            return parent::__get($column);
        }
    }

    public function zget($column)
    {
        if ($column == 'zfields') {
            $this->setup_form();
            return $this->_z_fields;
        } elseif (isset($this->_has_many[$column])) {
            // echo Debug::vars($this);
            // die();
            if ($this->_has_many[$column]['through']) {
                
                $query = DB::select($this->_has_many[$column]['far_key'])->from($this->_has_many[$column]['through'])->where($this->_has_many[$column]['foreign_key'], '=', $this->pk());
                if (isset($this->_has_many[$column]['db_group'])) {
                    $results = $query->execute($this->_has_many[$column]['db_group']);
                } else {
                    $results = $query->execute($this->_db);
                }
                return array_keys($results->as_array($this->_has_many[$column]['far_key']));
            } else {
                // echo Debug::vars($this);
                // die();
                $model = ORM::factory($this->_has_many[$column]['model']);
                $model->where($this->_has_many[$column]['foreign_key'], '=', $this->pk());
                return array_keys($model->find_all()->as_array($model->primary_key()));
            }
            // read table values
        } else {
            return parent::__get($column);
        }
    }

    public function labels()
    {
        if (! $this->_z_inited)
            return parent::labels();
        return $this->_z_labels;
    }

    /**
     * Get a list of errors after validating.
     * Parse using error config
     *
     * @return string Error message
     *         @usage <code>$this->errors();</code>
     *         @usage <code>$this->errors('Alert::error', 'es', 'models');</code>
     */
    public function errors($callback = NULL, $language = TRUE, $directory = 'models')
    {
        try {
            $this->check();
            return array();
        } catch (ORM_Validation_Exception $ex) {
            $messages = $ex->errors($directory, $language = NULL);
            if ($callback && is_callable($callback)) {
                return join("\n", array_map($callback, $messages));
            }
            return $messages;
        }
    }

    /**
     * Set any field options beforehand
     */
    public function initialize()
    {}

    /**
     * Finish up after fields are loaded
     */
    public function finalize()
    {
        $locked = FALSE;
        // check if locked field exists and if its ticked
        if (isset($this->_z_fields['locked'])) {
            $locked = (boolean) $this->_z_fields['locked']->value;
        }
        foreach ($this->_z_fields as $name => $field) {
            if ($locked && in_array($name, $this->_z_locked_fields)) {
                $field->__set('readonly', TRUE);
            }
        }
    }

    /**
     * final finalzation
     */
    public function _z_finalize()
    {}

    /**
     * Field form name
     *
     * @param string $field
     * @return string
     */
    public function field_name($column)
    {
        return $this->loaded() ? $this->_z_orm_name . '[' . $this->pk() . '][' . $column . ']' : $this->_z_orm_name . '[' . $column . ']';
    }

    /**
     * Path in form array
     *
     * @param string $column
     * @return string
     */
    public function field_path($column)
    {
        return $this->loaded() ? $this->_z_orm_name . '.' . $this->pk() . '.' . $column : $this->_z_orm_name . '.' . $column;
    }

    /**
     * Form ID
     *
     * @param string $field
     * @return string
     */
    public function field_id($column)
    {
        return str_replace('.', '_', $this->field_path($column));
    }

    /**
     * Path in form array
     *
     * @param string $column
     * @return string
     */
    public function form_id()
    {
        $action = Request::$current->action();
        $id = $this->loaded() ? "{$this->_z_orm_name}.{$this->pk()}.form.{$action}" : "{$this->_z_orm_name}.form.{$action}";
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
     * Select function with weighted items and separators e.g.
     * Countries: [United States][Canada][------][Afghanistan]...[United States]...
     *
     * @param string $name
     * @param array $options
     * @param mixed $selected
     * @param array $attributes
     * @param string $separator
     * @return string
     */
    public static function multichoice($name, array $options = NULL, $selected = NULL, array $attributes = NULL, $separator = NULL)
    {
        $iselected = false;
        $first = true;
        $aoptions = '';
        $attributes['name'] = $name;
        if ($separator === NULL)
            $separator = '----------';
        foreach ((array) $options as $loptions) {
            if (! $first && $separator) {
                $aoptions .= '<option disabled="disabled">' . $separator . '</option>';
            } else {
                $first = false;
            }
            if (empty($loptions)) {
                // There are no options
                $loptions = '';
            } else {
                foreach ($loptions as $value => $name) {
                    if (is_array($name)) {
                        // Create a new optgroup
                        $group = array(
                            'label' => $value
                        );
                        // Create a new list of options
                        $_options = array();
                        foreach ($name as $_value => $_name) {
                            // Create a new attribute set for this option
                            $option = array(
                                'value' => $_value
                            );
                            if ($_value == $selected && ! $iselected) {
                                // This option is selected
                                $option['selected'] = 'selected';
                                $iselected = true;
                            }
                            // Sanitize the option title
                            $title = htmlspecialchars($_name, ENT_NOQUOTES, Kohana::$charset, FALSE);
                            // Change the option to the HTML string
                            $_options[] = '<option' . HTML::attributes($option) . '>' . $title . '</option>';
                        }
                        // Compile the options into a string
                        $_options = "\n" . implode("\n", $_options) . "\n";
                        $loptions[$value] = '<optgroup' . HTML::attributes($group) . '>' . $_options . '</optgroup>';
                    } else {
                        // Create a new attribute set for this option
                        $option = array(
                            'value' => $value
                        );
                        if ($value == $selected && ! $iselected) {
                            // This option is selected
                            $option['selected'] = 'selected';
                            $iselected = true;
                        }
                        // Sanitize the option title
                        $title = htmlspecialchars($name, ENT_NOQUOTES, Kohana::$charset, FALSE);
                        // Change the option to the HTML string
                        $loptions[$value] = '<option' . HTML::attributes($option) . '>' . $title . '</option>';
                    }
                }
                // Compile the options into a single string
                $loptions = "\n" . implode("\n", $loptions) . "\n";
            }
            $aoptions .= $loptions;
        }
        return '<select' . HTML::attributes($attributes) . '>' . $aoptions . '</select>';
    }

    /**
     * Used to filter out all excluded columns
     *
     * @param string $column
     * @return string
     */
    protected function _z_filter($column)
    {
        return ! in_array($column, $this->_z_exclude);
    }

    /**
     * Used to filter out all excluded columns for table
     *
     * @param string $column
     * @return string
     */
    protected function _z_filter_table($column)
    {
        if (! empty($this->_z_show_table)) {
            return in_array($column, $this->_z_show_table);
        }
        return ! in_array($column, $this->_z_exclude_table);
    }

    /**
     * Initialize basic properties: excluded fields, form name
     */
    protected function _z_initialize()
    {
        $this->initialize();
        // Exclude primary key
        $this->_z_exclude[] = $this->_primary_key;
        // Exclude created and updated columns
        if (isset($this->_updated_column['column']))
            $this->_z_exclude[] = $this->_updated_column['column'];
        if (isset($this->_created_column['column']))
            $this->_z_exclude[] = $this->_created_column['column'];
        if (empty($this->_z_orm_name))
            $this->_z_orm_name = Inflector::singular($this->_table_name);
        $this->get_used_limit();
    }

    public function get_used_limit()
    {
        if ($this->_feature_id) {
            if (isset($this->live) || isset($this->_live)) {
                $this->where('live', '=', 1);
            }
            if (isset($this->type)) {
                $this->and_where('type', '=', $this->type);
            }
            $used = $this->count_all();
            $feature = Features::instance();
            $allowed = $feature->max_allowed($this->_feature_id);
            if ($used >= $allowed) {
                $this->_used_limit = TRUE;
            }
            $name = $feature->get_name($this->_feature_id);
            // $name = Inflector::plural($name, $allowed);
            $this->_used_limit_message = __("feature-used-information-message-used-total-name", array(
                ':used' => $used,
                ':total' => $allowed,
                ':name' => __($name)
            ));
        }
        return $this->_used_limit_message;
    }

    /**
     * Create fields automatically
     */
    protected function _z_create_fields()
    {
        $orm_rules = $this->rules();
        
        foreach ($this->_belongs_to as $column => $data) {
            // Field has already been set in
            if (isset($this->_z_fields[$data['foreign_key']]) or in_array($data['foreign_key'], $this->_z_exclude))
                continue;
            if ($this->_localise) {
                $field_label = "{$this->_table_name}_{$column}_label";
                $info = "{$this->_table_name}_{$column}_info";
            } else {
                $field_label = Arr::get($this->_z_labels, $column, ucfirst(Inflector::humanize($column)));
                $info = htmlentities(Arr::get($this->_z_info, $column, ucfirst(Inflector::humanize($column))));
            }
            $options = ORM::factory($data['model']);
            $pk = Arr::overwrite(array(
                $options->primary_key(),
                $options->primary_key()
            ), (array) Arr::get($data, 'zform_pk'));
            $label = Arr::overwrite(array(
                $options->primary_val(),
                $options->primary_val()
            ), (array) Arr::get($data, 'zform_label'));
            
            if (isset($data['options'])) {
                $options_result = $data['options'];
            } else {
                $options = $options->select($pk)->select($label);
                if (isset($data['where'])) {
                    $options = $options->where($data['where'][0], $data['where'][1], $data['where'][2]);
                }
                if ($options->_primary_val) {
                    $options->order_by($options->_primary_val, 'ASC');
                }
                $options_result = $options->find_all()->as_array($pk[1], $label[1]);
            }
            $field_config = $this->_table_columns[$data['foreign_key']];
            if ((int) $field_config['is_nullable'] == 1) {
                $options_result = Arr::merge(array(
                    '0' => 'None'
                ), $options_result);
            } elseif (count($options_result) == 0) {
                throw new Kohana_Exception(__('No values for :field found, atleast one required', [
                    ':field' => __($field_label)
                ]));
            }
            $attributes = Arr::get($this->_z_attributes, $data['foreign_key']);
            $name = $this->field_name($data['foreign_key']);
            // $name = "{$name}[]";
            $id = $this->field_id($data['foreign_key']);
            // $label = Arr::get($this->_z_labels, $column, ucfirst(Inflector::humanize($column)));
            // $info = htmlentities(Arr::get($this->_z_info, $column, ucfirst(Inflector::humanize($column))), ENT_QUOTES);
            $config = (array) Arr::get($this->_z_field_config, $column);
            $config['options'] = $options_result;
            
            $data = Arr::merge($data, $this->_table_columns[$data['foreign_key']]);
            $rules = isset($orm_rules[$column]) ? $orm_rules[$column] : [];
            $this->_z_fields[$data['foreign_key']] = new ZForm_Field_Enum($name, $id, $field_label, $config, $attributes, $data, $info, $this->form_laytout, $rules);
        }
        foreach ($this->_has_many as $column => $data) {
            // Field has already been set in
            if (isset($this->_z_fields[$data['foreign_key']]) or in_array($data['foreign_key'], $this->_z_exclude))
                continue;
            $options = ORM::factory($data['model']);
            $pk = Arr::overwrite(array(
                $options->primary_key(),
                $options->primary_key()
            ), (array) Arr::get($data, 'zform_pk'));
            $label = Arr::overwrite(array(
                $options->primary_val(),
                $options->primary_val()
            ), (array) Arr::get($data, 'zform_label'));
            if (isset($data['options'])) {
                $options = $data['options'];
            } else {
                $options = $options->select($pk)->select($label);
                if (isset($data['where'])) {
                    $options = $options->where($data['where'][0], $data['where'][1], $data['where'][2]);
                }
                $options = $options->find_all()->as_array($pk[1], $label[1]);
            }
            $attributes = Arr::get($this->_z_attributes, $column);
            $name = $this->field_name($column);
            $id = $this->field_id($column);
            if ($this->_localise) {
                $label = "{$this->_table_name}_{$column}_label";
                $info = "{$this->_table_name}_{$column}_info";
            } else {
                $label = Arr::get($this->_z_labels, $column, ucfirst(Inflector::humanize($column)));
                $info = htmlentities(Arr::get($this->_z_info, $column, ucfirst(Inflector::humanize($column))));
            }
            $config = (array) Arr::get($this->_z_field_config, $column);
            $config['options'] = $options;
            if (! isset($config['multichoice'])) {
                $config['multichoice'] = TRUE;
            }
            if (! isset($config['multiple'])) {
                $config['multiple'] = TRUE;
            }
            $config['separator'] = ',';
            
            $rules = isset($orm_rules[$column]) ? $orm_rules[$column] : [];
            $this->_z_fields[$column] = new ZForm_Field_Enum($name, $id, $label, $config, $attributes, $data, $info, $this->form_laytout, $rules);
        }
        foreach ($this->_table_columns as $column => $field) {
            // Field has already been set in
            if (isset($this->_z_fields[$column]) or in_array($column, $this->_z_exclude))
                continue;
            $data_type = explode(' ', $field['data_type']);
            $data_type = $data_type[0];
            // Get additional config items, and add the default data
            $zcolumns = Kohana::$config->load('zcolumns');
            $config = Arr::merge($zcolumns->default['default_column'], (array) $zcolumns->default[$data_type]);
            $config = Arr::merge($config, (array) Arr::get($this->_z_field_config, $column));
            $type = 'ZForm_Field_' . $config['type'];
            $attributes = Arr::get($this->_z_attributes, $column);
            $name = $this->field_name($column);
            $id = $this->field_id($column);
            if ($this->_localise) {
                $label = "{$this->_table_name}_{$column}_label";
                $info = "{$this->_table_name}_{$column}_info";
            } else {
                $label = Arr::get($this->_z_labels, $column, ucfirst(Inflector::humanize($column)));
                $info = htmlentities(Arr::get($this->_z_info, $column, ucfirst(Inflector::humanize($column))));
            }
            
            $rules = isset($orm_rules[$column]) ? $orm_rules[$column] : [];
            $this->_z_fields[$column] = new $type($name, $id, $label, $config, $attributes, $field, $info, $this->form_laytout, $rules);
        }
    }

    /**
     * Remove all many to many associations
     *
     * @param string $alias
     * @return $this
     */
    public function remove_all($alias)
    {
        DB::delete($this->_has_many[$alias]['through'])->where($this->_has_many[$alias]['foreign_key'], '=', $this->pk())
            ->execute($this->_db);
        return $this;
    }

    /**
     *
     * @param array $where
     *            = $where['column'], $where['op'], $where['value']
     * @param Mix $columns
     * @return View
     */
    public function tableView(array $where = array(), $columns = NULL)
    {
        // get columns as the they are required to validate user request
        if (! $columns) {
            // All columns except excludedcheck
            $columns = array_keys(Arr::extract($this->_table_columns, array_filter(array_keys($this->_table_columns), array(
                $this,
                '_z_filter_table'
            ))));
        } elseif (! is_array($columns)) {
            $columns = func_get_args();
        }
        
        $select_list = array();
        $request = Request::current();
        $controller = Request::current()->controller();
        $action = Request::current()->action();
        $id = $controller . '-' . $action;
        $button_action = $request->query('button-action');
        $list_column = $request->query('list-column');
        $list_per_page = (int) $request->query('list-per-page');
        if (! $list_per_page) {
            $list_per_page = 15;
        }
        // check if user did not tried to temper with search column name
        if (! $list_column || ! in_array($list_column, $columns)) {
            $list_column = $this->primary_val();
        }
        $check = (array) $request->query('check');
        $list_search = $request->query('list-search');
        if ($where) {
            $this->where($where['column'], $where['op'], $where['value']);
        }
        if (! empty($check)) {
            $this->and_where('id', 'in', $check);
        }
        if ($button_action) {
            $this->and_where($list_column, 'like', "%$list_search%");
        }
        $total = $this->count_all();
        // Create a paginator
        $pagination = Pagination::factory(array(
            'total_items' => $total,
            'items_per_page' => $list_per_page, // set this to 30 or 15 for the real thing, now just for testing purposes...
            'form_id' => $id
        ))->route_params(array(
            'directory' => Request::current()->directory(),
            'controller' => Request::current()->controller(),
            'action' => Request::current()->action()
        ));
        // Get the items for the query
        $sort = $request->query('sort');
        $dir = $request->query('dir');
        // check if user did try to temper with sort column
        if (! $sort || ! in_array($sort, $columns)) {
            $sort = $this->primary_val();
        }
        // check if user did try to temper with sort by
        if (! $dir || ! in_array(strtolower($dir), array(
            'asc',
            'desc'
        ))) {
            $dir = 'ASC';
        }
        if (! $this->loaded()) {
            if ($where) {
                $this->where($where['column'], $where['op'], $where['value']);
            }
            if (! empty($check)) {
                $this->and_where('id', 'in', $check);
            }
            if ($button_action) {
                $this->and_where($list_column, 'like', "%$list_search%");
            }
            $results = $this->limit($pagination->items_per_page)
                ->offset($pagination->offset)
                ->order_by($sort, $dir)
                ->find_all();
        } else {
            $results[] = $this;
        }
        // echo $this->last_query();
        // die();
        
        $column_list = array();
        foreach ($columns as $column) {
            // if (!isset($this->_z_fields[$column]))
            // continue;
            if ($this->_localise) {
                $label = __("{$this->_table_name}_{$column}_label");
            } else {
                $label = Arr::get($this->_z_labels, $column, ucfirst(Inflector::humanize($column)));
            }
            $class = (isset($this->_z_field_config[$column]['_z_table_class'])) ? $this->_z_field_config[$column]['_z_table_class'] : null;
            $column_list[$column] = array(
                'label' => $label,
                'sortable' => true,
                'class' => $class
            );
            $select_list[$column] = $label;
        }
        
        if ($this->_z_config_delete) {
            $column_list['actions'] = array(
                'label' => __('genertic_table_title_actions'),
                'sortable' => false,
                'class' => 'col-xs-1 text-center'
            );
        }
        $controller = Request::current()->controller();
        $action = Request::current()->action();
        $template = Redback::instance()->admin_template;
        $data = array();
        foreach ($results as $result) {
            $row = $result->as_array();
            
            // $row['last_failed_login'] = Helper_Format::relative_time(strtotime($row['last_failed_login']));
            // add actions
            $locked = false;
            if (isset($row['locked'])) {
                $locked = (bool) $row['locked'];
            }
            if ($locked) {
                $row['actions'] = '<span class="locked fa fa-lock"></span>';
            } elseif ($this->_z_config_delete) {
                $row['actions'] = View::factory("{$template}/zform/wrappers/links", [
                    'row' => $row
                ])->render();
            }
            if (isset($row['created'])) {
                $row['created'] = Helper_Format::friendly_datetime($row['created']);
            }
            if (isset($row['modified'])) {
                $row['modified'] = Helper_Format::friendly_datetime($row['modified']);
            }
            if ($row[$result->primary_val()]) {
                $row[$result->primary_val()] = HTML::anchor($controller . '/edit/' . $result->pk(), '<span class="fa fa-pencil-square-o" aria-hidden="true"></span> ' . $result->primary_val_value(), array(
                    'title' => __('generic_label_edit') . ' ' . $result->primary_val_value(),
                    'data-id' => $result->pk()
                ));
            }
            // set roles
            $data[] = $row;
        }
        switch ($button_action) {
            case 'export-csv':
                {
                    $this->export_csv($data);
                    break;
                }
            case 'export-OfficeOpenXML':
                {
                    $this->export_OfficeOpenXML($data);
                    break;
                }
        }
        $used_limit_message = $this->get_used_limit();
        
        $view_path = $template . '/zform/wrappers/table';
        
        return View::factory($view_path, array(
            'paging' => $pagination,
            'default_sort' => $sort,
            'column_list' => $column_list,
            'data' => $data,
            'addNewButton' => $this->_z_add_new_button,
            'select_list' => $select_list,
            'id' => $id,
            'primary_key' => $this->primary_key(),
            'primary_val' => $this->primary_val(),
            'used_limit_message' => $used_limit_message,
            'check' => $check,
            'list_search' => $list_search,
            'list_column' => $list_column,
            'list_per_page' => $list_per_page,
            'button_action' => $button_action
        ));
    }

    public function export_sheet_builder($data)
    {
        // require (Kohana::find_file ( 'vendor', 'autoloader' ));
        $objPHPExcel = new PHPExcel();
        $properties = $objPHPExcel->getProperties();
        $properties->setCreator(Redback::instance()->get_system_name());
        // set active sheet to default
        $objPHPExcel->setActiveSheetIndex(0);
        // set show grid when printing.
        $objPHPExcel->getActiveSheet()->setShowGridlines(true);
        $activeSheet = $objPHPExcel->getActiveSheet();
        $y = 1;
        foreach ($data as $columns) {
            $x = 0;
            foreach ($columns as $name => $row) {
                if ($name != 'actions' && $name != 'password') {
                    $activeSheet->setCellValueByColumnAndRow($x, $y, $row);
                    $x ++;
                }
            }
            $y ++;
        }
        return $objPHPExcel;
    }

    public function export_csv($data)
    {
        $objPHPExcel = $this->export_sheet_builder($data);
        $objWriter = new PHPExcel_Writer_CSV($objPHPExcel);
        $objWriter->setUseBOM(true);
        $objWriter->setSheetIndex(0);
        $fname = File::get_unique_name('csv');
        if (Request::current()->is_ajax()) {
            try {
                $objWriter->save(Redback::instance()->get_dir_temp() . $fname);
                $anchor = HTML::file_anchor("/tmp/{$fname}", 'Download');
                Message::add('success', "CSV exported: $anchor");
            } catch (Exception $e) {
                Message::add(Message::ERROR, __("system permission error can't write to temp folder"));
            }
        } else {
            $mime = File::mime_by_ext('csv');
            header("Content-Type: $mime");
            header('Content-Disposition: attachment;filename="' . $fname . '"');
            header('Cache-Control: max-age=0');
            $objWriter->save("php://output");
            Redback::instance()->responseType = FALSE;
        }
    }

    public function export_OfficeOpenXML($data)
    {
        $objPHPExcel = $this->export_sheet_builder($data);
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $fname = File::get_unique_name('xlsx');
        if (Request::current()->is_ajax()) {
            $objWriter->save(Redback::instance()->get_dir_temp() . $fname);
            $anchor = HTML::file_anchor("/tmp/{$fname}", 'Download');
            Message::add('success', "XML exported: $anchor");
        } else {
            $mime = File::mime_by_ext('csv');
            header("Content-Type: $mime");
            header('Content-Disposition: attachment;filename="' . $fname . '"');
            header('Cache-Control: max-age=0');
            $objWriter->save("php://output");
            Redback::instance()->responseType = FALSE;
        }
    }

    /**
     * Get table name for zform object
     *
     * @return string
     */
    public function get_table_name()
    {
        return $this->_table_name;
    }

    /**
     * get table columns array
     *
     * @return array
     */
    public function get_table_columns()
    {
        return array_keys($this->_table_columns);
    }

    /**
     * convert ORM model erros to error alert
     *
     * @param Model $model
     */
    public static function errors_to_message($model)
    {
        $message = '';
        $errors = $model->errors();
        if (count($errors)) {
            foreach ($errors as $name => $error) {
                $name = Inflector::humanize($name);
                $message .= "{$name} - {$error}<br>";
            }
            Message::add(Message::error, __('generic_error_message', array(
                ':error_message' => $message
            )));
        }
    }

    public function get_folder_name()
    {
        return $this->_img_folder_name;
    }
}