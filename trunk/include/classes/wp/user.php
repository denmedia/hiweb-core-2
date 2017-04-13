<?php

	include_once hiweb()->dir_classes . '/post_types.php';


	class hw_wp_user_meta_boxes extends hw_post_type_meta_boxes{


		protected function my_hooks(){
			add_action( 'show_user_profile', array( $this, 'add_action_user_profile' ) );
			add_action( 'edit_user_profile', array( $this, 'add_action_user_profile' ) );
			add_action( 'personal_options_update', array( $this, 'add_action_options_update' ) );
			add_action( 'edit_user_profile_update', array( $this, 'add_action_options_update' ) );
		}


		public function __call( $name, $arguments ){
			switch( $name ){
				case 'add_action_user_profile':
					$this->add_action_user_profile( $arguments[0], isset( $arguments[1] ) ? $arguments[1] : null );
					break;
				case 'add_action_options_update':
					$this->add_action_options_update( $arguments[0] );
					break;
			}
		}


		protected function add_action_user_profile( $user, $b = null ){
			?>
			<table class="form-table" id="<?php echo $this->_id; ?>">
				<tbody>
				<?php
					foreach( $this->fields as $id => $field ){
						if( $user instanceof WP_User )
							$field->value( get_user_meta( $user->ID, $field->name(), true ) );
						?>
						<tr id="<?php echo $field->id() ?>" class="user-<?php echo $field->id() ?>-wrap">
							<th><label for="<?php echo $field->id() ?>"><?php echo $field->label() ?></label></th>
							<td>
								<?php $field->the() ?>
							</td>
						</tr>
						<?php
					}
				?>
				</tbody>
			</table>
			<?php
		}

	}