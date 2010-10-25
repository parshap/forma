<div class="forma-field">
	<?php echo Form::label($field->attributes['id'], __($field->label)) ?>
<?= sprintf('Uploaded: %s', Arr::get($field->get(), 'name')) ?>
	<?php echo Form::file($field->name, $field->attributes) ?>
</div>
