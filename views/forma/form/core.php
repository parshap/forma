<?php echo Form::open($form->action, $form->attributes) ?>
	<?php foreach($form->fields() as $field) : ?>
		<?php echo $field->render() ?>
	<?php endforeach; ?>
	<?php echo Form::submit(NULL, __('Submit')) ?>
<?php echo Form::close() ?>
