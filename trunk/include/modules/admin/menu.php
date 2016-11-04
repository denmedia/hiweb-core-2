<?php
	
	
	class hw_admin_menu{
		
		private $pages = array();
		
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
		public function give_page( $slug ){
			$slug_sanitize = sanitize_file_name(strtolower($slug));
			if( !array_key_exists( $slug_sanitize, $this->_admin_menu_pages ) ){
				$this->_admin_menu_pages[ $slug_sanitize ] = new hw_admin_menu_page( $slug );
				$this->pages[$slug_sanitize] = $this->_admin_menu_pages[ $slug_sanitize ];
			}
			return $this->_admin_menu_pages[ $slug_sanitize ];
		}
		
		
		/**
		 * Возвращает объект для работы со страницей опций
		 * @param $slug
		 * @param null $parentSlug
		 * @return hw_admin_submenu_page
		 */
		public function give_subpage( $slug, $parentSlug = null ){
			$slug_sanitize = sanitize_file_name(strtolower($slug));
			if( !array_key_exists( $slug_sanitize, $this->_admin_submenu_pages ) ){
				$this->_admin_submenu_pages[ $slug_sanitize ] = new hw_admin_submenu_page( $slug, $parentSlug );
				$this->pages[$slug_sanitize] = $this->_admin_submenu_pages[ $slug_sanitize ];
			}
			return $this->_admin_submenu_pages[ $slug_sanitize ];
		}
		
		
		/**
		 * Возвращает объект для работы со страницей опций
		 * @param $slug
		 * @return hw_admin_options_page
		 */
		public function give_options_page( $slug ){
			$slug_sanitize = sanitize_file_name(strtolower($slug));
			if( !array_key_exists( $slug_sanitize, $this->_admin_option_pages ) ){
				$this->_admin_option_pages[ $slug_sanitize ] = new hw_admin_options_page( $slug );
				$this->pages[$slug_sanitize] = $this->_admin_option_pages[ $slug_sanitize ];
			}
			return $this->_admin_option_pages[ $slug_sanitize ];
		}
		
		
		/**
		 * Возвращает объект для работы со страницей опций
		 * @param $slug
		 * @return hw_admin_theme_page
		 */
		public function give_theme_page( $slug ){
			$slug_sanitize = sanitize_file_name(strtolower($slug));
			if( !array_key_exists( $slug_sanitize, $this->_admin_theme_pages ) ){
				$this->_admin_theme_pages[ $slug_sanitize ] = new hw_admin_theme_page( $slug );
				$this->pages[$slug_sanitize] = $this->_admin_theme_pages[ $slug_sanitize ];
			}
			return $this->_admin_theme_pages[ $slug_sanitize ];
		}
		
		
		/**
		 * @param $menu_slug
		 * @return bool|hw_admin_menu_abstract
		 */
		public function get($menu_slug){
			$menu_slug = sanitize_file_name(strtolower($menu_slug));
			if(array_key_exists($menu_slug,$this->pages)){
				return $this->pages[$menu_slug];
			}
			return new hw_admin_menu_abstract();
		}
		
	}
	
	
	class hw_admin_menu_abstract{
		
		protected $page_title;
		protected $menu_title;
		protected $capability = 'administrator';
		protected $menu_slug;
		protected $function_echo;
		///
		/** @var  hw_input[] */
		protected $inputs;
		protected $pattern_slug = '/(.)*(\.php)^/';
		protected $inputs_prepend;
		
		
		public function __construct( $slug = null, $additionData = null ){
			if( !is_null( $slug ) && trim( $slug ) != '' ){
				$this->menu_slug = sanitize_file_name(strtolower($slug));
				$this->menu_title = $slug;
				$this->page_title = $slug;
				$this->inputs_prepend = $this->menu_slug.'-';
			}
			$this->init( $additionData );
			$this->hooks();
		}
		
		
		protected function hooks(){
			add_action( 'admin_init', array( $this, 'register_setting' ) );
			if( preg_match( $this->pattern_slug, $this->menu_slug ) > 0 ){
				hiweb()->console( $this->menu_slug );
			}else{
				add_action( 'admin_menu', array( $this, 'add_action_admin_menu' ) );
			}
		}
		
		
		protected function init( $additionData ){
		}
		
		
		public function __call( $name, $arguments ){
			switch( $name ){
				case 'add_action_admin_menu':
					$this->add_action_admin_menu();
					break;
				case 'the_page':
					$this->the_page();
					break;
				case 'register_setting':
					foreach($this->inputs as $input){
						if($input instanceof hw_input){
							register_setting( $this->menu_slug(), $input->id() );
						}
					}
					break;
			}
		}
		
		
		/**
		 *
		 */
		protected function add_action_admin_menu(){
		}
		
		
		/**
		 * @param null $set
		 * @return hw_admin_menu_abstract|mixed
		 */
		public function inputs_prepend($set = null){
			if(!is_null($set)){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}
		
		/**
		 * Возвращает / устанавливает значение
		 * @param string|null $set
		 * @return null|string|hw_admin_menu|hw_admin_menu_abstract|hw_admin_menu_page
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
		 * @return null|string|hw_admin_menu|hw_admin_menu_abstract|hw_admin_menu_page
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
		 * @return null|string|hw_admin_menu|hw_admin_menu_abstract|hw_admin_menu_page
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
		 * @return null|string|hw_admin_menu|hw_admin_menu_abstract|hw_admin_menu_page
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
		 * @return null|string|hw_admin_menu|hw_admin_menu_abstract|hw_admin_menu_page
		 */
		public function function_echo( $set = null ){
			if( !is_null( $set ) ){
				$this->function_echo = $set;
				return $this;
			}
			return $this->function_echo;
		}
		
		
		/**
		 * @param $id
		 * @param string $type
		 * @param null $title
		 * @param null $default_value
		 * @return hw_input
		 */
		public function add_field( $id, $type = 'text', $title = null, $default_value = null ){
			$input = hiweb()->input( $this->inputs_prepend.$id, $type ); //todo
			$input->title( is_null($title) ? $id : $title );
			$input->default_value( $default_value );
			$input->value( get_option($input->id(), $default_value) );
			$this->inputs[ $this->inputs_prepend.$id ] = $input;
			return $this->inputs[ $this->inputs_prepend.$id ];
		}
		
		
		/**
		 * @param $fieldId
		 * @return bool
		 */
		public function field_exists($fieldId){
			return array_key_exists($this->inputs_prepend().$fieldId, $this->inputs);
		}
		
		
		/**
		 * @param $fieldId
		 * @return hw_input|hw_input_checkbox|hw_input_repeat|hw_input_text
		 */
		public function get_field($fieldId){
			if($this->field_exists($fieldId)){
				$inp = $this->inputs[$this->inputs_prepend().$fieldId];
				return $inp;
			} else {
				return hiweb()->input($fieldId);
			}
		}
		
		
		public function the_page(){
			if( is_callable( $this->function_echo ) ){
				call_user_func( $this->function_echo );
			}elseif( is_string( $this->function_echo ) ){
				echo $this->function_echo;
			}elseif(is_array($this->inputs) && count($this->inputs) > 0){
				?><div class="wrap"><?php do_action('admin_notices') ?><h1><?php echo $this->page_title ?></h1><?php
				hiweb()->forms()->give($this->menu_slug)->template('options')->settings_group($this->menu_slug)->fields($this->inputs)->action('options.php')->the();
				?></div><?php
			}else{
				echo '<div class="wrap"><h1>This is empty options page</h1></div>';
			}
		}
		
		
	}
	
	
	class hw_admin_menu_page extends hw_admin_menu_abstract{
		
		private $icon_url;
		private $position;
		
		
		protected function add_action_admin_menu(){
			add_menu_page( $this->page_title, $this->menu_title, $this->capability, $this->menu_slug, array(
				$this, 'the_page'
			), $this->icon_url, $this->position );
		}
		
		
		/**
		 * Возвращает / устанавливает значение
		 * @param string $set
		 * @return null|string|hw_admin_menu|hw_admin_menu_page
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
		
		
		protected function init( $additionData ){
			if( $additionData instanceof hw_admin_menu_abstract ){
				$this->parent_slug = $additionData->menu_slug();
			}else
				$this->parent_slug = $additionData;
		}
		
		
		protected function add_action_admin_menu(){
			add_submenu_page( $this->parent_slug, $this->page_title, $this->menu_title, $this->capability, $this->menu_slug, array(
				$this, 'the_page'
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
				$this, 'the_page'
			) );
		}
		
	}
	
	
	class hw_admin_theme_page extends hw_admin_menu_abstract{
		
		protected function add_action_admin_menu(){
			add_theme_page( $this->page_title, $this->menu_title, $this->capability, $this->menu_slug, array(
				$this, 'the_page'
			) );
		}
	}