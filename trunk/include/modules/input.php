<?php


	class hw_input{

		/**
		 * @var array
		 */
		public $inputs = array();


		/**
		 * Подключить класс типа
		 * @param $type
		 */
		private function inc( $type ){
			$path = HIWEB_DIR_MODULES . '/input/' . $type . '.php';
			if( file_exists( $path ) && is_file( $path ) && is_readable( $path ) )
				include_once $path;
			else {
				hiweb()->console()->warn( 'Файла [' . $path . '] нет', true );
			}
		}


		/**
		 * @param null $id
		 * @param string $type
		 * @return hw_input_object
		 */
		public function make( $id = null, $type = 'text' ){
			$this->inc( $type );
			$className = 'hw_input_object_' . $type;
			if( !class_exists( $className ) ){
				hiweb()->console()->warn( 'Класса [' . $className . '] нет', true );
				$className = 'hw_input_object';
			}
			/** @var hw_input_object $newInput */
			$newInput = new $className( $id, $type );
			///
			$this->inputs[ $newInput->global_id() ] = $newInput;
			///
			return $newInput;
		}


		public function get( $id ){
			if( !array_key_exists( $id, $this->inputs ) ){
				$this->inputs[ $id ] = $this->make( $id );
			}
			return $this->inputs[ $id ];
		}


	}


	class hw_input_object{

		/** @var null|integer Глобалвьный ID */
		protected $global_id = null;
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
			$this->global_id = md5( implode( '+', array( $this->id, $this->type, microtime() ) ) );
		}


		public function __call( $name, $arguments ){
			hiweb()->console()->warn('Попытка образения к несуществующему методу ['.$name.']', true);
		}


		protected function set_id( $id ){
			if( !is_string( $id ) && !is_int( $id ) ){
				$this->id = hiweb()->string()->rand( 8, true, true, false );
			}else $this->id = $id;
			if( is_null( $this->name ) )
				$this->name = $this->id;
		}


		/**
		 * Установка глобального ID
		 */
		public function global_id(){
			return $this->global_id;
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


		public function type( $set = null ){
			if( is_string( $set ) ){
				$this->type = $set;
			}
			return $this->type;
		}


		/**
		 * Установить тэг NAME, либо
		 * @param null $name - установить имя поля
		 * @return string|hw_input
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
		 * @return string|hw_input
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
			return vsprintf( "<label><input type='$this->type' id='%s' name='%s' value='%s' placeholder='%s' /> $this->label</label>", $this->prepare_tags() );
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