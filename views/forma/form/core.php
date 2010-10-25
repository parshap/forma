<?php echo Form::open($form->action, $form->attributes) ?>
	<?php if ($form->errors) : ?>
		<div class="forma-errors">
			<ul>
				<?php foreach ($form->errors as $error) : ?>
					<li><?= $error ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<?php foreach ($form->fields() as $field) : ?>
		<?php echo $field->render() ?>
	<?php endforeach; ?>

	<?php echo Form::submit(NULL, __('Submit')) ?>
<?php echo Form::close() ?>
