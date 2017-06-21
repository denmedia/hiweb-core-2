<?php

	hiweb()->inputs()->register_type( 'bool', 'hw_input_checkbox' );
	hiweb()->inputs()->register_type( 'check', 'hw_input_checkbox' );
	hiweb()->inputs()->register_type( 'checkbox', 'hw_input_checkbox' );
	hiweb()->inputs()->register_type( 'radio', 'hw_input_checkbox' );
	hiweb()->inputs()->register_type( 'radiobutton', 'hw_input_checkbox' );


	class hw_input_checkbox extends hw_input{

		use hw_hidden_methods_props;


		/**
		 * @return string
		 */
		private function get_sub_type(){
			$sub_type = [ 'bool' => 'checkbox', 'check' => 'chekbox', 'checkbox' => 'checkbox', 'radio' => 'radiobutton', 'radiobutton' => 'radiobutton' ];
			return $sub_type[ $this->type() ];
		}

		public function get_value(){
			return $this->value != '';
		}


		public function html(){
			if( !hiweb()->context()->is_backend_page() ){
				hiweb()->console()->error( __( 'Can not display INPUT [' . $this->type() . '], it works only in the back-End' ) );
				return '';
			}
			wp_enqueue_media();
			hiweb()->css( hiweb()->dir_css . '/input-checkbox.css' );
			ob_start();
			?>
			<div class="hw-input-checkbox">
				<input type="<?= $this->get_sub_type() ?>" class="<?=$this->get_sub_type()?>" id="<?=$this->id()?>" name="<?=$this->name()?>" <?=$this->value() ? 'checked="checked"' : ''?>>
				<label for="<?=$this->id()?>"><?= $this->attributes( 'label' ) ?></label>
			</div>
			<?php
			return ob_get_clean();
		}

	}