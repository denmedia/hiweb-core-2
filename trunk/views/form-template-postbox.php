<?php
	/**
	 * @var hw_form $this
	 * @var string  $addition_class
	 */
?>
<div class="hw-form-template-postbox <?php echo $addition_class ?>"><?php

		foreach( $this->get_fields() as $field ){
			if( $field instanceof hw_field ){
				?>
				<span class="hw-form-field hw-field-<?= $field->type() ?>" data-field-id="<?= $field->id() ?>" data-field-global-id="<?= $field->global_id() ?>">
				<p><strong><?php echo $field->label() ?></strong></p>
					<?php $field->the(); ?>
					<?php echo $field->description() != '' ? '<p class="description">' . $field->description() . '</p>' : ''; ?>
				</span>
				<?php
			} elseif( $field instanceof hw_field_separator ) {
				?>
				<div class="hw-form-field-separator"><?php $field->the() ?></div>
				<?php
			} else {
				?>
				<div class="hw-form-field"><?php hiweb()->dump( $field ) ?></div><?php
			}
		}

	?></div>