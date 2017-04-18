<?php


	/**
	 * Created by PhpStorm.
	 * User: denmedia
	 * Date: 25.02.2017
	 * Time: 15:38
	 */
	trait hw_input_value{

		protected $value = null;
		protected $value_original = null;

		/** @var null|array */
		protected $cols_source = null;
		/** @var null|array */
		protected $rows_source = null;

		protected $dimension = 0; //0 - 2

		protected $loop;
		protected $loop_current_row;


		protected function set_value( $value ){
			$this->value_original = $value;
			$this->value = [ [] ];
			switch( $this->dimension ){
				case 0:
					$this->value[0][0] = is_array( $value ) ? ( is_array( reset( $value ) ) ? current( current( $value ) ) : reset( $value ) ) : $value;
					break;
				case 1:
					$this->value[0] = is_array( $value ) ? $value : [ $value ];
					break;
				case 2:
					$this->value = is_array( $value ) ? ( is_array( reset( $value ) ) ? $value : [ $value ] ) : [ [ $value ] ];
					break;
			}
		}


		protected function get_value(){
			switch( $this->dimension ){
				case 1:
					return $this->value[0];
				case 2:
					return $this->value;
				default:
					return $this->value[0][0];
					break;
			}
		}


		/**
		 * Set demension from 0 to 3
		 * @param int $dimension
		 * @return bool
		 */
		protected function set_dimension( $dimension = 0 ){
			if( !is_numeric( $dimension ) ){
				hiweb()->console()->warn( sprintf( __( 'Demension [%s] is not a number (range 0 - 2)' ), $dimension ) );
				return false;
			}
			$dimension = intval( $dimension );
			if( $dimension < 0 || $dimension > 2 ){
				hiweb()->console()->warn( sprintf( __( 'Dimension [%s] not included in the range 0-2' ), $dimension ) );
				return false;
			}
			$this->dimension = $dimension;
			return true;
		}


		public function reset_row(){
			$this->loop = $this->value();
			return $this->loop;
		}


		/**
		 * Next the row
		 * @return mixed
		 */
		public function the_row(){
			if( !$this->have_rows() ){
				$this->reset_row();
			}
			$this->loop_current_row = array_shift( $this->loop );
			return $this->loop_current_row;
		}


		/**
		 * @return bool
		 */
		public function have_rows(){
			if( !is_array( $this->loop ) )
				$this->reset_row();
			return ( $this->dimension > 0 && is_array( $this->loop ) && count( $this->loop ) > 0 );
		}


		/**
		 * @return array|mixed
		 */
		public function get_rows(){
			$R = [];
			if( $this->have_rows() ){
				if( $this->dimension == 1 ){
					return is_array( $this->value ) ? reset( $this->value ) : array();
				} else {
					return $this->value();
				}
			}
			return $R;
		}


		/**
		 * @return mixed
		 */
		public function current_row(){
			return $this->loop_current_row;
		}


		/**
		 * @param        $idOrName
		 * @param string $type
		 * @return hw_field
		 */
		public function add_col( $idOrName, $type = 'text' ){
			$field = hiweb()->field( $idOrName, $type );
			$field->input()->tags['data-col-id'] = $field->input()->name;
			$field->input()->name = false;
			$this->cols_source[ $idOrName ] = $field;
			return $this->cols_source[ $idOrName ];
		}


		/**
		 * @return array
		 */
		public function get_col_ids(){
			$R = array();
			if( is_array( $this->value ) ){
				foreach( $this->value as $row ){
					if( !is_array( $row ) )
						continue;
					foreach( $row as $key => $val ){
						if( !array_key_exists( $key, $R ) )
							$R[ $key ] = '';
					}
				}
			}
			return array_keys( $R );
		}


		/**
		 * @return bool
		 */
		public function have_cols(){
			return ( is_array( $this->cols_source ) && count( $this->cols_source ) > 0 );
		}


		/**
		 * @return array|hw_field[]
		 */
		public function get_cols(){
			return $this->have_cols() ? $this->cols_source : array();
		}


		/**
		 * @return array
		 */
		public function get_row_ids(){
			if( !is_array( $this->value ) )
				return array();
			return array_keys( $this->value );
		}

	}