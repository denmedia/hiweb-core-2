<?php

	hiweb()->inputs()->register_type( 'textarea', 'hw_input_textarea' );
	hiweb()->fields()->register_content_type( 'textarea', function( $value, $apply_filter_content = true ){
		return $apply_filter_content ? apply_filters( 'the_content', $value ) : nl2br( $value );
	} );


	class hw_input_textarea extends hw_input{

		public function html(){
			hiweb()->css( hiweb()->url_css . '/input-textarea.css' );
			return '<textarea class="input-textarea" ' . $this->tags_html() . '>' . $this->value() . '</textarea>';
		}

	}