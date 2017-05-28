<?php


	/**
	 * Created by PhpStorm.
	 * User: denmedia
	 * Date: 19.05.2017
	 * Time: 22:37
	 */
	trait hw_input_attributes{

		/** @var array Attributes array */
		protected $attributes = [];


		/**
		 * @param null|string|array $name_or_array
		 * @param null              $value
		 * @return hw_field|array|mixed|null
		 */
		public function attributes( $name_or_array = null, $value = null ){
			if( is_array( $name_or_array ) ){
				$this->attributes = array_merge( $this->attributes, $name_or_array );
				return $this;
			} elseif( !is_string( $name_or_array ) || trim( $name_or_array ) == '' ) {
				return $this->attributes;
			} elseif( is_null( $value ) ) {
				return array_key_exists( $name_or_array, $this->attributes ) ? $this->attributes[ $name_or_array ] : null;
			} else {
				$this->attributes[ $name_or_array ] = $value;
				return $this;
			}
		}

	}