<script type="text/javascript">
	if(!window.forma) window.forma={};
	window.forma[<?php echo json_encode($form->attributes['id']) ?>] = <?php echo json_encode($form->data()) ?>;
</script>
