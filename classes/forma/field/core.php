<?php defined('SYSPATH') or die('No direct script access.');

abstract class Forma_Field_Core
{
	/**
	 * @var string The name of the field.
	 */
	public $name;

	/**
	 * @var string The string representation of the field's current value.
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
	 * @var array Validation rules for this field.
	 */
	public $rules = array();

	public $attributes = array(
		'id' => NULL,
	);

	public $required = true;

	/**
	 * Creates and initializes the form field.
	 */
	public function __construct($name, $options = array())
	{
		$options['name'] = $name;

		// Assign each option as object attributes.
		foreach ($options as $name => $value)
		{
			$this->$name = $value;
		}

		// If no label was given, generate one from the name.
		if ( ! $this->label)
		{
			$this->label = ucwords(preg_replace('/[\W_]+/', ' ', $this->name));
		}

		// If no ID was given, generate one.
		if ( ! $this->attributes['id'] )
		{
			$this->attributes['id'] = Forma::uniqid();
		}

		if ($this->required)
		{
			$this->rules += array('not_empty' => NULL);
		}
	}

	/**
	 * Returns the field's value. This function should be able to return three
	 * distinct values representing three states:
	 *  null - to represent the field value being unset
	 *  empty - a representation of this field's value being empty
	 *  value - some representation of this field's value
	 * @return mixed
	 */
	public function get()
	{
		return $this->value;
	}

	/**
	 * Sets the field's value and returns it.
	 * @param mixed $value
	 * @return mixed
	 */
	public function set($value)
	{
		$this->value = $value;
		return $this->get();
	}

	public function check($data)
	{
		// Create a local Validate object and perform validation.
		$validate = Validate::factory(Arr::extract($data, array($this->name)));
		$validate->rules($this->name, $this->rules);
		$check = $validate->check();

		printf("%s rules: %s<br />", $this->name, print_r($this->rules, true));
		printf("checking %s against %s: %s<br />", $this->name, print_r($validate->as_array(), true), $check);

		// Add any errors to the form's validate object.
		foreach($validate->errors() as $field => $value)
		{
			list($error, $params) = $value;
			$data->error($field, $error, $params);
			printf("%s failed: %s<br />", $field, $error);
		}

		return $check;
	}

	public function render()
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
