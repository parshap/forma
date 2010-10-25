<?php defined('SYSPATH') or die('No direct script access.');

abstract class Forma_Field_Email_Core extends Forma_Field_Text
{
	public function __construct($name, $options = array())
	{
		parent::__construct($name, $options);

		$this->attributes['type'] = 'email';
		$this->rules += array('email' => null);
	}
}
