<?php


	class hw_input_gallery extends hw_input{
		

		private $preview_width = 100;
		private $preview_height = 100;


		/**
		 * @param int $width
		 * @param int $height
		 * @return array|hw_input_image
		 */
		public function preview_size( $width = null, $height = null ){
			if( !is_null( $width ) ){
				if( is_numeric( $width ) )
					$this->preview_width = $width;
				if( is_numeric( $height ) )
					$this->preview_height = $height;
				return $this;
			}
			return array( $this->preview_width, $this->preview_height );
		}


		private function get_rowAdd( $right = true ){
			return '<a href="#add-' . ( $right ? 'right' : 'left' ) . '" title="Add images to ' . ( $right ? 'right' : 'left' ) . '" class="button" style="'.( ( !$right && $this->have_rows() == 0 ) ? 'display: none;' : '' ).'; width: '.$this->preview_width.'px; height: '.$this->preview_height.'px;"><i class="dashicons dashicons-format-image" data-icon="select"></i></a>';
		}


		private function get_row( $img_id = null ){
			$img_url = wp_get_attachment_image_src($img_id, array($this->preview_width,$this->preview_height));
			if($img_url !== false){
				$img_path = hiweb()->path()->url_to_path($img_url[0]);
				if(file_exists($img_path)) $img_url = 'background-image: url('.$img_url[0].');';
				else $img_url = '';
			}
			return '<a href="#' . ( is_null( $img_id ) ? 'source' : 'image' ) . '" class="button" style="'.$img_url.'; width: '.$this->preview_width.'px; height: '.$this->preview_height.'px;"><i class="dashicons dashicons-no-alt" data-icon="deselect"></i><input type="hidden" '.( trim($img_url) == '' ? 'data-name' : 'name' ).'="' . $this->id . '[]" value="'.$img_id.'" /></a>';
		}


		public function get( $arguments = null ){
			hiweb()->js( HIWEB_URL_JS . '/input_gallery.js', array( 'jquery-ui-sortable' ) );
			hiweb()->css( HIWEB_URL_CSS . '/input_gallery.css' );
			$R = '';
			if( $this->have_rows() === false || $this->have_rows() === 0 ){
			}else{
				foreach( $this->value as $img_id ){
					$R .= $this->get_row( $img_id );
				}
			}
			return '<div class="hw-input-gallery" data-id="' . $this->id . '">' . $this->get_rowAdd( 0 ) . $this->get_row() . '<div data-wrap>' . $R . '</div>' . $this->get_rowAdd( 1 ) . '<div class="clear"></div></div>';
		}

	}