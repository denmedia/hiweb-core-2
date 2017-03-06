<?php

	hiweb()->inputs()->register_type( 'checkboxes', 'hw_input_checkboxes' );


	class hw_input_checkboxes extends hw_input{

		protected $dimension = 1;


		public function get_value(){
			$R = array();
			$rows = is_array( $this->value ) ? reset( $this->value ) : array();
			foreach( $this->options() as $key => $val ){
				$R[ $key ] = isset( $rows[ $key ] ) && ( $rows[ $key ] == 'on' || $rows[ $key ] );
			}
			return $R;
		}


		public function html(){
			$R = '';
			$value = $this->get_value();
			foreach( $this->options() as $key => $val ){
				$checked = array_key_exists( $key, $value ) && $value[ $key ];
				$R .= '<div class="item"><label><input type="checkbox" name="' . $this->name . '[' . $key . ']" ' . ( $checked ? 'checked="checked"' : '' ) . ' /> ' . $val . '</label></div>';
			}
			return '<div class="hw-field-checkboxes"><input type="hidden" name="' . $this->name . '[]">' . $R . '</div>';
		}

	}