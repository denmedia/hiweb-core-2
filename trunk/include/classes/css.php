<?php


	/**
	 * Class hw_css
	 * @version 1.1
	 */
	class hw_css{

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
		 * Поставить в очередь файл CSS
		 * @version 1.2
		 * @param $file
		 * @return bool
		 */
		public function enqueue( $file ){
			if( strpos( $file, '/' ) === 0 ){
				$backtrace = debug_backtrace();
				if( strpos( $file, hiweb()->path()->base_dir() ) !== 0 ){
					$sourceDir = dirname( $backtrace[1]['file'] );
					$file = $sourceDir . $file;
				}
			}
			$url = hiweb()->path()->path_to_url( $file );
			$file = hiweb()->path()->url_to_path( $file );
			if( file_exists( $file ) && is_file( $file ) && is_readable( $file ) && $url != '' ){
				$this->files[ md5( $url ) ] = array(
					$url,
					$file
				);

				return true;
			} else {
				hiweb()->console()->error( sprintf( __( 'File [%s] not found or not readable' ), $file ), 2 );

				return false;
			}
		}


		function _my_wp_enqueue_scripts(){
			foreach( $this->files as $slug => $path ){
				unset( $this->files[ $slug ] );
				wp_register_style( $slug, $path[0], array(), filemtime( $path[1] ) );
				wp_enqueue_style( $slug );
			}
		}


	}