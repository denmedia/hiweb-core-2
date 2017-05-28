<?php


	/**
	 * Created by PhpStorm.
	 * User: denmedia
	 * Date: 25.02.2017
	 * Time: 15:38
	 */
	trait hw_input_value{

		protected $value;
		protected $dimension = 0;


		/// VALUE
		protected function set_value( $value ){
			$this->value = $value;
			return $this;
		}


		protected function get_value(){
			return $this->value;
		}


		/**
		 * Возвращает / устанавливает значение
		 * @param null $set - установить значение
		 * @return mixed|hw_input
		 */
		public function value( $set = null ){
			if( !is_null( $set ) ){
				$this->set_value( $set );
				return $this;
			}
			return $this->get_value();
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
		 * @return bool
		 */
		public function have_value_rows(){
			return ( is_array( $this->value() ) && count( $this->value() ) > 0 );
		}

	}