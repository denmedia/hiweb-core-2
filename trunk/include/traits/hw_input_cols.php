<?php

//TODO!
	trait hw_input_cols{

		private $cols = array();


		/**
		 * @param $idOrName
		 * @param string $type
		 * @return hw_input
		 */
		public function add_col( $idOrName, $type = 'text' ){
			$id = sanitize_file_name( mb_strtolower( $idOrName ) );
			$input = hiweb()->inputs()->make( $idOrName, $type );
			$input->tags( 'data-col-id', $id );
			$input->tags( 'name' );
			$this->cols[ $id ] = $input;
			return $this->cols[ $id ];
		}


		/**
		 * @return array|hw_input[]
		 */
		public function get_cols(){
			return $this->cols;
		}


		/**
		 * @param $col_id
		 */
		public function get_col( $col_id ){
			/*if( array_key_exists( $col_id, $this->cols ) )
				;*/
		}


		/**
		 * @return bool
		 */
		public function have_cols(){
			return ( is_array( $this->cols ) && count( $this->cols ) > 0 );
		}

	}