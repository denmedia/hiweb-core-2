<?php
	/**
	 * Created by PhpStorm.
	 * User: d9251
	 * Date: 31.08.2016
	 * Time: 17:36
	 */




	/**
	 * Класс для работы с WordPress
	 * Class hiweb_wp
	 */
	class hw_wp{


		public function add_user_meta_box( $id, $hiweb_user_meta_boxes = null ){
			hiweb()->post_type();
			if( !isset( $this->_user_meta_boxes[ $id ] ) ){
				if( $hiweb_user_meta_boxes instanceof hw_wp_user_meta_boxes )
					$this->_user_meta_boxes[ $id ] = $hiweb_user_meta_boxes;else $this->_user_meta_boxes[ $id ] = new hw_wp_user_meta_boxes( $id );
			}
			return $this->_user_meta_boxes[ $id ];
		}


		/**
		 * Возвращает TRUE, если текущий запрос происходит через AJAX
		 * @return bool
		 */
		public function is_ajax(){
			return ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ( !empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' );
		}


	}