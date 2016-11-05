<?php
	
	
	class hw_fields{
		
		private $fields = array();
		
		private $the_rows_current_field = array( '', '', '' );
		private $the_rows;
		private $the_row;
		
		
		/**
		 * @param $fieldId
		 * @param null $contextId
		 * @param null $contextType
		 * @return hw_field
		 */
		public function give( $fieldId, $contextId = null, $contextType = null ){
			if( is_null( $contextId ) ){
				if( function_exists( 'get_queried_object' ) ){
					$contextId = get_queried_object();
				}elseif( is_string( $fieldId ) && trim( $fieldId ) != '' ){
					$contextId = '';
					$contextType = 'options';
				}
			}
			///
			if( $contextId instanceof WP_Post ){
				$contextId = $contextId->ID;
				$contextType = 'post';
			}elseif( $contextId instanceof WP_Term ){
				$contextId = $contextId->term_id;
				$contextType = 'term';
			}elseif( $contextId instanceof WP_User ){
				$contextId = $contextId->ID;
				$contextType = 'user';
			}elseif( is_string( $contextId ) ){
				$contextId = sanitize_file_name( strtolower( $contextId ) );
				$contextType = 'options';
			}elseif( is_integer( $contextId ) ){
				$contextType = 'post';
			}
			///
			if( !isset( $this->fields[ $contextType ][ $contextId ] ) ){
				$this->fields[ $contextType ][ $contextId ] = new hw_field( $fieldId, $contextId, $contextType );
			}
			return $this->fields[ $contextType ][ $contextId ];
		}
		
		
		/**
		 * @param $fieldId
		 * @param null $contextId
		 * @param null $contextType
		 * @return bool
		 */
		public function have_rows( $fieldId, $contextId = null, $contextType = null ){
			$field = $this->give( $fieldId, $contextId, $contextType );
			if( !$field->have_rows() ){
				return false;
			}else{
				if( $this->the_rows_current_field[0] != $field->id() || $this->the_rows_current_field[1] != $field->context_id() || $this->the_rows_current_field[2] != $field->context_type() ){
					$this->the_rows = $field->get();
					$this->the_rows_current_field[0] = $field->id();
					$this->the_rows_current_field[1] = $field->context_id();
					$this->the_rows_current_field[2] = $field->context_type();
				}
				return ( is_array( $this->the_rows ) && count( $this->the_rows ) > 0 );
			}
		}
		
		
		/**
		 * Возвращает следующий массив строки, либо FALSE
		 * @return bool|mixed
		 */
		public function the_row(){
			if( is_array( $this->the_rows ) && count( $this->the_rows ) > 0 ){
				$this->the_row = array_shift( $this->the_rows );
				return $this->the_row;
			}else{
				return false;
			}
		}
		
		
		/**
		 * Возвращает значение клетки в текущей строке полей
		 * @param $subFieldId
		 * @return mixed|null
		 */
		public function get_sub_field( $subFieldId ){
			if( is_array( $this->the_row ) ){
				return array_key_exists( $subFieldId, $this->the_row ) ? $this->the_row[ $subFieldId ] : null;
			}
			return $this->the_row;
		}
		
	}
	
	
	class hw_field{
		
		private $id;
		/** @var hw_input|hw_input_checkbox|hw_input_repeat|hw_input_text */
		private $input;
		
		private $contextId;
		private $contextType;
		
		private $cols = array();
		private $rows = array();
		
		
		public function __construct( $fieldId, $contextId = '', $contextType = 'post' ){
			$this->id = sanitize_file_name( strtolower( $fieldId ) );
			$this->contextId = $contextId;
			$this->contextType = $contextType;
			switch( $this->contextType ){
				case 'post':
					$this->input = hiweb()->post( $contextId )->get_field( $fieldId );
					break;
				case 'term':
					$this->input = hiweb()->taxonomies()->get_taxonomy_by_term( $contextId )->get_field( $fieldId );
					$this->input->value( get_term_meta( $contextId, $fieldId, true ) );
					hiweb()->console( $this->input->type() ); //todo!!!
					break;
				case 'user':
					//todo
					break;
				case 'options':
					if( trim( $contextId ) == '' ){
						foreach( hiweb()->admin()->menu()->get_pages( 0, 1, 0, 0, 0 ) as $section ){
							if( $section->field_exists( $this->id ) ){
								$this->input = $section->get_field( $this->id );
							}
						}
						if( !$this->input instanceof hw_input )
							$this->input = hiweb()->input()->value( get_option( $this->id ) );
					}else{
						$this->input = hiweb()->admin()->menu()->get( $contextId )->get_field( $this->id );
					}
					break;
				default:
					$this->input = hiweb()->input();
			}
		}
		
		
		/**
		 * @return mixed
		 */
		public function id(){
			return $this->id;
		}
		
		
		/**
		 * @return string
		 */
		public function context_id(){
			return $this->contextId;
		}
		
		
		/**
		 * @return string
		 */
		public function context_type(){
			return $this->contextType;
		}
		
		
		/**
		 * @param null $args
		 * @return mixed
		 */
		public function get( $args = null ){
			return $this->input->value( $args );
		}
		
		
		/**
		 * @param null $args
		 * @return string
		 */
		public function get_content( $args = null ){
			return $this->input()->get_content( $args );
		}
		
		
		/**
		 * @param null $args
		 * @return string
		 */
		public function the_content( $args = null ){
			return $this->input()->the_content( $args );
		}
		
		
		/**
		 * @param null $args
		 * @return mixed
		 */
		public function the( $args = null ){
			return $this->input->the_value( $args );
		}
		
		
		/**
		 * @return bool|int
		 */
		public function have_rows(){
			return ( is_array( $this->input->value() ) ? count( $this->input->value() ) : false );
		}
		
		
		/**
		 * @return hw_input|hw_input_checkbox|hw_input_repeat|hw_input_text
		 */
		public function input(){
			return $this->input;
		}
		
	}