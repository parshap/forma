<?php defined('SYSPATH') or die('No direct script access.');

abstract class Forma_Core
{
	public static function factory($form, $values = array())
	{
		$class = 'form_' . $form;
		return new $class($values);
	}
}
