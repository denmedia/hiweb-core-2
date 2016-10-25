<?php


	class hw_taxonomies{

		/** @var hw_taxonomy[] */
		private $taxonomies = array();


		public function is_exist( $taxonomy_name ){
			return taxonomy_exists( $taxonomy_name );
		}


		/**
		 * @param null $taxonomy_name
		 * @return hw_taxonomy
		 */
		public function taxonomy( $taxonomy_name = null ){
			if( !array_key_exists( $taxonomy_name, $this->taxonomies ) ){
				$this->taxonomies[ $taxonomy_name ] = new hw_taxonomy( $taxonomy_name );
			}

			return $this->taxonomies[ $taxonomy_name ];
		}


		/**
		 * @param $taxonomy_name_source
		 * @param $taxonomy_name_dest
		 * @return bool|hw_taxonomy
		 */
		public function copy( $taxonomy_name_source, $taxonomy_name_dest ){
			if( !$this->is_exist( $taxonomy_name_source ) ){
				return false;
			}
			$this->taxonomies[ $taxonomy_name_dest ] = clone $this->taxonomy( $taxonomy_name_source );
			$this->taxonomies[ $taxonomy_name_dest ]->name( $taxonomy_name_dest );
			return $this->taxonomies[ $taxonomy_name_dest ];
		}

	}


	class hw_taxonomy{

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
			'show_admin_column' => false, 'meta_box_cb' => null, 'capabilities' => array(), 'rewrite' => true, 'update_count_callback' => '', '_builtin' => false, 'object_type' => array()
		);
		private $object_type = array();
		/** @var array */
		private $terms = array();


		public function __construct( $name ){
			$this->name = $name;
			$this->labels = $name;
			$this->set_properties();
			add_action( 'init', array( $this, 'register_taxonomy' ), 10 );
		}


		private function set_properties(){
			if( taxonomy_exists( $this->name ) ){
				$properties = (array)get_taxonomy( $this->name );
				foreach( $properties as $key => $val ){
					if( property_exists( $this, $key ) ){
						$this->{$key} = $val;
					}
				}
			}
		}


		public function __call( $name, $arguments ){
			switch( $name ){
				case 'register_taxonomy':
					$this->register_taxonomy();
					break;
			}
		}


		public function __clone(){
			add_action( 'init', array( $this, 'register_taxonomy' ), 99 );
		}


		private function register_taxonomy(){
			if( !taxonomy_exists( $this->name ) ){
				register_taxonomy( $this->name, $this->object_type(), $this->props() );
			}else{
				global $wp_taxonomies;
				foreach( $this->props() as $key => $val ){
					if( property_exists( $wp_taxonomies[ $this->name ], $key ) ){
						$wp_taxonomies[ $this->name ]->{$key} = $val;
					}
				}
			}
		}


		/**
		 * Получить/Утсановить POST TYPE
		 * @param null|array|string $object_type - post type
		 * @param bool $append - добавлять к текущему значению
		 * @return $this|array
		 */
		public function object_type( $object_type = null, $append = false ){
			if( !is_null( $object_type ) ){
				if( !is_array( $this->object_type ) )
					$this->object_type = array( $this->object_type );
				if( !is_array( $object_type ) )
					$object_type = array( $object_type );
				$this->object_type = $append ? array_merge( $this->object_type, $object_type ) : $object_type;
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
		 * @param null $name
		 * @return string
		 */
		public function name( $name = null ){
			if( is_string( $name ) ){
				$this->name = $name;
				$this->set_properties();
				return $this;
			}
			return $this->name;
		}


		/**
		 * @param null $labels
		 * @return $this|string
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
		 * @return $this|string
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
		 * @return $this|string
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
		 * @return $this|string
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
		 * @return $this|string
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
		 * @return $this|string
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
		 * @return $this|string
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
		 * @return $this|string
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
		 * @return $this|string
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
		 * @return $this|string
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
		 * @return $this|string
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
		 * @return $this|string
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
		 * @return $this|string
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
		 * @return $this|string
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
		 * @return $this|string
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
		 * @return $this|string
		 */
		public function show_tagcloud( $show_tagcloud = null ){
			if( !is_null( $show_tagcloud ) ){
				$this->show_tagcloud = $show_tagcloud;
				return $this;
			}
			return $this->show_tagcloud;
		}


		/**
		 * Возвращает все термины таксономии
		 * @param string $returnKeyGoup
		 * @param string $orderby
		 * @param bool $hide_empty
		 * @param int $number
		 * @param int $offset
		 * @param string $field
		 * @return int|WP_Error|WP_Term[]
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
		 * Копирование объекта
		 * @param $new_name
		 * @return bool|hw_taxonomy
		 */
		public function copy( $new_name ){
			return hiweb()->taxonomies()->copy( $this->name, $new_name );
		}


	}