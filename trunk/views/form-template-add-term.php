<?php

	/** @var hw_form $this */

	foreach( $this->get_fields() as $field ){

		?>
		<div class="form-field term-<?=$field->id() ?>-wrap" data-field-id="<?=$field->id()?>" data-field-global-id="<?=$field->global_id()?>">
			<label for="<?php echo $field->input()->id ?>"><?php echo $field->label() ?></label>
			<?php $field->the() ?>
			<?php if( $field->description() != '' ){
				?><p class="description"><?php echo $field->description() ?></p><?php
			} ?>
		</div>

	<?php } ?>