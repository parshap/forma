<?php defined('SYSPATH') or die('No direct script access.');

abstract class Forma_Form_Core
{
	/**
	 * @var array List of fields the form has.
	 */
	private $_fields = array();
	
	/**
	 * @var string The HTTP method to use with teh form.
	 */
	public $method = 'POST';

	/**
	 * @var string The URI to use for the form's action.
	 */
	public $action = '';


	public function __construct($values = array())
	{
		call_user_func(array(get_class($this), 'initialize'), $this);

		if($values)
		{
			$this->set($values);
		}
	}

	/**
	 * Returns or sets field(s) of the form.
	 * $form->fields() - returns all fields
	 * $form->fields('password') - returns the field named password
	 * $form->fields('password', $field_object) - sets the password field
	 * $form->fields($array_of_field_objects) - sets each field
	 */
	public function fields($param = null, $field = null)
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
		else if (is_subclass_of($field, 'Forma_Field'))
		{
			$param = array($param => $field);
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
			// Set the field's name.
			foreach($param as $name => $field)
			{
				$field->name = $name;
			}

			$this->_fields += $param;
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

			foreach($values as $key => $value)
			{
				$field = $this->fields($key);

				// If this is not a form field, skip it.
				if ( ! $field)
				{
					continue;
				}

				$old_value = $field->value;
				$value = $field->set($value);

				if ($value === $old_value)
				{
					continue;
				}

				$this->_changed[$field->name] = $value;

				$this->_saved = false;
			}
		}
	}
}
