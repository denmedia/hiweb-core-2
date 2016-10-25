<?php


	class hw_meta{

		private $meta = array();


		/**
		 * @param $field_id
		 * @return hw_meta_field
		 */
		public function get( $field_id ){
			if( !array_key_exists( $field_id, $this->meta ) ){
				$this->meta[ $field_id ] = new hw_meta_field( $field_id );
			}
			return $this->meta[ $field_id ];
		}

	}


	class hw_meta_field{

		private $id;
		public $object_type;
		public $object_id = 0;
		private $value;
		private $rows;
		private $row;


		public function __construct( $id ){
			$this->id = $id;
			if( !function_exists( 'get_queried_object' ) ){
				hiweb()->console()->warn( 'hiweb()→meta() warn: function[get_queried_object] not exists!' );
			}else{
				switch( get_class( get_queried_object() ) ){
					case 'WP_Post':
						$this->set_post( get_queried_object_id() );
						break;
					case 'WP_Term':
						$this->set_term( get_queried_object_id() );
						break;
					case 'WP_User':
						$this->set_user( get_queried_object_id() );
						break;
					default:
						$this->object_id = get_option( 'page_on_front' );
						if( intval( $this->object_id ) < 1 )
							$this->object_id = get_option( 'page_for_posts' );
						$this->object_type = 'post';
						break;
				}
			}
		}


		/**
		 * Возвращает значение поля
		 * @return array|hw_post_meta|mixed|null
		 */
		public function get(){
			if( is_null( $this->value ) ){
				if( $this->object_type == 'post' ){
					$this->value = get_post_meta( $this->object_id, $this->id, true );
				}elseif( $this->object_type == 'term' ){
					$this->value = get_term_meta( $this->object_id, $this->id, true );
				}elseif( $this->object_type == 'user' ){
					$this->value = hiweb()->user( $this->object_id )->meta( $this->id );
				}
			}
			return $this->value;
		}
		
		
		/**
		 * Выводит значение поля
		 */
		public function the(){
			echo $this->get();
		}


		public function set_post( $post = null ){
			$this->object_type = 'post';
			if( $post instanceof WP_Post ){
				$this->object_id = $post->ID;
			}elseif( is_numeric( $post ) ){
				$this->object_id = $post;
			}elseif( get_queried_object() instanceof WP_Post ){
				$this->object_id = get_queried_object_id();
			}else{
				hiweb()->console()->warn( 'hiweb()→meta()→set_post() error: post id for [' . $this->id . '] not found!' );
			}
			return $this;
		}


		public function set_term( $term = null ){
			$this->object_type = 'term';
			if( $term instanceof WP_Term ){
				$this->object_id = $term->term_id;
			}elseif( is_numeric( $term ) ){
				$this->object_id = $term;
			}elseif( get_queried_object() instanceof WP_Term ){
				$this->object_id = get_queried_object_id();
			}else{
				hiweb()->console()->warn( 'hiweb()→meta()→set_post() error: term id for [' . $this->id . '] not found!' );
			}
			return $this;
		}


		public function set_user( $user ){
			$this->object_type = 'user';
			if( $user instanceof WP_User ){
				$this->object_id = $user->ID;
			}elseif( is_numeric( $user ) ){
				$this->object_id = $user;
			}elseif( get_queried_object() instanceof WP_User ){
				$this->object_id = get_queried_object_id();
			}else{
				hiweb()->console()->warn( 'hiweb()→meta()→set_post() error: user id for [' . $this->id . '] not found!' );
			}
			return $this;
		}


		/**
		 * Возвращает TRUE, если есть суб-строки и ячейки
		 * @return bool
		 */
		public function has_rows(){
			return is_array( $this->get() ) && count( $this->get() );
		}


		/**
		 * Возвращает(устанавливает) текущую строку
		 * @return mixed|null
		 */
		public function the_row(){
			if( !$this->has_rows() ){
				return null;
			}
			///
			if( !is_array( $this->rows ) ){
				$this->rows = $this->get();
			}
			///
			if( count( $this->rows ) == 0 ){
				return null;
			}else{
				$this->row = array_shift( $this->rows );
				return $this->row;
			}
		}


		/**
		 * Получить содержимое суб-ячейки в текущей строке
		 * @param $subfield_id
		 * @return null
		 */
		public function sub_field( $subfield_id ){
			if( !$this->has_rows() ){
				return null;
			}
			if( is_null( $this->row ) ){
				$row = reset( $this->get() );
			}else{
				$row = $this->row;
			}
			return array_key_exists( $subfield_id, $this->row ) ? $this->row[ $subfield_id ] : null;
		}


		/**
		 * Выводит суб-ячейку (ECHO)
		 * @param $subfield_id
		 */
		public function the_subfield($subfield_id){
			echo $this->sub_field($subfield_id);
		}
	}