<?php


	/**
	 * Created by PhpStorm.
	 * User: denmedia
	 * Date: 27.05.2017
	 * Time: 19:35
	 */
	class hw_input_axis_rows{

		private $parent_object;

		private $rows = null;
		private $row_id = null;
		private $row = null;


		public function __construct( $parent_object ){
			$this->parent_object = $parent_object;
		}


		/**
		 * @return mixed
		 */
		public function reset_rows(){
			if( $this->parent_object instanceof hw_field_frontend ){
				if( is_array( $this->parent_object->value() ) && count( $this->parent_object->value() ) > 0 ){
					$this->rows = $this->parent_object->value();
				}
			}
			if( $this->parent_object instanceof hw_input ){
				if( is_array( $this->parent_object->value() ) && count( $this->parent_object->value() ) > 0 ){
					$this->rows = $this->parent_object->value();
				}
			}
			return $this->rows;
		}


		/**
		 * @return bool
		 */
		public function have_rows(){
			if( !is_array( $this->rows ) ){
				$this->reset_rows();
			}
			$R = is_array( $this->rows ) && count( $this->rows ) > 0;
			if( !$R )
				$this->reset_rows();
			return $R;
		}


		/**
		 * @return mixed|null
		 */
		public function the_row(){
			if( $this->have_rows() ){
				$this->row_id = key( $this->rows );
				if( $this->row_id === 0 ){
					if( $this->parent_object instanceof hw_field_frontend )
						$this->row_id = count( $this->parent_object->value() ) - count( $this->rows ) + 1;
					elseif( $this->parent_object instanceof hw_input )
						$this->row_id = count( $this->parent_object->value() ) - count( $this->rows ) + 1;
					else {
						//$this->row_id = 0;
					}
				}
				$this->row = array_shift( $this->rows );
				return $this->row;
			}
			return null;
		}


		/**
		 * @return null|mixed
		 */
		public function current_row(){
			return $this->row;
		}


		/**
		 * @return null|mixed
		 */
		public function current_row_id(){
			return $this->row_id;
		}


		/**
		 * @param $col_id
		 * @return bool
		 */
		public function has_col( $col_id ){
			if( $this->parent_object instanceof hw_field_frontend ){
				return $this->parent_object->input()->has_col( $col_id );
			} elseif( $this->parent_object instanceof hw_input ) {
				return $this->parent_object->has_col( $col_id );
			}
			return false;
		}


		/**
		 * @param $col_id
		 * @return hw_input
		 */
		public function get_sub_input( $col_id ){
			if( $this->parent_object instanceof hw_field_frontend ){
				$has_col = $this->parent_object->input()->has_col( $col_id );
				return $has_col ? $this->parent_object->input()->get_col( $col_id )->input_by_row( $this->row_id, $this->row ) : hiweb()->input();
			} elseif( $this->parent_object instanceof hw_input ) {
				$has_col = $this->parent_object->has_col( $col_id );
				return $has_col ? $this->parent_object->get_col( $col_id )->input_by_row( $this->row_id, $this->row ) : hiweb()->input();
			}
			return hiweb()->input();
		}


		/**
		 * @param $col_id
		 * @return mixed
		 */
		public function get_sub_input_value( $col_id ){
			return $this->get_sub_input( $col_id )->value();
		}


		/**
		 * @param      $col_id
		 * @param null $atts
		 * @param null $atts2
		 * @return mixed
		 */
		public function get_sub_input_content( $col_id, $atts = null, $atts2 = null ){
			return $this->get_sub_input( $col_id )->content( $atts, $atts2 );
		}
	}