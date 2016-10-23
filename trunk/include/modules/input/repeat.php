<?php


	class hw_input_repeat extends hw_input{

		private $fields = array();


		public function fields( $fields ){
			$this->fields = $fields;
		}


		public function value( $value = null ){
			return @json_decode( $this->value );
		}


		public function get(){
			hiweb()->css( HIWEB_URL_CSS . '/input_repeat.css' );
			hiweb()->js( HIWEB_URL_JS . '/input_repeat.js', array('jquery-ui-sortable') );
			$R = '<div class="hw-input-repeat"><input type="hidden" id="' . $this->id . '" name="' . $this->name . '">';
			$R .= '<table class="hw-input-repeat-table"><thead><th class="col-drag"></th>';
			///
			$S = '';
			if( is_array( $this->fields ) )
				foreach( $this->fields as $field ){
					$R .= '<th>' . $field->label() . '</th>';
					$S .= '<td>' . $field->get() . '</td>';
				}
			$S = '<tr data-source><td data-drag><i class="dashicons dashicons-sort"></i></td>' . $S . '<td><span data-click="remove"><i class="dashicons dashicons-no-alt"></i></button></td></tr>';
			///
			$R .= '<th class="col-control"><span data-click="add"><i class="dashicons dashicons-plus-alt"></i></span></th></thead><tbody data-wrap>' . $S;
			$value = $this->value();
			if( is_array( $value ) && count( $value ) > 0 ){

			}else{
				$R .= '<tr data-help="first"><td colspan="'.( count($this->fields) + 2 ).'">Нажмите кнопку <span data-click="add"><i class="dashicons dashicons-plus-alt"></i></span>, чтобы добавить первое поле</tr>';
			}
			///
			$R .= '</tbody></table></div>';
			return $R;
		}

	}