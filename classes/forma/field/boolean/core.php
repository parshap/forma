<?php defined('SYSPATH') or die('No direct script access.');

class Forma_Field_Boolean_Core extends Forma_Field
{
	public function get()
	{
		return $this->value === null ? null : (bool) $this->value;
	}
}

