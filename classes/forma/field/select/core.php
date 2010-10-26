<?php defined('SYSPATH') or die('No direct script access.');

abstract class Forma_Field_Select_Core extends Forma_Field
{
	public $options = array();

	public function __construct($name, $options = array())
	{
		parent::__construct($name, $options);

		if ( ! $this->required)
		{
			Arr::unshift($this->options, '', '');
		}

		$this->rules += array('in_array' => array(array_keys($this->options)));
	}
	
	public function set($value)
	{
		$this->value = $value;

		return $this->get();
	}

	public function get()
	{
		return array_key_exists($this->value, $this->options) ? $this->value : null;
	}
}
