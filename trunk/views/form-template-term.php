<?php

	/** @var hw_form $this */

?>
<table class="form-table">
	<?php
		foreach( $this->get_fields() as $field ){
			?>
			<tr class="form-field term-description-wrap hw-field-<?php echo $field->type() ?>">
				<th scope="row"><label for="<?php echo $field->input()->id ?>"><?php echo $field->label() ?></label></th>
				<td><?php $field->the() ?>
					<?php if( $field->description() != '' ){
						?><p class="description"><?php echo $field->description() ?></p><?php
					} ?></td>
			</tr>
			<?php
		}
	?>
</table>