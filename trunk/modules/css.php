<?php

	/**
	 * Created by PhpStorm.
	 * User: hiweb
	 * Date: 30.06.2016
	 * Time: 22:16
	 */
	class hw_css {

		private $files = array();

		public function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, '_my_wp_enqueue_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, '_my_wp_enqueue_scripts' ) );
			add_action( 'login_enqueue_scripts', array( $this, '_my_wp_enqueue_scripts' ) );
			add_action( 'wp_footer', array( $this, '_my_wp_enqueue_scripts' ) );
			add_action( 'admin_footer', array( $this, '_my_wp_enqueue_scripts' ) );
		}

		/**
		 * Поставить в очередь файл CSS
		 * @version 1.2
		 *
		 * @param $file
		 *
		 * @return bool
		 */
		public function enqueue( $file ) {
			$url = '';
			if ( strpos( $file, '/' ) === 0 ) {
				$backtrace = debug_backtrace();
				if ( strpos( $file, hiweb()->path()->base_dir() ) !== 0 ) {
					$sourceDir = dirname( $backtrace[1]['file'] );
					$file      = $sourceDir . $file;
				}
			}
			$url = hiweb()->path()->url_to_path( $file );
			if ( $url != '' ) {
				$this->files[ md5( $url ) ] = $url;

				return true;
			} else {
				hiweb()->console()->error( 'hiweb()→css(): файл [' . $file . '] не найден!', true );

				return false;
			}
		}

		function _my_wp_enqueue_scripts() {
			foreach ( $this->files as $slug => $url ) {
				unset( $this->files[ $slug ] );
				wp_register_style( $slug, $url );
				wp_enqueue_style( $slug );
			}
		}


	}