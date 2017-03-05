<?php

	hiweb()->inputs()->register_type('text','hw_input_text');

	class hw_input_text extends hw_input{

		public function __construct( $id = false ){ parent::__construct( $id ); }

	}