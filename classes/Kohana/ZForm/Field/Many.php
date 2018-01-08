<?php
defined ( 'SYSPATH' ) or die ( 'No direct script access.' );

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
class Kohana_ZForm_Field_Many extends ZForm_Field {
	protected $_z_fields = array ();
	protected $_db = NULL;
	protected $_table_columns = array ();
	protected $_value = array ();
	protected $_config = array (
			'multiple' => false,
			'multichoice' => false,
			'options' => array (),
			'format' => 'select',
			'separator' => '',
			'bubble-info' => false,
			'bubble-required' => false 
	);
	protected $_changed = FALSE;
	
	/**
	 * Wrapper
	 *
	 * @var string
	 */
	protected $_wrapper = 'zform/wrappers/many';
	public function __construct($name, $id, $label, array $config = NULL, array $attributes = NULL, array $extra = NULL, $info = NULL) {
		parent::__construct ( $name, $id, $label, $config, $attributes, $extra, $info );
		if ($this->_extra ['through']) {
			$this->_db = Database::instance ( isset ( $this->_extra ['database'] ) ? $this->_extra ['database'] : NULL );
			$this->_table_columns = $this->_db->list_columns ( $this->_extra ['through'] );
			foreach ( $this->_config ['options'] as $option_id => $option_name ) {
				foreach ( $this->_table_columns as $column => $field ) {
					$label = ucfirst ( Inflector::humanize ( $column ) );
					$info = ucfirst ( Inflector::humanize ( $column ) );
					$name = $this->field_name ( $column, $option_id );
					$id = $this->field_id ( $column, $option_id );
					if ($column == $this->_extra ['foreign_key']) {
						// do nothing for for forign key..
					} elseif ($column == $this->_extra ['far_key']) {
						// convert far_key to checkbox
						$label = ucfirst ( Inflector::humanize ( $option_name ) );
						$this->_z_fields [$option_id] [$column] = new ZForm_Field_Boolean ( $name, $id, $label );
						$this->_z_fields [$option_id] [$column]->true_value = $option_id;
					} else {
						$data_type = explode ( ' ', $field ['data_type'] );
						$data_type = $data_type [0];
						// Get additional config items, and add the default data
						$zcolumns = Kohana::$config->load ( 'zcolumns' );
						$config = Arr::merge ( $zcolumns->default ['default_column'], ( array ) $zcolumns->default [$data_type] );
						// $config = Arr::merge($config, (array) Arr::get($this->_z_field_config, $column));
						$type = 'ZForm_Field_' . $config ['type'];
						$attributes = array ();
						$this->_z_fields [$option_id] [$column] = new $type ( $name, $id, $label, $config, $attributes, $field, $info );
					}
				}
			}
		} else {
			foreach ( $this->_config ['options'] as $option_id => $option_name ) {
				$label = ucfirst ( Inflector::humanize ( $option_name ) );
				$info = ucfirst ( Inflector::humanize ( $option_name ) );
				$name = $this->field_name ( $this->_extra ['foreign_key'], $option_id );
				$id = $this->field_id ( $this->_extra ['foreign_key'], $option_id );
				$this->_z_fields [$option_id] = new ZForm_Field_Boolean ( $name, $id, $label );
				$this->_z_fields [$option_id]->true_value = $option_id;
			}
		}
	}
	protected function _set_value($value) {
		if (is_array ( $value )) {
			$this->_changed = FALSE;
			if ($this->_extra ['through']) {
				foreach ( $value as $option_id => $rows ) {
					foreach ( $rows as $row_name => $row_val ) {
						if ($this->_z_fields [$option_id] [$row_name]->value != $row_val) {
							$old_value = $this->_z_fields [$option_id] [$row_name]->value;
							if (empty ( $row_val )) {
								DB::delete ( $this->_extra ['through'] )->where ( $this->_extra ['foreign_key'], '=', $this->_value )->and_where ( $row_name, '=', $old_value )->execute ( $this->_db );
							} else {
								$columns = array (
										$this->_extra ['foreign_key'] => $this->_value,
										$row_name => $row_val 
								);
								DB::insert ( $this->_extra ['through'], array_keys ( $columns ) )->values ( array_values ( $columns ) )->execute ( $this->_db );
							}
							// $this->_changed = TRUE;
						}
						$this->_z_fields [$option_id] [$row_name]->value = $row_val;
					}
				}
			} else {
				foreach ( $value as $option_id => $rows ) {
					foreach ( $rows as $row_name => $row_val ) {
						if ($this->_z_fields [$option_id]->value != $row_val) {
							$this->_z_fields [$option_id]->value = $row_val;
							$model = ORM::factory ( $this->_extra ['model'], $row_val );
							$model->$row_name = $this->_value;
							$model->save ();
						}
					}
				}
			}
		} else {
			$this->_value = $value;
			if ($this->_extra ['through']) {
				$values = DB::select ( '*' )->from ( $this->_extra ['through'] )->where ( $this->_extra ['foreign_key'], '=', $this->_value )->execute ( $this->_db )->as_array ( $this->_extra ['far_key'] );
				/**
				 * setup values of fields
				 */
				foreach ( $values as $option_id => $rows ) {
					foreach ( $rows as $row_name => $row_val ) {
						if (isset ( $this->_z_fields [$option_id] [$row_name] )) {
							$this->_z_fields [$option_id] [$row_name]->value = $row_val;
						}
					}
				}
			} else {
				$model = ORM::factory ( $this->_extra ['model'] );
				$model->where ( $this->_extra ['foreign_key'], '=', $this->_value );
				$results = $model->find_all ();
				foreach ( $results as $result ) {
					$this->_z_fields [$result->pk ()]->value = $result->pk ();
				}
			}
		}
	}
	public function render() {
		$render = Form::select ( $this->_name . '[]', $this->_config ['options'], ( array ) $this->_value, $this->_attributes + array (
				'id' => $this->_id,
				'class' => 'form-control' 
		) );
		return $render;
		$render = '';
		if ($this->_extra ['through']) {
			foreach ( $this->_z_fields as $fields ) {
				$render .= '<ul class="list-unstyled">';
				foreach ( $fields as $field ) {
					$render .= $field->single_field ( array (
							'class' => 'form-group ',
							'id' => 'field_' . $field->get_field_id () 
					) );
				}
				$render .= '</ul>';
			}
			// print_r($this);
		} else {
			// echo Debug::vars($this);
			// die();
			$render .= '<ul class="list-unstyled">';
			foreach ( $this->_z_fields as $field ) {
				$render .= $field->single_field ( array (
						'class' => 'form-group ',
						'id' => 'field_' . $field->get_field_id () 
				) );
			}
			$render .= '</ul>';
		}
		return $render;
	}
	public function field_name($column, $option_id = NULL) {
		return $option_id ? $this->_name . '[' . $option_id . '][' . $column . ']' : $this->_name . '[' . $column . ']';
	}
	
	/**
	 * Path in form array
	 *
	 * @param string $column        	
	 * @return string
	 */
	public function field_path($column, $option_id = NULL) {
		return $option_id ? $this->_id . '.' . $option_id . '.' . $column : $this->_id . '.' . $column;
	}
	
	/**
	 * Form ID
	 *
	 * @param string $field        	
	 * @return string
	 */
	public function field_id($column, $option_id = NULL) {
		return str_replace ( '.', '_', $this->field_path ( $column, $option_id ) );
	}
	
	/**
	 * Display single field (and optionally label) in a wrapper
	 *
	 * @param array $attributes        	
	 * @return string
	 */
	public function single_field(array $attributes) {
		return View::factory ( $this->_wrapper )->set ( 'field', $this )->set ( 'attributes', $attributes );
	}
	
	/**
	 * Render the field
	 *
	 * @return string
	 */
	public function form_label() {
		return '<legend>' . __ ( $this->_label ) . '</legend>';
	}
	
	/**
	 * ZForm: a text field values as XML
	 *
	 * @package ZForm
	 * @category Field
	 * @author .....
	 */
	public function render_XML() {
		$render = '';
		if ($this->_extra ['through']) {
			foreach ( $this->_z_fields as $fields ) {
				foreach ( $fields as $field ) {
					$render .= $field->single_field_XML ( array (
							'class' => 'form-group ',
							'id' => 'field_' . $field->get_field_id () 
					) );
				}
			}
			// print_r($this);
		}
		return $render;
	}
}