<?php

	/** @var hw_form $this */

?>
<table class="form-table">
	<?php
		foreach( $this->get_fields() as $field ){
			if( $field instanceof hw_field ){
				?>
				<tr class="form-field term-description-wrap hw-field-<?= $field->type() ?>" data-field-id="<?= $field->id() ?>" data-field-global-id="<?= $field->global_id() ?>">
					<th scope="row"><label for="<?php echo $field->input()->id() ?>"><?php echo $field->label() ?></label></th>
					<td><?php $field->the() ?>
						<?php if( $field->description() != '' ){
							?><p class="description"><?php echo $field->description() ?></p><?php
						} ?></td>
				</tr>
				<?php
			} elseif( $field instanceof hw_field_separator ) {
				?>
				<div class="hw-form-field-separator"><?php $field->the() ?></div>
				<?php
			}
		}
	?>
</table>