<div class="forma-field <?php if ($field->is_invalid) : ?>invalid<?php endif; ?>">
	<div class="forma-label" <?php if ($field->hide_label) : ?>style="display: none"<?php endif; ?>>
		<?php echo $label ?>
	</div>

	<div class="forma-input">
		<?php echo $input ?>
	</div>
</div>
