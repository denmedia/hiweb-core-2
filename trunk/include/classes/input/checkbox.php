<?php

	hiweb()->inputs()->register_type( 'checkbox', 'hw_input_checkbox' );


	class hw_input_checkbox extends hw_input{


		public function html( $arguments = null ){
			hiweb()->console( $this->value() );
			return '<input ' . $this->get_tags() . ' ' . ( $this->value() != '' ? 'checked' : '' ) . '/>';
		}

	}