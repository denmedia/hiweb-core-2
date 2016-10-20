<?php


	class hw_form{

		/** @var hw_form_object[] */
		private $forms = array();


		public function get( $id ){
			if( !array_key_exists( $id, $this->forms ) ){
				$this->forms[ $id ] = new hw_form_object();
			}
			return $this->forms[ $id ];
		}

	}
	

	class hw_form_object{

		public $id = '';
		public $action = '';
		public $method = 'get';
		
		/** @var hw_form_input[] */
		private $fields = array();
		
		
		public function input( $id, $type = 'text' ){
			if( !array_key_exists( $id, $this->fields ) ){
				$className = 'hw_form_input_' . $type;
				$path = HIWEB_DIR_MODULES . '/form/' . $type . '.php';
				if( file_exists( $path ) && is_file( $path ) && is_readable( $path ) ){
					include_once $path;
				}
				if( class_exists( $className ) ){
					$this->fields[ $id ] = new $className( $id );
				}else{
					hiweb()->console()->warn( 'class [' . $className . '] not exist!', true );
					$this->fields[ $id ] = new hw_form_input( $id, $type );
				}
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
			if(is_array($this->fields)) foreach($this->fields as $id => $field){
				$R .= $field->get();
			}
			return '<form ' . implode( ' ', $formTags ) . '>' . $R . '</form>';
		}
		
	}
	
	
	class hw_form_input{
		
		/** @var  string */
		protected $id;
		/** @var  string */
		protected $name;
		/** @var  string */
		protected $value;
		/** @var  string */
		protected $label;
		/** @var string */
		protected $placeholder;
		/** @var string */
		protected $type;
		
		
		public function __construct( $id = null, $type = 'text' ){
			$this->set_id( $id );
			$this->type = $type;
		}
		
		
		protected function set_id( $id ){
			if( !is_string( $id ) && !is_int( $id ) ){
				$this->id = hiweb()->string()->rand( 8, true, true, false );
			}else $this->id = $id;
			if( is_null( $this->name ) )
				$this->name = $this->id;
		}
		
		
		protected function prepare_tags( $props = array( 'id', 'name', 'value', 'placeholder' ) ){
			$R = array();
			foreach( $props as $key ){
				if( property_exists( $this, $key ) ){
					$R[ $key ] = htmlentities( $this->{$key}, ENT_QUOTES, 'utf-8', false );
				}
			}
			return $R;
		}
		
		
		/**
		 * Возвращает ID
		 * @return string
		 */
		public function id(){
			return $this->id;
		}
		
		
		/**
		 * Установить тэг NAME, либо
		 * @param null $name - установить имя поля
		 * @return string|hw_form_input
		 */
		public function name( $name = null ){
			if( !is_null( $name ) ){
				$this->name = $name;
				return $this;
			}else return $this->name;
		}
		
		
		/**
		 * Установить LABEL
		 * @param null $label - установить название поля
		 * @return string|hw_form_input
		 */
		public function label( $label = null ){
			if( !is_null( $label ) ){
				$this->label = $label;
				return $this;
			}else return $this->label;
		}
		
		
		/**
		 * Установить VALUE, либо
		 * @param null $value - установить имя поля
		 * @return string|$this
		 */
		public function value( $value = null ){
			if( !is_null( $value ) ){
				$this->value = $value;
				return $this;
			}else return $this->value;
		}
		
		
		/**
		 * Установить PLACEHOLDER, либо
		 * @param null $placeholder - установить имя поля
		 * @return string|$this
		 */
		public function placholder( $placeholder = null ){
			if( !is_null( $placeholder ) ){
				$this->placeholder = $placeholder;
				return $this;
			}else return $this->placeholder;
		}
		
		
		/**
		 * Возвращает HTML
		 * @return string
		 */
		public function get(){
			return vsprintf( "<input type='$this->type' id='%s' name='%s' value='%s' placeholder='%s' />", $this->prepare_tags() );
		}
		
		
		/**
		 * Выводит HTML
		 * @return string
		 */
		public function get_echo(){
			$html = $this->get();
			echo $html;
			return $html;
		}
		
	}