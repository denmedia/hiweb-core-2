<?php


	/**
	 * Class hw_wp_admin_page
	 */
	abstract class hw_wp_admin_menu{

		protected $page_title;
		protected $menu_title;
		protected $capability = 'administrator';
		protected $menu_slug;
		protected $function;
		protected $function_echo;


		public function __construct( $slug = null, $additionData = null ){
			if( !is_null( $slug ) && trim( $slug ) != '' ){
				$this->menu_slug = $slug;
				$this->menu_title = $slug;
				$this->page_title = $slug;
			}
			add_action( 'admin_menu', array( $this, 'add_action_admin_menu' ) );
			$this->__construct2( $additionData );
		}


		protected function __construct2( $additionData ){
		}


		public function __call( $name, $arguments ){
			switch( $name ){
				case 'add_action_admin_menu':
					$this->add_action_admin_menu();
					break;
				case 'echo_page':
					$this->echo_page();
					break;
			}
		}


		protected function add_action_admin_menu(){
		}


		protected function echo_page(){
			if( is_callable( $this->function_echo ) ){
				call_user_func( $this->function_echo );
			}else{
				echo 'No function ECHO exists...';
			}
		}


		/**
		 * Возвращает / устанавливает значение
		 * @param string|null $set
		 * @return null|string|$this
		 */
		public function page_title( $set = null ){
			if( !is_null( $set ) ){
				$this->page_title = $set;
				return $this;
			}
			return $this->page_title;
		}


		/**
		 * Возвращает / устанавливает значение
		 * @param null|string $set
		 * @return null|string|$this
		 */
		public function menu_title( $set = null ){
			if( !is_null( $set ) ){
				$this->menu_title = $set;
				return $this;
			}
			return $this->menu_title;
		}


		/**
		 * Возвращает / устанавливает значение
		 * @param array|string|int|null $set
		 * @return null|string|$this
		 */
		public function capability( $set = null ){
			if( !is_null( $set ) ){
				$this->capability = $set;
				return $this;
			}
			return $this->capability;
		}


		/**
		 * Возвращает / устанавливает значение
		 * @param string $set
		 * @return null|string|$this
		 */
		public function menu_slug( $set = null ){
			if( !is_null( $set ) ){
				$this->menu_slug = $set;
				return $this;
			}
			return $this->menu_slug;
		}


		/**
		 * Возвращает / устанавливает функцию
		 * @param callable $set
		 * @return callable|$this|null
		 */
		public function function_call( $set = null ){
			if( !is_null( $set ) ){
				$this->function = $set;
				return $this;
			}
			return $this->function;
		}


		/**
		 * Возвращает / устанавливает функцию
		 * @param callable $set
		 * @return callable|$this|null
		 */
		public function function_echo( $set = null ){
			if( !is_null( $set ) ){
				$this->function_echo = $set;
				return $this;
			}
			return $this->function_echo;
		}


	}


	/**
	 * Class hw_wp_admin_menu_page
	 */
	class hw_wp_admin_menu_page extends hw_wp_admin_menu{

		private $icon_url;
		private $position;


		protected function add_action_admin_menu(){
			add_menu_page( $this->page_title, $this->menu_title, $this->capability, $this->menu_slug, array(
				$this, 'echo_page'
			), $this->icon_url, $this->position );
		}


		/**
		 * Возвращает / устанавливает значение
		 * @param string $set
		 * @return null|string|$this
		 */
		public function icon_url( $set = null ){
			if( !is_null( $set ) ){
				$this->icon_url = $set;
				return $this;
			}
			return $this->icon_url;
		}


		/**
		 * Возвращает / устанавливает значение
		 * @param string $set
		 * @return null|string|$this
		 */
		public function position( $set = null ){
			if( !is_null( $set ) ){
				$this->position = $set;
				return $this;
			}
			return $this->position;
		}
	}


	/**
	 * Class hw_wp_submenu_page
	 */
	class hw_wp_admin_submenu_page extends hw_wp_admin_menu{

		private $parent_slug;


		protected function __construct2( $additionData ){
			if( $additionData instanceof hw_wp_admin_menu ){
				$this->parent_slug = $additionData->menu_slug();
			}else
				$this->parent_slug = $additionData;
		}


		protected function add_action_admin_menu(){
			add_submenu_page( $this->parent_slug, $this->page_title, $this->menu_title, $this->capability, $this->menu_slug, array(
				$this, 'echo_page'
			) );
		}


		/**
		 * Возвращает / устанавливает значение
		 * @param string $set
		 * @return null|string|$this
		 */
		public function parent_slug( $set = null ){
			if( !is_null( $set ) ){
				$this->parent_slug = $set;
				return $this;
			}
			return $this->parent_slug;
		}
	}


	/**
	 * Class hw_wp_admin_page_options
	 */
	class hw_wp_admin_options_page extends hw_wp_admin_menu{

		protected function add_action_admin_menu(){
			add_options_page( $this->page_title, $this->menu_title, $this->capability, $this->menu_slug, array(
				$this, 'echo_page'
			) );
		}

	}


	/**
	 * Class hw_wp_admin_page_theme
	 */
	class hw_wp_admin_theme_page extends hw_wp_admin_menu{

		protected function add_action_admin_menu(){
			add_theme_page( $this->page_title, $this->menu_title, $this->capability, $this->menu_slug, array(
				$this, 'echo_page'
			) );
		}
	}