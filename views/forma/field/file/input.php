<?php echo Form::file($field->name, $field->attributes) ?>

<?php if ($field->get()) : ?>
	<?php echo sprintf('Uploaded: %s', Arr::get($field->get(), 'path')) ?>
	<?php echo Form::hidden($field->name . ',path', Arr::get($field->get(), 'path')) ?>
<?php endif; ?>

