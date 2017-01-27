<?php
	
	/** @var hw_form $this */
	
	if( !$this->have_fields() ){
		hiweb()->console()->warn( 'Для формы id[' . $this->id . '] нет полей ввода.', true );
	}else
		foreach( $this->get_fields() as $field ){
			if( $field instanceof hw_input ){
				?>
				<div class="hw-form-field">
				<p><strong><?php echo $field->title() ?></strong></p>
				<?php $field->the(); ?>
				<?php echo $field->description() != '' ? '<p class="description">' . $field->description() . '</p>' : ''; ?>
				</div><?php
			}else{
				?>
				<div class="hw-form-field"><?php hiweb()->dump( $field ) ?></div><?php
			}
		}

?>