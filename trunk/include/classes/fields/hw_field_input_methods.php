<?php


	/**
	 * Created by PhpStorm.
	 * User: denmedia
	 * Date: 19.05.2017
	 * Time: 10:55
	 */
	trait hw_field_input_methods{

		/** @var hw_input */
		protected $input;


		/**
		 * @param string $type
		 * @return null|string
		 */
		public function type( $type = null ){
			if( is_null( $type ) ){
				return $this->have_input() ? $this->input()->type() : null;
			} else {
				return $this->make_input( $type )->type();
			}
		}


		/**
		 * @return bool
		 */
		public function have_input(){
			return is_object( $this->input ) && ( $this->input instanceof hw_input );
		}


		/**
		 * @param string $fieldType
		 * @return hw_input
		 */
		protected function make_input( $fieldType = 'text' ){
			$this->input = hiweb()->inputs()->create( $fieldType, $this->id );
			$this->input->tag_add('data-field-id', $this->id);
			return $this->input;
		}


		/**
		 * Возвращает HTML поля с инпутом
		 * @param string $template
		 * @return string
		 */
		public function html( $template = '' ){
			if( is_null( $this->input()->tag_get( 'placeholder' ) ) && trim( $this->value_default() ) != '' ){
				$this->input()->tag_add( 'placeholder', $this->value_default() );
			}
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
		 * @return hw_input
		 */
		public function input(){
			if( !$this->have_input() ){
				$this->input = $this->make_input();
			}
			return $this->input;
		}


		/**
		 * Возвращает значение поля
		 * @param null $set_value
		 * @return mixed|null
		 */
		public function value( $set_value = null ){
			////
			if( is_null( $set_value ) ){
				$input_value = $this->input()->value();
				if( is_null( $input_value ) ){
					$this->input()->value( $this->value_default() );
				}
				return $this->input()->value();
			} else {
				$this->input()->value( $set_value );
				return $this;
			}
		}


		/**
		 * @param mixed $args
		 * @param null  $args2
		 * @return mixed
		 */
		public function content( $args = null, $args2 = null ){
			return apply_filters( 'hiweb-fields-content-type-' . $this->type(), $this->value(), $args, $args2 );
		}


		/**
		 * @param        $id
		 * @param string $name
		 * @param null   $input_type
		 * @return hw_input_axis_col
		 */
		public function add_col( $id = null, $input_type = null, $name = null ){
			return $this->input()->add_col( $id, $input_type, $name );
		}


		/**
		 * @return bool
		 */
		public function have_rows(){
			return $this->input()->have_rows();
		}


		/**
		 * @param null $name_or_array
		 * @param null $value
		 * @return array|hw_field|mixed|null
		 */
		public function attributes( $name_or_array = null, $value = null ){
			$R = $this->input()->attributes($name_or_array, $value);
			return ($R instanceof hw_input) ? $this : $R;
		}

	}