<?php


	class hw_input_image extends hw_input{

		public function get(){
			hiweb()->js(HIWEB_URL_JS.'/input_image.js');
			return '<span class="button hw-input-image">TEST</span>';
		}

	}