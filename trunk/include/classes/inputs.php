<?php

	require_once 'inputs/hw_input_attributes.php';
	require_once 'inputs/hw_input_value.php';
	require_once 'inputs/hw_input_tags.php';
	require_once 'inputs/hw_input_axis.php';
	require_once 'inputs/hw_input_axis_rows.php';
	require_once 'inputs/hw_input.php';


	class hw_inputs{


		use hw_hidden_methods_props;


		/**
		 * @var array
		 */
		public $types = array();


		public $dir_inputs = 'inputs';


		public function __construct(){
			$this->dir_inputs = hiweb()->dir_include . '/' . $this->dir_inputs;
		}


		private function _init(){
			$R = hiweb()->path()->include_dir( $this->dir_inputs );
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
		 * @param null        $value
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
					$class = new $className( $id, $type );
					if( !$class instanceof hw_input ){
						hiweb()->console()->warn( sprintf( __( 'Class [%s] dosen\'t extends hw_input! For this class Assign heir:\n<?php class %s  extends hw_input{ ... }' ), $className ) );
						continue;
					}
					if( !is_null( $value ) )
						$class->value = $value;
					return $class;
				}
			}
			hiweb()->console()->warn( sprintf( __( 'Type of input [%s] not found', 'hw-core-2' ), $type ), 0 );
			///Make default input
			$class = new hw_input( $id, $type );
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