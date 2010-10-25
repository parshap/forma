<div class="forma-field">
	<?php echo Form::label($field->attributes['id'], __($field->label)) ?>
	<?php echo Form::input(
		$field->name, $field->value, $field->attributes
	) ?>
</div>
