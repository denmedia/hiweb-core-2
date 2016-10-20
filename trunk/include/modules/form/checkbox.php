<?php


	class hw_form_input_checkbox extends hw_form_input{

		public function get(){
			return vsprintf( "<input type='$this->type' id='%s' name='%s' value='%s' placeholder='%s' />", $this->prepare_tags() );
		}

	}