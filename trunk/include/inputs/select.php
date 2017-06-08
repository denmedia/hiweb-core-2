<?php

	hiweb()->inputs()->register_type( 'select', 'hw_input_select' );


	class hw_input_select extends hw_input{

		public function html(){
			hiweb()->css( hiweb()->dir_css . '/input-select.css' );
			$options = array();
			if( is_array( $this->attributes( 'options' ) ) )
				$options = $this->attributes( 'options' );
			$R = '';
			foreach( $options as $key => $val ){
				$selected = '';
				if( !is_null( $this->value() ) && $key == $this->value() ){
					$selected = 'selected';
				}
				$R .= '<option ' . $selected . ' value="' . htmlentities( $key, ENT_QUOTES, 'UTF-8' ) . '">' . $val . '</option>';
			}
			return '<select class="hw-input-select" ' . $this->tags_html() . '>' . $R . '</select>';
		}

	}