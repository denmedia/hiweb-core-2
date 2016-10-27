<?php


	class hw_forms{

		/** @var hw_form[] */
		private $forms = array();


		/**
		 * @param $id
		 * @return hw_form
		 */
		public function get( $id ){
			if( !array_key_exists( $id, $this->forms ) ){
				$this->forms[ $id ] = new hw_form( $id );
			}
			return $this->forms[ $id ];
		}

	}
	

	class hw_form{

		public $id = '';
		public $action = '';
		public $method = 'get';
		public $template = 'default';
		
		/** @var hw_input[] */
		private $fields = array();


		public function __construct( $id = '' ){
			$this->id = $id;
		}
		

		/**
		 * @param $idOrField
		 * @param string $type
		 * @return hw_input|hw_input_image|hw_input_repeat|hw_input_gallery
		 */
		public function field( $idOrField, $type = 'text' ){
			if( !array_key_exists( $idOrField, $this->fields ) ){
				$this->fields[ $idOrField ] = hiweb()->inputs()->get( $idOrField, $type );
			}
			return $this->fields[ $idOrField ];
		}


		public function fields(){
			return is_array( $this->fields ) ? $this->fields : array();
		}


		/**
		 * Возвращает HTML формы
		 * @return string
		 */
		public function get(){
			///Form Tags
			$formTagsPairs = array(
				'class' => 'hw-form', 'action' => $this->action, 'method' => $this->method, 'id' => $this->id
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
			///
			$templatePath = HIWEB_DIR_MODULES . '/forms/' . $this->template . '.php';
			if( !file_exists( $templatePath ) )
				$templatePath = HIWEB_DIR_MODULES . '/forms/default.php';
			ob_start();
			include $templatePath;
			$R = ob_get_clean();
			///
			return '<form ' . implode( ' ', $formTags ) . '>' . $R . '</form>';
		}
		

		/**
		 * @return string
		 */
		public function the(){
			$content = $this->get();
			echo $content;
			return $content;
		}
		
	}
	
	
	