<?php

$attributes = $field->attributes;
if ($field->size)
{
	$attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' ' : '';
	$attributes['class'] .= ' forma-size-' . $field->size;
}

?>

<?php if ($field->multiline) : ?>
	<?php echo Form::textarea($field->name, $field->value, $attributes) ?>
<?php else : ?>
	<?php echo Form::input($field->name, $field->value, $attributes) ?>
<?php endif; ?>
