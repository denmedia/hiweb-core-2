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

		private $_sections = array();


		/**
		 * Возвращает объект для работы со страницей опций
		 * @param string $slug
		 * @return hw_admin_menu_page
		 */
		public function give_page( $slug ){
			$slug_sanitize = sanitize_file_name( strtolower( $slug ) );
			if( !array_key_exists( $slug_sanitize, $this->_admin_menu_pages ) ){
				$this->_admin_menu_pages[ $slug_sanitize ] = new hw_admin_menu_page( $slug );
				$this->pages[ $slug_sanitize ] = $this->_admin_menu_pages[ $slug_sanitize ];
			}
			return $this->_admin_menu_pages[ $slug_sanitize ];
		}


		/**
		 * Возвращает объект для работы со страницей опций
		 * @param      $slug
		 * @param null $parentSlug
		 * @return hw_admin_submenu_page
		 */
		public function give_subpage( $slug, $parentSlug = null ){
			$slug_sanitize = sanitize_file_name( strtolower( $slug ) );
			if( !array_key_exists( $slug_sanitize, $this->_admin_submenu_pages ) ){
				$this->_admin_submenu_pages[ $slug_sanitize ] = new hw_admin_submenu_page( $slug, $parentSlug );
				$this->pages[ $slug_sanitize ] = $this->_admin_submenu_pages[ $slug_sanitize ];
			}
			return $this->_admin_submenu_pages[ $slug_sanitize ];
		}


		/**
		 * Возвращает объект для работы со страницей опций
		 * @param $slug
		 * @return hw_admin_options_page
		 */
		public function give_options_page( $slug ){
			$slug_sanitize = sanitize_file_name( strtolower( $slug ) );
			if( !array_key_exists( $slug_sanitize, $this->_admin_option_pages ) ){
				$this->_admin_option_pages[ $slug_sanitize ] = new hw_admin_options_page( $slug );
				$this->pages[ $slug_sanitize ] = $this->_admin_option_pages[ $slug_sanitize ];
			}
			return $this->_admin_option_pages[ $slug_sanitize ];
		}


		/**
		 * Возвращает объект для работы со страницей опций
		 * @param $slug
		 * @return hw_admin_theme_page
		 */
		public function give_theme_page( $slug ){
			$slug_sanitize = sanitize_file_name( strtolower( $slug ) );
			if( !array_key_exists( $slug_sanitize, $this->_admin_theme_pages ) ){
				$this->_admin_theme_pages[ $slug_sanitize ] = new hw_admin_theme_page( $slug );
				$this->pages[ $slug_sanitize ] = $this->_admin_theme_pages[ $slug_sanitize ];
			}
			return $this->_admin_theme_pages[ $slug_sanitize ];
		}


		/**
		 * @param $menu_slug
		 * @return bool|hw_admin_menu_abstract
		 */
		public function get( $menu_slug ){
			$menu_slug = sanitize_file_name( strtolower( $menu_slug ) );
			if( array_key_exists( $menu_slug, $this->pages ) ){
				return $this->pages[ $menu_slug ];
			}
			return new hw_admin_menu_abstract();
		}


		/**
		 * @param             $section_slug
		 * @param string      $options_slug
		 * @param null|string $section_title
		 * @return hw_admin_menu_section
		 */
		public function give_options( $options_slug = 'options-general.php', $section_slug = '', $section_title = null ){
			$section_slug_sanitize = sanitize_file_name( strtolower( $section_slug ) );
			if( !array_key_exists( $section_slug_sanitize, $this->_sections ) ){
				$section = new hw_admin_menu_section( $section_slug, $options_slug );
				$section->title( $section_title );
				$this->_sections[ $section_slug_sanitize ] = $section;
			}
			return $this->_sections[ $section_slug_sanitize ];
		}


		/**
		 * Return Pages
		 * @param bool $pages
		 * @param bool $sections
		 * @param bool $subpages
		 * @param bool $options
		 * @param bool $themes
		 * @return hw_admin_menu_abstract[]|hw_admin_menu_section[]
		 */
		public function get_pages( $pages = true, $sections = true, $subpages = true, $options = true, $themes = true ){
			$R = array();
			if( $pages )
				$R = array_merge( $R, $this->_admin_menu_pages );
			if( $subpages )
				$R = array_merge( $R, $this->_admin_submenu_pages );
			if( $options )
				$R = array_merge( $R, $this->_admin_option_pages );
			if( $themes )
				$R = array_merge( $R, $this->_admin_theme_pages );
			if( $sections )
				$R = array_merge( $R, $this->_sections );
			return $R;
		}

	}


	class hw_admin_menu_abstract{

		protected $page_title;
		protected $menu_title;
		protected $capability = 'administrator';
		protected $menu_slug = '';
		protected $function_echo;
		/** @var hw_field[] */
		protected $fields = array();

		protected $update_success_message = '';

		protected $use_title_form = true;

		use hw_hidden_methods_props;


		public function __construct( $slug = null, $additionData = null ){
			$this->update_success_message = __('Options "<b>%s</b>" success updated!');
			if( !is_null( $slug ) && trim( $slug ) != '' ){
				$slug_sanitize = sanitize_file_name( strtolower( $slug ) );
				$this->menu_slug = $slug_sanitize;
				$this->menu_title = $slug;
				$this->page_title = $slug;
			}
			$this->init( $additionData );
			add_action( 'admin_menu', array(
				$this, 'add_action_admin_menu'
			) );
		}


		protected function init( $additionData ){
		}


		/**
		 * Add field to options page
		 * @param $field
		 */
		public function add_field( hw_field $field ){
			$this->fields[ $field->get_id() ] = $field;
		}


		/**
		 * Return all fields from options page
		 * @return hw_field[]
		 */
		public function get_fields(){
			return $this->fields;
		}


		/**
		 * Remove field by ID
		 * @param $fieldId
		 * @return bool
		 */
		public function remove_field( $fieldId ){
			$R = isset( $this->fields[ $fieldId ] );
			unset( $this->fields[ $fieldId ] );
			return $R;
		}


		/**
		 *
		 */
		protected function add_action_admin_menu(){
		}


		/**
		 * Использовать титл и форму
		 * @param null $set
		 * @return hw_admin_menu_abstract|mixed
		 */
		public function use_title_form( $set = null ){
			if( !is_null( $set ) ){
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
		 * Выводит страницу опций
		 */
		protected function the_page(){
			///
			do_action( 'hw_admin_menu_page_' . $this->menu_slug . '_before', $this );
			///CONTENT
			$content = '';
			ob_start();
			///
			if(isset($_REQUEST['settings-updated']) && $_REQUEST['settings-updated']) {
				echo '<div class="notice notice-success"><p>'.sprintf(__($this->update_success_message, 'hw-core-2'), $this->page_title).'</p></div>';
			}
			///
			$page_is_empty = true;
			if( is_callable( $this->function_echo ) ){
				call_user_func( $this->function_echo );
				$page_is_empty = false;
			} elseif( is_string( $this->function_echo ) ) {
				echo $this->function_echo;
				$page_is_empty = false;
			}
			///Field
			do_action( 'hw_admin_menu_page', $this );
			///hook content
			if( has_action( 'hw_admin_menu_page_' . $this->menu_slug ) ){
				do_action( 'hw_admin_menu_page_' . $this->menu_slug, $this );
				$page_is_empty = false;
			}
			///Fields to options
			if( is_array( $this->fields ) ){
				$field_ids = array();
				foreach( $this->fields as $field ){
					register_setting( 'hw_options_group_' . $this->menu_slug, $field->input()->name );
					$field_ids[] = $field->input()->name;
				}
				echo '<input type="hidden" name="action" value="update" /><input type="hidden" name="page_options" value="' . implode( ',', $field_ids ) . '" />';
			}
			///
			$content = ob_get_clean();
			///
			//Wrap + Title
			if( $this->use_title_form )
			if( !has_filter( 'hw_admin_menu_page_' . $this->menu_slug . '_opening' ) || apply_filters( 'hw_admin_menu_page_' . $this->menu_slug . '_opening', $this ) ){
				echo '<div class="wrap"><h2>' . $this->page_title . '</h2>';
			}
			if( $content != '' ){
				//Form
				if( $this->use_title_form )
				if( !has_filter( 'hw_admin_menu_page_' . $this->menu_slug . '_form' ) || apply_filters( 'hw_admin_menu_page_' . $this->menu_slug . '_form', $this ) ){
					echo '<form method="post" action="options.php">';
					wp_nonce_field( 'update-options' );
				}
			}
			////
			echo $content;
			///
			if( $content == '' ){
				include hiweb()->dir_views . '/admin-menu-empty-page.php';
			}
			///
			if( $content != '' ){
				//Form Close
				if( $this->use_title_form )
				if( !has_filter( 'hw_admin_menu_page_' . $this->menu_slug . '_form' ) || apply_filters( 'hw_admin_menu_page_' . $this->menu_slug . '_form', $this ) ){
					submit_button();
					echo '</form>';
				}
			}
			//Wrap Close
			if( !has_filter( 'hw_admin_menu_page_' . $this->menu_slug ) || apply_filters( 'hw_admin_menu_page_' . $this->menu_slug, $this ) ){
				echo '</div>';
			}
			///
			do_action( 'hw_admin_menu_page_' . $this->menu_slug . '_after', $this );
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
			} else
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


	class hw_admin_menu_section{

		private $id = '';
		private $title;
		private $parent_slug;
		private $parent_slug_short;
		private $inputs = array();
		///
		private $pattern_slug = '/options-(.*)(\.php)$/';


		public function __construct( $id, $parent_slug = 'options-general.php' ){
			$this->id = sanitize_file_name( strtolower( $id ) );
			if( trim( $this->id ) == '' )
				$this->id = 'hw_admin_menu_sections_' . $parent_slug;
			if( preg_match( $this->pattern_slug, $parent_slug, $math ) > 0 ){
				$this->parent_slug = $parent_slug;
				$this->parent_slug_short = $math[1];
				add_action( 'admin_init', array(
					$this, 'add_settings_section'
				) );
				/*add_action( 'admin_init', array(
					$this,
					'register_setting'
				) );*/
			}
		}


		public function __call( $name, $arguments ){
			switch( $name ){
				/*case 'register_setting':
					foreach( $this->get_fields() as $input ){
						if( $input instanceof hw_input ){
							register_setting( $this->id, $input->name() );
						}
					}
					break;*/
				case 'add_settings_section':
					add_settings_section( $this->id, $this->title, array(
						$this, 'the_fields'
					), $this->parent_slug_short ); //todo!!!
					break;
				case 'the_fields':
					$this->the_fields();
					break;
			}
		}


		/**
		 * @param null|string $set
		 * @return string|null|hw_admin_menu_section
		 */
		public function title( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}

	}