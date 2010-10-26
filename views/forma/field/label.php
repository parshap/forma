<?php

if ($field->required)
{
	echo '<span class="required" title="This field is required">*</span>';
}

echo Form::label($field->attributes['id'], __($field->label));

echo ':';

