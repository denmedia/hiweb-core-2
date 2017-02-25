<?php


	//TODO!
	trait hw_input_cols{

		/** @var hw_field[] */
		private $cols = array();


		/**
		 * @param        $idOrName
		 * @param string $type
		 * @return hw_input
		 */
		public function add_col( $idOrName, $type = 'text', $name = '' ){
			$id = sanitize_file_name( mb_strtolower( $idOrName ) );
			$field = hiweb()->field($idOrName,  $type, $name );
			$input = $field->input();
			$input->tags['data-col-id'] = $id;
			$input->tags['name'] = false;
			$this->cols[ $id ] = $input;
			return $this->cols[ $id ];
		}


		/**
		 * @return hw_field[]
		 */
		public function get_cols(){
			return $this->cols;
		}


		/**
		 * @param $col_id
		 * @return hw_input
		 */
		public function get_col( $col_id ){
			if( array_key_exists( $col_id, $this->cols ) ){
				return $this->cols[ $col_id ];
			} else return hiweb()->inputs()->create();
		}


		/**
		 * @return bool
		 */
		public function have_cols(){
			return ( is_array( $this->cols ) && count( $this->cols ) > 0 );
		}


		public function set_cols( $fields = array() ){
			if( !is_array( $fields ) )
				return false;
			$this->cols = array();
			foreach( $fields as $field ){
				if( $field instanceof hw_field ){
					$this->cols[ $field->get_id() ] = $field;
				}
			}
			return $this->cols;
		}

	}