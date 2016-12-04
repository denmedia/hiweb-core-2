<?php
	
	
	class hw_inputs{
		
		/**
		 * @var array
		 */
		private $inputs = array();
		
		/** @var hw_inputs_home */
		private $root_home;
		/** @var array|hw_inputs_home[] */
		private $homes = array();
		
		
		public function __construct(){
			$this->root_home = new hw_inputs_home( '' );
		}
		
		
		/**
		 * Возвращает корневой дом
		 * @return hw_inputs_home
		 */
		public function home(){
			return $this->root_home;
		}
		
		
		/**
		 * @param $home_id
		 * @return hw_inputs_home
		 */
		public function give_home( $home_id ){
			$current_home = $this->root_home;
			if( !is_array( $home_id ) )
				$home_id = array( $home_id );
			$current_home_id_path = array();
			while( count( $home_id ) > 0 ){
				$sub_home_id = array_shift( $home_id );
				$current_home_id_path[] = $sub_home_id;
				if( !$current_home->children_home_exists( $sub_home_id ) ){ //Make new HOME
					///New HOME
					$new_global_id = $this->get_home_global_id_from_path( $current_home_id_path );
					$children_home = new hw_inputs_home( $sub_home_id, $current_home );
					$children_home->parent_homes_count = $current_home->parent_homes_count + 1;
					$this->homes[ $new_global_id ] = $children_home;
					$current_home->add_children_home( $children_home );
					///Swap to next
					$current_home = $children_home;
				} else { //Use exists HOME
					$current_home = $current_home->children_home( $sub_home_id );
				}
			}
			return $current_home;
		}
		
		
		/**
		 * @param $homeIdOrPath
		 * @return bool|hw_inputs_home
		 */
		public function get_home( $homeIdOrPath ){
			if( is_array( $homeIdOrPath ) )
				$homeIdOrPath = $this->get_home_global_id_from_path( $homeIdOrPath, false );
			if( isset( $this->homes[ $homeIdOrPath ] ) && ( $this->homes[ $homeIdOrPath ] instanceof hw_inputs_home ) ){
				return $this->homes[ $homeIdOrPath ];
			}
			return false;
		}
		
		
		/**
		 * Возвращает все дома инпутов
		 * @return array|hw_inputs_home[]
		 */
		public function homes(){
			return $this->homes;
		}
		
		
		/**
		 * Возвращает ID для дома из его пути, например array('taxonomy','category') вернет 'taxonomy:category'
		 * @param $path
		 * @param bool $explode - Вернуть массив или строку
		 * @return string|array
		 */
		public function get_home_global_id_from_path( $path, $explode = false ){
			if( !is_array( $path ) )
				$path = array( $path );
			$separator = ':';
			$R = array();
			foreach( $path as $item ){
				$R[] = strtolower( $item );
			}
			return $explode ? $R : implode( $separator, $R );
		}
		
		
		/**
		 * @param null $id
		 * @param string $type
		 * @return hw_input
		 */
		public function make( $id = null, $type = 'text', $parent_home = null ){
			///Find Class path
			$path = hiweb()->dir_modules . '/input/' . $type . '.php';
			if( file_exists( $path ) && is_file( $path ) && is_readable( $path ) ){
				include_once $path;
			} else {
				hiweb()->console()->warn( 'Файла [' . $path . '] нет', true );
			}
			///Find Class
			$className = 'hw_input_' . $type;
			if( !class_exists( $className ) ){
				hiweb()->console()->warn( 'Класса [' . $className . '] нет', true );
				$className = 'hw_input';
			}
			///Make new input
			/** @var hw_input $newInput */
			$newInput = new $className( $id, $type, $parent_home );
			///
			$this->inputs[ $newInput->global_id() ] = $newInput;
			///
			return $newInput;
		}
		
		
		public function get_global(){
			return $this->inputs;
		}
		
		
		public function get( $path = array() ){
			return $this->give_home( $path )->get_inputs();
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
		/** @var hw_inputs_home */
		protected $home;
		
		use hw_input_cols;
		use hw_input_rows;
		
		
		public function __construct( $id = null, $type = 'text', $home = null ){
			$id_sanitize = sanitize_file_name( mb_strtolower( $id ) );
			$this->set_id( $id_sanitize );
			$this->type = trim( $type ) == '' ? 'text' : $type;
			if( $home instanceof hw_inputs_home )
				$this->home = $home;
			$this->init();
		}
		
		
		/**
		 * @param hw_inputs_home|null $home
		 * @return hw_inputs_home|null
		 */
		public function home( $home = null ){
			if( $home instanceof hw_inputs_home ){
				$this->home = $home;
			} elseif( !is_null( $home ) ) {
				hiweb()->console()->warn( 'Попытка передать для инпута вместо дома другое значение типа [' . gettype( $home ) . ']', true );
			}
			return $this->home;
		}
		
		
		protected function init(){
		}
		
		
		public function __call( $name, $arguments ){
			hiweb()->console()->warn( 'Попытка обращения к несуществующему методу [' . $name . ']', true );
		}
		
		
		protected function set_id( $id ){
			if( !is_string( $id ) && !is_int( $id ) ){
				$this->id = hiweb()->string()->rand( 8, true, true, false );
			} else $this->id = sanitize_file_name( strtolower( $id ) );
			if( is_null( $this->name ) )
				$this->name = $this->id;
			if( is_null( $this->title ) )
				$this->title = $id;
		}
		
		
		/**
		 * Установка глобального ID
		 */
		public function global_id(){
			if( is_null( $this->global_id ) ){
				if( $this->home instanceof hw_inputs_home ){
					$home_path = $this->home->get_global_id();
					$home_path[] = $this->id();
					$this->global_id = implode( ':', $home_path );
				} else {
					$this->global_id = '~' . md5( implode( '+', array( $this->id, $this->type, microtime() ) ) );
				}
			}
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
		 * @return string|hw_input|hw_input_repeat|hw_input_repeat
		 */
		public function name( $name = null ){
			if( !is_null( $name ) ){
				$this->name = $name;
				return $this;
			} else return $this->name;
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
			} else return $this->title;
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
			} else return $this->label;
		}
		
		
		/**
		 * Установить VALUE, либо возвращает его
		 * @param null $value - установить имя поля
		 * @return string|hw_input|hw_input_repeat|hw_input_repeat
		 */
		public function value( $value = null ){
			if( !is_null( $value ) ){
				$this->value = $value;
				return $this;
			} else {
			}
			return ( ( is_array( $this->value ) && count( $this->value ) == 0 ) || hiweb()->string()->is_empty( $this->value ) ) ? $this->default_value() : $this->value;
		}
		
		
		/**
		 * Выводит значение поля
		 */
		public function the_value(){
			$value = $this->value();
			echo $value;
			return $value;
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
			} else return $this->default;
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
			} else return $this->placeholder;
		}
		
		
		public function get_tags( array $tags = array( 'type', 'id', 'name', 'title', 'value', 'placeholder' ), $use_additionTags = true, $returnStr = true ){
			$R = array();
			if( $use_additionTags && is_array( $tags ) && is_array( $this->tags ) ){
				$tags = array_merge( $tags, array_keys( $this->tags ) );
			}
			foreach( $tags as $tag ){
				if( array_key_exists( $tag, $this->tags ) ){
					$R[ $tag ] = $this->tags[ $tag ];
				} else if( property_exists( $this, $tag ) ){
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
				if( !is_array( $val ) && !is_object( $val ) )
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
			$html = $this->get( $arguments );
			echo $html;
			return $html;
		}
		
		
		/**
		 * Возвращает
		 * @param null $arguments
		 * @return string
		 */
		public function get_content( $arguments = null ){
			return $this->value();
		}
		
		
		/**
		 * @param null $arguments
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
	
	
	trait hw_input_cols{
		
		private $cols = array();
		
		
		/**
		 * @param $idOrName
		 * @param string $type
		 * @return hw_input
		 */
		public function add_col( $idOrName, $type = 'text' ){
			$id = sanitize_file_name( mb_strtolower( $idOrName ) );
			$input = hiweb()->inputs()->make( $idOrName, $type );
			$input->tags( 'data-col-id', $id );
			$input->tags( 'name' );
			$this->cols[ $id ] = $input;
			return $this->cols[ $id ];
		}
		
		
		/**
		 * @return array|hw_input[]
		 */
		public function get_cols(){
			return $this->cols;
		}
		
		
		/**
		 * @param $col_id
		 */
		public function get_col( $col_id ){
			if( array_key_exists( $col_id, $this->cols ) )
				;
		}
		
		
		/**
		 * @return bool
		 */
		public function have_cols(){
			return ( is_array( $this->cols ) && count( $this->cols ) > 0 );
		}
		
	}
	
	
	trait hw_input_rows{
		
		private $rows;
		
		
		public function is_rows(){
			return ( is_array( $this->value() ) );
		}
		
		
		public function have_value_rows(){
			return ( is_array( $this->value() ) && count( $this->value() ) > 0 );
		}
		
		
		/**
		 * @return bool
		 */
		public function have_rows(){
			if( $this->is_rows() ){
				if( !is_array( $this->rows ) )
					$this->rows = $this->value();
			}
			return ( is_array( $this->rows ) && count( $this->rows ) > 0 );
		}
		
		
		/**
		 * @return mixed
		 */
		public function the_row(){
			return array_shift( $this->rows );
		}
	}
	
	
	class hw_inputs_home{
		
		/** @var string */
		private $id = '';
		/** @var string */
		private $global_id = '';
		/** @var hw_input[] */
		private $inputs = array();
		/** @var null|hw_inputs_home */
		private $parent_home;
		/** @var array|hw_inputs_home[] */
		private $children_homes = array();
		/** @var int */
		public $parent_homes_count = 0;
		public $children_homes_count = 0;
		
		
		public function __construct( $home_id, $parent_home = null ){
			$this->id = $home_id;
			if( $parent_home instanceof hw_inputs_home ){
				$this->parent_home = $parent_home;
			}
		}
		
		
		/**
		 * @return hw_inputs_home|null
		 */
		public function parent_home(){
			return $this->parent_home;
		}
		
		
		public function get_id(){
			return $this->id;
		}
		
		
		/**
		 * @param bool $explode
		 * @return array|string
		 */
		public function get_global_id( $explode = true ){
			if( !is_array( $this->global_id ) ){
				$this->global_id = array();
				if( $this->parent_home instanceof hw_inputs_home )
					$this->global_id = $this->parent_home->get_global_id( true );
				if( $this->id != '' )
					$this->global_id[] = $this->id;
			}
			return hiweb()->inputs()->get_home_global_id_from_path( $this->global_id, $explode );
		}
		
		
		/**
		 * @param bool $include_own
		 * @param int $include_children
		 * @param int $include_parent
		 * @return bool
		 */
		public function have_inputs( $include_own = true, $include_children = 99, $include_parent = 0 ){
			$inputs = $this->get_inputs( $include_own, $include_children, $include_parent );
			return ( is_array( $inputs ) && count( $inputs ) > 0 );
		}
		
		
		/**
		 * @param bool $include_own - Возвращаеть инпуты текущего дома
		 * @param int $include_children - Возвращать инпуты дочернего дома (указывается количество уровней)
		 * @param int $include_parent - Возвращать инпуты родительского дома (указывается количество уровней родителей)
		 * @return hw_input[]
		 */
		public function get_inputs( $include_own = true, $include_children = 99, $include_parent = 0 ){
			///Get own inputs
			$own = array();
			if( $include_own ){
				foreach( $this->inputs as $input ){
					$own[ $input->id() ] = $input;
				}
				if( $this->is_multiple() ){
					///Get multi own inputs
					foreach( $this->children_homes( false, true, false ) as $home ){
						$own = array_merge( $own, $home->get_inputs( true, true, false ) );
					}
				}
			}
			///Get children inputs
			$children = array();
			if( $include_children > 0 && count( $this->children_homes ) > 0 ){
				foreach( $this->children_homes( true, false, false ) as $home ){
					$children = array_merge( $children, $home->get_inputs( true, $include_children - 1, 0 ) );
				}
			} else $children = array();
			///Get parent inputs
			$parent = array();
			if( $include_parent > 0 && $this->parent_home instanceof hw_inputs_home ){
				$parent = $this->parent_home->get_inputs( true, false, $include_parent - 1 );
			} else {
				$parent = array();
			}
			///
			$R =array_merge( $parent, $own, $children );
			return $R;
		}
		
		
		/**
		 * @param bool $normal - возвращает обычные дочерние дома
		 * @param bool $multi - возвращает мульти-дома
		 * @param bool $hidden - возвращает скрытые дома
		 * @return array|hw_inputs_home[]
		 */
		public function children_homes( $normal = true, $multi = false, $hidden = false ){
			$R = array();
			if( is_array( $this->children_homes ) )
				foreach( $this->children_homes as $id => $home ){
					if( ( $id == '=' && $multi ) || ( $id == '-' && $hidden ) || ( $id != '=' && $id != '-' && $normal ) )
						$R[ $id ] = $home;
				}
			return $R;
		}
		
		
		/**
		 * @param $home_id
		 * @return bool
		 */
		public function children_home_exists( $home_id ){
			return ( isset( $this->children_homes[ $home_id ] ) && ( $this->children_homes[ $home_id ] instanceof hw_inputs_home ) );
		}
		
		
		/**
		 * @param $home_id
		 * @return bool|hw_inputs_home
		 */
		public function children_home( $home_id ){
			if( !$this->children_home_exists( $home_id ) )
				return false;
			return $this->children_homes[ $home_id ];
		}
		
		
		/**
		 * @param hw_inputs_home $home
		 */
		public function add_children_home( hw_inputs_home $home ){
			$this->children_homes[ $home->get_id() ] = $home;
			$this->children_homes_count = count( $this->children_homes );
		}
		
		
		/**
		 * @param string $nameOrId
		 * @param string $type
		 * @return hw_input
		 */
		public function add_input( $nameOrId = '', $type = 'type' ){
			if( $nameOrId instanceof hw_input ){
				$input = $nameOrId;
				$id = $nameOrId->id();
			} else {
				$id = sanitize_file_name( strtolower( $nameOrId ) );
				$input = hiweb()->inputs()->make( $id, $type, $this );
				$input->title( $nameOrId );
			}
			$this->inputs[ $id ] = $input;
			return $input;
		}
		
		
		/**
		 * @param array $inputs
		 * @return hw_input[]
		 */
		public function add_inputs( $inputs = array() ){
			if( $inputs instanceof hw_input )
				$inputs = array( $inputs );
			if( is_array( $inputs ) )
				foreach( $inputs as $input ){
					if( $input instanceof hw_input ){
						$this->add_input( $input );
					}
				}
			return $this->inputs;
		}
		
		
		/**
		 * @param $id
		 * @param bool $include_child
		 * @param bool $include_parent
		 * @return bool
		 */
		public function input_exists( $id, $include_child = false, $include_parent = false ){
			return array_key_exists( $id, $this->get_inputs(true, $include_child, $include_parent) );
		}
		
		
		/**
		 * @param $id
		 * @param bool $include_child
		 * @param bool $include_parent
		 * @return hw_input
		 */
		public function give_input( $id, $include_child = false, $include_parent = false ){
			$id = sanitize_file_name( mb_strtolower( $id ) );
			$fields = $this->get_inputs( true, $include_child, $include_parent );
			if( !isset( $fields[ $id ] ) ){
				$input = hiweb()->inputs()->make( $id, 'text', $this );
			} else {
				$input = $fields[ $id ];
			}
			return $input;
		}
		
		
		/**
		 * @return bool
		 */
		public function is_multiple(){
			return( array_key_exists( '=', $this->children_homes ) );
		}
		
		
	}
	
	
	trait hw_inputs_home_functions{
		
		/** @var null|hw_inputs_home */
		private $inputs_home;
		private $inputs_name_prepend;
		
		
		/**
		 * @param $path
		 * @param bool $multi_home - создать
		 * @return hw_inputs_home
		 */
		private function inputs_home_make( $path ){
			$this->inputs_home = hiweb()->inputs()->give_home( $path );
			return $this->inputs_home;
		}
		
		
		private function inputs_name_prepend( $set ){
			$this->inputs_name_prepend = $set;
		}
		
		
		/**
		 * @return bool
		 */
		private function inputs_home_exists(){
			return $this->inputs_home instanceof hw_inputs_home;
		}
		
		
		/**
		 * @param $idOrName
		 * @param string|hw_input|null $type
		 * @return hw_input
		 */
		public function add_field( $idOrName, $type = 'text' ){
			if( !$this->inputs_home_exists() ){
				hiweb()->console()->warn( 'Попытка добавить поле в несуществующий дом', true );
				$input = hiweb()->inputs()->make( $idOrName, $type );
			} else {
				$input = $this->inputs_home->add_input( $idOrName, $type );
			}
			$input->name( $this->inputs_name_prepend . $input->name() );
			return $input;
		}
		
		
		/**
		 * @param $fields
		 * @return array|hw_input[]
		 */
		public function add_fields( $fields ){
			if( !$this->inputs_home_exists() ){
				hiweb()->console()->warn( 'Попытка добавить поля в несуществующий дом', true );
				return array();
			} else {
				if( $fields instanceof hw_input )
					$fields = array( $fields );
				if( is_array( $fields ) )
					/** @var hw_input $field */
					foreach( $fields as $field ){
						$this->add_field( $field );
					}
				return $this->inputs_home->get_inputs( true, 0, 0 );
			}
		}
		
		
		/**
		 * @param bool $return_children
		 * @return array|hw_input[]
		 */
		public function get_fields( $return_children = false ){
			if( !$this->inputs_home_exists() ){
				hiweb()->console()->warn( 'Попытка получить поля из несуществующего дома', true );
				return array();
			} else {
				return $this->inputs_home->get_inputs( true, $return_children === true ? 99 : $return_children );
			}
		}
		
		
		/**
		 * @param $id
		 * @return bool
		 */
		public function field_exists( $id ){
			return $this->inputs_home->input_exists( $id );
		}
		
		
		/**
		 * @param bool $return_children
		 * @return bool
		 */
		public function have_fields( $return_children = false ){
			$inputs = $this->get_fields( $return_children );
			return ( is_array( $inputs ) && count( $inputs ) > 0 );
		}
		
		
	}
	
	
	trait hw_inputs_home_multi_functions{
		
		/** @var null|hw_inputs_home */
		private $inputs_home_parent;
		
		/** @var array|hw_inputs_home[] */
		private $inputs_homes = array();
		private $inputs_homes_multi_path = array();
		
		
		/**
		 * @param $path - массив пути
		 */
		private function inputs_home_make( $path ){
			if( !is_array( $path ) )
				$path = array( $path );
			$this->inputs_homes_multi_path = $path;
			$this->inputs_home_parent = hiweb()->inputs()->give_home( $path );
		}
		
		
		/**
		 * @param string $position
		 * @return hw_inputs_home
		 */
		private function inputs_home( $position = null, $make_if_not_exists = false ){
			$position = (string)$position;
			if( !isset( $this->inputs_homes[ $position ] ) ){
				$new_home_path = array_merge( $this->inputs_homes_multi_path, array( '=', $position ) );
				if( $make_if_not_exists ){
					$new_home = hiweb()->inputs()->give_home( $new_home_path );
					$this->inputs_homes[ $position ] = $new_home;
				} else {
					$new_home = hiweb()->inputs()->get_home( $new_home_path );
					if( $new_home instanceof hw_inputs_home ){
						$this->inputs_homes[ $position ] = $new_home;
					} else return new hw_inputs_home( '' );
				}
			}
			return $this->inputs_homes[ $position ];
		}
		
		
		/**
		 * @param $position
		 * @return bool
		 */
		private function inputs_home_exists( $position ){
			return ( array_key_exists( $position, $this->inputs_homes ) && ( $this->inputs_homes[ $position ] instanceof hw_inputs_home ) );
		}
		
		
		/**
		 * @param $idOrName
		 * @param string|hw_input|null $type
		 * @param int $position
		 * @return hw_input
		 */
		public function add_field( $idOrName, $type = 'text', $position = null ){
			$position = (string)$position;
			return $this->inputs_home( $position, true )->add_input( $idOrName, $type );
		}
		
		
		/**
		 * @param $fields
		 * @param null $position
		 * @return array|hw_input[]
		 */
		public function add_fields( $fields, $position = null ){
			$position = (string)$position;
			if( $fields instanceof hw_input )
				$fields = array( $fields );
			if( is_array( $fields ) )
				/** @var hw_input $field */
				foreach( $fields as $field ){
					$this->add_field( $field, null, $position );
				}
			return $this->get_fields();
		}
		
		
		/**
		 * @param null $position
		 * @param bool|int $return_children
		 * @param bool|int $return_parent
		 * @return array|hw_input[]
		 */
		public function get_fields( $position = null, $return_children = false, $return_parent = false ){
			$R = array();
			if( is_null( $position ) || is_bool( $position ) ){
				foreach( $this->inputs_homes as $home ){
					$R = array_merge( $R, $home->get_inputs( true, $return_children === true ? 99 : $return_children, $return_parent === true ? 99 : $return_parent ) );
				}
			} else {
				$position = (string)$position;
				if( $this->inputs_home_exists( $position ) )
					$R = $this->inputs_home( $position )->get_inputs( true, $return_children === true ? 99 : $return_children, $return_parent === true ? 99 : $return_parent );
			}
			///Children and Parent fields
			if( $return_children !== false ){
				$R = array_merge( $R, $this->inputs_home_parent->get_inputs( false, $return_children, $return_parent ) );
			}
			return $R;
		}
		
		
		/**
		 * @param null $position
		 * @param bool $return_children
		 * @param bool $return_parent
		 * @return bool
		 */
		public function have_fields( $position = null, $return_children = false, $return_parent = false ){
			$inputs = $this->get_fields( $position, $return_children, $return_parent );
			return ( is_array( $inputs ) && count( $inputs ) > 0 );
		}
		
		
	}