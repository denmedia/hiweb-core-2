<?php


	/**
	 * Created by PhpStorm.
	 * User: denmedia
	 * Date: 15.04.2017
	 * Time: 12:05
	 */
	class hw_fields_locations{

		/** @var hw_fields_location_root[] */
		public $locations = [];
		/** @var array */
		public $rules = [];
		/** @var array */
		public $rulesId = [];

		use hw_hidden_methods_props;


		/**
		 * @param hw_field $field
		 * @return hw_fields_location_root
		 */
		public function add( hw_field $field ){
			$globalId = hiweb()->string()->rand();
			$location = new hw_fields_location_root( $field, $globalId );
			$this->locations[ $globalId ] = $location;
			$this->rules[ $globalId ] = [];
			$this->rulesId[ $globalId ] = '';
			return $location;
		}


		/**
		 * @param $globalId
		 * @return bool|hw_fields_location_root
		 */
		public function get( $globalId ){
			if( !isset( $this->locations[ $globalId ] ) )
				return false;
			return $this->locations[ $globalId ];
		}


		/**
		 * Return array of locations
		 * @param string|array $groups - allow forms: 'post_type','taxonomy','user','options'
		 * @return hw_fields_location_root[]
		 * @internal param bool $like
		 */
		public function get_byGroup( $groups = [ 'post_type' ] ){
			$R = [];
			if( !is_array( $groups ) )
				$groups = [ $groups ];
			foreach( $this->rulesId as $location_id => $rule ){
				foreach( $groups as $group_name ){
					if( preg_match( '/^' . $group_name . ':.*/i', $rule ) ){
						$R[] = $this->locations[ $location_id ];
					}
				}
			}
			return $R;
		}


		/**
		 * Сопоставляет правило и заданному фильтру контекста, возвращая TRUE или FALSE.
		 * По указанию аргумента $return_result_array - возвращает составленные паттерны и результат соответствия
		 * @param       $ruleId              - строка правила
		 * @param       $group               - наименование группы
		 * @param array $filter              - массив контекста
		 * @param bool  $return_result_array - возвратить массив паттернов с рузельтатами
		 * @return string
		 */
		public function get_context_compare( $ruleId, $group, $filter = [], $return_result_array = false ){
			$R = true;
			$PATTERNS = [];
			////
			if( !is_array( $filter ) )
				$filter = [ $filter ];
			//attributes constructor
			foreach( $filter as $key => $val ){
				$filter_pattern = ['(?:"' . $key . '":(?>'];
				if( !is_array( $val ) && !is_bool( $val ) )
					$val = [ $val ];
				$pattern_val = [];
				if( count( $val ) > 1 ){
					foreach( $val as $key2 => $val2 ){
						$pattern_val[] = json_encode( $val2, JSON_UNESCAPED_UNICODE );
					}
					$filter_pattern[] = '\[' . implode( '|', $pattern_val ) . '\]';
				} else {
					$filter_pattern[] = strtr( json_encode( $val, JSON_UNESCAPED_UNICODE ), [ '[' => '\[', ']' => '\]' ] );
				}
				$filter_pattern[] = ')|(?!"' . $key . '":\[(?:"[\w\d]+"|\d)\]).)*';
				//(?:"slug":\["theme"\]|(?!"slug":\["\w+"\]).)
				$filter_pattern = '/^' . $group . ':{' . implode('',$filter_pattern) . '}$/i';
				if( !array_key_exists( $filter_pattern, $PATTERNS ) )
					$PATTERNS[ $filter_pattern ] = preg_match( $filter_pattern, $ruleId );
				if( $PATTERNS[ $filter_pattern ] == 0 )
					$R = false;
			}
			//
			return $return_result_array ? $PATTERNS : $R;
		}


		/**
		 * @param       $group
		 * @param array $filter
		 * @param array $not_in_filter
		 * @param bool  $like
		 * @return string
		 */
		/*public function get_pattern_by( $group, $filter = [], $not_in_filter = [], $like = true ){
			///PREPARE PATTERN FILTER
			$pattern = [ '/^' . $group . ':{' ];
			if( $like ){
				$pattern[] = '.*';
			}
			if( !is_array( $filter ) )
				$filter = [ $filter ];
			if( !is_array( $not_in_filter ) )
				$not_in_filter = [ $not_in_filter ];
			///ATTRIBUTES FILTER
			$filter_pattern = [];
			foreach( $filter as $key => $val ){
				if( !is_array( $val ) && !is_bool( $val ) )
					$val = [ $val ];
				$pattern_val = [];
				if( count( $val ) > 1 ){
					foreach( $val as $key2 => $val2 ){
						$pattern_val[] = json_encode( $val2, JSON_UNESCAPED_UNICODE );
					}
					$filter_pattern[] = '"' . $key . '":(?>\[' . implode( '|', $pattern_val ) . '\])';
				} else {
					$filter_pattern[] = '"' . $key . '":(?>' . strtr( json_encode( $val, JSON_UNESCAPED_UNICODE ), [ '[' => '\[', ']' => '\]' ] ) . ')';
				}
			}
			//Attributes construct
			if( count( $filter_pattern ) > 0 ){
				if( count( $filter_pattern ) > 1 )
					$pattern[] = '(?>';
				$pattern[] = '(?>' . implode( ',?)|(?>', $filter_pattern ) . ',?)';
				if( count( $filter_pattern ) > 1 )
					$pattern[] = '){' . count( $filter_pattern ) . '}';
			}
			///ATTRIBUTES NOT IN FILTER
			$filter_pattern = [];
			foreach( $not_in_filter as $key => $val ){
				if( !is_array( $val ) && !is_bool( $val ) )
					$val = [ $val ];
				$pattern_val = [];
				if( count( $val ) > 1 ){
					foreach( $val as $key2 => $val2 ){
						$pattern_val[] = json_encode( $val2, JSON_UNESCAPED_UNICODE );
					}
					$filter_pattern[] = '"' . $key . '":(?>\[' . implode( '|', $pattern_val ) . '\])';
				} else {
					$filter_pattern[] = '"' . $key . '":(?>' . strtr( json_encode( $val, JSON_UNESCAPED_UNICODE ), [ '[' => '\[', ']' => '\]' ] ) . ')';
				}
			}
			//Attributes construct
			if( count( $filter_pattern ) > 0 ){
				if( count( $filter_pattern ) > 1 )
					$pattern[] = '(?>';
				$pattern[] = '[^(?>' . implode( ',?)|(?>', $filter_pattern ) . ',?)]';
				if( count( $filter_pattern ) > 1 )
					$pattern[] = '){' . count( $filter_pattern ) . '}';
			}
			//LIKE
			if( !$like )
				$pattern[] = '}$';
			$pattern[] = '/i';
			//PATTER CONSTRUCT
			return implode( '', $pattern );
		}*/

		/**
		 * Return Locations by filter
		 * USE: $locations = hiweb()->locations()->get_by( $group = 'post_type', $filter = [ 'post_type' => 'page' ], $like = true );
		 * @param string $group
		 * @param array  $filter
		 * @param array  $not_in_filter
		 * @param bool   $like
		 * @return hw_fields_location_root[]
		 */
		public function get_by( $group, $filter = [] ){
			$R = [];
			foreach( $this->rulesId as $location_id => $ruleStr ){
				if( $this->get_context_compare( $ruleStr, $group, $filter ) ){
					$R[] = $this->locations[ $location_id ];
				}
			}
			return $R;
		}


		/**
		 * Get fields by filtered locations
		 * @param       $group
		 * @param array $filter
		 * @param array $not_in_filter
		 * @param bool  $like
		 * @return hw_field[]
		 */
		public function get_fields_by( $group, $filter = [] ){
			$locations = $this->get_by( $group, $filter );
			$R = [];
			foreach( $locations as $location ){
				$R[ $location->get_field()->get_id() ] = $location->get_field();
			}
			return $R;
		}

	}


	class hw_fields_location_root{

		use hw_hidden_methods_props;

		public $rules = array();
		public $rulesId = '';
		public $globalId = '';
		/** @var hw_field */
		private $field;


		/**
		 * hw_fields_location constructor.
		 * @param hw_field $field
		 * @param string   $location_globalId - global location id
		 */
		public function __construct( hw_field $field, $location_globalId ){
			$this->globalId = $location_globalId;
			$this->field = $field;
		}


		/**
		 * Update Rules Id, register them
		 */
		public function update_rulesId(){
			$sections = [];
			foreach( $this->rules as $rule_group => $rules ){
				$sections[ $rule_group ] = $rule_group . ':' . json_encode( $rules, JSON_UNESCAPED_UNICODE ) . '';
			}
			$this->rulesId = rtrim( implode( '|', $sections ), ':|' );
			hiweb()->fields()->locations()->rules[ $this->globalId ] = $this->rules;
			hiweb()->fields()->locations()->rulesId[ $this->globalId ] = $this->rulesId;
		}


		/**
		 * @param null|string|array $post_type - массив и название типа поста, напрмиер 'page'
		 * @return hw_fields_location_post_type
		 */
		public function post_type( $post_type = null ){
			$this->rules['post_type'] = [];
			$location = new hw_fields_location_post_type( $this );
			if( is_array( $post_type ) || is_string( $post_type ) ){
				$location->post_type( $post_type );
			}
			return $location;
		}


		/**
		 * @param null $taxonomy
		 * @return hw_fields_location_taxonomy
		 */
		public function taxonomy( $taxonomy = null ){
			$this->rules['taxonomy'] = [];
			$location = new hw_fields_location_taxonomy( $this );
			if( is_array( $taxonomy ) || is_string( $taxonomy ) ){
				$location->name( $taxonomy );
			}
			return $location;
		}


		/**
		 * @return hw_fields_location_user
		 */
		public function user(){
			$this->rules['user'] = [];
			return new hw_fields_location_user( $this );
		}


		/**
		 * @param string $slug - 'options-general.php' or 'general', 'options-writing.php', 'options-reading.php',
		 * @return hw_fields_location_options_page
		 */
		public function options_page( $slug = 'options-general.php' ){
			$this->rules['options_page'] = [];
			$location = new hw_fields_location_options_page( $this );
			$location->slug( $slug );
			return $location;
		}


		public function admin_menu( $slug = 'theme' ){
			$this->rules['admin_menu'] = [];
			$location = new hw_fields_location_admin_menu( $this );
			$location->slug( $slug );
			return $location;
		}


		/**
		 * @return hw_field
		 */
		public function get_field(){
			return $this->field;
		}

	}


	class hw_fields_location_post_type{

		/** @var hw_fields_location_root */
		private $location_root;


		public function __construct( hw_fields_location_root $location_root ){
			$this->location_root = $location_root;
			$this->location_root->rules['post_type']['position'] = [ 3 ];
			$this->location_root->update_rulesId();
		}


		public function ID( $set ){
			$this->location_root->rules['post_type'][ __FUNCTION__ ] = is_array( $set ) ? $set : [ $set ];
			$this->location_root->update_rulesId();
			return $this;
		}


		public function post_name( $set ){
			$this->location_root->rules['post_type'][ __FUNCTION__ ] = is_array( $set ) ? $set : [ $set ];
			$this->location_root->update_rulesId();
			return $this;
		}


		public function post_type( $set = 'page' ){
			$this->location_root->rules['post_type'][ __FUNCTION__ ] = is_array( $set ) ? $set : [ $set ];
			$this->location_root->update_rulesId();
			return $this;
		}


		public function post_status( $set ){
			$this->location_root->rules['post_type'][ __FUNCTION__ ] = is_array( $set ) ? $set : [ $set ];
			$this->location_root->update_rulesId();
			return $this;
		}


		public function comment_status( $set ){
			$this->location_root->rules['post_type'][ __FUNCTION__ ] = is_array( $set ) ? $set : [ $set ];
			$this->location_root->update_rulesId();
			return $this;
		}


		public function post_parent( $set ){
			$this->location_root->rules['post_type'][ __FUNCTION__ ] = is_array( $set ) ? $set : [ $set ];
			$this->location_root->update_rulesId();
			return $this;
		}


		public function have_taxonomy( $set ){
			$this->location_root->rules['post_type'][ __FUNCTION__ ] = is_array( $set ) ? $set : [ $set ];
			$this->location_root->update_rulesId();
			return $this;
		}


		public function front_page( $set = true ){
			$this->location_root->rules['post_type'][ __FUNCTION__ ] = $set;
			$this->location_root->update_rulesId();
			return $this;
		}


		public function taxonomy_term( $taxonomy, $terms, $term_field = 'slug' ){
			$this->location_root->rules['post_type'][ __FUNCTION__ ][ $taxonomy ][ $term_field ] = is_array( $terms ) ? $terms : [ $terms ];
			$this->location_root->update_rulesId();
			return $this;
		}


		/**
		 * @param int $position - позиция: 1 - after title, 2 - before editor, 3 - after editor, 4 - over sidebar, 5 - bottom on edit page
		 * @return $this
		 */
		public function position( $position = 3 ){
			$this->location_root->rules['post_type'][ __FUNCTION__ ] = [ $position ];
			$this->location_root->update_rulesId();
			return $this;
		}


		/**
		 * @return hw_field
		 */
		public function get_field(){
			return $this->location_root->get_field();
		}

	}


	class hw_fields_location_taxonomy{

		/** @var hw_fields_location_root */
		private $location_root;


		public function __construct( hw_fields_location_root $location_root ){
			$this->location_root = $location_root;
		}


		public function hierarchical( $set = true ){
			$this->location_root->rules['taxonomy'][ __FUNCTION__ ] = $set;
			$this->location_root->update_rulesId();
			return $this;
		}


		public function name( $set ){
			$this->location_root->rules['taxonomy'][ __FUNCTION__ ] = is_array( $set ) ? $set : [ $set ];
			$this->location_root->update_rulesId();
			return $this;
		}


		public function object_type( $set ){
			$this->location_root->rules['taxonomy'][ __FUNCTION__ ] = is_array( $set ) ? $set : [ $set ];
			$this->location_root->update_rulesId();
			return $this;
		}


		public function term( $taxonomy_name, $terms, $term_field = 'slug' ){
			$this->location_root->rules['post_type'][ __FUNCTION__ ][ $taxonomy_name ][ $term_field ] = is_array( $terms ) ? $terms : [ $terms ];
			$this->location_root->update_rulesId();
			return $this;
		}

	}


	class hw_fields_location_user{

		/** @var hw_fields_location_root */
		private $location_root;


		public function __construct( hw_fields_location_root $location_root ){
			$this->location_root = $location_root;
		}
	}


	class hw_fields_location_options_page{

		/** @var hw_fields_location_root */
		private $location_root;


		public function __construct( hw_fields_location_root $location_root ){
			$this->location_root = $location_root;
			$this->location_root->rules['options_page']['section_title'] = [ '' ];
			$this->location_root->update_rulesId();
		}


		/**
		 * @param $set
		 * @return $this
		 */
		public function slug( $set ){
			$set = preg_replace( [ '/^options-/', '/.php$/' ], '', $set );
			$this->location_root->rules['options_page'][ __FUNCTION__ ] = is_array( $set ) ? $set : [ $set ];
			$this->location_root->update_rulesId();
			return $this;
		}


		public function section_title( $set ){
			$this->location_root->rules['options_page'][ __FUNCTION__ ] = is_array( $set ) ? $set : [ $set ];
			$this->location_root->update_rulesId();
			return $this;
		}
	}


	class hw_fields_location_admin_menu{

		/** @var hw_fields_location_root */
		private $location_root;


		public function __construct( hw_fields_location_root $location_root ){
			$this->location_root = $location_root;
		}


		/**
		 * @param $set
		 * @return $this
		 */
		public function slug( $set ){
			$this->location_root->rules['admin_menu'][ __FUNCTION__ ] = is_array( $set ) ? $set : [ $set ];
			$this->location_root->update_rulesId();
			return $this;
		}
	}