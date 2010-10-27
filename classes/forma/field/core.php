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
		'id' => null,
	);

	public $required = true;

	public $hide_label = false;

	/*
	 * @var mixed The limit of this field being repeated. Can be a number or
	 * '+' for "1 or more".
	 * @todo: implement '*', 'n+', 'n-'
	 */
	public $limit = 1;

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
		// If there are no validation rules then we pass!
		if( ! $this->rules)
		{
			return ! $this->is_invalid = false;
		}

		// If this field's value is not set, set it to null so that validation
		// can do its job.
		if ( ! isset($data[$this->name]))
		{
			$data[$this->name] = null;
		}

		// Create a local Validate object and perform validation.
		$validate = Validate::factory(Arr::extract($data, array($this->name)));
		$validate->rules($this->name, $this->rules);
		$this->is_invalid = ! $validate->check();

		// Add any errors to the form's validate object.
		foreach($validate->errors() as $field => $value)
		{
			list($error, $params) = $value;
			$data->error($field, $error, $params);
		}

		return ! $this->is_invalid;
	}

	/**
	 * Determines if this field meets its dependencies.
	 * @return bool
	 */
	public function depends($values)
	{
		// If there are no dependencies, we've met them!
		if ( ! $this->depends)
		{
			return true;
		}

		// If we fail any of our dependencies, return false.
		foreach ($this->depends as $field => $regex)
		{
			// Special processing of "not empty" regex, ".+", so that it works
			// on array values as well.
			if ($regex === '.+')
			{
				if ( empty($values[$field]))
				{
					return false;
				}
			}

			else if ( ! preg_match('/'.$regex.'/', $values[$field]))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Returns a representation of this field ready for json use.
	 */
	public function data()
	{
		return get_object_vars($this);
	}

	public function render()
	{
		$file = Forma_Field::get_view_file('field', get_class($this));

		return View::factory($file, array(
			'field' => $this,
			'label' => $this->render_label(),
			'input' => $this->render_input(),
		))->render();
	}

	public function render_label()
	{
		$file = Forma_Field::get_view_file('label', get_class($this));

		return View::factory($file, array('field' => $this))->render();
	}

	public function render_input()
	{
		$file = Forma_Field::get_view_file('input', get_class($this));

		return View::factory($file, array('field' => $this))->render();
	}

	protected static function get_view_file($name, $class_name, $directory = 'forma/field')
	{
		// @todo: More robust view hierarchy to allow overrides on a per-form.
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
