<?php
	
	
	class hw_fields{
		
		private $fields = array();
		
		
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
		public function type_id(){
			return $this->contextId;
		}
		
		
		/**
		 * @return string
		 */
		public function type(){
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
		 */
		public function the( $args = null ){
			return $this->input->the_value( $args );
		}
		
		
		/**
		 * @return bool|int
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
			return ( is_array( $this->input->value() ) ? count( $this->input->value() ) : false );
		}
		
		
		public function the_row(){
			if( !$this->have_rows() ){
				return false;
			}
			///
			if( !is_array( $this->rows ) ){
				foreach( $this->input->value() as $row_values ){
					if( is_array( $row_values ) )
						$this->rows[] = new hw_field_row( $this, $row_values );else hiweb()->console()->warn( 'hiweb()→meta()→the_row() error: once of the rows is not array!', 1 );
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
		
		public function cols(){
			
		}
		
		
		/**
		 * @return hw_input|hw_input_checkbox|hw_input_repeat|hw_input_text
		 */
		public function input(){
			return $this->input;
		}
		
	}
	
	
	class hw_field_row{
		
		/** @var  hw_field */
		private $parent_field;
		private $row_values = array();
		
		
		public function __construct( hw_field $field, array $row_values ){
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