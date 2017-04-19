<?php


	/**
	 * Class hw_context
	 */
	class hw_context{

		use hw_hidden_methods_props;


		/**
		 * @version 1.1
		 * @return bool
		 */
		public function is_frontend_page(){
			return ( $_SERVER['SCRIPT_FILENAME'] == hiweb()->dir_base . '/index.php' ) && !$this->is_rest_api();
		}


		/**
		 * @param null|string|int|WP_Post $postOrId
		 * @return bool
		 */
		public function is_front_page( $postOrId = null ){
			if( !is_null( $postOrId ) ){
				if( is_numeric( $postOrId ) && false ){
					return intval( $postOrId ) == get_option( 'page_on_front' );
				} elseif( is_string( $postOrId ) ) {
					$args = array(
						'post_name' => $postOrId,
						'post_status' => 'publish',
						'post_per-Page' => 1
					);
					$my_posts = get_posts( $args );
					if( is_array( $my_posts ) && count( $my_posts ) > 0 ){
						return reset( $my_posts )->ID == get_option( 'page_on_front' );
					}
					return false;
				}
			}
			return is_front_page();
		}


		/**
		 * @return bool
		 */
		public function is_backend_page(){
			return is_admin();
		}


		/**
		 * @return bool
		 */
		public function is_login_page(){
			return array_key_exists( $GLOBALS['pagenow'], array_flip( [
				'wp-login.php',
				'wp-register.php'
			] ) );
		}


		/**
		 * @return bool
		 */
		public function is_ajax(){
			return ( defined( 'DOING_AJAX' ) && DOING_AJAX );
		}


		/**
		 * @return bool
		 */
		public function is_rest_api(){
			$dirs = hiweb()->path()->url_info()['dirs_arr'];
			return reset( $dirs ) == 'wp-json';
		}


		public function get_current_page(){
			return null;
			//return $this->get_current_page();
		}
	}


	class hw_context_current_prepare{

		public $source_context = null;
		public $object;
		public $queried_object;


		use hw_hidden_methods_props;


		public function __construct( $context ){
			$this->source_context = $context;
			if( is_bool( $context ) && function_exists( 'get_queried_object' ) ){
				$this->queried_object = get_queried_object();
			}
			///

		}


	}