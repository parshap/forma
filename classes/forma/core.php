<?php defined('SYSPATH') or die('No direct script access.');

abstract class Forma_Core
{
	public static function factory($form, $values = array())
	{
		$class = 'forma_form_' . $form;
		return new $class($values);
	}

	public static function class_name($form)
	{
	}

	public static function form_name($form)
	{
	}
}
