<?php defined('SYSPATH') or die('No direct script access.');

abstract class Forma_Field_File_Core extends Forma_Field
{
	public function __construct($name, $options = array())
	{
		parent::__construct($name, $options);

		$this->attributes['type'] = 'file';
		$this->rules += array('Upload::valid' => null);

		if ($this->required)
		{
			$this->rules += array('Upload::not_empty' => null);
		}
	}

	protected function load($path)
	{
		// We already know that the upload is being carried from a previous
		// submit and is not empty, so remove the validation rule. If we do not
		// it will fail, as tmp_name does not exist.
		unset($this->rules['Upload::not_empty']);

		return array(
			'name' => basename($path),
			'size' => filesize($path),
			'tmp_name' => '',
			'path' => $path,
			'type' => File::mime($path),
			'error' => 0,
		);
	}

	public function set($value)
	{
		if (Arr::get($value, 'path'))
		{
			$this->value = $this->load($value['path']);
		}

		if (Arr::get($value, 'name'))
		{
			$this->value['name'] = $value['name'];
		}

		if (is_array($value) && Upload::not_empty($value))
		{
			// We already know that the upload was not empty, so remove the
			// validation rule. If we do not, it will fail as the file at
			// tmp_name will no longer exist after Upload::save is called.
			unset($this->rules['Upload::not_empty']);

			if ( ($path = Upload::save($value)) !== FALSE)
			{
				$value['path'] = $value['tmp_name'] = 'upload/' . basename($path);
				$this->value = $value;
			}
		}

		return $this->get();
	}

	public function get()
	{
		if ( ! $this->value)
		{
			return null;
		}

		if (is_array($this->value) && Upload::valid($this->value))
		{
			return Arr::extract($this->value, array('name', 'size', 'path', 'type'));
		}

		return array();
	}
}
