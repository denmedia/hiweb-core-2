<?php
	
	/** @var hw_form $this */
	
	if( !$this->have_fields() ){
		hiweb()->console()->warn( 'Для формы id[' . $this->id . '] нет полей ввода.', true );
	} else
		foreach( $this->get_fields() as $field ){
			if( $field instanceof hw_input ){
				?>
				<div class="hw-form-field hw-form-field-compact">
				<div><strong><?php echo $field->title() ?></strong></div>
				<?php $field->the(); ?>
				<?php echo $field->description() != '' ? '<div class="description">' . $field->description() . '</div>' : ''; ?>
				</div><?php
			} else {
				?>
				<div class="hw-form-field"><?php hiweb()->dump( $field ) ?></div><?php
			}
		}

?>