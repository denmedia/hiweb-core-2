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
		/** @var hw_field_location[] */
		private $hooks = array();
		/** @var null|hw_input */
		private $input;
		///
		/** @var mixed Значение по-умолчанию */
		private $default;

		private $form_template = '';


		/**
		 * hw_field constructor.
		 * @param        $fieldId  - индификатор поля
		 * @param        $globalId - глобальный ID
		 * @param string $fieldType
		 */
		public function __construct( $fieldId, $globalId, $fieldType = 'text' ){
			$this->id = sanitize_file_name( mb_strtolower( $fieldId ) );
			$this->global_id = $globalId;
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
		 * @return hw_input
		 */
		public function add_col( $idOrName, $type = 'text' ){
			return $this->input()->add_col( $idOrName, $type );
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
		 * @return hw_field_location
		 */
		public function location(){
			$hook = hiweb()->fields()->hook( $this );
			$this->hooks[] = $hook;
			return $hook;
		}


		/**
		 * @return hw_field_location[]
		 */
		public function get_locations(){
			return $this->hooks;
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