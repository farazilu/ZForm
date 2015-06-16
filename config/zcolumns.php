<?php defined('SYSPATH') or die('No direct script access.');

return array(
	// Default  configuration
	'default' => array(
		'default_column' => array(
			'default'    => 'column_default',
			'type'       => 'Text',
			'multiline'  => false,
			'maxlength'  => false,
		),
		'varchar' => array(
			'maxlength'  => 'character_maximum_length',
		),
		'char'    => array(
			'maxlength'  => 'character_maximum_length',
		),
		'int'     => array(),
		'double'  => array(),
		'decimal' => array(),
		'enum'    => array(
			'type'       => 'Enum',
			'options'    => 'options',
			'format'     => 'radio',
		),
		'set'      => array(
			'type'       => 'Enum',
			'options'    => 'options',
			'multiple'   => true,
			'separator'  => ',',
		),
		'tinyint' => array(
			'type'       => 'Boolean',
		),
		'tinytext'   => array(
			'type'       => 'Text',
			'multiline'  => 3,
		),
		'text'       => array(
			'type'       => 'Text',
			'multiline'  => 5,
		),
		'mediumtext' => array(
			'type'       => 'Text',
			'multiline'  => 7,
		),
		'longtext'   => array(
			'type'       => 'Text',
			'multiline'  => 12,
		),
		'date'       => array(
			'type'       => 'Temporal',
			'format'     => 'Y-m-d',
			'year'       => true,
			'month'      => true,
			'day'        => true,
			'fields'     => ':month :day :year',
			'wrapper'    => 'zform/wrappers/date',
		),
		'datetime'   => array(
			'type'       => 'Temporal',
			'format'     => DateTime::RFC3339,
			'year'       => true,
			'month'      => true,
			'day'        => true,
			'hour'       => true,
			'minute'     => true,
			'meridien'   => true,
			'fields'     => ':month :day :year :hour :minute :second :meridien',
		),
		'timestamp'   => array(
			'type'       => 'Temporal',
			'format'     => DateTime::RFC3339,
			'year'       => true,
			'month'      => true,
			'day'        => true,
			'hour'       => true,
			'minute'     => true,
			'meridien'   => true,
			'fields'     => ':month :day :year :hour :minute :second :meridien',
		),
		'time'        => array(
			'type'       => 'Temporal',
			'format'     => 'H:i:s',
			'hour'       => true,
			'minute'     => true,
			'meridien'   => true,
			'fields'     => ':hour :minute :meridien',
		),
	),
);