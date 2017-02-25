<?php


	class hw_input_checkbox extends hw_input{

		public function value( $value = null ){
			return trim( parent::value() ) != '' && parent::value() != false;
		}


		public function html( $arguments = null ){
			return '<input ' . $this->get_tags() . ' ' . ( $this->value() ? 'checked' : '' ) . '/>';
		}

	}