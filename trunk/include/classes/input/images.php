<?php

	hiweb()->inputs()->register_type( 'images', 'hw_input_images' );


	class hw_input_images extends hw_input{

		protected $options = array(
			'width' => 150, 'height' => 80
		);


		private function html_rowAdd( $right = true ){
			return '<a href="#add-' . ( $right ? 'right' : 'left' ) . '" title="Add images to ' . ( $right ? 'right' : 'left' ) . '" class="button" style="' . ( ( !$right && $this->have_rows() == 0 ) ? 'display: none;' : '' ) . '"><i class="dashicons dashicons-format-image" data-icon="select"></i></a>';
		}


		private function html_row( $img_id = null ){
			$img_url = wp_get_attachment_image_src( $img_id, [ $this->options( 'width' ), $this->options( 'height' ) ] );
			if( $img_url !== false ){
				$img_path = hiweb()->path()->url_to_path( $img_url[0] );
				if( file_exists( $img_path ) )
					$img_url = 'background-image: url(' . $img_url[0] . ');'; else $img_url = '';
			}
			return '<a href="#' . ( is_null( $img_id ) ? 'source' : 'image' ) . '" class="button" style="' . $img_url . '"><i class="dashicons dashicons-no-alt" data-icon="deselect"></i><input type="hidden" ' . ( trim( $img_url ) == '' ? 'data-name' : 'name' ) . '="' . $this->id . '[]" value="' . $img_id . '" /></a>';
		}


		public function html(){
			if( !hiweb()->context()->is_backend_page() ){
				hiweb()->console()->error( 'Невозможно показать инпут IMAGES, он работает только в бэк-энде' );
				return '';
			}
			wp_enqueue_media();
			hiweb()->js( hiweb()->url_js . '/input_images.js', array( 'jquery-ui-sortable' ) );
			hiweb()->css( hiweb()->url_css . '/input_images.css' );
			$R = '';
			if( $this->have_rows() === false || $this->have_rows() === 0 ){
			} else {
				foreach( $this->value as $img_id ){
					$R .= $this->html_row( $img_id );
				}
			}
			return '<div class="hw-input-gallery" data-id="' . $this->id . '">' . $this->html_rowAdd( 0 ) . $this->html_row() . '<div data-wrap>' . $R . '</div>' . $this->html_rowAdd( 1 ) . '<div class="clear"></div></div>';
		}

	}