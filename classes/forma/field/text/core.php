<?php defined('SYSPATH') or die('No direct script access.');

abstract class Forma_Field_Text_Core extends Forma_Field
{
	public function get()
	{
		return $this->value === null ? null : (string) $this->value;
	}
}
