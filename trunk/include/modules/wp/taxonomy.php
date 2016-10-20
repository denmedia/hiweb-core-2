<?php


	class hw_wp_add_taxonomy{

		private $name;
		private $labels;
		private $description;
		private $public;
		private $publicly_queryable;
		private $hierarchical;
		private $show_ui;
		private $show_in_menu;
		private $show_in_nav_menus;
		private $show_tagcloud;
		private $show_in_quick_edit;
		private $show_admin_column;
		private $meta_box_cb;
		private $capabilities;
		private $rewrite;
		private $update_count_callback;
		private $_builtin;
		private $defaults = array(
			'labels' => array(), 'description' => '', 'public' => true, 'publicly_queryable' => null, 'hierarchical' => false, 'show_ui' => null, 'show_in_menu' => null, 'show_in_nav_menus' => null, 'show_tagcloud' => null, 'show_in_quick_edit' => null,
			'show_admin_column' => false, 'meta_box_cb' => null, 'capabilities' => array(), 'rewrite' => true, 'update_count_callback' => '', '_builtin' => false,
		);
		private $object_type = array();


		public function __construct( $name ){
			$this->name = $name;
			$this->labels = $name;
			add_action( 'init', array( $this, 'register_taxonomy' ), 0 );
		}


		public function __call( $name, $arguments ){
			switch( $name ){
				case 'register_taxonomy':
					$this->register_taxonomy();
					break;
			}
		}


		private function register_taxonomy(){
			register_taxonomy( $this->name, $this->object_type(), $this->props() );
		}


		/**
		 * Получить/Утсановить POST TYPE
		 * @param null|array|string $object_type - post type
		 * @param bool $append - добавлять к текущему значению
		 * @return hw_wp_add_taxonomy|string
		 */
		public function object_type( $object_type = null, $append = false ){
			if( !is_null( $object_type ) ){
				if( !is_array( $this->object_type ) )
					$this->object_type = array( $this->object_type );
				if( !is_array( $object_type ) )
					$object_type = array( $object_type );
				$this->object_type = $append ? $this->object_type + $object_type : $object_type;
				return $this;
			}else return $this->object_type;
		}


		/**
		 * @return array
		 */
		public function props(){
			$R = array();
			if( is_array( $this->defaults ) )
				foreach( $this->defaults as $key => $def_value ){
					if( property_exists( $this, $key ) && !is_null( $this->{$key} ) )
						$R[ $key ] = $this->{$key};else $R[ $key ] = $def_value;
				}
			return $R;
		}


		/**
		 * @return string
		 */
		public function name(){
			return $this->name;
		}


		/**
		 * @param null $labels
		 * @return hw_wp_add_taxonomy|string
		 */
		public function labels( $labels = null ){
			if( !is_null( $labels ) ){
				if( !is_array( $labels ) )
					$labels = array( 'name' => $labels );
				$this->labels = $labels;
				return $this;
			}
			return $this->labels;
		}


		/**
		 * @param null $description
		 * @return hw_wp_add_taxonomy|string
		 */
		public function description( $description = null ){
			if( !is_null( $description ) ){
				if( !is_array( $description ) )
					$description = array( 'name' => $description );
				$this->description = $description;
				return $this;
			}
			return $this->description;
		}


		/**
		 * @param null $publicly_queryable
		 * @return hw_wp_add_taxonomy|string
		 */
		public function publicly_queryable( $publicly_queryable = null ){
			if( !is_null( $publicly_queryable ) ){
				if( !is_array( $publicly_queryable ) )
					$publicly_queryable = array( 'name' => $publicly_queryable );
				$this->publicly_queryable = $publicly_queryable;
				return $this;
			}
			return $this->publicly_queryable;
		}


		/**
		 * @param null $show_in_menu
		 * @return hw_wp_add_taxonomy|string
		 */
		public function show_in_menu( $show_in_menu = null ){
			if( !is_null( $show_in_menu ) ){
				if( !is_array( $show_in_menu ) )
					$show_in_menu = array( 'name' => $show_in_menu );
				$this->show_in_menu = $show_in_menu;
				return $this;
			}
			return $this->show_in_menu;
		}


		/**
		 * @param null $show_in_quick_edit
		 * @return hw_wp_add_taxonomy|string
		 */
		public function show_in_quick_edit( $show_in_quick_edit = null ){
			if( !is_null( $show_in_quick_edit ) ){
				if( !is_array( $show_in_quick_edit ) )
					$show_in_quick_edit = array( 'name' => $show_in_quick_edit );
				$this->show_in_quick_edit = $show_in_quick_edit;
				return $this;
			}
			return $this->show_in_quick_edit;
		}


		/**
		 * @param null $meta_box_cb
		 * @return hw_wp_add_taxonomy|string
		 */
		public function meta_box_cb( $meta_box_cb = null ){
			if( !is_null( $meta_box_cb ) ){
				if( !is_array( $meta_box_cb ) )
					$meta_box_cb = array( 'name' => $meta_box_cb );
				$this->meta_box_cb = $meta_box_cb;
				return $this;
			}
			return $this->meta_box_cb;
		}


		/**
		 * @param null $capabilities
		 * @return hw_wp_add_taxonomy|string
		 */
		public function capabilities( $capabilities = null ){
			if( !is_null( $capabilities ) ){
				if( !is_array( $capabilities ) )
					$capabilities = array( 'name' => $capabilities );
				$this->capabilities = $capabilities;
				return $this;
			}
			return $this->capabilities;
		}


		/**
		 * @param null $rewrite
		 * @return hw_wp_add_taxonomy|string
		 */
		public function rewrite( $rewrite = null ){
			if( !is_null( $rewrite ) ){
				if( !is_array( $rewrite ) )
					$rewrite = array( 'name' => $rewrite );
				$this->rewrite = $rewrite;
				return $this;
			}
			return $this->rewrite;
		}


		/**
		 * @param null $update_count_callback
		 * @return hw_wp_add_taxonomy|string
		 */
		public function update_count_callback( $update_count_callback = null ){
			if( !is_null( $update_count_callback ) ){
				if( !is_array( $update_count_callback ) )
					$update_count_callback = array( 'name' => $update_count_callback );
				$this->update_count_callback = $update_count_callback;
				return $this;
			}
			return $this->update_count_callback;
		}


		/**
		 * @param null $_builtin
		 * @return hw_wp_add_taxonomy|string
		 */
		public function _builtin( $_builtin = null ){
			if( !is_null( $_builtin ) ){
				if( !is_array( $_builtin ) )
					$_builtin = array( 'name' => $_builtin );
				$this->_builtin = $_builtin;
				return $this;
			}
			return $this->_builtin;
		}


		/**
		 * @param null $hierarchical
		 * @return hw_wp_add_taxonomy|string
		 */
		public function hierarchical( $hierarchical = null ){
			if( !is_null( $hierarchical ) ){
				$this->hierarchical = $hierarchical;
				return $this;
			}
			return $this->hierarchical;
		}


		/**
		 * @param null $public
		 * @return hw_wp_add_taxonomy|string
		 */
		public function publicly( $public = null ){
			if( !is_null( $public ) ){
				$this->public = $public;
				return $this;
			}
			return $this->public;
		}


		/**
		 * @param null $show_ui
		 * @return hw_wp_add_taxonomy|string
		 */
		public function show_ui( $show_ui = null ){
			if( !is_null( $show_ui ) ){
				$this->show_ui = $show_ui;
				return $this;
			}
			return $this->show_ui;
		}


		/**
		 * @param null $show_admin_column
		 * @return hw_wp_add_taxonomy|string
		 */
		public function show_admin_column( $show_admin_column = null ){
			if( !is_null( $show_admin_column ) ){
				$this->show_admin_column = $show_admin_column;
				return $this;
			}
			return $this->show_admin_column;
		}


		/**
		 * @param null $show_in_nav_menus
		 * @return hw_wp_add_taxonomy|string
		 */
		public function show_in_nav_menus( $show_in_nav_menus = null ){
			if( !is_null( $show_in_nav_menus ) ){
				$this->show_in_nav_menus = $show_in_nav_menus;
				return $this;
			}
			return $this->show_in_nav_menus;
		}


		/**
		 * @param null $show_tagcloud
		 * @return hw_wp_add_taxonomy|string
		 */
		public function show_tagcloud( $show_tagcloud = null ){
			if( !is_null( $show_tagcloud ) ){
				$this->show_tagcloud = $show_tagcloud;
				return $this;
			}
			return $this->show_tagcloud;
		}


	}


	/**
	 * Класс для работы с таксономией и постами
	 * Class hiweb_wp_taxonomy
	 */
	class hw_wp_taxonomy{

		/** @var string */
		private $name;

		/** @var array */
		private $terms = array();

		private $object;


		public function __construct( $taxonomy ){
			$this->name = $taxonomy;
			$this->object = get_taxonomy( $taxonomy );
		}


		public function object(){
			return $this->object;
		}


		/**
		 * Возвращает значение ключа таксономии, либо NULL, если ключа нет
		 * @param null $key - название ключа
		 * @param null $secondKey - поиск значения ключа во вложенном массиве
		 * @return null
		 */
		public function get( $key, $secondKey = null ){
			if( array_key_exists( $key, (array)$this->object ) ){
				if( is_null( $secondKey ) ){
					return $this->object->{$key};
				}else{
					$val = (array)$this->object->{$key};

					return $val[ $secondKey ];
				}
			}else{
				return null;
			}
		}


		/**
		 * Возвращает TRUE, если таксономия существует
		 * @return bool
		 */
		public function exist(){
			return taxonomy_exists( $this->name );
		}


		/**
		 * Возвращает все термины таксономии
		 * @return WP_Term[]|int|WP_Error
		 */
		public function terms( $returnKeyGoup = '', $orderby = 'name', $hide_empty = false, $number = 0, $offset = 0, $field = 'all' ){
			$taxonomy = $this->name;
			$args = get_defined_vars();
			$argsString = md5( json_encode( $args ) );
			if( !array_key_exists( $argsString, $this->terms ) ){
				$this->terms[ $argsString ] = array();
				$terms = get_terms( $args );
				/** @var WP_Term[] $terms */
				if( is_array( $terms ) ){
					if( is_string( $returnKeyGoup ) && trim( $returnKeyGoup != '' ) ){
						foreach( $terms as $term ){
							if( property_exists( $term, $returnKeyGoup ) ){
								$this->terms[ $argsString ][ $term->{$returnKeyGoup} ][] = $term;
							}
						}
					}else{
						foreach( $terms as $term ){
							$this->terms[ $argsString ][ $term->term_id ] = $term;
						}
					}
				}else{
					$this->terms[ $argsString ] = array();
				}
			}

			return $this->terms[ $argsString ];
		}


		/**
		 * Возвращает SLUG таксономии
		 * @return string
		 */
		public function name(){
			return $this->name;
		}

	}