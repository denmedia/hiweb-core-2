<?php

	hiweb()->inputs()->register_type( 'file', 'hw_input_file' );
	hiweb()->fields()->register_content_type( 'file', function( $value, $size = 'thumbnail', $return_file_html = false ){
		if( !is_numeric( $value ) ) return false;
		if( $return_file_html ){
			return wp_get_attachment_file( $value, $size );
		}
		$R = wp_get_attachment_file_src( $value, $size );
		if( !is_array( $R ) || !array_key_exists( 0, $R ) ) return false;
		return $R[0];
	} );


	class hw_input_file extends hw_input{

		private $has_file = [];


		/**
		 * Возвращает TRUE, если файл существует
		 * @return bool
		 */
		public function have_file(){
			$attachment_url = wp_get_attachment_url( $this->value() );
			$this->has_file[ $attachment_url ] = hiweb()->path()->is_readable( $attachment_url );
			return $this->has_file[ $attachment_url ];
		}


		/**
		 * @return string
		 */
		public function html(){
			if( !hiweb()->context()->is_backend_page() ){
				hiweb()->console()->error( __( 'Can not display INPUT [FILE], it works only in the back-End' ) );
				return '';
			}
			wp_enqueue_media();
			hiweb()->js( hiweb()->dir_js . '/input-file.js', [ 'jquery' ] );
			hiweb()->css( hiweb()->dir_css . '/input-file.css' );

			$attr_width = $this->attributes( 'width' );
			$attr_height = $this->attributes( 'height' );
			$preview = false;
			$file_small = true;
			if( $this->have_file() ){
				//$preview = wp_get_attachment_url( $this->value() );
				//$file_small = !( $attr_width <= $preview[1] || $attr_height <= $preview[2] );
			}

			ob_start();
			?>
			<div class="hw-input-file" id="<?= $this->id() ?>" data-has-file="<?= $this->have_file() ? '1' : '0' ?>">
				<input type="hidden" <?= $this->tags_html() ?> value="<?= ( $this->has_file ? $this->value() : '' ) ?>"/>
				<a href="#" class="file-select" title="<?= __( 'Select/Deselect file...' ) ?>" data-click="<?= ( $this->have_file() ? 'deselect' : 'select' ) ?>" style="width: 80px; height: 80px;">
					<div class="overlay"></div>
					<i class="dashicons dashicons-paperclip" data-icon="select"></i>
					<i class="dashicons dashicons-format-aside" data-icon="deselect"></i>
				</a>
			</div>
			<?php
			return ob_get_clean();
		}

	}