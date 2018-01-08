<?php
defined ( 'SYSPATH' ) or die ( 'No direct script access.' );

/**
 * ZForm: a Password field implementation.
 * Displays a textbox
 *
 * @package ZForm
 * @category Field
 * @author Faraz Ahmad
 */
class Kohana_ZForm_Field_Password extends ZForm_Field {
	protected $_config = array (
			'maxlength' => false,
			'bubble-info' => false,
			'bubble-required' => false,
			'class' => 'text form-control' 
	);
	public function render() {
		if (isset ( $this->_extra ['is_nullable'] ) && $this->_extra ['is_nullable'] === false) {
			$this->_attributes ['required'] = 'required';
		}
		$return = '';
		if ($this->_config ['maxlength'] and ! isset ( $this->_attributes ['maxlength'] )) {
			$this->_attributes ['maxlength'] = $this->_extra [$this->_config ['maxlength']];
		}
		$class = (empty ( $this->_error )) ? $this->_config ['class'] . ' form-control' : $this->_config ['class'] . ' form-control errorField';
		return Form::password ( $this->_name, '', $this->_attributes + array (
				'id' => $this->_id,
				'size' => 40,
				'title' => $this->_info,
				'class' => $class,
				'placeholder' => __ ( $this->_label ) 
		) );
		return $return;
	}
	
	/**
	 * ZForm: a Password field values as XML
	 *
	 * @package ZForm
	 * @category Field
	 * @author .....
	 */
	public function render_XML() {
		return '<' . $this->_id . ' />';
	}
}