<?php

	hiweb()->inputs()->register_type( 'editor', 'hw_input_editor' );


	class hw_input_editor extends hw_input{

		public function html(){
			ob_start();
			wp_editor( $this->get_value(), $this->name, $settings = $this->options() );
			return ob_get_clean();
		}

	}