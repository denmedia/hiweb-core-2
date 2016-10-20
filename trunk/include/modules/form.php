<?php


	class hw_form{

		/** @var hw_form_input[] */
		private $fields = array();


		public function input( $id ){
			if( !array_key_exists( $id, $this->fields ) ){
				$this->fields[ $id ] = new hw_form_input( $id );
			}
			return $this->fields[ $id ];
		}

	}


	class hw_form_input{

		/** @var  string */
		private $id;
		/** @var  string */
		private $name;
		/** @var  string */
		private $value;
		/** @var  string */
		private $label;
		/** @var string */
		private $placeholder;


		public function __construct( $id = null ){
			$this->set_id( $id );
		}


		protected function set_id( $id ){
			if( !is_string( $id ) && !is_int( $id ) ){
				$this->id = hiweb()->string()->rand( 8, true, true, false );
			} else $this->id = $id;
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
			return vsprintf( "<input type='text' id='%s' name='%s' value='%s' placeholder='%s' />", $this->prepare_tags() );
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