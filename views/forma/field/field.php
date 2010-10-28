<div class="forma-field <?php if ($field->is_invalid) : ?>invalid<?php endif; ?>">
	<div class="forma-label" <?php if ($field->hide_label) : ?>style="display: none"<?php endif; ?>>
		<?php echo $label ?>
	</div>

	<div class="forma-extra">
		<?= $field->extra ?>
		<?php if (isset($field->rules['max_words'])) : ?>
			<?= $field->rules['max_words'][0] ?> words max.
		<?php endif; ?>
		<?php if (isset($field->rules['Upload::type'])) : ?>
			Accepted file types: <?php echo implode(', ', $field->rules['Upload::type'][0]) ?>
		<?php endif; ?>
	</div>

	<div class="forma-input">
		<?php echo $input ?>
	</div>
</div>
