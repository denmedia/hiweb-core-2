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
			return hiweb()->fields()->add_field( $id, $type, $name );
		}
	}

	if( !function_exists( 'get_field' ) ){
		/**
		 * @param      $fieldId
		 * @param null $contextId
		 * @param null $contextType
		 * @return mixed
		 */
		function get_field( $fieldId, $contextId = null, $contextType = null ){
			return hiweb()->field( $fieldId, $contextId, $contextType )->get();
		}
	}

	if( !function_exists( 'the_field' ) ){
		/**
		 * @param      $fieldId
		 * @param null $contextId
		 * @param null $contextType
		 * @return mixed
		 */
		function the_field( $fieldId, $contextId = null, $contextType = null ){
			return hiweb()->field( $fieldId, $contextId, $contextType )->the();
		}
	}

	if( !function_exists( 'have_rows' ) ){
		/**
		 * @param      $fieldId
		 * @param null $contextId
		 * @param null $contextType
		 * @return bool
		 */
		function have_rows( $fieldId, $contextId = null, $contextType = null ){
			return hiweb()->fields()->have_rows( $fieldId, $contextId, $contextType );
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
		 * @return mixed|null
		 */
		function the_sub_field( $subFieldId ){
			$content = hiweb()->fields()->get_sub_field( $subFieldId );
			echo $content;
			return $content;
		}
	}

	if(!function_exists('add_admin_menu_page')){
		/**
		 * Добавить в админ-панель страницу опций
		 * @param      $title
		 * @param      $slug
		 * @param null $parent_slug
		 * @return hw_admin_menu_page|hw_admin_submenu_page
		 */
		function add_admin_menu_page($title, $slug, $parent_slug = null){
			if(is_string($parent_slug)){
				$R = hiweb()->admin()->menu()->give_subpage($slug, $parent_slug);
				$R->menu_title($title);
				$R->page_title($title);
			} else {
				$R = hiweb()->admin()->menu()->give_page($slug);
				$R->menu_title($title);
				$R->page_title($title);
			}
			return $R;
		}
	}