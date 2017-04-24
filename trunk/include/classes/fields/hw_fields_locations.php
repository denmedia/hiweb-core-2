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
		 * @param array $required_filter     - обязательные параметры в массиве контекста
		 * @param bool  $return_result_array - возвратить массив паттернов с рузельтатами
		 * @return string
		 */
		public function get_context_compare( $ruleId, $group, $filter = [], $required_filter = [], $return_result_array = false ){
			$R = true;
			$PATTERNS[ $ruleId ] = [];
			////
			if( !is_array( $filter ) )
				$filter = [ $filter ];
			//attributes filter
			$filter_pattern = [];
			foreach( $required_filter as $key => $val ){
				if( is_int( $key ) ){
					$filter_pattern[] = '"' . $val . '":(?:{|\[).*(?:}|\])';
				} else {
					$filter_pattern[] = '"' . $key . '":\[' . trim( json_encode( $val ), '[]{}' ) . '\]';
				}
			}
			if( count( $filter_pattern ) > 0 ){
				$filter_pattern = '/(?:(?>' . implode( '),?|(?>', $filter_pattern ) . ')){' . count( $required_filter ) . '}/i';
				if( !array_key_exists( $filter_pattern, $PATTERNS[ $ruleId ] ) ){
					$match = preg_match( $filter_pattern, $ruleId );
					$PATTERNS[ $ruleId ][ $filter_pattern ] = $match;
					if( $match == 0 )
						$R = false;
				}
			}
			foreach( $filter as $key => $val ){
				$filter_pattern = [ '(?:"' . $key . '":(?>' ];
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
				$filter_pattern[] = ')|(?!"' . $key . '":\[(?:"[\w-.\d]+"|\d)\]).)*';
				//(?:"slug":\["theme"\]|(?!"slug":\["\w+"\]).)
				$filter_pattern = '/^' . $group . ':(?:{|\[)' . implode( '', $filter_pattern ) . '(?:}|\])$/i';
				if( !array_key_exists( $filter_pattern, $PATTERNS[ $ruleId ] ) ){
					$match = preg_match( $filter_pattern, $ruleId );
					$PATTERNS[ $ruleId ][ $filter_pattern ] = $match;
					if( $match == 0 )
						$R = false;
				}
			}
			//
			return $return_result_array ? $PATTERNS : $R;
		}


		/**
		 * Return Locations by filter
		 * USE: $locations = hiweb()->locations()->get_by( $group = 'post_type', $filter = [ 'post_type' => 'page' ], $like = true );
		 * @param string $group
		 * @param array  $filter
		 * @param array  $required_filter
		 * @return hw_fields_location_root[]
		 */
		public function get_by( $group, $filter = [], $required_filter = [] ){
			$R = [];
			foreach( $this->rulesId as $location_id => $ruleStr ){
				if( $this->get_context_compare( $ruleStr, $group, $filter, $required_filter ) ){
					$R[] = $this->locations[ $location_id ];
				}
			}
			return $R;
		}


		/**
		 * Get fields by filtered locations
		 * @param       $group
		 * @param array $filter
		 * @param array $required_filter
		 * @return hw_field[]
		 */
		public function get_fields_by( $group, $filter = [], $required_filter = [] ){
			$locations = $this->get_by( $group, $filter, $required_filter );
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
		/** @var  hw_fields_location_post_type */
		private $post_type;
		/** @var  hw_fields_location_taxonomy */
		private $taxonomy;
		/** @var  hw_fields_location_user */
		private $users;
		/** @var  hw_fields_location_options_page */
		private $options_page;
		/** @var hw_fields_location_admin_menu */
		private $admin_menu;


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
			$this->post_type = new hw_fields_location_post_type( $this );
			if( is_array( $post_type ) || is_string( $post_type ) ){
				$this->post_type->post_type( $post_type );
			}
			return $this->post_type;
		}


		/**
		 * @param null $taxonomy
		 * @return hw_fields_location_taxonomy
		 */
		public function taxonomy( $taxonomy = null ){
			$this->rules['taxonomy'] = [];
			$this->update_rulesId();
			$this->taxonomy = new hw_fields_location_taxonomy( $this );
			if( is_array( $taxonomy ) || is_string( $taxonomy ) ){
				$this->taxonomy->name( $taxonomy );
			}
			return $this->taxonomy;
		}


		/**
		 * @return hw_fields_location_user
		 */
		public function user(){
			$this->rules['user'] = [];
			$this->users = new hw_fields_location_user( $this );
			return $this->users;
		}


		/**
		 * @param string $slug - 'options-general.php' or 'general', 'options-writing.php', 'options-reading.php',
		 * @return hw_fields_location_options_page
		 */
		public function options_page( $slug = 'options-general.php' ){
			$this->rules['options_page'] = [];
			$this->options_page = new hw_fields_location_options_page( $this );
			$this->options_page->slug( $slug );
			register_setting( hiweb()->fields()->get_options_group_id( $this->options_page > get_slug() ), hiweb()->fields()->get_options_field_id( $this->options_page > get_slug(), $this->get_field()->get_id() ) );
			return $this->options_page;
		}


		public function admin_menu( $slug = 'theme' ){
			$this->rules['admin_menu'] = [];
			$this->admin_menu = new hw_fields_location_admin_menu( $this );
			$this->admin_menu->slug( $slug );
			register_setting( hiweb()->fields()->get_options_group_id( $slug ), hiweb()->fields()->get_options_field_id( $slug, $this->get_field()->get_id() ) );
			return $this->admin_menu;
		}


		/**
		 * @return hw_field
		 */
		public function get_field(){
			return $this->field;
		}

	}


	class hw_fields_location_post_type{

		use hw_hidden_methods_props;

		/** @var hw_fields_location_root */
		private $location_root;
		private $columns_manager;


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
		 * @param int $position - position in post edit page: 1 - after title, 2 - before editor, 3 - after editor, 4 - over sidebar, 5 - bottom on edit page
		 * @return $this
		 */
		public function position( $position = 3 ){
			$this->location_root->rules['post_type'][ __FUNCTION__ ] = [ $position ];
			$this->location_root->update_rulesId();
			return $this;
		}


		/**
		 * @return hw_fields_location_columns_manager
		 */
		public function columns_manager(){
			if( !$this->columns_manager instanceof hw_fields_location_columns_manager ){
				$this->columns_manager = new hw_fields_location_columns_manager( $this );
				$this->location_root->rules['post_type'][ __FUNCTION__ ] = [];
				$this->location_root->update_rulesId();
			}
			return $this->columns_manager;
		}


		/**
		 * @return hw_field
		 */
		public function get_field(){
			return $this->location_root->get_field();
		}


		/**
		 * @return hw_fields_location_root
		 */
		public function get_location(){
			return $this->location_root;
		}

	}


	class hw_fields_location_taxonomy{

		use hw_hidden_methods_props;

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


		/**
		 * @return hw_field
		 */
		public function get_field(){
			return $this->location_root->get_field();
		}


		/**
		 * @return hw_fields_location_root
		 */
		public function get_location(){
			return $this->location_root;
		}

	}


	class hw_fields_location_user{

		use hw_hidden_methods_props;

		/** @var hw_fields_location_root */
		private $location_root;


		public function __construct( hw_fields_location_root $location_root ){
			$this->location_root = $location_root;
		}


		/**
		 * @return hw_field
		 */
		public function get_field(){
			return $this->location_root->get_field();
		}


		/**
		 * @return hw_fields_location_root
		 */
		public function get_location(){
			return $this->location_root;
		}
	}


	class hw_fields_location_options_page{

		use hw_hidden_methods_props;

		/** @var hw_fields_location_root */
		private $location_root;

		private $slug = '';


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
			$this->slug = $set;
			$this->location_root->rules['options_page'][ __FUNCTION__ ] = is_array( $set ) ? $set : [ $set ];
			$this->location_root->update_rulesId();
			return $this;
		}


		/**
		 * @return string
		 */
		public function get_slug(){
			return $this->slug;
		}


		public function section_title( $set ){
			$this->location_root->rules['options_page'][ __FUNCTION__ ] = is_array( $set ) ? $set : [ $set ];
			$this->location_root->update_rulesId();
			return $this;
		}


		/**
		 * @return hw_field
		 */
		public function get_field(){
			return $this->location_root->get_field();
		}


		/**
		 * @return hw_fields_location_root
		 */
		public function get_location(){
			return $this->location_root;
		}
	}


	class hw_fields_location_admin_menu{

		use hw_hidden_methods_props;

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


		/**
		 * @return hw_field
		 */
		public function get_field(){
			return $this->location_root->get_field();
		}


		/**
		 * @return hw_fields_location_root
		 */
		public function get_location(){
			return $this->location_root;
		}
	}


	class hw_fields_location_columns_manager{

		use hw_hidden_methods_props;

		private $root_location_post_type;
		private $position = 3;
		private $name = null;
		private $callback = null;
		private $sort = false;


		public function __construct( hw_fields_location_post_type $location_post_type ){
			$this->root_location_post_type = $location_post_type;
			$this->position( 3 );
		}


		/**
		 * @param int $set
		 * @return $this
		 */
		public function position( $set = 3 ){
			$this->position = $set;
			$this->root_location_post_type->get_location()->rules['post_type']['columns_manager'][ __FUNCTION__ ] = $set;
			$this->root_location_post_type->get_location()->update_rulesId();
			return $this;
		}


		/**
		 * @param string $set
		 * @return $this
		 */
		public function name( $set = null ){
			$this->name = $set;
			$this->root_location_post_type->get_location()->rules['post_type']['columns_manager'][ __FUNCTION__ ] = $set;
			$this->root_location_post_type->get_location()->update_rulesId();
			return $this;
		}


		public function callback( $set ){
			$this->callback = $set;
			$this->root_location_post_type->get_location()->rules['post_type']['columns_manager'][ __FUNCTION__ ] = $set;
			$this->root_location_post_type->get_location()->update_rulesId();
			return $this;
		}


		/**
		 *
		 */
		public function get_field(){
			$this->root_location_post_type->get_field();
		}

	}