<?php
defined ( 'SYSPATH' ) or die ( 'No direct script access.' );

/**
 * ZForm: a boolean field implementation.
 * Displays a checkbox
 *
 * @package ZForm
 * @category Field
 * @author Azuka Okuleye
 * @copyright (c) 2011 Azuka Okuleye
 * @license http://zahymaka.com/license.html
 */
class Kohana_ZForm_Field_Boolean extends ZForm_Field {
	protected $_config = array (
			'true_value' => '1',
			'false_value' => '0',
			'format' => 'checkbox',
			'bubble-info' => false,
			'bubble-required' => false 
	);
	public function render() {
		if (isset ( $this->_extra ['is_nullable'] ) && $this->_extra ['is_nullable'] === false) {
			$this->_attributes ['required'] = 'required';
		}
		if ($this->_readonly) {
			$return = ($this->_value) ? 'Yes' : 'No';
			$return .= Form::hidden ( $this->_name, $this->_value == $this->_config ['true_value'], $this->_attributes + array (
					'id' => $this->_id 
			) );
			return $return;
		} else {
			switch ($this->_config ['format']) {
				case 'radio' :
					
					{
						return Form::radio ( $this->_name, $this->_config ['true_value'], $this->_value == $this->_config ['true_value'], $this->_attributes + array (
								'id' => $this->_id . '_1' 
						) ) . Form::label ( $this->_id . '_1', __ ( 'radio_label_yes' ) ) . Form::radio ( $this->_name, $this->_config ['false_value'], $this->_value == $this->_config ['false_value'] || empty ( $this->_value ), $this->_attributes + array (
								'id' => $this->_id . '_0' 
						) ) . Form::label ( $this->_id . '_0', __ ( 'radio_label_no' ) );
					}
				case 'checkbox' :
				default :
					{
						unset ( $this->_attributes ['required'] );
						$return = '<div class="form-switcher">';
						$return .= Form::hidden ( $this->_name, 0 );
						$return .= Form::checkbox ( $this->_name, $this->_config ['true_value'], $this->_value == $this->_config ['true_value'], $this->_attributes + array (
								'id' => $this->_id 
						) );
						$return .= Form::label ( $this->_id, '', [ 
								'class' => 'switcher' 
						] );
						$return .= '</div>';
						return $return;
						break;
					}
			}
		}
	}
	public function db_value() {
		return $this->_value ? $this->_config ['true_value'] : $this->_config ['false_value'];
	}
	
	/**
	 * Display single field (and optionally label) in a wrapper
	 *
	 * @param array $attributes        	
	 * @return string
	 */
	public function single_field(array $attributes) {
		switch ($this->_config ['format']) {
			case 'checkbox' :
				return View::factory ( $this->_wrapper )->set ( 'field', $this )->set ( 'attributes', $attributes )->set ( 'form_horizontal', $this->form_horizontal );
				break;
			
			case 'radio' :
			default :
				return View::factory ( 'zform/wrappers/radio' )->set ( 'field', $this )->set ( 'attributes', $attributes );
				break;
		}
	}
	
	/**
	 * Render the field
	 *
	 * @return string
	 */
	public function form_label() {
		switch ($this->_config ['format']) {
			case 'checkbox' :
				$attributes = array ();
				if ($this->form_horizontal == 'horizontal') {
					$attributes ['class'] = 'col-sm-2 control-label hint';
				} else {
					$attributes ['class'] = 'hint control-label';
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
				return Form::label ( $this->_id, __ ( $this->_label ), $attributes );
				break;
			
			case 'radio' :
			default :
				return __ ( $this->_label );
				break;
		}
	}
}