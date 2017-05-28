<?php

	/** @var hw_form $this */

	foreach( $this->get_fields() as $field ){

		?>
		<div class="form-field term-<?php echo $field->id() ?>-wrap">
			<label for="<?php echo $field->input()->id ?>"><?php echo $field->label() ?></label>
			<?php $field->the() ?>
			<?php if( $field->description() != '' ){
				?><p class="description"><?php echo $field->description() ?></p><?php
			} ?>
		</div>

	<?php } ?>