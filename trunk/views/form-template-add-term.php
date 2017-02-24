<?php

	/** @var hw_form $this */

	foreach( $this->get_fields() as $field ){

		?>
		<div class="form-field term-<?php echo $field->get_id() ?>-wrap">
			<label for="<?php echo $field->input()->id ?>"><?php echo $field->name() ?></label>
			<?php $field->the() ?>
			<?php if( $field->description() != '' ){
				?><p class="description"><?php echo $field->description() ?></p><?php
			} ?>
		</div>

	<?php } ?>