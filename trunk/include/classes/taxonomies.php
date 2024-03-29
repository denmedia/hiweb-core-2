<?php


	class hw_taxonomies{

		/** @var hw_taxonomy[] */
		private $taxonomies = [];


		public function __construct(){
			//add_action( 'init', array( $this, 'add_action_init' ) );
		}


		public function is_exist( $taxonomy_name ){
			return taxonomy_exists( $taxonomy_name );
		}


		/**
		 * @param null $taxonomy_name
		 * @return hw_taxonomy
		 */
		public function give( $taxonomy_name = null ){
			if( !array_key_exists( $taxonomy_name, $this->taxonomies ) ){
				$this->taxonomies[ $taxonomy_name ] = new hw_taxonomy( $taxonomy_name );
			}

			return $this->taxonomies[ $taxonomy_name ];
		}


		/**
		 * @return hw_taxonomy[]
		 */
		public function get_all(){
			return $this->taxonomies;
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
			$this->taxonomies[ $taxonomy_name_dest ] = clone $this->give( $taxonomy_name_source );
			$this->taxonomies[ $taxonomy_name_dest ]->name( $taxonomy_name_dest );
			return $this->taxonomies[ $taxonomy_name_dest ];
		}


		/**
		 * @param int|object|WP_Term $termOrId
		 * @return hw_taxonomy
		 */
		public function get_taxonomy_by_term( $termOrId ){
			$term = get_term( $termOrId );
			if( $term instanceof WP_Term ){
				$R = $this->give( $term->taxonomy );
			} else {
				$R = $this->give( '' );
			}
			return $R;
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
		private $defaults = [ 'labels' => [], 'description' => '', 'public' => true, 'publicly_queryable' => null, 'hierarchical' => false, 'show_ui' => null, 'show_in_menu' => null, 'show_in_nav_menus' => null, 'show_tagcloud' => null, 'show_in_quick_edit' => null, 'show_admin_column' => false, 'meta_box_cb' => null, 'capabilities' => [], 'rewrite' => true, 'update_count_callback' => '', '_builtin' => false, 'object_type' => [] ];
		/** @var array */
		private $object_type = [];
		/** @var array */
		private $terms = [];

		use hw_hidden_methods_props;


		/** @var hw_input[] */

		public function __construct( $name ){
			$this->name = strtolower( $name );
			if( strlen( $this->name ) < 1 ){
				return;
			}
			if( strlen( $this->name ) > 20 ){
				hiweb()->console()->warn( 'Name of taxonomy must be between 1 and 20 symbols, this name is [' . $this->name . ']', true );
			}
			$this->labels = $name;
			///Create
			add_action( 'init', [ $this, 'register_taxonomy' ] );
		}


		/*public function __clone(){
			add_action( 'init', array( $this, 'register_taxonomy' ), 99 );
		}*/

		private function register_taxonomy(){
			if( !taxonomy_exists( $this->name ) ){
				register_taxonomy( $this->name, $this->object_type(), $this->props() );
			} else {
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
		 * @param bool              $append      - добавлять к текущему значению
		 * @return hw_taxonomy|array
		 */
		public function object_type( $object_type = null, $append = false ){
			if( !is_null( $object_type ) ){
				if( !is_array( $this->object_type ) ) $this->object_type = [ $this->object_type ];
				if( !is_array( $object_type ) ) $object_type = [ $object_type ];
				$this->object_type = $append ? array_merge( $this->object_type, $object_type ) : $object_type;
				return $this;
			} else return $this->object_type;
		}


		/**
		 * @return array
		 */
		public function props(){
			$R = [];
			if( is_array( $this->defaults ) ) foreach( $this->defaults as $key => $def_value ){
				if( property_exists( $this, $key ) && !is_null( $this->{$key} ) ) $R[ $key ] = $this->{$key}; else $R[ $key ] = $def_value;
			}
			return $R;
		}


		/**
		 * @param null $name
		 * @return hw_taxonomy|string
		 */
		public function name( $name = null ){
			if( !is_null( $name ) ){
				$this->{__FUNCTION__} = $name;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param string|array $labels
		 * @return hw_taxonomy|string
		 */
		public function labels( $labels = null ){
			if( !is_null( $labels ) ){
				if( !is_array( $labels ) ) $labels = [ 'name' => $labels ];
				$this->labels = $labels;
				return $this;
			}
			return $this->labels;
		}


		/**
		 * @param string $set
		 * @return hw_taxonomy|string
		 */
		public function description( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param string $set
		 * @return hw_taxonomy|string
		 */
		public function publicly_queryable( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param string $set
		 * @return hw_taxonomy|string
		 */
		public function show_in_menu( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param string $set
		 * @return hw_taxonomy|string
		 */
		public function show_in_quick_edit( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param string $set
		 * @return hw_taxonomy|string
		 */
		public function meta_box_cb( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param string $set
		 * @return hw_taxonomy|string
		 */
		public function capabilities( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param array|bool $set
		 * @return hw_taxonomy|string
		 */
		public function rewrite( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param bool $set
		 * @return hw_taxonomy|string
		 */
		public function update_count_callback( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param bool $set
		 * @return hw_taxonomy|string
		 */
		public function _builtin( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param bool $set
		 * @return hw_taxonomy|string
		 */
		public function hierarchical( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param string $set
		 * @return hw_taxonomy|string
		 */
		public function _public( $set = null ){
			if( !is_null( $set ) ){
				$this->public = $set;
				return $this;
			}
			return $this->public;
		}


		/**
		 * @param bool $set
		 * @return hw_taxonomy|string
		 */
		public function show_ui( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param bool $set
		 * @return hw_taxonomy|string
		 */
		public function show_admin_column( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param bool $set
		 * @return hw_taxonomy|string
		 */
		public function show_in_nav_menus( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param bool $set
		 * @return hw_taxonomy|string
		 */
		public function show_tagcloud( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * Возвращает все термины таксономии
		 * @param string $returnKeyGoup
		 * @param string $orderby
		 * @param bool   $hide_empty
		 * @param int    $number
		 * @param int    $offset
		 * @param string $field
		 * @return int|WP_Error|WP_Term[]
		 */
		public function terms( $returnKeyGoup = '', $orderby = 'name', $hide_empty = false, $number = 0, $offset = 0, $field = 'all' ){
			$taxonomy = $this->name;
			$args = get_defined_vars();
			$argsString = md5( json_encode( $args ) );
			if( !array_key_exists( $argsString, $this->terms ) ){
				$this->terms[ $argsString ] = [];
				$terms = get_terms( $args );
				/** @var WP_Term[] $terms */
				if( is_array( $terms ) ){
					if( is_string( $returnKeyGoup ) && trim( $returnKeyGoup != '' ) ){
						foreach( $terms as $term ){
							if( property_exists( $term, $returnKeyGoup ) ){
								$this->terms[ $argsString ][ $term->{$returnKeyGoup} ][] = $term;
							}
						}
					} else {
						foreach( $terms as $term ){
							$this->terms[ $argsString ][ $term->term_id ] = $term;
						}
					}
				} else {
					$this->terms[ $argsString ] = [];
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