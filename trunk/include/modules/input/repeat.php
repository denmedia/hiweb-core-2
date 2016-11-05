<?php


	class hw_input_repeat extends hw_input{

		/** @var hw_input[]|hw_input_checkbox[]|hw_input_image[] */
		private $fields = array();


		/**
		 * Установить повторяющиеся поля
		 * @param null|array $inputs
		 * @return hw_input[]|hw_input_checkbox[]|hw_input_image[]
		 */
		public function fields( $inputs = null ){
			if( is_array( $inputs ) ){
				/** @var hw_input $field */
				foreach( $inputs as $field ){
					$id = $field->id();
					$this->fields[ $id ] = $field->copy( hiweb()->string()->rand() );
					$this->fields[ $id ]->tags( 'data-col-id', $id );
					$this->fields[ $id ]->tags( 'name' );
				}
			}
			return $this->fields;
		}
		
		public function add_field($id, $type = 'text'){
			$input = hiweb()->input($id,$type);
			$input->tags( 'data-col-id', $input->id() );
			$input->tags( 'name' );
			$this->fields[$input->id()] = $input;
			return $input;
		}


		private function get_rowNull(){
			$R = '<th class="drag"><span class="spacer"></span></th>';
			if( is_array( $this->fields ) && count( $this->fields ) > 0 ){
				$widthFull = 0;
				foreach( $this->fields as $field ){
					$widthFull += $field->width();
				}
				foreach( $this->fields as $field ){
					$width = round( $field->width() / $widthFull * 100 );
					$R .= '<th class="field" style="width: ' . $width . '%">' . $field->title() . '</th>';
				}
			}else
				$R .= '<th><span class="spacer"></span></th>';
			$R .= '<th class="control"><span class="button button-small" data-click="add"><i class="dashicons dashicons-plus-alt"/></span></th>';
			return '<thead><tr class="row">' . $R . '</tr>' . $this->get_row( array(), array( 'source' ) ) . '</thead>';
		}


		private function get_row( $row = array(), $additionClass = array() ){
			if(!is_array($row)) return '';
			$R = '<th class="drag-handle" title="Move row"><i class="dashicons dashicons-sort"/></th>';
			if( is_array( $this->fields ) )
				foreach( $this->fields as $id => $field ){
				if( array_key_exists( $id, $row ) ){
						$field->value( $row[ $id ] );
					}
					$R .= '<td class="field">' . $field->get() . '</td>';
				}else $R .= '<td></td>';
			$R .= '<th class="control"><span class="button button-small button-link" data-click="remove" title="Remove row"><i class="dashicons dashicons-dismiss"/></span></th>';
			return '<tr class="row ' . implode( $additionClass ) . '">' . $R . '</tr>';
		}


		public function get( $arguments = null ){
			hiweb()->css( HIWEB_URL_CSS . '/input_repeat.css' );
			hiweb()->js( HIWEB_URL_JS . '/input_repeat.js', array( 'jquery-ui-sortable' ) );
			$R = '';
			$R .= $this->get_rowNull() . '<tbody class="wrap">';
			if( $this->have_rows() != false ){
				foreach( $this->value() as $row ){
					$R .= $this->get_row( $row );
				}
			}
			$R .= '<tr class="message" style=" ' . ( $this->have_rows() == 0 ? '' : 'display:none;' ) . '"><td colspan="' . ( count( $this->fields ) + 2 ) . '">For add first row, press PLUS button...</td></tr>';
			$R .= '</tbody>';
			return '<div class="hw-input-repeat" id="' . $this->id . '"><table>' . $R . '</table></div>';
		}

	}