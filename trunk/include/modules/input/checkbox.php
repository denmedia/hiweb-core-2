<?php


	class hw_input_checkbox extends hw_input{

		public function value( $value = null ){
			if(!is_null($value)){
				$this->value = $value;
				return $this;
			}
			return trim($this->value) != '' && $this->value != false;
		}


		public function get($arguments = null){
			return '<input '.$this->get_tags().' '.( $this->value() ? 'checked' : '' ).'/>';
		}

	}