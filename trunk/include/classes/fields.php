<?php


	class hw_fields{


		use hw_hidden_methods_props;


		private $dir = 'fields';

		public $input_prefix = '';

		/** @var hw_fields_hook[] */
		private $hooks = array();

		/** @var hw_field[] */
		private $fields = array();

		private $fieldId_globalId = array();
		private $globalId_fieldId = array();

		/**  */
		public $hook_fields = array();

		private $hook_template = array(
			'edit_form_top' => 'default', 'edit_form_before_permalink' => 'postbox', 'edit_form_after_title' => 'postbox', 'edit_form_after_editor' => 'postbox', 'submitpage_box' => 'postbox', 'submitpost_box' => 'postbox', 'edit_page_form' => 'postbox', 'edit_form_advanced' => 'postbox', '_add_form_fields' => 'add-term', '_edit_form' => 'term', 'options' => 'default'
		);


		public function __construct(){
			$this->dir = hiweb()->dir_classes . '/' . $this->dir;
			///
			add_action( 'current_screen', array(
				$this, 'do_distribute'
			), 999999999999 );
			///
			add_action( 'save_post', array(
				$this, 'save_post'
			) );
			///
			add_action( 'personal_options_update', array(
				$this, 'user_options_update'
			) );
			add_action( 'edit_user_profile_update', array(
				$this, 'user_options_update'
			) );
			///
			add_action( 'init', function(){
				if( function_exists( 'get_taxonomies' ) ){
					foreach( get_taxonomies() as $taxonomy ){
						//Save Term
						add_action( 'create_term', array(
							$this, 'save_taxonomy'
						), 99 );
						add_action( 'edited_' . $taxonomy, array(
							$this, 'save_taxonomy'
						), 99 );
					}
				}
			} );
		}


		/**
		 * @param string $hook
		 * @return mixed|string
		 */
		public function get_form_template_from_hook( $hook = '' ){
			$R = 'default';
			static $success = array();
			if( isset( $success[ $hook ] ) ){
				$R = $success[ $hook ];
			} elseif( isset( $this->hook_template[ $hook ] ) ) {
				$success[ $hook ] = $this->hook_template[ $hook ];
				$R = $success[ $hook ];
			} else {
				foreach( $this->hook_template as $hook_name => $template ){
					if( strpos( $hook, $hook_name ) !== false ){
						$success[ $hook ] = $template;
						$R = $template;
						break;
					}
				}
			}
			return $R;
		}


		public function get_fields(){
			return $this->fields;
		}


		/**
		 * Добавить поле
		 * @param        $fieldId
		 * @param string $type
		 * @return hw_field
		 */
		public function add_field( $fieldId, $type = 'text', $name = null ){
			$global_id = hiweb()->string()->rand();
			$field = new hw_field( $fieldId, $global_id, $type );
			$field->name( $name );
			$this->fields[ $global_id ] = $field;
			$this->fieldId_globalId[ $fieldId ][] = $field;
			$this->globalId_fieldId[ $global_id ][] = $field;
			return $field;
		}


		/**
		 * @param $field_id
		 * @return hw_field
		 */
		public function get_field( $field_id ){
			if( !isset( $this->fieldId_globalId[ $field_id ] ) ){
				hiweb()->console()->warn( sprintf( __( 'Field id:[%s] not found to display value by context', 'hw-core-2' ), $field_id ) );
				return $this->add_field( $field_id );
			}
			return end( $this->fieldId_globalId[ $field_id ] );
		}


		/**
		 * Добавление функции в бэк-энд редакторе типов поста, пользователя, таксономий
		 * @param hw_field $hw_field
		 * @return hw_fields_hook
		 */
		public function hook( hw_field $hw_field ){
			$edit_hook = new hw_fields_hook( $hw_field );
			$this->hooks[] = $edit_hook;
			return $edit_hook;
		}


		/**
		 * @return hw_fields_hook[]
		 */
		public function get_hooks(){
			$R = array();
			if( is_array( $this->hooks ) )
				foreach( $this->hooks as $hook ){
					if( $hook instanceof hw_fields_hook )
						$R[] = $hook;
				}
			return $R;
		}


		///////////////////////

		protected function do_distribute(){
			include 'fields/do_distribute.php';
		}


		/**
		 * Возвращает блок полей
		 * @param array|mixed $fields - массив полей, если передать массив, то будут выведены все зарегистрированные поля
		 * @param string      $template
		 * @return bool|string
		 */
		private function html( $fields = array(), $template = '', $hook_name = '' ){
			if( !is_array( $fields ) )
				$fields = $this->fields;
			hiweb()->css( hiweb()->dir_css . '/fields.css' );
			$template_path = $this->dir . '/html' . ( (string)$template == '' ? '' : '-' . $template ) . '.php';
			return hiweb()->path()->get_content( $template_path, compact( [
				'fields', 'hook_name'
			] ) );
		}


		/**
		 * Вывод блока полей
		 * @param array|mixed $fields - массив полей
		 * @param string      $template
		 * @return bool|string
		 */
		private function the( $fields = array(), $template = '', $hook_name = '' ){
			$R = $this->html( $fields, $template, $hook_name );
			echo $R;
			return $R;
		}


		/**
		 * @param null $post_id
		 * @param null $post
		 */
		private function save_post( $post_id = null, $post = null ){
			/*$update_data = array();
			foreach( $_POST as $key => $value ){
				if( strpos( $key, hiweb()->fields()->input_prefix ) === 0 ){
					$update_data[ $key ] = $value;
				}
			}*/
			/** @var hw_fields_hook $hook */
			foreach( $this->get_hooks() as $hook ){
				$input_name = $hook->get_field()->input()->name;
				if( array_key_exists( $input_name, $_POST ) ){
					//todo: сделать фильтр, если данного поля на самом деле не должно быть
					update_post_meta( $post_id, $hook->get_field()->input()->name, $_POST[ $input_name ] );
				}
			}
		}


		/**
		 * @param integer $term_id
		 */
		private function save_taxonomy( $term_id ){
			/*$update_data = array();
			foreach( $_POST as $key => $value ){
				if( strpos( $key, hiweb()->fields()->input_prefix ) === 0 ){
					$update_data[ $key ] = $value;
				}
			}*/
			/** @var hw_fields_hook $hook */
			foreach( $this->get_hooks() as $hook ){
				$input_name = $hook->get_field()->input()->name;
				if( array_key_exists( $input_name, $_POST ) ){
					//todo: сделать фильтр, если данного поля на самом деле не должно быть
					update_term_meta( $term_id, $hook->get_field()->input()->name, $_POST[ $input_name ] );
				}
			}
		}


		/**
		 * User Options Update
		 * @param $user_id
		 */
		private function user_options_update( $user_id ){
			if( !isset( $_POST['user_id'] ) )
				return;
			foreach( $this->get_hooks() as $hook ){
				if( count( $hook->get_rules_by_group( 'users' ) ) > 0 && isset( $_POST[ $hook->get_field()->input()->name ] ) ){
					update_user_meta( $_POST['user_id'], $hook->get_field()->input()->name, $_POST[ $hook->get_field()->input()->name ] );
				}
			}
		}

	}


	class hw_field{

		use hw_hidden_methods_props;

		private $id;
		private $global_id;
		///
		private $name;
		private $description;
		///
		private $type;
		/** @var hw_fields_hook[] */
		private $hooks = array();
		/** @var null|hw_input */
		private $input;
		///
		/** @var mixed Значение по-умолчанию */
		private $default;

		private $form_template = '';


		/**
		 * hw_field constructor.
		 * @param        $fieldId  - индификатор поля
		 * @param        $globalId - глобальный ID
		 * @param string $fieldType
		 */
		public function __construct( $fieldId, $globalId, $fieldType = 'text' ){
			$this->id = sanitize_file_name( mb_strtolower( $fieldId ) );
			$this->global_id = $globalId;
			////
			$this->input = hiweb()->inputs()->create( $fieldType, hiweb()->fields()->input_prefix . $this->id );
			$this->name = $fieldId;
		}


		public function get_id(){
			return $this->id;
		}


		/**
		 * @param string $type
		 */
		public function set_type( $type = 'text' ){
			$this->type = $type;
			$this->input = hiweb()->inputs()->create( $type, $this->id );
		}
		///////////////////////

		/**
		 * @param        $idOrName
		 * @param string $type
		 * @return hw_input
		 */
		public function add_col( $idOrName, $type = 'text' ){
			return $this->input()->add_col( $idOrName, $type );
		}


		/**
		 * @return bool
		 */
		public function have_rows(){
			return $this->input()->have_rows();
		}
		///////////////////////

		/**
		 * @param null $set
		 * @return hw_field|string
		 */
		public function form_template( $set = null ){
			if( is_string( $set ) && trim( $set ) != '' ){
				$this->form_template = $set;
				return $this;
			} else {
				return $this->form_template;
			}
		}


		/**
		 * @return mixed
		 */
		public function get_type(){
			return $this->type;
		}


		/**
		 * Возвращает TRUE, если имеет инпут
		 * @return bool
		 */
		public function have_input(){
			return ( $this->input instanceof hw_input );
		}


		/**
		 * @param null $set
		 * @return hw_field|string
		 */
		public function placeholder( $set = null ){
			if( is_null( $set ) ){
				return $this->input()->placeholder;
			} else {
				$this->input()->placeholder = $set;
				return $this;
			}
		}


		/**
		 * @return hw_input
		 */
		public function input(){
			if( !$this->input instanceof hw_input ){
				$this->input = hiweb()->inputs()->create();
			}
			if( $this->input->placeholder == '' )
				$this->input->placeholder = $this->default;
			//$this->input->value = $this->value;
			return $this->input;
		}


		/**
		 * Возвращает значение поля
		 * @param null $set_value
		 * @return mixed|null
		 */
		public function value( $set_value = null ){
			if( !is_null( $set_value ) ){
				$this->input()->value( $set_value );
			}
			if( $this->have_input() ){
				$R = $this->input->value();
				if( is_null( $R ) ){
					$this->input->value( $this->default );
					$R = $this->default;
				}
				return $R;
			}
			hiweb()->console()->error( sprintf( __( 'For field [%s] input not be set' ), $this->id ), true );
			return null;
		}


		/**
		 * @param null $contextId
		 * @param null $contextType
		 * @return mixin
		 */
		public function value_by_context( $contextId = null, $contextType = null ){ //TODO!!!
			$R = include 'fields/context.php';
			return $R;
		}


		/**
		 * @return hw_fields_hook
		 */
		public function location(){
			$hook = hiweb()->fields()->hook( $this );
			$this->hooks[] = $hook;
			return $hook;
		}


		/**
		 * @return hw_fields_hook[]
		 */
		public function get_locations(){
			return $this->hooks;
		}


		/**
		 * Возвращает HTML поля с инпутом
		 * @param string $template
		 * @return string
		 */
		public function html( $template = '' ){
			$R = $this->input()->html();
			return $R;
		}


		/**
		 * @param string $template
		 * @return string
		 */
		public function the( $template = '' ){
			$R = $this->html( $template );
			echo $R;
			return $R;
		}


		/**
		 * Установить/получить имя поля
		 * @param null $set
		 * @return hw_field|string
		 */
		public function name( $set = null ){
			if( is_null( $set ) ){
				return $this->name;
			}
			$this->name = $set;
			return $this;
		}


		/**
		 * Установить/получить пояснение для поля
		 * @param null $set
		 * @return hw_field|string
		 */
		public function description( $set = null ){
			if( is_null( $set ) ){
				return $this->description;
			}
			$this->description = $set;
			return $this;
		}


		/**
		 * Установить/получить значение поля по-умолчания
		 * @param null $set
		 * @return hw_field|string
		 */
		public function default_value( $set = null ){
			if( is_null( $set ) ){
				return $this->default;
			}
			$this->default = $set;
			return $this;
		}


	}


	class hw_fields_hook{

		use hw_hidden_methods_props;

		public $rules = array();
		/** @var hw_field */
		private $field;


		public function __construct( $field ){
			$this->field = $field;
		}


		/**
		 * Возвращает TRUE, если hook_name соответствует текущему хуку админки
		 * @param $hook_name
		 * @return bool
		 * @deprecated
		 */
		/*protected function get_compare( $hook_name ){
			if( is_array( $hook_name ) )
				$hook_name = reset( $hook_name );
			if( !is_string( $hook_name ) && !is_integer( $hook_name ) )
				return false;
			if( !function_exists( 'get_current_screen' ) || !get_current_screen() instanceof WP_Screen ){
				hiweb()->console()->warn( __( 'Object [WP_Screen] not found' ), true );
				return false;
			}
			if( !is_array( $this->rules ) ){
				hiweb()->console()->warn( __( 'The rules are not an array' ), true );
				return false;
			}
			///
			$R = 0;
			foreach( $this->rules as $rule_group => $rules ){
				////FILTER
				switch( $rule_group ){
					//////////////////////POST TYPES
					case 'post_type':
						///
						if( get_current_screen()->base != 'post' ){
							continue 2;
						}
						///
						if( !is_array( $rules ) ){
							hiweb()->console()->warn( __( 'The rules do not accidentally found an array' ), true );
							continue;
						}
						foreach( $rules as $rule_data ){
							$post_type = $rule_data[0];
							$position = $rule_data[1];
							///
							if( get_current_screen()->id != $post_type ){
								continue 3;
							}
							///
							$position_numbers = array(
								'edit_form_top' => 0,
								'edit_form_before_permalink' => 1,
								'edit_form_after_title' => 2,
								'edit_form_after_editor' => 3,
								'submitpage_box' => 4,
								'submitpost_box' => 4,
								'edit_page_form' => 5,
								'edit_form_advanced' => 5
							);
							///
							if( !array_key_exists( $hook_name, $position_numbers ) ){
								hiweb()->console()->warn( sprintf( __( 'Hook [%s] not found' ), $hook_name ), true );
								continue 3;
							}
							///
							if( !array_key_exists( $position, array_flip( $position_numbers ) ) ){
								hiweb()->console()->warn( sprintf( __( 'Position [%s] in hook [$s] not found' ), $position_numbers, $hook_name ), true );
								continue 3;
							}
							///
							if( $position != $position_numbers[ $hook_name ] ){
								continue 3;
							}
							///
						}
						break;
					//////////////////////TAXONOMY
					case 'taxonomy':
						if( !array_key_exists( get_current_screen()->base, array_flip( [
							'edit-tags',
							'term'
						] ) )
						){
							continue 2;
						}
						foreach( $rules as $rule_data ){
							$taxonomy = $rule_data[0];
							if( $taxonomy != get_current_screen()->taxonomy ){
								continue 3;
							}
						}
						break;
					//////////////////////USERS
					case 'users':
						///TODO!!!
						continue 2;
						break;
					//////////////////////OPTIONS
					case 'options':
						///TODO!!!

						continue 2;
						break;
					//////////////////////ADMIN MENU
					case 'admin_menu':
						foreach( $rules as $rule_data ){
							if( $rule_data == get_current_screen()->parent_file ){
								$R ++;
							}
						}
						continue 2;
						break;
					default:
						hiweb()->console()->warn( sprintf( __( 'Group [%s] not exists' ), $rule_group ), 3 );
						continue 2;
						break;
				}
				///
				$R ++;
			}
			return count( $this->rules ) == $R;
		}*/

		/**
		 * Проверка хука на соответствие
		 * @param array $arguments
		 */
		protected function do_hook( $arguments = array() ){
			$hook_name = $arguments[0];
			if( $this->get_compare( $hook_name ) ){
				$save_hooks = [
					'save_post', 'create_term', 'edited_taxonomy'
				];
				if( array_key_exists( $hook_name, array_flip( $save_hooks ) ) ){
					///SAVE VALUE
				} else {
					//$this->field->edi; //todo!
				}
			}
		}


		/**
		 * Add field to all post types edit page
		 * @param int $position
		 * @return $this
		 */
		public function post_types( $position = 3 ){
			$this->rules['post_types'][] = $position;
			return $this;
		}


		/**
		 * Add field to the edit page of post type
		 * @param string $post_type
		 * @param int    $position - позиция: 1 - after title, 2 - before editor, 3 - after editor, 4 - over sidebar, 5 - bottom on edit page
		 * @param bool   $equal
		 * @return $this
		 */
		public function post_type( $post_type = 'post', $position = 3, $equal = true ){
			$this->rules['post_type'][ $post_type ] = $position;
			return $this;
		}


		/**
		 * Установить поле на странице таксономии
		 * @param bool $value
		 * @param bool $equal
		 * @return $this
		 */
		public function taxonomy( $value = false, $equal = true ){
			if( is_array( $value ) ){
				$this->rules[ __FUNCTION__ ] = !isset( $this->rules[ __FUNCTION__ ] ) ? $value : array_merge( $this->rules[ __FUNCTION__ ], $value );
			} else {
				$this->rules[ __FUNCTION__ ][] = $value;
			}

			return $this;
		}


		/**
		 * Установить поле в панель установок пользователя
		 * @return $this
		 */
		public function users( $position = 0 ){
			$this->rules['users'][] = $position;
			return $this;
		}


		/**
		 * Установить поле на странице пользователя с ролей
		 * @param bool $value
		 * @param bool $equal
		 * @return $this
		 */
		/*public function user_role( $value = false, $equal = true ){
			if( is_array( $value ) ){
				$this->rules[__FUNCTION__] = !isset( $this->rules[__FUNCTION__] ) ? $value : array_merge( $this->rules[__FUNCTION__], $value );
			} else {
				$this->rules[__FUNCTION__][] = $value;
			}

			return $this;
		}*/

		/**
		 * Set the field location in options
		 * @param string $options_page_slug
		 * @param int    $position
		 * @return $this
		 */
		public function options( $options_page_slug = 'options-general.php', $position = 0 ){
			$this->rules[ __FUNCTION__ ][ $options_page_slug ] = $position;
			///
			return $this;
		}


		/**
		 * Set the field location in Admin Menu
		 * @param string $admin_menu_slug
		 * @return hw_fields_hook
		 */
		public function admin_menu( $admin_menu_slug = 'theme' ){
			if( is_array( $admin_menu_slug ) ){
				$this->rules[ __FUNCTION__ ] = !isset( $this->rules[ __FUNCTION__ ] ) ? $admin_menu_slug : array_merge( $this->rules[ __FUNCTION__ ], $admin_menu_slug );
			} else {
				$this->rules[ __FUNCTION__ ][] = $admin_menu_slug;
			}
			///
			return $this;
		}


		/**
		 * Возвращает массив правил от определенной группы
		 * @param string $rules_group
		 * @return array
		 */
		public function get_rules_by_group( $rules_group = 'post_type' ){
			if( array_key_exists( $rules_group, $this->rules ) && is_array( $this->rules[ $rules_group ] ) ){
				return $this->rules[ $rules_group ];
			}
			return array();
		}


		/**
		 * @return hw_field
		 */
		public function get_field(){
			return $this->field;
		}

	}