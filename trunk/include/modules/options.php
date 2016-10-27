<?php


	class hw_options{
		/** @var hw_option[] */
		private $options = array();


		/**
		 * Возвращает опцию, по необходимости создавая ее
		 * @param string $id
		 * @param string $type
		 * @return hw_option
		 */
		public function get( $id, $type = 'text' ){
			if( !$this->is_exist( $id ) ){
				$this->options[ $id ] = new hw_option( $id, $type );
			}
			return $this->options[ $id ];
		}


		/**
		 * Возвращает TRUE, если опция существует
		 * @param $id
		 * @return bool
		 */
		public function is_exist( $id ){
			return array_key_exists( $id, $this->options );
		}

	}


	class hw_option{

		private $id;
		private $input;


		public function __construct( $id, $type = 'text' ){
			$this->id = $id;
			$this->input = hiweb()->input( $id, $type );
		}


	}