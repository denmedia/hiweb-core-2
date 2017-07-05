<?php

	hiweb()->inputs()->register_type( 'image', 'hw_input_image' );
	hiweb()->fields()->register_content_type( 'image', function( $value, $size = 'thumbnail', $return_image_html = false ){
		if( !is_numeric( $value ) ) return false;
		if( $return_image_html ){
			return wp_get_attachment_image( $value, $size );
		}
		$R = wp_get_attachment_image_src( $value, $size );
		if( !is_array( $R ) || !array_key_exists( 0, $R ) ) return false;
		return $R[0];
	} );


	class hw_input_image extends hw_input{

		protected $attributes = [
			'width' => 250,
			'height' => 120
		];

		private $has_image = [];


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
					$img = $thumb[0];
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
			if( $img_url === false ) return false;
			$img_path = hiweb()->path()->url_to_path( $img_url );
			$this->has_image[ $key ] = file_exists( $img_path );
			return $this->has_image[ $key ];
		}


		/**
		 * @return string
		 */
		public function html(){
			if( !hiweb()->context()->is_backend_page() ){
				hiweb()->console()->error( __( 'Can not display INPUT [IMAGE], it works only in the back-End' ) );
				return '';
			}
			wp_enqueue_media();
			hiweb()->js( hiweb()->dir_js . '/input_image.js', [ 'jquery' ] );
			hiweb()->css( hiweb()->dir_css . '/input_image.css' );

			$attr_width = $this->attributes( 'width' );
			$attr_height = $this->attributes( 'height' );
			$preview = false;
			$image_small = true;
			if( $this->have_image() ){
				$preview = wp_get_attachment_image_src( $this->value(), [ $attr_width, $attr_height ] );
				$image_small = !( $attr_width <= $preview[1] || $attr_height <= $preview[2] );
			}

			ob_start();
			?>
			<div class="hw-input-image" id="<?= $this->id() ?>" data-has-image="<?= $this->have_image() ? '1' : '0' ?>" data-image-small="<?= $image_small ? '1' : '0' ?>">
				<input type="hidden" <?= $this->tags_html() ?> value="<?= ( $this->has_image ? $this->value() : '' ) ?>"/>
				<a href="#" class="image-select" title="<?= __( 'Select/Deselect image...' ) ?>" data-click="<?= ( $this->have_image() ? 'deselect' : 'select' ) ?>" style="width: <?= $attr_width ?>px; height: <?= $attr_height ?>px;">
					<div class="thumbnail" style="<?= $this->have_image() ? 'background-image:url(' . $preview[0] . ')' : '' ?>"></div>
					<div class="overlay"></div>
					<i class="dashicons dashicons-format-image" data-icon="select"></i>
					<i class="dashicons dashicons-dismiss" data-icon="deselect"></i>
				</a>
			</div>
			<?php
			return ob_get_clean();
		}

	}