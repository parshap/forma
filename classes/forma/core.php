<?php defined('SYSPATH') or die('No direct script access.');

abstract class Forma_Core
{
	protected static $_form_prefix = 'form_';

	public static function factory($form, $values = array())
	{
		$class = Forma::$_form_prefix . $form;
		return new $class($values);
	}

	public static function class_name($form)
	{
	}

	public static function form_name($form)
	{
	}
}
