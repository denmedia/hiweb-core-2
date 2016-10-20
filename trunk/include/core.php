<?php
	/**
	 * Created by PhpStorm.
	 * User: hiweb
	 * Date: 30.06.2016
	 * Time: 15:34
	 */

	if( !class_exists( 'hiweb' ) ){

		/**
		 * Запрос к корневому классу hiweb
		 * @return hiweb
		 */
		function hiweb(){
			static $class;
			if( !$class instanceof hiweb )
				$class = new hiweb();
			return $class;
		}


		class hiweb{

			private $modules = array();


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
			 * @return bool|hw_path
			 */
			public function path(){
				return $this->module( 'path' );
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
			 * Получить / созлать новый тип записей.
			 * @param string $type - необходимый тип
			 * @return bool|hw_post_type_object
			 */
			public function post_type( $type = 'post' ){
				return $this->module( 'post_type' )->object( $type );
			}


			/**
			 * @return bool|hw_wp
			 */
			public function wp(){
				return $this->module( 'wp' );
			}


			/**
			 * @return bool|hw_admin
			 */
			public function admin(){
				return $this->module( 'admin' );
			}


			/**
			 * @return bool|hw_date
			 */
			public function date(){
				return $this->module( 'date' );
			}


			/**
			 * @param null $id - необязательный параметр
			 * @return bool|hw_form_object
			 */
			public function form( $id = null ){
				return $this->module( 'form' )->get( $id );
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