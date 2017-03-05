<?php

	hiweb()->inputs()->register_type('textarea', 'hw_input_textarea');

	class hw_input_textarea extends hw_input{

		public function html(){
			return '<textarea ' . $this->get_tags() . ' style="width: 100%" rows="4">' . $this->value() . '</textarea>';
		}

	}