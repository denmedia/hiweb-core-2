<?php
	/**
	 * Created by PhpStorm.
	 * User: d9251
	 * Date: 31.08.2016
	 * Time: 17:36
	 */


	include_once 'wp/post.php';
	include_once 'wp/taxonomy.php';
	include_once 'wp/theme.php';
	include_once 'wp/user.php';


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
		 * Возвращает корневой класс для работы с данными пользователя
		 * @param $idOrLoginOrEmail - если не указывать, то будет взят текущий авторизированный пользователь
		 * @return hw_wp_user
		 */
		public function user( $idOrLoginOrEmail = null ){
			if( is_null( $idOrLoginOrEmail ) ){
				require_once ABSPATH . '/wp-includes/pluggable.php';
				require_once ABSPATH . '/wp-includes/pluggable.php';
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