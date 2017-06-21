<?php
	/**
	 * Created by PhpStorm.
	 * User: hiweb
	 * Date: 30.06.2016
	 * Time: 15:34
	 */

	include_once 'short_functions.php';

	if( !class_exists( 'hw_core' ) ){

		class hw_core{

			private $classes = [];
			/** @var string Корневая папка плагина */
			public $dir = '';
			public $dir_base = '';
			/** @var string URL корневой папка плагина */
			public $url = '';
			public $url_base = '';
			/** @var string Корневая папка яндра */
			public $dir_include = 'include';
			/** @var string Корневая папка видов */
			public $dir_views = 'views';
			/** @var string Корневая папка модулей */
			public $dir_classes = 'classes';
			/** @var string Корневая папка трейтов */
			public $dir_traits = 'traits';
			/** @var string Корневая папка трейтов */
			public $dir_tools = 'tools';
			/** @var string Путь до папки стилей */
			public $dir_css = 'css';
			/** @var string URL папки стилей */
			public $url_css = 'css';
			/** @var string Папка скриптов JS */
			public $dir_js = 'js';
			/** @var string URL папки скриптов JS */
			public $url_js = 'js';
			/** @var string Папка VENDORS */
			public $dir_vendors = 'vendors';
			/** @var string URL папки VENDORS */
			public $url_vendors = 'vendors';


			public function __call( $name, $arguments ){
				$this->console()->warn( 'hiweb()->' . $name . '() error: вызван не существующий метод [' . $name . ']', true );
			}


			public function __construct(){
				///
				$this->dir = dirname( dirname( __FILE__ ) );
				$this->dir_base = $this->path()->base_dir();
				$this->url = $this->path()->path_to_url( $this->dir );
				$this->url_base = $this->path()->base_url();
				///
				$this->url_css = $this->url . '/' . $this->dir_css;
				$this->url_js = $this->url . '/' . $this->dir_js;
				$this->url_vendors = $this->url . '/' . $this->url_vendors;
				$this->dir_include = $this->dir . '/' . $this->dir_include;
				$this->dir_views = $this->dir . '/' . $this->dir_views;
				$this->dir_classes = $this->dir_include . '/' . $this->dir_classes;
				$this->dir_traits = $this->dir_include . '/' . $this->dir_traits;
				$this->dir_tools = $this->dir_include . '/' . $this->dir_tools;
				$this->dir_css = $this->dir . '/' . $this->dir_css;
				$this->dir_js = $this->dir . '/' . $this->dir_js;
				$this->dir_vendors = $this->dir . '/' . $this->dir_vendors;
				///Load traits
				$this->path()->include_dir( $this->dir_traits );
				///ERRORS DISPLAY
				if( defined( WP_DEBUG ) && WP_DEBUG ){
					$this->errors();
				}
			}


			/**
			 * @return bool|hw_admin
			 */
			public function admin(){
				return $this->give_class( 'admin' );
			}


			/**
			 * @return bool|hw_arrays
			 */
			public function arrays(){
				return $this->give_class( 'arrays' );
			}


			/**
			 * @return bool|hw_backtrace
			 */
			public function backtrace(){
				return $this->give_class( 'backtrace' );
			}


			/**
			 * @param null $data
			 * @return bool|hw_console
			 */
			public function console( $data = null ){
				if( !is_null( $data ) || trim( $data ) != '' ) return $this->give_class( 'console' )->info( $data ); else return $this->give_class( 'console', $data );
			}


			/**
			 * Возвращает данные контекста, текущего контекста
			 * @return hw_context
			 */
			public function context(){
				return $this->give_class( 'context' );
			}


			/**
			 * Display Errors ON, Debug enable
			 * @return hw_errors
			 */
			public function errors(){
				return $this->give_class( 'errors' );
			}


			/**
			 * @return hw_fields
			 */
			public function fields(){
				return $this->give_class( 'fields' );
			}


			//			/**
			//			 * @param        $fieldId - индификатор поля
			//			 * @param string $type    - тип поля
			//			 * @param null   $name    - имя поля
			//			 * @return hw_field
			//			 */
			//			public function field( $fieldId, $type = 'text', $name = null ){
			//				return $this->fields()->make( $fieldId, $type, $name );
			//			}

			/**
			 * Возвращает класс-контроллер форм
			 * @return bool|hw_forms
			 */
			public function forms(){
				return $this->give_class( 'forms' );
			}


			/**
			 * Возвращает форму
			 * @param null $id - необязательный параметр, задать ID формы
			 * @return hw_form
			 */
			public function form( $id = null ){
				return $this->forms()->give( $id );
			}


			/**
			 * @return bool|hw_string
			 */
			public function string(){
				return $this->give_class( 'string' );
			}


			/**
			 * @param null $data
			 * @return mixed|hw_dump
			 */
			public function dump( $data = null ){
				return $this->give_class( 'dump', $data, true );
			}


			/**
			 * Подключение файла CSS
			 * @param      $file
			 * @param bool $in_footer
			 * @return mixed
			 */
			public function css( $file, $in_footer = false ){
				return $this->give_class( 'css' )->enqueue( $file, $in_footer );
			}


			/**
			 * Подключение JS файла
			 * @param       $file
			 * @param array $afterJS   - список предварительных JS файлов от WP
			 * @param bool  $in_footer - показывать в фтуре
			 * @return mixed
			 */
			public function js( $file, $afterJS = [], $in_footer = false ){
				return $this->give_class( 'js' )->enqueue( $file, $afterJS, $in_footer );
			}


			/**
			 * @return hw_inputs
			 */
			public function inputs(){
				return $this->give_class( 'inputs' );
			}


			/**
			 * Корневой класс для работы с полями ввода
			 * @param string            $type
			 * @param bool|false|string $id
			 * @param null              $value - значение
			 * @return hw_input
			 */
			public function input( $type = 'text', $id = false, $value = null ){
				$input = $this->inputs()->create( $type, $id, $value );
				return $input;
			}


			/**
			 * Получить класс мета данных
			 * @param null $field_id
			 * @param null $screen_id
			 * @return bool|hw_meta_field
			 */
			public function meta( $field_id = null, $screen_id = null ){
				$meta = $this->give_class( 'meta' )->give( $field_id );
				if( !is_null( $screen_id ) ) $meta->object_id = $screen_id;
				return $meta;
			}


			/**
			 * @return hw_post_types
			 */
			public function post_types(){
				$this->inputs();
				return $this->give_class( 'post_types' );
			}


			/**
			 * Получить / созлать новый тип записей.
			 * @param string $post_type - вернуть указанный тип
			 * @return bool|hw_post_type
			 */
			public function post_type( $post_type = 'post' ){
				return $this->post_types()->give( $post_type );
			}


			/**
			 * @return bool|hw_path
			 */
			public function path(){
				return $this->give_class( 'path' );
			}


			/**
			 * @param null $postOrId
			 * @return bool|hw_post
			 */
			public function post( $postOrId = null ){
				return $this->give_class( 'posts' )->get( $postOrId );
			}


			/**
			 * @return hw_taxonomies
			 */
			public function taxonomies(){
				return $this->give_class( 'taxonomies' );
			}


			/**
			 * @param $theme_name
			 * @return hw_theme
			 */
			public function theme( $theme_name = null ){
				return $this->give_class( 'theme', $theme_name );
			}


			/**
			 * @return hw_users
			 */
			public function users(){
				$this->inputs();
				return $this->give_class( 'users' );
			}


			/**
			 * @param $loginOrId - логин, мэил или ID пользователя
			 * @return bool|hw_user
			 */
			public function user( $loginOrId = null ){
				return $this->users()->get( $loginOrId );
			}


			/**
			 * @return bool|hw_date
			 */
			public function date(){
				return $this->give_class( 'date' );
			}


			/**
			 * @return bool|hw_tools
			 */
			public function tools(){
				return $this->give_class( 'tools' );
			}


			/**
			 * Подключение класса
			 * @param            $name
			 * @param null|mixed $data
			 * @param bool       $newInstance
			 * @return mixed
			 * @version 1.2
			 */
			protected function give_class( $name, $data = null, $newInstance = false ){
				if( !array_key_exists( $name, $this->classes ) ) $this->classes[ $name ] = [];
				$index = count( $this->classes[ $name ] );
				if( $index == 0 || $newInstance ){
					$className = 'hw_' . $name;
					if( !class_exists( $className ) ){
						$this->classes[ $name ][ $index ] = null;
						include_once $this->dir_classes . '/' . $name . '.php';
					}
					$this->classes[ $name ][ $index ] = new $className( $data );
					///EVENT INIT
					if( method_exists( end( $this->classes[ $name ] ), '_init' ) ){
						end( $this->classes[ $name ] )->_init( $data );
					}
				}
				///EVENT CALL
				if( method_exists( end( $this->classes[ $name ] ), '_call' ) ){
					end( $this->classes[ $name ] )->_call( $data );
				}
				///
				return end( $this->classes[ $name ] );
			}


		}
	}