<?php

	hiweb()->inputs()->register_type( 'textarea', 'hw_input_textarea' );


	class hw_input_textarea extends hw_input{

		public function html(){
			hiweb()->css( hiweb()->url_css . '/input-textarea.css' );
			return '<textarea class="input-textarea" ' . $this->get_tags() . '>' . $this->value() . '</textarea>';
		}

	}