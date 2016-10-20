<?php


	class hw_wp_user{

		/** @var int */
		private $id;
		/** @var string */
		private $login;
		/** @var string */
		private $email;
		/** @var  WP_User */
		private $wp_user;


		public function __construct( $idOrLoginOrMail ){
			$fields = array( 'id', 'login', 'email' );
			require_once ABSPATH . '/wp-includes/pluggable.php';
			foreach( $fields as $field ){
				if( $idOrLoginOrMail instanceof WP_User )
					$user = $idOrLoginOrMail;else $user = get_user_by( $field, $idOrLoginOrMail );
				if( !$user instanceof WP_User )
					continue;
				$this->{$field} = $idOrLoginOrMail;
				$this->wp_user = $user;
				break;
			}
			///
			if( $this->is_exist() ){
				$this->id = $this->wp_user->ID;
				$this->login = $this->wp_user->user_login;
				$this->email = $this->wp_user->user_email;
			}
		}


		/**
		 * @return false|WP_User
		 */
		public function wp_user(){
			return $this->wp_user;
		}


		/**
		 * @return array
		 */
		public function data(){
			return $this->is_exist() ? (array)$this->wp_user->data : array();
		}


		/**
		 * @return array
		 */
		public function allcaps(){
			return $this->is_exist() ? (array)$this->wp_user->allcaps : array();
		}


		/**
		 * @return array
		 */
		public function caps(){
			return $this->is_exist() ? (array)$this->wp_user->caps : array();
		}


		/**
		 * Возвращает TRUE, если для данного пользователя заданная роль актуальна
		 * @param string $role
		 * @return bool
		 */
		public function is_role( $role = 'administrator' ){
			if( $this->is_exist() ){
				foreach( $this->caps() as $cap => $bool ){
					if( strtolower( $role ) == $cap )
						return true;
				}
			}
			return false;
		}


		/**
		 * @return int
		 */
		public function id(){
			return $this->id;
		}


		/**
		 * @return int
		 */
		public function login(){
			return $this->login;
		}


		/**
		 * @return int
		 */
		public function email(){
			return $this->email;
		}


		/**
		 * @return bool
		 */
		public function is_exist(){
			return ( $this->wp_user instanceof WP_User );
		}


		/**
		 * Возвращает мета данные в массиве, либо значение указанного ключа
		 * @param null $metaKey
		 * @return array|mixed|null
		 */
		public function meta( $metaKey = null ){
			if( !$this->is_exist() )
				return null;
			$meta = get_user_meta( $this->id() );
			if( !is_string( $metaKey ) ){
				$R = array();
				if( is_array( $meta ) )
					foreach( $meta as $key => $cval ){
						$R[ $key ] = get_user_meta( $this->id, $key, true );
					}
				return $R;
			}else{
				if( array_key_exists( $metaKey, $meta ) )
					return get_user_meta( $this->id, $metaKey, true );
			}
			return null;
		}


		/**
		 * Обновит/удалить мета
		 * @param $metaKey
		 * @param null $metaValue
		 * @return bool|int
		 */
		public function meta_update( $metaKey, $metaValue = null ){
			if( !$this->is_exist() )
				return false;
			if( is_null( $metaValue ) )
				return delete_user_meta( $this->id, $metaKey );
			return update_user_meta( $this->id, $metaKey, $metaValue );
		}

	}


	class hw_wp_user_meta_boxes extends hw_wp_cpt_meta_boxes{


		protected function _hooks(){
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
							$field->value( get_user_meta( $user->ID, $field->name(), true ) );//todo
						?>
						<tr id="<?php echo $field->id() ?>" class="user-<?php echo $field->id() ?>-wrap">
							<th><label for="<?php echo $field->id() ?>"><?php echo $field->label() ?></label></th>
							<td>
								<?php $field->get_echo() ?>
							</td>
						</tr>
						<?php
					}
				?>
				</tbody>
			</table>
			<?php
		}


		protected function add_action_options_update( $user_id ){
			if( !is_null( $this->callback_save_post ) )
				return call_user_func( $this->callback_save_post, $user_id );else{
				if( is_array( $this->fields ) )
					foreach( $this->fields as $id => $field ){
						hiweb()->wp()->user( $user_id )->meta_update( $field->name(), $_POST[ $field->name() ] );
					}
			}
		}

	}