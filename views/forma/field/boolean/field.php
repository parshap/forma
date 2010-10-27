<div class="forma-field forma-field-checkbox <?php if ($field->is_invalid) : ?>invalid<?php endif; ?>">
	<div class="forma-label">
		<?php echo Form::label($field->attributes['id'], __($field->label)) ?>
	</div>

	<div class="forma-input">
		<?php echo Form::input($field->name, $field->value, $field->attributes) ?>
	</div>
</div>

