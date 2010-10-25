<?php defined('SYSPATH') or die('No direct script access.');

abstract class Forma_Core
{
	protected static $_form_prefix = 'form_';

	public static function factory($form, $values = array())
	{
		$class = Forma::$_form_prefix . $form;
		return new $class($values);
	}

	private static $_uniqid_count = 0;
	public static function uniqid($prefix = 'forma-')
	{
		self::$_uniqid_count += 1;
		return $prefix . self::$_uniqid_count;
	}

	public static function form_name($form)
	{
		if ($form instanceof Forma_Form)
		{
			$form = get_class($form);
		}

		if (strtolower(substr($form, 0, strlen(Forma::$_form_prefix))) ===
			strtolower(Forma::$_form_prefix)
		)
		{
			$form = substr($form, strlen(Forma::$_form_prefix));
		}

		return strtolower($form);
	}
}
