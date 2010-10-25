<?php defined('SYSPATH') or die('No direct script access.');

abstract class Forma_Core
{
	public static function factory($form, $values = array())
	{
		$class = 'form_' . $form;
		return new $class($values);
	}

	private static $_uniqid_count = 0;
	public static function uniqid($prefix = 'forma-')
	{
		self::$_uniqid_count += 1;
		return $prefix . self::$_uniqid_count;
	}
}
