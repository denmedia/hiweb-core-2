<?php


	trait hw_hidden_methods_props{

		public function __call( $name, $args ){
			if( method_exists( $this, $name ) ){
				return $this->{$name}( isset( $args[0] ) ? $args[0] : null, isset( $args[1] ) ? $args[1] : null, isset( $args[2] ) ? $args[2] : null );
			} elseif( property_exists( $this, $name ) ) {
				//hiweb()->console()->warn( __CLASS__ . ' (trait hw_hidden_methods_props) : не работает установка свойства [' . $name . ']!' );
				return $this->{$name} = $args;
			} elseif( method_exists( $this, '_call' ) ) {
				return $this->_call( $name, $args );
			} else {
				hiweb()->console()->warn( __CLASS__ . ' (trait hw_hidden_methods_props) : не найдены метод или свойство [' . $name . ']!', true );
			}
		}
	}