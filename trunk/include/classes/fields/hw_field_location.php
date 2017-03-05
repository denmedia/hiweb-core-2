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