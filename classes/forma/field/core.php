<?php defined('SYSPATH') or die('No direct script access.');

abstract class Forma_Field_Core
{
	/**
	 * @var string The current value of the field.
	 */
	public $value;
	
	/**
	 * @var string A human-readable label for the field.
	 */
	public $label;

	/**
	 * @var string Extended instructions for the field.
	 */
	public $instructions;

	/**
	 * @var array The field's dependencies.
	 */
	public $depends = array();

	public function __construct($options = array())
	{
		foreach ($options as $name => $value)
		{
			$this->$name = $value;
		}
	}

	/**
	 * Gets the field's value processed according to teh field's standards.
	 */
	public function get()
	{
		return $this->value;
	}

}
