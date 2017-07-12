<?php


	class hw_theme{

		/** @var  string */
		private $theme;

		/** @var hw_wp_location[] */
		private $locations = [];


		public function __construct( $theme ){
			$this->theme = trim( $theme ) == '' ? get_option( 'stylesheet' ) : $theme;
		}


		public function exist(){
			//todo
		}


		/**
		 * Возвращает массив локаций
		 * @return array
		 */
		public function locations(){
			$R = [];
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
			$R = [];
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


		/**
		 * @return string
		 */
		public function dir(){
			return dirname( get_template_directory() ) . '/' . $this->theme;
		}


		/**
		 * @return string
		 */
		public function url(){
			return dirname( get_template_directory_uri() ) . '/' . $this->theme;
		}


		/**
		 * Return post (or posts array) by template file name, like 'page-template.php', or FALSE, if them not exists
		 * @param string $template_name
		 * @param bool $return_array
		 * @return bool|WP_Post|WP_Post[]
		 */
		public function get_post_by_template( $template_name = 'page-template.php', $return_array = false ){
			$args = [
				'post_type' => 'page',
				'nopaging' => true,
				'meta_key' => '_wp_page_template',
				'meta_value' => $template_name
			];
			$pages = get_posts( $args );
			if( !is_array( $pages ) || count( $pages ) == 0 ) return false;
			return $return_array ? $pages : reset( $pages );
		}

	}


	class hw_wp_location{

		public $name = '';
		public $id = 0;
		public $slug = '';
		public $menus = [];
		private $location;


		public function __construct( $location ){
			$this->location = $location;
			$locations = get_registered_nav_menus();
			$menus = wp_get_nav_menus();
			if( array_key_exists( $location, $locations ) ){
				$this->slug = $location;
				$this->name = $locations[ $location ];
				$location_ids = hiweb()->theme()->locations();
				foreach( $menus as $menu ){
					if( $menu->term_id == $location_ids[ $location ] ){
						$this->menus[] = $menu;
					}
				}
			}
		}

	}