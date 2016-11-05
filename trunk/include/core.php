<?php
	/**
	 * Created by PhpStorm.
	 * User: hiweb
	 * Date: 30.06.2016
	 * Time: 15:34
	 */
	
	if( !class_exists( 'hw_core' ) ){
		
		/**
		 * Запрос к корневому классу hiweb
		 * @return hw_core
		 */
		function hiweb(){
			static $class;
			if( !$class instanceof hw_core )
				$class = new hw_core();
			return $class;
		}
		
		
		class hw_core{
			
			private $modules = array();
			
			
			public function __call( $name, $arguments ){
				$this->console()->warn( 'hiweb()->' . $name . '() error: вызван не существующий метод [' . $name . ']', true );
			}
			
			
			/**
			 * @return bool|hw_admin
			 */
			public function admin(){
				return $this->module( 'admin' );
			}
			
			
			/**
			 * @return bool|hw_arrays
			 */
			public function arrays(){
				return $this->module( 'arrays' );
			}
			
			
			/**
			 * @return bool|hw_backtrace
			 */
			public function backtrace(){
				return $this->module( 'backtrace' );
			}
			
			
			/**
			 * @param null $data
			 * @return bool|hw_console
			 */
			public function console( $data = null ){
				if( !is_null( $data ) || trim( $data ) != '' )
					return $this->module( 'console' )->info( $data );else return $this->module( 'console', $data );
			}
			
			
			/**
			 * Возвращает класс-контроллер форм
			 * @return bool|hw_forms
			 */
			public function forms(){
				return $this->module( 'forms' );
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
				return $this->module( 'string' );
			}
			
			
			/**
			 * @param null $data
			 * @return mixed|hw_dump
			 */
			public function dump( $data = null ){
				return $this->module( 'dump', $data );
			}
			
			
			/**
			 * @param $file
			 * @return mixed
			 */
			public function css( $file ){
				return $this->module( 'css' )->enqueue( $file );
			}
			
			
			/**
			 * @param       $file
			 * @param array $afterJS - список предварительных JS файлов от WP
			 * @param bool $in_footer - показывать в фтуре
			 * @return mixed
			 */
			public function js( $file, $afterJS = array(), $in_footer = false ){
				return $this->module( 'js' )->enqueue( $file, $afterJS, $in_footer );
			}
			
			
			/**
			 * @return hw_inputs
			 */
			public function inputs(){
				return $this->module( 'inputs' );
			}
			
			
			/**
			 * Корневой класс для работы с полями ввода
			 * @param null $id
			 * @param string $type
			 * @return hw_input|hw_input_text|hw_input_checkbox|hw_input_repeat
			 */
			public function input( $id = null, $type = 'text' ){
				return $this->inputs()->make( $id, $type );
			}
			
			
			/**
			 * @param $fieldId
			 * @param null $contextId
			 * @param null $contextType
			 * @return hw_field
			 */
			public function field( $fieldId, $contextId = null, $contextType = null ){
				return $this->module( 'fields' )->give( $fieldId, $contextId, $contextType );
			}
			
			
			/**
			 * Получить класс мета данных
			 * @param null $field_id
			 * @param null $screen_id
			 * @return bool|hw_meta_field
			 */
			public function meta( $field_id = null, $screen_id = null ){
				$meta = $this->module( 'meta' )->give( $field_id );
				if( !is_null( $screen_id ) )
					$meta->object_id = $screen_id;
				return $meta;
			}
			
			
			/**
			 * Получить контроллер-класс мета боксов
			 * @return bool|hw_meta_boxes
			 */
			public function meta_boxes(){
				return $this->module( 'meta_boxes' );
			}
			
			
			/**
			 * Получить мета бокс
			 * @param null $id
			 * @param null $title
			 * @return bool|hw_meta_box
			 */
			public function meta_box( $id = null, $title = null ){
				return $this->meta_boxes()->give( $id, $title );
			}
			
			
			/**
			 * Класс-контроллер опций
			 * @return bool|hw_options
			 */
			public function options(){
				return $this->module( 'options' );
			}
			
			
			/**
			 * Возвращает опцию
			 * @param $id
			 * @param string $type
			 * @return bool|hw_option
			 */
			public function option( $id, $type = 'text' ){
				return $this->options()->give( $id, $type );
			}
			
			
			/**
			 * Получить / созлать новый тип записей.
			 * @param string $post_type - вернуть указанный тип
			 * @return bool|hw_post_type
			 */
			public function post_type( $post_type = 'post' ){
				return $this->module( 'post_types' )->give( $post_type );
			}
			
			
			/**
			 * @return bool|hw_path
			 */
			public function path(){
				return $this->module( 'path' );
			}
			
			
			/**
			 * @param null $postOrId
			 * @return bool|hw_post
			 */
			public function post( $postOrId = null ){
				return $this->module( 'posts' )->get( $postOrId );
			}
			
			
			/**
			 * @return hw_taxonomies
			 */
			public function taxonomies(){
				return $this->module( 'taxonomies' );
			}
			
			
			/**
			 * @param $theme_name
			 * @return hw_theme
			 */
			public function theme( $theme_name = null ){
				return $this->module( 'theme', $theme_name );
			}
			
			
			/**
			 * @return bool|hw_wp
			 */
			public function wp(){
				return $this->module( 'wp' );
			}
			
			
			/**
			 * @param $loginOrId - логин, мэил или ID пользователя
			 * @return bool|hw_user
			 */
			public function user( $loginOrId = null ){
				return $this->module( 'users' )->get( $loginOrId );
			}
			
			
			/**
			 * @return bool|hw_date
			 */
			public function date(){
				return $this->module( 'date' );
			}
			
			
			/**
			 * Подключение модуля
			 * @param            $name
			 * @param null|mixed $data
			 * @param bool $newInstance
			 * @return mixed
			 * @version 1.0
			 */
			protected function module( $name, $data = null, $newInstance = false ){
				if( !array_key_exists( $name, $this->modules ) )
					$this->modules[ $name ] = array();
				$index = count( $this->modules[ $name ] );
				if( $index == 0 || $newInstance ){
					$className = 'hw_' . $name;
					$this->modules[ $name ][ $index ] = null;
					include_once HIWEB_DIR_MODULES . '/' . $name . '.php';
					$this->modules[ $name ][ $index ] = new $className( $data );
				}
				return end( $this->modules[ $name ] );
			}
			
			
		}
	}