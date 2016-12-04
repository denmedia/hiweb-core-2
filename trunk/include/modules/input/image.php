<?php


	class hw_input_image extends hw_input{

		private $has_image = array();

		private $preview_width = 278;
		private $preview_height = 120;


		/**
		 * Возвращает URL до изображения
		 * @param string $size
		 * @return bool|string
		 */
		public function get_src( $size = 'thumbnail' ){
			$img = $this->default_value();
			if( is_numeric( $this->value() ) ){
				$thumb = wp_get_attachment_image_src( $this->value(), $size );
				if( is_array( $thumb ) ){
					$img = reset( $thumb );
				}
			}
			return strpos( $img, 'http' ) === 0 ? $img : false;
		}


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
		 * @param string|array $size - размер
		 * @return string
		 */
		public function get( $size = 'thumbnail' ){
			wp_enqueue_media();
			hiweb()->js( hiweb()->url_js . '/input_image.js' );
			hiweb()->css( hiweb()->url_css . '/input_image.css' );

			return '<div class="hw-input-image" id="' . $this->id . '" data-has-image="' . ( $this->have_image( $this->preview_size() ) ? '1' : '0' ) . '">
<input type="hidden" ' . $this->get_tags( array( 'value', 'id', 'name', 'title' ) ) . '/>
	<a href="#" class="button image-select" title="Select/Deselect image..." data-click="' . ( $this->have_image( $this->preview_size() ) ? 'deselect' : 'select' ) . '" style="width: ' . $this->preview_width . 'px; height: ' . $this->preview_height . 'px; ' . ( $this->have_image() ? 'background-image:url(' . $this->get_src($this->preview_size()) . ')' : '' ) . '">
	<i class="dashicons dashicons-format-image" data-icon="select"></i>
	<i class="dashicons dashicons-no-alt" data-icon="deselect"></i>
	</a>
</div>';
		}
		

		/**
		 * @param string|array $size - размер
		 * @return string
		 */
		public function get_content( $size = 'thumbnail' ){
			return wp_get_attachment_image($this->value(), $size);
		}
		
	}