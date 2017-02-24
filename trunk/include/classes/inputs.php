<?php


	class hw_inputs{


		use hw_hidden_methods_props;


		/**
		 * @var array
		 */
		public $types = array();


		public $dir_inputs = 'input';


		public function __construct(){
			$this->dir_inputs = hiweb()->dir_classes . '/' . $this->dir_inputs;
		}


		private function _init(){
			hiweb()->path()->include_dir( $this->dir_inputs );
		}


		/**
		 * Возвращает TRUE, если тип инпута существует
		 * @param $type
		 * @return bool
		 */
		public function has_type( $type ){
			return ( array_key_exists( $type, $this->types ) && is_array( $this->types[ $type ] ) && count( $this->types[ $type ] ) > 0 );
		}


		/**
		 * Создает новый экземпляр инпута и возвращает его
		 * @param string      $type
		 * @param bool|string $id
		 * @return hw_input
		 */
		public function create( $type = 'text', $id = false, $value = null ){
			if( $this->has_type( $type ) ){
				$classNames = array_reverse( $this->types[ $type ] );
				foreach( $classNames as $className ){
					if( !class_exists( $className ) ){
						hiweb()->console()->warn( 'Класс для инпута не найден [' . $className . ']', true );
						continue;
					}
					///Make input
					$class = new $className( $id );
					if( !$class instanceof hw_input ){
						hiweb()->console()->warn( 'Класс [' . $className . '] не является экземпляром интерфейса hw_input! Для этого назначте класс наследником:\n<?php class ' . $className . '  extends hw_input{ ... }' );
						continue;
					}
					if( !is_null( $value ) )
						$class->value = $value;
					$class->tags['type'] = $type;
					return $class;
				}
			}
			hiweb()->console()->warn( sprintf(__('Type of input [%s] not found','hw-core-2'), $type), true );
			///Make default input
			$class = new hw_input( $id );
			if( !is_null( $value ) )
				$class->value = $value;
			return $class;
		}


		/**
		 * Зарегистрировать тип инпута
		 * @param string $type
		 * @param string $className
		 * @param int    $priority - приоритет определяет какой класс откроется
		 */
		public function register_type( $type = 'text', $className, $priority = 10 ){
			$priority = intval( $priority );
			if( !array_key_exists( $type, $this->types ) ){
				$this->types[ $type ] = array();
			}
			if( array_key_exists( $priority, $this->types[ $type ] ) ){
				$movedClassName = $this->types[ $type ][ $priority ];
				$this->register_type( $type, $priority + 1, $movedClassName );
			}
			$this->types[ $type ][ $priority ] = $className;
		}


	}


	class hw_input{

		use hw_hidden_methods_props;
		use hw_input_rows;
		use hw_input_cols;

		/** @var string */
		public $id;
		/** @var string */
		public $name;
		/** @var string|array|bool */
		public $value;
		/** @var string */
		public $placeholder;
		/** @var string */
		protected $type;
		/** @var array */
		public $tags = array();
		/** @var array */
		protected $options = array();


		public function __construct( $id = false ){
			$this->set_id( $id );
		}


		protected function set_id( $id ){
			if( !is_string( $id ) && !is_int( $id ) ){
				$this->id = hiweb()->string()->rand( 8, true, true, false );
			} else $this->id = sanitize_file_name( strtolower( $id ) );
			if( is_null( $this->name ) )
				$this->name = $this->id;
		}


		/**
		 * Установить / Получить значение опции, при установке свойства, возвращаеться объект
		 * @param null $option_key
		 * @param null $option_value
		 * @return array|mixed
		 */
		public function options( $option_key = null, $option_value = null ){
			if( is_string( $option_key ) ){
				if( !is_null( $option_value ) ){
					$this->options[ $option_key ] = $option_value;
				}
				return $this->options[ $option_key ];
			} else {
				return $this->options;
			}
		}


		/**
		 * Возвращает строку тэгов
		 * @param bool|string|integer $index
		 * @param bool                $return_array - возвращаеть массив тегов
		 * @return array|string
		 */
		public function get_tags( $index = false, $return_array = false ){
			$R = array();
			$tags = $this->tags;
			$add_tag_keys = array(
				'id', 'name', 'placeholder', 'type'
			);
			foreach( $add_tag_keys as $add_tag_key ){
				if( !isset( $tags[ $add_tag_key ] ) )
					$tags[ $add_tag_key ] = $this->{$add_tag_key};
			}
			///
			if( $return_array )
				return $tags;
			///
			foreach( $tags as $key => $val ){
				if( is_null( $val ) )
					continue;
				if( is_string( $index ) && $this->have_rows() && array_key_exists( $key, array_flip( [ 'value', 'name', 'id' ] ) ) ){
					if( array_key_exists( $index, $this->value() ) ){
						switch( $key ){
							case 'name':
								$R[] = 'name="' . sanitize_file_name( $val ) . '[' . $index . ']"';
								break;
							case 'id':
								$R[] = 'id="' . sanitize_file_name( $val ) . '-' . sanitize_file_name( $index ) . '"';
								break;
							case 'value':
								$R[] = 'value="' . htmlentities( $val[ $index ], ENT_QUOTES, 'utf-8' ) . '"';
								break;
						}
					}
				} elseif( !is_array( $val ) && !is_object( $val ) ) {
					$R[] = $key . '="' . htmlentities( $val, ENT_QUOTES, 'utf-8' ) . '"';
				} else {
					$complexTag = array();
					foreach( $val as $subKey => $subVal ){
						if( is_array( $subVal ) || is_object( $subVal ) ){
							$complexTag[] = $subKey . ':' . json_encode( $subVal ) . '';
						} else {
							$complexTag[] = $subKey . ':' . htmlentities( $subVal, ENT_QUOTES, 'utf-8' ) . '';
						}
					}
					$R[] = $key . '="' . implode( ';', $complexTag ) . '"';
				}
			}
			return implode( ' ', $R );
		}


		/**
		 * Возвращает / устанавливает значение
		 * @param null $set - установить значение
		 * @return mixed
		 */
		public function value( $set = null ){
			if( is_null( $set ) ){
				return $this->value;
			}
			$this->value = $set;
			return $this->value();
		}


		/**
		 * Возвращает HTML
		 * @param null $arguments - Дополнительные аргументы
		 * @return string
		 */
		public function html(){
			if( $this->have_rows() ){
				$R = '';
				if( $this->have_cols() ){
					foreach( array_keys( $this->value() ) as $row_key ){
						foreach( array_keys( $this->get_cols() ) as $col_key ){
							//TODO!!!
						}
					}
					$R = '';
				} else {
					foreach( $this->value() as $row_key => $row_val ){
						if( is_array( $row_val ) || is_object( $row_val ) ){
							hiweb()->console()->warn( [ 'В строке вывода инпута попался массив или объект', $row_val ], true );
						} else {
							$R .= '<p><input ' . $this->get_tags( $row_key ) . ' value="' . htmlentities( $row_val, ENT_QUOTES, 'utf-8' ) . '"/></p>';
						}
					}
				}
			} else {
				$R = '<input ' . $this->get_tags() . ' value="' .$this->value().'"/>';
			}
			return $R;
		}


		/**
		 * Выводит HTML
		 * @param null $arguments - Дополнительные аргументы
		 * @return string
		 */
		public function the(){
			$html = $this->html();
			echo $html;
			return $html;
		}


		/**
		 * @param $new_id
		 * @return $this
		 */
		public function copy( $new_id ){
			$new_input = clone $this;
			$new_input->id = $new_id;
			$new_input->name = $new_id;
			hiweb()->inputs()->put( $new_input );
			return $new_input;
		}


		/**
		 * Возвращает TRUE, если имеются поля
		 * @return bool|int
		 */
		public function have_rows(){
			return ( is_array( $this->value ) ? count( $this->value ) : false );
		}


	}