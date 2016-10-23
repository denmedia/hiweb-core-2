<?php


	class hw_meta_boxes{

		private $meta_boxes = array();


		/**
		 * Возвращает метабокс, по необходимости создавая его
		 * @param $id
		 * @return hw_meta_box|mixed
		 */
		public function get( $id ){
			if( array_key_exists( $id, $this->meta_boxes ) ){
				return $this->meta_boxes[ $id ];
			}else{
				if( hiweb()->string()->is_empty( $id ) )
					$id = hiweb()->string()->rand();
				return new hw_meta_box( $id );
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
			add_action( 'current_screen', array( $this, 'init_screen' ), 9999999 );
			add_action( 'created_term', array( $this, 'save_taxonomy_custom_meta' ), 10, 2 ); //todo!!!
		}


		public function __call( $name, $arguments ){
			switch( $name ){
				case 'init_screen':
					$this->init_screen();
					break;
				case 'add_action_add_meta_box':
					if( $this->screen_logic->detect()->detect() )
						add_meta_box( $this->_id, $this->title, is_null( $this->callback ) ? array( $this, 'generate_post' ) : $this->callback, $this->screen, $this->context, $this->priority, $this->callback_args );
					break;
				case 'generate_post' :
					$this->generate_post( $arguments[0], $arguments[1] );
					break;
				case 'add_action_add_form_fields':
					$this->generate_taxonomy_add( $arguments[0] );
					break;
				case 'add_action_edit_form':
					$this->generate_taxonomy_edit( $arguments[0] );
					break;
				case 'save_taxonomy_custom_meta':
					$this->save_taxonomy_custom_meta( $arguments[0] );
					break;
			}
		}


		private function init_screen(){
			add_action( 'add_meta_boxes', array( $this, 'add_action_add_meta_box' ), 10, 2 );
			$taxonomies = $this->screen_logic->detect()->get_taxonomies_from_chain();
			if( is_array( $taxonomies ) )
				foreach( $taxonomies as $tax ){
					add_action( $tax . '_add_form_fields', array( $this, 'add_action_add_form_fields' ), 10, 2 );
					add_action( $tax . '_edit_form', array( $this, 'add_action_edit_form' ), 10, 2 );
					add_action( 'edited_term_taxonomy', array( $this, 'save_taxonomy_custom_meta' ), 10, 2 );
				}
		}
		

		/**
		 * Установить логику отображения
		 * @return hw_screen_logic
		 */
		public function screen(){
			if( !$this->screen_logic instanceof hw_screen_logic ){
				$this->screen_logic = hiweb()->screen_logic();
			}
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


		public function add_field( $id, $type = 'text' ){
			$this->fields[ $id ] = hiweb()->inputs()->get( $id, $type );
			return $this->fields[ $id ];
		}


		/**
		 * @return hw_input[]
		 */
		public function fields(){
			return $this->fields;
		}


		protected function add_action_save_post( $post_id = null ){
			if( !is_null( $this->callback_save_post ) )
				return call_user_func( $this->callback_save_post, $post_id );else{
				if( is_array( $this->fields ) )
					foreach( $this->fields as $id => $field ){
						update_post_meta( $post_id, $field->name(), $_POST[ $field->name() ] );
					}
			}
			return $post_id;
		}


		protected function generate_post( $post, $meta_box ){
			if( !is_array( $this->fields ) || count( $this->fields ) == 0 ){
			}else foreach( $this->fields as $id => $field ){
				?><p><strong><?php echo $field->title() ?></strong></p><p>
					<?php $field->the(); echo ' '.$field->label(); ?>
				</p><p><?php echo $field->description() ?></p><?php
			}
		}


		protected function generate_taxonomy_add( $taxonomy ){
			if( !is_array( $this->fields ) || count( $this->fields ) == 0 ){
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


		protected function generate_taxonomy_edit( $taxonomy ){
			if( !is_array( $this->fields ) || count( $this->fields ) == 0 ){
			}else foreach( $this->fields as $id => $field ){
				hiweb()->console( get_term_meta( $_GET['tag_ID'] ) ); //todo
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


		protected function save_taxonomy_custom_meta( $term_id ){
			if( !is_array( $this->fields ) || count( $this->fields ) == 0 ){
			}else foreach( $this->fields as $id => $field ){
				if( isset( $_POST[ $field->name() ] ) ){
					update_term_meta( $term_id, $field->name(), $_POST[ $field->name() ] );
				}
			}
		}

	}