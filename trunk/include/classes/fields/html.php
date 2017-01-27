<?php

	/** @var hw_field[] $fields */
	/** @var string $hook_name */

	if( !is_array( $fields ) || count( $fields ) == 0 ){
		//Нет полей
	} else {
		?>
        <div class="hw-fields-block" data-hook="<?= $hook_name ?>">
			<?php
				foreach( $fields as $field ){
					?>
                    <div class="hw-field">
                        <h2><?= $field->name() ?></h2>
                        <div class="hw-field-input"><?php $field->the() ?></div>
                    </div>
					<?php
				}
			?>
        </div>
		<?php
	}