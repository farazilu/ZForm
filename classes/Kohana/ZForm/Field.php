<?php
defined ( 'SYSPATH' ) or die ( 'No direct script access.' );

/**
 * ZForm field: The base class for all Zform field types
 *
 * @package ZForm
 * @author Azuka Okuleye
 * @copyright (c) 2011 Azuka Okuleye
 * @license http://zahymaka.com/license.html
 */
abstract class Kohana_ZForm_Field {
	
	/**
	 * Form field attributes
	 *
	 * @var array
	 */
	protected $_attributes = array ();
	
	/**
	 * Valid configuration items
	 *
	 * @var array
	 */
	protected $_config = array ();
	
	/**
	 * Data about the column from ORM
	 *
	 * @var array
	 */
	protected $_extra = array ();
	
	/**
	 * Form field id
	 *
	 * @var string
	 */
	protected $_id = NULL;
	
	/**
	 * Form field name
	 *
	 * @var string
	 */
	protected $_name = NULL;
	
	/**
	 * Form field label
	 *
	 * @var string
	 */
	protected $_label = NULL;
	
	/**
	 * Info field label
	 *
	 * @var string
	 */
	protected $_info = NULL;
	
	/**
	 * Error message for field
	 *
	 * @var String
	 */
	protected $_error = NULL;
	
	/**
	 * Form field value
	 *
	 * @var string
	 */
	protected $_value = NULL;
	
	/**
	 * check if this field is read only or not
	 *
	 * @var boolean
	 */
	protected $_readonly = FALSE;
	
	/**
	 * Form field help text.
	 *
	 * @var string
	 */
	protected $_help_text = NULL;
	
	/**
	 * Wrapper
	 *
	 * @var string
	 */
	protected $_wrapper = 'zform/wrappers/default';
	
	/**
	 * Wrapper_XML
	 *
	 * @var string
	 */
	protected $_wrapper_XML = 'zform/wrappers/xml';
	protected $form_horizontal = false;
	
	/**
	 * set Zform rules for given field
	 *
	 * @var array
	 */
	protected $_rules = array ();
	
	/**
	 * Render the field
	 *
	 * @return string
	 */
	abstract public function render();
	
	/**
	 * Create a new field
	 *
	 * @param string $name        	
	 * @param string $id        	
	 * @param string $label        	
	 * @param array $config        	
	 * @param array $attributes        	
	 * @param array $extra        	
	 */
	public function __construct($name, $id, $label, array $config = NULL, array $attributes = NULL, array $extra = NULL, $info = NULL, $form_horizontal = false, array $rules = null) {
		$this->_attributes = $this->_attributes + ( array ) $attributes;
		$this->_config = Arr::overwrite ( $this->_config, ( array ) $config );
		$this->_extra = ( array ) $extra;
		$this->_name = $name;
		$this->_label = $label;
		$this->_id = $id;
		$this->_info = $info;
		$this->form_horizontal = $form_horizontal;
		$this->_rules = $rules;
		
		// check ORM rules set to not_empty soft not null
		if (is_array ( $this->_rules )) {
			if (Helper::in_array ( 'not_empty', $this->_rules, true )) {
				$this->_extra ['is_nullable'] = false;
			}
		}
	}
	
	/**
	 * Get the value
	 *
	 * @param mixed $name        	
	 * @return mixed
	 */
	public function __get($name) {
		if ($name === 'value')
			return $this->_value;
		elseif ($name === 'label')
			return $this->_label;
		elseif ($name === 'wrapper')
			return $this->_wrapper;
		elseif (isset ( $this->_config [$name] ))
			return $this->_config [$name];
		else {
			throw new Kohana_Exception ( 'The :property: property does not exist in the :class: class', array (
					':property:' => $name,
					':class:' => get_class ( $this ) 
			) );
		}
	}
	
	/**
	 * Set the value
	 *
	 * @param mixed $name        	
	 * @param mixed $value        	
	 */
	public function __set($name, $value) {
		if ($name === 'value')
			$this->_set_value ( $value );
		elseif ($name === 'label')
			$this->_label = $value;
		elseif ($name === 'wrapper')
			$this->_wrapper = $value;
		elseif ($name === 'readonly')
			$this->_readonly = $value;
		elseif (isset ( $this->_config [$name] ))
			$this->_config [$name] = $value;
		else {
			throw new Kohana_Exception ( 'The :property: property does not exist in the :class: class', array (
					':property:' => $name,
					':class:' => get_class ( $this ) 
			) );
		}
	}
	
	/**
	 * Add more attribures after initealization.
	 *
	 * @param array $attributes        	
	 */
	public function addAttribute(array $attributes = NULL) {
		$this->_attributes = $this->_attributes + ( array ) $attributes;
	}
	
	/**
	 * String representation
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->render ();
	}
	
	/**
	 * Render the field
	 *
	 * @return string
	 */
	public function form_field() {
		return $this->render ();
	}
	
	/**
	 * Render the field
	 *
	 * @return string
	 */
	public function form_field_XML() {
		return $this->render_XML ();
	}
	
	/**
	 * Render the field
	 *
	 * @return string
	 */
	public function form_label() {
		$attributes = array ();
		if ($this->form_horizontal == 'horizontal') {
			$attributes ['class'] = 'col-sm-2 control-label';
		} else {
			$attributes ['class'] = 'control-label';
		}
		if ($this->_config ['bubble-info'] === true) {
			$attributes ['data-toggle'] = 'tooltip';
			$attributes ['data-placement'] = 'right';
		}
		$attributes ['title'] = $this->info ();
		if ($this->_config ['bubble-required'] === true) {
			if (isset ( $this->_extra ['is_nullable'] ) && $this->_extra ['is_nullable'] === false) {
				$attributes ['data-required'] = 'yes';
			}
		}
		// echo Debug::vars($this, $attributes);
		return Form::label ( $this->_id, __ ( $this->_label ), $attributes );
	}
	
	/**
	 * Display single field (and optionally label) in a wrapper
	 *
	 * @param array $attributes        	
	 * @return string
	 */
	public function single_field(array $attributes) {
		return View::factory ( $this->_wrapper )->set ( 'field', $this )->set ( 'attributes', $attributes )->set ( 'form_horizontal', $this->form_horizontal );
	}
	
	/**
	 * Display single field (and optionally label) in a XML
	 *
	 * @param array $attributes        	
	 * @return string
	 */
	public function single_field_XML(array $attributes) {
		return View::factory ( $this->_wrapper_XML )->set ( 'field', $this )->set ( 'attributes', $attributes );
	}
	
	/**
	 * Display single field (and optionally label) in a XML
	 *
	 * @param array $attributes        	
	 * @return string
	 */
	public function single_field_json(array $attributes) {
		$return = array ();
		if (is_array ( $this->_value )) {
			$keys = array_keys ( $this->_config ['options'] );
			foreach ( $this->_value as $val ) {
				$id = $this->_id . '_' . array_search ( $val, $keys );
				$return [$id] ['value'] = $val;
				if (! empty ( $this->_error )) {
					$return [$id] ['error'] = '<error><![CDATA[' . $this->_error . ']]></error>';
				}
			}
		} else {
			$return [$this->_id] ['value'] = $this->_value;
			if (! empty ( $this->_error )) {
				$return [$this->_id] ['error'] = '<error><![CDATA[' . $this->_error . ']]></error>';
			}
		}
		return $return;
	}
	
	/**
	 * Value formatted for the database
	 *
	 * @return string
	 */
	public function db_value() {
		return $this->_value;
	}
	
	/**
	 * Set the value to the default
	 */
	public function set_default() {
		$this->value = Arr::get ( $this->_extra, Kohana::$config->load ( 'zcolumns.default.default_column.default' ) );
	}
	
	/**
	 * Set the value.
	 * Override to handle array and other value types
	 *
	 * @param mixed $value        	
	 */
	protected function _set_value($value) {
		$this->_value = $value;
	}
	public function set_error($error) {
		$this->_error = $error;
	}
	
	/**
	 * Get error message for field
	 *
	 * @param Boolean $span        	
	 * @return string
	 * @uses span if TRUE will return span wraped around
	 */
	public function get_error($span = FALSE) {
		if ($span) {
			if (! empty ( $this->_error )) {
				return '<span class="text-danger">' . __ ( $this->_error ) . '</span>';
			}
		}
		return __ ( $this->_error );
	}
	
	/**
	 * Get INFO message for field
	 *
	 * @param Boolean $span        	
	 * @return string
	 * @uses span if TRUE will return span wraped around
	 */
	public function info() {
		return __ ( $this->_info );
	}
	public function get_field_id() {
		return $this->_id;
	}
	
	/**
	 * ZForm: a text field values as XML
	 *
	 * @package ZForm
	 * @category Field
	 * @author .....
	 */
	public function render_XML() {
		$return = '';
		if (is_array ( $this->_value )) {
			$keys = array_keys ( $this->_config ['options'] );
			foreach ( $this->_value as $val ) {
				$id = $this->_id . '_' . array_search ( $val, $keys );
				$return .= '<' . $id . '><value><![CDATA[' . HTML::chars ( $val ) . ']]></value>';
				if (! empty ( $this->_error )) {
					$return .= '<error><![CDATA[' . $this->_error . ']]></error>';
				}
				$return .= '</' . $id . '>';
			}
		} else {
			$return .= '<' . $this->_id . '><value><![CDATA[' . HTML::chars ( $this->_value ) . ']]></value>';
			if (! empty ( $this->_error )) {
				$return .= '<error><![CDATA[' . $this->_error . ']]></error>';
			}
			$return .= '</' . $this->_id . '>';
		}
		return $return;
	}
}