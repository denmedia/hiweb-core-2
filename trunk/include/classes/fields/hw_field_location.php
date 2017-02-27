<?php


	class hw_field_location{

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
		 * @return hw_field_location
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