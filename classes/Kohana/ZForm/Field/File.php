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
class Kohana_ZForm_Field_File extends ZForm_Field {
	protected $_config = array (
			'maxlength' => false,
			'bubble-info' => false,
			'bubble-required' => false 
	);
	public function render() {
		$return = '';
		/*
		 * if ($this->_config['maxlength'] AND !isset($this->_attributes['maxlength'])) { $this->_attributes['maxlength'] = $this->_extra[$this->_config['maxlength']]; }
		 */
		$class = (empty ( $this->_error )) ? 'text form-control' : 'text form-control errorField';
		return Form::file ( $this->_name, $this->_attributes + array (
				'id' => $this->_id,
				'title' => $this->_info,
				'class' => $class 
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