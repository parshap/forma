<?php defined('SYSPATH') or die('No direct script access.');

abstract class Forma_Form_Core
{
	/**
	 * @var array List of fields the form has.
	 */
	private $_fields = array();
	
	/**
	 * @var string The URI to use for the form's action.
	 */
	public $action = null;

	public $attributes = array('class' => 'forma');

	public $errors = array();

	private $_saved = true;

	private $_changed = array();

	public function __construct($values = array())
	{
		call_user_func(array(get_class($this), 'initialize'), $this);

		if ($values)
		{
			$this->set($values);
		}
	}

	/**
	 * Returns or sets field(s) of the form.
	 * $form->fields() - returns all fields
	 * $form->fields('password') - returns the field named password
	 * $form->fields($field_object) - adds the field
	 * $form->fields($array_of_field_objects) - adds each field
	 */
	public function fields($param = null)
	{
		// If no parameter was passed, return all of the form's fields.
		// case: $form->fields()
		if (func_num_args() === 0)
		{
			return $this->_fields;
		}

		// If the parameter is a Forma_Field object, we are adding that field.
		// Put it in the array form we expect.
		// case: $form->fields('password', $field_object)
		else if (is_object($param) && is_subclass_of($param, 'Forma_Field'))
		{
			$param = array($param->name => $param);
		}

		// If the parameter is a string, return the field with that name.
		// case: $form->fields('password')
		else if (is_string($param) && isset($this->_fields[$param]))
		{
			return $this->_fields[$param];
		}

		// If the parameter is an array, we are adding some fields. Return the
		// form object so that calls can be chained.
		// case: $form->fields($array_of_field_objects)
		if (is_array($param))
		{
			foreach($param as $field)
			{
				$this->_fields[$field->name] = $field;

				// If the field is a file upload, add the enctype attribute.
				if(Arr::get($field->attributes, 'type') === 'file')
				{
					$this->attributes['enctype'] = 'multipart/form-data';
				}
			}

			return $this;
		}

		return null;
	}

	/**
	 * Sets the values of form fields.
	 * @param mixed $values array of field value pairs or the name of the field
	 * @param string $value
	 */
	public function set($values, $value = NULL)
	{
		if ( ! is_array($values))
		{
			$values = array($values => $value);
		}

		foreach($values as $key => $value)
		{
			$keys = explode(',', $key);
			$name = $keys[0];
			$field = $this->fields($name);

			// Wrap the value around an associate array based on the key.
			// e.g., leader,name => array('name' => $value)
			foreach(array_reverse(array_slice($keys, 1)) as $key)
			{
				$value = array($key => $value);
			}

			// If this is not a form field, skip it.
			if ( ! $field)
			{
				continue;
			}

			$old_value = $field->get();
			$new_value = $field->set($value);

			if ($old_value !== null && $new_value === $old_value)
			{
				continue;
			}

			$this->_changed[$field->name] = $field->value;

			$this->_saved = false;
		}
	}

	/**
	 * Validates the current state of the form.
	 */
	public function check()
	{
		$check = true;
		$data = Validate::factory($this->_changed);

		foreach ($this->fields() as $field)
		{
			$check = $field->check($data) && $check;
			$data->label($field->name, $field->label);
		}

		$this->errors = array();

		if ( ! $check)
		{
			$this->errors = $data->errors('form/' . Forma::form_name($this));
		}

		return $check;
	}

	public function render()
	{
		$view_file = $this->get_view_file();

		return View::factory($view_file, array('form' => $this));
	}

	protected function get_view_file($class_name = NULL)
	{
		if($class_name === NULL)
		{
			$class_name = get_class($this);
		}

		$view_name = str_replace(
			array('forma_', 'form_'),
			array('', ''),
			strtolower($class_name)
		);

		$view_file = 'forma/form/' . $view_name;

		// If we can't find the view file, use the parent's.
		if ( ! Kohana::find_file('views', $view_file) && $class_name !== __CLASS__)
		{
			return $this->get_view_file(get_parent_class($class_name));
		}

		return $view_file;
	}
}
