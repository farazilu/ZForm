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
class Kohana_ZForm_Field_Image extends ZForm_Field {
	protected $_config = array (
			'folder' => '',
			'bubble-info' => false,
			'bubble-required' => false 
	);
	public function render() {
		if (isset ( $this->_extra ['is_nullable'] ) && $this->_extra ['is_nullable'] === false) {
			$this->_attributes ['required'] = 'required';
		}
		$return = '';
		/*
		 * if ($this->_config['maxlength'] AND !isset($this->_attributes['maxlength'])) { $this->_attributes['maxlength'] = $this->_extra[$this->_config['maxlength']]; }
		 */
		$class = (empty ( $this->_error )) ? 'text form-control' : 'text form-control errorField';
		
		$destination = Redback::instance ()->getRepositetoryFolder ( $this->_config ['folder'] );
		if ($this->_value != '') {
			$url = Redback::instance ()->getRepositoryLink ( $this->_config ['folder'] );
			$return .= HTML::image ( $url . $this->_value, array (
					'alt' => $this->_label 
			) );
		}
		$return .= Form::file ( $this->_name, $this->_attributes + array (
				'id' => $this->_id,
				'title' => $this->_info,
				'class' => $class 
		) );
		$return .= __ ( 'jpg, jpeg, png, ico  or gif images only' );
		return $return;
	}
	
	/**
	 * Set the value.
	 * Override to handle array and other value types
	 *
	 * @param mixed $value        	
	 */
	protected function _set_value($value) {
		// echo Debug::vars($value);
		$this->_value = $value;
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