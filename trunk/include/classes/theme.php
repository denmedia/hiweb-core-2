<?php


	class hw_theme{

		/** @var  string */
		private $theme;

		/** @var hw_wp_location[] */
		private $locations = array();


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

	}


	class hw_wp_location{

		private $location;


		public function __construct( $location ){
			$this->location = $location;
		}

	}