<?php
defined ( 'SYSPATH' ) or die ( 'No direct script access.' );

/**
 * ZForm: a date and time field implementation.
 * Displays select dropdowns
 *
 * @package ZForm
 * @category Field
 * @author Azuka Okuleye
 * @copyright (c) 2011 Azuka Okuleye
 * @license http://zahymaka.com/license.html
 * @todo Fix the parsing. This is the messiest class in this suite
 */
class Kohana_ZForm_Field_Phone extends ZForm_Field {
	protected $_config = array (
			'format' => '',
			'country' => TRUE,
			'city' => TRUE,
			'number' => TRUE,
			'fields' => ':country :city :number',
			'bubble-info' => false,
			'bubble-required' => false 
	);
	
	/**
	 * The current stored date
	 *
	 * @var array
	 */
	protected $_phone = array (
			'country' => '+1',
			'city' => '',
			'number' => '' 
	);
	protected $_matches = array ();
	protected function _field_country() {
		if (! $this->_config ['country'])
			return '';
		return Form::select ( $this->_name . '[country]', Country::instance ()->get_calling_code (), $this->_phone ['country'], ( array ) Arr::get ( $this->_attributes, 'country' ) + array (
				'id' => $this->_id . '_country',
				'class' => 'country_code form-control' 
		) );
	}
	protected function _field_city() {
		if (! $this->_config ['number'])
			return '';
		return Form::input ( $this->_name . '[city]', $this->_phone ['city'], ( array ) Arr::get ( $this->_attributes, 'city' ) + array (
				'id' => $this->_id . 'city',
				'maxlength' => '5',
				'size' => '5',
				'class' => 'phone_city form-control' 
		) );
	}
	protected function _field_number() {
		if (! $this->_config ['number'])
			return '';
		return Form::input ( $this->_name . '[number]', $this->_phone ['number'], ( array ) Arr::get ( $this->_attributes, 'number' ) + array (
				'id' => $this->_id . '_number',
				'maxlength' => '15',
				'class' => 'phone_number form-control' 
		) );
	}
	public function render() {
		$values = array (
				':country' => $this->_field_country (),
				':city' => $this->_field_city (),
				':number' => $this->_field_number () 
		);
		return __ ( $this->_config ['fields'], $values );
	}
	
	/**
	 * Parse settings and return date
	 *
	 * @return int
	 */
	protected function _value_from_phone() {
		// $this->_phone = array_map('intval', $this->_phone);
		$this->_phone ['number'] = preg_replace ( '/([^0-9\+]+)/', '', $this->_phone ['number'] );
		$this->_phone ['city'] = preg_replace ( '/([^0-9]+)/', '', $this->_phone ['city'] );
		$this->_phone ['country'] = preg_replace ( '/([^0-9]+)/', '', $this->_phone ['country'] );
		return $this->_phone ['country'] . "-" . $this->_phone ['city'] . "-" . $this->_phone ['number'];
	}
	protected function _set_value($value) {
		if (! $value)
			return;
			// Coming in from $_POST
		if (is_array ( $value )) {
			$this->_phone = Arr::overwrite ( $this->_phone, $value );
		}
		if (is_string ( $value )) {
			if (strstr ( $value, '-' )) {
				$phone = explode ( '-', $value, 3 );
			} else {
				$phone = explode ( ' ', $value, 3 );
			}
			$this->_phone = array (
					'country' => $phone [0],
					'city' => isset ( $phone [1] ) ? $phone [1] : '',
					'number' => isset ( $phone [2] ) ? $phone [2] : '' 
			);
		}
		$value = $this->_value_from_phone ();
		parent::_set_value ( $value );
	}
	
	/**
	 * Display single field (and optionally label) in a wrapper
	 *
	 * @param array $attributes        	
	 * @return string
	 */
	public function single_field(array $attributes) {
		return View::factory ( 'zform/wrappers/radio' )->set ( 'field', $this )->set ( 'attributes', $attributes );
	}
	
	/**
	 * Render the field
	 *
	 * @return string
	 */
	public function form_label() {
		return '<legend>' . __ ( $this->_label ) . '</legend>';
	}
}