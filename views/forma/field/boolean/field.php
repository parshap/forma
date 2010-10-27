<div class="forma-field forma-field-checkbox <?php if ($field->is_invalid) : ?>invalid<?php endif; ?>">
	<div class="forma-label">
		<?php echo Form::label($field->attributes['id'], __($field->label)) ?>
	</div>

	<div class="forma-input">
		<?php echo Form::checkbox($field->name, null, $field->get(), $field->attributes) ?>
	</div>
</div>

