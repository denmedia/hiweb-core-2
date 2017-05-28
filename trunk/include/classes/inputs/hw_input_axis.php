<?php

	require_once 'hw_input_axis_cols.php';


	/**
	 * Created by PhpStorm.
	 * User: denmedia
	 * Date: 22.05.2017
	 * Time: 8:32
	 */
	trait hw_input_axis{

		/** @var hw_input_axis_col[] */
		protected $cols = array();
		/** @var hw_input_axis_rows */
		private $rows;

		protected $loop;
		protected $loop_current_row;


		///COLS


		/**
		 * @param null|string $id
		 * @param null|string $name
		 * @param null|string $input_type
		 * @return hw_input_axis_col
		 */
		public function add_col( $id, $input_type = null, $name = null ){
			/** @var hw_input $this */
			$this->cols[ $id ] = new hw_input_axis_col( $id, $this );
			if( is_string( $name ) )
				$this->cols[ $id ]->name( $name );
			if( is_string( $input_type ) )
				$this->cols[ $id ]->input( $input_type );
			return $this->cols[ $id ];
		}


		/**
		 * @param $id
		 * @return hw_input_axis_col
		 */
		public function get_col( $id ){
			if( array_key_exists( $id, $this->cols ) )
				return $this->cols[ $id ];
			/** @var hw_input $this */
			return new hw_input_axis_col( '', $this );
		}


		/**
		 * @return array|hw_input_axis_col[]
		 */
		public function get_cols(){
			return $this->cols;
		}


		/**
		 * @return array
		 */
		public function get_col_ids(){
			return array_keys( $this->get_cols() );
		}


		/**
		 * @param $id
		 */
		public function remove_col( $id ){
		}


		/**
		 * @param $id
		 * @return bool
		 */
		public function has_col( $id ){
			return array_key_exists( $id, $this->cols );
		}


		/**
		 * @return bool
		 */
		public function have_cols(){
			return ( is_array( $this->get_cols() ) && count( $this->get_cols() ) > 0 );
		}

		///ROWS


		/**
		 * @return hw_input_axis_rows
		 */
		public function rows(){
			if( !$this->rows instanceof hw_input_axis_rows ){
				$this->rows = new hw_input_axis_rows( $this );
			}
			return $this->rows;
		}

	}