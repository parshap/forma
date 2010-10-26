<?php defined('SYSPATH') or die('No direct script access.');

abstract class Forma_Form_Core
{
	/**
	 * @var string The URI to use for the form's action.
	 */
	public $action;

	public $attributes = array(
		'id' => null,
		'class' => 'forma',
	);

	public $errors = array();

	public $name;

	/**
	 * @var array List of fields the form has.
	 */
	protected $_fields = array();

	protected $_saved = true;

	protected $_changed = array();

	protected $_pending_dependencies = array();

	public static function initialize($form)
	{
	}

	public function __construct($values = array())
	{
		$this->name = get_class($this);

		call_user_func(array(get_class($this), 'initialize'), $this);

		// If no ID was given, generate one.
		if ( ! $this->attributes['id'] )
		{
			$this->attributes['id'] = Forma::uniqid();
		}

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
	 * Returns an associative array of field names and field values.
	 * @return array
	 */
	public function values()
	{
		$values = array();

		foreach($this->fields() as $field)
		{
			$values[$field->name] = $field->get();
		}

		return $values;
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

		$changed = false;

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

			// If this field's dependencies are not met, save the the value (in
			// case dependencies are met in the future) and skip it.
			if ( ! $field->depends($this->values()))
			{
				$this->_pending_dependencies[$field->name] = $value;
				continue;
			}

			// Since this field's value is getting updated, we no longer care
			// about any previous dependencies-pending values.
			unset($this->_pending_dependencies[$field->name]);

			$old_value = $field->get();
			$new_value = $field->set($value);

			if ($old_value !== null && $new_value === $old_value)
			{
				continue;
			}

			$this->_changed[$field->name] = $field->value;
			$this->_saved = ! $changed = true;
		}

		// Since the form's state has changed, re-evaluate dependencies.
		if ($changed)
		{
			$this->_evaluate_dependencies();
		}
	}

	protected function _evaluate_dependencies()
	{
		// Unset any fields that do not meet dependancies.
		foreach($this->fields() as $field)
		{
			if ( ! $field->depends($this->values()))
			{
				$this->_pending_dependencies[$field->name] = $field->value;
				// @todo: $this->unset() ?
				$this->set(null);
			}
		}

		$this->set($this->_pending_dependencies);
	}

	/**
	 * Validates the current state of the form.
	 */
	public function check()
	{
		$check = true;
		$data = Validate::factory($this->_changed);

		// Validate each of the form's fields.
		foreach ($this->fields() as $field)
		{
			// If this field does not meet its dependencies, we do not need to
			// validate it.
			if ( ! $field->depends($this->values()))
			{
				continue;
			}

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

	/**
	 * Returns the meta data associated with this form as primitive arrays
	 * (intended for use with json_encode).
	 */
	public function data()
	{
		// Initialize the data array.
		$d = array(
			'fields' => array(),
		);

		foreach ($this->fields() as $field)
		{
			$d['fields'][$field->name] = $field->data();
		}

		return $d;
	}

	/**
	 * Used to render the form. If no parameters are passed, the entire form
	 * is rendered. If any parameters are passed they will be used as the names
	 * of the fields to render.
	 */
	public function render($field = null)
	{
		// No parameters are passed - render the form.
		if (func_num_args() === 0)
		{
			return View::factory(
				$this->_get_view_file(),
				array('form' => $this, 'script' => $this->render_script())
			);
		}

		$output = array();
		for ($i = 0; $i < func_num_args(); $i++)
		{
			// @todo: throw exception if invalid field - hard to debug otherwise
			$output[] = $this->fields(func_get_arg($i))->render();
		}
		return implode("\n", $output);
	}

	public function render_script()
	{
		return View::factory('forma/script', array('form' => $this))->render();
	}

	protected function _get_view_file($class_name = null)
	{
		// @todo: allow views in form/ in addition to forma/form/
		if ( ! $class_name)
		{
			$form_name = $this->name;
			$class_name = get_class($this);
		}
		else
		{
			$form_name = str_replace(
				array('forma_form_', 'form_'),
				array('', ''),
				strtolower($class_name)
			);
		}

		$view_file = 'forma/form/' . $form_name;

		// If we can't find the view file, use the parent's.
		if ( ! Kohana::find_file('views', $view_file) && $class_name !== __CLASS__)
		{
			return $this->_get_view_file(get_parent_class($class_name));
		}

		return $view_file;
	}
}
