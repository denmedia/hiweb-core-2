<?php

	/** @var hw_field[] $fields */
	/** @var string $hook_name */

	if( !is_array( $fields ) || count( $fields ) == 0 ){
		//Нет полей
	} else {
		?>
		<table class="form-table" data-hook="<?= $hook_name ?>">
			<?php
				foreach( $fields as $field ){
					?>
                    <tr class="form-field term-description-wrap">
                        <th scope="row"><label for="<?php echo $field->input()->id ?>"><?php echo $field->name() ?></label></th>
                        <td><?php $field->the() ?>
							<?php if($field->description() != '') {
								?><p class="description"><?php echo $field->description() ?></p><?php
							} ?></td>
                    </tr>
					<?php
				}
			?>
        </table>
		<?php
	}