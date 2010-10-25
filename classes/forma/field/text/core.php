<?php defined('SYSPATH') or die('No direct script access.');

abstract class Forma_Field_Text_Core extends Forma_Field
{
	public function value()
	{
		return (string) $this->value;
	}
}
