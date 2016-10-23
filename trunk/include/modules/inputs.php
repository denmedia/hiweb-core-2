<?php


	class hw_inputs{

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
				include_once $path;else{
				hiweb()->console()->warn( 'Файла [' . $path . '] нет', true );
			}
		}


		/**
		 * @param null $id
		 * @param string $type
		 * @return hw_input
		 */
		private function make( $id = null, $type = 'text' ){
			$this->inc( $type );
			$className = 'hw_input_' . $type;
			if( !class_exists( $className ) ){
				hiweb()->console()->warn( 'Класса [' . $className . '] нет', true );
				$className = 'hw_input';
			}
			/** @var hw_input $newInput */
			$newInput = new $className( $id, $type );
			///
			$this->inputs[ $newInput->global_id() ] = $newInput;
			///
			return $newInput;
		}


		public function get( $id, $type = 'text' ){
			if( !array_key_exists( $id, $this->inputs ) ){
				$this->inputs[ $id ] = $this->make( $id, $type );
			}
			return $this->inputs[ $id ];
		}


	}


	class hw_input{

		/** @var null|integer Глобалвьный ID */
		protected $global_id = null;
		/** @var string */
		protected $id;
		/** @var string */
		protected $name;
		/** @var string */
		protected $value;
		/** @var string */
		protected $label;
		/** @var string */
		protected $description;
		/** @var string */
		protected $title;
		/** @var string */
		protected $placeholder;
		/** @var string */
		protected $type;
		/** @var array */
		protected $tags = array();


		public function __construct( $id = null, $type = 'text' ){
			$this->set_id( $id );
			$this->type = trim($type) == '' ? 'text' : $type;
			$this->global_id = md5( implode( '+', array( $this->id, $this->type, microtime() ) ) );
		}


		public function __call( $name, $arguments ){
			hiweb()->console()->warn( 'Попытка образения к несуществующему методу [' . $name . ']', true );
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


		/**
		 * Возвращает ID
		 * @return string
		 */
		public function id(){
			return $this->id;
		}


		/**
		 * @param null $set
		 * @return string|$this
		 */
		public function description( $set = null ){
			if( !is_null( $set ) ){
				$this->description = $set;
				return $this;
			}
			return $this->description;
		}


		/**
		 * Усттановить дополнительные тэги HTML, например array('class' => 'class_name')
		 * @param null $set
		 * @return array|$this
		 */
		public function tags( $set = null ){
			if( is_string( $set ) ){
				$this->tags = $set;
				return $this;
			}
			return $this->tags;
		}


		/**
		 * @param null $set
		 * @return $this|string
		 */
		public function type( $set = null ){
			if( is_string( $set ) ){
				$this->type = $set;
				return $this;
			}
			return $this->type;
		}


		/**
		 * Установить тэг NAME, либо
		 * @param null $name - установить имя поля
		 * @return string|$this
		 */
		public function name( $name = null ){
			if( !is_null( $name ) ){
				$this->name = $name;
				return $this;
			}else return $this->name;
		}


		/**
		 * Установить TITLE
		 * @param null $title - установить название поля
		 * @return string|hw_input
		 */
		public function title( $title = null ){
			if( !is_null( $title ) ){
				$this->title = $title;
				return $this;
			}else return $this->title;
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
			return '<input type="'.$this->type.'" id="' . $this->id . '" name="' . $this->name . '" title="' . $this->label . '" value="' . htmlentities( $this->value, ENT_QUOTES, 'utf-8' ) . '"/>';
		}


		/**
		 * Выводит HTML
		 * @return string
		 */
		public function the(){
			$html = $this->get();
			echo $html;
			return $html;
		}

	}