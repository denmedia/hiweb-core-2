<?php

	/** @var hw_form $this */

	foreach( $this->fields() as $id => $field ){
		?>
		<div class="hw-form-field">
		<p><strong><?php echo $field->title() ?></strong></p>
		<?php $field->the(); ?>
		<?php echo $field->description() != '' ? '<p class="description">' . $field->description() . '</p>' : ''; ?>
		</div><?php
	}

?>