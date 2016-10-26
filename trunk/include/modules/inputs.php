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


		/**
		 * @param $id
		 * @param string $type
		 * @return hw_input
		 */
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
		protected $default;
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
		/** @var int */
		protected $width = 100;


		public function __construct( $id = null, $type = 'text' ){
			$this->set_id( $id );
			$this->type = trim( $type ) == '' ? 'text' : $type;
			$this->global_id = md5( implode( '+', array( $this->id, $this->type, microtime() ) ) );
			$this->init();
		}


		protected function init(){
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
		 * Установить/получить ширину элемента в блоке
		 * @param null $width_percent
		 * @return int|hw_input|hw_input_image|hw_input_repeat
		 */
		public function width( $width_percent = null ){
			if( !is_null( $width_percent ) ){
				if( is_numeric( $width_percent ) ){
					$width_percent = intval( $width_percent );
					if( $width_percent < 1 ){
						$width_percent = 1;
					}
					if( $width_percent > 100 ){
						$width_percent = 100;
					}
					$this->width = $width_percent;
				}
				return $this;
			}
			return $this->width;
		}


		/**
		 * @param null $set
		 * @return string|hw_input|hw_input_repeat|hw_input_repeat
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
		 * @param null $key
		 * @param null $value
		 * @return $this|array|hw_input_repeat|hw_input_repeat
		 */
		public function tags( $key = null, $value = null ){
			if( is_string( $key ) ){
				$this->tags[ $key ] = $value;
				return $this;
			}
			return $this->tags;
		}


		/**
		 * @param null $set
		 * @return $this|string|hw_input_repeat|hw_input_repeat
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
		 * @return string|$this|hw_input_repeat|hw_input_repeat
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
		 * @return string|hw_input|hw_input_repeat|hw_input_repeat
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
		 * @return string|hw_input|hw_input_repeat|hw_input_repeat
		 */
		public function label( $label = null ){
			if( !is_null( $label ) ){
				$this->label = $label;
				if( trim( $this->title ) == '' ){
					$this->title = $label;
				}
				return $this;
			}else return $this->label;
		}


		/**
		 * Установить VALUE, либо возвращает его
		 * @param null $value - установить имя поля
		 * @return string|$this|hw_input_repeat|hw_input_repeat
		 */
		public function value( $value = null ){
			if( !is_null( $value ) ){
				$this->value = $value;
				return $this;
			}else{
			}
			return ( ( is_array( $this->value ) && count( $this->value ) == 0 ) || hiweb()->string()->is_empty( $this->value ) ) ? $this->default_value() : $this->value;
		}


		/**
		 * Выводит значение поля
		 */
		public function the_value(){
			echo $this->value();
		}


		/**
		 * Установить DEFAULT VALUE, либо возвращает его
		 * @param null $value - установить имя поля
		 * @return string|$this
		 */
		public function default_value( $value = null ){
			if( !is_null( $value ) ){
				$this->default = $value;
				if( trim( (string)$this->placeholder ) == '' )
					$this->placeholder = $value;
				return $this;
			}else return $this->default;
		}


		/**
		 * Выводит default-значение поля
		 */
		public function the_default(){
			echo $this->default_value();
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


		public function get_tags( array $tags = array( 'type', 'id', 'name', 'title', 'value', 'placeholder' ), $use_additionTags = true, $returnStr = true ){
			$R = array();
			if( $use_additionTags && is_array( $tags ) && is_array( $this->tags ) ){
				$tags = array_merge( $tags, array_keys( $this->tags ) );
			}
			foreach( $tags as $tag ){
				if( array_key_exists( $tag, $this->tags ) ){
					$R[ $tag ] = $this->tags[ $tag ];
				}else if( property_exists( $this, $tag ) ){
					$R[ $tag ] = $this->{$tag};
				}
			}
			if( !$returnStr )
				return $R;
			///
			$R2 = array();
			foreach( $R as $key => $val ){
				if( is_null( $val ) )
					continue;
				$R2[] = $key . '="' . htmlentities( $val, ENT_QUOTES, 'utf-8' ) . '"';
			}
			return implode( ' ', $R2 );
		}


		/**
		 * Возвращает HTML
		 * @param null $arguments - Дополнительные аргументы
		 * @return string
		 */
		public function get( $arguments = null ){
			return '<input ' . $this->get_tags() . '/>';
		}


		/**
		 * Выводит HTML
		 * @param null $arguments - Дополнительные аргументы
		 * @return string
		 */
		public function the( $arguments = null ){
			$html = $this->get();
			echo $html;
			return $html;
		}


		/**
		 * Возвращает
		 * @return string
		 */
		public function get_content( $arguments = null ){
			return $this->get( $arguments );
		}


		/**
		 * @return string
		 */
		public function the_content( $arguments = null ){
			$content = $this->get_content( $arguments );
			echo $content;
			return $content;
		}


		/**
		 * @param $new_id
		 * @return $this
		 */
		public function copy( $new_id ){
			hiweb()->inputs()->inputs[ $new_id ] = clone $this;
			hiweb()->inputs()->inputs[ $new_id ]->id = $new_id;
			hiweb()->inputs()->inputs[ $new_id ]->name = $new_id;
			return hiweb()->inputs()->inputs[ $new_id ];
		}


		/**
		 * Возвращает TRUE, если имеются поля
		 * @return bool|int
		 */
		public function have_rows(){
			return ( is_array( $this->value ) ? count( $this->value ) : false );
		}

	}