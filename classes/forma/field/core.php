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

	/**
	 * @var bool A field is invalid if it has failed to pass validation.
	 */
	public $is_invalid = false;

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
		$this->is_invalid = ! $validate->check();

		printf("%s rules: %s<br />", $this->name, print_r($this->rules, true));
		printf("checking %s against %s: %s<br />", $this->name, print_r($validate->as_array(), true), ! $this->is_invalid);

		// Add any errors to the form's validate object.
		foreach($validate->errors() as $field => $value)
		{
			list($error, $params) = $value;
			$data->error($field, $error, $params);
			printf("%s failed: %s<br />", $field, $error);
		}

		return ! $this->is_invalid;
	}

	public function render()
	{
		$file = Forma_Field::get_view_file('field', get_class($this));

		return View::factory($file, array(
			'field' => $this,
			'label' => $this->render_label(),
			'input' => $this->render_input(),
		));
	}

	public function render_label()
	{
		$file = Forma_Field::get_view_file('label', get_class($this));

		return View::factory($file, array('field' => $this));
	}

	public function render_input()
	{
		$file = Forma_Field::get_view_file('input', get_class($this));

		return View::factory($file, array('field' => $this));
	}

	protected static function get_view_file($name, $class_name, $directory = 'forma/field')
	{
		$path = $directory;
		$subdir = str_replace('forma_field_', '', strtolower($class_name));

		if( ! empty($subdir) && $subdir !== 'core')
		{
			$path .= '/' . $subdir;
		}

		$path .= '/' . $name;

		// If we can't find the view file, use the parent's.
		if ( ! Kohana::find_file('views', $path) && $class_name !== __CLASS__)
		{
			return Forma_Field::get_view_file($name, get_parent_class($class_name), $directory);
		}

		return $path;
	}
}
