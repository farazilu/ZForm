<?php
defined('SYSPATH') or die('No direct script access.');
return array(
    // Default configuration
    'default' => array(
        'default_column' => array(
            'default' => 'column_default',
            'type' => 'Text',
            'multiline' => false,
            'maxlength' => false
        ),
        'varchar' => array(
            'maxlength' => 'character_maximum_length'
        ),
        'char' => array(
            'maxlength' => 'character_maximum_length'
        ),
        'int' => array(),
        'double' => array(),
        'decimal' => array(),
        'enum' => array(
            'type' => 'Enum',
            'options' => 'options',
            'format' => 'radio'
        ),
        'set' => array(
            'type' => 'Enum',
            'options' => 'options',
            'multiple' => true,
            'separator' => ','
        ),
        'binary' => array(
            'type' => 'Boolean'
        ),
        'tinyint' => array(
            'type' => 'Boolean'
        ),
        'tinytext' => array(
            'type' => 'Text',
            'multiline' => 3
        ),
        'text' => array(
            'type' => 'Textarea',
            'multiline' => 5
        ),
        'mediumtext' => array(
            'type' => 'Textarea',
            'multiline' => 7
        ),
        'longtext' => array(
            'type' => 'Textarea',
            'multiline' => 12
        ),
        'blob' => array(
            'type' => 'Blob',
            'multiline' => 12
        ),
        'date' => array(
            'type' => 'Temporal',
            'format' => 'Y-m-d',
            'year' => true,
            'month' => true,
            'day' => true,
            'fields' => ':month :day :year'
        ),
        'datetime' => array(
            'type' => 'Temporal',
            'format' => 'Y-m-d H:i:s',
            'year' => true,
            'month' => true,
            'day' => true,
            'hour' => true,
            'minute' => true,
            'meridien' => true,
            'fields' => ':month :day :year :hour :minute :second :meridien'
        ),
        'timestamp' => array(
            'type' => 'Temporal',
            'format' => 'Y-m-d H:i:s',
            'year' => true,
            'month' => true,
            'day' => true,
            'hour' => true,
            'minute' => true,
            'meridien' => true,
            'fields' => ':month :day :year :hour :minute :second :meridien'
        ),
        'time' => array(
            'type' => 'Temporal',
            'format' => 'H:i:s',
            'hour' => true,
            'minute' => true,
            'meridien' => true,
            'fields' => ':hour :minute :meridien'
        ),
        'File' => array(
            'type' => 'File'
        ),
        'Image' => array(
            'type' => 'Image'
        ),
        'Phone' => array(
            'type' => 'Phone',
            'country' => TRUE,
            'city' => TRUE,
            'number' => TRUE,
            'fields' => ':country :city :number'
        )
    )
);