<?php defined('SYSPATH') or die('No direct script access.');

abstract class Forma_Field_Core
{
	/**
	 * @var string The name of the field.
	 */
	public $name;

	/**
	 * @var string The current value of the field.
	 */
	public $value;
	
	/**
	 * @var string A human-readable label for the field.
	 */
	public $label;

	/**
	 * @var string Extended instructions for the field.
	 */
	public $instructions;

	/**
	 * @var array The field's dependencies.
	 */
	public $depends = array();

	/**
	 * @var string The field's id.
	 */
	protected $id;

	public function __construct($options = array())
	{
		foreach ($options as $name => $value)
		{
			$this->$name = $value;
		}
	}

	/**
	 * Gets the field's value processed according to teh field's standards.
	 */
	public function value()
	{
		return $this->value;
	}

	/**
	 * Returns the unique id attribute of this field.
	 */
	public function id()
	{
		if ( ! $this->id)
		{
			$this->id = Forma::uniqid();
		}

		return $this->id;
	}

	public function attributes()
	{
		return array(
			'id' => $this->id(),
		);
	}

	public function render($file = NULL)
	{
		$view_file = $this->get_view_file();

		return View::factory($view_file, array('field' => $this));
	}

	protected function get_view_file($class_name = NULL)
	{
		if($class_name === NULL)
		{
			$class_name = get_class($this);
		}

		$view_name = str_replace('forma_field_', '', strtolower($class_name));
		$view_file = 'forma/field/' . $view_name;

		// If we can't find the view file, use the parent's.
		if ( ! Kohana::find_file('views', $view_file) && $class_name !== __CLASS__)
		{
			return $this->get_view_file(get_parent_class($class_name));
		}

		return $view_file;
	}

}
