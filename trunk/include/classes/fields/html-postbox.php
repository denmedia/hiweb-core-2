<?php

	/** @var hw_field[] $fields */
	/** @var string $hook_name */

	if( !is_array( $fields ) || count( $fields ) == 0 ){
		//Нет полей
	} else {
		?>
        <div class="postbox hw-fields-block" data-hook="<?= $hook_name ?>">
			<?php
				foreach( $fields as $field ){
					?>
                    <h2><?php echo $field->name() ?></h2>
                    <div class="inside hw-field">
                        <div class="hw-field-input"><?php $field->the() ?></div>
                    </div>
					<?php
				}
			?>
        </div>
		<?php
	}