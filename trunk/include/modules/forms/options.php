<table class="form-table">
	<tbody>
	<?php
		
		/** @var hw_form $this */
		
		settings_fields( $this->settings_group() );
		do_settings_sections( $this->settings_group() );
		
		if( !$this->have_fields() ){
			hiweb()->console()->warn( 'Для формы опций id[' . $this->id . '] нет полей ввода.', true );
		}else
			foreach( $this->get_fields() as $field ){
				?>
				<tr class="hw-form-field"><?php
				if( $field instanceof hw_input ){
					?>
					<th scope="row"><label for="<?php echo $id ?>"><?php echo $field->title() ?></label></th>
					<td><?php $field->the(); ?>
					<?php echo $field->description() != '' ? '<p class="description">' . $field->description() . '</p>' : '';
					?></td><?php
				}else{
					?>
					<td colspan="2"><?php hiweb()->dump( $field ) ?></td><?php
				}
				?></tr><?php
			}
	
	?>
	</tbody>
</table>