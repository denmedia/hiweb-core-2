<?php

	include_once 'admin/bar.php';
	include_once 'admin/menu.php';
	include_once 'admin/notice.php';


	class hw_admin{

		use hw_hidden_methods_props;


		/** @var hw_wp_admin_bar[] */
		private $_admin_bar = array();





		/**
		 * @return hw_admin_menu
		 */
		public function menu(){
			static $class;
			if( !$class instanceof hw_admin_menu ){
				$class = new hw_admin_menu();
			}
			return $class;
		}


		/**
		 * Добавить линки в админ-бар
		 * @param $id
		 * @return hw_wp_admin_bar
		 */
		public function bar( $id ){
			if( !array_key_exists( $id, $this->_admin_bar ) ){
				$this->_admin_bar[ $id ] = new hw_wp_admin_bar( $id );
			}
			return $this->_admin_bar[ $id ];
		}


		public function notice($notice = null){
			static $class;
			if(!$class instanceof hw_admin_notices) $class = new hw_admin_notices();
			if(!is_null($notice)) $class->info($notice);
			return $class;
		}

	}


