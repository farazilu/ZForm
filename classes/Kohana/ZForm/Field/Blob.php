<?php
defined ( 'SYSPATH' ) or die ( 'No direct script access.' );

/**
 * ZForm: a text field implementation.
 * Displays a textbox
 *
 * @package ZForm
 * @category Field
 * @author Azuka Okuleye
 * @copyright (c) 2011 Azuka Okuleye
 * @license http://zahymaka.com/license.html
 */
class Kohana_ZForm_Field_Blob extends ZForm_Field {
	protected $_config = array (
			'multiline' => false,
			'maxlength' => false,
			'bubble-info' => false,
			'bubble-required' => false 
	);
	public function render() {
		if (isset ( $this->_extra ['is_nullable'] ) && $this->_extra ['is_nullable'] === false) {
			$this->_attributes ['required'] = 'required';
		}
		if ($this->_config ['maxlength'] and ! isset ( $this->_attributes ['maxlength'] )) {
			$this->_attributes ['maxlength'] = $this->_extra [$this->_config ['maxlength']];
		}
		$class = (empty ( $this->_error )) ? 'form-control ' : 'form-control errorField';
		if ($this->_config ['multiline']) {
			return Form::textarea ( $this->_name, Format::chars_decode ( $this->_value ), $this->_attributes + array (
					'id' => $this->_id,
					'rows' => $this->_config ['multiline'],
					'cols' => 100,
					'title' => $this->_info,
					'class' => $class,
					'placeholder' => __ ( $this->_label ) 
			) );
		}
		return Form::input ( $this->_name, Format::chars_decode ( $this->_value ), $this->_attributes + array (
				'id' => $this->_id,
				'size' => 40,
				'title' => $this->_info,
				'class' => $class,
				'placeholder' => __ ( $this->_label ) 
		) );
	}
}