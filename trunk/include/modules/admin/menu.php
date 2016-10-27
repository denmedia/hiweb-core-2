<?php


	class hw_admin_menu{

		/** @var hw_admin_menu_page[] */
		private $_admin_menu_pages = array();
		/** @var hw_admin_submenu_page[] */
		private $_admin_submenu_pages = array();
		/** @var hw_admin_options_page[] */
		private $_admin_option_pages = array();
		/** @var hw_admin_theme_page[] */
		private $_admin_theme_pages = array();


		/**
		 * Возвращает объект для работы со страницей опций
		 * @param $slug
		 * @return hw_admin_menu_page
		 */
		public function add_page( $slug ){
			if( !array_key_exists( $slug, $this->_admin_menu_pages ) ){
				$this->_admin_menu_pages[ $slug ] = new hw_admin_menu_page( $slug );
			}
			return $this->_admin_menu_pages[ $slug ];
		}


		/**
		 * Возвращает объект для работы со страницей опций
		 * @param $slug
		 * @param null $parentSlug
		 * @return hw_admin_submenu_page
		 */
		public function add_sub_page( $slug, $parentSlug = null ){
			if( !array_key_exists( $slug, $this->_admin_submenu_pages ) ){
				$this->_admin_submenu_pages[ $slug ] = new hw_admin_submenu_page( $slug, $parentSlug );
			}
			return $this->_admin_submenu_pages[ $slug ];
		}


		/**
		 * Возвращает объект для работы со страницей опций
		 * @param $slug
		 * @return hw_admin_options_page
		 */
		public function add_options_page( $slug ){
			if( !array_key_exists( $slug, $this->_admin_option_pages ) ){
				$this->_admin_option_pages[ $slug ] = new hw_admin_options_page( $slug );
			}
			return $this->_admin_option_pages[ $slug ];
		}


		/**
		 * Возвращает объект для работы со страницей опций
		 * @param $slug
		 * @return hw_admin_theme_page
		 */
		public function add_theme_page( $slug ){
			if( !array_key_exists( $slug, $this->_admin_theme_pages ) ){
				$this->_admin_theme_pages[ $slug ] = new hw_admin_theme_page( $slug );
			}
			return $this->_admin_theme_pages[ $slug ];
		}

	}


	abstract class hw_admin_menu_abstract{

		protected $page_title;
		protected $menu_title;
		protected $capability = 'administrator';
		protected $menu_slug;
		protected $function_echo;
		/** @var  hw_options_page[] */
		protected $options_pages;


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
			}elseif( is_string( $this->function_echo ) ){
				echo $this->function_echo;
			}else{
				//echo 'No function ECHO exists...';
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
		public function function_echo( $set = null ){
			if( !is_null( $set ) ){
				$this->function_echo = $set;
				return $this;
			}
			return $this->function_echo;
		}


		public function add_option( $id, $type = 'text', $title = null, $default_value = null ){
			$option = hiweb()->options()->get( $id );
		}


	}


	class hw_admin_menu_page extends hw_admin_menu_abstract{

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


	class hw_admin_submenu_page extends hw_admin_menu_abstract{

		private $parent_slug;


		protected function __construct2( $additionData ){
			if( $additionData instanceof hw_admin_menu_abstract ){
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


	class hw_admin_options_page extends hw_admin_menu_abstract{

		protected function add_action_admin_menu(){
			add_options_page( $this->page_title, $this->menu_title, $this->capability, $this->menu_slug, array(
				$this, 'echo_page'
			) );
		}

	}


	class hw_admin_theme_page extends hw_admin_menu_abstract{

		protected function add_action_admin_menu(){
			add_theme_page( $this->page_title, $this->menu_title, $this->capability, $this->menu_slug, array(
				$this, 'echo_page'
			) );
		}
	}