<?php

	if( !function_exists( 'hiweb' ) ){
		/**
		 * Запрос к корневому классу hiweb
		 * @return hw_core
		 */
		function hiweb(){
			static $class;
			if( !$class instanceof hw_core )
				$class = new hw_core;
			return $class;
		}
	}

	if( !function_exists( 'add_field' ) ){
		/**
		 * Add field
		 * @param        $id
		 * @param string $type
		 * @param null   $name
		 * @return hw_field
		 */
		function add_field( $id, $type = 'text', $name = null ){
			return hiweb()->fields()->make( $id, $type, $name );
		}
	}

	if( !function_exists( 'get_field' ) ){
		/**
		 * @param      $fieldId
		 * @param null $contextId
		 * @return mixed
		 */
		function get_field( $fieldId, $contextId = null ){
			return hiweb()->fields()->get_byContext( $fieldId, $contextId )->value();
		}
	}

	if( !function_exists( 'get_field_content' ) ){
		/**
		 * @param            $fieldId
		 * @param null       $contextId
		 * @param null|mixed $args
		 * @param null|mixed $args2
		 * @return mixed
		 */
		function get_field_content( $fieldId, $contextId = null, $args = null, $args2 = null ){
			return hiweb()->fields()->get_byContext( $fieldId, $contextId )->content( $args, $args2 );
		}
	}

	if( !function_exists( 'the_field_content' ) ){
		/**
		 * @param            $fieldId
		 * @param null       $contextId
		 * @param null|mixed $args
		 * @param null|mixed $args2
		 */
		function the_field_content( $fieldId, $contextId = null, $args = null, $args2 = null ){
			echo hiweb()->fields()->get_byContext( $fieldId, $contextId )->content( $args, $args2 );
		}
	}

	if( !function_exists( 'the_field' ) ){
		/**
		 * @param      $fieldId
		 * @param null $contextId
		 * @return mixed
		 */
		function the_field( $fieldId, $contextId = null ){
			echo hiweb()->fields()->get_byContext( $fieldId, $contextId )->value();
		}
	}

	if( !function_exists( 'have_rows' ) ){
		/**
		 * @param      $fieldId
		 * @param null $contextId
		 * @return bool
		 */
		function have_rows( $fieldId, $contextId = null ){
			return hiweb()->fields()->get_byContext( $fieldId, $contextId )->have_rows();
		}
	}

	if( !function_exists( 'the_row' ) ){
		/**
		 * @return bool|mixed
		 */
		function the_row(){
			return hiweb()->fields()->the_row();
		}
	}

	if( !function_exists( 'get_sub_field' ) ){
		/**
		 * @param $subFieldId
		 * @return mixed|null
		 */
		function get_sub_field( $subFieldId ){
			return hiweb()->fields()->get_sub_field( $subFieldId );
		}
	}

	if( !function_exists( 'the_sub_field' ) ){
		/**
		 * @param $subFieldId
		 */
		function the_sub_field( $subFieldId ){
			echo hiweb()->fields()->get_sub_field( $subFieldId );
		}
	}

	if( !function_exists( 'reset_rows' ) ){
		/**
		 * @param      $fieldId
		 * @param null $contextId
		 * @return mixed
		 */
		function reset_rows($fieldId, $contextId = null){
			return hiweb()->fields()->get_byContext($fieldId, $contextId)->reset_row();
		}
	}

	if( !function_exists( 'add_admin_menu_page' ) ){
		/**
		 * Добавить в админ-панель страницу опций
		 * @param      $title
		 * @param      $slug
		 * @param null $parent_slug
		 * @return hw_admin_menu_page|hw_admin_submenu_page
		 */
		function add_admin_menu_page( $title, $slug, $parent_slug = null ){
			if( is_string( $parent_slug ) ){
				$R = hiweb()->admin()->menu()->give_subpage( $slug, $parent_slug );
				$R->menu_title( $title );
				$R->page_title( $title );
			} else {
				$R = hiweb()->admin()->menu()->give_page( $slug );
				$R->menu_title( $title );
				$R->page_title( $title );
			}
			return $R;
		}
	}