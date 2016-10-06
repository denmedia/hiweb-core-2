<?php
	/**
	 * Created by PhpStorm.
	 * User: d9251
	 * Date: 31.08.2016
	 * Time: 17:36
	 */


	/**
	 * Класс для работы с WordPress
	 * Class hiweb_wp
	 */
	class hw_wp{

		/** @var hw_wp_post[] */
		private $posts = array();

		/** @var hw_wp_taxonomy[] */
		private $taxonomies = array();

		/** @var hw_wp_theme[] */
		private $themes = array();

		/** @var hw_wp_cpt[] */
		private $cpts = array();

		/** @var hw_wp_add_taxonomy[] */
		private $add_taxonomies = array();

		/** @var hw_wp_user[] */
		private $users = array();

		/** @var hw_wp_user_meta_boxes[] */
		private $_user_meta_boxes = array();


		/**
		 * Возвращает hiweb_wp_post
		 * @param int|WP_post $postOrId
		 * @return hw_wp_post
		 */
		public function post( $postOrId ){
			if( $postOrId instanceof WP_Post ){
				$postId = $postOrId->ID;
			}else{
				$postId = $postOrId;
			}
			///
			if( !isset( $this->posts[ $postId ] ) ){
				$this->posts[ $postId ] = new hw_wp_post( $postOrId );
			}

			return $this->posts[ $postId ];
		}


		/**
		 * @param int|WP_Post $postOrId
		 * @param null $key
		 * @param null $use_regex_index
		 * @return hw_wp_post_meta|mixed|null
		 */
		public function meta( $postOrId, $key = null, $use_regex_index = null ){
			return $this->post( $postOrId )->meta( $key, $use_regex_index );
		}


		public function taxonomy( $taxonomy = null ){
			if( !array_key_exists( $taxonomy, $this->taxonomies ) ){
				$this->taxonomies[ $taxonomy ] = new hw_wp_taxonomy( $taxonomy );
			}

			return $this->taxonomies[ $taxonomy ];
		}


		/**
		 * Возвращает класс создания таксономии
		 * @param $name
		 * @return hw_wp_add_taxonomy
		 */
		public function add_taxonomy( $name ){
			if( !array_key_exists( $name, $this->add_taxonomies ) ){
				$this->add_taxonomies[ $name ] = new hw_wp_add_taxonomy( $name );
			}
			return $this->add_taxonomies[ $name ];
		}


		/**
		 * @param $theme - слуг темы
		 * @return hw_wp_theme
		 */
		public function theme( $theme = null ){
			if( !is_string( $theme ) || trim( $theme ) == '' ){
				$theme = get_option( 'template' );
			}
			if( !array_key_exists( $theme, $this->themes ) ){
				$this->themes[ $theme ] = new hw_wp_theme( $theme );
			}

			return $this->themes[ $theme ];
		}


		/**
		 * Возвращает корневой CPT класс для работы с кастомным типом поста
		 * @param $post_type
		 * @return hw_wp_cpt
		 */
		public function cpt( $post_type ){
			if( !array_key_exists( $post_type, $this->cpts ) ){
				$this->cpts[ $post_type ] = new hw_wp_cpt( $post_type );
			}
			return $this->cpts[ $post_type ];
		}


		/**
		 * Возвращает корневой класс для работы с данными пользователя
		 * @param $idOrLoginOrEmail - если не указывать, то будет взят текущий авторизированный пользователь
		 * @return hw_wp_user
		 */
		public function user( $idOrLoginOrEmail = null ){
			if( is_null( $idOrLoginOrEmail ) ){
				require_once ABSPATH.'/wp-includes/pluggable.php';
				$current_user = wp_get_current_user();
				if( $current_user instanceof WP_User )
					$idOrLoginOrEmail = $current_user->ID;
			}
			if( !isset( $this->users[ $idOrLoginOrEmail ] ) ){
				$user = new hw_wp_user( $idOrLoginOrEmail );
				$this->users[ $idOrLoginOrEmail ] = $user;
				if( $user->is_exist() ){
					$this->users[ $user->id() ] = $user;
					$this->users[ $user->login() ] = $user;
					$this->users[ $user->email() ] = $user;
				}
			}
			return $this->users[ $idOrLoginOrEmail ];
		}


		public function add_user_meta_box( $id, $hiweb_user_meta_boxes = null ){
			if( !isset( $this->_user_meta_boxes[ $id ] ) ){
				if( $hiweb_user_meta_boxes instanceof hw_wp_user_meta_boxes )
					$this->_user_meta_boxes[ $id ] = $hiweb_user_meta_boxes;else $this->_user_meta_boxes[ $id ] = new hw_wp_user_meta_boxes( $id );
			}
			return $this->_user_meta_boxes[ $id ];
		}


		/**
		 * Возвращает TRUE, если текущий запрос происходит через AJAX
		 * @return bool
		 */
		public function is_ajax(){
			return ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ( !empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' );
		}


	}


	/**
	 * Класс для работы с одной записью
	 * Class hiweb_wp_post
	 */
	class hw_wp_post{

		/**
		 * @var array|null|WP_Post
		 */
		private $object;

		/**
		 * @var hw_wp_post_meta
		 */
		private $meta;

		private $taxonomy_exist = array();
		/** @var hw_wp_taxonomy[] */
		private $taxonomies = array();


		public function __construct( $postOrId = 0 ){
			$this->object = get_post( $postOrId );
		}


		/**
		 * Возвращает TRUE, если пост существует
		 * @return bool
		 */
		public function exist(){
			return ( $this->object instanceof WP_Post );
		}


		/**
		 * Возвращает текущий объект WP_Post, либо NULL
		 * @return array|null|WP_Post
		 */
		public function object(){
			return $this->object;
		}


		/**
		 * Возвращает ID записи
		 * @return int|null
		 */
		public function id(){
			return ( $this->object instanceof WP_Post ) ? $this->object->ID : null;
		}


		/**
		 * Возвращает класс для работы с мета записи
		 * @param null|string $key - вернуть значение ключа мета, либо regex-паттерн (обязатиельно указать INT $use_regex_index), либо объект класса для работы с мета даннойзаписи
		 * @param null $use_regex_index - если $key является паттерном regex, то вернуть значение по индексу найденного ключа
		 * @return hw_wp_post_meta|mixed|null
		 */
		public function meta( $key = null, $use_regex_index = null ){
			if( !$this->meta instanceof hw_wp_post_meta ){
				$this->meta = new hw_wp_post_meta( $this->object );
			}
			if( is_null( $key ) ){
				return $this->meta;
			}

			return $this->meta->get( $key, $use_regex_index );
		}


		/**
		 * Возвращает TRUE, если таксономия принадлежит типу записи
		 * @param $taxonomy
		 * @return bool
		 */
		public function taxonomy_exist( $taxonomy ){
			if( !array_key_exists( $taxonomy, $this->taxonomy_exist ) ){
				$taxonomies = get_post_taxonomies( $this->object );
				$this->taxonomy_exist[ $taxonomy ] = array_key_exists( $taxonomy, array_flip( $taxonomies ) );
			}

			return $this->taxonomy_exist[ $taxonomy ];
		}


		/**
		 * Возвращает класс таксономии hiweb_wp_taxonomy
		 * @param $taxonomy
		 * @return hw_wp_taxonomy|bool
		 */
		public function taxonomy( $taxonomy ){
			if( !isset( $this->taxonomies[ $taxonomy ] ) ){
				if( $this->taxonomy_exist( $taxonomy ) ){
					$this->taxonomies[ $taxonomy ] = false;
				}
				$this->taxonomies[ $taxonomy ] = hiweb()->wp()->taxonomy( $taxonomy );
			}

			return $this->taxonomies[ $taxonomy ];
		}


		/**
		 * @return hw_wp_taxonomy[]
		 */
		public function taxonomies(){
			$taxonomies = get_post_taxonomies( $this->object );
			$R = array();
			if( is_array( $taxonomies ) ){
				foreach( $taxonomies as $taxonomy ){
					$tax = $this->taxonomy( $taxonomy );
					if( $tax->exist() ){
						$R[ $tax->name() ] = $tax;
					}
				}
			}

			return $R;
		}


		/**
		 * Возвращает массив терминов данного поста
		 * @param      $taxonomy - если не указать таксономию (указав 0, null, false), то будет вернут массив, сгрупированный по таксономиям
		 * @param null $only_field - если указать ключь термина, например name, то вместо WP_Term вернеться расгруппированный объект по ключу
		 * @return array
		 */
		public function terms( $taxonomy = null, $only_field = null ){
			$R = array();
			if( !is_string( $taxonomy ) || trim( $taxonomy ) == '' ){
				$taxonomies = $this->taxonomies();
				foreach( $taxonomies as $tax ){
					$R[ $tax->name() ] = $this->terms( $tax->name(), $only_field );
				}
			}elseif( $this->taxonomy_exist( $taxonomy ) ){
				$terms = get_the_terms( $this->id(), $taxonomy );
				if( is_array( $terms ) ){
					foreach( $terms as $term ){
						if( is_string( $only_field ) && trim( $only_field ) != '' ){
							$R[ $term->term_id ] = property_exists( $term, $only_field ) ? $term->{$only_field} : $term;
						}else{
							$R[ $term->term_id ] = $term;
						}
					}
				}
			}

			return $R;
		}

	}


	/**
	 * Класс для работы с мета-данными одной записи
	 * Class hw_wp_post_meta
	 */
	class hw_wp_post_meta{

		/**
		 * @return hw_wp_post
		 */
		private $post;

		/**
		 * @var array
		 */
		private $meta;


		public function __construct( $postOrId ){
			$this->post = get_post( $postOrId );
			if( $this->post instanceof WP_Post ){
				$meta = get_post_meta( $this->post->ID );
				if( is_array( $meta ) ){
					foreach( $meta as $key => $val ){
						$this->meta[ $key ] = get_post_meta( $this->post->ID, $key, true );
					}
				}
			}
		}


		/**
		 * Возвращает массив значений мета
		 * @param null $key_regex_pattern - regex-паттер для поиска нужных ключей, если не указать, будут вернуты все ключи
		 * @return array
		 */
		public function arr( $key_regex_pattern = null ){
			if( is_null( $key_regex_pattern ) ){
				return $this->meta;
			}
			////
			$R = array();
			foreach( $this->meta as $key => $val ){
				if( preg_match( $key_regex_pattern, $key ) > 0 ){
					$R[ $key ] = $val;
				}
			}

			return $R;
		}


		/**
		 * Возвращает значение ключа
		 * @param null $key - ключь, либо regex-паттерн (обязатиельно указать INT $use_regex_index)
		 * @param null $use_regex_index - если $key является паттерном regex, то вернуть значение по индексу найденного ключа
		 * @return mixed|null
		 */
		public function get( $key = null, $use_regex_index = null ){
			if( is_null( $key ) ){
				return null;
			}
			if( !is_int( $use_regex_index ) ){
				return $this->exist( $key ) ? $this->meta[ $key ] : null;
			}else{
				$arr = $this->arr( $key );

				return array_key_exists( $use_regex_index, $arr ) ? $arr[ $use_regex_index ] : null;
			}
		}


		/**
		 * Возвращает TRUE, если ключ существует
		 * @param $key
		 * @return bool
		 */
		public function exist( $key ){
			return array_key_exists( $key, $this->meta );
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


	class hw_wp_theme{

		/** @var  string */
		private $theme;

		/** @var hw_wp_location[] */
		private $locations = array();


		public function __construct( $theme ){
			$this->theme = $theme;
		}


		public function exist(){
		}


		/**
		 * Возвращает массив локаций
		 * @return array
		 */
		public function locations(){
			$R = array();
			$mods = get_option( 'theme_mods_' . $this->theme );
			if( isset( $mods['nav_menu_locations'] ) ){
				$R = $mods['nav_menu_locations'];
			}

			return $R;
		}


		/**
		 * @param $location
		 * @return hw_wp_location
		 */
		public function location( $location = null ){
			if( !array_key_exists( $location, $this->locations ) ){
				$this->locations[ $location ] = new hw_wp_location( $location );
			}

			return $this->locations[ $location ];
		}


		/**
		 * Возвращает массив с массивами элементов
		 * @param $location
		 * @return array|false
		 */
		public function menu_items( $location ){
			$R = array();
			$menus = wp_get_nav_menus();
			$menu_locations = $this->locations();
			if( isset( $menu_locations[ $location ] ) ){
				foreach( $menus as $menu ){
					if( $menu->term_id == $menu_locations[ $location ] ){
						return wp_get_nav_menu_items( $menu );
					}
				}
			}

			return $R;
		}

	}


	class hw_wp_location{

		private $location;


		public function __construct( $location ){
			$this->location = $location;
		}

	}
	
	
	class hw_wp_cpt{
		
		private $_type;
		/** @var WP_Error|WP_Post_Type */
		private $_object;
		private $_defaults = array(
			'label' => null, 'labels' => array(), 'description' => '', 'public' => false, 'hierarchical' => false, 'exclude_from_search' => null, 'publicly_queryable' => null, 'show_ui' => true, 'show_in_menu' => null, 'show_in_nav_menus' => null,
			'show_in_admin_bar' => null, 'menu_position' => null, 'menu_icon' => 'dashicons-sticky', 'capability_type' => 'post', 'capabilities' => array(), 'map_meta_cap' => null, 'supports' => array(), 'register_meta_box_cb' => null,
			'taxonomies' => array(), 'has_archive' => false, 'rewrite' => true, 'query_var' => true, 'can_export' => true, 'delete_with_user' => null, '_builtin' => false, '_edit_link' => 'post.php?post=%d',
		);
		///////PROPS
		public $label;
		public $labels;
		public $description;
		public $public;
		public $hierarchical;
		public $exclude_from_search;
		public $publicly_queryable;
		public $show_ui;
		public $show_in_menu;
		public $show_in_nav_menus;
		public $show_in_admin_bar;
		public $menu_position;
		public $menu_icon;
		public $capability_type;
		public $capabilities;
		public $map_meta_cap;
		public $supports;
		public $register_meta_box_cb;
		public $taxonomies;
		public $has_archive;
		public $rewrite;
		public $query_var;
		public $can_export;
		public $delete_with_user;
		public $_builtin;
		public $_edit_link;

		///////
		/** @var  hw_wp_add_taxonomy[] */
		private $_taxonomies = array();
		/** @var hw_wp_cpt_meta_boxes[] */
		private $_meta_boxes = array();


		public function __construct( $post_type ){
			$this->_type = $post_type;
			add_action( 'init', array( $this, '_create' ) );
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
		public function _create(){
			$this->_object = register_post_type( $this->_type, $this->props() );
			return $this->get();
		}


		/**
		 * @param string|int $id
		 * @param hw_wp_cpt_meta_boxes $hiweb_meta_boxes
		 * @return hw_wp_cpt_meta_boxes
		 */
		public function add_meta_box( $id, $hiweb_meta_boxes = null ){
			if( !isset( $this->_meta_boxes[ $id ] ) ){
				if( $hiweb_meta_boxes[ $id ] instanceof hw_wp_cpt_meta_boxes )
					$this->_meta_boxes = $hiweb_meta_boxes;else $this->_meta_boxes[ $id ] = new hw_wp_cpt_meta_boxes( $id );
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
		 * @return hw_wp_cpt_meta_boxes[]
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


	class hw_wp_cpt_meta_boxes{

		/** @var string */
		protected $_id;
		protected $title = '&nbsp;';
		protected $callback;
		protected $screen = array();
		protected $context = 'normal'; //normal, advanced или side
		protected $priority = 'default';
		protected $callback_args;
		protected $callback_save_post;
		/** @var hw_form_input[] */
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
			$this->fields[ $id ] = hiweb()->form()->input( $id );
			return $this->fields[ $id ];
		}


		/**
		 * @return hw_form_input[]
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
		}


		protected function generate_meta_box( $post, $meta_box ){
			foreach( $this->fields as $id => $field ){
				if( $post instanceof WP_Post )
					$field->value( get_post_meta( $post->ID, $field->name(), true ) );
				?>
				<p>
					<strong><?php echo $field->label(); ?></strong>
					<label class="screen-reader-text" for="<?php echo $id ?>"><?php echo $field->label() ?></label>
				</p>
				<?php $field->get_echo();
			}
		}
	}


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
			require_once ABSPATH.'/wp-includes/pluggable.php';
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