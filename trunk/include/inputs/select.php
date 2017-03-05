<?php

	hiweb()->inputs()->register_type( 'select', 'hw_input_select' );


	class hw_input_select extends hw_input{

		public function html(){
			hiweb()->css(hiweb()->dir_css.'/input-select.css');
			$options = array();
			if( is_array( $this->options ) )
				$options = $this->options;
			$R = '';
			foreach( $options as $key => $val ){
				$selected = '';
				if(!is_null($this->value()) && ((!is_integer($key) && $key == $this->value()) || $val == $this->value()) ){
					$selected = 'selected';
				}
				if( is_integer( $key ) ){
					$R .= '<option '.$selected.' value="' . htmlentities( $val, ENT_QUOTES, 'UTF-8' ) . '">' . $val . '</option>';
				} else {
					$R .= '<option '.$selected.' value="' . htmlentities( $key, ENT_QUOTES, 'UTF-8' ) . '">' . $val . '</option>';
				}
			}
			return '<select class="hw-input-select" ' . $this->get_tags() . '>' . $R . '</select>';
		}

	}