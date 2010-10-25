<div class="forma-field">
	<?php echo Form::label($field->attributes['id'], __($field->label)) ?>

	<?php if ($field->get()) : ?>
		<?php echo sprintf('Uploaded: %s', Arr::get($field->get(), 'path')) ?>
		<?php echo Form::hidden($field->name . ',path', Arr::get($field->get(), 'path')) ?>
	<?php endif; ?>

	<?php echo Form::file($field->name, $field->attributes) ?>
</div>
