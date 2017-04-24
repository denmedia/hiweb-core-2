<?php

	hiweb()->inputs()->register_type( 'text', 'hw_input_text' );
	hiweb()->fields()->register_content_type('text', function($value){
		return $value;
	});


	class hw_input_text extends hw_input{

		public function __construct( $id = false ){ parent::__construct( $id ); }

	}