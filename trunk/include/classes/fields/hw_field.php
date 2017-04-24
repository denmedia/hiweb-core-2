<?php


	class hw_field{

		use hw_hidden_methods_props;

		private $id;
		private $global_id;
		///
		private $name;
		private $description;
		///
		private $type;
		/** @var hw_fields_location_root[] */
		private $hooks = array();
		/** @var null|hw_input */
		private $input;
		///
		/** @var mixed Значение по-умолчанию */
		private $default;

		private $form_template = '';

		private $prepend = '';
		private $append = '';

		/** @var array Attributes array */
		private $attributes = [];


		/**
		 * hw_field constructor.
		 * @param        $fieldId  - индификатор поля
		 * @param        $globalId - глобальный ID
		 * @param string $fieldType
		 */
		public function __construct( $fieldId, $globalId, $fieldType = 'text' ){
			$this->id = mb_strtolower( $fieldId );
			$this->global_id = $globalId;
			$this->type = $fieldType;
			////
			$this->input = hiweb()->inputs()->create( $fieldType, $this->id );
			$this->name = $fieldId;
		}


		public function get_id(){
			return $this->id;
		}


		/**
		 * @param string $type
		 */
		public function set_type( $type = 'text' ){
			$this->type = $type;
			$this->input = hiweb()->inputs()->create( $type, $this->id );
		}
		///////////////////////

		/**
		 * @param        $idOrName
		 * @param string $type
		 * @param string $name
		 * @return hw_field
		 */
		public function add_col( $idOrName, $type = 'text', $name = '' ){
			$field = $this->input()->add_col( $idOrName, $type );
			if( is_string( $name ) && $name != '' )
				$field->name( $name );
			return $field;
		}


		/**
		 * @return bool
		 */
		public function have_rows(){
			return $this->input()->have_rows();
		}
		///////////////////////

		/**
		 * @param null $set
		 * @return hw_field|string
		 */
		public function form_template( $set = null ){
			if( is_string( $set ) && trim( $set ) != '' ){
				$this->form_template = $set;
				return $this;
			} else {
				return $this->form_template;
			}
		}


		/**
		 * @param null|string|array $name_or_array
		 * @param null              $value
		 * @return hw_field|array|mixed|null
		 */
		public function attr( $name_or_array = null, $value = null ){
			if( is_array( $name_or_array ) ){
				$this->attributes = array_merge( $this->attributes, $name_or_array );
				return $this;
			} elseif( !is_string( $name_or_array ) || trim( $name_or_array ) == '' ) {
				return $this->attributes;
			} elseif( is_null( $value ) ) {
				return array_key_exists( $name_or_array, $this->attributes ) ? $this->attributes[ $name_or_array ] : null;
			} else {
				$this->attributes[ $name_or_array ] = $value;
				return $this;
			}
		}


		/**
		 * Установить / Получить значение опции, при установке свойства, возвращаеться объект
		 * @param null|string|array $option_key   - ключ опции, либо массив [ключ => значение]. Если передать не массив и не строку, то ф-я вернет весь массив опций.
		 * @param null|mixed|true   $option_value - значение опции, если option_key был ключем, если он был массивом, то значени true перепишет все опции. Если значение не передать (null), то ф-я вернут значение данного ключа
		 * @return array|mixed|hw_field
		 */
		public function options( $option_key = null, $option_value = null ){
			$R = $this->input()->options( $option_key, $option_value );
			return ( $R instanceof hw_input ) ? $this : $R;
		}


		/**
		 * @return mixed
		 */
		public function get_type(){
			return $this->type;
		}


		/**
		 * Возвращает TRUE, если имеет инпут
		 * @return bool
		 */
		public function have_input(){
			return ( $this->input instanceof hw_input );
		}


		/**
		 * @param null $set
		 * @return hw_field|string
		 */
		public function placeholder( $set = null ){
			if( is_null( $set ) ){
				return $this->input()->placeholder;
			} else {
				$this->input()->placeholder = $set;
				return $this;
			}
		}


		/**
		 * @return hw_input
		 */
		public function input(){
			if( !$this->input instanceof hw_input ){
				$this->input = hiweb()->inputs()->create();
			}
			if( $this->input->placeholder == '' )
				$this->input->placeholder = $this->default;
			//$this->input->value = $this->value;
			return $this->input;
		}


		/**
		 * Возвращает значение поля
		 * @param null $set_value
		 * @return mixed|null
		 */
		public function value( $set_value = null ){
			if( !is_null( $set_value ) ){
				$this->input()->value( $set_value );
			}
			if( $this->have_input() ){
				$R = $this->input->value();
				if( is_null( $R ) ){
					$this->input->value( $this->default );
					$R = $this->default;
				}
				return $R;
			}
			hiweb()->console()->error( sprintf( __( 'For field [%s] input not be set' ), $this->id ), true );
			return null;
		}


		/**
		 * @param mixed $args
		 * @param null  $args2
		 * @return mixed
		 */
		public function content($args = null,$args2 = null){
			return apply_filters( 'hiweb-fields-content-type-' . $this->get_type(), $this->value(), $args, $args2 );
		}


		/**
		 * @return hw_fields_location_root
		 */
		public function location(){
			return hiweb()->fields()->locations()->add( $this );
		}


		/**
		 * Возвращает HTML поля с инпутом
		 * @param string $template
		 * @return string
		 */
		public function html( $template = '' ){
			$R = $this->input()->html();
			return $R;
		}


		/**
		 * @param string $template
		 * @return string
		 */
		public function the( $template = '' ){
			$R = $this->html( $template );
			echo $R;
			return $R;
		}


		/**
		 * Установить/получить имя поля
		 * @param null $set
		 * @return hw_field|string
		 */
		public function name( $set = null ){
			if( is_null( $set ) ){
				return $this->name;
			}
			$this->name = $set;
			return $this;
		}


		/**
		 * @param string $set
		 * @return string|hw_field
		 */
		public function prepend( $set = null ){
			if( is_null( $set ) ){
				return (string)$this->prepend;
			}
			$this->prepend = $set;
			return $this;
		}


		/**
		 * @param string $set
		 * @return string|hw_field
		 */
		public function append( $set = null ){
			if( is_null( $set ) ){
				return $this->append;
			}
			$this->append = $set;
			return $this;
		}


		/**
		 * Установить/получить пояснение для поля
		 * @param null $set
		 * @return hw_field|string
		 */
		public function description( $set = null ){
			if( is_null( $set ) ){
				return $this->description;
			}
			$this->description = $set;
			return $this;
		}


		/**
		 * Установить/получить значение поля по-умолчания
		 * @param null $set
		 * @return hw_field|string
		 */
		public function default_value( $set = null ){
			if( is_null( $set ) ){
				return $this->default;
			}
			$this->default = $set;
			return $this;
		}


	}