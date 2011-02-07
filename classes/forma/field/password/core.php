<?php defined('SYSPATH') or die('No direct script access.');

abstract class Forma_Field_Password_Core extends Forma_Field_Text
{
	public function __construct($name, $options = array())
	{
		parent::__construct($name, $options);

		$this->attributes['type'] = 'password';
	}
}
