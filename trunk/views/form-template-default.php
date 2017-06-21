<?php

	/** @var hw_form $this */

	foreach( $this->get_fields() as $field ){
		if( $field instanceof hw_field ){
			?>
		<div class="hw-form-field hw-field-<?php echo $field->type() ?>">
			<p class="name"><?php echo $field->label() ?></p>
			<?php $field->the(); ?>
			<?php echo $field->description() != '' ? '<p class="description">' . $field->description() . '</p>' : ''; ?>
			</div><?php
		} else {
			?>
			<div class="hw-form-field"><?php hiweb()->dump( $field ) ?></div><?php
		}
	}

?>