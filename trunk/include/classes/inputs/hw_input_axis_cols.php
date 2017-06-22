<?php


	/**
	 * Created by PhpStorm.
	 * User: denmedia
	 * Date: 23.05.2017
	 * Time: 9:43
	 */
	class hw_input_axis_col{

		/** @var string */
		private $id = '';
		/** @var string */
		private $name = '';
		/** @var string */
		private $description = '';
		/** @var  hw_input */
		private $parent_input;
		/** @var hw_input */
		private $input;
		/** @var hw_input[] */
		private $inputs_by_row = [];
		/** @var int */
		private $width = 1;


		/**
		 * hw_input_axis_col constructor.
		 * @param string   $id
		 * @param hw_input $parent_input
		 */
		public function __construct( $id, hw_input $parent_input ){
			$this->id = $id;
			$this->parent_input = $parent_input;
		}


		/**
		 * @param hw_input $parent_input
		 */
		public function set_parent_input( hw_input $parent_input ){
			$this->parent_input = $parent_input;
		}


		/**
		 * @param null $number
		 * @return int|hw_input_axis_col
		 */
		public function width( $number = null ){
			if( !is_null( $number ) ){
				$this->width = floatval( $number );
				return $this;
			} else {
				return floatval( $this->width );
			}
		}


		/**
		 * @return bool
		 */
		public function has_input(){
			return ( is_object( $this->input ) && ( $this->input instanceof hw_input ) );
		}


		/**
		 * @param        $type
		 * @param string $value
		 * @return hw_input
		 */
		public function input( $type = null, $value = null ){
			if( !$this->input instanceof hw_input ){
				$this->input = hiweb()->inputs()->create( $type, $this->id, $value );
			}
			return $this->input;
		}


		/**
		 * @param        $row_id
		 * @param array  $row
		 * @return hw_input
		 */
		public function input_by_row( $row_id = null, $row = [] ){
			if( !array_key_exists( $row_id, $this->inputs_by_row ) ){
				$row_value = null;
				if( !is_string( $row_id ) && !is_numeric( $row_id ) ){
					///
				} elseif( array_key_exists( $this->id, $row ) ) {
					$row_value = $row[ $this->id ];
				} elseif( array_key_exists( $this->id, array_values( $row ) ) ) {
					$row_value = hiweb()->arrays()->get_index( $row, $this->id );
				}
				$input = clone $this->input();
				$input->value( $row_value );
				$input->id( $this->parent_input->name() . '-' . $this->id().'-'.rand(1000,9999) );
				$input->name( $this->parent_input->name() . '[' . $row_id . '][' . $this->id() . ']' );
				$this->inputs_by_row[ $row_id ] = $input;
			}
			return $this->inputs_by_row[ $row_id ];
		}


		/**
		 * @return string
		 */
		public function id(){
			return $this->id;
		}


		/**
		 * @param null|string $set
		 * @return string|hw_input_axis_col
		 */
		public function name( $set = null ){
			if( is_null( $set ) ){
				return $this->{__FUNCTION__};
			} else {
				$this->{__FUNCTION__} = $set;
				return $this;
			}
		}


		/**
		 * @param null|string $set
		 * @return string|hw_input_axis_col
		 */
		public function description( $set = null ){
			if( is_null( $set ) ){
				return $this->{__FUNCTION__};
			} else {
				$this->{__FUNCTION__} = $set;
				return $this;
			}
		}


		public function the_name(){
			echo $this->name;
		}


		public function the_description(){
			echo $this->description;
		}


	}