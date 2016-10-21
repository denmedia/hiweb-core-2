<?php


	class hw_input_object_repeat extends hw_input_object{

		private $fields = array();


		public function fields( $fields ){
			$this->fields = $fields;
		}


		public function get(){
			return hiweb()->path()->get_content(dirname(__FILE__).'/repeat_template.php');
		}

	}