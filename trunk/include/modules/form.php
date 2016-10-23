<?php


	class hw_form{

		/** @var hw_form_object[] */
		private $forms = array();


		/**
		 * @param $id
		 * @return hw_form_object
		 */
		public function get( $id ){
			if( !array_key_exists( $id, $this->forms ) ){
				$this->forms[ $id ] = new hw_form_object( $id );
			}
			return $this->forms[ $id ];
		}

	}
	

	class hw_form_object{

		public $id = '';
		public $action = '';
		public $method = 'get';
		
		/** @var hw_input[] */
		private $fields = array();


		public function __construct( $id = '' ){
			$this->id = $id;
		}
		

		/**
		 * @param $id
		 * @param string $type
		 * @return hw_input|hw_input__repeat
		 */
		public function field( $id, $type = 'text' ){
			if( !array_key_exists( $id, $this->fields ) ){
				$this->fields[ $id ] = hiweb()->inputs()->make( $id, $type );
			}
			return $this->fields[ $id ];
		}


		/**
		 * Возвращает HTML формы
		 * @return string
		 */
		public function get(){
			///Form Tags
			$formTagsPairs = array(
				'action' => $this->action, 'method' => $this->method, 'id' => $this->id
			);
			$formTags = array();
			foreach( $formTagsPairs as $key => $val ){
				if( trim( $val ) == '' )
					continue;
				if( is_numeric( $key ) ){
					$formTags[] = $val;
				}else{
					$formTags[] = $key . '="' . htmlentities( $val, ENT_QUOTES, 'utf-8' ) . '"';
				}
			}
			///Form Fileds
			$R = '';
			if( is_array( $this->fields ) )
				foreach( $this->fields as $id => $field ){
					$R .= '<div class="hw-form-field">' . $field->get() . '</div>';
				}
			return '<form ' . implode( ' ', $formTags ) . '>' . $R . '</form>';
		}
		
	}
	
	
	