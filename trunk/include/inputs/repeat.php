<?php

	hiweb()->inputs()->register_type( 'repeat', 'hw_input_repeat' );


	class hw_input_repeat extends hw_input{


		public function _init(){
			parent::_init();
			$this->dimension = 2;
		}


		private function get_adminRowNull(){
			$R = '<th class="drag"><span class="spacer"></span></th>';
			if( $this->have_cols() ){
				$widthFull = 1;
				foreach( $this->get_cols() as $field ){
					$widthFull += 100;//$field->width();
				}
				foreach( $this->get_cols() as $field ){
					$width = round( 100 / $widthFull * 100 );
					$R .= '<th class="field">' . $field->name() . ( trim( $field->description() ) != '' ? '<p class="description">' . $field->description() . '</p>' : '' ) . '</th>';
				}
			} else
				$R .= '<th><span class="spacer"></span></th>';
			$R .= '<th class="control"><span class="button button-small" data-click="add"><i class="dashicons dashicons-plus-alt"/></span></th>';
			return '<thead><tr class="row">' . $R . '</tr>' . $this->get_adminRow( array(), array( 'source' ) ) . '</thead>';
		}


		private function get_adminRow( $row = array(), $additionClass = array() ){
			if( !is_array( $row ) )
				return '';
			$R = '<th class="drag-handle" title="Move row"><i class="dashicons dashicons-sort"/></th>';
			if( $this->have_cols() )
				foreach( $this->get_cols() as $id => $field ){
					if( array_key_exists( $id, $row ) ){
						$field->value( $row[ $id ] );
					} elseif( array_key_exists( $id, array_values( $row ) ) ) {
						$field->value( hiweb()->arrays()->get_index( $row, $id ) );
					}
					$R .= '<td class="field">' . $field->html() . '</td>';
				} else
				$R .= '<td></td>';
			$R .= '<th class="control"><span class="button button-small button-link" data-click="remove" title="Remove row"><i class="dashicons dashicons-dismiss"/></span></th>';
			return '<tr class="row ' . implode( $additionClass ) . '">' . $R . '</tr>';
		}


		public function html( $arguments = null ){
			hiweb()->css( hiweb()->url_css . '/input_repeat.css' );
			hiweb()->js( hiweb()->url_js . '/input_repeat.js', array( 'jquery-ui-sortable' ) );
			///
			if( !$this->have_cols() ){
				return '<div class="hw-input-repeat"><p class="empty-message">' . sprintf( __( 'For repeat input [%s] not add col fields. For that do this: <code>$field->add_col(\'my-col\')</code>' ), $this->id ) . '</p></div>';
			} else {
				$R = '';
				$R .= $this->get_adminRowNull() . '<tbody class="wrap">';
					foreach( $this->value() as $row ){
						$R .= $this->get_adminRow( $row );
					}
				$R .= '<tr class="message" style="' . ( $this->have_rows() ? 'display: none;' : '' ) . '"><td colspan="' . ( count( $this->get_cols() ) + 2 ) . '">' . __( 'For add first row, press PLUS button...', 'hw-core-2' ) . '</td></tr>';
				$R .= '</tbody>';
				return '<div class="hw-input-repeat" id="' . $this->id . '"><table>' . $R . '</table></div>';
			}
		}

	}