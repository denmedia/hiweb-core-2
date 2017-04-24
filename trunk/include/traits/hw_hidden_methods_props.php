<?php


	trait hw_hidden_methods_props{

		/**
		 * @param $name
		 * @param $args
		 * @return null
		 */
		public function __call( $name, $args ){
			if( method_exists( $this, $name ) ){
				return $this->{$name}( isset( $args[0] ) ? $args[0] : null, isset( $args[1] ) ? $args[1] : null, isset( $args[2] ) ? $args[2] : null );
			} elseif( method_exists( $this, '_call' ) ) {
				//???
				return $this->_call( $name, $args );
			} else {
				hiweb()->console()->warn( __CLASS__ . ' (trait hw_hidden_methods_props) : не найден метод [' . $name . ']!', true );
			}
			return null;
		}


		/**
		 * @param $name
		 * @return null
		 */
		public function __get( $name ){
			if( property_exists( $this, $name ) ){
				return $this->{$name};
			} else {
				hiweb()->console()->warn( __CLASS__ . ' (trait hw_hidden_methods_props) : не найдено свойство [' . $name . ']!', true );
				return null;
			}
		}


		/**
		 * @param $name
		 * @param $value
		 * @return null
		 */
		public function __set( $name, $value ){
			if( property_exists( $this, $name ) ){
				return $this->{$name} = $value;
			} else {
				hiweb()->console()->warn( __CLASS__ . ' (trait hw_hidden_methods_props) : не найдено свойство [' . $name . ']!', true );
				return null;
			}
		}
	}