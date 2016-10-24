<?php


	class hw_post_types{
		
		/** @var hw_post_type[] */
		private $post_types = array();


		/**
		 * Возвращает корневой CPT класс для работы с кастомным типом поста
		 * @param $post_type
		 * @return hw_post_type
		 */
		public function post_type( $post_type ){
			if( !array_key_exists( $post_type, $this->post_types ) ){
				$this->post_types[ $post_type ] = new hw_post_type( $post_type );
			}
			return $this->post_types[ $post_type ];
		}
	}


	class hw_post_type{

		private $_type;
		/** @var WP_Error|WP_Post_Type */
		private $_object;
		private $_defaults = array(
			'label' => null, 'labels' => array(), 'description' => '', 'public' => false, 'hierarchical' => false, 'exclude_from_search' => null, 'publicly_queryable' => null, 'show_ui' => true, 'show_in_menu' => null, 'show_in_nav_menus' => null,
			'show_in_admin_bar' => null, 'menu_position' => null, 'menu_icon' => 'dashicons-sticky', 'capability_type' => 'post', 'capabilities' => array(), 'map_meta_cap' => null, 'supports' => array(), 'register_meta_box_cb' => null,
			'taxonomies' => array(), 'has_archive' => false, 'rewrite' => true, 'query_var' => true, 'can_export' => true, 'delete_with_user' => null, '_builtin' => false, '_edit_link' => 'post.php?post=%d',
		);
		///////PROPS
		private $label;
		private $labels;
		private $description;
		private $public;
		private $hierarchical;
		private $exclude_from_search;
		private $publicly_queryable;
		private $show_ui;
		private $show_in_menu;
		private $show_in_nav_menus;
		private $show_in_admin_bar;
		private $menu_position;
		private $menu_icon;
		private $capability_type;
		private $capabilities;
		private $map_meta_cap;
		private $supports;
		private $register_meta_box_cb;
		private $taxonomies;
		private $has_archive;
		private $rewrite;
		private $query_var;
		private $can_export;
		private $delete_with_user;
		private $_builtin;
		private $_edit_link;


		/**
		 * @param null $set
		 * @return $this
		 */
		public function label( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function labels( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param string $key
		 * @param string $value
		 * @return $this
		 */
		public function labels_set( $key = 'name', $value = '' ){
			$this->labels->{$key} = $value;
			return $this;
		}

		///////
		/** @var  hw_taxonomy[] */
		private $_taxonomies = array();
		/** @var hw_meta_boxes[] */
		private $_meta_boxes = array();


		public function __construct( $post_type ){
			$this->_type = $post_type;
			$this->set_props();
			add_action( 'init', array( $this, 'add_action_init_create' ), 99999 );
		}


		private function set_props(){
			if( post_type_exists( $this->_type ) ){
				$props = (array)get_post_type_object( $this->_type );
				foreach( $props as $key => $val ){
					if( property_exists( $this, $key ) ){
						$this->{$key} = $val;
					}
				}
			}
		}


		public function __call( $name, $arguments ){
			switch( $name ){
				case 'add_action_init_create':
					$this->add_action_init_create();
					break;
			}
		}


		/**
		 * @return string
		 */
		public function type(){
			return $this->_type;
		}


		/**
		 * Возвращает массив установок
		 * @return array
		 */
		public function props(){
			$R = array();
			foreach( $this->_defaults as $key => $def_value ){
				$R[ $key ] = ( !property_exists( $this, $key ) || is_null( $this->{$key} ) ) ? $def_value : $this->{$key};
			}
			return $R;
		}


		/**
		 * @return WP_Error|WP_Post_Type
		 */
		public function get(){
			return $this->_object;
		}


		/**
		 * Процедура регистрации типа поста
		 * @return WP_Error|WP_Post_Type
		 */
		private function add_action_init_create(){
			if( post_type_exists( $this->_type ) ){
				global $wp_post_types;
				foreach( $wp_post_types[ $this->_type ] as $key => $val ){
					if( property_exists( $this, $key ) ){
						if( $key == 'label' ){
							$wp_post_types[ $this->_type ]->{$key} = __($this->{$key});
						}elseif( $key == 'labels' ){
							if( is_object( $val ) )
								foreach( $val as $label => $name ){
									$wp_post_types[ $this->_type ]->{$key}->{$label} = __( $this->labels->{$label});
								}
						}else{
							$wp_post_types[ $this->_type ]->{$key} = $this->{$key};
						}
					}
				}
				hiweb()->console( $wp_post_types[$this->_type] );
				//If PT exist
				/*foreach( (array)get_post_type_object( $this->_type ) as $key => $value ){
					if( property_exists( $this, $key ) )
						$this->{$key} = $value;
				}*/
			}else{
				//Register PT
				$this->_object = register_post_type( $this->_type, $this->props() );
			}
			return $this->get();
		}


		/**
		 * @param string|int $id
		 * @param hw_post_type_meta_boxes $hiweb_meta_boxes
		 * @return hw_post_type_meta_boxes
		 */
		public function add_meta_box( $id, $hiweb_meta_boxes = null ){
			if( !isset( $this->_meta_boxes[ $id ] ) ){
				if( $hiweb_meta_boxes[ $id ] instanceof hw_post_type_meta_boxes )
					$this->_meta_boxes = $hiweb_meta_boxes;else $this->_meta_boxes[ $id ] = new hw_post_type_meta_boxes( $id );
				$this->_meta_boxes[ $id ]->screen( $this->_type );
			}
			return $this->_meta_boxes[ $id ];
		}


		/**
		 * @param $name
		 * @return hw_wp_add_taxonomy
		 */
		public function add_taxonomy( $name ){
			if( !isset( $this->_taxonomies[ $name ] ) ){
				$this->_taxonomies[ $name ] = hiweb()->wp()->add_taxonomy( $name );
				$this->_taxonomies[ $name ]->object_type( $this->_type );
			}
			return $this->_taxonomies[ $name ];
		}


		/**
		 * @return hw_post_type_meta_boxes[]
		 */
		public function meta_boxes(){
			return $this->_meta_boxes;
		}


		/**
		 * Возвращает все таксономии данного типа
		 * @param string $output - [names|objects]
		 * @return array
		 */
		public function taxonomies( $output = 'names' ){
			if( is_null( $this->_taxonomies[ $output ] ) ){
				$this->_taxonomies[ $output ] = get_object_taxonomies( $this->_type, $output );
			}
			return $this->_taxonomies[ $output ];
		}


	}


	class hw_post_type_meta_boxes{

		/** @var string */
		protected $_id;
		protected $title = '&nbsp;';
		protected $callback;
		protected $screen = array();
		protected $context = 'normal'; //normal, advanced или side
		protected $priority = 'default';
		protected $callback_args;
		protected $callback_save_post;
		/** @var hw_input[] */
		protected $fields;
		/** @var string */
		protected $fields_prefix = 'hw_wp_meta_boxes_';


		public function __construct( $id ){
			$this->_id = $id;
			$this->_hooks();
		}


		protected function _hooks(){
			add_action( 'add_meta_boxes', array( $this, 'add_action_add_meta_box' ), 10, 2 );
			add_action( 'save_post', array( $this, 'add_action_save_post' ), 10, 2 );
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
		 * @param null $screen
		 * @param bool $append
		 * @return $this
		 */
		public function screen( $screen = null, $append = true ){
			$this->screen = is_array( $this->screen ) ? $this->screen : array( $this->screen );
			if( is_null( $screen ) ){
				return $this->screen;
			}else{
				if( !is_array( $screen ) )
					$screen = array( $screen );
				$this->screen = $append ? $this->screen + $screen : $screen;
			}
			return $this;
		}


		/**
		 * @param null $context
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


		public function __call( $name, $arguments ){
			switch( $name ){
				case 'add_action_add_meta_box':
					$this->add_action_add_meta_box( $arguments[0], isset( $arguments[1] ) ? $arguments[1] : null );
					break;
				case 'add_action_save_post':
					$this->add_action_save_post( $arguments[0] );
					break;
				case 'generate_meta_box':
					$this->generate_meta_box( $arguments[0], $arguments[1] );
					break;
			}
		}


		public function add_field( $id ){
			$this->fields[ $id ] = hiweb()->input()->make( $id );
			return $this->fields[ $id ];
		}


		/**
		 * @return hw_input[]
		 */
		public function fields(){
			return $this->fields;
		}


		protected function add_action_add_meta_box( $post_type, $post = null ){
			add_meta_box( $this->_id, $this->title, is_null( $this->callback ) ? array( $this, 'generate_meta_box' ) : $this->callback, $this->screen, $this->context, $this->priority, $this->callback_args );
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


		protected function generate_meta_box( $post, $meta_box ){
			if( is_array( $this->fields ) )
				foreach( $this->fields as $id => $field ){
					if( $post instanceof WP_Post )
						$field->value( get_post_meta( $post->ID, $field->name(), true ) );
					?>
					<p>
						<strong><?php echo $field->label(); ?></strong>
						<label class="screen-reader-text" for="<?php echo $id ?>"><?php echo $field->label() ?></label>
					</p>
					<?php $field->the();
				}else ?><span>no fields</span><?php
		}
	}