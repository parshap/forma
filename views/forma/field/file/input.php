<?php echo Form::file($field->name, $field->attributes) ?>

<?php if ($field->get()) : ?>
	<div class="forma-fileinfo">
		<strong>Attached</strong>: <span><?echo  Arr::get($field->get(), 'name') ?></span>
		<a href="#" class="remove">[x] Remove</a>
		<?php echo Form::hidden($field->name . ',path', Arr::get($field->get(), 'path')) ?>
		<?php echo Form::hidden($field->name . ',name', Arr::get($field->get(), 'name')) ?>
	</div>
<?php endif; ?>
