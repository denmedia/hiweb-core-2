<?php

	hiweb()->inputs()->register_type( 'image', 'hw_input_image' );


	class hw_input_image extends hw_input{

		protected $options = array(
			'width' => 278, 'height' => 120
		);

		private $has_image = array();


		/**
		 * Возвращает URL до изображения
		 * @param string $size
		 * @return bool|string
		 */
		private function get_src( $size = 'thumbnail' ){
			$img = false;
			if( is_numeric( $this->value() ) ){
				$thumb = wp_get_attachment_image_src( $this->value(), $size );
				if( is_array( $thumb ) ){
					$img = reset( $thumb );
				}
			}
			return strpos( $img, 'http' ) === 0 ? $img : false;
		}


		/**
		 * Возвращает TRUE, если файл существует
		 * @param string $size
		 * @return bool
		 */
		public function have_image( $size = 'thumbnail' ){
			$key = json_encode( $size );
			if( array_key_exists( $key, $this->has_image ) ){
				return $this->has_image[ $key ];
			}
			$img_url = $this->get_src( $size );
			if( $img_url === false )
				return false;
			$img_path = hiweb()->path()->url_to_path( $img_url );
			$this->has_image[ $key ] = file_exists( $img_path );
			return $this->has_image[ $key ];
		}


		/**
		 * @return string
		 */
		public function html(){
			if( !hiweb()->context()->is_backend_page() ){
				hiweb()->console()->error( 'Невозможно показать инпут IMAGE, он работает только в бэк-энде' );
				return '';
			}
			wp_enqueue_media();
			hiweb()->js( hiweb()->dir_js . '/input_image.js', array( 'jquery' ) );
			hiweb()->css( hiweb()->dir_css . '/input_image.css' );

			return '<div class="hw-input-image" id="' . $this->id . '" data-has-image="' . ( $this->have_image( [ $this->options( 'width' ), $this->options( 'height' ) ] ) ? '1' : '0' ) . '">
<input type="hidden" ' . $this->get_tags() . '/>
	<a href="#" class="button image-select" title="Select/Deselect image..." data-click="' . ( $this->have_image( [ $this->options( 'width' ), $this->options( 'height' ) ] ) ? 'deselect' : 'select' ) . '" style="width: ' . $this->options( 'width' ) . 'px; height: ' . $this->options( 'height' ) . 'px; ' . ( $this->have_image() ? 'background-image:url(' . $this->get_src( [ $this->options( 'width' ), $this->options( 'height' ) ] ) . ')' : '' ) . '">
	<i class="dashicons dashicons-format-image" data-icon="select"></i>
	<i class="dashicons dashicons-no-alt" data-icon="deselect"></i>
	</a>
</div>';
		}

	}