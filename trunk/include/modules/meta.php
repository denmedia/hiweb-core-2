<?php


	class hw_meta{

		private $meta = array();


		/**
		 * @param $field_id
		 * @return hw_meta_field
		 */
		public function give( $field_id ){
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
		/** @var  hw_meta_field_row[] */
		private $rows;
		/** @var  hw_meta_field_row */
		private $row;
		/** @var array */
		private $cols;
		/** @var  hw_input|hw_input_image|hw_input_repeat */
		private $input;


		public function __construct( $id ){
			$this->id = $id;
			$this->input = hiweb()->inputs()->is_exist( $id ) ? hiweb()->inputs()->give( $id ) : false;
			if( !did_action( 'wp' ) ){
				hiweb()->console()->warn( 'hiweb()→meta() warn: action [wp] dosen\'t did! Auto detect queried object is not possible!' );
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
				if($this->input instanceof hw_input) $this->input->value($this->get());
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


		/**
		 * Возвращает соответствующее поле. Если его не существует, создается новое
		 * @return bool|hw_input|hw_input_image|hw_input_repeat|hw_meta_box
		 */
		public function get_input(){
			return $this->input;
		}


		/**
		 * @param null $arguments
		 * @return null|string
		 */
		public function get_content( $arguments = null ){
			if( $this->input instanceof hw_input ){
				return $this->input->get_content( $arguments );
			}
			return null;
		}


		/**
		 * @param null $arguments
		 * @return null|string
		 */
		public function the_content( $arguments = null ){
			$content = $this->get_content( $arguments );
			echo $content;
			return $content;
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
		public function have_rows(){
			if( !is_array( $this->cols ) ){
				$this->cols = array();
				$value = $this->get();
				if( is_array( $value ) && count( $value ) > 0 ){
					foreach( $value as $row ){
						if( is_array( $row ) )
							foreach( $row as $col_id => $col_val ){
								$this->cols[] = $col_id;
							}
					}
				}
			}
			return ( count( $this->cols ) > 0 );
		}


		/**
		 * Возвращает массив ID колонок (в любом случае). Для определения наличия полей, используйте has_rows()
		 * @return array
		 */
		public function cols(){
			$this->have_rows();
			return $this->cols;
		}


		/**
		 * Возвращает(устанавливает) текущую строку
		 * @return false|hw_meta_field_row
		 */
		public function the_row(){
			if( !$this->have_rows() ){
				return false;
			}
			///
			if( !is_array( $this->rows ) ){
				foreach( $this->get() as $row_values ){
					if( is_array( $row_values ) )
						$this->rows[] = new hw_meta_field_row( $this, $row_values );else hiweb()->console()->warn( 'hiweb()→meta()→the_row() error: once of the rows is not array!', 1 );
				}
			}
			///
			if( count( $this->rows ) == 0 ){
				return false;
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
			if( !$this->have_rows() ){
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
		public function the_subfield( $subfield_id ){
			echo $this->sub_field( $subfield_id );
		}
	}


	class hw_meta_field_row{

		/** @var  hw_meta_field */
		private $parent_field;
		private $row_values = array();


		public function __construct( hw_meta_field $field, array $row_values ){
			$this->parent_field = $field;
			$this->row_values = $row_values;
		}


		/**
		 * @return array
		 */
		public function cols(){
			$R = array();
			if( is_array( $this->parent_field->cols() ) )
				foreach( $this->parent_field->cols() as $col_id ){
					$R[ $col_id ] = array_key_exists( $col_id, $this->row_values ) ? $this->row_values[ $col_id ] : null;
				}
			return $R;
		}


		/**
		 * @param $col_id
		 * @return mixed|null
		 */
		public function get_cell( $col_id ){
			return array_key_exists( $col_id, $this->row_values ) ? $this->row_values[ $col_id ] : null;
		}


		/**
		 * @param $col_id
		 * @return mixed|null
		 */
		public function the_cell( $col_id ){
			$value = $this->get_cell( $col_id );
			echo $value;
			return $value;
		}
	}