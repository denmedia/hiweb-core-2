<?php

	hiweb()->inputs()->register_type( 'color', 'hw_input_color' );


	class hw_input_color extends hw_input{

		public function html(){
			ob_start();
			$this->tag_add('type','text');
			$this->tag_add('data-type-color',' ');
			$this->tag_add('value',$this->value());
			hiweb()->js(hiweb()->dir_js.'/input-color.js');
			hiweb()->js(hiweb()->dir_vendors.'/tinyColorPicker/jqColorPicker.min.js');
			?>
			<input <?= $this->tags_html() ?>>
			<?php
			return ob_get_clean();
		}

	}