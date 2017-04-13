<?php


	/**
	 * Created by PhpStorm.
	 * User: hiweb
	 * Date: 30.06.2016
	 * Time: 22:16
	 */
	class hw_js{

		private $files = array();


		public function __construct(){
			add_action( 'wp_enqueue_scripts', array(
				$this,
				'_my_wp_enqueue_scripts'
			) );
			add_action( 'admin_enqueue_scripts', array(
				$this,
				'_my_wp_enqueue_scripts'
			) );
			add_action( 'login_enqueue_scripts', array(
				$this,
				'_my_wp_enqueue_scripts'
			) );
			add_action( 'wp_footer', array(
				$this,
				'_my_wp_enqueue_scripts'
			) );
			add_action( 'admin_footer', array(
				$this,
				'_my_wp_enqueue_scripts'
			) );
		}


		/**
		 * Поставить в очередь файл JS
		 * @version 1.3
		 * @param       $file
		 * @param array $afterJS
		 * @param bool  $in_footer
		 * @return bool
		 */
		public function enqueue( $file, $afterJS = array(), $in_footer = false ){
			if( strpos( $file, '/' ) === 0 ){
				$backtrace = debug_backtrace();
				if( strpos( $file, hiweb()->path()->base_dir() ) !== 0 ){
					$sourceDir = dirname( $backtrace[1]['file'] );
					$file = $sourceDir . $file;
				}
			}
			$url = hiweb()->path()->path_to_url( $file );
			$file = hiweb()->path()->url_to_path( $file );
			if( ( $url == $file ) || ( file_exists( $file ) && is_file( $file ) && is_readable( $file ) && $url != '' ) ){
				$id = md5( $url );
				$this->files[ $id ] = array(
					$url,
					$afterJS,
					$in_footer,
					$file
				);
				return $id;
			} else {
				hiweb()->console()->error( 'файл [' . $file . '] не найден!', 2 );
				return false;
			}
		}


		function _my_wp_enqueue_scripts(){
			foreach( $this->files as $slug => $script ){
				unset( $this->files[ $slug ] );
				wp_register_script( $slug, $script[0], $script[1], $script[0] == $script[3] ? 0 : filemtime( $script[3] ), $script[2] );
				wp_enqueue_script( $slug );
			}
		}


	}