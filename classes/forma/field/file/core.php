<?php defined('SYSPATH') or die('No direct script access.');

abstract class Forma_Field_File_Core extends Forma_Field
{
	public function __construct($name, $options = array())
	{
		parent::__construct($name, $options);

		$this->attributes['type'] = 'file';
		$this->rules += array('Upload::valid' => null);

		if ($this->required)
		{
			$this->rules += array('Upload::not_empty' => null);
		}
	}

	public function get()
	{
		if (is_array($this->value) && Upload::valid($this->value))
		{
			return Arr::extract($this->value, array('name', 'size'));
		}

		return null;
	}
}
