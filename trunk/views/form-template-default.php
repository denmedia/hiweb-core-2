<?php

	/** @var hw_form $this */

	foreach( $this->get_fields() as $field ){
		if( $field instanceof hw_field ){
			?>
		<div class="hw-form-field hw-field-<?= $field->type() ?>" data-field-id="<?= $field->id() ?>" data-field-global-id="<?= $field->global_id() ?>">
			<p class="name"><?php echo $field->label() ?></p>
			<?php $field->the(); ?>
			<?php echo $field->description() != '' ? '<p class="description">' . $field->description() . '</p>' : ''; ?>
			</div><?php
		}elseif($field instanceof hw_field_separator){
			?>
			<div class="hw-form-field-separator"><?php $field->the() ?></div>
			<?php
		} else {
			?>
			<div class="hw-form-field"><?php hiweb()->dump( $field ) ?></div><?php
		}
	}

?>