<?php


	class hw_input_object_checkbox extends hw_input_object{

		public function get(){
			return vsprintf( "<label for='$this->id' data-global-id='$this->global_id'><input type='$this->type' id='%s' name='%s' value='%s' placeholder='%s' /> $this->label</label>", $this->prepare_tags() );
		}

	}