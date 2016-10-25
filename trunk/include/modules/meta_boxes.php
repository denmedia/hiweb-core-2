<?php

	include_once 'meta_boxes/screen_logic.php';


	class hw_meta_boxes{

		private $meta_boxes = array();


		/**
		 * Возвращает метабокс, по необходимости создавая его
		 * @param $id
		 * @return hw_meta_box|mixed
		 */
		public function get( $id = null, $title = null ){
			if( array_key_exists( $id, $this->meta_boxes ) ){
				if( !is_null( $title ) )
					$this->meta_boxes[ $id ]->title( $title );
				return $this->meta_boxes[ $id ];
			}else{
				if( hiweb()->string()->is_empty( $id ) )
					$id = hiweb()->string()->rand();
				$this->meta_boxes[ $id ] = new hw_meta_box( $id );
				if( !is_null( $title ) )
					$this->meta_boxes[ $id ]->title( $title );
				return $this->meta_boxes[ $id ];
			}
		}

	}


	class hw_meta_box{

		/** @var string */
		protected $_id;
		protected $title = '&nbsp;';
		protected $callback;
		protected $screen;
		protected $context = 'normal'; //normal, advanced или side
		protected $priority = 'default';
		protected $callback_args;
		protected $callback_save_post;
		/** @var hw_input[] */
		protected $fields;
		/** @var string */
		protected $fields_prefix = 'hw_wp_meta_boxes_';
		/** @var  hw_screen_logic */
		protected $screen_logic;


		public function __construct( $id ){
			$this->_id = $id;
			$this->screen_logic = new hw_screen_logic();
			///Show META
			add_action( 'add_meta_boxes', array( $this, 'add_action_add_meta_box' ), 10, 2 );
			$taxonomies = array_keys( get_taxonomies() );
			if( is_array( $taxonomies ) )
				foreach( $taxonomies as $tax ){
					add_action( $tax . '_add_form_fields', array( $this, 'the_taxonomy_add' ), 10, 2 );
					add_action( $tax . '_edit_form', array( $this, 'the_taxonomy_edit' ), 10, 2 );
				}
			add_action( 'show_user_profile', array( $this, 'the_user_edit' ) );
			add_action( 'edit_user_profile', array( $this, 'the_user_edit' ) );
			add_action( 'user_new_form', array( $this, 'the_user_edit' ) );
			///Update Meta
			add_action( 'save_post', array( $this, 'the_post_update' ), 10, 2 );
			add_action( 'create_term', array( $this, 'the_taxonomy_update' ), 10, 2 );
			add_action( 'edited_term_taxonomy', array( $this, 'the_taxonomy_update' ), 10, 2 );
			add_action( 'personal_options_update', array( $this, 'the_user_update' ) );
			add_action( 'edit_user_profile_update', array( $this, 'the_user_update' ) );
			add_action( 'user_register', array( $this, 'the_user_update' ) );
		}


		public function __call( $name, $arguments ){
			switch( $name ){
				case 'add_action_add_meta_box':
					if( $this->screen_logic->detect()->detect() )
						add_meta_box( $this->_id, $this->title, is_null( $this->callback ) ? array( $this, 'the_post_edit' ) : $this->callback, $this->screen, $this->context, $this->priority, $this->callback_args );
					break;
				case 'the_post_edit':
					$this->the_post_edit( $arguments[0], $arguments[1] );
					break;
				case 'the_post_update':
					$this->the_post_update( $arguments[0] );
					break;
				case 'the_taxonomy_add':
					$this->the_taxonomy_add( $arguments[0] );
					break;
				case 'the_taxonomy_edit':
					$this->the_taxonomy_edit( $arguments[0] );
					break;
				case 'the_user_edit':
					$this->the_user_edit();
					break;
				case 'the_taxonomy_update':
					$this->the_taxonomy_update( $arguments[0] );
					break;
				case 'the_user_update':
					$this->the_user_update( $arguments[0] );
					break;
			}
		}
		

		/**
		 * Установить логику отображения
		 * @return hw_screen_logic
		 */
		public function screen(){
			return $this->screen_logic;
		}


		/**
		 * Возвращает ID текущего мета-бокса
		 * @return string
		 */
		public function id(){
			return $this->_id;
		}


		/**
		 * @param $title
		 * @return $this
		 */
		public function title( $title = null ){
			if( is_null( $title ) )
				return $this->title;else $this->title = $title;
			return $this;
		}


		/**
		 * @param $callback
		 * @return $this
		 */
		public function callback( $callback = null ){
			if( is_null( $callback ) )
				return $this->callback;else $this->callback = $callback;
			return $this;
		}


		/**
		 * Мето, где должен распологаться блок
		 * * normal
		 * * advanced (default)
		 * * side
		 * @param null|string $context
		 * @return $this
		 */
		public function context( $context = null ){
			if( is_null( $context ) )
				return $this->context;else $this->context = $context;
			return $this;
		}


		/**
		 * @param null $priority
		 * @return $this
		 */
		public function priority( $priority = null ){
			if( is_null( $priority ) )
				return $this->priority;else $this->priority = $priority;
			return $this;
		}


		/**
		 * @param null $callback_args
		 * @return $this
		 */
		public function callback_args( $callback_args = null ){
			if( is_null( $callback_args ) )
				return $this->callback_args;else $this->callback_args = $callback_args;
			return $this;
		}


		/**
		 * @param null $callback
		 * @return $this
		 */
		public function callback_save_post( $callback = null ){
			if( is_null( $callback ) )
				return $this->callback_save_post;else $this->callback_save_post = $callback;
			return $this;
		}


		/**
		 * @param $idOrInput - ID нового поля, либо hw_input, либо hw_input[] (массив полей)
		 * @param string $type
		 * @return hw_input|hw_input_text|hw_input_checkbox|hw_input_repeat
		 */
		public function add_field( $idOrInput, $type = 'text' ){
			if( $idOrInput instanceof hw_input ){
				$this->fields[ $idOrInput->id() ] = $idOrInput;
				return $idOrInput;
			}elseif( is_array( $idOrInput ) ){
				foreach( $idOrInput as $field ){
					$this->fields[ $field->id() ] = $field;
				}
				return reset( $idOrInput );
			}else{
				$this->fields[ $idOrInput ] = hiweb()->input( $idOrInput, $type );
				return $this->fields[ $idOrInput ];
			}
		}


		/**
		 * @return hw_input[]
		 */
		public function fields(){
			return $this->fields;
		}


		protected function the_post_edit( $post, $meta_box ){
			if( !$this->screen()->detect()->detect() || !is_array( $this->fields ) || count( $this->fields ) == 0 ){
			}else foreach( $this->fields as $id => $field ){
				$value = hiweb()->post( $post )->meta( $field->name() );
				$field->value( $value );
				?><p><strong><?php echo $field->title() ?></strong></p><p>
					<?php $field->the();
						echo ' ' . $field->label(); ?>
				</p><p><?php echo $field->description() ?></p><?php
			}
		}


		protected function the_post_update( $post_id = null ){
			if( !is_null( $this->callback_save_post ) )
				return call_user_func( $this->callback_save_post, $post_id );else{
				if( is_array( $this->fields ) )
					foreach( $this->fields as $id => $field ){
						update_post_meta( $post_id, $field->name(), $_POST[ $field->name() ] );
					}
			}
			return $post_id;
		}


		protected function the_taxonomy_add( $taxonomy ){
			if( !$this->screen()->detect()->detect() || !is_array( $this->fields ) || count( $this->fields ) == 0 ){
			}else foreach( $this->fields as $id => $field ){
				?>
				<div class="form-field term-slug-wrap">
					<label for="<?php echo $field->id() ?>"><?php echo $field->title(); ?></label>
					<?php $field->the();
						echo ' ' . $field->label() ?>
					<p><?php echo $field->description(); ?></p>
				</div>
				<?php
			}
		}


		protected function the_taxonomy_edit( $taxonomy ){
			if( !$this->screen()->detect()->detect() || !is_array( $this->fields ) || count( $this->fields ) == 0 ){
			}else foreach( $this->fields as $id => $field ){
				$field->value( get_term_meta( $_GET['tag_ID'], $field->name(), true ) );
				?>
				<table class="form-table">
					<tbody>
					<tr class="form-field term-parent-wrap">
						<th scope="row"><label for="<?php echo $field->id() ?>"><?php echo $field->title() ?></label></th>
						<td>
							<?php $field->the() ?>
							<p class="description"><?php echo $field->description() ?></p>
						</td>
					</tr>
					</tbody>
				</table>

				<?php
			}
		}


		protected function the_taxonomy_update( $term_id ){
			if( !is_array( $this->fields ) || count( $this->fields ) == 0 ){
			}else foreach( $this->fields as $id => $field ){
				if( isset( $_POST[ $field->name() ] ) ){
					update_term_meta( $term_id, $field->name(), $_POST[ $field->name() ] );
				}
			}
		}


		protected function the_user_edit(){
			if( !$this->screen()->detect()->detect() || !is_array( $this->fields ) || count( $this->fields ) == 0 ){
			}else{
				?>
				<table class="form-table">
				<tbody><?php
					foreach( $this->fields as $id => $field ){
						if( get_current_screen()->action != 'add' ){
							$field->value( hiweb()->user( isset( $_GET['user_id'] ) ? $_GET['user_id'] : null )->meta( $field->name() ) );
						}
						?>
						<tr>
							<th><label for="<?php echo $field->name() ?>"><?php echo $field->title() ?></label></th>
							<td><?php $field->the(); ?><p class="description"><?php echo $field->description() ?></p></td>
						</tr>
						<?php
					}
				?></tbody>
				</table><?php
			}
		}


		protected function the_user_update( $new_user_id = null ){
			if( !is_array( $this->fields ) || count( $this->fields ) == 0 ){
			}else foreach( $this->fields as $id => $field ){
				if( isset( $_POST[ $field->name() ] ) ){
					$B = hiweb()->user( isset( $_POST['user_id'] ) ? $_POST['user_id'] : $new_user_id )->meta_update( $field->name(), $_POST[ $field->name() ] );
				}
			}
		}

	}