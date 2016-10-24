<?php


	class hw_input_repeat extends hw_input{

		private $fields = array();


		public function cols( $fields ){
			if( is_array( $fields ) ){
				foreach( $fields as $field ){
					$field->tags( 'data-col-id', $field->id() );
					$field->tags( 'name' );
				}
				$this->fields = $fields;
			}
		}


		public function get(){
			//todo
			hiweb()->console( $this->value() );
			hiweb()->css( HIWEB_URL_CSS . '/input_repeat.css' );
			hiweb()->js( HIWEB_URL_JS . '/input_repeat.js', array( 'jquery-ui-sortable' ) );
			$R = '<div class="hw-input-repeat" data-name="' . $this->name . '"><input type="hidden" id="' . $this->id . '" name="' . $this->name . '">';
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
				foreach( $value as $row_index => $rows ){
					$R .= '<tr><td></td>';
					if( is_array( $this->fields ) && is_array( $rows ) )
						foreach( $this->fields as $field ){
							$field->tags( 'name', $this->id() . '[' . $row_index . '][' . $field->id() . ']' );
							if( array_key_exists( $field->id(), $rows ) )
								$field->value( $rows[ $field->id() ] );else $field->value( '' );
							$R .= '<td>' . $field->get() . '</td>';
						}
					$R .= '<td></td></tr>';
				}
			}else{
				$R .= '<tr data-help="first"><td colspan="' . ( count( $this->fields ) + 2 ) . '">Нажмите кнопку <span data-click="add"><i class="dashicons dashicons-plus-alt"></i></span>, чтобы добавить первое поле</tr>';
			}
			///
			$R .= '</tbody></table></div>';
			return $R;
		}

	}