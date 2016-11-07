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
			if( !isset( $this->fields[ $contextType ][ $contextId ] ) ){
				$this->fields[$fieldId][ $contextType ][ $contextId ] = new hw_field( $fieldId, $contextId, $contextType );
			}
			return $this->fields[$fieldId][ $contextType ][ $contextId ];
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
			$subFieldId = sanitize_file_name(mb_strtolower($subFieldId));
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
			$this->id = sanitize_file_name( mb_strtolower( $fieldId ) );
			////
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
				$home = hiweb()->inputs()->give_home(array('post_types',$contextId->post_type));
				$this->input = $home->give_input( $this->id, false, true )->value(get_post_meta($contextId->ID, $this->id, true));
			}elseif( $contextId instanceof WP_Term ){
				$home = hiweb()->inputs()->give_home(array('taxonomies',$contextId->taxonomy));
				$this->input = $home->give_input( $this->id, false, true )->value(get_term_meta($contextId->term_id, $this->id, true));
			}elseif( $contextId instanceof WP_User ){
				$home = hiweb()->inputs()->give_home(array('users',$contextId->user_login));
				$this->input = $home->give_input( $this->id, false, true )->value(get_user_meta($contextId->ID, $this->id, true));
			}elseif( is_string( $contextId ) ){
				$home = hiweb()->inputs()->give_home(array('options',$contextId));
				$this->input = $home->give_input( $this->id, false, true )->value( get_option( $this->id ) );
			}elseif( is_integer( $contextId ) ){
				//todo
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